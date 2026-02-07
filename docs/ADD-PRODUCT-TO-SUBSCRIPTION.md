# הוספת מוצרים למנוי (Recharge + Shopify)

## הגדרת מוצרים באדמין (ידנית)

**קטלוג המוצרים** שאפשר להציע ב"הוסף מוצר" מנוהל **באפליקציה**:

1. התחבר לאדמין: **`/admin`**
2. בתפריט: **Subscription products** (או "Portal" אם הוגדר קבוצה)
3. הוסף רשומה לכל מוצר שאתה רוצה להציע:
   - **Title** – שם לתצוגה
   - **Shopify Variant ID** – חובה. מזהה הווריאנט ב-Shopify (מספר או `gid://shopify/ProductVariant/...` לפי מה ש-Recharge מצפה)
   - **Recharge Product ID** – אופציונלי (להזכר)
   - **Image URL** – קישור לתמונה
   - **Order interval** – תדירות (למשל 1 + month = כל חודש)
   - **Sort order** – סדר בתצוגה
   - **Active** – סמן כדי שהמוצר יופיע בפורטל

הנתונים נשמרים בטבלה **`subscription_products`**. דף "הוסף מוצר" בפורטל (כשתוסיף) יטען מוצרים מ-`SubscriptionProduct::active()->ordered()->get()` וישתמש ב-`shopify_variant_id` ב-`createSubscription()`.

---

## איך זה עובד ב-Recharge

ב-Recharge **כל מנוי = מוצר אחד** (שורה אחת). אין "הוספת שורה" למנוי קיים.

**"הוספת מוצר למנוי"** = **יצירת מנוי חדש** עם המוצר החדש, לאותו לקוח ולאותה כתובת.  
אחרי ההוספה ללקוח יהיו כמה מנויים (למשל: מנוי ל־Daily Pack A ומנוי ל־Daily Pack B) לאותה כתובת.

---

## מה צריך כדי שזה יעבוד

### 1. הרשאות API ב-Recharge

ב-**Recharge Dashboard → Settings → API** וודא שלטוקן ה-API יש:

- **`read_products`** – כדי לקבל רשימת מוצרים שמוגדרים למנוי
- **`write_subscriptions`** – כדי ליצור מנוי חדש (סביר שכבר קיים מהפורטל)
- **`read_customers`** – כבר בשימוש

### 2. מוצרים ב-Recharge

רק מוצרים ש-**הוגדרו ב-Recharge כמנוי** (עם תדירות חיוב, אפשרויות משלוח וכו') יופיעו ב-API ויוכלו להתווסף.

- ב-Recharge: **Products** – וודא שכל מוצר שאתה רוצה להציע בפורטל מופיע שם ומוגדר למנוי.
- המוצרים מקושרים ל-**Shopify** דרך `shopify_product_id` / `shopify_variant_id`.

### 3. Shopify

- **לא חובה** להריץ קריאות ישירות ל-Shopify רק כדי "להוסיף מוצר למנוי".
- רשימת המוצרים למנוי מגיעה מ-**Recharge Products API** (`GET /products`).
- ביצירת מנוי שולחים ל-Recharge את ה-**Shopify Variant ID** (שמגיע ממוצרי Recharge); Recharge מדבר עם Shopify מאחורי הקלעים.

### 4. בפרויקט (Laravel)

- **RechargeService** – צריך שתהיה:
  - **listProducts()** – קריאה ל-`GET /products` (עם pagination אם צריך).
  - **createSubscription(array $payload)** – קריאה ל-`POST /subscriptions` עם:
    - `address_id` – כתובת הלקוח (למשל מהמנוי הקיים)
    - `external_variant_id` (או השדה שהגרסה שלך של ה-API דורשת) – מזהה ה-variant ב-Shopify
    - `quantity`
    - `order_interval_frequency` + `order_interval_unit` (למשל כל 28 יום)
    - לפי הצורך: `next_charge_scheduled_at` וכו'
- **Controller** – דף "הוסף מוצר":
  - טוען מוצרים מ-`listProducts()`
  - טוען את ה-`address_id` של הלקוח (מהמנוי הקיים או מכתובת ברירת מחדל)
  - טופס: בחירת מוצר/וריאנט, כמות, תדירות
  - בשליחת הטופס: קריאה ל-`createSubscription(...)` עם הנתונים האלה
- **Routes** – למשל:
  - `GET /account/add-product` – הצגת הטופס
  - `POST /account/add-product` – שליחת הטופס ויצירת המנוי
- **View** – דף עם רשימת מוצרים (לפי מה ש-Recharge מחזיר), בחירת וריאנט/כמות/תדירות וכפתור "הוסף למנוי שלי".

---

## פרמטרים ליצירת מנוי (Recharge 2021-11)

דוגמה כללית ל-body של `POST /subscriptions` (לבדוק מול [תיעוד Recharge](https://developer.rechargepayments.com/2021-11/subscriptions/subscriptions_create)):

- `address_id` – חובה
- `external_variant_id` או `shopify_variant_id` (תלוי גרסת API)
- `quantity`
- `order_interval_frequency` (למשל 1)
- `order_interval_unit` (למשל "day" / "week" / "month")
- `charge_interval_frequency` / `charge_interval_unit` אם נדרש
- אופציונלי: `next_charge_scheduled_at`, `price`, וכו'

הערך של `external_variant_id` מגיע ממוצרי Recharge (שם מקבלים את הקישור ל-Shopify variant).

---

## סיכום

| רכיב | מקור | שימוש |
|------|------|--------|
| רשימת מוצרים למנוי | Recharge `GET /products` | להצגה בדף "הוסף מוצר" |
| כתובת למשלוח | Recharge – כתובת של מנוי קיים של הלקוח | `address_id` ב-`createSubscription` |
| יצירת מנוי חדש | Recharge `POST /subscriptions` | הוספת "מוצר חדש" = מנוי חדש לאותה כתובת |
| Shopify | לא ישירות מפורטל | Recharge משתמש ב-Shopify variant IDs שהוא כבר מכיר |

אם תרצה, אפשר בשלב הבא להוסיף בקוד את `listProducts()` ו-`createSubscription()` ואת דף "הוסף מוצר" בפורטל.
