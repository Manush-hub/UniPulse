<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YourEvent - Create an event</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/create-event-style.css">

</head>

<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="nav-brand">
                <div class="logo">
                    <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="unp-logo">
                </div>
            </div>
            <div class="unp-nav-group">
                <a href="#features" class="unp-nav-link">Home</a>
                <a href="#users" class="unp-nav-link">Upcoming Events</a>
                <a href="#events" class="unp-nav-link">My Events</a>
            </div>
            <div class="header-right">
                <div class="profile">
                    <div class="profile-avatar">JK</div>
                    <span>Jennifer King</span>
                </div>
            </div>
        </nav>
    </header>

    <div class="main-container">
        <aside class="sidebar">
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
            <div class="sidebar-item" data-target="custom-fields">Custom Fields</div>
            <div class="sidebar-item" data-target="Request-Volunteer">Request Volunteer</div>
            <div class="sidebar-item" data-target="donation">Donations</div>

        </aside>
        <form action="" id="create-event">
            <main class="content">
                <h2 style="margin-bottom: 30px;">Create an event</h2>

                <section class="section" id="upload-cover">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Upload cover</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">
                        <p style="color: #666; margin-bottom: 15px;">Upload the event cover to capture your audience's
                            attention</p>

                        <div class="cover-image" id="coverImageContainer">
                            <div class="cover-image-placeholder">
                                <div>
                                    <div>Rock Revolt</div>
                                    <div style="font-size: 32px; margin: 10px 0;">Power</div>
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
                                <input type="file" id="coverFileInput" class="file-input" accept="image/*">
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
                            <input type="text" class="form-input" value="Rock Revolt: A Fusion of Power and Passion"
                                placeholder="Enter event name">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Provide essential event details</p>
                            <textarea class="form-textarea" placeholder="Enter event description"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Select the category for your event
                            </p>
                            <select class="form-select">
                                <option>Music</option>
                                <option>Sports</option>
                                <option>Business</option>
                                <option>Technology</option>
                            </select>
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
                                        <label
                                            style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">University</label>
                                        <select class="form-select">
                                            <option value="">Select University</option>
                                            <option value="university-of-colombo">University of Colombo</option>
                                            <option value="university-of-moratuwa">University of Moratuwa</option>
                                            <option value="university-of-peradeniya">University of Peradeniya</option>
                                            <option value="university-of-sri-jayewardenepura">University of Sri
                                                Jayewardenepura</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Faculty/Department</label>
                                        <input type="text" class="form-input"
                                            placeholder="e.g., Faculty of Engineering, Department of Computer Science">
                                    </div>
                                </div>

                                <div>
                                    <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Exact
                                        Location</label>
                                    <input type="text" class="form-input"
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
                                        <label
                                            style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Venue
                                            Name</label>
                                        <input type="text" class="form-input" placeholder="e.g., Colombo Convention Center">
                                    </div>
                                    <div>
                                        <label
                                            style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Street
                                            Address</label>
                                        <input type="text" class="form-input" placeholder="Enter street address">
                                    </div>
                                    <div>
                                        <label
                                            style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">City</label>
                                        <input type="text" class="form-input" placeholder="Enter city">
                                    </div>
                                    <div>
                                        <label
                                            style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">District/Province</label>
                                        <input type="text" class="form-input" placeholder="Enter district or province">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Time</label>
                            <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Let attendees know when your event
                                starts</p>

                            <div class="time-inputs">
                                <div>
                                    <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Event
                                        Date</label>
                                    <input type="date" class="form-input" value="2023-06-10">
                                </div>
                                <div>
                                    <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start
                                        Time</label>
                                    <input type="time" class="form-input" value="19:00">
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
                            <label class="form-label">Target Audience</label>
                            <div class="audience-options">
                                <div class="audience-option">
                                    <input type="radio" id="university-students" name="audience" value="university-students"
                                        checked>
                                    <label for="university-students">
                                        <i class="fas fa-graduation-cap" style="color: #4A5BCC; font-size: 18px;"></i>
                                        University Students
                                    </label>
                                </div>
                                <div class="audience-option">
                                    <input type="radio" id="public-users" name="audience" value="public-users">
                                    <label for="public-users">
                                        <i class="fas fa-users" style="color: #FF6B35; font-size: 18px;"></i>
                                        Public Users
                                    </label>
                                </div>
                                <div class="audience-option">
                                    <input type="radio" id="both-audiences" name="audience" value="both">
                                    <label for="both-audiences">
                                        <i class="fas fa-globe" style="color: #10B981; font-size: 18px;"></i>
                                        Both
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Additional Requirements</label>
                            <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Any specific requirements for
                                attendees (optional)</p>
                            <textarea class="form-textarea"
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
                                        <input type="radio" id="free-all" name="ticketType" value="free-all">
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
                                        <input type="radio" id="mixed" name="ticketType" value="mixed" checked>
                                        <label for="mixed">
                                            <i class="fas fa-university ticket-icon" style="color: #4A5BCC;"></i>
                                            Free for Uni Students + Paid for Others
                                        </label>
                                    </div>
                                </div>

                                <!-- Free for All Details -->
                                <div id="freeAllDetails" class="ticket-details hidden">
                                    <div class="info-note" style="background: #f0fdf4; border-color: #10B981;">
                                        <i class="fas fa-gift"></i>
                                        Free registration for all attendees.
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Registration Limit (Optional)</label>
                                        <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Set a maximum number of registrations if needed</p>
                                        <input type="number" class="form-input" placeholder="Leave empty for unlimited registrations" min="1">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Registration Period</label>
                                        <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Set when registration opens and closes</p>

                                        <div class="sale-dates">
                                            <div>
                                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Date</label>
                                                <input type="date" class="form-input" value="2023-06-05">
                                            </div>
                                            <div>
                                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Time</label>
                                                <input type="time" class="form-input" value="09:00">
                                            </div>
                                            <div>
                                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Date</label>
                                                <input type="date" class="form-input" value="2023-06-09">
                                            </div>
                                            <div>
                                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Time</label>
                                                <input type="time" class="form-input" value="18:00">
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
                                            Ticket Types
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
                                                            <label class="form-label">Quantity Available</label>
                                                            <input type="number" class="form-input ticket-quantity" value="100" placeholder="Enter quantity" min="1">
                                                        </div>
                                                        <div>
                                                            <label class="form-label">Price (LKR)</label>
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
                                        <label class="form-label">Sale Period</label>
                                        <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Set when tickets will be available for purchase</p>

                                        <div class="sale-dates">
                                            <div>
                                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Date</label>
                                                <input type="date" class="form-input" value="2023-06-05">
                                            </div>
                                            <div>
                                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Time</label>
                                                <input type="time" class="form-input" value="09:00">
                                            </div>
                                            <div>
                                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Date</label>
                                                <input type="date" class="form-input" value="2023-06-09">
                                            </div>
                                            <div>
                                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Time</label>
                                                <input type="time" class="form-input" value="18:00">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mixed (Free for Uni + Paid for Others) Details -->
                                <div id="mixedDetails" class="ticket-details">
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
                                            <input type="number" class="form-input" placeholder="Leave empty for unlimited registrations" min="1">
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Registration Period</label>
                                            <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Set when registration opens and closes</p>

                                            <div class="sale-dates">
                                                <div>
                                                    <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Date</label>
                                                    <input type="date" class="form-input" value="2023-06-05">
                                                </div>
                                                <div>
                                                    <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Time</label>
                                                    <input type="time" class="form-input" value="09:00">
                                                </div>
                                                <div>
                                                    <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Date</label>
                                                    <input type="date" class="form-input" value="2023-06-09">
                                                </div>
                                                <div>
                                                    <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Time</label>
                                                    <input type="time" class="form-input" value="18:00">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paid for Outside Users Section -->
                                    <div class="form-group">
                                        <h4 style="margin-bottom: 15px; font-size: 16px; color: #333;">
                                            <i class="fas fa-users" style="color: #FF6B35; margin-right: 8px;"></i>
                                            Paid Tickets for Outside Users
                                        </h4>

                                        <!-- Ticket Types Container -->
                                        <div class="ticket-types-container">
                                            <h5 style="margin-bottom: 15px; font-size: 14px; color: #666;">
                                                <i class="fas fa-ticket-alt" style="color: #4A5BCC; margin-right: 8px;"></i>
                                                Ticket Types
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
                                                                <label class="form-label">Quantity Available</label>
                                                                <input type="number" class="form-input ticket-quantity" value="100" placeholder="Enter quantity" min="1">
                                                            </div>
                                                            <div>
                                                                <label class="form-label">Price (LKR)</label>
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
                                            <label class="form-label">Sale Period</label>
                                            <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Set when tickets will be available for purchase</p>

                                            <div class="sale-dates">
                                                <div>
                                                    <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Date</label>
                                                    <input type="date" class="form-input" value="2023-06-05">
                                                </div>
                                                <div>
                                                    <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start Time</label>
                                                    <input type="time" class="form-input" value="09:00">
                                                </div>
                                                <div>
                                                    <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Date</label>
                                                    <input type="date" class="form-input" value="2023-06-09">
                                                </div>
                                                <div>
                                                    <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End Time</label>
                                                    <input type="time" class="form-input" value="18:00">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                    <select id="fieldType" class="form-select">
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
                                <input type="checkbox" id="volunteerToggle" name="volunteerToggle">
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div id="volunteerDetails" class="volunteer-details hidden" style="margin-top: 20px;">
                            <div class="info-note">
                                <i class="fas fa-hands-helping"></i>
                                Select where you'd like to recruit volunteers from
                            </div>

                            <div class="form-group">
                                <label class="form-label">Volunteer Source</label>
                                <div class="volunteer-source-options">
                                    <div class="volunteer-source-option">
                                        <input type="checkbox" id="faculty-volunteers" name="volunteer-source" value="faculty"
                                            checked>
                                        <label for="faculty-volunteers">
                                            <i class="fas fa-graduation-cap" style="color: #4A5BCC; font-size: 18px;"></i>
                                            From My Faculty
                                        </label>
                                    </div>
                                    <div class="volunteer-source-option">
                                        <input type="checkbox" id="university-volunteers" name="volunteer-source"
                                            value="university">
                                        <label for="university-volunteers">
                                            <i class="fas fa-university" style="color: #FF6B35; font-size: 18px;"></i>
                                            From My University
                                        </label>
                                    </div>
                                    <div class="volunteer-source-option">
                                        <input type="checkbox" id="public-volunteers" name="volunteer-source" value="public">
                                        <label for="public-volunteers">
                                            <i class="fas fa-users" style="color: #10B981; font-size: 18px;"></i>
                                            Public Users
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Number of Volunteers Needed</label>
                                <p style="font-size: 12px; color: #666; margin-bottom: 8px;">How many volunteers do you
                                    need?</p>
                                <input type="number" class="form-input" placeholder="e.g., 5" min="1"
                                    style="max-width: 200px;">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Volunteer Requirements</label>
                                <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Include positions when requesting a volunteer:</p>
                                <label for="newOption">Enter positions:</label>
                                <input type="text" class="form-input" id="newOption" placeholder="Type something...">
                                <button type="button" id="addPositionBtn" class="add-field-btn" style="margin-top: 10px;">+ Add Position</button>
                                <p><a href="volunteerreg.view.php">Go to volunteerreg Form</a></p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Add this section after the ticket section -->
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
                                <input type="checkbox" id="donationToggle" name="donationToggle">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </section>

                <div class="bottom-actions">
                    <button class="cancel-btn">Cancel</button>
                    <div class="action-buttons">
                        <button class="save-draft-btn">Save draft</button>
                        <button class="publish-btn">Publish Event</button>
                    </div>
                </div>
            </main>
        </form>
    </div>
    <?php include 'footer.php' ?>
    <script src="/unipulse/public/assets/js/create-event-app.js"></script>
</body>

</html>