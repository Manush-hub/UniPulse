<?php
// Admin Sponsorship Proposals List View
$user = $data['user'] ?? [];
$proposals = $data['proposals'] ?? [];
$status = $data['status'] ?? 'submitted';
$page_title = $data['page_title'] ?? 'Sponsorship Proposals';

function getStatusBadge($status) {
    $colors = [
        'submitted' => '#3498db',
        'under_review' => '#f39c12',
        'negotiating' => '#9b59b6',
        'accepted' => '#27ae60',
        'rejected' => '#e74c3c',
        'draft' => '#95a5a6'
    ];
    $text = ucfirst(str_replace('_', ' ', $status));
    $color = $colors[$status] ?? '#95a5a6';
    return '<span style="background: '.$color.'; color: white; padding: 5px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">'.$text.'</span>';
}

function getTypeIcon($type) {
    $icons = [
        'monetary' => '💵',
        'in-kind' => '🎁',
        'service' => '🔧',
        'mixed' => '🤝'
    ];
    return $icons[$type] ?? '📋';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Admin</title>
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
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .page-header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .page-header h1 {
            color: #333;
            font-size: 28px;
        }
        .header-stats {
            display: flex;
            gap: 20px;
        }
        .stat-box {
            background: #f9f9f9;
            padding: 15px 20px;
            border-radius: 5px;
            border-left: 4px solid #667eea;
        }
        .stat-number {
            font-size: 20px;
            font-weight: bold;
            color: #667eea;
        }
        .stat-label {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        .filters {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .filter-btn {
            padding: 8px 16px;
            border: 2px solid #ddd;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s;
        }
        .filter-btn.active {
            border-color: #667eea;
            background: #667eea;
            color: white;
            font-weight: 600;
        }
        .filter-btn:hover {
            border-color: #667eea;
        }
        .proposals-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            font-size: 13px;
            text-transform: uppercase;
        }
        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
            color: #666;
        }
        tbody tr:hover {
            background: #f9f9f9;
        }
        .proposal-title {
            font-weight: 600;
            color: #333;
        }
        .proposal-type {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 8px;
            background: #f0f0f0;
            border-radius: 4px;
            font-size: 12px;
        }
        .sponsor-info {
            color: #667eea;
            font-weight: 600;
        }
        .event-name {
            color: #999;
            font-size: 12px;
        }
        .actions {
            display: flex;
            gap: 8px;
        }
        .btn-view {
            padding: 6px 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-view:hover {
            background: #764ba2;
        }
        .btn-accept {
            padding: 6px 12px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-accept:hover {
            background: #229954;
        }
        .btn-reject {
            padding: 6px 12px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-reject:hover {
            background: #c0392b;
        }
        .date-cell {
            font-size: 12px;
            color: #999;
        }
        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #999;
        }
        .empty-state h3 {
            color: #666;
            margin-bottom: 10px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        .modal-header {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }
        .modal-body {
            margin-bottom: 20px;
        }
        .modal-body textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
            min-height: 100px;
        }
        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #764ba2;
        }
        .btn-secondary {
            background: #ddd;
            color: #333;
        }
        .btn-secondary:hover {
            background: #ccc;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1><?php echo htmlspecialchars($page_title); ?></h1>
            <div class="header-stats">
                <div class="stat-box">
                    <div class="stat-number"><?php echo count($proposals); ?></div>
                    <div class="stat-label">Current Status</div>
                </div>
                <a href="/unipulse/public/admin/sponsorshipproposals/stats" style="text-decoration: none;">
                    <div class="stat-box">
                        <div class="stat-number">📊</div>
                        <div class="stat-label">View Statistics</div>
                    </div>
                </a>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filters">
            <a href="/unipulse/public/admin/sponsorshipproposals?status=submitted" class="filter-btn <?php echo ($status === 'submitted') ? 'active' : ''; ?>">
                New Submissions (<?php echo count(array_filter($proposals, fn($p) => $p->status === 'submitted')); ?>)
            </a>
            <a href="/unipulse/public/admin/sponsorshipproposals?status=under_review" class="filter-btn <?php echo ($status === 'under_review') ? 'active' : ''; ?>">
                Under Review (<?php echo count(array_filter($proposals, fn($p) => $p->status === 'under_review')); ?>)
            </a>
            <a href="/unipulse/public/admin/sponsorshipproposals?status=negotiating" class="filter-btn <?php echo ($status === 'negotiating') ? 'active' : ''; ?>">
                Negotiating (<?php echo count(array_filter($proposals, fn($p) => $p->status === 'negotiating')); ?>)
            </a>
            <a href="/unipulse/public/admin/sponsorshipproposals?status=accepted" class="filter-btn <?php echo ($status === 'accepted') ? 'active' : ''; ?>">
                Accepted
            </a>
            <a href="/unipulse/public/admin/sponsorshipproposals?status=rejected" class="filter-btn <?php echo ($status === 'rejected') ? 'active' : ''; ?>">
                Rejected
            </a>
        </div>
        
        <!-- Proposals Table -->
        <div class="proposals-table">
            <?php if (!empty($proposals)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Sponsor</th>
                        <th>Event</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proposals as $proposal): ?>
                    <tr>
                        <td class="proposal-title"><?php echo htmlspecialchars($proposal->title); ?></td>
                        <td>
                            <div class="sponsor-info"><?php echo htmlspecialchars($proposal->company_name); ?></div>
                            <div class="event-name"><?php echo htmlspecialchars($proposal->contact_person); ?></div>
                        </td>
                        <td class="event-name"><?php echo htmlspecialchars(substr($proposal->event_title, 0, 30)); ?></td>
                        <td>
                            <span class="proposal-type">
                                <?php echo getTypeIcon($proposal->proposal_type); ?>
                                <?php echo ucfirst(str_replace('-', ' ', $proposal->proposal_type)); ?>
                            </span>
                        </td>
                        <td><?php echo getStatusBadge($proposal->status); ?></td>
                        <td class="date-cell"><?php echo date('M d, Y', strtotime($proposal->created_at)); ?></td>
                        <td class="actions">
                            <a href="/unipulse/public/admin/sponsorshipproposals/view/<?php echo $proposal->id; ?>" class="btn-view">Review</a>
                            <?php if (in_array($proposal->status, ['submitted', 'under_review', 'negotiating'])): ?>
                                <button class="btn-accept" onclick="acceptProposal(<?php echo $proposal->id; ?>)">Accept</button>
                                <button class="btn-reject" onclick="openRejectModal(<?php echo $proposal->id; ?>)">Reject</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <h3>No Proposals Found</h3>
                <p>There are currently no <?php echo $status; ?> sponsorship proposals.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Reject Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Reject Proposal</div>
            <div class="modal-body">
                <p style="margin-bottom: 15px; color: #666;">Please provide a reason for rejecting this proposal:</p>
                <textarea id="rejectReason" placeholder="Reason for rejection..."></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeRejectModal()">Cancel</button>
                <button class="btn btn-primary" onclick="submitReject()">Reject Proposal</button>
            </div>
        </div>
    </div>
    
    <script>
        let currentProposalId = null;
        
        function openRejectModal(proposalId) {
            currentProposalId = proposalId;
            document.getElementById('rejectModal').style.display = 'block';
            document.getElementById('rejectReason').focus();
        }
        
        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
            document.getElementById('rejectReason').value = '';
            currentProposalId = null;
        }
        
        async function submitReject() {
            const reason = document.getElementById('rejectReason').value.trim();
            if (!reason) {
                alert('Please provide a rejection reason');
                return;
            }
            
            const formData = new FormData();
            formData.append('reason', reason);
            
            try {
                const response = await fetch('/unipulse/public/admin/sponsorshipproposals/reject/' + currentProposalId, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                if (result.success) {
                    alert('Proposal rejected successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
            
            closeRejectModal();
        }
        
        async function acceptProposal(proposalId) {
            if (confirm('Accept this sponsorship proposal?')) {
                try {
                    const response = await fetch('/unipulse/public/admin/sponsorshipproposals/accept/' + proposalId, {
                        method: 'POST'
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        alert('Proposal accepted successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (error) {
                    alert('Error: ' + error.message);
                }
            }
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('rejectModal');
            if (event.target === modal) {
                closeRejectModal();
            }
        }
    </script>
</body>
</html>
