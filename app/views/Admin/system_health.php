<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Health - UniPulse</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Admin/dashboard-style.css">
    <style>
        /* Additional styles specific to the system health page */
        .system-health-container {
            padding: 20px;
            background-color: #f8fafc;
            border-radius: 12px;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .system-health-container h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .system-health-container p {
            color: #666;
            margin-bottom: 2rem;
        }

        .health-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .metric-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-color: #1E3A8A;
        }

        .metric-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .metric-title {
            font-weight: 600;
            color: #1f2937;
            font-size: 1.1rem;
        }

        .metric-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1E3A8A;
        }

        .metric-bar {
            height: 8px;
            background-color: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin: 10px 0;
        }

        .metric-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
            background: linear-gradient(135deg, #1E3A8A, #F97316);
        }

        .metric-details {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .metric-details div {
            font-size: 0.9rem;
            color: #666;
        }

        .metric-details span {
            font-weight: 600;
            color: #1E3A8A;
        }

        .system-logs {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .log-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .log-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
        }

        .refresh-btn {
            background-color: #1E3A8A;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .refresh-btn:hover {
            background: #1e40af;
            transform: translateY(-1px);
        }

        .log-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .log-item {
            padding: 1rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .log-item:last-child {
            border-bottom: none;
        }

        .log-time {
            color: #6c757d;
            font-size: 0.85rem;
            width: 150px;
        }

        .log-message {
            flex-grow: 1;
            margin: 0 15px;
            font-size: 0.9rem;
        }

        .log-level {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            width: 70px;
            text-align: center;
        }

        .level-info {
            background: #f0f9ff;
            color: #1E3A8A;
        }

        .level-warning {
            background: #fef3c7;
            color: #d97706;
        }

        .level-error {
            background: #f8d7da;
            color: #721c24;
        }

        /* Responsive Design for system health page */
        @media (max-width: 1024px) {
            .health-metrics {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .health-metrics {
                grid-template-columns: 1fr;
            }

            .log-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .log-time {
                width: auto;
            }
        }
    </style>
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

    <script>
        // Simulate updating metrics
        function updateMetrics() {
            // In a real implementation, this would fetch data from the server
            document.getElementById('cpuValue').textContent = '65%';
            document.getElementById('memoryValue').textContent = '42%';
            document.getElementById('storageValue').textContent = '78%';
            document.getElementById('uptimeValue').textContent = '98%';
        }

        document.getElementById('refreshLogs').addEventListener('click', function() {
            // In a real implementation, this would fetch updated logs
            alert('Logs refreshed!');
        });

        // Initial load
        updateMetrics();

    </script>
</body>

</html>