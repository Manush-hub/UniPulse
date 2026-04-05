<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Edit Event</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/create-event-style.css">
        <link rel="stylesheet" href="/unipulse/public/assets/css/Publisher/edit-event-standalone-style.css">
</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'events'];
    include __DIR__ . '/components/header.php';
    ?>

    <div class="container" style="max-width: 800px; margin: 0 auto; padding: 2rem;">
        <!-- Back Link -->
        <a href="/unipulse/public/publisher/dashboard" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Page Header -->
        <div class="page-header">
            <h1>Edit Event</h1>
            <p>Update your event details and settings</p>
        </div>

        <!-- Success/Error Messages -->
        <div id="message-container"></div>

        <!-- Edit Event Form -->
        <form id="editEventForm" enctype="multipart/form-data">
            <input type="hidden" name="event_id" id="event_id">

            <!-- Basic Information -->
            <section class="form-section">
                <div class="section-header">
                    <i class="fas fa-info-circle"></i>
                    <h2>Basic Information</h2>
                </div>

                <div class="form-group">
                    <label for="event_name" class="form-label">Event Name *</label>
                    <input type="text" id="event_name" name="event_name" class="form-input" required
                        placeholder="Enter event name">
                </div>

                <div class="form-group">
                    <label for="event_description" class="form-label">Event Description *</label>
                    <textarea id="event_description" name="event_description" class="form-textarea" required
                        placeholder="Describe your event in detail" rows="5"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="event_category" class="form-label">Category *</label>
                        <select id="event_category" name="event_category" class="form-select" required>
                            <option value="">Select a category</option>
                            <option value="academic">Academic</option>
                            <option value="sports">Sports</option>
                            <option value="cultural">Cultural</option>
                            <option value="technology">Technology</option>
                            <option value="social">Social</option>
                            <option value="workshop">Workshop</option>
                            <option value="business">Business</option>
                            <option value="music">Music</option>
                        </select>
                    </div>
                </div>
            </section>

            <!-- Date & Time -->
            <section class="form-section">
                <div class="section-header">
                    <i class="fas fa-calendar-alt"></i>
                    <h2>Date & Time</h2>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="event_date" class="form-label">Event Date *</label>
                        <input type="date" id="event_date" name="event_date" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="event_time" class="form-label">Start Time *</label>
                        <input type="time" id="event_time" name="event_time" class="form-input" required>
                    </div>
                </div>
            </section>

            <!-- Location -->
            <section class="form-section">
                <div class="section-header">
                    <i class="fas fa-map-marker-alt"></i>
                    <h2>Location</h2>
                </div>

                <div class="form-group">
                    <label for="event_location" class="form-label">Location *</label>
                    <input type="text" id="event_location" name="event_location" class="form-input" required
                        placeholder="Enter event location">
                </div>

                <div class="form-group">
                    <label for="location_type" class="form-label">Location Type</label>
                    <select id="location_type" name="location-type" class="form-select">
                        <option value="inside-university">Inside University</option>
                        <option value="outside-university">Outside University</option>
                        <option value="online">Online</option>
                        <option value="hybrid">Hybrid</option>
                    </select>
                </div>
            </section>

            <!-- Event Settings -->
            <section class="form-section">
                <div class="section-header">
                    <i class="fas fa-cog"></i>
                    <h2>Event Settings</h2>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="max_participants" class="form-label">Max Participants</label>
                        <input type="number" id="max_participants" name="max_participants" class="form-input"
                            placeholder="Enter maximum number of participants" min="1" value="100">
                    </div>

                    <div class="form-group">
                        <label for="visibility" class="form-label">Event Visibility</label>
                        <select id="visibility" name="visibility" class="form-select">
                            <option value="faculty-only">Faculty Only</option>
                            <option value="university-only">University Only</option>
                            <option value="all-universities">All Universities</option>
                            <option value="public">Public</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="ticket_type" class="form-label">Ticket Type</label>
                    <select id="ticket_type" name="ticketType" class="form-select">
                        <option value="free-all">Free for All</option>
                        <option value="free-students">Free for Students</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
            </section>

            <!-- Additional Options -->
            <section class="form-section">
                <div class="section-header">
                    <i class="fas fa-plus-circle"></i>
                    <h2>Additional Options</h2>
                </div>

                <div class="form-group">
                    <label for="needs_volunteers" class="checkbox-group">
                        <input type="checkbox" id="needs_volunteers" name="volunteerToggle" value="1">
                        <span class="checkmark"></span>
                        This event needs volunteers
                    </label>
                </div>

                <div class="form-group volunteer-details" style="display: none;">
                    <label for="volunteers_needed" class="form-label">Number of Volunteers Needed</label>
                    <input type="number" id="volunteers_needed" name="volunteers_needed" class="form-input"
                        placeholder="How many volunteers do you need?" min="1">
                </div>

                <div class="form-group">
                    <label for="accepts_donations" class="checkbox-group">
                        <input type="checkbox" id="accepts_donations" name="donationToggle" value="1">
                        <span class="checkmark"></span>
                        This event accepts donations
                    </label>
                </div>

                <div class="form-group">
                    <label for="requirements" class="form-label">Event Requirements</label>
                    <textarea id="requirements" name="requirements" class="form-textarea"
                        placeholder="List any requirements for participants (one per line)" rows="4"></textarea>
                </div>
            </section>

            <!-- Cover Image -->
            <section class="form-section">
                <div class="section-header">
                    <i class="fas fa-image"></i>
                    <h2>Cover Image</h2>
                </div>

                <div class="form-group">
                    <label for="cover_image" class="form-label">Event Cover Image</label>
                    <input type="file" id="cover_image" name="cover_image" class="form-input" accept="image/*">
                    <div id="current_image" style="margin-top: 10px;"></div>
                </div>
            </section>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="/unipulse/public/publisher/dashboard" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="button" id="deleteBtn" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete Event
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Event
                </button>
            </div>
        </form>
    </div>

        <script src="/unipulse/public/assets/js/Publisher/edit-event-standalone-app.js?v=<?= time() ?>"></script>
</body>

</html>