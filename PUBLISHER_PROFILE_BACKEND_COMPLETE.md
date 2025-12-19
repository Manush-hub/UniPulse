# Publisher/Organizer Profile Backend Implementation

## Overview
Comprehensive backend implementation for the publisher/organizer profile management system in UniPulse. This implementation includes database operations, API endpoints, authentication, and frontend integration.

**Implementation Date:** December 18, 2025

## Architecture

### Database Structure

#### Tables Created:
1. **publishers** (existing - from create_publishers_table.php)
   - Core publisher information (name, email, phone, university, faculty)
   - Approval status and authentication

2. **publisher_profiles** (new - from create_publisher_profiles_table.php)
   - Extended profile data
   - Organization details
   - Social media links
   - Profile images

### Backend Components

#### 1. Publisher Model (`app/models/Publisher.php`)

**New Methods Added:**
- `getProfileData($publisherId)` - Fetches profile data, creates if doesn't exist
- `createEmptyProfile($publisherId)` - Creates default profile entry
- `updateBasicInfo($publisherId, $data)` - Updates core publisher info
- `updateProfileData($publisherId, $data)` - Updates extended profile data
- `uploadImage($file, $type, $publisherId)` - Handles image uploads

**Features:**
- Automatic profile creation on first access
- Safe field validation and sanitization
- Transaction support for data integrity
- Error logging for debugging

#### 2. Profile Controller (`app/controllers/Publisher/Profile.php`)

**Endpoints Implemented:**

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/publisher/profile` | GET | Display profile page with data |
| `/publisher/profile/getProfileData` | GET | API: Fetch profile data |
| `/publisher/profile/updateOrganizationInfo` | POST | API: Update organization details |
| `/publisher/profile/updateSocialLinks` | POST | API: Update social media links |
| `/publisher/profile/uploadProfileImage` | POST | API: Upload logo image |
| `/publisher/profile/uploadCoverPhoto` | POST | API: Upload cover photo |

**Security Features:**
- Authentication check on every request
- Publisher-type validation
- Session-based user identification
- Unauthorized access protection

#### 3. Frontend Integration (`public/assets/js/publisherprofie-app.js`)

**Updated Functions:**
- `loadUserData()` - Loads data from PHP into form fields
- `saveOrganizationInfo()` - AJAX call to update organization data
- `saveSocialLinks()` - AJAX call to update social links
- `showNotification(message, type)` - User feedback system

**Data Flow:**
```
Page Load → PHP fetches data → Pass to JavaScript → Populate forms
User Edit → JavaScript collects data → AJAX to backend → Update database → Notify user
```

## Database Schema

### publisher_profiles Table

```sql
CREATE TABLE publisher_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    publisher_id INT NOT NULL UNIQUE,
    org_type VARCHAR(50) NULL,
    address TEXT NULL,
    established_year INT NULL,
    member_count INT NULL,
    headline VARCHAR(255) NULL,
    bio TEXT NULL,
    mission TEXT NULL,
    website VARCHAR(255) NULL,
    facebook VARCHAR(255) NULL,
    instagram VARCHAR(255) NULL,
    linkedin VARCHAR(255) NULL,
    twitter VARCHAR(255) NULL,
    discord VARCHAR(255) NULL,
    youtube VARCHAR(255) NULL,
    logo_url VARCHAR(500) NULL,
    cover_photo_url VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (publisher_id) REFERENCES publishers(id) ON DELETE CASCADE
);
```

## API Documentation

### 1. Get Profile Data

**Endpoint:** `GET /publisher/profile/getProfileData`

**Authentication:** Required (Publisher only)

**Response:**
```json
{
    "success": true,
    "data": {
        "publisher": {
            "id": 1,
            "society_name": "Tech Society",
            "email": "tech@example.com",
            "phone": "0771234567",
            "university": "University Name",
            "faculty": "Engineering"
        },
        "profile": {
            "org_type": "student-org",
            "address": "123 Main St",
            "established_year": 2020,
            "member_count": 150,
            "headline": "Leading tech organization",
            "bio": "We focus on innovation...",
            "mission": "To foster...",
            "website": "https://example.com",
            "facebook": "https://facebook.com/...",
            "logo_url": "/uploads/...",
            "cover_photo_url": "/uploads/..."
        }
    }
}
```

### 2. Update Organization Info

**Endpoint:** `POST /publisher/profile/updateOrganizationInfo`

**Authentication:** Required (Publisher only)

**Request Body:**
```json
{
    "orgName": "New Organization Name",
    "orgType": "student-org",
    "university": "University Name",
    "faculty": "Faculty Name",
    "contactNumber": "0771234567",
    "address": "Full Address",
    "establishedYear": 2020,
    "memberCount": 150,
    "headline": "Short description",
    "bio": "Full biography",
    "mission": "Mission statement"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Organization information updated successfully"
}
```

### 3. Update Social Links

**Endpoint:** `POST /publisher/profile/updateSocialLinks`

**Authentication:** Required (Publisher only)

**Request Body:**
```json
{
    "website": "https://example.com",
    "facebook": "https://facebook.com/page",
    "instagram": "https://instagram.com/profile",
    "linkedin": "https://linkedin.com/company/name",
    "twitter": "https://twitter.com/handle",
    "discord": "https://discord.gg/invite",
    "youtube": "https://youtube.com/channel"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Social links updated successfully"
}
```

### 4. Upload Profile Image (Logo)

**Endpoint:** `POST /publisher/profile/uploadProfileImage`

**Authentication:** Required (Publisher only)

**Request:** Multipart form-data with 'image' field

**Response:**
```json
{
    "success": true,
    "message": "Logo uploaded successfully",
    "imageUrl": "/unipulse/public/uploads/publisher_images/1/logo_123456.jpg"
}
```

**Constraints:**
- Max file size: 5MB
- Allowed types: JPG, JPEG, PNG, GIF

### 5. Upload Cover Photo

**Endpoint:** `POST /publisher/profile/uploadCoverPhoto`

**Authentication:** Required (Publisher only)

**Request:** Multipart form-data with 'image' field

**Response:**
```json
{
    "success": true,
    "message": "Cover photo uploaded successfully",
    "imageUrl": "/unipulse/public/uploads/publisher_images/1/cover_123456.jpg"
}
```

## Installation & Setup

### 1. Run Database Migration

```bash
php database/create_publisher_profiles_table.php
```

This creates the `publisher_profiles` table with all necessary fields and relationships.

### 2. Create Upload Directory

The system automatically creates upload directories, but you can pre-create:

```bash
mkdir -p public/uploads/publisher_images
chmod 755 public/uploads/publisher_images
```

### 3. Verify Files

Ensure these files are in place:
- `app/controllers/Publisher/Profile.php`
- `app/models/Publisher.php` (updated)
- `app/views/Publisher/profile.view.php` (updated)
- `public/assets/js/publisherprofie-app.js` (updated)

## Testing

### Manual Testing

1. **Open test page:**
   - Navigate to: `http://localhost/UniPulse/test_publisher_profile_backend.html`
   - Login as a publisher first

