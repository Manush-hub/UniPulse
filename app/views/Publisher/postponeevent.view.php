<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postpone Event - UniPulse</title>
    <!-- Include global css -->
    <link rel="stylesheet" href="/unipulse/public/assets/css/global.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-hover: #2563eb;
            --warning-color: #eab308;
            --warning-hover: #ca8a04;
            --bg-color: #f4f7f6;
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --input-focus: rgba(59, 130, 246, 0.2);
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-color); color: var(--text-main); }
        .postpone-container {
            max-width: 750px;
            margin: 60px auto;
            background: var(--card-bg);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        h2 { margin-bottom: 25px; color: var(--text-main); font-weight: 700; font-size: 26px; }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 25px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .back-link:hover { color: var(--primary-color); }
        
        .current-details {
            background-color: #f8fafc;
            border-left: 4px solid var(--primary-color);
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 35px;
            color: var(--text-main);
        }
        .current-details p { margin: 8px 0; font-size: 15px; }
        .current-details strong { color: #334155; }
        
        .form-section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-main);
            margin: 30px 0 15px;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 10px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: end;
        }

        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { 
            display: block; 
            font-weight: 600; 
            margin-bottom: 8px; 
            font-size: 14px;
            color: #4b5563;
        }
        .form-group input[type="date"],
        .form-group input[type="time"],
        .form-group textarea,
        .form-group input[type="file"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: #fff;
            box-sizing: border-box;
        }
        .form-group input[type="date"],
        .form-group input[type="time"],
        .form-group input[type="file"] {
            height: 48px;
        }
        .form-group input[type="file"] {
            padding: 8px 15px;
            color: var(--text-muted);
        }
        .form-group input[type="file"]::file-selector-button {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 6px 12px;
            cursor: pointer;
            margin-right: 15px;
            font-weight: 500;
            transition: background-color 0.2s;
            color: #374151;
            height: 30px;
        }
        .form-group input[type="file"]::file-selector-button:hover { background-color: #e5e7eb; }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px var(--input-focus);
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            color: #fff;
            padding: 16px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: background-color 0.2s, transform 0.1s;
            margin-top: 10px;
        }
        .btn-warning:hover { background-color: var(--warning-hover); transform: translateY(-1px); }
        .btn-warning:active { transform: translateY(0); }

        .btn-disabled {
            background-color: #d1d5db;
            color: #6b7280;
            cursor: not-allowed;
            padding: 16px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
        }

        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
        .alert-error { background-color: #fee2e2; color: #b91c1c; border: 1px solid #f87171; }
        
        @media (max-width: 600px) {
            .form-row { grid-template-columns: 1fr; gap: 0; }
            .postpone-container { padding: 25px; margin: 30px auto; }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/components/header.php'; ?>

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
            <form action="/unipulse/public/publisher/postponeevent/<?php echo $event->id; ?>" method="POST" enctype="multipart/form-data">
                <h3 class="form-section-title">New Event Details</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="event_date">New Event Date <span style="color: red;">*</span></label>
                        <input type="date" id="event_date" name="event_date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="event_time">New Event Start Time <span style="color: red;">*</span></label>
                        <input type="time" id="event_time" name="event_time" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="event_end_time">New Event End Time <span style="color: red;">*</span></label>
                        <input type="time" id="event_end_time" name="event_end_time" required>
                    </div>

                    <div class="form-group">
                        <label for="cover_photo">Update Cover Photo (Optional)</label>
                        <input type="file" id="cover_photo" name="cover_photo" accept="image/jpeg, image/png, image/gif, image/webp">
                    </div>
                </div>

                <h3 class="form-section-title">Registration & Tickets</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="registration_end_date">New Registration/Ticket End Date (Optional)</label>
                        <input type="date" id="registration_end_date" name="registration_end_date" min="<?php echo date('Y-m-d'); ?>" <?php echo $event->requires_registration ? 'required' : ''; ?>>
                    </div>
                    
                    <div class="form-group">
                        <label for="registration_end_time">New Registration/Ticket End Time (Optional)</label>
                        <input type="time" id="registration_end_time" name="registration_end_time" <?php echo $event->requires_registration ? 'required' : ''; ?>>
                    </div>
                </div>
                <small style="color: #6b7280; font-size: 13px; display: block; margin-top: -15px; margin-bottom: 25px;">Please set the new deadline for registration/tickets if applicable.</small>

                <h3 class="form-section-title">Additional Information</h3>

                <div class="form-group">
                    <label for="postpone_reason">Reason for Postponement (Optional)</label>
                    <textarea id="postpone_reason" name="postpone_reason" rows="3" placeholder="Briefly explain why the event is being postponed..."></textarea>
                </div>

                <button type="submit" class="btn-warning"><i class="fas fa-calendar-check" style="margin-right: 8px;"></i> Confirm Postponement</button>
            </form>
        <?php else: ?>
            <button class="btn-disabled" disabled><i class="fas fa-ban" style="margin-right: 8px;"></i> Postpone action is disabled</button>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>

</body>
</html>