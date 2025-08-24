<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YourEvent - Create an event</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
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
            <div class="sidebar-item active" data-target="album-upload">Album upload</div>
            <div class="sidebar-item" data-target="location-time">Location and time</div>
            <div class="sidebar-item" data-target="ticket">Ticket</div>
            <div class="sidebar-item" data-target="Request-Volunteer">Request Volunteer</div>
        </aside>

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

            <!-- ALBUM UPLOAD SECTION - UPDATED -->
            <section class="section" id="album-upload">
                <div class="section-header">
                    <div class="section-icon"></div>
                    <h3>Album</h3>
                    <div class="toggle-icon" style="margin-left: auto;">▼</div>
                </div>
                <div class="section-content">
                    <p style="color: #666; margin-bottom: 15px;">Upload photos showcasing your event</p>

                    <div class="album-upload-compact">
                        <div class="upload-section-compact" id="uploadAreaCompact">
                            <div class="upload-icon-compact">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="upload-text-compact">
                                <h3>Drag & Drop</h3>
                                <p>Upload your event photos or click browse</p>
                            </div>
                            <input type="file" id="fileInputCompact" class="file-input" multiple accept="image/*">
                            <label for="fileInputCompact" class="browse-btn-compact">Browse Files</label>
                            <p class="file-info-compact">Supported formats: JPG, PNG, GIF</p>
                        </div>

                        <div class="preview-section-compact">
                            <div class="preview-title-compact">
                                <span>Selected Photos</span>
                            </div>
                            <div class="preview-grid-compact" id="previewGridCompact">
                                <div class="empty-state-compact">
                                    <i class="fas fa-images"></i>
                                    <p>No photos selected yet</p>
                                </div>
                            </div>

                            <button class="upload-btn-compact" id="uploadBtnCompact" disabled>
                                <i class="fas fa-upload"></i> Upload Album
                            </button>
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
                        <label class="form-label">Location</label>
                        <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Tell attendees where your event is
                            happening</p>

                        <div class="location-inputs">
                            <div>
                                <label
                                    style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Address</label>
                                <input type="text" class="form-input" placeholder="Enter address">
                            </div>
                            <div>
                                <label
                                    style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">City</label>
                                <input type="text" class="form-input" placeholder="Enter city">
                            </div>
                            <div>
                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">State /
                                    Province</label>
                                <input type="text" class="form-input" placeholder="Enter state">
                            </div>
                            <div>
                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Country
                                    / Region</label>
                                <input type="text" class="form-input" placeholder="Enter country">
                            </div>
                        </div><br>
                        <div class="map-container" id="mapPreview">
                            <div class="map-placeholder">
                                <i class="fas fa-map-marker-alt"></i>
                                <p>Map will appear when location is entered</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Time</label>
                        <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Let attendees know when your event
                            starts</p>

                        <div class="time-inputs">
                            <div>
                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start
                                    Date</label>
                                <input type="date" class="form-input" value="2023-06-10">
                            </div>
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
                            <div>
                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End
                                    Time</label>
                                <input type="time" class="form-input" value="23:00">
                            </div>
                        </div>

                        <button
                            style="background: #FF6B35; color: white; border: none; padding: 8px 16px; border-radius: 4px; margin-top: 15px; cursor: pointer; font-size: 12px;">Add
                            agenda</button>
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
                    <div class="ticket-options">
                        <div class="ticket-option">
                            <input type="radio" name="ticket-type" id="paid" checked>
                            <label for="paid">Paid</label>
                        </div>
                        <div class="ticket-option">
                            <input type="radio" name="ticket-type" id="free">
                            <label for="free">Free</label>
                        </div>
                    </div>

                    <div class="ticket-details">
                        <div>
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-input" value="300" placeholder="Enter quantity">
                        </div>
                        <div>
                            <label class="form-label">Price</label>
                            <input type="number" class="form-input" value="10" placeholder="Enter price">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Sale date</label>
                        <p style="font-size: 12px; color: #666; margin-bottom: 8px;">Set the sale date when your
                            audiences is able to purchase the tickets</p>

                        <div class="sale-dates">
                            <div>
                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start
                                    Date</label>
                                <input type="date" class="form-input" value="2023-06-05">
                            </div>
                            <div>
                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">Start
                                    Time</label>
                                <input type="time" class="form-input" value="09:00">
                            </div>
                            <div>
                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End
                                    Date</label>
                                <input type="date" class="form-input" value="2023-06-09">
                            </div>
                            <div>
                                <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">End
                                    Time</label>
                                <input type="time" class="form-input" value="18:00">
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
                        Need volunteers for your event’s success? Let us know.
                    </p>

                    <label for="volunteerToggle" style="display: block; margin-bottom: 8px; color: #333;">
                        Do you want volunteers?
                    </label>

                    <label class="switch">
                        <input type="checkbox" id="volunteerToggle" name="volunteerToggle" checked>
                        <span class="slider"></span>
                    </label>
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
    </div>

    <?php include 'footer.php' ?>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/unipulse/public/assets/js/create-event-app.js"></script>

</body>

</html>