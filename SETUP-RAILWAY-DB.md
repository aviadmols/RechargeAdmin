# איפוס ה-DB של Railway ויצירת אדמין (מהמחשב שלך)

כשהחיבור ל-DB נכשל מתוך הקונטיינר ב-Railway, אפשר להריץ איפוס ומיגרציות **מהמחשב שלך** – מחובר ל-DB של Railway.

## 1. העתקת חיבור ה-DB מ-Railway

1. ב-Railway → שירות **Postgres** → **Variables** או **Connect**.
2. העתק את **DATABASE_PUBLIC_URL** (או **Public URL**) – מחרוזת שמתחילה ב-`postgresql://...`.
3. בפרויקט המקומי: פתח/צור קובץ **`.env.railway`** (או ערוך `.env` זמנית) והוסף:
   ```env
   DATABASE_PUBLIC_URL=postgresql://user:pass@yamabiko.proxy.rlwy.net:54326/railway
   APP_KEY=base64:xxxx   # אותו APP_KEY כמו ב-Railway
   ```
   או העתק את כל ה-Variables הרלוונטיים מ-Railway.

## 2. הרצת הסקריפט

### Windows (PowerShell)

```powershell
cd C:\Users\user\Desktop\Projects\RechargePortal

# טעינת חיבור ל-DB של Railway (החלף בערך האמיתי!)
$env:DATABASE_PUBLIC_URL = "postgresql://postgres:XXXX@yamabiko.proxy.rlwy.net:54326/railway"
$env:APP_KEY = "base64:xxxx"

# איפוס טבלאות + יצירת אדמין
php artisan migrate:fresh --force
php artisan admin:create --email=aviadmols@gmail.com --password="הסיסמה_שלך" --name="Aviad"
```

### Mac / Linux (או Git Bash ב-Windows)

```bash
cd /path/to/RechargePortal

# אם העתקת ל-.env – לא צריך. אחרת:
export DATABASE_PUBLIC_URL="postgresql://..."
export APP_KEY="base64:..."

# הרצת הסקריפט (אימייל/סיסמה כברירת מחדל בסקריפט)
sh setup-railway-db.sh
```

או עם סיסמה משלך:

```bash
RAILWAY_ADMIN_EMAIL=aviadmols@gmail.com RAILWAY_ADMIN_PASSWORD="הסיסמה" RAILWAY_ADMIN_NAME="Aviad" sh setup-railway-db.sh
```

## 3. אחרי ההרצה

- כל הטבלאות ב-DB של Railway נוצרו מחדש (migrate:fresh).
- נוצר משתמש אדמין עם האימייל והסיסמה שהזנת.
- היכנס לאתר ב-Railway: **https://xxx.up.railway.app/admin** עם האימייל והסיסמה.

## אבטחה

- אל תעלה את `.env.railway` או `.env` עם סיסמאות ל-Git.
- אחרי ההרצה אפשר למחוק ערכים רגישים מ-.env אם השתמשת בו.
