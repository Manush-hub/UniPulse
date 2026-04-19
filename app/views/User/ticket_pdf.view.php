<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-Ticket - <?= htmlspecialchars((string)($ticket->order_number ?? '')) ?></title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/User/ticket-pdf-style.css">
</head>
<body>

    <button onclick="window.print()" class="print-btn">🖨️ Download / Print Ticket</button>

    <div class="ticket-container">
        <div class="header">
            <h1>UniPulse e-Ticket</h1>
            <p>Order Number: <strong><?= htmlspecialchars((string)($ticket->order_number ?? '')) ?></strong></p>
            <p>Purchased by: <?= htmlspecialchars((string)($ticket->registered_user_name_snapshot ?? '')) ?> (<?= htmlspecialchars((string)($ticket->registered_user_email_snapshot ?? '')) ?>)</p>
        </div>

        <div class="event-details">
            <h3>Event Information</h3>
            <div class="event-details-grid">
                <div class="detail-item">
                    <strong>Event Name</strong>
                    <span><?= htmlspecialchars((string)($ticket->event_title ?? '')) ?></span>
                </div>
                <div class="detail-item">
                    <strong>Date & Time</strong>
                    <span><?= htmlspecialchars((string)($ticket->event_date ?? '')) ?> at <?= htmlspecialchars((string)($ticket->event_time ?? '')) ?></span>
                </div>
                <div class="detail-item">
                    <strong>Location</strong>
                    <span>
                        <?php
                            if (($ticket->location_type ?? '') === 'online') {
                                echo "Online Event";
                            } else {
                                $locParts = [];
                                if (!empty($ticket->location)) $locParts[] = $ticket->location;
                                if (!empty($ticket->venue_name)) $locParts[] = $ticket->venue_name;
                                if (!empty($ticket->street_address)) $locParts[] = $ticket->street_address;
                                if (!empty($ticket->city)) $locParts[] = $ticket->city;
                                
                                echo !empty($locParts) ? htmlspecialchars(implode(', ', $locParts)) : "Location TBA";
                            }
                        ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="ticket-boxes">
            <h3>Your Tickets</h3>
            <?php 
                $metadata = json_decode($ticket->metadata ?? '{}', true) ?? [];
                if (!empty($metadata['ticket_breakdown'])):
                    foreach ($metadata['ticket_breakdown'] as $t): 
                        $qty = (int)($t['quantity'] ?? 1);
                        $unitPrice = $t['price'] ?? 0;
                        if (!isset($t['price']) && isset($t['subtotal']) && $qty > 0) {
                            $unitPrice = $t['subtotal'] / $qty;
                        }
                        for ($i = 1; $i <= $qty; $i++):
            ?>
                <div class="ticket-box">
                    <div class="info">
                        <h4><?= htmlspecialchars((string)($t['name'] ?? '')) ?></h4>
                        <p>Ticket <?= $i ?> of <?= $qty ?></p>
                    </div>
                    <div class="price">
                        <?= htmlspecialchars((string)($ticket->currency_code ?? 'LKR')) ?> <?= number_format($unitPrice, 2) ?>
                    </div>
                </div>
            <?php 
                        endfor;
                    endforeach;
                else: 
                    $qty = (int)($ticket->ticket_quantity ?? 1);
                    $unitPrice = (float)($ticket->subtotal_amount ?? 0) / ($qty > 0 ? $qty : 1);
                    for ($i = 1; $i <= $qty; $i++):
            ?>
                <div class="ticket-box">
                    <div class="info">
                        <h4><?= htmlspecialchars((string)($ticket->ticket_tier_name ?? 'General Admission')) ?></h4>
                        <p>Ticket <?= $i ?> of <?= $qty ?></p>
                    </div>
                    <div class="price">
                        <?= htmlspecialchars((string)($ticket->currency_code ?? 'LKR')) ?> <?= number_format($unitPrice, 2) ?>
                    </div>
                </div>
            <?php 
                    endfor;
                endif; 
            ?>
        </div>

        <div class="payment-details">
            <h3>Payment Summary</h3>
            <div class="payment-row">
                <span>Transaction ID:</span>
                <span><?= htmlspecialchars((string)($ticket->payment_transaction_id ?? 'N/A')) ?></span>
            </div>
            <div class="payment-row">
                <span>Payment Method:</span>
                <span style="text-transform: uppercase;"><?= htmlspecialchars((string)($ticket->payment_gateway ?? $ticket->payment_method ?? 'Online')) ?></span>
            </div>
            <div class="payment-row">
                <span>Payment Status:</span>
                <strong style="color: #27ae60; text-transform: uppercase;"><?= htmlspecialchars((string)($ticket->payment_status ?? 'Unknown')) ?></strong>
            </div>

            <div class="payment-row total">
                <span>Total Amount Paid:</span>
                <span><?= htmlspecialchars((string)($ticket->currency_code ?? 'LKR')) ?> <?= number_format((float)($ticket->total_amount ?? 0), 2) ?></span>
            </div>
        </div>

        <div class="footer">
            <p>Please present this document (digital or printed) at the entrance.</p>
            <p>&copy; <?= date('Y') ?> UniPulse Events. All rights reserved.</p>
        </div>
    </div>

    <script src="/unipulse/public/assets/js/User/ticket-pdf-app.js"></script>
</body>
</html>