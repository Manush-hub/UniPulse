<?php

/**
 * Gallery System Verification Script
 * Run this to verify all gallery components are working correctly
 * Visit: /unipulse/verify_gallery.php (while logged in)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require authentication
if (empty($_SESSION['user_id'] ?? '')) {
    die('Please <a href="/unipulse/public/signin">sign in</a> first.');
}

$checks = [];

// Check 1: Database connection
$checks['Database Connection'] = 'Testing...';
try {
    require_once '../app/Core/Controller.php';
    require_once '../app/Core/Database.php';

    class TestDB extends Controller
    {
        use Database;
    }

    $test = new TestDB();
    $conn = $test->connect();
    $checks['Database Connection'] = 'OK ✓';
} catch (Exception $e) {
    $checks['Database Connection'] = 'FAILED ✗ - ' . $e->getMessage();
}

// Check 2: Gallery column exists
$checks['Gallery Column'] = 'Testing...';
try {
    $userType = $_SESSION['type'] ?? 'university';
    $tableName = ($userType === 'public') ? 'public_users' : 'university_users';

    $checkQuery = "SHOW COLUMNS FROM {$tableName} LIKE 'gallery'";
    $test = new TestDB();
    $result = $test->query($checkQuery);

    if ($result && count($result) > 0) {
        $column = $result[0];
        $checks['Gallery Column'] = "OK ✓ ({$column->Type})";
    } else {
        $checks['Gallery Column'] = 'MISSING ✗ - Run migration script';
    }
} catch (Exception $e) {
    $checks['Gallery Column'] = 'ERROR ✗ - ' . $e->getMessage();
}

// Check 3: PHP version (needs 7.4+)
$checks['PHP Version'] = PHP_VERSION;
if (version_compare(PHP_VERSION, '7.4.0') >= 0) {
    $checks['PHP Version'] .= ' ✓';
} else {
    $checks['PHP Version'] .= ' ⚠️ (7.4+ recommended)';
}

// Check 4: JSON functions
$checks['JSON Functions'] = 'Testing...';
if (function_exists('json_encode') && function_exists('json_decode')) {
    $checks['JSON Functions'] = 'OK ✓';
} else {
    $checks['JSON Functions'] = 'MISSING ✗';
}

// Check 5: PDO support
$checks['PDO Support'] = 'Testing...';
if (extension_loaded('pdo') && extension_loaded('pdo_mysql')) {
    $checks['PDO Support'] = 'OK ✓';
} else {
    $checks['PDO Support'] = 'MISSING ✗';
}

// Check 6: File permissions (if needed)
$checks['Temp Directory'] = 'Testing...';
$tmpDir = sys_get_temp_dir();
if (is_writable($tmpDir)) {
    $checks['Temp Directory'] = 'Writable ✓';
} else {
    $checks['Temp Directory'] = 'Not writable ⚠️';
}

// Check 7: Session
$checks['Session User ID'] = $_SESSION['user_id'] ?? 'MISSING ✗';
$checks['Session User Name'] = $_SESSION['name'] ?? 'MISSING ✗';
$checks['Session User Type'] = $_SESSION['type'] ?? 'university (default)';

?>
<!DOCTYPE html>
<html>

<head>
    <title>Gallery System Verification</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .content {
            padding: 30px;
        }

        .check-group {
            margin-bottom: 30px;
        }

        .group-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .check-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            margin-bottom: 8px;
            background: #f9f9f9;
            border-radius: 6px;
            border-left: 4px solid #ddd;
            transition: all 0.3s ease;
        }

        .check-item:hover {
            background: #f5f5f5;
            transform: translateX(5px);
        }

        .check-item.ok {
            border-left-color: #27ae60;
            background: #ecf9f0;
        }

        .check-item.warning {
            border-left-color: #f39c12;
            background: #fef5e7;
        }

        .check-item.error {
            border-left-color: #e74c3c;
            background: #fdedec;
        }

        .check-label {
            font-weight: 500;
            color: #333;
        }

        .check-value {
            color: #666;
            font-family: monospace;
            font-size: 13px;
            text-align: right;
            max-width: 400px;
            overflow-x: auto;
        }

        .summary {
            background: #f0f0f0;
            padding: 20px;
            border-radius: 6px;
            margin-top: 30px;
            text-align: center;
        }

        .summary h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .summary p {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s ease;
            min-width: 150px;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
        }

        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .info-box strong {
            color: #1976d2;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-ok {
            background: #d4edda;
            color: #155724;
        }

        .status-error {
            background: #f8d7da;
            color: #721c24;
        }

        .status-warning {
            background: #fff3cd;
            color: #856404;
        }

        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Gallery System Verification</h1>
            <p>Checking all components for proper installation and configuration</p>
        </div>

        <div class="content">
            <div class="info-box">
                <strong>ℹ️ Information:</strong> This tool verifies that your gallery system is properly configured and ready to use. All checks should pass or show warnings only.
            </div>

            <!-- System Checks -->
            <div class="check-group">
                <div class="group-title">⚙️ System Configuration</div>

                <?php
                $systemChecks = [
                    'PHP Version' => $checks['PHP Version'],
                    'JSON Functions' => $checks['JSON Functions'],
                    'PDO Support' => $checks['PDO Support'],
                    'Temp Directory' => $checks['Temp Directory'],
                ];

                foreach ($systemChecks as $label => $value) {
                    $isOk = strpos($value, '✓') !== false || strpos($value, '(default)') !== false;
                    $isWarning = strpos($value, '⚠️') !== false;
                    $isError = strpos($value, '✗') !== false;

                    $class = $isOk ? 'ok' : ($isWarning ? 'warning' : ($isError ? 'error' : ''));

                    echo "<div class='check-item {$class}'>";
                    echo "<span class='check-label'>{$label}</span>";
                    echo "<span class='check-value'>{$value}</span>";
                    echo "</div>";
                }
                ?>
            </div>

            <!-- Database Checks -->
            <div class="check-group">
                <div class="group-title">🗄️ Database Configuration</div>

                <?php
                $dbChecks = [
                    'Database Connection' => $checks['Database Connection'],
                    'Gallery Column' => $checks['Gallery Column'],
                ];

                foreach ($dbChecks as $label => $value) {
                    $isOk = strpos($value, '✓') !== false;
                    $isWarning = strpos($value, '⚠️') !== false;
                    $isError = strpos($value, '✗') !== false;

                    $class = $isOk ? 'ok' : ($isWarning ? 'warning' : ($isError ? 'error' : ''));

                    echo "<div class='check-item {$class}'>";
                    echo "<span class='check-label'>{$label}</span>";
                    echo "<span class='check-value'>" . htmlspecialchars($value) . "</span>";
                    echo "</div>";
                }
                ?>
            </div>

            <!-- Session Checks -->
            <div class="check-group">
                <div class="group-title">👤 Session Information</div>

                <?php
                $sessionChecks = [
                    'User ID' => $checks['Session User ID'],
                    'User Name' => $checks['Session User Name'],
                    'User Type' => $checks['Session User Type'],
                ];

                foreach ($sessionChecks as $label => $value) {
                    $isOk = strpos($value, 'MISSING') === false;
                    $class = $isOk ? 'ok' : 'error';

                    echo "<div class='check-item {$class}'>";
                    echo "<span class='check-label'>{$label}</span>";
                    echo "<span class='check-value'>" . htmlspecialchars($value) . "</span>";
                    echo "</div>";
                }
                ?>
            </div>

            <!-- Summary -->
            <div class="summary">
                <?php
                $okCount = 0;
                $errorCount = 0;
                $warningCount = 0;

                foreach ($checks as $value) {
                    if (strpos($value, '✓') !== false) $okCount++;
                    if (strpos($value, '✗') !== false) $errorCount++;
                    if (strpos($value, '⚠️') !== false) $warningCount++;
                }
                ?>

                <h3>📊 Summary</h3>
                <p>
                    <span class="status-badge status-ok">✓ <?php echo $okCount; ?> Passed</span>
                    <?php if ($warningCount > 0): ?>
                        <span class="status-badge status-warning">⚠️ <?php echo $warningCount; ?> Warnings</span>
                    <?php endif; ?>
                    <?php if ($errorCount > 0): ?>
                        <span class="status-badge status-error">✗ <?php echo $errorCount; ?> Errors</span>
                    <?php endif; ?>
                </p>

                <?php if ($errorCount === 0): ?>
                    <p style="color: #27ae60; font-weight: bold;">✅ Gallery system is ready to use!</p>
                <?php else: ?>
                    <p style="color: #e74c3c; font-weight: bold;">⚠️ Please fix the errors above before using gallery</p>
                <?php endif; ?>

                <div class="buttons">
                    <a href="/unipulse/public/user/profile" class="btn btn-primary">Go to Profile</a>
                    <a href="/unipulse/debug_gallery.php" class="btn btn-secondary">Debug Tool</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>