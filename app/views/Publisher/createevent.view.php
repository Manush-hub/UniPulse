<?php
$universities = [
    'university-of-colombo' => 'University of Colombo',
    'university-of-peradeniya' => 'University of Peradeniya',
    'university-of-sri-jayewardenepura' => 'University of Sri Jayewardenepura',
    'university-of-kelaniya' => 'University of Kelaniya',
    'university-of-moratuwa' => 'University of Moratuwa',
    'university-of-jaffna' => 'University of Jaffna',
    'university-of-ruhuna' => 'University of Ruhuna',
    'eastern-university' => 'Eastern University, Sri Lanka',
    'south-eastern-university' => 'South Eastern University of Sri Lanka',
    'rajarata-university' => 'Rajarata University of Sri Lanka',
    'sabaragamuwa-university' => 'Sabaragamuwa University of Sri Lanka',
    'wayamba-university' => 'Wayamba University of Sri Lanka',
    'uva-wellassa-university' => 'Uva Wellassa University',
    'open-university' => 'Open University of Sri Lanka',
    'buddhist-and-pali-university' => 'Buddhist and Pali University of Sri Lanka',
    'sliit' => 'Sri Lanka Institute of Information Technology (SLIIT)',
    'nsbm' => 'NSBM Green University',
    'cinec' => 'CINEC Campus',
    'apiit' => 'Asia Pacific Institute of Information Technology (APIIT)',
    'kiu' => 'KIU (Kaatsu International University)',
    'metropolitan-campus' => 'KIU (Kaatsu International University)',
    'other' => 'Other'
];

$faculties = [
    'ucsc' => 'University of Colombo School of Computing (UCSC)',
    'faculty-of-engineering' => 'Faculty of Engineering',
    'faculty-of-medicine' => 'Faculty of Medicine',
    'faculty-of-science' => 'Faculty of Science',
    'faculty-of-management' => 'Faculty of Management and Finance',
    'faculty-of-arts' => 'Faculty of Arts',
    'faculty-of-law' => 'Faculty of Law',
    'faculty-of-information-technology' => 'Faculty of Information Technology',
    'faculty-of-applied-sciences' => 'Faculty of Applied Sciences',
    'faculty-of-agriculture' => 'Faculty of Agriculture',
    'faculty-of-architecture' => 'Faculty of Architecture',
    'faculty-of-education' => 'Faculty of Education',
    'faculty-of-social-sciences' => 'Faculty of Social Sciences',
    'faculty-of-allied-health-sciences' => 'Faculty of Allied Health Sciences',
    'faculty-of-dental-sciences' => 'Faculty of Dental Sciences',
    'other' => 'Other'
];

$pubUniversity = isset($publisherDetails->university) ? $publisherDetails->university : '';
$pubFaculty = isset($publisherDetails->faculty) ? $publisherDetails->faculty : '';

$displayUniversity = isset($universities[$pubUniversity]) ? $universities[$pubUniversity] : $pubUniversity;
$displayFaculty = isset($faculties[$pubFaculty]) ? $faculties[$pubFaculty] : $pubFaculty;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YourEvent - Create an event</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/create-event-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Publisher/createevent-style.css">

