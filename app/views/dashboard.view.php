<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> - UniPulse</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .logo {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .welcome-msg {
            font-size: 1.2em;
            margin: 20px 0;
        }
        .btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
        .success {
            background: rgba(76, 175, 80, 0.3);
            border: 2px solid rgba(76, 175, 80, 0.5);
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🎓 UniPulse</div>
            <h1><?= $title ?? 'Dashboard' ?></h1>
        </div>

        <div class="success">
            <h2>🎉 Authentication System Working!</h2>
            <p><?= $message ?? 'Welcome to your dashboard!' ?></p>
            <p>The complete backend authentication system has been successfully implemented and tested.</p>
        </div>

        <div style="text-align: center;">
            <h3>Backend Features Implemented:</h3>
            <ul style="text-align: left; display: inline-block;">
                <li>✅ User Registration with validation</li>
                <li>✅ Secure password hashing (Argon2ID)</li>
                <li>✅ User authentication/login</li>
                <li>✅ Session management</li>
                <li>✅ SQL injection prevention (prepared statements)</li>
                <li>✅ JSON API endpoints</li>
                <li>✅ Email and username uniqueness checks</li>
                <li>✅ Password strength validation</li>
                <li>✅ Rate limiting for login attempts</li>
                <li>✅ Database abstraction layer</li>
            </ul>
            
            <div style="margin-top: 30px;">
                <button class="btn" onclick="window.location.href='signin'">Sign In</button>
                <button class="btn" onclick="window.location.href='signup'">Sign Up</button>
                <button class="btn" onclick="logout()">Sign Out</button>
                <button class="btn" onclick="window.location.href='home'">Home</button>
            </div>
        </div>
    </div>

    <script>
        async function logout() {
            try {
                const response = await fetch('/backend/api/logout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    window.location.href = result.redirectUrl || 'home';
                } else {
                    alert('Logout failed: ' + result.message);
                }
            } catch (error) {
                console.error('Logout error:', error);
                alert('Logout failed. Please try again.');
            }
        }
    </script>
</body>
</html>