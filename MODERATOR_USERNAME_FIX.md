# Moderator Username Display Fix

## Issue
The moderator's full name from the database was only displaying correctly on the dashboard page. On all other moderator pages, it was showing the fallback text "Moderator" instead of the actual full_name from the moderators table.

## Root Cause
The controllers were using incorrect view paths when calling `$this->view()`. The MVC framework requires the full path (e.g., `Moderator/events`) for proper data passing to views.

**Incorrect paths caused the `$moderator` data to not reach the view files, triggering the fallback text.**

## Files Modified

### 1. `/app/controllers/Moderator/Events.php`
**Line 108**
```php
// BEFORE
$this->view('events', $data);

// AFTER
$this->view('Moderator/events', $data);
```

### 2. `/app/controllers/Moderator/Comments.php`
**Lines 44 and 55**
```php
// BEFORE
parent::view('comments_moderation', $data);

// AFTER
parent::view('Moderator/comments_moderation', $data);
```

## How It Works Now

### View Path Convention
All moderator controllers now use the correct path format:
```php
$this->view('Moderator/[page_name]', $data);
```

### Data Flow
1. **Controller fetches moderator data:**
   ```php
   $moderator = new Moderator();
   $moderatorData = $moderator->findById($currentUser['id']);
   ```

2. **Data passed to view:**
   ```php
   $data = [
       'moderator' => $moderatorData,
       'user' => $currentUser,
       // ... other data
   ];
   $this->view('Moderator/page_name', $data);
   ```

3. **Header component displays:**
   ```php
   <?= htmlspecialchars($moderator->full_name ?? 'Moderator') ?>
   ```

## Verified Controllers
All moderator controllers now correctly pass moderator data with proper view paths:

✅ `/app/controllers/Moderator/Dashboard.php` - Uses `Moderator/dashboard`
✅ `/app/controllers/Moderator/Events.php` - Uses `Moderator/events` (FIXED)
✅ `/app/controllers/Moderator/Comments.php` - Uses `Moderator/comments_moderation` (FIXED)
✅ `/app/controllers/Moderator/Contentmoderation.php` - Uses `Moderator/content_moderation`
✅ `/app/controllers/Moderator/Userreports.php` - Uses `Moderator/user_reports`
✅ `/app/controllers/Moderator.php`:
  - `events()` method - Uses `Moderator/events`
  - `publishers()` method - Uses `Moderator/publishers`
  - `publisherapproval()` method - Uses `Moderator/publisher_approval`
  - `comments_moderation()` method - Uses `Moderator/comments_moderation`

## View Files
All moderator view files correctly include the header component:

✅ `/app/views/Moderator/dashboard.view.php`
✅ `/app/views/Moderator/events.view.php`
✅ `/app/views/Moderator/comments_moderation.view.php`
✅ `/app/views/Moderator/content_moderation.view.php`
✅ `/app/views/Moderator/user_reports.view.php`
✅ `/app/views/Moderator/publisher_approval.view.php` (inline header, already correct)

## Testing
To verify the fix:

1. Log in as a moderator
2. Navigate to each page:
   - Dashboard: `http://localhost/unipulse/public/moderator/dashboard`
   - Events: `http://localhost/unipulse/public/moderator/events`
   - Content Moderation: `http://localhost/unipulse/public/moderator/contentmoderation`
   - User Reports: `http://localhost/unipulse/public/moderator/userreports`
   - Publisher Approval: `http://localhost/unipulse/public/moderator/publisherapproval`
   - Comments: `http://localhost/unipulse/public/moderator/comments_moderation`

3. Verify the header displays the moderator's actual full_name from the database (not "Moderator")

## Database Schema
The moderators table should have:
```sql
CREATE TABLE moderators (
    id INT PRIMARY KEY,
    full_name VARCHAR(255),
    email VARCHAR(255),
    university VARCHAR(255),
    -- other fields...
);
```

## Key Takeaway
**Always use the full view path in controllers** for the MVC framework to properly pass data arrays to view files:
```php
// ✅ CORRECT
$this->view('Moderator/page_name', $data);

// ❌ WRONG (data won't pass correctly)
$this->view('page_name', $data);
```
