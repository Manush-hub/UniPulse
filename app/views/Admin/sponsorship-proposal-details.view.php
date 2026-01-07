<?php
// Admin Sponsorship Proposal Details View
$user = $data['user'] ?? [];
$proposal = $data['proposal'] ?? null;

function formatProposalType($type) {
    return [
        'monetary' => 'Monetary Sponsorship',
        'in-kind' => 'In-Kind Sponsorship (Products/Services)',
        'service' => 'Service Sponsorship',
        'mixed' => 'Mixed Sponsorship'
    ][$type] ?? $type;
}

function getStatusColor($status) {
    $colors = [
        'submitted' => '#3498db',
        'under_review' => '#f39c12',
        'negotiating' => '#9b59b6',
        'accepted' => '#27ae60',
        'rejected' => '#e74c3c',
        'draft' => '#95a5a6'
    ];
    return $colors[$status] ?? '#95a5a6';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Sponsorship Proposal - Admin</title>
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
            max-width: 1200px;
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
        .proposal-header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-left: 6px solid <?php echo getStatusColor($proposal->status); ?>;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
        }
        .proposal-title-section h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .proposal-meta {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 15px;
        }
        .meta-item {
            padding: 10px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .meta-label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .meta-value {
            font-size: 14px;
            color: #333;
            font-weight: 600;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            color: white;
            font-weight: 600;
            font-size: 13px;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .grid-1 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }
        .section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .section h2 {
            color: #667eea;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
            font-size: 18px;
        }
        .section h3 {
            color: #333;
            margin-top: 15px;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .info-group {
            margin-bottom: 15px;
        }
        .info-label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 14px;
            color: #333;
            line-height: 1.6;
        }
        .contact-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }
        .contact-info p {
            margin: 8px 0;
            font-size: 13px;
        }
        .list-items {
            list-style: none;
            padding: 0;
        }
        .list-items li {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 13px;
            color: #666;
        }
        .list-items li:before {
            content: "✓ ";
            color: #27ae60;
            font-weight: bold;
            margin-right: 8px;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
        }
        .btn-accept {
            background: #27ae60;
            color: white;
        }
        .btn-accept:hover {
            background: #229954;
        }
        .btn-reject {
            background: #e74c3c;
            color: white;
        }
        .btn-reject:hover {
            background: #c0392b;
        }
        .btn-review {
            background: #f39c12;
            color: white;
        }
        .btn-review:hover {
            background: #e67e22;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        }
        .modal-header {
            font-size: 22px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }
        .modal-body {
            margin-bottom: 20px;
        }
        .modal-body textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
            min-height: 120px;
        }
        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .btn-secondary {
            background: #ddd;
            color: #333;
        }
        .btn-secondary:hover {
            background: #ccc;
        }
        .rejection-note {
            background: #fee;
            border: 1px solid #fcc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #c00;
        }
        .negotiation-note {
            background: #f0f4f8;
            border: 1px solid #dde5f0;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/unipulse/public/admin/sponsorshipproposals" class="back-btn">← Back to Proposals</a>
        
        <?php if ($proposal): ?>
        
        <!-- Header Section -->
        <div class="proposal-header">
            <div class="header-top">
                <div class="proposal-title-section">
                    <h1><?php echo htmlspecialchars($proposal->title); ?></h1>
                    <p style="color: #999; margin-top: 5px;">For: <?php echo htmlspecialchars($proposal->event_title); ?></p>
                </div>
                <div>
                    <span class="status-badge" style="background: <?php echo getStatusColor($proposal->status); ?>;">
                        <?php echo ucfirst(str_replace('_', ' ', $proposal->status)); ?>
                    </span>
                </div>
            </div>
            
            <div class="proposal-meta">
                <div class="meta-item">
                    <div class="meta-label">Sponsor</div>
                    <div class="meta-value"><?php echo htmlspecialchars($proposal->company_name); ?></div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Type</div>
                    <div class="meta-value"><?php echo formatProposalType($proposal->proposal_type); ?></div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Submitted</div>
                    <div class="meta-value"><?php echo date('M d, Y @ h:i A', strtotime($proposal->created_at)); ?></div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Views</div>
                    <div class="meta-value"><?php echo $proposal->views_count; ?></div>
                </div>
            </div>
        </div>
        
        <!-- Rejection or Negotiation Note -->
        <?php if ($proposal->status === 'rejected' && $proposal->rejection_reason): ?>
        <div class="rejection-note">
            <strong>❌ Rejection Reason:</strong><br>
            <?php echo htmlspecialchars($proposal->rejection_reason); ?>
        </div>
        <?php elseif ($proposal->status === 'negotiating' && $proposal->rejection_reason): ?>
        <div class="negotiation-note">
            <strong>💬 Feedback for Sponsor:</strong><br>
            <?php echo htmlspecialchars($proposal->rejection_reason); ?>
        </div>
        <?php endif; ?>
        
        <!-- Proposal Details -->
        <div class="grid-2">
            <!-- Left Column -->
            <div class="grid-1">
                <!-- Basic Information -->
                <div class="section">
                    <h2>Proposal Details</h2>
                    
                    <div class="info-group">
                        <div class="info-label">Description</div>
                        <div class="info-value"><?php echo nl2br(htmlspecialchars($proposal->description)); ?></div>
                    </div>
                    
                    <?php if ($proposal->proposal_type === 'monetary' || $proposal->proposal_type === 'mixed'): ?>
                    <?php if ($proposal->monetary_amount): ?>
                    <div class="info-group">
                        <div class="info-label">Monetary Amount</div>
                        <div class="info-value">
                            <?php echo htmlspecialchars($proposal->currency); ?> 
                            <?php echo number_format($proposal->monetary_amount, 2); ?>
                        </div>
                    </div>
                    <?php if ($proposal->payment_schedule): ?>
                    <div class="info-group">
                        <div class="info-label">Payment Schedule</div>
                        <div class="info-value"><?php echo htmlspecialchars($proposal->payment_schedule); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if ($proposal->proposal_type === 'in-kind' || $proposal->proposal_type === 'mixed'): ?>
                    <?php if (!empty($proposal->in_kind_items)): ?>
                    <div class="info-group">
                        <div class="info-label">In-Kind Items/Services</div>
                        <ul class="list-items">
                            <?php foreach ($proposal->in_kind_items as $item): ?>
                            <li><?php echo htmlspecialchars($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php if ($proposal->estimated_value): ?>
                    <div class="info-group">
                        <div class="info-label">Estimated Value</div>
                        <div class="info-value">$<?php echo number_format($proposal->estimated_value, 2); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if ($proposal->proposal_type === 'service' || $proposal->proposal_type === 'mixed'): ?>
                    <?php if ($proposal->service_description): ?>
                    <div class="info-group">
                        <div class="info-label">Service Description</div>
                        <div class="info-value"><?php echo nl2br(htmlspecialchars($proposal->service_description)); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ($proposal->service_duration): ?>
                    <div class="info-group">
                        <div class="info-label">Service Duration</div>
                        <div class="info-value"><?php echo htmlspecialchars($proposal->service_duration); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Right Column -->
            <div class="grid-1">
                <!-- Contact Information -->
                <div class="section">
                    <h2>Contact Information</h2>
                    <div class="contact-info">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($proposal->contact_person); ?></p>
                        <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($proposal->contact_email); ?>"><?php echo htmlspecialchars($proposal->contact_email); ?></a></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($proposal->contact_phone); ?></p>
                    </div>
                </div>
                
                <!-- Deliverables -->
                <?php if (!empty($proposal->deliverables)): ?>
                <div class="section">
                    <h2>What They'll Deliver</h2>
                    <ul class="list-items">
                        <?php foreach ($proposal->deliverables as $deliverable): ?>
                        <li><?php echo htmlspecialchars($deliverable); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <!-- Expected Benefits -->
                <?php if (!empty($proposal->expected_benefits)): ?>
                <div class="section">
                    <h2>What They Expect</h2>
                    <ul class="list-items">
                        <?php foreach ($proposal->expected_benefits as $benefit): ?>
                        <li><?php echo htmlspecialchars($benefit); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <?php if (in_array($proposal->status, ['submitted', 'under_review', 'negotiating'])): ?>
        <div class="section">
            <div class="action-buttons">
                <button class="btn btn-accept" onclick="acceptProposal(<?php echo $proposal->id; ?>)">
                    ✓ Accept Proposal
                </button>
                <button class="btn btn-review" onclick="openFeedbackModal()">
                    💬 Request Changes
                </button>
                <button class="btn btn-reject" onclick="openRejectModal()">
                    ✗ Reject Proposal
                </button>
            </div>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="section" style="text-align: center; padding: 60px 20px;">
            <h2>Proposal Not Found</h2>
            <p style="margin-top: 10px; color: #999;">The proposal you're looking for could not be found.</p>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Reject Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Reject Proposal</div>
            <div class="modal-body">
                <p style="margin-bottom: 15px; color: #666;">Please provide a detailed reason for rejecting this proposal:</p>
                <textarea id="rejectReason" placeholder="Reason for rejection..."></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeRejectModal()">Cancel</button>
                <button class="btn btn-reject" onclick="submitReject()">Reject Proposal</button>
            </div>
        </div>
    </div>
    
    <!-- Feedback Modal -->
    <div id="feedbackModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Request Changes</div>
            <div class="modal-body">
                <p style="margin-bottom: 15px; color: #666;">Provide feedback for the sponsor on how they can improve their proposal:</p>
                <textarea id="feedbackText" placeholder="Feedback for sponsor..."></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeFeedbackModal()">Cancel</button>
                <button class="btn btn-review" onclick="submitFeedback()">Send Feedback</button>
            </div>
        </div>
    </div>
    
    <script>
        const proposalId = <?php echo $proposal->id ?? 0; ?>;
        
        function openRejectModal() {
            document.getElementById('rejectModal').style.display = 'block';
            document.getElementById('rejectReason').focus();
        }
        
        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
            document.getElementById('rejectReason').value = '';
        }
        
        function openFeedbackModal() {
            document.getElementById('feedbackModal').style.display = 'block';
            document.getElementById('feedbackText').focus();
        }
        
        function closeFeedbackModal() {
            document.getElementById('feedbackModal').style.display = 'none';
            document.getElementById('feedbackText').value = '';
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
                const response = await fetch('/unipulse/public/admin/sponsorshipproposals/reject/' + proposalId, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                if (result.success) {
                    alert('Proposal rejected and sponsor notified!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
            
            closeRejectModal();
        }
        
        async function submitFeedback() {
            const feedback = document.getElementById('feedbackText').value.trim();
            if (!feedback) {
                alert('Please provide feedback');
                return;
            }
            
            const formData = new FormData();
            formData.append('feedback', feedback);
            
            try {
                const response = await fetch('/unipulse/public/admin/sponsorshipproposals/requestChanges/' + proposalId, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                if (result.success) {
                    alert('Feedback sent to sponsor!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
            
            closeFeedbackModal();
        }
        
        async function acceptProposal(id) {
            if (confirm('Accept this sponsorship proposal?')) {
                try {
                    const response = await fetch('/unipulse/public/admin/sponsorshipproposals/accept/' + id, {
                        method: 'POST'
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        alert('Proposal accepted and sponsor notified!');
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
            const rejectModal = document.getElementById('rejectModal');
            const feedbackModal = document.getElementById('feedbackModal');
            
            if (event.target === rejectModal) {
                closeRejectModal();
            }
            if (event.target === feedbackModal) {
                closeFeedbackModal();
            }
        }
    </script>
</body>
</html>
