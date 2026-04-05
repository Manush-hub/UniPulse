<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Health - UniPulse</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/dashboard-style.css">
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/css/extracted/Admin_system_health.css">
</head>

<body>
    <!-- Header -->
    <?php $pageConfig = ['activeNav' => 'dashboard']; ?>
    <?php include __DIR__ . '/components/header.php'; ?>

    <!-- Main Container -->
    <div class="main-container">
        <div class="container">
            <!-- System Health Section -->
            <section class="system-health-container">
                <h1>System Health</h1>
                <p>Monitor system performance and resource utilization</p>

                <div class="health-metrics">
                    <div class="metric-card">
                        <div class="metric-header">
                            <div class="metric-title">CPU Usage</div>
                            <div class="metric-value" id="cpuValue">65%</div>
                        </div>
                        <div class="metric-bar">
                            <div class="metric-fill" style="width: 65%"></div>
                        </div>
                        <div class="metric-details">
                            <div>Processes: <span id="cpuProcesses">142</span></div>
                            <div>Load Average: <span id="cpuLoad">1.2, 1.5, 1.8</span></div>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-header">
                            <div class="metric-title">Memory Usage</div>
                            <div class="metric-value" id="memoryValue">42%</div>
                        </div>
                        <div class="metric-bar">
                            <div class="metric-fill" style="width: 42%"></div>
                        </div>
                        <div class="metric-details">
                            <div>Used: <span id="memoryUsed">3.2 GB</span></div>
                            <div>Available: <span id="memoryAvailable">4.4 GB</span></div>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-header">
                            <div class="metric-title">Storage</div>
                            <div class="metric-value" id="storageValue">78%</div>
                        </div>
                        <div class="metric-bar">
                            <div class="metric-fill" style="width: 78%"></div>
                        </div>
                        <div class="metric-details">
                            <div>Used: <span id="storageUsed">156 GB</span></div>
                            <div>Free: <span id="storageFree">44 GB</span></div>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-header">
                            <div class="metric-title">Uptime</div>
                            <div class="metric-value" id="uptimeValue">98%</div>
                        </div>
                        <div class="metric-bar">
                            <div class="metric-fill" style="width: 98%"></div>
                        </div>
                        <div class="metric-details">
                            <div>Last Restart: <span id="lastRestart">15 days ago</span></div>
                            <div>Response Time: <span id="responseTime">1.2s</span></div>
                        </div>
                    </div>
                </div>

                <div class="system-logs">
                    <div class="log-header">
                        <h3>Recent System Logs</h3>
                        <button class="refresh-btn" id="refreshLogs">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                    <div class="log-list" id="logList">
                        <div class="log-item">
                            <div class="log-time">2025-03-15 14:32:10</div>
                            <div class="log-message">User registration processed successfully</div>
                            <div class="log-level level-info">INFO</div>
                        </div>
                        <div class="log-item">
                            <div class="log-time">2025-03-15 14:28:45</div>
                            <div class="log-message">Event published: Annual Cultural Fest</div>
                            <div class="log-level level-info">INFO</div>
                        </div>
                        <div class="log-item">
                            <div class="log-time">2025-03-15 14:25:12</div>
                            <div class="log-message">Database backup completed</div>
                            <div class="log-level level-info">INFO</div>
                        </div>
                        <div class="log-item">
                            <div class="log-time">2025-03-15 14:20:33</div>
                            <div class="log-message">Payment processing delay detected</div>
                            <div class="log-level level-warning">WARNING</div>
                        </div>
                        <div class="log-item">
                            <div class="log-time">2025-03-15 14:15:07</div>
                            <div class="log-message">Failed login attempt from IP: 192.168.1.105</div>
                            <div class="log-level level-warning">WARNING</div>
                        </div>
                        <div class="log-item">
                            <div class="log-time">2025-03-15 14:10:22</div>
                            <div class="log-message">Memory usage threshold exceeded</div>
                            <div class="log-level level-error">ERROR</div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-links">
                <a href="#terms">Terms of Service</a>
                <a href="#privacy">Privacy Policy</a>
                <a href="#contact">Contact Support</a>
                <a href="#about">About UniPulse</a>
            </div>
            <div class="footer-copyright">
                <span>&copy; 2025 UniPulse. All rights reserved.</span>
                <span class="system-version">v2.4.1</span>
            </div>
        </div>
    </footer>

    <script src="<?php echo ROOT ?>/assets/js/extracted/Admin_system_health.js"></script>
</body>

</html>