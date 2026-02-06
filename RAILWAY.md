# פריסה ל-Railway (Recharge Customer Portal)

מדריך קצר להקמת הפרויקט ב-[Railway](https://railway.com/) כך שבטעינת השרת ייווצרו אוטומטית ה-DB והקונפיגורציה.

---

## איזה DB להקים?

**מומלץ: PostgreSQL**  
ב-Railway הכי נוח להוסיף **PostgreSQL** – Railway מזהה אותו אוטומטית ומזריק את משתנה `DATABASE_URL`. Laravel תומך ב-PostgreSQL out of the box.

**אפשרות חלופית: MySQL**  
אם אתה מעדיף MySQL – הוסף את תוסף MySQL ב-Railway. גם אז Railway מספק connection string; תצטרך למפות אותו ל-`DATABASE_URL` או להגדיר ב-Laravel את `DB_CONNECTION=mysql` ואת השדות הנדרשים.

בקוד: ה-`config/database.php` כבר תומך ב-`DATABASE_URL` (משמש כ-`url` של החיבור), כך שברגע ש-`DATABASE_URL` מוגדר – Laravel משתמש בו ואין צורך להגדיר ידנית host/port/database/user/password.

---

## שלבי הקמה ב-Railway

### 1. יצירת פרויקט וחיבור ל-Git

1. היכנס ל-[railway.com](https://railway.com/) וצור פרויקט חדש.
2. **New → GitHub Repo** ובחר את הריפו `aviadmols/RechargeAdmin` (או העלאת הקוד קודם ל-GitHub).
3. Railway יזהה Laravel ויבנה את הפרויקט לפי ה-`railway.toml` / הגדרות ברירת המחדל.

### 2. הוספת מסד נתונים (PostgreSQL מומלץ)

1. בפרויקט: **+ New → Database → PostgreSQL**.
2. Railway ייצור שרת PostgreSQL ויעניק משתנה **`DATABASE_URL`**.
3. **חשוב:** חבר את משתנה `DATABASE_URL` לשירות האפליקציה:
   - בחר את שירות ה-Laravel → **Variables**.
   - הוסף/העתק את `DATABASE_URL` מהשירות של PostgreSQL (או השתמש ב-**Reference** ב-Railway כדי לחבר בין השירותים).

אם בחרת ב-**MySQL** במקום PostgreSQL:

- **+ New → Database → MySQL**.
- חבר את ה-MySQL connection string לשירות האפליקציה. אם Railway נותן רק host/user/password, הגדר:
  - `DB_CONNECTION=mysql`
  - `DB_HOST=...`, `DB_DATABASE=...`, `DB_USERNAME=...`, `DB_PASSWORD=...`
  או הרכב `DATABASE_URL` בעצמך והגדר `DATABASE_URL` (או `DB_URL`) בהתאם.

### 3. משתני סביבה חובה

ב-**Variables** של שירות האפליקציה הוסף/עדכן:

| משתנה | תיאור |
|--------|--------|
| `APP_KEY` | הרץ מקומית: `php artisan key:generate --show` והדבק. |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | כתובת האתר ב-Railway, למשל `https://your-app.up.railway.app` |
| `DATABASE_URL` | מגיע אוטומטית אם חיברת את שירות PostgreSQL/MySQL (ראו למעלה). |

אם אין `DATABASE_URL` (למשל ב-MySQL עם משתנים נפרדים):

- `DB_CONNECTION=mysql`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

**אופציונלי:**

- `CACHE_STORE` = `database` או `redis` (אם הוספת Redis).
- `QUEUE_CONNECTION` = `database` או `redis`.
- `SESSION_DRIVER` = `database`.
- הגדרות Mail (SMTP) אם אתה שולח מייל OTP.

### 4. מה קורה בטעינת השרת (Deploy)

קובץ `railway.toml` מגדיר:

- **preDeployCommand:**  
  `php artisan config:cache && php artisan migrate --force`  
  → בכל דיפלוי רצות מיגרציות ולכן ה-DB (הטבלאות) נוצרות/מתעדכנות אוטומטית.

- **startCommand:**  
  `php artisan serve --host=0.0.0.0 --port=${PORT:-8000}`  
  → השרת עולה עם Laravel.

כלומר: ברגע ש-`DATABASE_URL` (או חיבור DB אחר) מוגדר ו-Railway מריץ את ה-pre-deploy – ה-DB וכל הקונפיגורציה של המיגרציות ייווצרו/יעודכנו בכל דיפלוי.

### 5. Worker (תור – אופציונלי)

אם אתה משתמש ב-queue (למשל לשליחת מייל OTP):

- **+ New → Empty Service** (או "Worker" אם קיים).
- חבר לאותו ריפו ובשירות זה הגדר **Start Command:**  
  `php artisan queue:work --sleep=3 --tries=3`
- העתק את אותם משתני סביבה (במיוחד `APP_KEY`, `DATABASE_URL`) גם ל-Worker.

### 6. Scheduler (Cron – אופציונלי)

להרצת פקודות מתוזמנות (ניקוי OTP, גיזום לוגים):

- ב-Railway: **Scheduled Jobs** או Cron.
- הרץ כל דקה: `php artisan schedule:run` (או הגדר לפי הצורך ב-`app/Console/Kernel.php`).

---

## סיכום

- **DB להקים:** PostgreSQL (מומלץ) או MySQL.
- **קונפיגורציה:** `DATABASE_URL` (או חיבור MySQL מפורש) + `APP_KEY`, `APP_URL`, `APP_ENV=production`, `APP_DEBUG=false`.
- **יצירת ה-DB והטבלאות:** מתבצעת אוטומטית בכל דיפלוי thanks ל-`preDeployCommand` ב-`railway.toml` שמריץ `php artisan migrate --force`.

אחרי הדיפלוי הראשון, היכנס ל-Admin (Filament) והגדר את טוקן Recharge וההגדרות דרך הממשק.
