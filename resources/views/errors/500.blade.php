<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Error</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f8fafc; color: #1e293b; min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; padding: 1rem; }
        .box { background: #fff; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 2rem; max-width: 28rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        h1 { font-size: 1.25rem; margin: 0 0 0.5rem; }
        p { color: #64748b; font-size: 0.875rem; margin: 0 0 1rem; line-height: 1.5; }
        a { color: #6366f1; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Server Error (500)</h1>
        <p>Something went wrong. This is often due to the database connection from Railway to Postgres.</p>
        <p><strong>Try:</strong> Refresh in a minute. If it keeps failing, ensure you ran migrations from your computer (see SETUP-RAILWAY-DB.md) and that Postgres shows &quot;Database Connection&quot; as connected in Railway.</p>
        <p><a href="javascript:location.reload()">Refresh page</a></p>
    </div>
</body>
</html>
