<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YourEvent - Edit Event</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/create-event-style.css">
    <style>
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Back button - simple style */
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            color: #1E3A8A;
            text-decoration: none;
            padding: 10px 0;
            margin-bottom: 20px;
            transition: all 0.3s;
            font-weight: 500;
            border: none;
        }
        .back-button:hover {
            color: #1e40af;
            text-decoration: none;
        }
        
        /* Container */
        .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
        }
        
        /* Required field indicator */
        .form-label.required::after,
        label.required::after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
        }

        /* Use main color #1E3A8A throughout */
        .section-icon {
            background: #1E3A8A !important;
        }
        
        .sidebar-item.active {
            color: #1E3A8A !important;
            background: #fff5f2;
            border-left: 3px solid #1E3A8A !important;
        }
        
        .publish-btn {
            background: #1E3A8A !important;
            border-color: #1E3A8A !important;
        }
        
        .publish-btn:hover {
            background: #1e40af !important;
            border-color: #1e40af !important;
        }
        
        .add-field-btn {
            background: #1E3A8A !important;
        }
        
        .add-field-btn:hover {
            background: #1e40af !important;
        }
        
        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            border-color: #1E3A8A !important;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1) !important;
        }
        
        input:checked + .slider {
            background-color: #1E3A8A !important;
        }
        
        .browse-btn {
            background: #1E3A8A !important;
        }
        
        .browse-btn:hover {
            background: #1e40af !important;
        }
        
        .upload-area.active,
        .upload-area:hover {
            border-color: #1E3A8A !important;
            background: #fff5f2 !important;
        }
        
        .upload-icon {
            color: #1E3A8A !important;
        }
        
        .add-ticket-type-btn {
            background: #1E3A8A !important;
        }
        
        .add-ticket-type-btn:hover {
            background: #1e40af !important;
        }
        
        /* Ticket functionality styles */
        .ticket-container {
            margin-top: 20px;
        }

        .ticket-type-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .ticket-type-option {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .ticket-type-option:hover {
            border-color: #1E3A8A;
            background: #f8fafc;
        }

        .ticket-type-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .ticket-type-option input[type="radio"]:checked + label {
            color: #1E3A8A;
            font-weight: 600;
        }

        .ticket-type-option input[type="radio"]:checked {
            + label::before {
                background: #1E3A8A;
                border-color: #1E3A8A;
            }
        }

        .ticket-type-option label {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            color: #374151;
            margin: 0;
        }

        .ticket-type-option label::before {
            content: '';
            width: 20px;
            height: 20px;
            border: 2px solid #d1d5db;
            border-radius: 50%;
            background: white;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .ticket-type-option input[type="radio"]:checked + label::before {
            background: #1E3A8A;
            border-color: #1E3A8A;
            box-shadow: inset 0 0 0 3px white;
        }

        .ticket-icon {
            font-size: 20px;
            margin-right: 8px;
        }

        .ticket-details {
            margin-top: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            background: #f9fafb;
        }

        .ticket-details.hidden {
            display: none;
        }

        .info-note {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 8px;
            color: #0c4a6e;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .ticket-types-container {
            margin: 20px 0;
        }

        .ticket-type-item {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
        }

        .ticket-type-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .remove-ticket-type-btn {
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 4px;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .remove-ticket-type-btn:hover {
            background: #dc2626;
        }

        .add-ticket-type-btn {
            background: #1E3A8A;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 15px;
        }

        .add-ticket-type-btn:hover {
            background: #1e40af;
        }

        .ticket-discount-section {
            margin: 15px 0;
        }

        .toggle-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #1E3A8A;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .discount-details {
            margin-top: 15px;
        }

        .discount-details.hidden {
            display: none;
        }

        .sale-dates {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        /* Expand content area width */
        .main-container {
            max-width: 1600px !important;
        }
        
        .content {
            width: 1088px;
            /* width: 100% !important; */
        }
        
        .form-container {
            max-width: 1200px !important;
        }
        
        /* Make sections wider */
        .section {
            width: 100%;
        }
        
        .section-content {
            padding: 30px 40px !important;
        }
        
        /* Wider form inputs */
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }
        
        /* Volunteer source styles */
        .volunteer-source-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 12px;
        }
        
        .volunteer-source-option {
            position: relative;
        }
        
        .volunteer-source-option input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }
        
        .volunteer-source-option label {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }
        
        .volunteer-source-option input[type="checkbox"]:checked + label {
            border-color: #1E3A8A;
            background: #F0F4FF;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        }
        
        .volunteer-source-option label:hover {
            border-color: #1E3A8A;
            background: #F9FAFB;
        }
        
        /* Custom Fields Styles */
        .custom-field-builder {
            background: #F9FAFB;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        
        .add-field-btn {
            background: #1E3A8A;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .add-field-btn:hover {
            background: #1e40af;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
        }
        
        .custom-fields-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .custom-field-item {
            background: white;
            border: 2px solid #E5E7EB;
            border-radius: 8px;
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            transition: all 0.3s ease;
        }
        
        .custom-field-item:hover {
            border-color: #1E3A8A;
            box-shadow: 0 2px 8px rgba(30, 58, 138, 0.1);
        }
        
        .field-info {
            flex: 1;
        }
        
        .field-info strong {
            display: block;
            color: #333;
            margin-bottom: 8px;
            font-size: 15px;
        }
        
        .field-type-badge {
            display: inline-block;
            background: #E0E7FF;
            color: #1E3A8A;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 8px;
        }
        
        .required-badge {
            display: inline-block;
            background: #FEE2E2;
            color: #DC2626;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .field-options-display {
            margin-top: 8px;
            color: #666;
            font-size: 13px;
            font-style: italic;
        }
        
        .remove-field-btn {
            background: #FEE2E2;
            color: #DC2626;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .remove-field-btn:hover {
            background: #DC2626;
            color: white;
            transform: scale(1.1);
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #333;
            cursor: pointer;
            user-select: none;
        }
        
        .checkbox-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .info-note {
            background: #EFF6FF;
            border-left: 4px solid #1E3A8A;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1E3A8A;
        }
        
        .info-note i {
            font-size: 18px;
        }
    </style>

</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => ''];
    include __DIR__ . '/components/header.php';
    ?>
    
    <div class="main-container">
        <form action="/unipulse/public/publisher/editevent/<?= $data['event_id'] ?>" method="POST" enctype="multipart/form-data" id="edit-event">
            <!-- Hidden field to help with AJAX detection -->
            <input type="hidden" name="ajax" value="1" id="ajax-flag">
            <input type="hidden" name="ticket_types" id="ticket_types_input" value="">
            <input type="hidden" name="schedule" id="schedule_input" value="">
            <input type="hidden" name="custom_fields" id="custom_fields_input" value="">
            <input type="hidden" name="volunteer_positions" id="volunteer_positions_input" value="">
            
                        <main class="content">
                <!-- Back button -->
                <a href="/unipulse/public/publisher/eventview?id=<?= $data['event_id'] ?>" class="back-button">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>
        
                <h2 style="margin-bottom: 30px;">Edit Event</h2>
        
                <!-- Display success message if exists -->
                <?php if (isset($data['success'])): ?>
                    <div class="success-message" style="background: #4CAF50; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                        <strong>✓ <?= htmlspecialchars($data['success']) ?></strong>
                    </div>
                <?php endif; ?>
                
                <!-- Display errors if any -->
                <?php if (isset($data['errors']) && !empty($data['errors'])): ?>
                    <div class="error-message" style="background: #f44336; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                        <strong>Error(s):</strong>
                        <ul style="margin: 10px 0 0 20px;">
                            <?php foreach ($data['errors'] as $field => $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Basic Event Information -->
                <section class="section" id="general-info">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Basic Information</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">

                    <div class="form-group">
                        <label for="event_name" class="form-label">Event Name *</label>
                        <div class="input-group">
                            <input type="text" name="event_name" class="form-input" required
                                   value="<?= htmlspecialchars($data['old_data']['title'] ?? $data['event']->title) ?>"
                                   placeholder="Enter event name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="event_description" class="form-label">Event Description *</label>
                        <div class="input-group">
                            <textarea name="event_description" class="form-textarea" required
                                      placeholder="Describe your event in detail"><?= htmlspecialchars($data['old_data']['description'] ?? $data['event']->description) ?></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="event_category" class="form-label">Category *</label>
                            <div class="input-group">
                                <select name="event_category" class="form-select" required>
                                    <option value="">Select a category</option>
                                    <?php 
                                    $categories = [
                                        'academic' => 'Academic',
                                        'sports' => 'Sports',
                                        'cultural' => 'Cultural',
                                        'technology' => 'Technology',
                                        'social' => 'Social',
                                        'workshop' => 'Workshop',
                                        'business' => 'Business',
                                        'music' => 'Music'
                                    ];
                                    $selectedCategory = $data['old_data']['category'] ?? $data['event']->category;
                                    foreach ($categories as $value => $label): 
                                    ?>
                                        <option value="<?= $value ?>" <?= $selectedCategory == $value ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="audience" class="form-label">Target Audience *</label>
                            <div class="input-group">
                                <select name="audience" class="form-select" required>
                                    <?php 
                                    $audiences = [
                                        'university-students' => 'University Students',
                                        'public-users' => 'Public Users',
                                        'both' => 'Both'
                                    ];
                                    $selectedAudience = $data['old_data']['target_audience'] ?? $data['event']->target_audience;
                                    foreach ($audiences as $value => $label): 
                                    ?>
                                        <option value="<?= $value ?>" <?= $selectedAudience == $value ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Date & Time -->
                <section class="section" id="location-time">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Date & Time</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="event_date" class="form-label">Event Date *</label>
                            <div class="input-group">
                                <input type="date" name="event_date" class="form-input" required
                                       value="<?= htmlspecialchars($data['old_data']['event_date'] ?? $data['event']->event_date) ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="event_time" class="form-label">Start Time *</label>
                            <div class="input-group">
                                <input type="time" name="event_time" class="form-input" required
                                       value="<?= htmlspecialchars($data['old_data']['event_time'] ?? $data['event']->event_time) ?>">
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Location -->
                <section class="section" id="location">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Location</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">

                    <div class="form-group">
                        <label for="event_location" class="form-label">Location *</label>
                        <div class="input-group">
                            <input type="text" name="event_location" class="form-input" required
                                   value="<?= htmlspecialchars($data['old_data']['location'] ?? $data['event']->location) ?>"
                                   placeholder="Enter event location">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="location-type" class="form-label">Location Type</label>
                            <div class="input-group">
                                <select name="location-type" class="form-select">
                                    <?php 
                                    $locationTypes = [
                                        'inside-university' => 'Inside University',
                                        'outside-university' => 'Outside University'
                                    ];
                                    $selectedLocationType = $data['old_data']['location_type'] ?? $data['event']->location_type ?? 'inside-university';
                                    foreach ($locationTypes as $value => $label): 
                                    ?>
                                        <option value="<?= $value ?>" <?= $selectedLocationType == $value ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="venue_name" class="form-label">Venue Name</label>
                            <div class="input-group">
                                <input type="text" name="venue_name" class="form-input"
                                       value="<?= htmlspecialchars($data['old_data']['venue_name'] ?? $data['event']->venue_name ?? '') ?>"
                                       placeholder="Venue name (optional)">
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Ticket Section -->
                <section class="section" id="ticket">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Ticket</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">
                        <div class="form-group">
                            <div class="ticket-container">
                                <h4>Ticket Booking Options:</h4>

                                <?php 
                                $selectedTicketType = $data['old_data']['ticket_type'] ?? $data['event']->ticket_type ?? 'free-all';
                                ?>

                                <div class="ticket-type-options">
                                    <div class="ticket-type-option">
                                        <input type="radio" id="free-all" name="ticketType" value="free-all" <?= $selectedTicketType == 'free-all' ? 'checked' : '' ?>>
                                        <label for="free-all">
                                            <i class="fas fa-gift ticket-icon" style="color: #10B981;"></i>
                                            Free for All
                                        </label>
                                    </div>
                                    <div class="ticket-type-option">
                                        <input type="radio" id="paid-all" name="ticketType" value="paid-all" <?= $selectedTicketType == 'paid-all' ? 'checked' : '' ?>>
                                        <label for="paid-all">
                                            <i class="fas fa-credit-card ticket-icon" style="color: #F59E0B;"></i>
                                            Paid for All
                                        </label>
                                    </div>
                                    <div class="ticket-type-option">
                                        <input type="radio" id="mixed" name="ticketType" value="mixed" <?= $selectedTicketType == 'mixed' ? 'checked' : '' ?>>
                                        <label for="mixed">
                                            <i class="fas fa-university ticket-icon" style="color: #4A5BCC;"></i>
                                            Free for Uni Students + Paid for Others
                                        </label>
                                    </div>
                                </div>

                                <!-- Free for All Details -->
                                <div id="freeAllDetails" class="ticket-details <?= $selectedTicketType != 'free-all' ? 'hidden' : '' ?>">
                                    <div class="info-note" style="background: #f0fdf4; border-color: #10B981;">
                                        <i class="fas fa-gift"></i>
                                        Free registration for all attendees.
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Registration Limit (Optional)</label>
                                        <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Set a maximum number of registrations if needed</p>
                                        <input type="number" class="form-input" name="max_participants" placeholder="Leave empty for unlimited registrations" min="1"
                                               value="<?= htmlspecialchars($data['old_data']['max_participants'] ?? $data['event']->max_participants ?? '') ?>">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Registration Period (Optional)</label>
                                        <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Set registration period if needed. Leave empty to allow registration until event date</p>

                                        <div class="sale-dates">
                                            <div>
                                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Date</label>
                                                <input type="date" name="registration_start_date" class="form-input registration-start-date"
                                                    min="<?php echo date('Y-m-d'); ?>"
                                                    value="<?= htmlspecialchars($data['old_data']['registration_start_date'] ?? $data['event']->registration_start_date ?? '') ?>">
                                            </div>
                                            <div>
                                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Time</label>
                                                <input type="time" name="registration_start_time" class="form-input"
                                                       value="<?= htmlspecialchars($data['old_data']['registration_start_time'] ?? $data['event']->registration_start_time ?? '') ?>">
                                            </div>
                                            <div>
                                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Date</label>
                                                <input type="date" name="registration_end_date" class="form-input registration-end-date"
                                                    min="<?php echo date('Y-m-d'); ?>"
                                                    value="<?= htmlspecialchars($data['old_data']['registration_end_date'] ?? $data['event']->registration_end_date ?? '') ?>">
                                            </div>
                                            <div>
                                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Time</label>
                                                <input type="time" name="registration_end_time" class="form-input"
                                                       value="<?= htmlspecialchars($data['old_data']['registration_end_time'] ?? $data['event']->registration_end_time ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Paid for All Details -->
                                <div id="paidAllDetails" class="ticket-details <?= $selectedTicketType != 'paid-all' ? 'hidden' : '' ?>">
                                    <div class="info-note">
                                        <i class="fas fa-ticket-alt"></i>
                                        Configure your paid ticket details below
                                    </div>

                                    <!-- Ticket Types Container -->
                                    <div class="ticket-types-container">
                                        <h4 style="margin-bottom: 15px; font-size: 16px; color: #333;">
                                            <i class="fas fa-ticket-alt" style="color: #4A5BCC; margin-right: 8px;"></i>
                                            Ticket Types <span style="color: #dc3545;">*</span>
                                        </h4>

                                        <div id="ticketTypesList">
                                            <?php
                                            // Get existing ticket types data
                                            $existingTicketTypes = [];
                                            if (isset($data['event']->ticket_types)) {
                                                if (is_string($data['event']->ticket_types)) {
                                                    $existingTicketTypes = json_decode($data['event']->ticket_types, true) ?: [];
                                                } else {
                                                    $existingTicketTypes = $data['event']->ticket_types ?: [];
                                                }
                                            }
                                            
                                            if (empty($existingTicketTypes) && $selectedTicketType == 'paid-all') {
                                                // Default ticket type if no existing data
                                                $existingTicketTypes = [[
                                                    'name' => 'General Admission',
                                                    'quantity' => 100,
                                                    'price' => 10,
                                                    'description' => ''
                                                ]];
                                            }
                                            
                                            foreach ($existingTicketTypes as $index => $ticketType):
                                            ?>
                                            <!-- Ticket type item -->
                                            <div class="ticket-type-item" data-ticket-id="<?= $index + 1 ?>">
                                                <div class="ticket-type-header">
                                                    <input type="text" class="form-input ticket-type-name" value="<?= htmlspecialchars($ticketType['name'] ?? 'General Admission') ?>" placeholder="Ticket Type Name" style="max-width: 250px; margin-bottom: 0;">
                                                    <button type="button" class="remove-ticket-type-btn">×</button>
                                                </div>
                                                <div class="ticket-type-details">
                                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                                        <div>
                                                            <label class="form-label required">Quantity Available</label>
                                                            <input type="number" class="form-input ticket-quantity" value="<?= htmlspecialchars($ticketType['quantity'] ?? 100) ?>" placeholder="Enter quantity" min="1">
                                                        </div>
                                                        <div>
                                                            <label class="form-label required">Price (LKR)</label>
                                                            <input type="number" class="form-input ticket-price" value="<?= htmlspecialchars($ticketType['price'] ?? 10) ?>" placeholder="Enter price" min="0" step="0.01">
                                                        </div>
                                                    </div>

                                                    <!-- Discount Section for Ticket Type -->
                                                    <div class="ticket-discount-section">
                                                        <div class="toggle-container">
                                                            <span><i class="fas fa-tag" style="color: #FF6B35; margin-right: 8px;"></i> Discount for University Students?</span>
                                                            <label class="switch">
                                                                <input type="checkbox" class="discount-toggle" <?= isset($ticketType['discount_percent']) && $ticketType['discount_percent'] > 0 ? 'checked' : '' ?>>
                                                                <span class="slider"></span>
                                                            </label>
                                                        </div>

                                                        <div class="discount-details <?= !isset($ticketType['discount_percent']) || $ticketType['discount_percent'] <= 0 ? 'hidden' : '' ?>">
                                                            <div class="info-note" style="background: #f0f9ff; border-color: #0ea5e9; margin-bottom: 15px;">
                                                                <i class="fas fa-info-circle"></i>
                                                                Discount will be applied to university students only
                                                            </div>

                                                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                                                <div>
                                                                    <label class="form-label">Discount Percentage</label>
                                                                    <input type="number" class="form-input discount-percent" placeholder="Enter discount %" min="0" max="100" value="<?= htmlspecialchars($ticketType['discount_percent'] ?? '') ?>">
                                                                </div>
                                                                <div>
                                                                    <label class="form-label">Discounted Price</label>
                                                                    <input type="number" class="form-input discounted-price" placeholder="Calculated price" readonly value="<?= htmlspecialchars($ticketType['discounted_price'] ?? '') ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label class="form-label">Description (Optional)</label>
                                                        <textarea class="form-textarea" placeholder="Describe this ticket type" style="min-height: 60px;"><?= htmlspecialchars($ticketType['description'] ?? '') ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <button type="button" class="add-ticket-type-btn" id="addTicketTypeBtn">
                                            <i class="fas fa-plus"></i> Add Another Ticket Type
                                        </button>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label required">Sale Period</label>
                                        <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Ticket sale period must be between today and event date</p>

                                        <div class="sale-dates">
                                            <div>
                                                <label class="required" style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Date</label>
                                                <input type="date" name="sale_start_date" class="form-input sale-start-date"
                                                    min="<?php echo date('Y-m-d'); ?>"
                                                    value="<?= htmlspecialchars($data['old_data']['sale_start_date'] ?? $data['event']->sale_start_date ?? '') ?>">
                                            </div>
                                            <div>
                                                <label class="required" style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Time</label>
                                                <input type="time" name="sale_start_time" class="form-input"
                                                       value="<?= htmlspecialchars($data['old_data']['sale_start_time'] ?? $data['event']->sale_start_time ?? '') ?>">
                                            </div>
                                            <div>
                                                <label class="required" style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Date</label>
                                                <input type="date" name="sale_end_date" class="form-input sale-end-date"
                                                    min="<?php echo date('Y-m-d'); ?>"
                                                    value="<?= htmlspecialchars($data['old_data']['sale_end_date'] ?? $data['event']->sale_end_date ?? '') ?>">
                                            </div>
                                            <div>
                                                <label class="required" style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Time</label>
                                                <input type="time" name="sale_end_time" class="form-input"
                                                       value="<?= htmlspecialchars($data['old_data']['sale_end_time'] ?? $data['event']->sale_end_time ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mixed (Free for Uni + Paid for Others) Details -->
                                <div id="mixedDetails" class="ticket-details <?= $selectedTicketType != 'mixed' ? 'hidden' : '' ?>">
                                    <div class="info-note" style="background: #eff6ff; border-color: #4A5BCC;">
                                        <i class="fas fa-university"></i>
                                        Free for university students, paid for outside users.
                                    </div>

                                    <!-- Free for University Students Section -->
                                    <div class="form-group">
                                        <h4 style="margin-bottom: 15px; font-size: 16px; color: #333;">
                                            <i class="fas fa-graduation-cap" style="color: #4A5BCC; margin-right: 8px;"></i>
                                            Free Registration for University Students
                                        </h4>

                                        <div class="form-group">
                                            <label class="form-label">Registration Limit (Optional)</label>
                                            <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Set a maximum number of registrations if needed</p>
                                            <input type="number" class="form-input" name="mixed_free_limit" placeholder="Leave empty for unlimited registrations" min="1"
                                                   value="<?= htmlspecialchars($data['old_data']['mixed_free_limit'] ?? $data['event']->mixed_free_limit ?? '') ?>">
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Registration Period</label>
                                            <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Registration period must be between today and event date</p>

                                            <div class="sale-dates">
                                                <div>
                                                    <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Date</label>
                                                    <input type="date" name="mixed_registration_start_date" class="form-input registration-start-date"
                                                        min="<?php echo date('Y-m-d'); ?>"
                                                        value="<?= htmlspecialchars($data['old_data']['mixed_registration_start_date'] ?? $data['event']->mixed_registration_start_date ?? '') ?>">
                                                </div>
                                                <div>
                                                    <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Time</label>
                                                    <input type="time" name="mixed_registration_start_time" class="form-input"
                                                           value="<?= htmlspecialchars($data['old_data']['mixed_registration_start_time'] ?? $data['event']->mixed_registration_start_time ?? '') ?>">
                                                </div>
                                                <div>
                                                    <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Date</label>
                                                    <input type="date" name="mixed_registration_end_date" class="form-input registration-end-date"
                                                        min="<?php echo date('Y-m-d'); ?>"
                                                        value="<?= htmlspecialchars($data['old_data']['mixed_registration_end_date'] ?? $data['event']->mixed_registration_end_date ?? '') ?>">
                                                </div>
                                                <div>
                                                    <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Time</label>
                                                    <input type="time" name="mixed_registration_end_time" class="form-input"
                                                           value="<?= htmlspecialchars($data['old_data']['mixed_registration_end_time'] ?? $data['event']->mixed_registration_end_time ?? '') ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paid for Outside Users Section -->
                                    <div class="form-group">
                                        <h4 style="margin-bottom: 15px; font-size: 16px; color: #333;">
                                            <i class="fas fa-users" style="color: #FF6B35; margin-right: 8px;"></i>
                                            Paid Tickets for Outside Users <span style="color: #dc3545;">*</span>
                                        </h4>

                                        <!-- Ticket Types Container -->
                                        <div class="ticket-types-container">
                                            <h5 style="margin-bottom: 15px; font-size: 14px; color: #666;">
                                                <i class="fas fa-ticket-alt" style="color: #4A5BCC; margin-right: 8px;"></i>
                                                Ticket Types <span style="color: #dc3545;">*</span>
                                            </h5>

                                            <div id="mixedTicketTypesList">
                                                <?php
                                                // Get existing mixed ticket types data
                                                $existingMixedTicketTypes = [];
                                                if (isset($data['event']->mixed_ticket_types)) {
                                                    if (is_string($data['event']->mixed_ticket_types)) {
                                                        $existingMixedTicketTypes = json_decode($data['event']->mixed_ticket_types, true) ?: [];
                                                    } else {
                                                        $existingMixedTicketTypes = $data['event']->mixed_ticket_types ?: [];
                                                    }
                                                }
                                                
                                                if (empty($existingMixedTicketTypes) && $selectedTicketType == 'mixed') {
                                                    // Default ticket type if no existing data
                                                    $existingMixedTicketTypes = [[
                                                        'name' => 'General Admission',
                                                        'quantity' => 100,
                                                        'price' => 15,
                                                        'description' => ''
                                                    ]];
                                                }
                                                
                                                foreach ($existingMixedTicketTypes as $index => $ticketType):
                                                ?>
                                                <!-- Default ticket type -->
                                                <div class="ticket-type-item" data-ticket-id="<?= $index + 1 ?>">
                                                    <div class="ticket-type-header">
                                                        <input type="text" class="form-input ticket-type-name" value="<?= htmlspecialchars($ticketType['name'] ?? 'General Admission') ?>" placeholder="Ticket Type Name" style="max-width: 250px; margin-bottom: 0;">
                                                        <button type="button" class="remove-ticket-type-btn">×</button>
                                                    </div>
                                                    <div class="ticket-type-details">
                                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                                            <div>
                                                                <label class="form-label required">Quantity Available</label>
                                                                <input type="number" class="form-input ticket-quantity" value="<?= htmlspecialchars($ticketType['quantity'] ?? 100) ?>" placeholder="Enter quantity" min="1">
                                                            </div>
                                                            <div>
                                                                <label class="form-label required">Price (LKR)</label>
                                                                <input type="number" class="form-input ticket-price" value="<?= htmlspecialchars($ticketType['price'] ?? 15) ?>" placeholder="Enter price" min="0" step="0.01">
                                                            </div>
                                                        </div>

                                                        <!-- Discount Section for Ticket Type -->
                                                        <div class="ticket-discount-section">
                                                            <div class="toggle-container">
                                                                <span><i class="fas fa-tag" style="color: #FF6B35; margin-right: 8px;"></i>Discount for Outside Users?</span>
                                                                <label class="switch">
                                                                    <input type="checkbox" class="discount-toggle" <?= isset($ticketType['discount_percent']) && $ticketType['discount_percent'] > 0 ? 'checked' : '' ?>>
                                                                    <span class="slider"></span>
                                                                </label>
                                                            </div>

                                                            <div class="discount-details <?= !isset($ticketType['discount_percent']) || $ticketType['discount_percent'] <= 0 ? 'hidden' : '' ?>">
                                                                <div class="info-note" style="background: #f0f9ff; border-color: #0ea5e9; margin-bottom: 15px;">
                                                                    <i class="fas fa-info-circle"></i>
                                                                    Discount will be applied to Outside Users
                                                                </div>

                                                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                                                    <div>
                                                                        <label class="form-label">Discount Percentage</label>
                                                                        <input type="number" class="form-input discount-percent" placeholder="Enter discount %" min="0" max="100" value="<?= htmlspecialchars($ticketType['discount_percent'] ?? '') ?>">
                                                                    </div>
                                                                    <div>
                                                                        <label class="form-label">Discounted Price</label>
                                                                        <input type="number" class="form-input discounted-price" placeholder="Calculated price" readonly value="<?= htmlspecialchars($ticketType['discounted_price'] ?? '') ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <label class="form-label">Description (Optional)</label>
                                                            <textarea class="form-textarea" placeholder="Describe this ticket type" style="min-height: 60px;"><?= htmlspecialchars($ticketType['description'] ?? '') ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>

                                            <button type="button" class="add-ticket-type-btn" id="addMixedTicketTypeBtn">
                                                <i class="fas fa-plus"></i> Add Another Ticket Type
                                            </button>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label required">Sale Period</label>
                                            <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Ticket sale period must be between today and event date</p>

                                            <div class="sale-dates">
                                                <div>
                                                    <label class="required" style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Date</label>
                                                    <input type="date" name="mixed_sale_start_date" class="form-input sale-start-date"
                                                        min="<?php echo date('Y-m-d'); ?>"
                                                        value="<?= htmlspecialchars($data['old_data']['mixed_sale_start_date'] ?? $data['event']->mixed_sale_start_date ?? '') ?>">
                                                </div>
                                                <div>
                                                    <label class="required" style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Time</label>
                                                    <input type="time" name="mixed_sale_start_time" class="form-input"
                                                           value="<?= htmlspecialchars($data['old_data']['mixed_sale_start_time'] ?? $data['event']->mixed_sale_start_time ?? '') ?>">
                                                </div>
                                                <div>
                                                    <label class="required" style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Date</label>
                                                    <input type="date" name="mixed_sale_end_date" class="form-input sale-end-date"
                                                        min="<?php echo date('Y-m-d'); ?>"
                                                        value="<?= htmlspecialchars($data['old_data']['mixed_sale_end_date'] ?? $data['event']->mixed_sale_end_date ?? '') ?>">
                                                </div>
                                                <div>
                                                    <label class="required" style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Time</label>
                                                    <input type="time" name="mixed_sale_end_time" class="form-input"
                                                           value="<?= htmlspecialchars($data['old_data']['mixed_sale_end_time'] ?? $data['event']->mixed_sale_end_time ?? '') ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Volunteers Section -->
                <section class="section" id="Request-Volunteer">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Volunteer Requirements</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">

                    <div class="volunteer-toggle">
                        <div class="toggle-container">
                            <label for="volunteerToggle" style="display: block; margin-bottom: 8px; color: #333;">
                                Do you want volunteers?
                            </label>

                            <label class="switch">
                                <input type="checkbox" id="volunteerToggle" name="volunteerToggle" value="1"
                                       <?= (($data['old_data']['needs_volunteers'] ?? $data['event']->needs_volunteers) == 1) ? 'checked' : '' ?>>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div id="volunteerDetails" class="volunteer-details <?= (($data['old_data']['needs_volunteers'] ?? $data['event']->needs_volunteers) == 1) ? '' : 'hidden' ?>" style="margin-top: 20px;">
                            <div class="info-note">
                                <i class="fas fa-hands-helping"></i>
                                Select where you'd like to recruit volunteers from
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Volunteer Source</label>
                                <div class="volunteer-source-options">
                                    <?php 
                                    $volunteerSources = $data['old_data']['volunteer_sources'] ?? $data['event']->volunteer_sources ?? [];
                                    if (!is_array($volunteerSources)) {
                                        $volunteerSources = json_decode($volunteerSources, true) ?? [];
                                    }
                                    ?>
                                    <div class="volunteer-source-option">
                                        <input type="checkbox" id="faculty-volunteers" name="volunteer-source[]" value="faculty"
                                            <?= in_array('faculty', $volunteerSources) ? 'checked' : '' ?>>
                                        <label for="faculty-volunteers">
                                            <i class="fas fa-graduation-cap" style="color: #4A5BCC; font-size: 18px;"></i>
                                            From My Faculty
                                        </label>
                                    </div>
                                    <div class="volunteer-source-option">
                                        <input type="checkbox" id="university-volunteers" name="volunteer-source[]" value="university"
                                            <?= in_array('university', $volunteerSources) ? 'checked' : '' ?>>
                                        <label for="university-volunteers">
                                            <i class="fas fa-university" style="color: #FF6B35; font-size: 18px;"></i>
                                            From My University
                                        </label>
                                    </div>
                                    <div class="volunteer-source-option">
                                        <input type="checkbox" id="public-volunteers" name="volunteer-source[]" value="public"
                                            <?= in_array('public', $volunteerSources) ? 'checked' : '' ?>>
                                        <label for="public-volunteers">
                                            <i class="fas fa-users" style="color: #10B981; font-size: 18px;"></i>
                                            Public Users
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="volunteers_needed" class="form-label required">Number of Volunteers Needed</label>
                                <p style="font-size: 12px; color: #666; margin-bottom: 8px;">How many volunteers do you need?</p>
                                <div class="input-group">
                                    <input type="number" name="volunteers_needed" class="form-input" min="1" max="1000"
                                           value="<?= htmlspecialchars($data['old_data']['volunteers_needed'] ?? $data['event']->volunteers_needed ?? '') ?>"
                                           placeholder="e.g., 5" style="max-width: 200px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Donations Section -->
                <section class="section" id="donation">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Donation Options</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">

                    <div class="donation-toggle">
                        <div class="toggle-container">
                            <label for="donationToggle" style="display: block; margin-bottom: 8px; color: #333;">
                                Accept donations for this event?
                            </label>

                            <label class="switch">
                                <input type="checkbox" id="donationToggle" name="donationToggle" value="1"
                                       <?= (($data['old_data']['accepts_donations'] ?? $data['event']->accepts_donations) == 1) ? 'checked' : '' ?>>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </section>

                <!-- Custom Fields Section -->
                <section class="section" id="custom-fields">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Custom Fields</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">
                        <div class="info-note">
                            <i class="fas fa-info-circle"></i>
                            Add custom fields to collect additional information from participants during registration
                        </div>

                        <div class="custom-field-builder">
                            <div class="form-row" style="margin-bottom: 20px;">
                                <div class="form-group">
                                    <label class="form-label">Field Label</label>
                                    <input type="text" id="customFieldLabel" class="form-input" 
                                           placeholder="e.g., Dietary Restrictions, T-Shirt Size">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Field Type</label>
                                    <select id="customFieldType" class="form-select">
                                        <option value="text">Text Input</option>
                                        <option value="textarea">Text Area</option>
                                        <option value="select">Dropdown</option>
                                        <option value="checkbox">Checkbox</option>
                                        <option value="radio">Radio Buttons</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" id="customFieldOptionsContainer" style="display: none;">
                                <label class="form-label">Options (comma-separated)</label>
                                <input type="text" id="customFieldOptions" class="form-input" 
                                       placeholder="e.g., Small, Medium, Large, XL">
                                <p style="font-size: 12px; color: #666; margin-top: 5px;">
                                    Separate each option with a comma
                                </p>
                            </div>

                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="customFieldRequired">
                                    <span>Make this field required</span>
                                </label>
                            </div>

                            <button type="button" class="add-field-btn" onclick="addCustomField()">
                                <i class="fas fa-plus"></i> Add Custom Field
                            </button>
                        </div>

                        <!-- Existing Custom Fields Display -->
                        <?php 
                        $existingCustomFields = $data['event']->custom_fields ?? [];
                        if (!is_array($existingCustomFields)) {
                            $existingCustomFields = json_decode($existingCustomFields, true) ?? [];
                        }
                        if (!empty($existingCustomFields)): 
                        ?>
                        <div class="custom-fields-preview" style="margin-top: 30px;">
                            <h4 style="color: #333; margin-bottom: 15px; font-size: 16px;">
                                <i class="fas fa-list"></i> Current Custom Fields
                            </h4>
                            <div id="customFieldsList" class="custom-fields-list">
                                <?php foreach ($existingCustomFields as $index => $field): ?>
                                <div class="custom-field-item" data-index="<?= $index ?>">
                                    <div class="field-info">
                                        <strong><?= htmlspecialchars($field['label']) ?></strong>
                                        <span class="field-type-badge"><?= htmlspecialchars($field['type']) ?></span>
                                        <?php if ($field['required']): ?>
                                        <span class="required-badge">Required</span>
                                        <?php endif; ?>
                                        <?php if (!empty($field['options'])): ?>
                                        <div class="field-options-display">
                                            Options: <?= htmlspecialchars(implode(', ', $field['options'])) ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="remove-field-btn" onclick="removeCustomField(<?= $index ?>)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="custom-fields-preview" style="margin-top: 30px; display: none;">
                            <h4 style="color: #333; margin-bottom: 15px; font-size: 16px;">
                                <i class="fas fa-list"></i> Custom Fields Preview
                            </h4>
                            <div id="customFieldsList" class="custom-fields-list">
                                <!-- Custom fields will be added here -->
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Cover Image -->
                <section class="section" id="upload-cover">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Cover Image</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">
                        <p style="color: #666; margin-bottom: 15px;">Upload the event cover to capture your audience's attention</p>

                        <?php if (!empty($data['event']->cover_image)): ?>
                            <div class="current-image-container" style="
                                background: #f8f9fa; 
                                border: 2px solid #1E3A8A; 
                                border-radius: 12px; 
                                padding: 20px; 
                                margin-bottom: 20px;
                                box-shadow: 0 2px 8px rgba(30, 58, 138, 0.1);
                            ">
                                <p style="color: #1E3A8A; font-size: 14px; font-weight: 600; margin-bottom: 12px;">
                                    <i class="fas fa-image"></i> Current Cover Image:
                                </p>
                                <div style="text-align: center;">
                                    <img src="/unipulse/public/<?= htmlspecialchars($data['event']->cover_image) ?>" 
                                         alt="Current cover" 
                                         style="
                                            max-width: 100%; 
                                            max-height: 300px; 
                                            border-radius: 8px; 
                                            border: 2px solid #e9ecef;
                                            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                                         ">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="upload-area-container" style="
                            background: #ffffff; 
                            border: 2px dashed #1E3A8A; 
                            border-radius: 12px; 
                            padding: 30px; 
                            text-align: center;
                            transition: all 0.3s ease;
                        " onmouseover="this.style.background='#f0f4ff'; this.style.borderColor='#1e40af';" 
                           onmouseout="this.style.background='#ffffff'; this.style.borderColor='#1E3A8A';">
                            <div class="upload-icon" style="font-size: 48px; color: #1E3A8A; margin-bottom: 16px;">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="upload-text">
                                <h4 style="color: #333; margin-bottom: 8px;">Drag & Drop</h4>
                                <p style="color: #666; font-size: 14px; margin-bottom: 16px;">Upload your cover photo or click browse</p>
                            </div>
                            <input type="file" name="cover_image" id="coverFileInput" class="file-input" accept="image/*" style="display: none;">
                            <label for="coverFileInput" class="browse-btn" style="
                                background: #1E3A8A;
                                color: white;
                                padding: 12px 24px;
                                border-radius: 8px;
                                cursor: pointer;
                                display: inline-block;
                                font-weight: 600;
                                transition: all 0.3s ease;
                            " onmouseover="this.style.background='#1e40af';" 
                               onmouseout="this.style.background='#1E3A8A';">
                                Browse Files
                            </label>
                            <p style="color: #999; font-size: 12px; margin-top: 12px;">JPG, PNG, GIF up to 5MB</p>
                        </div>
                    </div>
                </section>

                <!-- Additional Requirements -->
                <section class="section" id="requirements">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Additional Information</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">

                    <div class="form-group">
                        <label for="requirements" class="form-label">Requirements (Optional)</label>
                        <div class="input-group">
                            <textarea name="requirements" class="form-textarea" 
                                      placeholder="Any special requirements or instructions for participants..."><?php 
                                      $requirements = $data['old_data']['requirements'] ?? $data['event']->requirements ?? '';
                                      if (is_array($requirements)) {
                                          echo htmlspecialchars(implode("\n", $requirements));
                                      } else if (is_string($requirements) && !empty($requirements)) {
                                          $decoded = json_decode($requirements, true);
                                          if (is_array($decoded)) {
                                              echo htmlspecialchars(implode("\n", $decoded));
                                          } else {
                                              echo htmlspecialchars($requirements);
                                          }
                                      }
                                      ?></textarea>
                        </div>
                    </div>
                </section>

                <!-- Action Buttons -->
                <div class="bottom-actions">
                    <div class="action-buttons">
                        <button type="button" onclick="window.location.href='/unipulse/public/publisher/eventview?id=<?= $data['event_id'] ?>'" class="cancel-btn">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="publish-btn">
                            <i class="fas fa-save"></i> Update Event
                        </button>
                    </div>
                </div>
            </main>
        </form>
    </div>
    <?php include __DIR__ . '/../components/footer.php'; ?>
    <script src="/unipulse/public/assets/js/create-event-app.js"></script>
    
    <script>
    // Function to show success message
    function showSuccessMessage(message) {
        // Remove any existing success messages
        const existingMessage = document.querySelector('.success-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        // Create success message element
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        successDiv.style.cssText = `
            background: #4CAF50;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 16px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            animation: slideDown 0.3s ease-out;
        `;
        successDiv.innerHTML = `
            <strong>✓ ${message}</strong>
            <button onclick="this.parentElement.remove()" style="
                background: none; 
                border: none; 
                color: white; 
                float: right; 
                cursor: pointer; 
                font-size: 18px;
                margin-top: -2px;
            ">×</button>
        `;
        
        // Insert at the top of the form
        const form = document.getElementById('edit-event');
        form.insertBefore(successDiv, form.firstChild);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (successDiv.parentElement) {
                successDiv.remove();
            }
        }, 5000);
    }
    
    // Handle form submission
    document.getElementById('edit-event').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.querySelector('.publish-btn');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating Event...';
        submitBtn.disabled = true;
        
        // Collect ticket types data before submitting
        const ticketType = document.querySelector('input[name="ticketType"]:checked')?.value || 'free-all';
        
        if (ticketType === 'paid-all') {
            // Collect paid ticket types
            const ticketTypes = [];
            document.querySelectorAll('#ticketTypesList .ticket-type-item').forEach(item => {
                const name = item.querySelector('.ticket-type-name')?.value || '';
                const quantity = item.querySelector('.ticket-quantity')?.value || '';
                const price = item.querySelector('.ticket-price')?.value || '';
                const description = item.querySelector('.form-textarea')?.value || '';
                const discountToggle = item.querySelector('.discount-toggle')?.checked || false;
                const discountPercent = item.querySelector('.discount-percent')?.value || '';
                const discountedPrice = item.querySelector('.discounted-price')?.value || '';
                
                if (name && quantity && price) {
                    ticketTypes.push({
                        name: name,
                        quantity: parseInt(quantity),
                        price: parseFloat(price),
                        description: description,
                        discount_percent: discountToggle ? parseFloat(discountPercent) || 0 : 0,
                        discounted_price: discountToggle ? parseFloat(discountedPrice) || 0 : 0
                    });
                }
            });
            
            if (ticketTypes.length > 0) {
                document.getElementById('ticket_types_input').value = JSON.stringify(ticketTypes);
            }
        } else if (ticketType === 'mixed') {
            // Collect mixed ticket types (for outside users)
            const mixedTicketTypes = [];
            document.querySelectorAll('#mixedTicketTypesList .ticket-type-item').forEach(item => {
                const name = item.querySelector('.ticket-type-name')?.value || '';
                const quantity = item.querySelector('.ticket-quantity')?.value || '';
                const price = item.querySelector('.ticket-price')?.value || '';
                const description = item.querySelector('.form-textarea')?.value || '';
                const discountToggle = item.querySelector('.discount-toggle')?.checked || false;
                const discountPercent = item.querySelector('.discount-percent')?.value || '';
                const discountedPrice = item.querySelector('.discounted-price')?.value || '';
                
                if (name && quantity && price) {
                    mixedTicketTypes.push({
                        name: name,
                        quantity: parseInt(quantity),
                        price: parseFloat(price),
                        description: description,
                        discount_percent: discountToggle ? parseFloat(discountPercent) || 0 : 0,
                        discounted_price: discountToggle ? parseFloat(discountedPrice) || 0 : 0
                    });
                }
            });
            
            if (mixedTicketTypes.length > 0) {
                // For mixed tickets, we need to send them as mixed_ticket_types
                const mixedTicketTypesInput = document.createElement('input');
                mixedTicketTypesInput.type = 'hidden';
                mixedTicketTypesInput.name = 'mixed_ticket_types';
                mixedTicketTypesInput.value = JSON.stringify(mixedTicketTypes);
                this.appendChild(mixedTicketTypesInput);
            }
        }
        
        // Get form data
        const formData = new FormData(this);
        
        // Ensure AJAX detection
        formData.set('ajax', '1');
        
        // Submit form
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers.get('content-type'));
            
            if (!response.ok && response.status !== 200) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    console.error('Non-JSON response received:', text);
                    throw new Error('Server returned non-JSON response: ' + text.substring(0, 200));
                });
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message with better styling
                showSuccessMessage('Event updated successfully! Redirecting to event view...');
                
                // Redirect to event view after a short delay
                setTimeout(function() {
                    window.location.href = '/unipulse/public/publisher/eventview?id=<?= $data['event_id'] ?>';
                }, 1500);
            } else {
                // Show error messages
                let errorMessage = 'Please fix the following errors:\n';
                if (data.errors) {
                    for (const [field, message] of Object.entries(data.errors)) {
                        errorMessage += `- ${message}\n`;
                    }
                } else {
                    errorMessage += data.message || 'Unknown error occurred';
                }
                alert(errorMessage);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network or server error: ' + error.message + '. Please check the console for more details.');
        })
        .finally(() => {
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Custom Fields Functionality
    let customFieldsArray = [];
    
    // Load existing custom fields
    <?php if (!empty($existingCustomFields)): ?>
    customFieldsArray = <?= json_encode($existingCustomFields) ?>;
    <?php endif; ?>
    
    // Show/hide options field based on field type
    const customFieldType = document.getElementById('customFieldType');
    const customFieldOptionsContainer = document.getElementById('customFieldOptionsContainer');
    
    if (customFieldType) {
        customFieldType.addEventListener('change', function() {
            if (this.value === 'select' || this.value === 'radio' || this.value === 'checkbox') {
                customFieldOptionsContainer.style.display = 'block';
            } else {
                customFieldOptionsContainer.style.display = 'none';
            }
        });
    }
    
    function addCustomField() {
        const label = document.getElementById('customFieldLabel').value.trim();
        const type = document.getElementById('customFieldType').value;
        const optionsInput = document.getElementById('customFieldOptions').value.trim();
        const required = document.getElementById('customFieldRequired').checked;
        
        if (!label) {
            alert('Please enter a field label!');
            return;
        }
        
        if ((type === 'select' || type === 'radio' || type === 'checkbox') && !optionsInput) {
            alert('Please enter options for this field type!');
            return;
        }
        
        // Parse options
        const options = (type === 'select' || type === 'radio' || type === 'checkbox') 
            ? optionsInput.split(',').map(opt => opt.trim()).filter(opt => opt !== '')
            : [];
        
        // Add to array
        const newField = {
            label: label,
            type: type,
            options: options,
            required: required
        };
        customFieldsArray.push(newField);
        
        // Update hidden input
        document.getElementById('custom_fields').value = JSON.stringify(customFieldsArray);
        
        // Update UI
        renderCustomFields();
        
        // Clear form
        document.getElementById('customFieldLabel').value = '';
        document.getElementById('customFieldOptions').value = '';
        document.getElementById('customFieldRequired').checked = false;
        document.getElementById('customFieldType').value = 'text';
        customFieldOptionsContainer.style.display = 'none';
    }
    
    function removeCustomField(index) {
        if (confirm('Are you sure you want to remove this custom field?')) {
            customFieldsArray.splice(index, 1);
            document.getElementById('custom_fields').value = JSON.stringify(customFieldsArray);
            renderCustomFields();
        }
    }
    
    function renderCustomFields() {
        const preview = document.querySelector('.custom-fields-preview');
        const list = document.getElementById('customFieldsList');
        
        if (customFieldsArray.length === 0) {
            preview.style.display = 'none';
            return;
        }
        
        preview.style.display = 'block';
        list.innerHTML = '';
        
        customFieldsArray.forEach((field, index) => {
            const fieldItem = document.createElement('div');
            fieldItem.className = 'custom-field-item';
            fieldItem.setAttribute('data-index', index);
            
            let optionsDisplay = '';
            if (field.options && field.options.length > 0) {
                optionsDisplay = `
                    <div class="field-options-display">
                        Options: ${field.options.join(', ')}
                    </div>
                `;
            }
            
            fieldItem.innerHTML = `
                <div class="field-info">
                    <strong>${field.label}</strong>
                    <span class="field-type-badge">${field.type}</span>
                    ${field.required ? '<span class="required-badge">Required</span>' : ''}
                    ${optionsDisplay}
                </div>
                <button type="button" class="remove-field-btn" onclick="removeCustomField(${index})">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            list.appendChild(fieldItem);
        });
    }
    
    // Initial render
    renderCustomFields();
    </script>
</body>

</html>