# Reset Railway DB and create admin (from your machine)

When the DB connection fails from inside the Railway container, you can run migrations and create an admin **from your computer**, connected to Railway’s database.

---

## How to connect your local environment to the server (Railway DB)

### Step 1: Get the DB connection string from Railway

1. Go to **railway.com** → your project.
2. Click the **Postgres** service (the database, not Web or Worker).
3. Open **Variables** (or **Connect** / **Data**).
4. Find **DATABASE_PUBLIC_URL** or **Public URL** or **Postgres Connection URL**.  
   It looks like: `postgresql://postgres:xxxx@yamabiko.proxy.rlwy.net:54326/railway`
5. Copy the **entire** string (Reveal / Copy).

### Step 2: Put it in your local .env

1. In the project root, open **`.env`** (same folder as `composer.json`).
2. Add or update one line (paste what you copied, no spaces):

   ```env
   DATABASE_PUBLIC_URL=postgresql://postgres:xxxx@host:port/railway
   ```

   Replace the right-hand side with the full string from Railway.  
   If `DATABASE_PUBLIC_URL=` already exists, replace its value.

3. (Optional) To force Laravel to use this DB instead of SQLite, add or set:

   ```env
   DB_CONNECTION=pgsql
   ```

4. **Save** `.env`.

Commands like `php artisan migrate` or `php artisan admin:create` run **in your terminal** will now use **Railway’s database**.

### Step 3: Check the connection

In the project folder (PowerShell or CMD):

```powershell
cd C:\Users\user\Desktop\Projects\RechargePortal
php artisan config:clear
php artisan db:show
```

If you see PostgreSQL connection details (host like yamabiko.proxy.rlwy.net), the connection works.

---

## Running the script

### Windows (PowerShell)

```powershell
cd C:\Users\user\Desktop\Projects\RechargePortal

# If you added DATABASE_PUBLIC_URL to .env, no need to set env here.
php artisan config:clear
php artisan migrate:fresh --force
php artisan admin:create --email=aviadmols@gmail.com --password="YourPassword" --name="Aviad"
```

### Mac / Linux (or Git Bash on Windows)

```bash
cd /path/to/RechargePortal
sh setup-railway-db.sh
```

With custom email/password:

```bash
RAILWAY_ADMIN_EMAIL=your@email.com RAILWAY_ADMIN_PASSWORD="YourPassword" RAILWAY_ADMIN_NAME="Your Name" sh setup-railway-db.sh
```

---

## After running

- All tables in Railway’s DB are recreated (migrate:fresh).
- An admin user exists with the email and password you set.
- Log in at **https://your-app.up.railway.app/admin** with that email and password.

## Security

- Do not commit `.env` or `.env.railway` with real credentials to Git.
- You can remove or change sensitive values in `.env` after running.
