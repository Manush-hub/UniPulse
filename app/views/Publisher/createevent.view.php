<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YourEvent - Create an event</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/create-event-style.css">
    <style>
        /* Dropdown with scroll - show 5 items */
        select[name="selected_university"],
        select[name="faculty_department"],
        select[name="event_category"],
        #fieldType {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }
        
        select[name="selected_university"] option,
        select[name="faculty_department"] option,
        select[name="event_category"] option,
        #fieldType option {
            padding: 10px;
        }
        
        /* Set size attribute to show 5 visible items when opened */
        select[name="selected_university"][size],
        select[name="faculty_department"][size],
        select[name="event_category"][size],
        #fieldType[size] {
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

    </style>

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
            <div class="sidebar-item" data-target="audience">Audience</div>
            <div class="sidebar-item" data-target="ticket">Ticket</div>
            <div class="sidebar-item" data-target="Request-Volunteer">Request Volunteer</div>
            <div class="sidebar-item" data-target="donation">Donations</div>
            <div class="sidebar-item" data-target="custom-fields">Custom Fields</div>

        </aside> -->
        
        <form action="/unipulse/public/publisher/createevent" method="POST" enctype="multipart/form-data" id="create-event">
            <input type="hidden" name="ajax" value="1" id="ajax-flag">
            <input type="hidden" name="ticket_types" id="ticket_types_input" value="">
            <input type="hidden" name="schedule" id="schedule_input" value="">
            <input type="hidden" name="custom_fields" id="custom_fields_input" value="">
            <input type="hidden" name="volunteer_positions" id="volunteer_positions_input" value="">
            
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
                                        <select name="selected_university" class="form-select" size="1">
                                            <option value="">Select University</option>
                                            <!-- State Universities (15) -->
                                            <option value="university-of-colombo">University of Colombo</option>
                                            <option value="university-of-peradeniya">University of Peradeniya</option>
                                            <option value="university-of-sri-jayewardenepura">University of Sri Jayewardenepura</option>
                                            <option value="university-of-kelaniya">University of Kelaniya</option>
                                            <option value="university-of-moratuwa">University of Moratuwa</option>
                                            <option value="university-of-jaffna">University of Jaffna</option>
                                            <option value="university-of-ruhuna">University of Ruhuna</option>
                                            <option value="eastern-university">Eastern University, Sri Lanka</option>
                                            <option value="south-eastern-university">South Eastern University of Sri Lanka</option>
                                            <option value="rajarata-university">Rajarata University of Sri Lanka</option>
                                            <option value="sabaragamuwa-university">Sabaragamuwa University of Sri Lanka</option>
                                            <option value="wayamba-university">Wayamba University of Sri Lanka</option>
                                            <option value="uva-wellassa-university">Uva Wellassa University</option>
                                            <option value="open-university">Open University of Sri Lanka</option>
                                            <option value="buddhist-and-pali-university">Buddhist and Pali University of Sri Lanka</option>
                                            <!-- Private Universities (5 Main) -->
                                            <option value="sliit">Sri Lanka Institute of Information Technology (SLIIT)</option>
                                            <option value="nsbm">NSBM Green University</option>
                                            <option value="cinec">CINEC Campus</option>
                                            <option value="apiit">Asia Pacific Institute of Information Technology (APIIT)</option>
                                            <option value="kiu">KIU (Kaatsu International University)</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="required"
                                            style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Faculty/Department</label>
                                        <select name="faculty_department" class="form-select" size="1">
                                            <option value="">Select Faculty/Department</option>
                                            <!-- Most Famous Faculties -->
                                            <option value="ucsc">University of Colombo School of Computing (UCSC)</option>
                                            <option value="faculty-of-engineering">Faculty of Engineering</option>
                                            <option value="faculty-of-medicine">Faculty of Medicine</option>
                                            <option value="faculty-of-science">Faculty of Science</option>
                                            <option value="faculty-of-management">Faculty of Management and Finance</option>
                                            <option value="faculty-of-arts">Faculty of Arts</option>
                                            <option value="faculty-of-law">Faculty of Law</option>
                                            <option value="faculty-of-information-technology">Faculty of Information Technology</option>
                                            <option value="faculty-of-applied-sciences">Faculty of Applied Sciences</option>
                                            <option value="faculty-of-agriculture">Faculty of Agriculture</option>
                                            <option value="faculty-of-architecture">Faculty of Architecture</option>
                                            <option value="faculty-of-education">Faculty of Education</option>
                                            <option value="faculty-of-social-sciences">Faculty of Social Sciences</option>
                                            <option value="faculty-of-allied-health-sciences">Faculty of Allied Health Sciences</option>
                                            <option value="faculty-of-dental-sciences">Faculty of Dental Sciences</option>
                                            <option value="other">Other</option>
                                        </select>
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
                                starts</p>

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
                            </div>
                        </div>
                    </div>
                </section>

                <section class="section" id="audience">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Audience</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">
                        <p style="color: #666; margin-bottom: 15px;">
                            Select who can view and attend your event
                        </p>

                        <div class="form-group">
                            <label class="form-label required">Target Audience</label>
                            <div class="audience-options">
                                <div class="audience-option">
                                    <input type="radio" id="university-students" name="audience" value="university-students"
                                        checked required>
                                    <label for="university-students">
                                        <i class="fas fa-graduation-cap" style="color: #4A5BCC; font-size: 18px;"></i>
                                        University Students
                                    </label>
                                </div>
                                <div class="audience-option">
                                    <input type="radio" id="public-users" name="audience" value="public-users" required>
                                    <label for="public-users">
                                        <i class="fas fa-users" style="color: #FF6B35; font-size: 18px;"></i>
                                        Public Users
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Additional Requirements</label>
                            <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Any specific requirements for
                                attendees (optional)</p>
                            <textarea name="requirements" class="form-textarea"
                                placeholder="e.g., Age restrictions, dress code, required documents, etc."
                                style="min-height: 80px;"></textarea>
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
                                                    <input type="text" class="form-input ticket-type-name" value="General Admission" placeholder="Ticket Type Name" style="max-width: 250px; margin-bottom: 0;">
                                                    <button type="button" class="remove-ticket-type-btn">×</button>
                                                </div>
                                                <div class="ticket-type-details">
                                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                                        <div>
                                                            <label class="form-label required">Quantity Available</label>
                                                            <input type="number" class="form-input ticket-quantity" value="100" placeholder="Enter quantity" min="1">
                                                        </div>
                                                        <div>
                                                            <label class="form-label required">Price (LKR)</label>
                                                            <input type="number" class="form-input ticket-price" value="10" placeholder="Enter price" min="0" step="0.01">
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
                                                        <input type="text" class="form-input ticket-type-name" value="General Admission" placeholder="Ticket Type Name" style="max-width: 250px; margin-bottom: 0;">
                                                        <button type="button" class="remove-ticket-type-btn">×</button>
                                                    </div>
                                                    <div class="ticket-type-details">
                                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                                            <div>
                                                                <label class="form-label required">Quantity Available</label>
                                                                <input type="number" class="form-input ticket-quantity" value="100" placeholder="Enter quantity" min="1">
                                                            </div>
                                                            <div>
                                                                <label class="form-label required">Price (LKR)</label>
                                                                <input type="number" class="form-input ticket-price" value="15" placeholder="Enter price" min="0" step="0.01">
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

                            <!-- <div class="form-group">
                                <label class="form-label">Volunteer Requirements</label>
                                <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Include positions when requesting a volunteer:</p>
                                <label for="newOption">Enter positions:</label>
                                <input type="text" class="form-input" id="newOption" placeholder="Type something...">
                                <button type="button" id="addPositionBtn" class="add-field-btn" style="margin-top: 10px;">+ Add Position</button>
                                <p><a href="volunteerreg.view.php">Go to volunteerreg Form</a></p>
                            </div> -->
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
                    </div>
                </section>

                <section class="section" id="custom-fields">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Custom Fields</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">
                        <p style="color: #666; margin-bottom: 15px;">
                            Add custom fields to collect additional information
                        </p>

                        <div class="custom-field-builder">
                            <h4 style="margin-bottom: 15px; font-size: 16px; color: #333;">
                                <i class="fas fa-plus-circle" style="color: #4A5BCC; margin-right: 8px;"></i>
                                Add Custom Field
                            </h4>

                            <div class="custom-field-inputs">
                                <div>
                                    <label class="form-label">Field Label</label>
                                    <input type="text" id="fieldLabel" class="form-input"
                                        placeholder="e.g., T-shirt Size, Dietary Requirements">
                                </div>
                                <div>
                                    <label class="form-label">Field Type</label>
                                    <select id="fieldType" class="form-select" size="1">
                                        <option value="text">Text Input</option>
                                        <option value="number">Number</option>
                                        <option value="email">Email</option>
                                        <option value="select">Dropdown</option>
                                        <option value="textarea">Text Area</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Options (for dropdown only)</label>
                                <input type="text" id="fieldOptions" class="form-input"
                                    placeholder="Enter options separated by commas (e.g., Small, Medium, Large, XL)">
                            </div>

                            <button type="button" class="add-field-btn" onclick="addCustomField()">
                                <i class="fas fa-plus"></i> Add Field
                            </button>
                        </div>

                        <div class="custom-fields-preview" id="customFieldsPreview">
                            <h4 style="margin-bottom: 15px; font-size: 16px; color: #333;">
                                <i class="fas fa-eye" style="color: #FF6B35; margin-right: 8px;"></i>
                                Custom Fields Preview
                            </h4>
                            <div id="dynamicFields">
                                <p style="color: #999; font-style: italic; text-align: center; padding: 20px;">
                                    No custom fields added yet. Add fields above to see them here.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="bottom-actions">
                    <button type="button" class="cancel-btn" onclick="window.location.href='/unipulse/public/publisher/events'">Cancel</button>
                    <div class="action-buttons">
                        <!-- <button type="button" class="save-draft-btn">Save draft</button> -->
                        <button type="submit" class="publish-btn">Publish Event</button>
                    </div>
                </div>
            </main>
        </form>
    </div>
    <?php include __DIR__ . '/../components/footer.php'; ?>
    <script src="/unipulse/public/assets/js/create-event-app.js"></script>
    
    <script>
    // Dropdown scroll functionality for university and faculty selects
    document.addEventListener('DOMContentLoaded', function() {
        const universitySelect = document.querySelector('select[name="selected_university"]');
        const facultySelect = document.querySelector('select[name="faculty_department"]');
        const categorySelect = document.querySelector('select[name="event_category"]');
        
        if (universitySelect) {
            universitySelect.addEventListener('focus', function() {
                this.size = 5;
            });
            
            universitySelect.addEventListener('blur', function() {
                this.size = 1;
            });
            
            universitySelect.addEventListener('change', function() {
                this.size = 1;
                this.blur();
            });
        }
        
        if (facultySelect) {
            facultySelect.addEventListener('focus', function() {
                this.size = 5;
            });
            
            facultySelect.addEventListener('blur', function() {
                this.size = 1;
            });
            
            facultySelect.addEventListener('change', function() {
                this.size = 1;
                this.blur();
            });
        }
        
        if (categorySelect) {
            categorySelect.addEventListener('focus', function() {
                this.size = 5;
            });
            
            categorySelect.addEventListener('blur', function() {
                this.size = 1;
            });
            
            categorySelect.addEventListener('change', function() {
                this.size = 1;
                this.blur();
            });
        }
        
        const fieldTypeSelect = document.getElementById('fieldType');
        if (fieldTypeSelect) {
            fieldTypeSelect.addEventListener('focus', function() {
                this.size = 5;
            });
            
            fieldTypeSelect.addEventListener('blur', function() {
                this.size = 1;
            });
            
            fieldTypeSelect.addEventListener('change', function() {
                this.size = 1;
                this.blur();
            });
        }

        // Handle visibility option selection styling
        const visibilityOptions = document.querySelectorAll('.visibility-option');
        const visibilityRadios = document.querySelectorAll('input[name="event_visibility"]');
        
        visibilityRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Reset all options
                visibilityOptions.forEach(option => {
                    option.style.borderColor = '#e5e7eb';
                    option.style.background = 'transparent';
                });
                
                // Highlight selected option
                if (this.checked) {
                    const parent = this.closest('.visibility-option');
                    parent.style.borderColor = '#3b82f6';
                    parent.style.background = '#eff6ff';
                }
            });
        });
        
        // Click on option div to select radio
        visibilityOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                if (e.target.type !== 'radio' && e.target.tagName !== 'LABEL') {
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                }
            });
        });
        
        // Set initial selected state
        const checkedRadio = document.querySelector('input[name="event_visibility"]:checked');
        if (checkedRadio) {
            const parent = checkedRadio.closest('.visibility-option');
            parent.style.borderColor = '#3b82f6';
            parent.style.background = '#eff6ff';
        }
    });

    // Function to show success message
    function showSuccessMessage(message) {
        // Remove any existing messages
        const existingMessage = document.querySelector('.success-message, .error-message');
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
        const form = document.getElementById('create-event');
        form.insertBefore(successDiv, form.firstChild);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (successDiv.parentElement) {
                successDiv.remove();
            }
        }, 5000);
    }
    
    // Function to show error message
    function showErrorMessage(message, errors = null) {
        // Remove any existing messages
        const existingMessage = document.querySelector('.success-message, .error-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        // Create error message element
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.cssText = `
            background: #f44336;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            animation: slideDown 0.3s ease-out;
        `;
        
        let errorContent = `<strong>✗ ${message}</strong>`;
        
        if (errors && typeof errors === 'object') {
            errorContent += '<ul style="margin: 10px 0 0 20px; text-align: left;">';
            for (const [field, errorMsg] of Object.entries(errors)) {
                errorContent += `<li>${errorMsg}</li>`;
            }
            errorContent += '</ul>';
        }
        
        errorContent += `
            <button onclick="this.parentElement.remove()" style="
                background: none; 
                border: none; 
                color: white; 
                float: right; 
                cursor: pointer; 
                font-size: 18px;
                margin-top: -2px;
                position: absolute;
                right: 15px;
                top: 15px;
            ">×</button>
        `;
        
        errorDiv.innerHTML = errorContent;
        
        // Insert at the top of the form
        const form = document.getElementById('create-event');
        form.insertBefore(errorDiv, form.firstChild);
        
        // Scroll to top to show error
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    // Form Validation Functions
    function validateForm() {
        const errors = [];
        
        // Validate Event Name
        const eventName = document.querySelector('input[name="event_name"]').value.trim();
        if (!eventName) {
            errors.push('Event name is required');
        } else if (eventName.length < 3) {
            errors.push('Event name must be at least 3 characters long');
        } else if (eventName.length > 200) {
            errors.push('Event name must be less than 200 characters');
        }
        
        // Validate Event Description
        const eventDescription = document.querySelector('textarea[name="event_description"]').value.trim();
        if (!eventDescription) {
            errors.push('Event description is required');
        } else if (eventDescription.length < 10) {
            errors.push('Event description must be at least 10 characters long');
        } else if (eventDescription.length > 5000) {
            errors.push('Event description must be less than 5000 characters');
        }
        
        // Validate Category
        const category = document.querySelector('select[name="event_category"]').value;
        if (!category) {
            errors.push('Event category is required');
        }
        
        // Validate Cover Image
        const coverImage = document.querySelector('input[name="cover_image"]');
        if (!coverImage.files || coverImage.files.length === 0) {
            errors.push('Event cover image is required');
        } else {
            const file = coverImage.files[0];
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            const maxSize = 5 * 1024 * 1024; // 5MB
            
            if (!allowedTypes.includes(file.type)) {
                errors.push('Cover image must be JPEG, PNG, GIF, or WebP');
            }
            
            if (file.size > maxSize) {
                errors.push('Cover image size must not exceed 5MB');
            }
        }
        
        // Validate Event Date
        const eventDate = document.querySelector('input[name="event_date"]').value;
        if (!eventDate) {
            errors.push('Event date is required');
        } else {
            const selectedDate = new Date(eventDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                errors.push('Event date cannot be in the past');
            }
            
            // Check if date is too far in the future (e.g., more than 2 years)
            const twoYearsFromNow = new Date();
            twoYearsFromNow.setFullYear(twoYearsFromNow.getFullYear() + 2);
            if (selectedDate > twoYearsFromNow) {
                errors.push('Event date cannot be more than 2 years in the future');
            }
        }
        
        // Validate Event Time
        const eventTime = document.querySelector('input[name="event_time"]').value;
        if (!eventTime) {
            errors.push('Event time is required');
        } else if (eventDate) {
            // If event is today, check if time is in the future
            const selectedDate = new Date(eventDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate.getTime() === today.getTime()) {
                const [hours, minutes] = eventTime.split(':');
                const eventDateTime = new Date();
                eventDateTime.setHours(parseInt(hours), parseInt(minutes), 0, 0);
                
                if (eventDateTime < new Date()) {
                    errors.push('Event time cannot be in the past for today\'s events');
                }
            }
        }
        
        // Validate Location
        const locationType = document.querySelector('input[name="location-type"]:checked')?.value || 'inside-university';
        const eventLocation = document.querySelector('input[name="event_location"]').value.trim();
        
        if (locationType === 'inside-university') {
            // Validate University
            const university = document.querySelector('select[name="selected_university"]').value;
            if (!university) {
                errors.push('University selection is required for inside university events');
            }
            
            // Validate Faculty/Department
            const faculty = document.querySelector('select[name="faculty_department"]').value;
            if (!faculty) {
                errors.push('Faculty/Department is required for inside university events');
            }
            
            // Validate Event Location
            if (!eventLocation) {
                errors.push('Event location is required (e.g., Main Auditorium, Hall A)');
            }
        } else {
            const venueName = document.querySelector('input[name="venue_name"]').value.trim();
            const city = document.querySelector('input[name="city"]').value.trim();
            
            if (!venueName) {
                errors.push('Venue name is required for outside university events');
            }
            if (!city) {
                errors.push('City is required for outside university events');
            }
        }
        
        // Validate Target Audience
        const audience = document.querySelector('input[name="audience"]:checked');
        if (!audience) {
            errors.push('Target audience is required');
        }
        
        // Validate Registration/Sale Dates based on ticket type
        const ticketType = document.querySelector('input[name="ticketType"]:checked')?.value || 'free-all';
        
        if (ticketType === 'free-all') {
            // For free-all, registration period is optional
            // Only validate if any registration date field is filled
            const regStartDate = document.querySelector('input[name="registration_start_date"]')?.value;
            const regEndDate = document.querySelector('input[name="registration_end_date"]')?.value;
            
            if (regStartDate || regEndDate) {
                // If any field is filled, validate the registration period
                validateRegistrationPeriod(errors, eventDate, '#freeAllDetails');
            }
        } else if (ticketType === 'paid-all') {
            // Check if paid-all details section is visible
            const paidAllDetails = document.getElementById('paidAllDetails');
            if (!paidAllDetails || !paidAllDetails.classList.contains('hidden')) {
                // Validate ticket types
                validateTicketTypes(errors, '#ticketTypesList');
                
                // Require sale period
                const saleStartDate = document.querySelector('input[name="sale_start_date"]');
                const saleStartTime = document.querySelector('input[name="sale_start_time"]');
                const saleEndDate = document.querySelector('input[name="sale_end_date"]');
                const saleEndTime = document.querySelector('input[name="sale_end_time"]');
                
                if (!saleStartDate || !saleStartDate.value) {
                    errors.push('Sale start date is required for paid events');
                }
                if (!saleStartTime || !saleStartTime.value) {
                    errors.push('Sale start time is required for paid events');
                }
                if (!saleEndDate || !saleEndDate.value) {
                    errors.push('Sale end date is required for paid events');
                }
                if (!saleEndTime || !saleEndTime.value) {
                    errors.push('Sale end time is required for paid events');
                }
                
                validateSalePeriod(errors, eventDate, '#paidAllDetails');
            }
        } else if (ticketType === 'mixed') {
            // Check if mixed details section is visible
            const mixedDetails = document.getElementById('mixedDetails');
            if (!mixedDetails || !mixedDetails.classList.contains('hidden')) {
                // Registration period for university students is optional
                validateRegistrationPeriod(errors, eventDate, '#mixedDetails');
                
                // Validate sale period for outside users - REQUIRED
                const saleStartDate = document.querySelector('input[name="mixed_sale_start_date"]');
                const saleStartTime = document.querySelector('input[name="mixed_sale_start_time"]');
                const saleEndDate = document.querySelector('input[name="mixed_sale_end_date"]');
                const saleEndTime = document.querySelector('input[name="mixed_sale_end_time"]');
                
                if (!saleStartDate || !saleStartDate.value) {
                    errors.push('Sale start date is required for outside users');
                }
                if (!saleStartTime || !saleStartTime.value) {
                    errors.push('Sale start time is required for outside users');
                }
                if (!saleEndDate || !saleEndDate.value) {
                    errors.push('Sale end date is required for outside users');
                }
                if (!saleEndTime || !saleEndTime.value) {
                    errors.push('Sale end time is required for outside users');
                }
                
                validateSalePeriod(errors, eventDate, '#mixedDetails');
                validateTicketTypes(errors, '#mixedTicketTypesList');
            }
        }
        
        // Validate Volunteers
        const needsVolunteers = document.getElementById('volunteerToggle').checked;
        console.log('Volunteer validation - needsVolunteers:', needsVolunteers);
        
        if (needsVolunteers) {
            const volunteersNeeded = document.querySelector('input[name="volunteers_needed"]').value;
            console.log('volunteersNeeded:', volunteersNeeded);
            
            if (!volunteersNeeded) {
                errors.push('Number of volunteers needed is required');
            } else if (parseInt(volunteersNeeded) < 1) {
                errors.push('Number of volunteers must be at least 1');
            } else if (parseInt(volunteersNeeded) > 1000) {
                errors.push('Number of volunteers cannot exceed 1,000');
            }
            
            // Check if at least one volunteer source is selected
            const volunteerSources = document.querySelectorAll('input[name="volunteer-source[]"]:checked');
            console.log('volunteerSources found:', volunteerSources.length);
            volunteerSources.forEach((source, index) => {
                console.log(`Source ${index}:`, source.value, source.checked);
            });
            
            if (volunteerSources.length === 0) {
                errors.push('Please select at least one volunteer source');
            }
        }
        
        // Show errors if any
        if (errors.length > 0) {
            const errorObj = {};
            errors.forEach((error, index) => {
                errorObj[`error${index + 1}`] = error;
            });
            showErrorMessage('Please fix the following validation errors:', errorObj);
            return false;
        }
        
        return true;
    }
    
    function validateRegistrationPeriod(errors, eventDate, containerSelector) {
        const container = document.querySelector(containerSelector);
        if (!container || container.classList.contains('hidden')) return;
        
        // Get date inputs properly
        const dateInputs = container.querySelectorAll('input[type="date"]');
        const startDate = dateInputs[0]?.value;
        const endDate = dateInputs[1]?.value;
        
        // Validate if only end date is provided without start date
        if (endDate && !startDate) {
            errors.push('Please provide a registration start date');
            return;
        }
        
        if (startDate && eventDate) {
            const regStart = new Date(startDate);
            const event = new Date(eventDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            // Registration start cannot be in the past
            if (regStart < today) {
                errors.push('Registration start date cannot be in the past (must be today or later)');
            }
            
            // Registration start cannot be after event date
            if (regStart > event) {
                errors.push('Registration start date cannot be after the event date');
            }
        }
        
        // Validate registration end date
        if (endDate && startDate) {
            const regEnd = new Date(endDate);
            const regStart = new Date(startDate);
            const event = new Date(eventDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            // End date must be after start date
            if (regEnd < regStart) {
                errors.push('Registration end date must be after start date');
            }
            
            // End date cannot be after event date
            if (regEnd > event) {
                errors.push('Registration period must close before or on the event date');
            }
            
            // End date cannot be in the past
            if (regEnd < today) {
                errors.push('Registration end date cannot be in the past');
            }
        }
        
        // If both dates are provided, validate the complete period
        if (startDate && endDate && eventDate) {
            const regStart = new Date(startDate);
            const regEnd = new Date(endDate);
            const event = new Date(eventDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            // Registration period must be between today and event date
            if (regStart < today || regEnd > event) {
                errors.push('Registration period must be between today and the event date');
            }
        }
    }
    
    function validateSalePeriod(errors, eventDate, containerSelector) {
        const container = document.querySelector(containerSelector);
        if (!container || container.classList.contains('hidden')) return;
        
        const saleDates = container.querySelectorAll('.sale-dates input[type="date"]');
        if (saleDates.length >= 2) {
            const startDate = saleDates[0].value;
            const endDate = saleDates[1].value;
            
            if (startDate && endDate) {
                const saleStart = new Date(startDate);
                const saleEnd = new Date(endDate);
                const event = new Date(eventDate);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                // Sale start cannot be in the past
                if (saleStart < today) {
                    errors.push('Ticket sale start date cannot be in the past (must be today or later)');
                }
                
                // Sale start must be before sale end
                if (saleStart > saleEnd) {
                    errors.push('Ticket sale start date must be before end date');
                }
                
                // Sale end cannot be after event date
                if (saleEnd > event) {
                    errors.push('Ticket sale must end before or on the event date');
                }
                
                // Complete validation: sale period between today and event
                if (saleStart < today || saleEnd > event) {
                    errors.push('Ticket sale period must be between today and the event date');
                }
            }
        }
    }
    
    function validateTicketTypes(errors, containerSelector) {
        const container = document.querySelector(containerSelector);
        if (!container) return;
        
        const ticketItems = container.querySelectorAll('.ticket-type-item');
        
        if (ticketItems.length === 0) {
            errors.push('At least one ticket type is required for paid events');
            return;
        }
        
        ticketItems.forEach((item, index) => {
            const typeName = item.querySelector('.ticket-type-name')?.value.trim();
            const quantity = item.querySelector('.ticket-quantity')?.value;
            const price = item.querySelector('.ticket-price')?.value;
            
            if (!typeName) {
                errors.push(`Ticket type ${index + 1}: Name is required`);
            }
            
            if (!quantity || parseInt(quantity) < 1) {
                errors.push(`Ticket type ${index + 1}: Quantity must be at least 1`);
            } else if (parseInt(quantity) > 100000) {
                errors.push(`Ticket type ${index + 1}: Quantity cannot exceed 100,000`);
            }
            
            if (!price || parseFloat(price) < 0) {
                errors.push(`Ticket type ${index + 1}: Price must be 0 or greater`);
            } else if (parseFloat(price) > 1000000) {
                errors.push(`Ticket type ${index + 1}: Price cannot exceed 1,000,000`);
            }
            
            // Validate discount if enabled
            const discountToggle = item.querySelector('.discount-toggle');
            if (discountToggle && discountToggle.checked) {
                const discountPercent = item.querySelector('.discount-percent')?.value;
                if (!discountPercent || parseFloat(discountPercent) < 0 || parseFloat(discountPercent) > 100) {
                    errors.push(`Ticket type ${index + 1}: Discount percentage must be between 0 and 100`);
                }
            }
        });
    }
    
    // Real-time validation helpers
    function setupRealtimeValidation() {
        // Event Date validation
        const eventDateInput = document.querySelector('input[name="event_date"]');
        if (eventDateInput) {
            eventDateInput.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (selectedDate < today) {
                    this.style.borderColor = '#dc3545';
                    showTooltip(this, 'Event date cannot be in the past');
                } else {
                    this.style.borderColor = '#10B981';
                    removeTooltip(this);
                }
                
                // Update max date for registration and sale periods
                updateRegistrationAndSalePeriodLimits(this.value);
            });
        }
        
        // Event Time validation
        const eventTimeInput = document.querySelector('input[name="event_time"]');
        if (eventTimeInput && eventDateInput) {
            eventTimeInput.addEventListener('change', function() {
                const eventDate = eventDateInput.value;
                if (!eventDate) return;
                
                const selectedDate = new Date(eventDate);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (selectedDate.getTime() === today.getTime()) {
                    const [hours, minutes] = this.value.split(':');
                    const eventDateTime = new Date();
                    eventDateTime.setHours(parseInt(hours), parseInt(minutes), 0, 0);
                    
                    if (eventDateTime < new Date()) {
                        this.style.borderColor = '#dc3545';
                        showTooltip(this, 'Event time cannot be in the past');
                    } else {
                        this.style.borderColor = '#10B981';
                        removeTooltip(this);
                    }
                }
            });
        }
        
        // Max Participants validation
        const maxParticipantsInput = document.querySelector('input[name="max_participants"]');
        if (maxParticipantsInput) {
            maxParticipantsInput.addEventListener('input', function() {
                const value = parseInt(this.value);
                if (value < 1 || value > 100000) {
                    this.style.borderColor = '#dc3545';
                } else {
                    this.style.borderColor = '#10B981';
                }
            });
        }
    }
    
    // Update registration and sale period date limits based on event date
    function updateRegistrationAndSalePeriodLimits(eventDate) {
        if (!eventDate) return;
        
        const today = new Date().toISOString().split('T')[0];
        
        // Update all registration date inputs
        const registrationStartDates = document.querySelectorAll('.registration-start-date');
        const registrationEndDates = document.querySelectorAll('.registration-end-date');
        
        registrationStartDates.forEach(input => {
            input.setAttribute('min', today);
            input.setAttribute('max', eventDate);
        });
        
        registrationEndDates.forEach(input => {
            input.setAttribute('min', today);
            input.setAttribute('max', eventDate);
        });
        
        // Update all sale date inputs
        const saleStartDates = document.querySelectorAll('.sale-start-date');
        const saleEndDates = document.querySelectorAll('.sale-end-date');
        
        saleStartDates.forEach(input => {
            input.setAttribute('min', today);
            input.setAttribute('max', eventDate);
        });
        
        saleEndDates.forEach(input => {
            input.setAttribute('min', today);
            input.setAttribute('max', eventDate);
        });
    }
    
    function showTooltip(element, message) {
        removeTooltip(element);
        const tooltip = document.createElement('div');
        tooltip.className = 'validation-tooltip';
        tooltip.textContent = message;
        tooltip.style.cssText = `
            position: absolute;
            background: #dc3545;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            margin-top: 5px;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        `;
        element.parentElement.style.position = 'relative';
        element.parentElement.appendChild(tooltip);
    }
    
    function removeTooltip(element) {
        const tooltip = element.parentElement.querySelector('.validation-tooltip');
        if (tooltip) {
            tooltip.remove();
        }
    }
    
    // Initialize real-time validation when page loads
    document.addEventListener('DOMContentLoaded', function() {
        setupRealtimeValidation();
    });
    
    // Collect and store all dynamic form data
    function collectAndStoreFormData() {
        // Collect ticket types based on ticket type selected
        const ticketType = document.querySelector('input[name="ticketType"]:checked')?.value || 'free-all';
        let ticketTypes = [];
        
        if (ticketType === 'paid-all') {
            // Collect from paid-all ticket types list
            const ticketCards = document.querySelectorAll('#ticketTypesList .ticket-type-item');
            ticketCards.forEach(card => {
                const name = card.querySelector('.ticket-type-name')?.value || '';
                const price = card.querySelector('.ticket-price')?.value || '0';
                const quantity = card.querySelector('.ticket-quantity')?.value || '0';
                const description = card.querySelector('textarea')?.value || '';
                const discountPercent = card.querySelector('.discount-percent')?.value || '';
                const discountedPrice = card.querySelector('.discounted-price')?.value || '';
                
                if (name && price && quantity) {
                    const ticketData = {
                        name: name,
                        price: parseFloat(price),
                        quantity: parseInt(quantity),
                        description: description
                    };
                    
                    // Add discount info if provided
                    if (discountPercent && discountedPrice) {
                        ticketData.discount_percent = parseFloat(discountPercent);
                        ticketData.discounted_price = parseFloat(discountedPrice);
                    }
                    
                    ticketTypes.push(ticketData);
                }
            });
        } else if (ticketType === 'mixed') {
            // Collect from mixed ticket types list
            const ticketCards = document.querySelectorAll('#mixedTicketTypesList .ticket-type-item');
            ticketCards.forEach(card => {
                const name = card.querySelector('.ticket-type-name')?.value || '';
                const price = card.querySelector('.ticket-price')?.value || '0';
                const quantity = card.querySelector('.ticket-quantity')?.value || '0';
                const description = card.querySelector('textarea')?.value || '';
                const discountPercent = card.querySelector('.discount-percent')?.value || '';
                const discountedPrice = card.querySelector('.discounted-price')?.value || '';
                
                if (name && price && quantity) {
                    const ticketData = {
                        name: name,
                        price: parseFloat(price),
                        quantity: parseInt(quantity),
                        description: description
                    };
                    
                    // Add discount info if provided
                    if (discountPercent && discountedPrice) {
                        ticketData.discount_percent = parseFloat(discountPercent);
                        ticketData.discounted_price = parseFloat(discountedPrice);
                    }
                    
                    ticketTypes.push(ticketData);
                }
            });
        }
        
        // Store ticket types in hidden input
        document.getElementById('ticket_types_input').value = JSON.stringify(ticketTypes);
        
        // Collect schedule
        const scheduleItems = [];
        document.querySelectorAll('#scheduleList .schedule-item-card').forEach(item => {
            const time = item.querySelector('[data-schedule-time]')?.textContent || '';
            const activity = item.querySelector('[data-schedule-activity]')?.textContent || '';
            if (time && activity) {
                scheduleItems.push({ time, activity });
            }
        });
        document.getElementById('schedule_input').value = JSON.stringify(scheduleItems);
        
        // Collect custom fields
        const customFields = [];
        document.querySelectorAll('#customFieldsList .custom-field-item').forEach(field => {
            const label = field.querySelector('[data-field-label]')?.textContent || '';
            const type = field.querySelector('[data-field-type]')?.textContent || '';
            if (label && type) {
                customFields.push({ label, type });
            }
        });
        document.getElementById('custom_fields_input').value = JSON.stringify(customFields);
        
        // Collect volunteer positions
        const volunteerPositions = [];
        document.querySelectorAll('#volunteerPositionsList .position-item').forEach(position => {
            const text = position.querySelector('span')?.textContent || position.textContent.replace('×', '').trim();
            if (text) {
                volunteerPositions.push(text);
            }
        });
        document.getElementById('volunteer_positions_input').value = JSON.stringify(volunteerPositions);
        
        console.log('Collected data:', {
            ticketTypes: ticketTypes,
            schedule: scheduleItems,
            customFields: customFields,
            volunteerPositions: volunteerPositions
        });
    }
    
    // Handle form submission
    document.getElementById('create-event').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get ticket type
        const ticketType = document.querySelector('input[name="ticketType"]:checked')?.value || 'free-all';
        
        // Create a hidden input for registration_limit if it doesn't exist
        let registrationLimitInput = this.querySelector('input[name="registration_limit"]');
        if (!registrationLimitInput) {
            registrationLimitInput = document.createElement('input');
            registrationLimitInput.type = 'hidden';
            registrationLimitInput.name = 'registration_limit';
            this.appendChild(registrationLimitInput);
        }
        
        // Set the value based on ticket type
        if (ticketType === 'free-all') {
            const freeLimit = document.querySelector('input[name="free_registration_limit"]')?.value;
            registrationLimitInput.value = freeLimit || '';
        } else if (ticketType === 'mixed') {
            const mixedLimit = document.querySelector('input[name="mixed_registration_limit"]')?.value;
            registrationLimitInput.value = mixedLimit || '';
        } else {
            registrationLimitInput.value = '';
        }
        
        // Collect all dynamic data before validation
        collectAndStoreFormData();
        
        // Validate form before submission
        if (!validateForm()) {
            return;
        }
        
        const submitBtn = document.querySelector('.publish-btn');
        const originalText = submitBtn.textContent;
        
        // Show loading state
        submitBtn.textContent = 'Creating Event...';
        submitBtn.disabled = true;
        
        // Get form data
        const formData = new FormData(this);
        
        // Ensure AJAX detection
        formData.set('ajax', '1');
        
        // Submit form
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
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
                // Show success message
                showSuccessMessage('Event created successfully! Redirecting to events page...');
                
                // Redirect to events page after a short delay
                setTimeout(() => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.href = '/unipulse/public/publisher/events';
                    }
                }, 1500); // 1.5 second delay to show success message
            } else {
                // Show error messages in styled message box
                const errorMsg = data.message || 'Failed to create event. Please check the errors below.';
                showErrorMessage(errorMsg, data.errors);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage('Network or server error occurred', { 'error': error.message });
        })
        .finally(() => {
            // Reset button
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    });
    </script>
</body>

</html>