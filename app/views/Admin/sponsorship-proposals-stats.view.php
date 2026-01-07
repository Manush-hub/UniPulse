<?php
// Admin Sponsorship Proposals Statistics View
$user = $data['user'] ?? [];
$stats = $data['stats'] ?? null;
$topSponsors = $data['topSponsors'] ?? [];
$proposalsByType = $data['proposalsByType'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sponsorship Proposals Statistics - Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 15px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .back-btn:hover {
            background: #764ba2;
        }
        .page-header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .page-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            border-radius: 8px;
            color: white;
            text-align: center;
        }
        .stat-card.blue {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        }
        .stat-card.green {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        }
        .stat-card.orange {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        }
        .stat-card.red {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }
        .stat-card.purple {
            background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
        }
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 13px;
            opacity: 0.9;
        }
        .section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .section h2 {
            color: #667eea;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
            font-size: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background: #f9f9f9;
            border-bottom: 2px solid #e0e0e0;
        }
        th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            font-size: 13px;
            text-transform: uppercase;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 13px;
            color: #666;
        }
        tbody tr:hover {
            background: #f9f9f9;
        }
        .sponsor-name {
            color: #667eea;
            font-weight: 600;
        }
        .type-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #f0f0f0;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .type-badge.monetary {
            background: #e8f5e9;
            color: #27ae60;
        }
        .type-badge.in-kind {
            background: #fff3e0;
            color: #f39c12;
        }
        .type-badge.service {
            background: #e3f2fd;
            color: #3498db;
        }
        .type-badge.mixed {
            background: #f3e5f5;
            color: #9b59b6;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 8px;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 11px;
            font-weight: bold;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        .empty-state h3 {
            color: #666;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/unipulse/public/admin/sponsorshipproposals" class="back-btn">← Back to Proposals</a>
        
        <!-- Page Header -->
        <div class="page-header">
            <h1>📊 Sponsorship Proposals Statistics</h1>
            
            <?php if ($stats): ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats->total_proposals; ?></div>
                    <div class="stat-label">Total Proposals</div>
                </div>
                <div class="stat-card blue">
                    <div class="stat-number"><?php echo $stats->submitted_proposals ?? 0; ?></div>
                    <div class="stat-label">New Submissions</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-number"><?php echo $stats->under_review_proposals ?? 0; ?></div>
                    <div class="stat-label">Under Review</div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-number"><?php echo $stats->negotiating_proposals ?? 0; ?></div>
                    <div class="stat-label">Negotiating</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-number"><?php echo $stats->accepted_proposals ?? 0; ?></div>
                    <div class="stat-label">Accepted</div>
                </div>
                <div class="stat-card red">
                    <div class="stat-number"><?php echo $stats->rejected_proposals ?? 0; ?></div>
                    <div class="stat-label">Rejected</div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="grid-2">
            <!-- Financial Overview -->
            <div class="section">
                <h2>💰 Financial Overview</h2>
                <?php if ($stats && $stats->total_monetary_value): ?>
                <div style="padding: 20px; background: #f9f9f9; border-radius: 5px; text-align: center;">
                    <div style="font-size: 28px; font-weight: bold; color: #27ae60; margin-bottom: 5px;">
                        $<?php echo number_format($stats->total_monetary_value, 2); ?>
                    </div>
                    <div style="color: #999; font-size: 13px;">Total Accepted Monetary Sponsorships</div>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <p>No monetary sponsorships accepted yet.</p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Engagement Metrics -->
            <div class="section">
                <h2>📈 Engagement Metrics</h2>
                <?php if ($stats): ?>
                <div style="padding: 20px;">
                    <div style="margin-bottom: 15px;">
                        <div style="font-size: 13px; color: #999; margin-bottom: 5px;">Average Views per Proposal</div>
                        <div style="font-size: 24px; font-weight: bold; color: #667eea;">
                            <?php echo round($stats->avg_views ?? 0); ?> views
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 13px; color: #999; margin-bottom: 5px;">Unique Sponsors</div>
                        <div style="font-size: 24px; font-weight: bold; color: #3498db;">
                            <?php echo $stats->total_sponsors ?? 0; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Proposals by Type -->
        <div class="section">
            <h2>📋 Proposals by Type</h2>
            <?php if (!empty($proposalsByType)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Total</th>
                        <th>Accepted</th>
                        <th>Acceptance Rate</th>
                        <th>Progress</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proposalsByType as $type): ?>
                    <tr>
                        <td>
                            <span class="type-badge <?php echo str_replace('-', '-', $type->proposal_type); ?>">
                                <?php echo ucfirst(str_replace('-', ' ', $type->proposal_type)); ?>
                            </span>
                        </td>
                        <td><?php echo $type->count; ?></td>
                        <td><?php echo $type->accepted_count ?? 0; ?></td>
                        <td>
                            <?php 
                                $rate = $type->count > 0 ? round(($type->accepted_count ?? 0) / $type->count * 100) : 0;
                                echo $rate . '%';
                            ?>
                        </td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $rate; ?>%;">
                                    <?php if ($rate > 5) echo $rate . '%'; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <p>No proposal data available.</p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Top Sponsors -->
        <div class="section">
            <h2>🏆 Top Sponsors</h2>
            <?php if (!empty($topSponsors)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Sponsor</th>
                        <th>Total Proposals</th>
                        <th>Accepted</th>
                        <th>Success Rate</th>
                        <th>Progress</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topSponsors as $sponsor): ?>
                    <tr>
                        <td class="sponsor-name"><?php echo htmlspecialchars($sponsor->company_name ?? 'Unknown'); ?></td>
                        <td><?php echo $sponsor->proposal_count ?? 0; ?></td>
                        <td><?php echo $sponsor->accepted_count ?? 0; ?></td>
                        <td>
                            <?php 
                                $rate = ($sponsor->proposal_count ?? 0) > 0 ? round(($sponsor->accepted_count ?? 0) / $sponsor->proposal_count * 100) : 0;
                                echo $rate . '%';
                            ?>
                        </td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $rate; ?>%;">
                                    <?php if ($rate > 5) echo $rate . '%'; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <p>No sponsor data available.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
