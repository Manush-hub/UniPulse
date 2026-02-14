# Code Examples - Profile Photo Auto-Save

## Frontend JavaScript Examples

### Example 1: How the Upload is Triggered
```javascript
// User clicks on cover photo area
// This calls: onclick="uploadCover()"
function uploadCover() {
    document.getElementById('coverInput').click(); // Opens file picker
}

// OR for profile photo
function uploadProfileImage() {
    document.getElementById('profileInput').click(); // Opens file picker
}
```

### Example 2: File Change Handler
```html
<!-- HTML Input -->
<input type="file" id="coverInput" accept="image/*" 
       style="display:none" onchange="changeCoverImage(event)">
```

```javascript
// JavaScript Handler
function changeCoverImage(event) {
    const file = event.target.files[0];
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        showImageUploadStatus('Please select a valid image file', 'error');
        return;
    }
    
    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        showImageUploadStatus('Image size must be less than 5MB', 'error');
        return;
    }
    
    // Show preview immediately
    const reader = new FileReader();
    reader.onload = (e) => {
        const coverImg = document.getElementById('coverPhoto');
        if (coverImg) {
            coverImg.src = e.target.result;
            coverImg.style.display = 'block';
        }
        
        // Show saving status
        showImageUploadStatus('Saving cover photo...', 'info', 0);
        
        // Save to database using FormData
        saveCoverImageFormData(file);
    };
    reader.readAsDataURL(file);
}
```

### Example 3: Uploading via FormData
```javascript
async function saveCoverImageFormData(file) {
    try {
        // Create FormData object
        const formData = new FormData();
        formData.append('cover_photo', file);
        
        // Send to server
        const response = await fetch('/unipulse/public/user/profile/updateProfile', {
            method: 'POST',
            credentials: 'same-origin',
            body: formData  // FormData instead of JSON
        });
        
        const result = await response.json();
        
        if (result.success) {
            showImageUploadStatus('Cover photo saved successfully!', 'success');
            console.log('Cover photo saved successfully');
        } else {
            showImageUploadStatus('Failed to save cover photo: ' + (result.message || result.error), 'error');
            console.error('Failed to save cover photo:', result.error);
        }
    } catch (error) {
        showImageUploadStatus('Error saving cover photo', 'error');
        console.error('Error saving cover photo:', error);
    }
}
```

### Example 4: User Feedback Notification
```javascript
function showImageUploadStatus(message, type = 'info', duration = 3000) {
    // Create status div if doesn't exist
    let statusDiv = document.getElementById('imageUploadStatus');
    if (!statusDiv) {
        statusDiv = document.createElement('div');
        statusDiv.id = 'imageUploadStatus';
        statusDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 4px;
            font-size: 14px;
            z-index: 10000;
            max-width: 300px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        `;
        document.body.appendChild(statusDiv);
    }
    
    // Set message and style
    statusDiv.textContent = message;
    statusDiv.style.display = 'block';
    
    if (type === 'success') {
        statusDiv.style.backgroundColor = '#4CAF50';
        statusDiv.style.color = 'white';
    } else if (type === 'error') {
        statusDiv.style.backgroundColor = '#f44336';
        statusDiv.style.color = 'white';
    } else {
        statusDiv.style.backgroundColor = '#2196F3';
        statusDiv.style.color = 'white';
    }
    
    // Auto-hide after duration
    if (duration > 0) {
        setTimeout(() => {
            statusDiv.style.display = 'none';
        }, duration);
    }
}
```

## Backend PHP Examples

### Example 1: Handling FormData in Controller
```php
public function updateProfile()
{
    header('Content-Type: application/json');
    if (!AuthService::isLoggedIn()) {
        echo json_encode(['success' => false, 'error' => 'Not authenticated']);
        return;
    }

    $u = AuthService::getCurrentUser();
    $fields = [];

    // Check if it's FormData (multipart/form-data)
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    
    if (strpos($contentType, 'multipart/form-data') !== false) {
        // Handle file uploads
        if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['cover_photo'];
            
            if ($this->isValidImageFile($file)) {
                $imageData = $this->processImageUpload($file);
                if ($imageData) {
                    $fields['cover_photo'] = $imageData;
                }
            }
        }
    }
    
    // ... process other fields ...
    
    // Update database
    $model->update($u['id'], $fields);
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
}
```

### Example 2: Validating Image Files
```php
private function isValidImageFile($file)
{
    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedMimes)) {
        return false;
    }
    
    // Check file extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExtensions)) {
        return false;
    }
    
    // Check file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return false;
    }
    
    return true;
}
```

### Example 3: Processing Uploaded Image
```php
private function processImageUpload($file)
{
    try {
        // Read file content
        $imageData = file_get_contents($file['tmp_name']);
        if ($imageData === false) {
            return null;
        }
        
        // Convert to base64 data URI
        $mimeType = mime_content_type($file['tmp_name']);
        $base64Image = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
        
        return $base64Image;
    } catch (Exception $e) {
        error_log('Error processing image upload: ' . $e->getMessage());
        return null;
    }
}
```

## HTML Structure

### Cover Photo Section
```html
<div class="cover-photo-section">
    <!-- Cover Photo -->
    <div class="cover-photo" style="background-color: #f0f0f0; min-height: 300px; position: relative; overflow: hidden;">
        <img id="coverPhoto" src="" alt="Cover Photo" style="width: 100%; height: 100%; object-fit: cover; display: none;">
        <div class="cover-overlay" onclick="uploadCover()" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center; flex-direction: column; background-color: rgba(0,0,0,0.3); cursor: pointer;">
            <i class="fas fa-camera"></i>
            Change Cover Photo
        </div>
    </div>
    <input type="file" id="coverInput" accept="image/*" style="display:none" onchange="changeCoverImage(event)">
