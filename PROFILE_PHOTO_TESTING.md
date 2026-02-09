# Profile Photo Auto-Save Testing Guide

## Quick Test Checklist

### Prerequisites
- User must be logged in
- Navigate to user profile page: `/unipulse/public/user/profile`
- Make sure the API endpoint `/unipulse/public/user/profile/getProfile` is working

### Test Case 1: Cover Photo Upload
1. **Action:**
   - Click on the cover photo area (gray box at top)
   - Select an image file (JPG, PNG, GIF, or WebP)
   
2. **Expected Results:**
   - Image preview appears immediately in the cover area
   - "Saving cover photo..." message appears at top right
   - "Cover photo saved successfully!" message appears after upload
   - Image stays visible when refreshing the page

### Test Case 2: Profile Photo Upload
1. **Action:**
   - Click on the circular profile photo area (below cover)
   - Select an image file (JPG, PNG, GIF, or WebP)
   
2. **Expected Results:**
   - Image preview appears immediately in the circle
   - "Saving profile photo..." message appears at top right
   - "Profile photo saved successfully!" message appears after upload
   - Image stays visible when refreshing the page

### Test Case 3: Invalid File Type
1. **Action:**
   - Try to upload a non-image file (.txt, .pdf, etc.)
   
2. **Expected Results:**
   - Error message: "Please select a valid image file"

### Test Case 4: File Size Limit
1. **Action:**
   - Try to upload an image larger than 5MB
   
2. **Expected Results:**
   - Error message: "Image size must be less than 5MB"

### Test Case 5: Page Refresh Persistence
1. **Action:**
   - Upload cover photo
   - Wait for success message
   - Refresh the page (F5 or Ctrl+R)
   
2. **Expected Results:**
   - Cover photo is still visible after refresh
   - Profile photo is still visible after refresh
   - No upload needed again

### Test Case 6: Multiple Uploads
1. **Action:**
   - Upload cover photo #1
   - Wait for success
   - Upload different cover photo #2
   - Wait for success
   - Refresh page
   
2. **Expected Results:**
   - Latest cover photo #2 is displayed
   - Previous photo #1 is replaced

## Debug Information

### Browser Console Checks
Open Developer Tools (F12) and check Console for:
- `"Cover photo saved successfully"` - indicates successful upload
- `"Failed to save cover photo"` - indicates API error
- Error messages with details

### Network Tab Checks
Check Network tab for:
- POST request to `/unipulse/public/user/profile/updateProfile`
- Request payload contains FormData with the image file
- Response status: 200 OK
- Response JSON: `{"success": true, "message": "Profile updated successfully"}`

### Database Verification
To check if images are saved in database:
```sql
-- For university users
SELECT id, firstname, lastname, 
       SUBSTRING(profile_photo, 1, 50) as profile_photo_preview,
       SUBSTRING(cover_photo, 1, 50) as cover_photo_preview
FROM university_users 
WHERE id = {user_id};

-- For public users
SELECT id, firstname, lastname,
       SUBSTRING(profile_photo, 1, 50) as profile_photo_preview,
       SUBSTRING(cover_photo, 1, 50) as cover_photo_preview
FROM public_users 
WHERE id = {user_id};
```

## Common Issues & Solutions

### Issue: Image uploads but doesn't persist after refresh
**Solution:** 
- Check database connection
- Verify `profile_photo` and `cover_photo` columns exist in users table
- Run migration: `/database/add_profile_images.php`

### Issue: "Saving..." message never completes
**Solution:**
- Check browser console for errors
- Check Network tab for failed POST request
- Verify `/unipulse/public/user/profile/updateProfile` endpoint exists
- Check PHP error logs

### Issue: File validation error when uploading valid image
**Solution:**
- Ensure image is actual image format (not renamed file)
- Check file size is under 5MB
- Try different image format (JPG, PNG, etc.)

### Issue: CORS or Authentication errors
**Solution:**
- Ensure user is logged in
- Check browser console for authentication errors
- Verify session cookie is set properly
- Check if credentials: 'same-origin' is working

## Performance Considerations
- Image uploads should complete within 2-5 seconds for typical 5MB images
- Data URI format in database may increase database size
- Consider periodic cleanup of old images if needed

## Support
For issues, check:
1. PHP error logs
2. Browser console errors
3. Network request/response in Developer Tools
4. Database for actual stored data
