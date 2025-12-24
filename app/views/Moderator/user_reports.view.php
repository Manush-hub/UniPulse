<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Reports - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/Moderator/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .reports-filters {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-group label {
            font-weight: 600;
            color: #374151;
            font-size: 0.9rem;
        }

        .filter-group select {
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: white;
        }

        .report-details {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            display: none;
        }

        .report-details.show {
            display: block;
        }

        .bulk-actions {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'dashboard'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Page Header -->
        <section class="welcome-section">
            <div class="container">
                <div class="welcome-content">
                    <div class="welcome-text">
                        <h1>User Reports Management</h1>
                        <p>Handle user-reported content and maintain platform quality</p>
                        <div class="quick-stats">
                            <div class="stat-item">
                                <span class="stat-number" id="pendingReports">23</span>
                                <span class="stat-label">Pending Reports</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="resolvedToday">12</span>
                                <span class="stat-label">Resolved Today</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="avgResolution">2.4h</span>
                                <span class="stat-label">Avg. Resolution</span>
                            </div>
                        </div>
                    </div>
                    <div class="welcome-actions">
                        <button class="btn btn-primary" onclick="exportReports()">
                            <i class="fas fa-download"></i>
                            Export Reports
                        </button>
                        <button class="btn btn-outline" onclick="showReportGuidelines()">
                            <i class="fas fa-book"></i>
                            Guidelines
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Filters -->
        <section class="quick-actions">
            <div class="container">
                <div class="reports-filters">
                    <div class="filter-group">
                        <label for="statusFilter">Status:</label>
                        <select id="statusFilter" onchange="filterReports()">
                            <option value="all">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="in-progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="typeFilter">Report Type:</label>
                        <select id="typeFilter" onchange="filterReports()">
                            <option value="all">All Types</option>
                            <option value="inappropriate">Inappropriate Content</option>
                            <option value="spam">Spam</option>
                            <option value="misinformation">Misinformation</option>
                            <option value="harassment">Harassment</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="priorityFilter">Priority:</label>
                        <select id="priorityFilter" onchange="filterReports()">
                            <option value="all">All Priorities</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="dateFilter">Reported Date:</label>
                        <select id="dateFilter" onchange="filterReports()">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bulk Actions -->
        <section class="recent-activity">
            <div class="container">
                <div class="bulk-actions">
                    <h3>Bulk Actions</h3>
                    <div class="action-buttons">
                        <button class="btn btn-outline" onclick="selectAllReports()">
                            <i class="fas fa-check-square"></i>
                            Select All
                        </button>
                        <button class="btn btn-primary" onclick="assignSelected()">
                            <i class="fas fa-user-check"></i>
                            Assign to Me
                        </button>
                        <button class="btn reject" onclick="resolveSelected()">
                            <i class="fas fa-check-double"></i>
                            Mark as Resolved
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Reports Table -->
        <section class="user-reports">
            <div class="container">
                <div class="section-header">
                    <h2>User Reports</h2>
                    <span class="badge" id="reportsCount">23 reports</span>
                </div>
                <div class="reports-table">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 30px;">
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </th>
                                <th>Reported Content</th>
                                <th>Report Type</th>
                                <th>Priority</th>
                                <th>Submitted</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="reportsTableBody">
                            <tr>
                                <td><input type="checkbox" class="report-checkbox" value="1"></td>
                                <td>
                                    <div class="report-content">"Spring Party" Event Description</div>
                                    <div class="report-reason">Contains inappropriate language</div>
                                </td>
                                <td>
                                    <span class="report-type type-inappropriate">Inappropriate</span>
                                </td>
                                <td>
                                    <span class="priority-high">High</span>
                                </td>
                                <td>2 hours ago</td>
                                <td>
                                    <span class="report-status status-pending">Pending</span>
                                </td>
                                <td>-</td>
                                <td>
                                    <div class="table-actions">
                                        <button class="action-btn view" onclick="viewReport(1)" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn resolve" onclick="assignToMe(1)" title="Assign to Me">
                                            <i class="fas fa-user-check"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="report-checkbox" value="2"></td>
                                <td>
                                    <div class="report-content">User Comment on "Tech Talk"</div>
                                    <div class="report-reason">Spam content</div>
                                </td>
                                <td>
                                    <span class="report-type type-spam">Spam</span>
                                </td>
                                <td>
                                    <span class="priority-medium">Medium</span>
                                </td>
                                <td>5 hours ago</td>
                                <td>
                                    <span class="report-status status-in-progress">In Progress</span>
                                </td>
                                <td>Lisa Chen</td>
                                <td>
                                    <div class="table-actions">
                                        <button class="action-btn view" onclick="viewReport(2)" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn resolve" onclick="resolveReport(2)" title="Resolve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Report Details Modal -->
    <div id="reportModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <span class="close-button" onclick="closeModal('reportModal')">&times;</span>
            <h3 id="modalTitle">Report Details</h3>
            <div class="modal-body" id="modalBody">
                <!-- Report details will be loaded here -->
            </div>
        </div>
    </div>

    <script src="/unipulse/public/assets/js/Moderator/reports.js"></script>
    <script src="/unipulse/public/assets/js/Moderator/header.js"></script>
</body>

</html>