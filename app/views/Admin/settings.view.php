<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - System Settings</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .settings-page {
            padding: 2rem 0;
            min-height: calc(100vh - 80px);
        }
        .settings-header {
            margin-bottom: 2rem;
        }
        .settings-header h1 {
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 1.75rem;
            font-weight: 600;
        }
        .settings-header p {
            font-size: 0.9375rem;
            color: #666;
        }
        .settings-tabs {
            display: flex;
            gap: 1rem;
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .tab-button {
            padding: 1rem 1.5rem;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.9375rem;
            color: #666;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .tab-button:hover {
            color: #1e3a8a;
        }
        .tab-button.active {
            color: #1e3a8a;
            border-bottom-color: #1e3a8a;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .settings-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .settings-card h3 {
            color: #333;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.125rem;
            font-weight: 600;
        }
        .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .setting-item:last-child {
            border-bottom: none;
        }
        .setting-info {
            flex: 1;
        }
        .setting-info h4 {
            color: #333;
            margin-bottom: 0.25rem;
            font-size: 0.9375rem;
            font-weight: 500;
        }
        .setting-info p {
            color: #666;
            font-size: 0.875rem;
            margin: 0;
            line-height: 1.5;
        }
        .setting-control {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        .toggle-switch {
            position: relative;
            width: 50px;
            height: 26px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 26px;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }
        input:checked + .toggle-slider {
            background-color: #1e3a8a;
        }
        input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }
        .btn-setting {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.9375rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .btn-edit {
            background: #dbeafe;
            color: #1e3a8a;
        }
        .btn-edit:hover {
            background: #bfdbfe;
        }
        .btn-save {
            background: #1e3a8a;
            color: white;
        }
        .btn-save:hover {
            background: #1e40af;
        }
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.9375rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: border-color 0.3s ease;
            margin-top: 0.5rem;
        }
        .form-control:focus {
            outline: none;
            border-color: #1e3a8a;
        }
        select.form-control {
            background-color: white;
            cursor: pointer;
        }
        .info-box {
            background: #dbeafe;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #1e3a8a;
            margin-top: 1rem;
            font-size: 0.9375rem;
        }
        .info-box i {
            color: #1e3a8a;
            margin-right: 0.5rem;
        }
        .info-box strong {
            font-weight: 600;
        }
        .warning-box {
            background: #fff3cd;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #856404;
            margin-top: 1rem;
            font-size: 0.9375rem;
        }
        .warning-box i {
            color: #856404;
            margin-right: 0.5rem;
        }
        .warning-box strong {
            font-weight: 600;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #1e3a8a;
            text-decoration: none;
            margin-bottom: 1rem;
            font-size: 0.9375rem;
            font-weight: 500;
        }
        .back-link:hover {
            color: #1e40af;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .stat-box {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
        }
        .stat-box h4 {
            color: #1e3a8a;
            font-size: 1.75rem;
            font-weight: 600;
            margin: 0;
        }
        .stat-box p {
            color: #666;
            margin: 0.5rem 0 0 0;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <!-- <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="/unipulse/public/admin/dashboard">
                    <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
                </a>
            </div>
            <nav class="nav">
                <a href="/unipulse/public/admin/dashboard">Dashboard</a>
                <a href="/unipulse/public/admin/moderators_list">Moderators</a>
                <a href="/unipulse/public/admin/admins_list">Admins</a>
                <a href="/unipulse/public/admin/settings" class="active">Settings</a>
            </nav>
            <div class="header-actions">
                <div class="user-menu">
                    <img src="/unipulse/public/assets/images/admin.png" alt="Admin" class="admin-avatar">
                    <div class="user-info">
                        <span class="username">System Admin</span>
                        <span class="user-role">System Administrator</span>
                    </div>
                    <div class="user-dropdown">
                        <a href="/unipulse/public/logout" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header> -->

    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'settings'];
    include __DIR__ . '/components/header.php';
    ?>


    <!-- Main Container -->
    <div class="main-container">
        <section class="settings-page">
            <div class="container">
                <a href="/unipulse/public/admin/dashboard" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
                
                <div class="settings-header">
                    <h1><i class="fas fa-cog"></i> System Settings</h1>
                    <p>Configure and manage your UniPulse platform settings</p>
                </div>

                <!-- Settings Tabs -->
                <div class="settings-tabs">
                    <button class="tab-button active" onclick="switchTab('general')">
                        <i class="fas fa-sliders-h"></i> General
                    </button>
                    <button class="tab-button" onclick="switchTab('security')">
                        <i class="fas fa-shield-alt"></i> Security
                    </button>
                    <button class="tab-button" onclick="switchTab('email')">
                        <i class="fas fa-envelope"></i> Email
                    </button>
                    <button class="tab-button" onclick="switchTab('notifications')">
                        <i class="fas fa-bell"></i> Notifications
                    </button>
                    <button class="tab-button" onclick="switchTab('system')">
                        <i class="fas fa-server"></i> System Info
                    </button>
                </div>

                <!-- General Settings Tab -->
                <div id="general" class="tab-content active">
                    <div class="settings-card">
                        <h3><i class="fas fa-globe"></i> Platform Settings</h3>
                        
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Platform Name</h4>
                                <p>The name of your platform displayed across the site</p>
                            </div>
                            <div class="setting-control">
                                <input type="text" class="form-control" value="UniPulse" style="width: 300px;">
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Platform Description</h4>
                                <p>A short description of your platform</p>
                            </div>
                            <div class="setting-control">
                                <input type="text" class="form-control" value="University Event Management Platform" style="width: 300px;">
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Maintenance Mode</h4>
                                <p>Enable maintenance mode to prevent user access</p>
                            </div>
                            <div class="setting-control">
                                <label class="toggle-switch">
                                    <input type="checkbox" onchange="alert('Maintenance mode toggled!')">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>User Registration</h4>
                                <p>Allow new users to register on the platform</p>
                            </div>
                            <div class="setting-control">
                                <label class="toggle-switch">
                                    <input type="checkbox" checked onchange="alert('User registration toggled!')">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Default Timezone</h4>
                                <p>Set the default timezone for the platform</p>
                            </div>
                            <div class="setting-control">
                                <select class="form-control" style="width: 300px;">
                                    <option>UTC</option>
                                    <option selected>Asia/Colombo</option>
                                    <option>America/New_York</option>
                                    <option>Europe/London</option>
                                    <option>Asia/Tokyo</option>
                                </select>
                            </div>
                        </div>

                        <div style="text-align: right; margin-top: 1rem;">
                            <button class="btn-setting btn-save" onclick="alert('General settings saved!')">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Security Settings Tab -->
                <div id="security" class="tab-content">
                    <div class="settings-card">
                        <h3><i class="fas fa-lock"></i> Security Settings</h3>
                        
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Two-Factor Authentication</h4>
                                <p>Require 2FA for admin accounts</p>
                            </div>
                            <div class="setting-control">
                                <label class="toggle-switch">
                                    <input type="checkbox" onchange="alert('2FA setting toggled!')">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Session Timeout</h4>
                                <p>Automatically log out inactive users after</p>
                            </div>
                            <div class="setting-control">
                                <select class="form-control" style="width: 200px;">
                                    <option>15 minutes</option>
                                    <option selected>30 minutes</option>
                                    <option>1 hour</option>
                                    <option>2 hours</option>
                                    <option>Never</option>
                                </select>
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Password Requirements</h4>
                                <p>Minimum password length</p>
                            </div>
                            <div class="setting-control">
                                <select class="form-control" style="width: 200px;">
                                    <option>6 characters</option>
                                    <option selected>8 characters</option>
                                    <option>10 characters</option>
                                    <option>12 characters</option>
                                </select>
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Login Attempts Limit</h4>
                                <p>Maximum failed login attempts before lockout</p>
                            </div>
                            <div class="setting-control">
                                <select class="form-control" style="width: 200px;">
                                    <option>3 attempts</option>
                                    <option selected>5 attempts</option>
                                    <option>10 attempts</option>
                                    <option>Unlimited</option>
                                </select>
                            </div>
                        </div>

                        <div class="warning-box">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Warning:</strong> Changing security settings may affect all user accounts. Please test thoroughly before applying.
                        </div>

                        <div style="text-align: right; margin-top: 1rem;">
                            <button class="btn-setting btn-save" onclick="alert('Security settings saved!')">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Email Settings Tab -->
                <div id="email" class="tab-content">
                    <div class="settings-card">
                        <h3><i class="fas fa-envelope"></i> Email Configuration</h3>
                        
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>SMTP Host</h4>
                                <p>Your SMTP server address</p>
                            </div>
                            <div class="setting-control">
                                <input type="text" class="form-control" value="smtp.gmail.com" style="width: 300px;">
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>SMTP Port</h4>
                                <p>SMTP server port (usually 587 or 465)</p>
                            </div>
                            <div class="setting-control">
                                <input type="number" class="form-control" value="587" style="width: 150px;">
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>SMTP Username</h4>
                                <p>Your SMTP username or email</p>
                            </div>
                            <div class="setting-control">
                                <input type="email" class="form-control" value="noreply@unipulse.com" style="width: 300px;">
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>From Name</h4>
                                <p>Name displayed in outgoing emails</p>
                            </div>
                            <div class="setting-control">
                                <input type="text" class="form-control" value="UniPulse Team" style="width: 300px;">
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Email Notifications</h4>
                                <p>Send email notifications to users</p>
                            </div>
                            <div class="setting-control">
                                <label class="toggle-switch">
                                    <input type="checkbox" checked onchange="alert('Email notifications toggled!')">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="info-box">
                            <i class="fas fa-info-circle"></i>
                            <strong>Test Email:</strong> Send a test email to verify your SMTP configuration is working correctly.
                            <button class="btn-setting btn-edit" style="margin-left: 1rem;" onclick="alert('Test email sent!')">
                                <i class="fas fa-paper-plane"></i> Send Test Email
                            </button>
                        </div>

                        <div style="text-align: right; margin-top: 1rem;">
                            <button class="btn-setting btn-save" onclick="alert('Email settings saved!')">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Notifications Settings Tab -->
                <div id="notifications" class="tab-content">
                    <div class="settings-card">
                        <h3><i class="fas fa-bell"></i> Notification Settings</h3>
                        
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>New Event Notifications</h4>
                                <p>Notify users when new events are published</p>
                            </div>
                            <div class="setting-control">
                                <label class="toggle-switch">
                                    <input type="checkbox" checked onchange="alert('Event notifications toggled!')">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Comment Notifications</h4>
                                <p>Notify users of new comments on their events</p>
                            </div>
                            <div class="setting-control">
                                <label class="toggle-switch">
                                    <input type="checkbox" checked onchange="alert('Comment notifications toggled!')">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Admin Notifications</h4>
                                <p>Notify admins of important system events</p>
                            </div>
                            <div class="setting-control">
                                <label class="toggle-switch">
                                    <input type="checkbox" checked onchange="alert('Admin notifications toggled!')">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Weekly Digest</h4>
                                <p>Send weekly summary emails to users</p>
                            </div>
                            <div class="setting-control">
                                <label class="toggle-switch">
                                    <input type="checkbox" onchange="alert('Weekly digest toggled!')">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Browser Push Notifications</h4>
                                <p>Enable browser push notifications</p>
                            </div>
                            <div class="setting-control">
                                <label class="toggle-switch">
                                    <input type="checkbox" checked onchange="alert('Push notifications toggled!')">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div style="text-align: right; margin-top: 1rem;">
                            <button class="btn-setting btn-save" onclick="alert('Notification settings saved!')">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>

                <!-- System Info Tab -->
                <div id="system" class="tab-content">
                    <div class="settings-card">
                        <h3><i class="fas fa-info-circle"></i> System Information</h3>
                        
                        <div class="stats-grid">
                            <div class="stat-box">
                                <h4>v2.1.0</h4>
                                <p>Platform Version</p>
                            </div>
                            <div class="stat-box">
                                <h4>PHP 8.1</h4>
                                <p>PHP Version</p>
                            </div>
                            <div class="stat-box">
                                <h4>MySQL 8.0</h4>
                                <p>Database Version</p>
                            </div>
                            <div class="stat-box">
                                <h4>99.8%</h4>
                                <p>Uptime</p>
                            </div>
                        </div>

                        <div class="setting-item" style="margin-top: 2rem;">
                            <div class="setting-info">
                                <h4>Server Time</h4>
                                <p>Current server timestamp</p>
                            </div>
                            <div class="setting-control">
                                <strong><?php echo date('Y-m-d H:i:s'); ?></strong>
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Database Size</h4>
                                <p>Total database storage used</p>
                            </div>
                            <div class="setting-control">
                                <strong>245 MB</strong>
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Total Events</h4>
                                <p>Number of events in the system</p>
                            </div>
                            <div class="setting-control">
                                <strong>1,247</strong>
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Total Users</h4>
                                <p>Registered users across all types</p>
                            </div>
                            <div class="setting-control">
                                <strong>2,847</strong>
                            </div>
                        </div>

                        <div class="info-box">
                            <i class="fas fa-info-circle"></i>
                            <strong>System Health:</strong> All systems are operating normally. Last checked: <?php echo date('Y-m-d H:i:s'); ?>
                        </div>
                    </div>

                    <div class="settings-card">
                        <h3><i class="fas fa-tools"></i> System Maintenance</h3>
                        
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Clear Cache</h4>
                                <p>Clear all cached data to improve performance</p>
                            </div>
                            <div class="setting-control">
                                <button class="btn-setting btn-edit" onclick="alert('Cache cleared successfully!')">
                                    <i class="fas fa-broom"></i> Clear Cache
                                </button>
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Database Backup</h4>
                                <p>Create a backup of the database</p>
                            </div>
                            <div class="setting-control">
                                <button class="btn-setting btn-edit" onclick="alert('Database backup started!')">
                                    <i class="fas fa-database"></i> Backup Now
                                </button>
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>System Logs</h4>
                                <p>View and download system logs</p>
                            </div>
                            <div class="setting-control">
                                <button class="btn-setting btn-edit" onclick="alert('Opening logs...')">
                                    <i class="fas fa-file-alt"></i> View Logs
                                </button>
                            </div>
                        </div>

                        <div class="warning-box">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Warning:</strong> System maintenance operations may temporarily affect platform performance.
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tab contents
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => content.classList.remove('active'));
            
            // Deactivate all tab buttons
            const buttons = document.querySelectorAll('.tab-button');
            buttons.forEach(button => button.classList.remove('active'));
            
            // Show selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Activate clicked tab button
            event.target.closest('.tab-button').classList.add('active');
        }
    </script>
</body>
</html>
