<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Access Denied</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Moderator/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="/unipulse/public/moderator/dashboard">
                    <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
                </a>
            </div>
            <nav class="nav">
                <a href="/unipulse/public/moderator/dashboard">Dashboard</a>
                <a href="/unipulse/public/logout">Logout</a>
            </nav>
        </div>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        <div class="access-denied-container">
            <div class="access-denied-content">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h1>Access Denied</h1>
                <p class="error-message">
                    <?= htmlspecialchars($error ?? 'You do not have permission to access this page.') ?>
                </p>
                <div class="actions">
                    <a href="/unipulse/public/moderator/dashboard" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <a href="/unipulse/public/logout" class="btn btn-outline">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .access-denied-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 60vh;
            padding: 40px 20px;
        }

        .access-denied-content {
            text-align: center;
            background: white;
            padding: 60px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        .error-icon {
            font-size: 4rem;
            color: #f56565;
            margin-bottom: 20px;
        }

        .access-denied-content h1 {
            font-size: 2rem;
            color: #2d3748;
            margin-bottom: 15px;
        }

        .error-message {
            color: #718096;
            font-size: 1.1rem;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background-color: #5a67d8;
            transform: translateY(-1px);
        }

        .btn-outline {
            background-color: transparent;
            color: #667eea;
            border: 1px solid #667eea;
        }

        .btn-outline:hover {
            background-color: #667eea;
            color: white;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .access-denied-content {
                padding: 40px 20px;
            }

            .access-denied-content h1 {
                font-size: 1.5rem;
            }

            .actions {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 200px;
                justify-content: center;
            }
        }
    </style>
</body>

</html>