</div>
```

### Profile Photo Section
```html
<div class="profile-photo" style="width: 150px; height: 150px; border-radius: 50%; position: absolute; bottom: -75px; left: 30px; background-color: white; border: 4px solid white; overflow: hidden;">
    <img id="profilePhoto" src="" alt="Profile Photo" style="width: 100%; height: 100%; object-fit: cover; display: none;">
    <div class="profile-overlay" onclick="uploadProfileImage()" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center; flex-direction: column; background-color: rgba(0,0,0,0.3); cursor: pointer;">
        <i class="fas fa-camera" style="color: white;"></i>
        <span style="color: white; font-size: 11px; text-align: center;">Change Photo</span>
    </div>
</div>
<input type="file" id="profileInput" accept="image/*" style="display:none" onchange="changeProfileImage(event)">
```

## API Response Examples

### Successful Upload Response
```json
{
    "success": true,
    "message": "Profile updated successfully"
}
```

### Error Response
```json
{
    "success": false,
    "error": "Invalid profile photo file type"
}
```

### Get Profile Response (with saved images)
```json
{
    "success": true,
    "data": {
        "firstname": "John",
        "lastname": "Doe",
        "email": "john@example.com",
        "profile_photo": "data:image/jpeg;base64,iVBORw0KGgoAAAANS...",
        "cover_photo": "data:image/png;base64,iVBORw0KGgoAAAANS...",
        ...
    }
}
```

## Network Request Example

### FormData POST Request
```
POST /unipulse/public/user/profile/updateProfile HTTP/1.1
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW

------WebKitFormBoundary7MA4YWxkTrZu0gW
Content-Disposition: form-data; name="cover_photo"; filename="photo.jpg"
Content-Type: image/jpeg

[BINARY IMAGE DATA]
------WebKitFormBoundary7MA4YWxkTrZu0gW--
```

## Database Storage Example

### Stored in Database (LONGTEXT field)
```
data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAIBAQIBAQICAgICAgICAwUDAwwDAwwHAwMGAwMGBwcHBwcHBwcICQsJCAgKCAcHCg0KCgsMDAwMBwkODw0MDgL/2wBDAQICAgMDAwYDAwYMCAcIDAwIDAwIDAwMDAwIDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwL/wAARCAABAAEDAQADAREAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8VAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwCwAA8A/9k=
```
