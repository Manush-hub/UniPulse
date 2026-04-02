<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-Ticket - <?= htmlspecialchars((string)($ticket->order_number ?? '')) ?></title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .ticket-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #ddd;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0;
            color: #7f8c8d;
            font-size: 16px;
        }
        .event-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
        }
        .event-details h3 {
            margin-top: 0;
            color: #34495e;
            font-size: 18px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .event-details-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .detail-item {
            flex: 1;
            min-width: 200px;
        }
        .detail-item strong {
            display: block;
            font-size: 12px;
            color: #95a5a6;
            text-transform: uppercase;
        }
        .detail-item span {
            font-size: 16px;
            color: #2c3e50;
            font-weight: 500;
        }
        .ticket-boxes {
            margin-bottom: 25px;
        }
        .ticket-box {
            border: 1px solid #e0e0e0;
            border-left: 5px solid #3498db;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
        }
        .ticket-box .info h4 {
            margin: 0 0 5px 0;
            color: #2980b9;
            font-size: 18px;
        }
        .ticket-box .info p {
            margin: 0;
            color: #7f8c8d;
            font-size: 14px;
        }
        .ticket-box .price {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }
        .payment-details {
            border-top: 2px solid #eee;
            padding-top: 20px;
        }
        .payment-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 15px;
        }
        .payment-row.total {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #bdc3c7;
            font-size: 14px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        
        .print-btn {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 12px 20px;
            background-color: #3498db;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }
        .print-btn:hover {
            background-color: #2980b9;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .ticket-container {
                box-shadow: none;
                max-width: 100%;
                padding: 10px;
            }
            .print-btn {
                display: none !important;
            }
        }
    </style>
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

    <script>
        // Auto-trigger print prompt when loaded so it acts as a download
        window.onload = function() { 
            setTimeout(() => { window.print(); }, 500); 
        }
    </script>
</body>
</html>