2. **Test each endpoint:**
   - Get Profile Data
   - Update Organization Info
   - Update Social Links
   - Upload Profile Image
   - Upload Cover Photo

### Test Scenarios

1. **New Publisher (No Profile)**
   - System should auto-create empty profile
   - All fields should be empty/null
   - Can save data successfully

2. **Existing Publisher**
   - Should load existing data
   - Updates should persist
   - Images should replace old ones

3. **Authentication**
   - Non-logged-in users redirected to login
   - Non-publisher users get "Unauthorized" error
   - Publisher can only access their own data

4. **Data Validation**
   - Invalid image types rejected
   - Large files (>5MB) rejected
   - Required fields enforced

## Field Mapping

### Database to Form Fields

| Database Field | Form Field ID | Description |
|----------------|---------------|-------------|
| `society_name` | `orgName` | Organization name |
| `email` | `officialEmail` | Official email |
| `phone` | `contactNumber` | Contact number |
| `university` | `university` | University name |
| `faculty` | `faculty` | Faculty name |
| `org_type` | `orgType` | Organization type |
| `address` | `address` | Physical address |
| `established_year` | `establishedYear` | Year founded |
| `member_count` | `memberCount` | Number of members |
| `headline` | `headline` | Short headline |
| `bio` | `bio` | Full biography |
| `mission` | `mission` | Mission statement |
| `website` | `website` | Website URL |
| `facebook` | `facebook` | Facebook URL |
| `instagram` | `instagram` | Instagram URL |
| `linkedin` | `linkedin` | LinkedIn URL |
| `twitter` | `twitter` | Twitter URL |
| `discord` | `discord` | Discord invite |
| `youtube` | `youtube` | YouTube channel |

