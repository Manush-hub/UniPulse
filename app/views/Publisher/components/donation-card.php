<?php
$status = $donation['status'] ?? 'pending';
$statusClass = in_array($status, ['pending', 'completed']) ? $status : 'rejected';
$statusLabels = [
    'pending' => 'Pending',
    'completed' => 'Completed',
    'failed' => 'Not Received',
    'refunded' => 'Not Received'
];

$paymentId = $donation['payment_id'] ?? '';
$hasSlip = is_string($paymentId) && strpos($paymentId, '/uploads/donation_slips/') !== false;
?>

<div class="sponsorship-card" data-status="<?= htmlspecialchars($status) ?>" data-id="<?= (int)($donation['id'] ?? 0) ?>">
    <div class="card-header">
        <span class="status-badge status-<?= $statusClass ?>">
            <?= $statusLabels[$status] ?? ucfirst($status) ?>
        </span>
        <span class="package-type package-custom">
            Donation
        </span>
    </div>

    <div class="card-body">
        <h3 class="event-title"><?= htmlspecialchars($donation['event_title'] ?? 'Event') ?></h3>

        <div class="sponsor-info">
            <h4><i class="fas fa-user"></i> Donor Details</h4>
            <div class="info-item">
                <strong>Name:</strong> <?= htmlspecialchars($donation['donor_name'] ?: 'Anonymous Donor') ?>
            </div>
            <?php if (!empty($donation['donor_email'])): ?>
                <div class="info-item">
                    <strong>Email:</strong>
                    <a href="mailto:<?= htmlspecialchars($donation['donor_email']) ?>">
                        <?= htmlspecialchars($donation['donor_email']) ?>
                    </a>
                </div>
            <?php endif; ?>
            <?php if (!empty($donation['donor_phone'])): ?>
                <div class="info-item">
                    <strong>Phone:</strong> <?= htmlspecialchars($donation['donor_phone']) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="sponsorship-details">
            <div class="detail-item amount">
                <i class="fas fa-money-bill-wave"></i>
                <span><?= htmlspecialchars($donation['currency'] ?? 'LKR') ?> <?= number_format((float)($donation['amount'] ?? 0), 2) ?></span>
            </div>

            <div class="detail-item">
                <i class="fas fa-calendar"></i>
                <span>Event: <?= !empty($donation['event_date']) ? date('M d, Y', strtotime($donation['event_date'])) : 'N/A' ?></span>
            </div>

            <div class="detail-item">
                <i class="fas fa-clock"></i>
                <span>Donated: <?= !empty($donation['created_at']) ? date('M d, Y', strtotime($donation['created_at'])) : 'N/A' ?></span>
            </div>

            <?php if (!empty($donation['payment_method'])): ?>
                <div class="detail-item">
                    <i class="fas fa-credit-card"></i>
                    <span>Method: <?= htmlspecialchars(ucwords(str_replace('_', ' ', $donation['payment_method']))) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($donation['transaction_reference'])): ?>
                <div class="detail-item">
                    <i class="fas fa-hashtag"></i>
                    <span>Ref: <?= htmlspecialchars($donation['transaction_reference']) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($donation['payment_id']) && !$hasSlip): ?>
                <div class="detail-item">
                    <i class="fas fa-receipt"></i>
                    <span>Payment ID: <?= htmlspecialchars($donation['payment_id']) ?></span>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($hasSlip): ?>
            <div class="payment-proof-section">
                <a href="<?= htmlspecialchars($paymentId) ?>" target="_blank" class="btn btn-proof">
                    <i class="fas fa-file-invoice"></i> View Donation Slip
                </a>
            </div>
        <?php endif; ?>

        <?php if (!empty($donation['message'])): ?>
            <div class="notes-section">
                <h5><i class="fas fa-comment"></i> Donor Message:</h5>
                <p><?= nl2br(htmlspecialchars($donation['message'])) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <div class="card-footer">
        <?php if ($status === 'pending'): ?>
            <div class="action-buttons">
                <button class="btn btn-approve" onclick="acceptDonation(<?= (int)($donation['id'] ?? 0) ?>)">
                    <i class="fas fa-check"></i> Accept
                </button>
                <button class="btn btn-reject" onclick="rejectDonation(<?= (int)($donation['id'] ?? 0) ?>)">
                    <i class="fas fa-times"></i> Reject
                </button>
            </div>
        <?php elseif ($status === 'completed'): ?>
            <span class="status-text completed">
                <i class="fas fa-check-circle"></i> Donation completed
            </span>
        <?php elseif ($status === 'failed'): ?>
            <span class="status-text rejected">
                <i class="fas fa-times-circle"></i> Donation not received
            </span>
        <?php elseif ($status === 'refunded'): ?>
            <span class="status-text rejected">
                <i class="fas fa-undo"></i> Donation not received
            </span>
        <?php endif; ?>
    </div>
</div>