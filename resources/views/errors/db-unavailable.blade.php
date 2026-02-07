<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Database temporarily unavailable</title>
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
        <h1>Database temporarily unavailable</h1>
        <p>The database connection is not ready yet. This often happens on Railway when Postgres is still starting or the proxy is reconnecting.</p>
        <p><strong>What to do:</strong> In Railway, open the <strong>Postgres</strong> service and wait until "Database Connection" shows connected. Then refresh this page.</p>
        <p><a href="javascript:location.reload()">Refresh page</a></p>
    </div>
</body>
</html>
