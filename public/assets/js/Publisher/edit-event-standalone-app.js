
/* Extracted from Publisher/edit-event-standalone.view.php */

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
    
