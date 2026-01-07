<?php
// My Sponsorship Proposals View
$user = $data['user'] ?? [];
$proposals = $data['proposals'] ?? [];
$status = $data['status'] ?? null;
$page_title = $data['page_title'] ?? 'My Sponsorship Proposals';

function getStatusBadge($status) {
    $colors = [
        'draft' => '#95a5a6',
        'submitted' => '#3498db',
        'under_review' => '#f39c12',
        'accepted' => '#27ae60',
        'rejected' => '#e74c3c',
        'negotiating' => '#9b59b6'
    ];
    $text = ucfirst(str_replace('_', ' ', $status));
    $color = $colors[$status] ?? '#95a5a6';
    return '<span style="background: '.$color.'; color: white; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">'.$text.'</span>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .btn-new {
            background: #667eea;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        .btn-new:hover {
            background: #764ba2;
        }
        .filters {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 15px;
        }
        .filter-btn {
            padding: 8px 15px;
            border: 2px solid #ddd;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        .filter-btn.active {
            border-color: #667eea;
            color: #667eea;
            font-weight: 600;
        }
        .filter-btn:hover {
            border-color: #667eea;
        }
        .proposals-grid {
            display: grid;
            gap: 20px;
        }
        .proposal-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .proposal-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }
        .proposal-header {
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: start;
        }
        .proposal-title {
            flex: 1;
        }
        .proposal-title h3 {
            margin: 0 0 8px 0;
            color: #333;
            font-size: 18px;
        }
        .proposal-event {
            color: #666;
            font-size: 14px;
            margin: 0;
        }
        .proposal-body {
            padding: 20px;
        }
        .proposal-description {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .proposal-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            font-size: 13px;
            color: #999;
            padding: 15px 0;
            border-top: 1px solid #f0f0f0;
        }
        .proposal-meta div {
            display: flex;
            justify-content: space-between;
        }
        .proposal-footer {
            padding: 15px 20px;
            background: #f9f9f9;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-view {
            background: #667eea;
            color: white;
        }
        .btn-view:hover {
            background: #764ba2;
        }
        .btn-edit {
            background: #f39c12;
            color: white;
        }
        .btn-edit:hover {
            background: #e67e22;
        }
        .btn-delete {
            background: #e74c3c;
            color: white;
        }
        .btn-delete:hover {
            background: #c0392b;
        }
        .btn-submit {
            background: #27ae60;
            color: white;
        }
        .btn-submit:hover {
            background: #229954;
        }
        .empty-state {
            background: white;
            padding: 60px 20px;
            border-radius: 10px;
            text-align: center;
            color: #999;
        }
        .empty-state h3 {
            color: #666;
            margin-bottom: 10px;
        }
        .rejection-reason {
            background: #fee;
            border: 1px solid #fcc;
            padding: 12px;
            border-radius: 5px;
            margin-top: 10px;
            color: #c00;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?php echo htmlspecialchars($page_title); ?></h1>
        </div>
        
        <div class="filters">
            <a href="/unipulse/public/sponsor/events/myProposals" class="filter-btn <?php echo !$status ? 'active' : ''; ?>">All Proposals</a>
            <a href="/unipulse/public/sponsor/events/myProposals?status=draft" class="filter-btn <?php echo $status === 'draft' ? 'active' : ''; ?>">Drafts</a>
            <a href="/unipulse/public/sponsor/events/myProposals?status=submitted" class="filter-btn <?php echo $status === 'submitted' ? 'active' : ''; ?>">Submitted</a>
            <a href="/unipulse/public/sponsor/events/myProposals?status=under_review" class="filter-btn <?php echo $status === 'under_review' ? 'active' : ''; ?>">Under Review</a>
            <a href="/unipulse/public/sponsor/events/myProposals?status=accepted" class="filter-btn <?php echo $status === 'accepted' ? 'active' : ''; ?>">Accepted</a>
            <a href="/unipulse/public/sponsor/events/myProposals?status=rejected" class="filter-btn <?php echo $status === 'rejected' ? 'active' : ''; ?>">Rejected</a>
        </div>
        
        <div class="proposals-grid">
            <?php if (!empty($proposals)): ?>
                <?php foreach ($proposals as $proposal): ?>
                <div class="proposal-card">
                    <div class="proposal-header">
                        <div class="proposal-title">
                            <h3><?php echo htmlspecialchars($proposal->title); ?></h3>
                            <p class="proposal-event"><?php echo htmlspecialchars($proposal->event_title); ?></p>
                        </div>
                        <div>
                            <?php echo getStatusBadge($proposal->status); ?>
                        </div>
                    </div>
                    
                    <div class="proposal-body">
                        <p class="proposal-description"><?php echo htmlspecialchars(substr($proposal->description, 0, 150)); ?>...</p>
                        
                        <div class="proposal-meta">
                            <div>
                                <span>Type:</span>
                                <strong><?php echo ucfirst(str_replace('-', ' ', $proposal->proposal_type)); ?></strong>
                            </div>
                            <div>
                                <span>Contact:</span>
                                <strong><?php echo htmlspecialchars($proposal->contact_person); ?></strong>
                            </div>
                            <div>
                                <span>Created:</span>
                                <strong><?php echo date('M d, Y', strtotime($proposal->created_at)); ?></strong>
                            </div>
                            <div>
                                <span>Views:</span>
                                <strong><?php echo htmlspecialchars($proposal->views_count); ?></strong>
                            </div>
                        </div>
                        
                        <?php if ($proposal->status === 'rejected' && $proposal->rejection_reason): ?>
                        <div class="rejection-reason">
                            <strong>Rejection Reason:</strong><br>
                            <?php echo htmlspecialchars($proposal->rejection_reason); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="proposal-footer">
                        <a href="/unipulse/public/sponsor/events/viewProposal/<?php echo $proposal->id; ?>" class="btn btn-view">View Details</a>
                        
                        <?php if ($proposal->status === 'draft'): ?>
                            <a href="/unipulse/public/sponsor/events/editProposal/<?php echo $proposal->id; ?>" class="btn btn-edit">Edit</a>
                            <button class="btn btn-delete" onclick="deleteProposal(<?php echo $proposal->id; ?>)">Delete</button>
                            <button class="btn btn-submit" onclick="submitProposal(<?php echo $proposal->id; ?>)">Submit for Review</button>
                        <?php elseif ($proposal->status === 'rejected'): ?>
                            <a href="/unipulse/public/sponsor/events/editProposal/<?php echo $proposal->id; ?>" class="btn btn-edit">Revise & Resubmit</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
            <div class="empty-state">
                <h3>No Proposals Found</h3>
                <p><?php echo $status ? 'You have no ' . $status . ' proposals.' : 'You haven\'t created any sponsorship proposals yet.'; ?></p>
                <a href="/unipulse/public/sponsor/events?view=sponsor" style="color: #667eea; text-decoration: none; font-weight: 600;">Browse Events</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        async function submitProposal(proposalId) {
            if (confirm('Submit this proposal for review?')) {
                try {
                    const response = await fetch('/unipulse/public/sponsor/events/submitProposalForReview/' + proposalId, {
                        method: 'POST'
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Proposal submitted successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (error) {
                    alert('Error: ' + error.message);
                }
            }
        }
        
        async function deleteProposal(proposalId) {
            if (confirm('Delete this proposal? This cannot be undone.')) {
                try {
                    const response = await fetch('/unipulse/public/sponsor/events/deleteProposal/' + proposalId, {
                        method: 'POST'
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Proposal deleted successfully');
                        location.reload();
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (error) {
                    alert('Error: ' + error.message);
                }
            }
        }
    </script>
</body>
</html>
