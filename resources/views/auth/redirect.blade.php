<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Connecting - Verixa</title>
    <meta http-equiv="refresh" content="0;url={{ $target }}">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            background: #070d18;
            color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            flex-direction: column;
            gap: 20px;
        }
        .card {
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(20, 184, 166, 0.3);
            border-radius: 20px;
            padding: 32px 40px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.6), 0 0 20px rgba(20, 184, 166, 0.15);
            backdrop-filter: blur(12px);
            max-width: 90%;
            width: 360px;
        }
        .spinner {
            width: 48px;
            height: 48px;
            margin: 0 auto 16px;
            border: 4px solid rgba(20, 184, 166, 0.15);
            border-top-color: #14b8a6;
            border-radius: 50%;
            animation: spin 0.75s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .title {
            font-size: 17px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 6px;
        }
        .subtitle {
            font-size: 13px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="spinner"></div>
        <div class="title">Welcome to Verixa</div>
        <div class="subtitle">Opening your dashboard...</div>
    </div>
    <script>
        window.location.replace(@json($target));
    </script>
</body>
</html>
