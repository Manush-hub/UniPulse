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
            
            <main class="content">
                <!-- Back button -->
                <a href="/unipulse/public/publisher/dashboard" class="back-button">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
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
                        <button type="button" onclick="window.location.href='/unipulse/public/publisher/events'" class="cancel-btn">
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