# Profile Photo Auto-Save Feature

## Overview
This feature enables automatic saving of cover photos and profile photos when users upload them. The changes persist after page refresh.

## Features Implemented

### 1. Frontend (JavaScript) - `/public/assets/js/userprofile-app.js`
- **Image Upload Functions:**
  - `changeCoverImage(event)` - Handles cover photo selection and upload
  - `changeProfileImage(event)` - Handles profile photo selection and upload
  - `uploadCover()` - Triggers cover photo file input
  - `uploadProfileImage()` - Triggers profile photo file input

- **Image Saving Functions:**
  - `saveCoverImageFormData(file)` - Sends cover photo via FormData
  - `saveProfileImageFormData(file)` - Sends profile photo via FormData
  - `showImageUploadStatus()` - Shows user feedback for upload status

- **Validation:**
  - File type validation (JPEG, PNG, GIF, WebP)
  - File size validation (max 5MB)
  - User feedback with status messages (saving, success, error)

### 2. Backend (PHP) - `/app/controllers/User/Profile.php`

#### Updated `updateProfile()` method:
- Handles both JSON and FormData (multipart/form-data) requests
- Supports file uploads for profile and cover photos
- Processes uploaded files and converts them to base64 for storage
- Returns appropriate success/error messages

#### New Helper Methods:
- `isValidImageFile($file)` - Validates uploaded image files
  - Checks MIME type
  - Validates file extension
  - Enforces 5MB size limit

- `processImageUpload($file)` - Processes uploaded images
  - Reads file content
  - Converts to base64 format
  - Returns data URI string for storage

### 3. Frontend View - `/app/views/User/profile.view.php`
- Cover photo section with click-to-upload overlay
- Profile photo section with circular display and click-to-upload overlay
- Hidden file inputs for both photos
- Automatic display of uploaded images before saving

### 4. Data Persistence
- **Storage:** Images are stored in database as base64 data URIs
- **Database Fields:**
  - `profile_photo` in university_users/public_users tables
  - `cover_photo` in university_users/public_users tables
- **Loading:** Images are automatically loaded from the API on page load via `getProfile()`

## User Experience Flow

### Uploading a Photo:
1. User clicks on cover/profile photo area
2. File picker dialog opens
3. User selects an image file
4. Image preview displays immediately
5. System shows "Saving..." status message
6. Image is uploaded and processed by server
7. Success notification appears
8. Change is persisted in database

### After Page Refresh:
1. Page loads
2. `getProfile()` API is called on page load
3. Saved images are retrieved from database
4. Images are displayed in their respective areas
5. User sees their previously saved photos

## Technical Details

### Image Format:
- Images are converted to base64 data URIs
- Format: `data:image/jpeg;base64,...`
- Stored in LONGTEXT field in database

### File Validation:
- **Allowed Types:** JPEG, PNG, GIF, WebP
- **Maximum Size:** 5MB
- **Validation Method:** MIME type verification + extension check

### API Endpoints:
- **GET:** `/unipulse/public/user/profile/getProfile` - Retrieves profile data with images
- **POST:** `/unipulse/public/user/profile/updateProfile` - Updates profile including photos

## Error Handling
- Invalid file type error message
- File size exceeded error message
- Processing error handling
- Server-side validation errors returned to user
- Network error handling with user feedback

## Browser Compatibility
- Works with modern browsers supporting:
  - FormData API
  - FileReader API
  - Fetch API
  - Base64 encoding

## Security Considerations
- File type validation on both client and server
- MIME type verification using finfo functions
- File size limits enforced
- Only authenticated users can update their profiles
- Images stored as base64 to prevent file system vulnerabilities
