<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Edit Event</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/create-event-style.css">
    <style>
        /* Additional styles for edit functionality */
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e2e8f0;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid;
        }

        .alert-success {
            background: #f0fdf4;
            border-color: #16a34a;
            color: #166534;
        }

        .alert-error {
            background: #fef2f2;
            border-color: #dc2626;
            color: #991b1b;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            color: #1f2937;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: #6b7280;
            font-size: 1.1rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #6b7280;
            text-decoration: none;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .back-link:hover {
            color: #374151;
        }
    </style>
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

                    <div class="form-group">
                        <label for="target_audience" class="form-label">Target Audience *</label>
                        <select id="target_audience" name="audience" class="form-select" required>
                            <option value="">Select audience</option>
                            <option value="university-students">University Students</option>
                            <option value="public-users">Public Users</option>
                            <option value="both">Both</option>
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

    <script>
        // Global variables
        let currentEventId = null;

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Get event ID from URL
            const urlParams = new URLSearchParams(window.location.search);
            currentEventId = urlParams.get('id');

            if (!currentEventId) {
                showMessage('Error: No event ID provided', 'error');
                return;
            }

            document.getElementById('event_id').value = currentEventId;
            loadEventData(currentEventId);

            // Setup form handlers
            setupFormHandlers();
        });

        // Load event data
        function loadEventData(eventId) {
            showMessage('Loading event data...', 'info');

            fetch(`/unipulse/public/publisher/edit-event-standalone/getEvent?id=${eventId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        populateForm(data.event);
                        hideMessage();
                    } else {
                        showMessage('Error loading event: ' + data.error, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('Failed to load event data', 'error');
                });
        }

        // Populate form with event data
        function populateForm(event) {
            document.getElementById('event_name').value = event.title || '';
            document.getElementById('event_description').value = event.description || '';
            document.getElementById('event_category').value = event.category || '';
            document.getElementById('target_audience').value = event.target_audience || '';
            document.getElementById('event_date').value = event.event_date || '';
            document.getElementById('event_time').value = event.event_time || '';
            document.getElementById('event_location').value = event.location || '';
            document.getElementById('location_type').value = event.location_type || 'inside-university';
            document.getElementById('max_participants').value = event.max_participants || 100;
            document.getElementById('visibility').value = event.visibility || 'public';
            document.getElementById('ticket_type').value = event.ticket_type || 'free-all';

            // Checkboxes
            document.getElementById('needs_volunteers').checked = event.needs_volunteers == 1;
            document.getElementById('accepts_donations').checked = event.accepts_donations == 1;
            document.getElementById('volunteers_needed').value = event.volunteers_needed || '';

            // Show/hide volunteer details
            toggleVolunteerDetails();

            // Requirements (convert array to string)
            if (event.requirements && Array.isArray(event.requirements)) {
                document.getElementById('requirements').value = event.requirements.join('\n');
            }

            // Show current image if exists
            if (event.image_url || event.cover_image) {
                const imageUrl = event.image_url || event.cover_image;
                document.getElementById('current_image').innerHTML =
                    `<p>Current image:</p><img src="${imageUrl}" alt="Current cover" style="max-width: 200px; border-radius: 8px;">`;
            }
        }

        // Setup form handlers
        function setupFormHandlers() {
            // Volunteer checkbox handler
            document.getElementById('needs_volunteers').addEventListener('change', toggleVolunteerDetails);

            // Form submission
            document.getElementById('editEventForm').addEventListener('submit', handleFormSubmit);

            // Delete button
            document.getElementById('deleteBtn').addEventListener('click', handleDelete);
        }

        // Toggle volunteer details
        function toggleVolunteerDetails() {
            const checkbox = document.getElementById('needs_volunteers');
            const details = document.querySelector('.volunteer-details');
            details.style.display = checkbox.checked ? 'block' : 'none';
        }

        // Handle form submission
        function handleFormSubmit(e) {
            e.preventDefault();

            const submitBtn = document.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            submitBtn.disabled = true;

            const formData = new FormData(document.getElementById('editEventForm'));
            formData.append('event_id', currentEventId);

            fetch(`/unipulse/public/publisher/edit-event-standalone/updateEvent`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('Event updated successfully!', 'success');
                        // Optionally redirect after a delay
                        setTimeout(() => {
                            window.location.href = '/unipulse/public/publisher/dashboard';
                        }, 2000);
                    } else {
                        showMessage('Error: ' + (data.errors?.general || 'Failed to update event'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('Network error occurred. Please try again.', 'error');
                })
                .finally(() => {
                    // Reset button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

        // Handle delete
        function handleDelete() {
            if (!confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
                return;
            }

            const deleteBtn = document.getElementById('deleteBtn');
            const originalText = deleteBtn.innerHTML;

            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            deleteBtn.disabled = true;

            fetch(`/unipulse/public/publisher/edit-event-standalone/deleteEvent`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        id: currentEventId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('Event deleted successfully!', 'success');
                        setTimeout(() => {
                            window.location.href = '/unipulse/public/publisher/dashboard';
                        }, 1500);
                    } else {
                        showMessage('Error: ' + (data.error || 'Failed to delete event'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('Network error occurred. Please try again.', 'error');
                })
                .finally(() => {
                    deleteBtn.innerHTML = originalText;
                    deleteBtn.disabled = false;
                });
        }

        // Show message
        function showMessage(message, type) {
            const container = document.getElementById('message-container');
            const alertClass = type === 'success' ? 'alert-success' :
                type === 'error' ? 'alert-error' : 'alert-info';

            container.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
        }

        // Hide message
        function hideMessage() {
            document.getElementById('message-container').innerHTML = '';
        }
    </script>
</body>

</html>