## Error Handling

### Common Errors

1. **"Unauthorized"**
   - Cause: Not logged in or not a publisher
   - Solution: Login as publisher account

2. **"Publisher profile not found"**
   - Cause: Database query failed
   - Solution: Check database connection

3. **"No image uploaded"**
   - Cause: File not in request
   - Solution: Ensure file input has name="image"

4. **"Failed to upload"**
   - Cause: File system permissions or invalid file
   - Solution: Check directory permissions and file type

## Security Considerations

### Implemented Security Features

1. **Authentication Check**
   - Every endpoint verifies user is logged in
   - User type must be 'publisher'

2. **Data Ownership**
   - Publishers can only modify their own data
   - User ID from session, not from request

3. **File Upload Safety**
   - File type validation
   - File size limits
   - Unique filename generation
   - Isolated directory per publisher

4. **SQL Injection Prevention**
   - Prepared statements throughout
   - Parameter binding for all queries

5. **XSS Prevention**
   - JSON encoding for data passing
   - No direct echo of user input

### Recommendations

1. Add CSRF token validation
2. Implement rate limiting
3. Add image content validation (not just extension)
4. Log all profile changes for audit
5. Add email verification for email changes

## File Structure

```
app/
├── controllers/
│   └── Publisher/
│       └── Profile.php (Complete backend logic)
├── models/
│   └── Publisher.php (Updated with profile methods)
└── views/
    └── Publisher/
        └── profile.view.php (Updated with data passing)

database/
└── create_publisher_profiles_table.php (Migration script)

public/
├── assets/
│   └── js/
│       └── publisherprofie-app.js (Updated with AJAX)
└── uploads/
    └── publisher_images/ (Auto-created)
        └── {publisher_id}/
            ├── logo_*.jpg
            └── cover_*.jpg

test_publisher_profile_backend.html (Testing interface)
```

## Usage Examples

### Loading Data on Page Load

```javascript
// Automatic on page load
// publisherData global variable populated by PHP
// loadUserData() called in init()
```

### Saving Organization Info

```javascript
// User clicks "Save Changes" button
saveOrganizationInfo() {
    // Collects form data
    // Makes AJAX POST request
    // Shows notification
    // Updates UI
}
```

### Uploading Images

```javascript
// User selects file and triggers upload
fetch('/publisher/profile/uploadProfileImage', {
    method: 'POST',
    body: formData
})
```

## Maintenance

### Regular Tasks

1. **Monitor upload directory size**
   - Old images not automatically deleted
   - Consider cleanup script for old files

2. **Database backups**
   - Include publisher_profiles table
   - Test restore procedures

3. **Log review**
   - Check error_log for issues
   - Monitor failed upload attempts

### Updates & Extensions

**To add new profile fields:**

1. Add column to `publisher_profiles` table
2. Add field to `$allowedFields` in `updateProfileData()`
3. Add field to form in `profile.view.php`
4. Add field to `$publisherJson` array in controller
5. Add mapping in `loadUserData()` JavaScript

## Troubleshooting

### Data Not Saving

1. Check browser console for JavaScript errors
2. Check network tab for API response
3. Check PHP error log
4. Verify database connection
5. Check file permissions on upload directory

### Images Not Uploading

1. Verify directory exists and is writable
2. Check file size (must be < 5MB)
3. Verify file type (JPG, PNG, GIF only)
4. Check PHP `upload_max_filesize` setting
5. Check disk space

### Data Not Loading

1. Verify publisher is logged in
2. Check if profile record exists
3. Verify foreign key relationship
4. Check JavaScript console for errors
5. Verify `publisherData` variable is set

## Support & Contact

For issues or questions:
1. Check error logs in browser and server
2. Review this documentation
3. Test with test_publisher_profile_backend.html
4. Verify all files are updated correctly

## Changelog

### Version 1.0 (December 18, 2025)
- Initial implementation
- Complete CRUD operations for publisher profiles
- Image upload functionality
- Frontend-backend integration
- Authentication and authorization
- Error handling and validation
- Test interface
