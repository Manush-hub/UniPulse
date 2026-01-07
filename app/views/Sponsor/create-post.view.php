<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Sponsor Post - UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/sponsor/create-post-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Force consistent font on this page */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        input, select, button, textarea { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    </style>
</head>
<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'events'];
    include __DIR__ . '/components/header.php';
    ?>

    <div class="container create-post-container">
        <!-- Page Header -->
        <header class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-pen-fancy"></i> Create Sponsor Post</h1>
                <p>Promote your brand on the event page</p>
            </div>
        </header>

        <div class="post-creation-section">
            <!-- Sidebar - Guidelines -->
            <aside class="guidelines-sidebar">
                <div class="guidelines-card">
                    <h3><i class="fas fa-lightbulb"></i> Guidelines</h3>
                    <ul>
                        <li><strong>Professional:</strong> Keep content professional and relevant to the event</li>
                        <li><strong>Length:</strong> Title: 5-255 chars | Content: 20-5000 chars</li>
                        <li><strong>No Prohibited Content:</strong> No illegal drugs, gambling, or explicit content</li>
                        <li><strong>Approval:</strong> Posts require admin approval before appearing</li>
                        <li><strong>Authentic:</strong> Ensure all links and images are valid</li>
                    </ul>
                </div>

                <div class="event-card">
                    <h4>Event Details</h4>
                    <div class="event-info">
                        <div class="info-item">
                            <strong>Title:</strong>
                            <p><?= htmlspecialchars($event->title) ?></p>
                        </div>
                        <div class="info-item">
                            <strong>Date:</strong>
                            <p><?= date('M j, Y', strtotime($event->event_date)) ?></p>
                        </div>
                        <div class="info-item">
                            <strong>University:</strong>
                            <p><?= htmlspecialchars($event->university_name) ?></p>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Form -->
            <main class="main-content">
                <form id="postForm" class="post-form" method="POST" enctype="multipart/form-data">
                    <!-- Title -->
                    <div class="form-section">
                        <h3>Post Title</h3>
                        <div class="form-group">
                            <label for="title">Title *</label>
                            <input type="text" id="title" name="title" placeholder="Enter an engaging title (5-255 characters)" maxlength="255" required>
                            <div class="char-count"><span id="titleCount">0</span>/255</div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="form-section">
                        <h3>Post Content</h3>
                        <div class="form-group">
                            <label for="content">Content *</label>
                            <textarea id="content" name="content" placeholder="Write your promotional content (20-5000 characters)" maxlength="5000" required rows="8"></textarea>
                            <div class="char-count"><span id="contentCount">0</span>/5000</div>
                        </div>
                    </div>

                    <!-- Media -->
                    <div class="form-section">
                        <h3>Media & Branding</h3>
                        
                        <div class="form-group">
                            <label for="image">Post Image</label>
                            <div class="file-upload">
                                <input type="file" id="image" name="image" accept="image/*" style="display:none;">
                                <button type="button" class="upload-area" data-input="image">
                                    <i class="fas fa-image"></i>
                                    <p>Click to upload or drag and drop</p>
                                    <small>PNG, JPG, GIF, WEBP up to 5MB</small>
                                </button>
                                <div id="imagePreview" class="preview" style="display: none;">
                                    <img id="imageImg" src="" alt="Preview">
                                    <button type="button" onclick="clearImage()">Clear</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="logo">Brand Logo</label>
                            <div class="file-upload">
                                <input type="file" id="logo" name="logo" accept="image/*" style="display:none;">
                                <button type="button" class="upload-area" data-input="logo">
                                    <i class="fas fa-building"></i>
                                    <p>Click to upload or drag and drop</p>
                                    <small>PNG, JPG, GIF, WEBP up to 5MB</small>
                                </button>
                                <div id="logoPreview" class="preview" style="display: none;">
                                    <img id="logoImg" src="" alt="Logo Preview">
                                    <button type="button" onclick="clearLogo()">Clear</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Call to Action -->
                    <div class="form-section">
                        <h3>Call to Action (Optional)</h3>
                        
                        <div class="form-group">
                            <label for="cta_text">CTA Text</label>
                            <input type="text" id="cta_text" name="cta_text" placeholder="e.g., 'Visit Our Website', 'Learn More'" maxlength="255">
                        </div>

                        <div class="form-group">
                            <label for="cta_url">CTA URL</label>
                            <input type="url" id="cta_url" name="cta_url" placeholder="https://example.com">
                            <small>Include full URL starting with http:// or https://</small>
                        </div>
                    </div>

                    <!-- Website -->
                    <div class="form-section">
                        <h3>Website Link (Optional)</h3>
                        <div class="form-group">
                            <label for="website_url">Website URL</label>
                            <input type="url" id="website_url" name="website_url" placeholder="https://yourcompany.com">
                            <small>Your company website for verification</small>
                        </div>
                    </div>

                    <!-- Error Messages -->
                    <div id="errorContainer" class="alert alert-danger" style="display: none;"></div>

                    <!-- Success Message -->
                    <div id="successContainer" class="alert alert-success" style="display: none;"></div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <a href="/unipulse/public/sponsor/events/viewEvent/<?= $event->id ?>" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn" title="Submit your sponsor post for admin approval">
                            <i class="fas fa-paper-plane"></i> Submit for Approval
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script>
        const form = document.getElementById('postForm');
        const titleInput = document.getElementById('title');
        const contentInput = document.getElementById('content');
        const submitBtn = document.getElementById('submitBtn');

        // Character counter
        titleInput.addEventListener('input', function() {
            document.getElementById('titleCount').textContent = this.value.length;
        });

        contentInput.addEventListener('input', function() {
            document.getElementById('contentCount').textContent = this.value.length;
        });

        // File upload preview + drag-drop
        function setupFilePreview(inputId, previewId, imgId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const img = document.getElementById(imgId);
            const button = document.querySelector(`.upload-area[data-input="${inputId}"]`);

            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        img.src = e.target.result;
                        if (preview) preview.style.display = 'none'; // keep preview hidden
                        if (button) {
                            button.classList.add('has-file');
                            button.style.backgroundImage = `url('${e.target.result}')`;
                            const label = button.querySelector('p');
                            const hint = button.querySelector('small');
                            if (label) label.textContent = file.name;
                            if (hint) hint.textContent = 'Selected';
                        }
                    };
                    reader.readAsDataURL(file);
                } else if (button) {
                    button.classList.remove('has-file');
                    button.style.backgroundImage = '';
                    const label = button.querySelector('p');
                    const hint = button.querySelector('small');
                    if (label) label.textContent = 'Click to upload or drag and drop';
                    if (hint) hint.textContent = 'PNG, JPG, GIF, WEBP up to 5MB';
                    if (preview) preview.style.display = 'none';
                }
            });

            // Drag & drop
            if (button) {
                ['dragenter','dragover'].forEach(evt => button.addEventListener(evt, e => {
                    e.preventDefault();
                    e.stopPropagation();
                    button.classList.add('dragover');
                }));

                ['dragleave','drop'].forEach(evt => button.addEventListener(evt, e => {
                    e.preventDefault();
                    e.stopPropagation();
                    button.classList.remove('dragover');
                }));

                button.addEventListener('drop', e => {
                    const file = e.dataTransfer.files && e.dataTransfer.files[0];
                    if (file) {
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        input.files = dt.files;
                        input.dispatchEvent(new Event('change'));
                    }
                });
            }
        }

        setupFilePreview('image', 'imagePreview', 'imageImg');
        setupFilePreview('logo', 'logoPreview', 'logoImg');

        // Hook upload buttons to hidden inputs
        document.querySelectorAll('.upload-area').forEach(btn => {
            btn.addEventListener('click', () => {
                const inputId = btn.getAttribute('data-input');
                const input = document.getElementById(inputId);
                if (input) input.click();
            });
        });

        function clearImage() {
            document.getElementById('image').value = '';
            document.getElementById('imagePreview').style.display = 'none';
            const btn = document.querySelector('.upload-area[data-input="image"]');
            if (btn) {
                btn.classList.remove('has-file');
                btn.style.backgroundImage = '';
                const label = btn.querySelector('p');
                const hint = btn.querySelector('small');
                if (label) label.textContent = 'Click to upload or drag and drop';
                if (hint) hint.textContent = 'PNG, JPG, GIF, WEBP up to 5MB';
            }
        }

        function clearLogo() {
            document.getElementById('logo').value = '';
            document.getElementById('logoPreview').style.display = 'none';
            const btn = document.querySelector('.upload-area[data-input="logo"]');
            if (btn) {
                btn.classList.remove('has-file');
                btn.style.backgroundImage = '';
                const label = btn.querySelector('p');
                const hint = btn.querySelector('small');
                if (label) label.textContent = 'Click to upload or drag and drop';
                if (hint) hint.textContent = 'PNG, JPG, GIF, WEBP up to 5MB';
            }
        }

        // Form submission
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });

                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    // Server returned HTML (likely an error page)
                    const text = await response.text();
                    console.error('Server returned HTML instead of JSON:', text);
                    throw new Error('Server error: ' + (text.substring(0, 200) || 'Unknown error'));
                }

                const data = await response.json();

                if (data.success) {
                    document.getElementById('successContainer').style.display = 'block';
                    document.getElementById('successContainer').innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                    
                    setTimeout(() => {
                        window.location.href = data.redirect || '/unipulse/public/sponsor/events?view=sponsor';
                    }, 2000);
                } else {
                    document.getElementById('errorContainer').style.display = 'block';
                    let errorHTML = '<i class="fas fa-exclamation-circle"></i> <div><strong>Error:</strong> ' + (data.message || 'An unknown error occurred');
                    if (data.debug) {
                        errorHTML += '<br><small style="margin-top: 5px; display: block;">Debug: ' + data.debug + '</small>';
                    }
                    if (data.errors && Array.isArray(data.errors)) {
                        errorHTML += '<ul style="margin-top: 10px;">';
                        data.errors.forEach(error => {
                            errorHTML += '<li>' + error + '</li>';
                        });
                        errorHTML += '</ul>';
                    }
                    errorHTML += '</div>';
                    document.getElementById('errorContainer').innerHTML = errorHTML;
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit for Approval';
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('errorContainer').style.display = 'block';
                document.getElementById('errorContainer').innerHTML = '<i class="fas fa-exclamation-circle"></i> <div><strong>Error:</strong> ' + error.message + '<br><small>Check browser console (F12) for details</small></div>';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit for Approval';
            }
        });
    </script>
</body>
</html>
