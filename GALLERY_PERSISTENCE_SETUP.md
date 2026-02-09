# Gallery Persistence Setup - Complete Guide

## ✅ What's Been Fixed

Your gallery albums will now **persist after page refresh** with full support for:
- 📥 **Loading** saved albums on page load
- 💾 **Saving** new albums automatically
- ✏️ **Editing** existing albums with auto-save
- 🗑️ **Deleting** albums with immediate backend sync

---

## 🔧 Setup Required (One-Time)

### Step 1: Add Database Column

You need to add a `gallery` column to the `user_profiles` table. Choose **ONE** method:

#### Method A: Using phpMyAdmin (Easiest)
1. Open **phpMyAdmin** (usually at http://localhost/phpmyadmin)
2. Select your database (likely `unipulse`)
3. Click on the `user_profiles` table
4. Click the **SQL** tab
5. Copy and paste this SQL:
   ```sql
   ALTER TABLE user_profiles 
   ADD COLUMN gallery TEXT NULL 
   COMMENT 'User photo gallery albums stored as JSON';
   ```
6. Click **Go**
7. ✅ Done!

#### Method B: Using MySQL Command Line
1. Open MySQL command line
2. Select your database:
   ```sql
   USE unipulse;
   ```
3. Run the SQL:
   ```sql
   ALTER TABLE user_profiles 
   ADD COLUMN gallery TEXT NULL 
   COMMENT 'User photo gallery albums stored as JSON';
   ```
4. ✅ Done!

#### Method C: Run the SQL File
The SQL file has been created at:
**`c:\wamp64\www\UniPulse\database\add_gallery_column.sql`**

Open it in phpMyAdmin's SQL tab or import it.

---

## 📝 What Was Changed

### Backend (PHP)
**File:** `app/controllers/User/Profile.php`

Added two new methods:
- **`getGallery()`** - Loads user's saved gallery albums
- **`updateGallery()`** - Saves gallery albums to database

### Frontend (JavaScript)
**File:** `public/assets/js/userprofile-app.js`

- ✅ Enabled `loadGalleryFromBackend()` on page load
- ✅ Already calls `saveGalleryToBackend()` after add/edit/delete

### Database
**Table:** `user_profiles`
- ✅ New column: `gallery` (TEXT, NULL) - Stores albums as JSON

---

## 🧪 Test It

### Test 1: Create and Persist Album
1. Open your profile page
2. Click **"Add Album"**
3. Fill in title, description, upload images
4. Click **"Save Gallery"**
5. ✅ See success notification
6. **Refresh the page (F5)**
7. ✅ **Your album should still be there!**

### Test 2: Edit Persists
1. Click **edit icon** on an album
2. Change the title
3. Click **"Save Gallery"**
4. **Refresh the page**
5. ✅ **Changes should be saved!**

### Test 3: Delete Persists
1. Click **trash icon** on an album
2. Confirm deletion
3. **Refresh the page**
4. ✅ **Album should remain deleted!**

---

## 🔍 Troubleshooting

### Issue: Albums still disappear after refresh

**Check Console for Errors:**
1. Press **F12** to open DevTools
2. Go to **Console** tab
3. Refresh the page
4. Look for errors related to:
   - `/unipulse/public/user/profile/getGallery`
   - `/unipulse/public/user/profile/updateGallery`

**Common Solutions:**

#### Error: "Column 'gallery' not found"
→ Run the database migration (Step 1 above)

#### Error: "404 Not Found" on gallery endpoints
→ Check that the Profile controller methods exist at:
   `app/controllers/User/Profile.php`

#### Error: "Not authenticated"
→ Make sure you're logged in
→ Check session is active

### Issue: Can't save albums (save fails silently)

**Check Database Connection:**
1. Open browser console (F12)
2. Look for network errors
3. Check the **Network** tab for failed requests

**Check Database Write Permissions:**
- Ensure the database user has UPDATE/INSERT permissions
- Check `user_profiles` table exists

---

## 📊 Data Structure

Gallery albums are stored as JSON in the database:

```json
[
  {
    "id": 1705123456789,
    "title": "My Event Photos",
    "description": "Great memories from the event",
    "images": [
      "data:image/jpeg;base64,...",
      "data:image/jpeg;base64,...",
      "data:image/jpeg;base64,..."
    ]
  },
  {
    "id": 1705123456790,
    "title": "Another Album",
    "description": "More photos",
    "images": ["data:image/jpeg;base64,..."]
  }
]
```

---

## 🎯 API Endpoints

### GET /unipulse/public/user/profile/getGallery
**Response:**
```json
{
  "success": true,
  "gallery": [...]
}
```

### POST /unipulse/public/user/profile/updateGallery
**Request:**
```json
{
  "gallery": [...]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Gallery updated successfully"
}
```

---

## ✨ Features Now Working

✅ **Auto-Load:** Albums load automatically on page load  
✅ **Auto-Save:** Changes save immediately to database  
✅ **Full CRUD:** Create, Read, Update, Delete all persist  
✅ **Multi-Photo:** Support for 1-5 photos per album  
✅ **Max Capacity:** Up to 5 albums per user  
✅ **Edit Friendly:** Edit title, description, or photos anytime  
✅ **Delete Safe:** Delete removes from database immediately  

---

## 🚀 Next Steps

1. ✅ Run the database migration (add gallery column)
2. ✅ Refresh your profile page
3. ✅ Create a test album
4. ✅ Refresh and verify it persists
5. 🎉 Start adding your real photo albums!

---

## ⚠️ Important Notes

- Images are stored as **base64** strings in the database
- This is simple but **not optimal for production** (large database)
- For production, consider:
  - Storing images as files on server
  - Saving only file paths in database
  - Using image optimization/compression
  - Implementing CDN for image serving

---

**You're all set! Your gallery albums will now survive page refreshes! 🎉**
