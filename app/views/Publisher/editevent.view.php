<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YourEvent - Edit Event</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/create-event-style.css">
    <style>
        /* Remove background gradient and make it white */
        body {
            background: #ffffff !important;
            background-image: none !important;
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
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            transition: background 0.3s;
        }
        .back-button:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }
    </style>

</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => ''];
    include __DIR__ . '/components/header.php';
    ?>
    
    <div class="container">
        <!-- Back button -->
        <a href="/unipulse/public/publisher/dashboard" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>
        
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
        
        <form action="/unipulse/public/publisher/editevent/<?= $data['event_id'] ?>" method="POST" enctype="multipart/form-data" id="edit-event">
            <!-- Hidden field to help with AJAX detection -->
            <input type="hidden" name="ajax" value="1" id="ajax-flag">
            
            <main class="form-container">
                <div class="form-header">
                    <h1 class="page-title">Edit Event</h1>
                    <p class="page-subtitle">Update your event details below</p>
                </div>

                <!-- Basic Event Information -->
                <section class="form-section">
                    <div class="section-header">
                        <i class="fas fa-info-circle"></i>
                        <h2>Basic Information</h2>
                    </div>

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
                <section class="form-section">
                    <div class="section-header">
                        <i class="fas fa-calendar-alt"></i>
                        <h2>Date & Time</h2>
                    </div>

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
                <section class="form-section">
                    <div class="section-header">
                        <i class="fas fa-map-marker-alt"></i>
                        <h2>Location</h2>
                    </div>

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

                <!-- Registration Details -->
                <section class="form-section">
                    <div class="section-header">
                        <i class="fas fa-users"></i>
                        <h2>Registration Details</h2>
                    </div>

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
                                <select name="ticketType" class="form-select" required>
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
                <section class="form-section">
                    <div class="section-header">
                        <i class="fas fa-hands-helping"></i>
                        <h2>Volunteer Requirements</h2>
                    </div>

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
                            <div class="form-group">
                                <label for="volunteers_needed" class="form-label">Number of Volunteers Needed</label>
                                <div class="input-group">
                                    <input type="number" name="volunteers_needed" class="form-input" min="1"
                                           value="<?= htmlspecialchars($data['old_data']['volunteers_needed'] ?? $data['event']->volunteers_needed ?? '') ?>"
                                           placeholder="How many volunteers do you need?">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Donations Section -->
                <section class="form-section">
                    <div class="section-header">
                        <i class="fas fa-donate"></i>
                        <h2>Donation Options</h2>
                    </div>

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

                <!-- Cover Image -->
                <section class="form-section">
                    <div class="section-header">
                        <i class="fas fa-image"></i>
                        <h2>Cover Image</h2>
                    </div>

                    <div class="form-group">
                        <label for="cover_image" class="form-label">Upload Cover Image</label>
                        <?php if (!empty($data['event']->cover_image)): ?>
                            <div class="current-image" style="margin-bottom: 10px;">
                                <p style="color: #666; font-size: 14px;">Current image:</p>
                                <img src="/unipulse/public/<?= htmlspecialchars($data['event']->cover_image) ?>" 
                                     alt="Current cover" style="max-width: 200px; max-height: 150px; border-radius: 5px; border: 1px solid #ddd;">
                            </div>
                        <?php endif; ?>
                        <div class="file-upload-container">
                            <div class="file-upload-area">
                                <input type="file" name="cover_image" id="coverFileInput" class="file-input" accept="image/*">
                                <label for="coverFileInput" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Choose new image or drag & drop</span>
                                    <small>JPG, PNG, GIF up to 5MB</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Additional Requirements -->
                <section class="form-section">
                    <div class="section-header">
                        <i class="fas fa-list-ul"></i>
                        <h2>Additional Information</h2>
                    </div>

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
                <div class="form-actions">
                    <button type="button" onclick="window.location.href='/unipulse/public/publisher/events'" class="cancel-btn">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="publish-btn">
                        <i class="fas fa-save"></i> Update Event
                    </button>
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
                showSuccessMessage('Event updated successfully!');
                
                // Scroll to top of form
                window.scrollTo({ top: 0, behavior: 'smooth' });
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
    </script>
</body>

</html>