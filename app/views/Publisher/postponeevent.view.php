<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postpone Event - UniPulse</title>
    <!-- Include global css -->
    <link rel="stylesheet" href="/unipulse/public/assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-color, #f4f7f6); }
        .postpone-container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h2 { margin-bottom: 20px; color: var(--text-color, #333); }
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 5px; }
        .form-group input[type="date"],
        .form-group input[type="time"],
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn-warning {
            background-color: #eab308;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-warning:hover { background-color: #ca8a04; }
        .btn-disabled {
            background-color: #d1d5db;
            color: #6b7280;
            cursor: not-allowed;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
        }
        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .alert-error { background-color: #fee2e2; color: #b91c1c; border: 1px solid #f87171; }
        .current-details {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            border: 1px solid #e5e7eb;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #3b82f6;
            text-decoration: none;
        }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="postpone-container">
        <a href="/unipulse/public/publisher/dashboard" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        
        <h2>Postpone Event: <?php echo htmlspecialchars($event->title); ?></h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="current-details">
            <p><strong>Current Event Date:</strong> <?php echo htmlspecialchars($event->event_date); ?></p>
            <p><strong>Current Start Time:</strong> <?php echo htmlspecialchars($event->event_time); ?></p>
            <?php if (!empty($event->event_end_time)): ?>
                <p><strong>Current End Time:</strong> <?php echo htmlspecialchars($event->event_end_time); ?></p>
            <?php endif; ?>
            
            <?php if ($event->requires_registration): ?>
                <hr style="margin: 10px 0; border: none; border-top: 1px solid #ccc;">
                <p><strong>Current Registration/Ticket End Date:</strong> <?php echo htmlspecialchars($event->registration_end_date ?? 'Not Set'); ?></p>
                <p><strong>Current Registration/Ticket End Time:</strong> <?php echo htmlspecialchars($event->registration_end_time ?? 'Not Set'); ?></p>
            <?php endif; ?>
        </div>

        <?php if (!$action_disabled): ?>
            <form action="/unipulse/public/publisher/postponeevent/<?php echo $event->id; ?>" method="POST">
                <div class="form-group">
                    <label for="event_date">New Event Date *</label>
                    <input type="date" id="event_date" name="event_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="event_time">New Event Start Time *</label>
                    <input type="time" id="event_time" name="event_time" required>
                </div>

                <div class="form-group">
                    <label for="event_end_time">New Event End Time (Optional)</label>
                    <input type="time" id="event_end_time" name="event_end_time">
                </div>

                <hr style="margin: 25px 0; border: none; border-top: 1px solid #e5e7eb;">
                <h3 style="margin-bottom: 15px; font-size: 18px; color: var(--text-color, #333);">Registration & Tickets</h3>
                
                <div class="form-group">
                    <label for="registration_end_date">New Registration/Ticket End Date</label>
                    <input type="date" id="registration_end_date" name="registration_end_date" min="<?php echo date('Y-m-d'); ?>" <?php echo $event->requires_registration ? 'required' : ''; ?>>
                    <small style="color: #666; font-size: 12px;">Please set the new deadline for registration/tickets.</small>
                </div>
                
                <div class="form-group">
                    <label for="registration_end_time">New Registration/Ticket End Time</label>
                    <input type="time" id="registration_end_time" name="registration_end_time" <?php echo $event->requires_registration ? 'required' : ''; ?>>
                </div>

                <hr style="margin: 25px 0; border: none; border-top: 1px solid #e5e7eb;">

                <div class="form-group">
                    <label for="postpone_reason">Reason for Postponement (Optional)</label>
                    <textarea id="postpone_reason" name="postpone_reason" rows="3" placeholder="Briefly explain why the event is being postponed..."></textarea>
                </div>

                <button type="submit" class="btn-warning">Confirm Postponement</button>
            </form>
        <?php else: ?>
            <button class="btn-disabled" disabled>Postpone action is disabled</button>
        <?php endif; ?>
    </div>
</body>
</html>