<div class="sponsorship-card" data-status="<?= $sponsorship['status'] ?>" data-id="<?= $sponsorship['id'] ?>">
    <div class="card-header">
        <span class="status-badge status-<?= $sponsorship['status'] ?>">
            <?php 
                $statusLabels = [
                    'pending' => 'Pending',
                    'rejected' => 'Not Delivered',
                    'completed' => 'Completed'
                ];
                echo $statusLabels[$sponsorship['status']] ?? ucfirst($sponsorship['status']);
            ?>
        </span>
        <span class="package-type package-<?= $sponsorship['package_type'] ?>">
            <?= ucfirst($sponsorship['package_type']) ?>
        </span>
    </div>
    
    <div class="card-body">
        <h3 class="event-title"><?= htmlspecialchars($sponsorship['event_title']) ?></h3>
        
        <div class="organizer-info">
            <h4><i class="fas fa-building"></i> Event Organizer</h4>
            <div class="info-item">
                <strong>Organization:</strong> <?= htmlspecialchars($sponsorship['organizer_name']) ?>
            </div>
            <div class="info-item">
                <strong>Email:</strong> 
                <a href="mailto:<?= htmlspecialchars($sponsorship['organizer_email']) ?>">
                    <?= htmlspecialchars($sponsorship['organizer_email']) ?>
                </a>
            </div>
        </div>
        
        <div class="sponsorship-details">
            <div class="detail-item">
                <i class="fas fa-box"></i>
                <span><?= htmlspecialchars($sponsorship['package_name']) ?> Package</span>
            </div>
            
            <div class="detail-item amount">
                <i class="fas fa-money-bill-wave"></i>
                <span>LKR <?= number_format($sponsorship['amount'], 2) ?></span>
            </div>
            
            <div class="detail-item">
                <i class="fas fa-calendar"></i>
                <span>Event: <?= date('M d, Y', strtotime($sponsorship['event_date'])) ?></span>
            </div>
            
            <div class="detail-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>
                    <?php if ($sponsorship['university_name']): ?>
                        <?= htmlspecialchars($sponsorship['university_name']) ?>
                    <?php else: ?>
                        <?= htmlspecialchars($sponsorship['venue_name'] ?? $sponsorship['city'] ?? 'Location TBA') ?>
                    <?php endif; ?>
                </span>
            </div>
            
            <div class="detail-item">
                <i class="fas fa-clock"></i>
                <span>Submitted: <?= date('M d, Y', strtotime($sponsorship['created_at'])) ?></span>
            </div>
            
            <?php if ($sponsorship['payment_reference']): ?>
                <div class="detail-item">
                    <i class="fas fa-hashtag"></i>
                    <span>Ref: <?= htmlspecialchars($sponsorship['payment_reference']) ?></span>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($sponsorship['payment_proof']): ?>
            <div class="payment-proof-section">
                <a href="<?= $sponsorship['payment_proof'] ?>" target="_blank" class="btn btn-proof">
                    <i class="fas fa-file-invoice"></i> View Payment Receipt
                </a>
            </div>
        <?php endif; ?>
        
        <?php if ($sponsorship['notes']): ?>
            <div class="notes-section">
                <h5><i class="fas fa-sticky-note"></i> Notes:</h5>
                <p><?= nl2br(htmlspecialchars($sponsorship['notes'])) ?></p>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="card-footer">
        <?php if ($sponsorship['status'] === 'pending'): ?>
            <span class="status-text">
                <i class="fas fa-hourglass-half"></i> Awaiting confirmation
            </span>
        <?php elseif ($sponsorship['status'] === 'completed'): ?>
            <span class="status-text completed">
                <i class="fas fa-check-double"></i> Sponsorship Completed
            </span>
        <?php elseif ($sponsorship['status'] === 'rejected'): ?>
            <span class="status-text rejected">
                <i class="fas fa-times-circle"></i> Sponsorship Not Delivered
            </span>
        <?php endif; ?>
    </div>
</div>
