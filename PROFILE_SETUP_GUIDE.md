# Profile CRUD Setup Guide

## Step-by-Step Implementation Guide

### 1. Database Preparation

Before the profile CRUD system works correctly, you need to run the database migrations to add the required fields.

#### Option A: Run via Web Browser
1. Open your browser and navigate to:
   - `http://localhost/unipulse/database/add_bio_field.php`
   - `http://localhost/unipulse/database/add_profile_images.php`

2. You should see output like:
   ```
   ✓ Added bio to university_users
   ✓ Added bio to public_users
   ✓ Added profile_photo to university_users
   ✓ Added cover_photo to university_users
   ✓ Added profile_photo to public_users
   ✓ Added cover_photo to public_users
   
   ✅ Migration completed successfully!
   ```

#### Option B: Run via Command Line
```bash
cd c:\wamp64\www\UniPulse
php database/add_bio_field.php
php database/add_profile_images.php
```

### 2. Verify the Implementation

After running migrations, test the profile CRUD functionality:

1. **Log into your account** with university user credentials
2. **Navigate to Profile Settings** from the dashboard/navigation
3. **Verify the form displays correctly:**
   - ✅ First Name - Editable input field
   - ✅ Last Name - Editable input field
   - ✅ Gender - Selectable buttons (Male/Female)
   - ✅ Phone Number - Editable input field
   - ✅ Bio - Editable textarea
   - ✅ University - Disabled/readonly with helper text
   - ✅ Faculty - Disabled/readonly with helper text
   - ✅ Student/Staff ID - Disabled/readonly with helper text
   - ✅ Email - Disabled/readonly with helper text
   - ✅ NIC - Disabled/readonly with helper text

### 3. Test All Functionality

#### Test Save Functionality:
1. Change First Name to "John"
2. Change Last Name to "Doe"
3. Select "Male" for Gender
4. Enter "0712345678" for Phone
5. Enter "Passionate developer" in Bio
6. Click **Save Changes**
7. ✅ Should see "Profile updated successfully!" message
8. Refresh the page - ✅ Changes should persist

#### Test Cancel Functionality:
1. Change any editable field
2. Click **Cancel** button
3. ✅ Fields should revert to original values
4. ✅ Should see "Changes cancelled" notification

#### Test Validation:
1. Try to save with First Name = "J" (less than 2 characters)
2. ✅ Should see error: "First Name must be at least 2 characters"
3. Try to save with Phone = "123" (invalid format)
4. ✅ Should see error: "Phone number format is invalid"
5. Try to save with empty Last Name
6. ✅ Should see error: "Last Name is required"

#### Test Read-only Fields:
1. Try to click on University field
2. ✅ Field should be disabled (cannot interact)
3. Try to click on Faculty field
4. ✅ Field should be disabled (cannot interact)
5. All read-only fields should show helper text

### 4. Data Flow Verification

**User Profile Edit Flow:**
```
Frontend (JavaScript)
    ↓ (collects editable fields only)
POST /unipulse/public/user/profile/updateProfile
    ↓ (sends firstname, lastname, gender, phone, bio)
Backend Controller (Profile.php)
    ↓ (validates fields)
    ↓ (prevents read-only field modification)
Model (UniversityUser/PublicUser)
    ↓ (updates database)
Database
    ↓ (stores changes)
Session
    ↓ (refreshes if name changed)
Frontend
    ↓ (displays success message)
User
```

### 5. API Endpoint Reference

#### Get Profile Data
```
GET /unipulse/public/user/profile/getProfile

Response:
{
  "success": true,
  "data": {
    "firstname": "John",
    "lastname": "Doe",
    "email": "john@university.edu",
    "phone": "+1-555-1234",
    "university": "Stanford University",
    "faculty": "Faculty of Engineering",
    "student_staff_id": "STU123456",
    "academic_year": "3rd Year",
    "gender": "male",
    "nic": "123456789V",
    "bio": "Passionate developer",
    ...
  }
}
```

#### Update Profile Data
```
POST /unipulse/public/user/profile/updateProfile

Request Body:
{
  "firstname": "John",
  "lastname": "Doe",
  "gender": "male",
  "phone": "+1-555-1234",
  "bio": "Passionate developer"
}

Response Success:
{
  "success": true
}

Response Error:
{
  "success": false,
  "errors": ["First Name is required", "Phone number format is invalid"]
}
```

### 6. File Locations

**Key Files Modified/Created:**
- Controller: `app/controllers/User/Profile.php`
- View: `app/views/User/profile.view.php`
- JavaScript: `public/assets/js/userprofile-app.js`
- Styling: `public/assets/css/User/profile-style.css`
- Database Migrations: `database/add_bio_field.php`, `database/add_profile_images.php`

### 7. Troubleshooting

**Issue: Fields are showing as editable when they should be readonly**
- Solution: Clear browser cache (Ctrl+Shift+Delete)
- Check that profile-style.css changes are saved

**Issue: Save button not working**
- Check browser console for errors (F12)
- Verify migration scripts have been run
- Check PHP error logs

**Issue: Changes not saving to database**
- Verify user is logged in (check session)
- Verify database migrations were successful
- Check database connection in config.php
- Check that user model has an update() method

**Issue: Validation errors**
- Check console logs for detailed error messages
- Verify phone number format (10-20 characters with numbers, +, -, spaces, parentheses)
- Ensure first/last name are at least 2 characters

### 8. Security Checklist

- ✅ Only authenticated users can access profile endpoint
- ✅ Server-side validation prevents invalid data
- ✅ Read-only fields cannot be modified via API
- ✅ User can only modify their own profile
- ✅ All input is properly sanitized
- ✅ Database fields use prepared statements (via PDO)

---

**Need Help?** 
Check the error messages in:
1. Browser Console (F12)
2. PHP Error Logs
3. Database Connection Settings in config.php