</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => ''];
    include __DIR__ . '/components/header.php';
    ?>


    <div class="main-container">
        <!-- <aside class="sidebar">
            <div style="margin-bottom: 20px;">
                <div style="font-size: 12px; color: #666; margin-bottom: 5px;">Last saved</div>
                <div style="font-size: 12px; color: #333;">Monday, June 06 | 06:42 AM</div>
                <div style="font-size: 12px; color: #666; margin-top: 5px;">Status</div>
                <div style="font-size: 12px; color: #333;">Draft</div>
            </div>

            <h3>EVENT INFORMATION</h3>
            <div class="sidebar-item" data-target="upload-cover">Upload cover</div>
            <div class="sidebar-item" data-target="general-info">General information</div>
            <div class="sidebar-item" data-target="location-time">Location and time</div>
            <div class="sidebar-item" data-target="ticket">Ticket</div>
            <div class="sidebar-item" data-target="Request-Volunteer">Request Volunteer</div>
            <div class="sidebar-item" data-target="donation">Donations</div>
            <div class="sidebar-item" data-target="custom-fields">Custom Fields</div>

        </aside> -->

        <form action="/unipulse/public/publisher/createevent" method="POST" enctype="multipart/form-data" id="create-event" novalidate>
            <input type="hidden" name="ajax" value="1" id="ajax-flag">
            <input type="hidden" name="ticket_types" id="ticket_types_input" value="">
            <input type="hidden" name="schedule" id="schedule_input" value="">
            <input type="hidden" name="volunteer_positions" id="volunteer_positions_input" value="">
            <input type="hidden" name="sponsorship_packages" id="sponsorship_packages_input" value="">

            <main class="form-container">
                <h2 style="margin-bottom: 30px;">Create an event</h2>

                <section class="section" id="upload-cover">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Upload cover <span style="color: #dc3545;">*</span></h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">
                        <p style="color: #666; margin-bottom: 15px;">Upload the event cover to capture your audience's
                            attention</p>

                        <div class="cover-image" id="coverImageContainer">
                            <div class="cover-image-placeholder">
                                <div>
                                    <div style="font-size: 32px;">Rock Revolt</div>
                                    <div style="font-size: 32px;">Power</div>
                                    <div style="font-size: 32px;">Passion</div>
                                </div>
                            </div>
                        </div>

                        <div class="album-upload-container" style="margin-top: 20px;">
                            <div class="upload-area" id="coverUploadArea">
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="upload-text">
                                    <h4>Drag & Drop</h4>
                                    <p>Upload your cover photo or click browse</p>
                                </div>
                                <input type="file" name="cover_image" id="coverFileInput" class="file-input" accept="image/*" required>
                                <label for="coverFileInput" class="browse-btn">Browse Files</label>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="section" id="general-info">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>General information</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">
                        <div class="form-group">
                            <label class="form-label required">Name</label>
                            <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Make it catchy and memorable</p>
                            <input type="text" name="event_name" class="form-input" required
                                minlength="3" maxlength="200"
                                placeholder="Enter event name">
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Description</label>
                            <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Provide essential event details</p>
                            <textarea name="event_description" class="form-textarea"
                                maxlength="5000" required
                                placeholder="Enter event description"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Category</label>
                            <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Select the category for your event
                            </p>
                            <select name="event_category" class="form-select" size="1" required>
                                <option value="">Select Category</option>
                                <option value="academic">Academic</option>
                                <option value="sports">Sports</option>
                                <option value="cultural">Cultural</option>
                                <option value="technology">Technology</option>
                                <option value="social">Social</option>
                                <option value="workshop">Workshop</option>
                                <option value="business">Business</option>
                                <option value="music">Music</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </section>

                <section class="section" id="event-visibility">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Event Visibility</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">
                        <div class="form-group">
                            <label class="form-label required">Who can see this event?</label>
                            <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Choose the audience level for your event</p>

                            <div class="visibility-options" style="display: grid; gap: 15px;">
                                <!-- Faculty Only -->
                                <div class="visibility-option" style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 15px; cursor: pointer; transition: all 0.3s;">
                                    <input type="radio" id="visibility-faculty" name="event_visibility" value="faculty-only" style="margin-right: 10px;">
                                    <label for="visibility-faculty" style="cursor: pointer; display: inline-flex; align-items: flex-start; gap: 12px; width: calc(100% - 30px);">
                                        <i class="fas fa-building" style="color: #8b5cf6; font-size: 20px; margin-top: 2px;"></i>
                                        <div>
                                            <div style="font-weight: 600; color: #333; margin-bottom: 4px;">Faculty Only</div>
                                            <div style="font-size: 12px; color: #666;">Only members from your specific faculty can view and join this event</div>
                                        </div>
                                    </label>
                                </div>

                                <!-- University Only -->
                                <div class="visibility-option" style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 15px; cursor: pointer; transition: all 0.3s;">
                                    <input type="radio" id="visibility-university" name="event_visibility" value="university-only" checked style="margin-right: 10px;">
                                    <label for="visibility-university" style="cursor: pointer; display: inline-flex; align-items: flex-start; gap: 12px; width: calc(100% - 30px);">
                                        <i class="fas fa-university" style="color: #3b82f6; font-size: 20px; margin-top: 2px;"></i>
                                        <div>
                                            <div style="font-weight: 600; color: #333; margin-bottom: 4px;">University Only</div>
                                            <div style="font-size: 12px; color: #666;">All students and staff from your university can view and join this event</div>
                                        </div>
                                    </label>
                                </div>

                                <!-- All Universities -->
                                <div class="visibility-option" style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 15px; cursor: pointer; transition: all 0.3s;">
                                    <input type="radio" id="visibility-all-universities" name="event_visibility" value="all-universities" style="margin-right: 10px;">
                                    <label for="visibility-all-universities" style="cursor: pointer; display: inline-flex; align-items: flex-start; gap: 12px; width: calc(100% - 30px);">
                                        <i class="fas fa-graduation-cap" style="color: #10b981; font-size: 20px; margin-top: 2px;"></i>
                                        <div>
                                            <div style="font-weight: 600; color: #333; margin-bottom: 4px;">All Universities</div>
                                            <div style="font-size: 12px; color: #666;">Students and staff from all universities can view and join this event</div>
                                        </div>
                                    </label>
                                </div>

                                <!-- Public -->
                                <div class="visibility-option" style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 15px; cursor: pointer; transition: all 0.3s;">
                                    <input type="radio" id="visibility-public" name="event_visibility" value="public" style="margin-right: 10px;">
                                    <label for="visibility-public" style="cursor: pointer; display: inline-flex; align-items: flex-start; gap: 12px; width: calc(100% - 30px);">
                                        <i class="fas fa-globe" style="color: #f59e0b; font-size: 20px; margin-top: 2px;"></i>
                                        <div>
                                            <div style="font-weight: 600; color: #333; margin-bottom: 4px;">Public</div>
                                            <div style="font-size: 12px; color: #666;">Everyone including non-university members can view and join this event</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="section" id="location-time">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Location and time</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">
                        <div class="form-group">
                            <label class="form-label">Event Location Type</label>
                            <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Select where your event will be
                                held</p>

                            <div class="location-type-options" style="display: flex; gap: 20px; margin-bottom: 20px;">
                                <div class="location-type-option">
                                    <input type="radio" id="inside-university" name="location-type"
                                        value="inside-university" checked>
                                    <label for="inside-university"
                                        style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                        <i class="fas fa-university" style="color: #4A5BCC;"></i>
                                        Inside University
                                    </label>
                                </div>
                                <div class="location-type-option">
                                    <input type="radio" id="outside-university" name="location-type"
                                        value="outside-university">
                                    <label for="outside-university"
                                        style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                        <i class="fas fa-map-marker-alt" style="color: #FF6B35;"></i>
                                        Outside University
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Location Details</label>

                            <!-- Inside University Location Fields -->
                            <div id="insideUniversityLocation" class="location-fields">
                                <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Specify the university
                                    location details</p>

                                <div class="university-location-inputs"
                                    style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                    <div>
                                        <label class="required"
                                            style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">University</label>
                                        <input type="text" name="selected_university" class="form-input" readonly
                                            value="<?php echo htmlspecialchars($displayUniversity); ?>"
                                            style="background-color: #f9fafb; cursor: not-allowed; color: #6b7280;">
                                    </div>
                                    <div>
                                        <label class="required"
                                            style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Faculty/Department</label>
                                        <input type="text" name="faculty_department" class="form-input" readonly
                                            value="<?php echo htmlspecialchars($displayFaculty); ?>"
                                            style="background-color: #f9fafb; cursor: not-allowed; color: #6b7280;">
                                    </div>
                                </div>

                                <div>
                                    <label class="required" style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Exact
                                        Location</label>
                                    <input type="text" name="event_location" class="form-input"
                                        minlength="3" maxlength="200"
                                        placeholder="e.g., Main Auditorium, Lecture Hall A, Sports Complex">
                                </div>
                            </div>

                            <!-- Outside University Location Fields -->
                            <div id="outsideUniversityLocation" class="location-fields hidden">
                                <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Provide the complete address
                                    details</p>

                                <div class="external-location-inputs"
                                    style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                    <div>
                                        <label class="required"
                                            style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Venue
                                            Name</label>
                                        <input type="text" name="venue_name" class="form-input" placeholder="e.g., Colombo Convention Center">
                                    </div>
                                    <div>
                                        <label
                                            style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Street
                                            Address</label>
                                        <input type="text" name="street_address" class="form-input" placeholder="Enter street address">
                                    </div>
                                    <div>
                                        <label class="required"
                                            style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">City</label>
                                        <input type="text" name="city" class="form-input" placeholder="Enter city">
                                    </div>
                                    <div>
                                        <label
                                            style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">District/Province</label>
                                        <input type="text" name="district_province" class="form-input" placeholder="Enter district or province">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Time</label>
                            <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Let attendees know when your event
                                starts and ends</p>

                            <div class="time-inputs">
                                <div>
                                    <label class="required" style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Event
                                        Date</label>
                                    <input type="date" name="event_date" class="form-input" required
                                        min="<?php echo date('Y-m-d'); ?>"
                                        max="<?php echo date('Y-m-d', strtotime('+2 years')); ?>">
                                </div>
                                <div>
                                    <label class="required" style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start
                                        Time</label>
                                    <input type="time" name="event_time" class="form-input" required>
                                </div>
                                <div>
                                    <label class="required" style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End
                                        Time</label>
                                    <input type="time" name="event_end_time" class="form-input" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

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

                                <div class="ticket-type-options">
                                    <div class="ticket-type-option">
                                        <input type="radio" id="free-all" name="ticketType" value="free-all" checked>
                                        <label for="free-all">
                                            <i class="fas fa-gift ticket-icon" style="color: #10B981;"></i>
                                            Free for All
                                        </label>
                                    </div>
                                    <div class="ticket-type-option">
                                        <input type="radio" id="paid-all" name="ticketType" value="paid-all">
                                        <label for="paid-all">
                                            <i class="fas fa-credit-card ticket-icon" style="color: #F59E0B;"></i>
                                            Paid for All
                                        </label>
                                    </div>
                                    <div class="ticket-type-option">
                                        <input type="radio" id="mixed" name="ticketType" value="mixed">
                                        <label for="mixed">
                                            <i class="fas fa-university ticket-icon" style="color: #4A5BCC;"></i>
                                            Free for Uni Students + Paid for Others
                                        </label>
                                    </div>
                                </div>

                                <!-- Free for All Details -->
                                <div id="freeAllDetails" class="ticket-details">
                                    <div class="info-note" style="background: #f0fdf4; border-color: #10B981;">
                                        <i class="fas fa-gift"></i>
                                        Free registration for all attendees.
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Registration Limit (Optional)</label>
                                        <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Set a maximum number of registrations if needed</p>
                                        <input type="number" name="free_registration_limit" class="form-input" placeholder="Leave empty for unlimited registrations" min="1">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Registration Period (Optional)</label>
                                        <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Set registration period if needed. Leave empty to allow registration until event date</p>

                                        <div class="sale-dates">
                                            <div>
                                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Date</label>
                                                <input type="date" name="registration_start_date" class="form-input registration-start-date"
                                                    min="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                            <div>
                                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Time</label>
                                                <input type="time" name="registration_start_time" class="form-input">
                                            </div>
                                            <div>
                                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Date</label>
                                                <input type="date" name="registration_end_date" class="form-input registration-end-date"
                                                    min="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                            <div>
                                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Time</label>
                                                <input type="time" name="registration_end_time" class="form-input">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Paid for All Details -->
                                <div id="paidAllDetails" class="ticket-details hidden">
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
                                            <!-- Default ticket type -->
                                            <div class="ticket-type-item" data-ticket-id="1">
                                                <div class="ticket-type-header">
                                                    <input type="text" class="form-input ticket-type-name" placeholder="Ticket Type Name" style="max-width: 250px; margin-bottom: 0;">
                                                    <button type="button" class="remove-ticket-type-btn">×</button>
                                                </div>
                                                <div class="ticket-type-details">
                                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                                        <div>
                                                            <label class="form-label required">Quantity Available</label>
                                                            <input type="number" class="form-input ticket-quantity" placeholder="Enter quantity" min="1">
                                                        </div>
                                                        <div>
                                                            <label class="form-label required">Price (LKR)</label>
                                                            <input type="number" class="form-input ticket-price" placeholder="Enter price" min="0" step="0.01">
                                                        </div>
                                                    </div>

                                                    <!-- Discount Section for Ticket Type -->
                                                    <div class="ticket-discount-section">
                                                        <div class="toggle-container">
                                                            <span><i class="fas fa-tag" style="color: #FF6B35; margin-right: 8px;"></i> Discount for University Students?</span>
                                                            <label class="switch">
                                                                <input type="checkbox" class="discount-toggle">
                                                                <span class="slider"></span>
                                                            </label>
                                                        </div>

                                                        <div class="discount-details hidden">
                                                            <div class="info-note" style="background: #f0f9ff; border-color: #0ea5e9; margin-bottom: 15px;">
                                                                <i class="fas fa-info-circle"></i>
                                                                Discount will be applied to university students only
                                                            </div>

                                                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                                                <div>
                                                                    <label class="form-label">Discount Percentage</label>
                                                                    <input type="number" class="form-input discount-percent" placeholder="Enter discount %" min="0" max="100">
                                                                </div>
                                                                <div>
                                                                    <label class="form-label">Discounted Price</label>
                                                                    <input type="number" class="form-input discounted-price" placeholder="Calculated price" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label class="form-label">Description (Optional)</label>
                                                        <textarea class="form-textarea" placeholder="Describe this ticket type" style="min-height: 60px;"></textarea>
                                                    </div>
                                                </div>
                                            </div>
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
                                                    min="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                            <div>
                                                <label class="required" style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Time</label>
                                                <input type="time" name="sale_start_time" class="form-input">
                                            </div>
                                            <div>
                                                <label class="required" style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Date</label>
                                                <input type="date" name="sale_end_date" class="form-input sale-end-date"
                                                    min="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                            <div>
                                                <label class="required" style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Time</label>
                                                <input type="time" name="sale_end_time" class="form-input">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mixed (Free for Uni + Paid for Others) Details -->
                                <div id="mixedDetails" class="ticket-details hidden">
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
                                            <input type="number" name="mixed_registration_limit" class="form-input" placeholder="Leave empty for unlimited registrations" min="1">
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Registration Period</label>
                                            <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Registration period must be between today and event date</p>

                                            <div class="sale-dates">
                                                <div>
                                                    <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Date</label>
                                                    <input type="date" name="mixed_registration_start_date" class="form-input registration-start-date"
                                                        min="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                                <div>
                                                    <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Time</label>
                                                    <input type="time" name="mixed_registration_start_time" class="form-input">
                                                </div>
                                                <div>
                                                    <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Date</label>
                                                    <input type="date" name="mixed_registration_end_date" class="form-input registration-end-date"
                                                        min="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                                <div>
                                                    <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Time</label>
                                                    <input type="time" name="mixed_registration_end_time" class="form-input">
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
                                                <!-- Default ticket type -->
                                                <div class="ticket-type-item" data-ticket-id="1">
                                                    <div class="ticket-type-header">
                                                        <input type="text" class="form-input ticket-type-name" placeholder="Ticket Type Name" style="max-width: 250px; margin-bottom: 0;">
                                                        <button type="button" class="remove-ticket-type-btn">×</button>
                                                    </div>
                                                    <div class="ticket-type-details">
                                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                                            <div>
                                                                <label class="form-label required">Quantity Available</label>
                                                                <input type="number" class="form-input ticket-quantity" placeholder="Enter quantity" min="1">
                                                            </div>
                                                            <div>
                                                                <label class="form-label required">Price (LKR)</label>
                                                                <input type="number" class="form-input ticket-price" placeholder="Enter price" min="0" step="0.01">
                                                            </div>
                                                        </div>

                                                        <!-- Discount Section for Ticket Type -->
                                                        <div class="ticket-discount-section">
                                                            <div class="toggle-container">
                                                                <span><i class="fas fa-tag" style="color: #FF6B35; margin-right: 8px;"></i>Discount for Outside Users?</span>
                                                                <label class="switch">
                                                                    <input type="checkbox" class="discount-toggle">
                                                                    <span class="slider"></span>
                                                                </label>
                                                            </div>

                                                            <div class="discount-details hidden">
                                                                <div class="info-note" style="background: #f0f9ff; border-color: #0ea5e9; margin-bottom: 15px;">
                                                                    <i class="fas fa-info-circle"></i>
                                                                    Discount will be applied to Outside Users
                                                                </div>

                                                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                                                    <div>
                                                                        <label class="form-label">Discount Percentage</label>
                                                                        <input type="number" class="form-input discount-percent" placeholder="Enter discount %" min="0" max="100">
                                                                    </div>
                                                                    <div>
                                                                        <label class="form-label">Discounted Price</label>
                                                                        <input type="number" class="form-input discounted-price" placeholder="Calculated price" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <label class="form-label">Description (Optional)</label>
                                                            <textarea class="form-textarea" placeholder="Describe this ticket type" style="min-height: 60px;"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
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
                                                        min="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                                <div>
                                                    <label class="required" style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Time</label>
                                                    <input type="time" name="mixed_sale_start_time" class="form-input">
                                                </div>
                                                <div>
                                                    <label class="required" style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Date</label>
                                                    <input type="date" name="mixed_sale_end_date" class="form-input sale-end-date"
                                                        min="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                                <div>
                                                    <label class="required" style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Time</label>
                                                    <input type="time" name="mixed_sale_end_time" class="form-input">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="section" id="Request-Volunteer">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Request Volunteer</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">
                        <p style="color: #666; margin-bottom: 15px;">
                            Need volunteers for your event's success? Let us know.
                        </p>

                        <div class="form-group">
                            <label for="volunteerToggle" style="display: block; margin-bottom: 8px; color: #333;">
                                Do you want volunteers?
                            </label>

                            <label class="switch">
                                <input type="checkbox" id="volunteerToggle" name="volunteerToggle" value="1">
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div id="volunteerDetails" class="volunteer-details hidden" style="margin-top: 20px;">
                            <div class="info-note">
                                <i class="fas fa-hands-helping"></i>
                                Select where you'd like to recruit volunteers from
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Volunteer Source</label>
                                <div class="volunteer-source-options">
                                    <div class="volunteer-source-option">
                                        <input type="checkbox" id="faculty-volunteers" name="volunteer-source[]" value="faculty"
                                            checked>
                                        <label for="faculty-volunteers">
                                            <i class="fas fa-graduation-cap" style="color: #4A5BCC; font-size: 18px;"></i>
                                            From My Faculty
                                        </label>
                                    </div>
                                    <div class="volunteer-source-option">
                                        <input type="checkbox" id="university-volunteers" name="volunteer-source[]"
                                            value="university">
                                        <label for="university-volunteers">
                                            <i class="fas fa-university" style="color: #FF6B35; font-size: 18px;"></i>
                                            From My University
                                        </label>
                                    </div>
                                    <div class="volunteer-source-option">
                                        <input type="checkbox" id="public-volunteers" name="volunteer-source[]" value="public">
                                        <label for="public-volunteers">
                                            <i class="fas fa-users" style="color: #10B981; font-size: 18px;"></i>
                                            Public Users
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Number of Volunteers Needed</label>
                                <p style="font-size: 12px; color: #666; margin-bottom: 8px;">How many volunteers do you
                                    need?</p>
                                <input type="number" name="volunteers_needed" class="form-input"
                                    placeholder="e.g., 5" min="1" max="1000"
                                    style="max-width: 200px;">
                            </div>


                        </div>
                    </div>
                </section>

                <!-- doantion-->
                <section class="section" id="donation">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Donations</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">
                        <div class="form-group">
                            <label for="donationToggle" style="display: block; margin-bottom: 8px; color: #333;">
                                Do you want to allow participants to make donations to support this event? </label>

                            <label class="switch">
                                <input type="checkbox" id="donationToggle" name="donationToggle" value="1">
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div id="donationDetails" class="donation-details hidden" style="margin-top: 20px;">
                            <!-- Bank Details Section -->
                            <div class="info-note" style="background: #FEF3C7; border-left: 4px solid #F59E0B;">
                                <i class="fas fa-university"></i>
                                Provide your bank account details for participants to send donations
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Account Holder Name</label>
                                <input type="text" name="donation_account_name" class="form-input"
                                    placeholder="e.g., University Events Committee">
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Bank Name</label>
                                <select name="donation_bank_name" class="form-input">
                                    <option value="" disabled selected>Select Bank</option>
                                    <option value="Bank of Ceylon (BOC)">Bank of Ceylon (BOC)</option>
                                    <option value="Commercial Bank of Ceylon">Commercial Bank of Ceylon</option>
                                    <option value="Hatton National Bank (HNB)">Hatton National Bank (HNB)</option>
                                    <option value="Sampath Bank">Sampath Bank</option>
                                    <option value="Seylan Bank">Seylan Bank</option>
                                    <option value="People's Bank">People's Bank</option>
                                    <option value="National Development Bank (NDB)">National Development Bank (NDB)</option>
                                    <option value="Nations Trust Bank (NTB)">Nations Trust Bank (NTB)</option>
                                    <option value="Pan Asia Bank">Pan Asia Bank</option>
                                    <option value="Union Bank of Colombo">Union Bank of Colombo</option>
                                    <option value="DFCC Bank">DFCC Bank</option>
                                    <option value="Amana Bank">Amana Bank</option>
                                    <option value="Cargills Bank">Cargills Bank</option>
                                    <option value="Sanasa Development Bank (SDB)">Sanasa Development Bank (SDB)</option>
                                    <option value="Standard Chartered Bank">Standard Chartered Bank</option>
                                    <option value="HSBC Sri Lanka">HSBC Sri Lanka</option>
                                    <option value="Citibank Sri Lanka">Citibank Sri Lanka</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Branch Name</label>
                                <select name="donation_branch" class="form-input">
                                    <option value="" disabled selected>Select Branch</option>
                                    <option value="Colombo">Colombo</option>
                                    <option value="Kandy">Kandy</option>
                                    <option value="Galle">Galle</option>
                                    <option value="Ampara">Ampara</option>
                                    <option value="Anuradhapura">Anuradhapura</option>
                                    <option value="Badulla">Badulla</option>
                                    <option value="Batticaloa">Batticaloa</option>
                                    <option value="Gampaha">Gampaha</option>
                                    <option value="Hambantota">Hambantota</option>
                                    <option value="Jaffna">Jaffna</option>
                                    <option value="Kalutara">Kalutara</option>
                                    <option value="Kegalle">Kegalle</option>
                                    <option value="Kilinochchi">Kilinochchi</option>
                                    <option value="Kurunegala">Kurunegala</option>
                                    <option value="Mannar">Mannar</option>
                                    <option value="Matale">Matale</option>
                                    <option value="Matara">Matara</option>
                                    <option value="Monaragala">Monaragala</option>
                                    <option value="Mullaitivu">Mullaitivu</option>
                                    <option value="Nuwara Eliya">Nuwara Eliya</option>
                                    <option value="Polonnaruwa">Polonnaruwa</option>
                                    <option value="Puttalam">Puttalam</option>
                                    <option value="Ratnapura">Ratnapura</option>
                                    <option value="Trincomalee">Trincomalee</option>
                                    <option value="Vavuniya">Vavuniya</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Account Number</label>
                                <input type="text" name="donation_account_number" class="form-input"
                                    placeholder="e.g., 123456789">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Payment Instructions</label>
                                <textarea name="donation_instructions" class="form-textarea" rows="3"
                                    placeholder="Additional instructions for donors (e.g., 'Please include event name in payment reference')"></textarea>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Request Sponsorship -->
                <section class="section" id="sponsorship">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Request Sponsorship</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">
                        <p style="color: #666; margin-bottom: 15px;">
                            Looking for sponsors? Create sponsorship packages and receive funding through bank transfers.
                        </p>

                        <div class="form-group">
                            <label for="sponsorshipToggle" style="display: block; margin-bottom: 8px; color: #333;">
                                Do you want to request sponsorships?
                            </label>

                            <label class="switch">
                                <input type="checkbox" id="sponsorshipToggle" name="sponsorshipToggle" value="1">
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div id="sponsorshipDetails" class="sponsorship-details hidden" style="margin-top: 20px;">
                            <!-- Bank Details Section -->
                            <div class="info-note" style="background: #FEF3C7; border-left: 4px solid #F59E0B;">
                                <i class="fas fa-university"></i>
                                Provide your bank account details for sponsors to send payments
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Account Holder Name</label>
                                <input type="text" name="sponsorship_account_name" class="form-input"
                                    placeholder="e.g., University Events Committee">
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Bank Name</label>
                                <select name="sponsorship_bank_name" class="form-input">
                                    <option value="" disabled selected>Select Bank</option>
                                    <option value="Bank of Ceylon (BOC)">Bank of Ceylon (BOC)</option>
                                    <option value="Commercial Bank of Ceylon">Commercial Bank of Ceylon</option>
                                    <option value="Hatton National Bank (HNB)">Hatton National Bank (HNB)</option>
                                    <option value="Sampath Bank">Sampath Bank</option>
                                    <option value="Seylan Bank">Seylan Bank</option>
                                    <option value="People's Bank">People's Bank</option>
                                    <option value="National Development Bank (NDB)">National Development Bank (NDB)</option>
                                    <option value="Nations Trust Bank (NTB)">Nations Trust Bank (NTB)</option>
                                    <option value="Pan Asia Bank">Pan Asia Bank</option>
                                    <option value="Union Bank of Colombo">Union Bank of Colombo</option>
                                    <option value="DFCC Bank">DFCC Bank</option>
                                    <option value="Amana Bank">Amana Bank</option>
                                    <option value="Cargills Bank">Cargills Bank</option>
                                    <option value="Sanasa Development Bank (SDB)">Sanasa Development Bank (SDB)</option>
                                    <option value="Standard Chartered Bank">Standard Chartered Bank</option>
                                    <option value="HSBC Sri Lanka">HSBC Sri Lanka</option>
                                    <option value="Citibank Sri Lanka">Citibank Sri Lanka</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Branch Name</label>
                                <select name="sponsorship_branch" class="form-input">
                                    <option value="" disabled selected>Select Branch</option>
                                    <option value="Colombo">Colombo</option>
                                    <option value="Kandy">Kandy</option>
                                    <option value="Galle">Galle</option>
                                    <option value="Ampara">Ampara</option>
                                    <option value="Anuradhapura">Anuradhapura</option>
                                    <option value="Badulla">Badulla</option>
                                    <option value="Batticaloa">Batticaloa</option>
                                    <option value="Gampaha">Gampaha</option>
                                    <option value="Hambantota">Hambantota</option>
                                    <option value="Jaffna">Jaffna</option>
                                    <option value="Kalutara">Kalutara</option>
                                    <option value="Kegalle">Kegalle</option>
                                    <option value="Kilinochchi">Kilinochchi</option>
                                    <option value="Kurunegala">Kurunegala</option>
                                    <option value="Mannar">Mannar</option>
                                    <option value="Matale">Matale</option>
                                    <option value="Matara">Matara</option>
                                    <option value="Monaragala">Monaragala</option>
                                    <option value="Mullaitivu">Mullaitivu</option>
                                    <option value="Nuwara Eliya">Nuwara Eliya</option>
                                    <option value="Polonnaruwa">Polonnaruwa</option>
                                    <option value="Puttalam">Puttalam</option>
                                    <option value="Ratnapura">Ratnapura</option>
                                    <option value="Trincomalee">Trincomalee</option>
                                    <option value="Vavuniya">Vavuniya</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Account Number</label>
                                <input type="text" name="sponsorship_account_number" class="form-input"
                                    placeholder="e.g., 123456789">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Payment Instructions</label>
                                <textarea name="sponsorship_instructions" class="form-textarea" rows="3"
                                    placeholder="Additional instructions for sponsors (e.g., 'Please include event name in payment reference')"></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    Sponsorship Proposal Document
                                    <span style="color: #ef4444;">*</span>
                                </label>
                                <div class="file-upload-container">
                                    <input type="file"
                                        name="sponsorship_proposal"
                                        id="sponsorship_proposal"
                                        class="file-input"
                                        accept=".pdf,.doc,.docx,.ppt,.pptx"
                                        onchange="handleProposalFileSelect(event)">
                                    <label for="sponsorship_proposal" class="file-upload-label">
                                        <i class="fas fa-upload"></i>
                                        <span id="proposalFileName">Upload proposal document (PDF, DOC, PPT)</span>
                                    </label>
                                    <small class="helper-text">
                                        <strong>Required:</strong> Upload a detailed proposal document that sponsors can view before making a decision. Max size: 10MB
                                    </small>
                                </div>
                            </div>

                            <!-- Sponsorship Packages Section -->
                            <div class="info-note" style="margin-top: 30px;">
                                <i class="fas fa-gift"></i>
                                Create sponsorship packages with different levels and benefits
                            </div>

                            <div class="sponsorship-package-builder">
                                <h4 style="margin-bottom: 15px; font-size: 16px; color: #333;">
                                    <i class="fas fa-plus-circle" style="color: #4A5BCC; margin-right: 8px;"></i>
                                    Add Sponsorship Package
                                </h4>

                                <div class="package-inputs" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                    <div>
                                        <label class="form-label">Package Type</label>
                                        <select id="packageType" class="form-select">
                                            <option value="bronze">Bronze</option>
                                            <option value="silver">Silver</option>
                                            <option value="gold">Gold</option>
                                            <option value="platinum">Platinum</option>
                                            <option value="custom">Custom Package</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label">Package Name</label>
                                        <input type="text" id="packageName" class="form-input"
                                            placeholder="e.g., Bronze Sponsor">
                                    </div>
                                </div>

                                <div class="package-inputs" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px;">
                                    <div>
                                        <label class="form-label">Amount (LKR)</label>
                                        <input type="number" id="packageAmount" class="form-input"
                                            placeholder="e.g., 50000" min="0" step="100">
                                    </div>
                                    <div>
                                        <label class="form-label">Available Slots</label>
                                        <input type="number" id="packageSlots" class="form-input"
                                            placeholder="e.g., 5" min="1" value="1">
                                    </div>
                                </div>

                                <div class="form-group" style="margin-top: 15px;">
                                    <label class="form-label">Package Description</label>
                                    <textarea id="packageDescription" class="form-textarea" rows="2"
                                        placeholder="Brief description of this sponsorship package"></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Benefits & Perks</label>
                                    <textarea id="packageBenefits" class="form-textarea" rows="3"
                                        placeholder="List the benefits (e.g., Logo on event materials, Social media mentions, VIP seating)"></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Terms & Conditions</label>
                                    <textarea id="packageTerms" class="form-textarea" rows="2"
                                        placeholder="Any terms or conditions for this package (optional)"></textarea>
                                </div>

                                <button type="button" class="add-field-btn" onclick="addSponsorshipPackage()">
                                    <i class="fas fa-plus"></i> Add Package
                                </button>
                            </div>

                            <!-- Packages Preview -->
                            <div class="sponsorship-packages-preview" style="margin-top: 30px;">
                                <h4 style="margin-bottom: 15px; font-size: 16px; color: #333;">
                                    <i class="fas fa-gifts" style="color: #FF6B35; margin-right: 8px;"></i>
                                    Sponsorship Packages
                                </h4>
                                <div id="sponsorshipPackagesDisplay">
                                    <p style="color: #999; font-style: italic; text-align: center; padding: 20px;">
                                        No sponsorship packages added yet. Add packages above to see them here.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

<div class="bottom-actions">
                    <button type="button" class="cancel-btn" onclick="window.location.href='/unipulse/public/publisher/events'">Cancel</button>
                    <div class="action-buttons">
                        <button type="button" class="save-draft-btn" id="clearDraftBtn" style="background: #ffffff; display: none;">
                            <i class="fas fa-trash"></i> Clear Draft
                        </button>
                        <button type="submit" class="publish-btn">Publish Event</button>
                    </div>
                </div>
            </main>
        </form>
    </div>
    <?php include __DIR__ . '/../components/footer.php'; ?>
    <script src="/unipulse/public/assets/js/create-event-app.js"></script>

    <script src="/unipulse/public/assets/js/Publisher/createevent-app.js"></script>
</body>

</html>