# Publisher Profile Backend - Quick Start Guide

## 🚀 Quick Setup (5 minutes)

### Step 1: Run Database Migration
```bash
php database/create_publisher_profiles_table.php
```
✅ Creates `publisher_profiles` table

### Step 2: Verify Files Updated
- ✅ `app/controllers/Publisher/Profile.php` - Backend API
- ✅ `app/models/Publisher.php` - Database operations
- ✅ `app/views/Publisher/profile.view.php` - Data passing
- ✅ `public/assets/js/publisherprofie-app.js` - Frontend

### Step 3: Test
Open: `test_publisher_profile_backend.html`

---

## 📋 API Endpoints Cheat Sheet

### GET /publisher/profile
Loads profile page with data

### GET /publisher/profile/getProfileData  
Returns JSON with all profile data

### POST /publisher/profile/updateOrganizationInfo
Updates org details (name, type, address, etc.)

### POST /publisher/profile/updateSocialLinks
Updates social media URLs

### POST /publisher/profile/uploadProfileImage
Uploads logo (multipart/form-data)

### POST /publisher/profile/uploadCoverPhoto
Uploads cover photo (multipart/form-data)

---

## 🔑 Key Features

✅ **Auto-create profiles** - First visit creates empty profile  
✅ **Live data loading** - Form auto-populated from database  
✅ **AJAX updates** - No page refresh needed  
✅ **Image uploads** - Logo & cover photo support  
✅ **Notifications** - User feedback for all actions  
✅ **Authentication** - Publisher-only access  
✅ **Validation** - File type & size checks  

---

## 📊 Database Fields

### Basic Info (publishers table)
- society_name, email, phone
- university, faculty
- approval_status, is_active

### Profile Data (publisher_profiles table)
- org_type, address, established_year, member_count
- headline, bio, mission
- website, facebook, instagram, linkedin, twitter, discord, youtube
- logo_url, cover_photo_url

---

## 🧪 Testing Checklist

- [ ] Login as publisher
- [ ] Open profile page - data loads
- [ ] Edit organization info - saves successfully
- [ ] Update social links - saves successfully  
- [ ] Upload logo - appears in profile
- [ ] Upload cover - appears in profile
- [ ] Logout/login - data persists

---

## ⚠️ Common Issues

**"Unauthorized" error**
→ Login as publisher account

**Data not saving**
→ Check browser console & network tab

**Images not uploading**
→ Check directory permissions: `chmod 755 public/uploads/publisher_images`

**Profile data empty**
→ Profile auto-created on first visit, normal behavior

---

## 🔧 Adding New Fields

1. Add to database:
```sql
ALTER TABLE publisher_profiles ADD COLUMN new_field VARCHAR(255);
```

2. Update `updateProfileData()` allowedFields:
```php
$allowedFields = [..., 'new_field'];
```

3. Add to form (profile.view.php):
```html
<input type="text" id="newField">
```

4. Add to loadUserData mapping:
```javascript
'new_field': 'newField'
```

---

## 📱 Frontend Usage

### Load data automatically:
```javascript
// Done automatically on page load
// Uses global publisherData variable
```

### Save data:
```javascript
// User clicks save button
// Calls saveOrganizationInfo() or saveSocialLinks()
// Makes fetch() request to backend
// Shows notification
```

### Upload image:
```javascript
// User selects file
// Calls upload function
// Sends FormData to backend
// Updates UI with new image URL
```

---

## 🎯 Next Steps

1. ✅ Test all endpoints with test file
2. ✅ Verify data persists after logout/login
3. ✅ Upload sample images to test upload
4. ✅ Check error logs for any issues
5. ⬜ Customize fields as needed
6. ⬜ Add validation rules
7. ⬜ Implement additional features

---

## 📞 Quick Troubleshooting

```bash
# Check if table exists
mysql> SHOW TABLES LIKE 'publisher_profiles';

# Check directory permissions
ls -la public/uploads/

# Check PHP errors
tail -f /path/to/error.log

# Test API endpoint
curl http://localhost/unipulse/public/publisher/profile/getProfileData
```

---

## 🎨 Customization Points

- Form fields in `profile.view.php`
- Validation rules in `Publisher.php`
- UI notifications in `publisherprofie-app.js`
- Upload constraints (file size, types)
- Additional API endpoints as needed

---

**Done!** Your publisher profile backend is complete and ready to use! 🎉
