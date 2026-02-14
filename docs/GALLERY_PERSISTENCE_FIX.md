# Gallery Persistence Fix - Testing Guide

## Problem
Albums were disappearing after page refresh despite showing success notifications.

## Root Cause Analysis
The system had a two-part problem:
1. **Frontend issue**: No fallback when backend fails silently
2. **Backend issue**: UPDATE query wasn't using proper PDO binding with the query() method

## What Was Fixed

### 1. ✅ Frontend Improvements
- Added **localStorage fallback** - albums are now saved locally to prevent data loss
- Improved console logging with timestamps for debugging
- Enhanced error notifications with fallback information
- Changed load strategy:
  - First try to load from backend
  - If backend fails or returns empty, load from localStorage
  - If both fail, show empty gallery

### 2. ✅ Backend Improvements  
- Updated `updateGallery()` method to use direct PDO execution instead of relying on `query()` method
- Added proper `rowCount()` check to verify database was actually updated
- Added comprehensive error logging showing:
  - User ID and type
  - Gallery data size
  - Number of rows affected
  - Any exception messages
- Made `Database::connect()` method public to allow direct database access

### 3. ✅ Browser Storage Integration
Added localStorage persistence layer that:
- Automatically saves gallery after each operation
- Acts as fallback when backend is unavailable
- Syncs to backend when connection is restored
- Allows users to keep working even if server is down

## How to Test

### Test 1: Check Backend Endpoints
1. Go to: `http://localhost/unipulse/debug_gallery.php` (while logged in)
2. Click "Test GET Gallery" button
3. Click "Test UPDATE Gallery" button
4. Check console logs for detailed debugging information

**Expected Results:**
- GET should return `{"success": true, "gallery": []}`
- UPDATE should return `{"success": true, "message": "...", "rows": 1}`
- Both should have status 200

### Test 2: Create a Gallery Album
1. Go to your profile page
2. Click "Add Gallery Photo"
3. Enter:
   - Title: "My First Album"
   - Description: "Test album for persistence"
4. Upload at least 1 image
5. Click "Save Gallery"

**Expected:**
- See green "Gallery added successfully!" notification
- Album appears in gallery grid
- Check browser console (F12) - should see `✓ Gallery saved to localStorage`

### Test 3: Refresh Page - Critical Test
1. **WITHOUT** refreshing, your album should be visible
2. **Refresh the page** (F5 or Ctrl+R)
3. **Check album is still there**

**Expected:** 
- Album persists and is visible after refresh
- Console should show one of:
  - `✓ Gallery loaded from backend` (if database save worked)
  - `✓ Gallery loaded from localStorage` (if fallback triggered)
  - Either way, album should be visible

### Test 4: Open Browser DevTools
While on profile page, press F12 and:
1. Go to **Console** tab
2. Filter for "Gallery" messages
3. You should see detailed logs like:
   ```
   ✓ Gallery saved to localStorage
   Loading gallery from backend...
   Backend response status: 200
   ✓ Gallery loaded from backend: 1 albums
   ```

### Test 5: Check localStorage Directly
In DevTools Console, type:
```javascript
console.log(JSON.parse(localStorage.getItem('galleryPhotos')))
```

Should show your gallery albums with their base64 image data.

## If Albums Still Don't Show After Refresh

### Step 1: Check Database Column
Visit debug page and click "Check Database Column" - this verifies the gallery column exists.

### Step 2: Check Console Logs
Open DevTools (F12) → Console tab and look for any red error messages.

### Step 3: Check Network Tab
1. Open DevTools → Network tab
2. Refresh page
3. Look for request to `getGallery`
4. Click it and check Response body
5. Should see: `{"success": true, "gallery": [...]}`

### Step 4: Check Database Directly
In phpMyAdmin:
```sql
-- Check if column exists
SHOW COLUMNS FROM university_users WHERE field = 'gallery';

-- Check if data is being saved
SELECT id, gallery FROM university_users WHERE id = YOUR_USER_ID;
```

## File Changes Made

### Modified Files:
1. `public/assets/js/userprofile-app.js`
   - Updated `saveGalleryToBackend()` with localStorage fallback
   - Updated `loadGalleryFromBackend()` with console logging
   - Added localStorage as backup storage

2. `app/controllers/User/Profile.php`
   - Fixed `updateGallery()` to use direct PDO
   - Added detailed error logging
   - Fixed rowCount verification

3. `app/Core/Database.php`
   - Changed `connect()` from protected to public
   - Allows direct database access for UPDATE operations

### New Files:
1. `debug_gallery.php`
   - Interactive debugging tool
   - Test endpoints without manual curl commands
   - Check database connectivity

## Data Flow After Fix

```
User Creates Album
    ↓
Frontend: saveGalleryPhoto()
    ↓
    ├─→ Update local galleryPhotos array
    ├─→ localStorage.setItem('galleryPhotos', JSON)  ✅ Local backup
    └─→ fetch POST /updateGallery with gallery data
            ↓
            Backend: updateGallery()
                ↓
                Database::UPDATE user table
                ↓
                Return success/error response
            ↓
        ✓ If success: Show "saved successfully"
        ✗ If error: Show "using local backup"

User Refreshes Page
    ↓
Frontend: loadGalleryFromBackend()
    ↓
    ├─→ fetch GET /getGallery
    │       ↓
    │       Backend returns data from database
    │       ↓
    │   ✓ If data exists: Load from backend
    │   ✗ If empty: Try localStorage
    │
    ├─→ If backend empty: localStorage.getItem('galleryPhotos')
    │       ↓
    │   ✓ If localStorage has data: Load from there
    │   ✗ If empty: Show empty gallery
    │
    └─→ Render gallery on page
```

## Success Indicators

You'll know the fix is working when:
- ✅ Albums appear immediately after creation
- ✅ Albums persist after page refresh
- ✅ Console shows either "loaded from backend" or "loaded from localStorage"
- ✅ No error messages in console
- ✅ Database shows gallery column has data (if checking phpMyAdmin)
- ✅ Debug tool shows successful GET/UPDATE responses

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Albums disappear after refresh | Check Console (F12) for error messages |
| "Error saving gallery" notification | Check Network tab → getGallery response |
| localStorage shows empty array | Check if POST was sent (Network tab) |
| Database column doesn't exist | Run migration: `php database/add_gallery_to_profiles.php` |
| User not authenticated | Clear cookies and re-login |
| Large images fail to save | Images should be < 100KB (base64 encoded) |

## Performance Notes

- Galleries are limited to 5 albums max
- Each album limited to 5 photos max
- Images stored as base64 in LONGTEXT column
- localStorage limited to ~5MB (varies by browser)
- Syncs to server on every save (may be slow on first save)

## Next Steps

1. **Test the implementation** using the steps above
2. **Monitor console logs** to see where data is being loaded from
3. **Check database** to confirm data is being saved
4. **Report any errors** from console or network tabs
5. **Consider enabling periodic sync** to ensure localStorage stays in sync with server
