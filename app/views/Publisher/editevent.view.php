<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YourEvent - Edit Event</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/create-event-style.css">
    <style>
        /* Dropdown with scroll - show 5 items */
        select[name="event_category"],
        select[name="audience"],
        select[name="location-type"],
        select[name="ticketType"],
        #customFieldType {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }
        
        select[name="event_category"] option,
        select[name="audience"] option,
        select[name="location-type"] option,
        select[name="ticketType"] option,
        #customFieldType option {
            padding: 10px;
        }
        
        /* Set size attribute to show 5 visible items when opened */
        select[name="event_category"][size],
        select[name="audience"][size],
        select[name="location-type"][size],
        select[name="ticketType"][size],
        #customFieldType[size] {
            height: auto;
        }
        
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
        
        /* Expand content area width */
        .main-container {
            max-width: 1600px !important;
        }
        
        .content {
            width: 800px;
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
                                <select name="event_category" class="form-select" size="1" required>
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
                                <select name="audience" class="form-select" size="1" required>
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
                                <select name="location-type" class="form-select" size="1">
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

                <!-- Registration Details -->
                <section class="section" id="ticket">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Registration Details</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="max_participants" class="form-label">Maximum Participants *</label>
                            <div class="input-group">
                                <input type="number" name="max_participants" class="form-input" min="1" required
                                       value="<?= htmlspecialchars($data['old_data']['max_participants'] ?? $data['event']->max_participants) ?>"
                                       placeholder="Maximum number of participants">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="ticketType" class="form-label">Ticket Type *</label>
                            <div class="input-group">
                                <select name="ticketType" class="form-select" size="1" required>
                                    <?php 
                                    $ticketTypes = [
                                        'free-all' => 'Free for All',
                                        'paid-all' => 'Paid for All',
                                        'mixed' => 'Mixed (Free & Paid)'
                                    ];
                                    $selectedTicketType = $data['old_data']['ticket_type'] ?? $data['event']->ticket_type;
                                    foreach ($ticketTypes as $value => $label): 
                                    ?>
                                        <option value="<?= $value ?>" <?= $selectedTicketType == $value ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
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
                                    <select id="customFieldType" class="form-select" size="1">
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

                        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                            <!-- Current Image - Left Side -->
                            <?php if (!empty($data['event']->cover_image)): ?>
                                <div class="current-image-container" style="
                                    flex: 1;
                                    min-width: 300px;
                                    background: #f8f9fa; 
                                    border: 2px solid #1E3A8A; 
                                    border-radius: 12px; 
                                    padding: 20px;
                                    box-shadow: 0 2px 8px rgba(30, 58, 138, 0.1);
                                ">
                                    <p style="color: #1E3A8A; font-size: 14px; font-weight: 600; margin-bottom: 12px; text-align: center;">
                                        <i class="fas fa-image"></i> Current Cover Image
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

                            <!-- New Image Preview - Right Side -->
                            <div class="new-image-container" style="
                                flex: 1;
                                min-width: 300px;
                                background: #f8f9fa; 
                                border: 2px solid #28a745; 
                                border-radius: 12px; 
                                padding: 20px;
                                box-shadow: 0 2px 8px rgba(40, 167, 69, 0.1);
                                display: none;
                            " id="newImagePreviewContainer">
                                <p style="color: #28a745; font-size: 14px; font-weight: 600; margin-bottom: 12px; text-align: center;">
                                    <i class="fas fa-check-circle"></i> New Cover Image Preview
                                </p>
                                <div style="text-align: center;">
                                    <img id="newImagePreview" 
                                         alt="New cover preview" 
                                         style="
                                            max-width: 100%; 
                                            max-height: 300px; 
                                            border-radius: 8px; 
                                            border: 2px solid #e9ecef;
                                            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                                         ">
                                </div>
                                <button type="button" onclick="clearNewImage()" style="
                                    margin-top: 10px;
                                    width: 100%;
                                    background: #dc3545;
                                    color: white;
                                    border: none;
                                    padding: 8px;
                                    border-radius: 6px;
                                    cursor: pointer;
                                    font-size: 12px;
                                    transition: background 0.3s;
                                " onmouseover="this.style.background='#c82333'" 
                                   onmouseout="this.style.background='#dc3545'">
                                    <i class="fas fa-times"></i> Remove New Image
                                </button>
                            </div>
                        </div>

                        <div class="upload-area-container" style="
                            background: #ffffff; 
                            border: 2px dashed #1E3A8A; 
                            border-radius: 12px; 
                            padding: 30px; 
                            text-align: center;
                            transition: all 0.3s ease;
                            margin-top: 20px;
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
    // Dropdown scroll functionality - show 5 items when opened
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.querySelector('select[name="event_category"]');
        const audienceSelect = document.querySelector('select[name="audience"]');
        const locationTypeSelect = document.querySelector('select[name="location-type"]');
        const ticketTypeSelect = document.querySelector('select[name="ticketType"]');
        const customFieldTypeSelect = document.getElementById('customFieldType');
        
        // Apply to all dropdowns
        [categorySelect, audienceSelect, locationTypeSelect, ticketTypeSelect, customFieldTypeSelect].forEach(select => {
            if (select) {
                select.addEventListener('focus', function() {
                    this.size = 5;
                });
                
                select.addEventListener('blur', function() {
                    this.size = 1;
                });
                
                select.addEventListener('change', function() {
                    this.size = 1;
                    this.blur();
                });
            }
        });
        
        // Cover image preview functionality
        const coverFileInput = document.getElementById('coverFileInput');
        if (coverFileInput) {
            coverFileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const newImagePreview = document.getElementById('newImagePreview');
                        const newImageContainer = document.getElementById('newImagePreviewContainer');
                        
                        newImagePreview.src = e.target.result;
                        newImageContainer.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
    
    // Function to clear new image selection
    function clearNewImage() {
        const coverFileInput = document.getElementById('coverFileInput');
        const newImageContainer = document.getElementById('newImagePreviewContainer');
        
        coverFileInput.value = '';
        newImageContainer.style.display = 'none';
    }
    
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