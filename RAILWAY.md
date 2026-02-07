# Deploying to Railway (Recharge Customer Portal)

Short guide to set up the project on [Railway](https://railway.com/) so that on each deploy the database and schema are created/updated automatically.

---

## Which database should I use?

**Recommended: PostgreSQL**  
On Railway it’s easiest to add **PostgreSQL**. Railway detects it and injects the `DATABASE_URL` variable. Laravel supports PostgreSQL out of the box.

**Alternative: MySQL**  
If you prefer MySQL, add the MySQL plugin in Railway. Railway still provides a connection string; you’ll need to map it to `DATABASE_URL` or set `DB_CONNECTION=mysql` and the required DB_* variables in Laravel.

In code: `config/database.php` already supports `DATABASE_URL` (used as the connection `url`), so once `DATABASE_URL` is set, Laravel uses it and you don’t need to set host/port/database/user/password manually.

---

## Railway setup steps

### 1. Create a project and connect Git

1. Go to [railway.com](https://railway.com/) and create a new project.
2. **New → GitHub Repo** and select the repo `aviadmols/RechargeAdmin` (or push your code to GitHub first).
3. Railway will detect Laravel and build the project using `railway.toml` or default settings.

### 2. Add a database (PostgreSQL recommended)

1. In the project: **+ New → Database → PostgreSQL**.
2. Railway will create a PostgreSQL instance and provide **`DATABASE_URL`**.
3. **Important:** Connect `DATABASE_URL` to your app service:
   - Select your Laravel service → **Variables**.
   - Add or copy `DATABASE_URL` from the PostgreSQL service (or use **Reference** in Railway to link services).

If you chose **MySQL** instead of PostgreSQL:

- **+ New → Database → MySQL**.
- Connect the MySQL connection string to your app service. If Railway only gives host/user/password, set:
  - `DB_CONNECTION=mysql`
  - `DB_HOST=...`, `DB_DATABASE=...`, `DB_USERNAME=...`, `DB_PASSWORD=...`
  Or build `DATABASE_URL` yourself and set `DATABASE_URL` (or `DB_URL`) accordingly.

### 3. Required environment variables

In your app service **Variables**, add or update:

**Checklist – מה חובה מעבר ל-DATABASE_URL (ש-Railway מוסיף):**

| Variable | Required | Description |
|----------|----------|-------------|
| `DATABASE_URL` | ✅ (from Railway) | כבר אצלך – מגיע מחיבור ל-Postgres. |
| `APP_KEY` | ✅ **חובה** | בלי זה האפליקציה לא עולה. מקומית: `php artisan key:generate --show` והדבק. |
| `APP_URL` | ✅ **חובה** | כתובת האתר ב-Railway, למשל `https://xxx.up.railway.app`. |
| `APP_ENV` | מומלץ | `production` |
| `APP_DEBUG` | מומלץ | `false` |

If you don’t have `DATABASE_URL` (e.g. MySQL with separate variables):

- `DB_CONNECTION=mysql`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

**Optional:**

- `CACHE_STORE` = `database` or `redis` (if you add Redis).
- `QUEUE_CONNECTION` = `database` or `redis`.
- `SESSION_DRIVER` = `database`.
- Mail (SMTP) settings if you send OTP emails.

### 4. What runs on deploy (טבלאות ה-DB נוצרות/מתעדכנות אוטומטית)

- **preDeployCommand** (ב־`railway.toml`):  
  `php artisan config:cache && php artisan migrate --force`  
  → לפני כל deploy – מיגרציות רצות וכל טבלאות ה-DB נוצרות או מתעדכנות.

- **startCommand** (שירות ה-web):  
  `php artisan migrate --force && php artisan serve ...`  
  → גם בהרצה, מיגרציות רצות שוב (למקרה ש-preDeploy דולג), ואז השרת עולה.

- **Procfile – worker:**  
  `php artisan migrate --force && php artisan queue:work ...`  
  → גם ה-worker מריץ מיגרציות בהפעלה, כך שהטבלאות קיימות גם כשרק ה-worker מחובר ל-Postgres.

אחרי ש־`DATABASE_URL` (או חיבור DB אחר) מוגדר, בכל deploy הטבלאות ייווצרו/יתעדכנו אוטומטית.

### 5. Worker (queue – optional)

If you use the queue (e.g. for OTP emails):

- **+ New → Empty Service** (or “Worker” if available).
- Connect it to the same repo. **Start Command:**  
  `php artisan migrate --force && php artisan queue:work --sleep=3 --tries=3`  
  (אם לא מגדירים Start Command, ה-Procfile מריץ את זה אוטומטית – כולל מיגרציות.)
- Copy the same environment variables (especially `APP_KEY`, `DATABASE_URL`) to the Worker service.

### 6. Scheduler (cron – optional)

To run scheduled commands (OTP cleanup, log pruning):

- In Railway: **Scheduled Jobs** or Cron.
- Run every minute: `php artisan schedule:run` (or configure as needed in `app/Console/Kernel.php`).

---

## Troubleshooting

### "Pre-deploy command failed"
- **בודקים לוגים:** ב-Railway → Deployments → בחר את ה-deploy שנכשל → **View Logs**.
- **אם כתוב ש-APP_KEY חסר:** הוסף ב-Variables את `APP_KEY` (הרץ מקומית `php artisan key:generate --show` והדבק).

### "could not translate host name Postgres.railway.internal to address"
החיבור הפנימי של Railway ל-Postgres לא נפתר (DNS). **פתרון:** להשתמש ב־**כתובת ציבורית** של ה-DB.

1. ב-Railway: בחר את שירות **Postgres** (לא את האפליקציה).
2. לך ל-**Variables** או **Connect** – חפש **Public URL** / **DATABASE_PUBLIC_URL** או connection string שמכיל host כמו `xxx.railway.app` (לא `Postgres.railway.internal`).
3. **העתק** את ה-URL הציבורי.
4. בחר את **שירות האפליקציה** (Web) → **Variables**.
5. הוסף משתנה חדש:
   - **Name:** `DATABASE_PUBLIC_URL`
   - **Value:** ההדבקה של ה-URL הציבורי (מתחיל ב-`postgresql://...`).
6. **שמור** ועשה **Redeploy** לשירות האפליקציה.

האפליקציה מוגדרת להעדיף `DATABASE_PUBLIC_URL` על פני `DATABASE_URL`, כך שהחיבור יעבוד דרך הכתובת הציבורית.

---

## Summary

- **Database:** PostgreSQL (recommended) or MySQL; `DATABASE_URL` from Railway.
- **Required variables:** `APP_KEY`, `APP_URL`, and optionally `APP_ENV=production`, `APP_DEBUG=false`.
- **Tables:** Created/updated on each deploy in the **start** phase (`php artisan migrate --force` in `railway.toml`).

After the first deploy, log in to the Admin (Filament) and configure the Recharge token and settings in the UI.
