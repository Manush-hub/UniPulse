<?php
$rows = $tableDonations ?? [];
?>

<div class="donation-table-wrap">
    <table class="donation-table">
        <thead>
            <tr>
                <th>Event Name</th>
                <th>Donor Details</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Donated</th>
                <th>View Slip</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $donation): ?>
                <?php
                $status = $donation['status'] ?? 'pending';
                if ($status === 'completed') {
                    $status = 'accepted';
                } elseif ($status === 'failed' || $status === 'refunded') {
                    $status = 'rejected';
                }
                $paymentId = $donation['payment_id'] ?? '';
                $hasSlip = is_string($paymentId) && strpos($paymentId, '/uploads/donation_slips/') !== false;
                ?>
                <tr>
                    <td><?= htmlspecialchars($donation['event_title'] ?? 'Event') ?></td>
                    <td>
                        <div><strong><?= htmlspecialchars($donation['donor_name'] ?: 'Anonymous Donor') ?></strong></div>
                        <?php if (!empty($donation['donor_email'])): ?>
                            <div><?= htmlspecialchars($donation['donor_email']) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($donation['donor_phone'])): ?>
                            <div><?= htmlspecialchars($donation['donor_phone']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($donation['currency'] ?? 'LKR') ?> <?= number_format((float)($donation['amount'] ?? 0), 2) ?></td>
                    <td><?= !empty($donation['payment_method']) ? htmlspecialchars(ucwords(str_replace('_', ' ', $donation['payment_method']))) : 'N/A' ?></td>
                    <td><?= !empty($donation['created_at']) ? date('M d, Y', strtotime($donation['created_at'])) : 'N/A' ?></td>
                    <td>
                        <?php if ($hasSlip): ?>
                            <a href="<?= htmlspecialchars($paymentId) ?>" target="_blank" class="btn btn-proof">
                                <i class="fas fa-file-invoice"></i> View Slip
                            </a>
                        <?php else: ?>
                            <span style="color:#9ca3af;">N/A</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($status === 'pending'): ?>
                            <div class="action-buttons">
                                <button class="btn btn-approve" onclick="acceptDonation(<?= (int)($donation['id'] ?? 0) ?>)">
                                    <i class="fas fa-check"></i> Accept
                                </button>
                                <button class="btn btn-reject" onclick="rejectDonation(<?= (int)($donation['id'] ?? 0) ?>)">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </div>
                        <?php elseif ($status === 'accepted'): ?>
                            <span class="status-text completed"><i class="fas fa-check-circle"></i> Accepted</span>
                        <?php else: ?>
                            <span class="status-text rejected"><i class="fas fa-times-circle"></i> Rejected</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>