<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YourEvent - Edit Event</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/create-event-style.css">
    <link rel="stylesheet" href="/unipulse/public/assets/css/Publisher/editevent-style.css">

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

            <!-- Hidden fields for read-only data that needs to be submitted -->
            <input type="hidden" name="event_category" value="<?= htmlspecialchars((string)($data['event']->category ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="event_date" value="<?= htmlspecialchars((string)($data['event']->event_date ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="event_time" value="<?= htmlspecialchars((string)($data['event']->event_time ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="event_location" value="<?= htmlspecialchars((string)($data['event']->location ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="location-type" value="<?= htmlspecialchars($data['event']->location_type ?? 'inside-university') ?>">
            <input type="hidden" name="venue_name" value="<?= htmlspecialchars($data['event']->venue_name ?? '') ?>">
            <input type="hidden" name="max_participants" value="<?= htmlspecialchars((string)($data['event']->max_participants ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="ticketType" value="<?= htmlspecialchars((string)($data['event']->ticket_type ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="volunteerToggle" value="<?= htmlspecialchars($data['event']->needs_volunteers ?? 0) ?>">
            <input type="hidden" name="volunteers_needed" value="<?= htmlspecialchars($data['event']->volunteers_needed ?? 0) ?>">
            <input type="hidden" name="donationToggle" value="<?= htmlspecialchars($data['event']->accepts_donations ?? 0) ?>">
            <?php
            $volunteerSources = $data['event']->volunteer_sources ?? [];
            if (!is_array($volunteerSources)) {
                $volunteerSources = json_decode($volunteerSources, true) ?? [];
            }
            foreach ($volunteerSources as $source):
            ?>
                <input type="hidden" name="volunteer-source[]" value="<?= htmlspecialchars($source) ?>">
            <?php endforeach; ?>

            <main class="content">

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
                                <label for="event_category" class="form-label">Category</label>
                                <div class="input-group">
                                    <input type="text" class="form-input" disabled
                                        value="<?php
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
                                                echo htmlspecialchars($categories[$data['event']->category] ?? $data['event']->category);
                                                ?>"
                                        style="background-color: #f5f5f5; cursor: not-allowed;">
                                </div>
                                <small style="color: #666; font-size: 12px;">Cannot be edited</small>
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
                                        <img src="/unipulse/public/<?= htmlspecialchars((string)($data['event']->cover_image ?? ''), ENT_QUOTES, 'UTF-8') ?>"
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

                <!-- Date & Time (Read-only) -->
                <section class="section" id="location-time">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Date & Time</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="event_date" class="form-label">Event Date</label>
                                <div class="input-group">
                                    <input type="date" class="form-input" disabled
                                        value="<?= htmlspecialchars((string)($data['event']->event_date ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                        style="background-color: #f5f5f5; cursor: not-allowed;">
                                </div>
                                <small style="color: #666; font-size: 12px;">Cannot be edited</small>
                            </div>

                            <div class="form-group">
                                <label for="event_time" class="form-label">Start Time</label>
                                <div class="input-group">
                                    <input type="time" class="form-input" disabled
                                        value="<?= htmlspecialchars((string)($data['event']->event_time ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                        style="background-color: #f5f5f5; cursor: not-allowed;">
                                </div>
                                <small style="color: #666; font-size: 12px;">Cannot be edited</small>
                            </div>

                            <div class="form-group">
                                <label for="event_end_time" class="form-label">End Time</label>
                                <div class="input-group">
                                    <input type="time" class="form-input" disabled
                                        value="<?= htmlspecialchars($data['event']->event_end_time ?? '') ?>"
                                        style="background-color: #f5f5f5; cursor: not-allowed;">
                                </div>
                                <small style="color: #666; font-size: 12px;">Cannot be edited</small>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Location (Read-only) -->
                <section class="section" id="location">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Location</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">
                        <div class="form-group">
                            <label for="event_location" class="form-label">Location</label>
                            <div class="input-group">
                                <input type="text" class="form-input" disabled
                                    value="<?= htmlspecialchars((string)($data['event']->location ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    style="background-color: #f5f5f5; cursor: not-allowed;">
                            </div>
                            <small style="color: #666; font-size: 12px;">Cannot be edited</small>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="location-type" class="form-label">Location Type</label>
                                <div class="input-group">
                                    <input type="text" class="form-input" disabled
                                        value="<?php
                                                $locationTypes = [
                                                    'inside-university' => 'Inside University',
                                                    'outside-university' => 'Outside University'
                                                ];
                                                echo htmlspecialchars($locationTypes[$data['event']->location_type ?? 'inside-university'] ?? $data['event']->location_type ?? 'Inside University');
                                                ?>"
                                        style="background-color: #f5f5f5; cursor: not-allowed;">
                                </div>
                                <small style="color: #666; font-size: 12px;">Cannot be edited</small>
                            </div>

                            <?php if (!empty($data['event']->venue_name)): ?>
                                <div class="form-group">
                                    <label for="venue_name" class="form-label">Venue Name</label>
                                    <div class="input-group">
                                        <input type="text" class="form-input" disabled
                                            value="<?= htmlspecialchars((string)($data['event']->venue_name ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                            style="background-color: #f5f5f5; cursor: not-allowed;">
                                    </div>
                                    <small style="color: #666; font-size: 12px;">Cannot be edited</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <!-- Registration Details (Read-only) -->
                <section class="section" id="ticket">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Registration Details</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="max_participants" class="form-label">Maximum Participants</label>
                                <div class="input-group">
                                    <input type="number" class="form-input" disabled
                                        value="<?= htmlspecialchars((string)($data['event']->max_participants ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                        style="background-color: #f5f5f5; cursor: not-allowed;">
                                </div>
                                <small style="color: #666; font-size: 12px;">Cannot be edited</small>
                            </div>

                            <div class="form-group">
                                <label for="ticketType" class="form-label">Ticket Type</label>
                                <div class="input-group">
                                    <input type="text" class="form-input" disabled
                                        value="<?php
                                                $ticketTypes = [
                                                    'free-all' => 'Free for All',
                                                    'paid-all' => 'Paid for All',
                                                    'mixed' => 'Mixed (Free & Paid)'
                                                ];
                                                echo htmlspecialchars($ticketTypes[$data['event']->ticket_type] ?? $data['event']->ticket_type);
                                                ?>"
                                        style="background-color: #f5f5f5; cursor: not-allowed;">
                                </div>
                                <small style="color: #666; font-size: 12px;">Cannot be edited</small>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Volunteers (Read-only) -->
                <?php if ($data['event']->needs_volunteers == 1): ?>
                    <section class="section" id="volunteers">
                        <div class="section-header">
                            <div class="section-icon"></div>
                            <h3>Volunteer Requirements</h3>
                            <div class="toggle-icon" style="margin-left: auto;">▼</div>
                        </div>
                        <div class="section-content">
                            <div class="info-note">
                                <i class="fas fa-info-circle"></i>
                                This event accepts volunteers from the following sources (cannot be edited)
                            </div>

                            <div class="form-group">
                                <label class="form-label">Volunteer Source</label>
                                <div style="background: #f5f5f5; padding: 15px; border-radius: 8px; border: 1px solid #ddd;">
                                    <?php
                                    $volunteerSources = $data['event']->volunteer_sources ?? [];
                                    if (!is_array($volunteerSources)) {
                                        $volunteerSources = json_decode($volunteerSources, true) ?? [];
                                    }
                                    $sourceLabels = [
                                        'faculty' => '✓ From My Faculty',
                                        'university' => '✓ From My University',
                                        'public' => '✓ Public Users'
                                    ];
                                    foreach ($volunteerSources as $source) {
                                        if (isset($sourceLabels[$source])) {
                                            echo '<p style="margin: 5px 0; color: #333;"><strong>' . htmlspecialchars($sourceLabels[$source]) . '</strong></p>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Number of Volunteers Needed</label>
                                <div class="input-group">
                                    <input type="number" class="form-input" disabled
                                        value="<?= htmlspecialchars($data['event']->volunteers_needed ?? 0) ?>"
                                        style="background-color: #f5f5f5; cursor: not-allowed; max-width: 200px;">
                                </div>
                                <small style="color: #666; font-size: 12px;">Cannot be edited</small>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Donations (Read-only) -->
                <section class="section" id="donation">
                    <div class="section-header">
                        <div class="section-icon"></div>
                        <h3>Donation Options</h3>
                        <div class="toggle-icon" style="margin-left: auto;">▼</div>
                    </div>
                    <div class="section-content">
                        <div style="background: #f5f5f5; padding: 15px; border-radius: 8px; border: 1px solid #ddd;">
                            <p style="margin: 0; color: #333;">
                                <strong>
                                    <?php if ($data['event']->accepts_donations == 1): ?>
                                        ✓ This event accepts donations
                                    <?php else: ?>
                                        ✗ This event does not accept donations
                                    <?php endif; ?>
                                </strong>
                            </p>
                            <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Cannot be edited</small>
                        </div>
                    </div>
                </section>

                <?php if (!empty($data['event']->requirements)): ?>
                    <!-- Requirements (Read-only) -->
                    <section class="section" id="requirements">
                        <div class="section-header">
                            <div class="section-icon"></div>
                            <h3>Additional Requirements</h3>
                            <div class="toggle-icon" style="margin-left: auto;">▼</div>
                        </div>
                        <div class="section-content">
                            <div class="form-group">
                                <label class="form-label">Requirements</label>
                                <div class="input-group">
                                    <textarea class="form-textarea" disabled
                                        style="background-color: #f5f5f5; cursor: not-allowed;"><?php
                                                                                                $requirements = $data['event']->requirements ?? '';
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
                                <small style="color: #666; font-size: 12px;">Cannot be edited</small>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

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
        window.publisherEditEventConfig = {
            redirectUrl: '/unipulse/public/publisher/eventview?id=<?= $data['event_id'] ?>'
        };
    </script>
    <script src="/unipulse/public/assets/js/Publisher/editevent-app.js"></script>

</body>

</html>