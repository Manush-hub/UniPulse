# Recent Activity Feature Implementation Guide

## Overview
This implementation adds a "Recent Activity" section to the user dashboard that automatically tracks:
1. **Event Registrations** - When a user registers for an event
2. **Volunteer Applications** - When a user applies as a volunteer
3. **Event Cancellations** - When a user cancels their event registration

All activities are displayed for **7 days (1 week)** in the dashboard's Recent Activity section.

---

## Files Created/Modified

### 1. **Database Migrations**

#### `database/create_user_activities.php` (NEW)
- Creates the `user_activities` table
- Tracks all user activities with automatic expiration
- **Run this first** after downloading

#### `database/create_volunteer_registrations.php` (NEW)
- Creates the `volunteer_registrations` table
- Stores volunteer registration details
- Required for volunteer activity tracking

### 2. **Models**

#### `app/models/Activity.php` (NEW)
Core model for activity management:
- `getRecentActivities()` - Fetch recent activities for a user (last 7 days)
- `logActivity()` - Log a new activity
- `deleteExpiredActivities()` - Cleanup expired activities
- `formatActivityForDisplay()` - Format activities for JSON response

#### `app/models/VolunteerRegistration.php` (NEW)
Manages volunteer registrations:
- `isUserRegistered()` - Check if user is already a volunteer
- `getRegistration()` - Get specific volunteer registration
- `updateStatus()` - Update volunteer status (pending, accepted, rejected, withdrawn)

#### `app/models/EventRegistration.php` (MODIFIED)
Updated to automatically log activities:
- `registerUser()` - Now logs event registration activity
- `cancelRegistration()` - Now logs cancellation activity
- New private methods: `logRegistrationActivity()`, `logCancellationActivity()`

### 3. **Controllers**

#### `app/controllers/User/Dashboard.php` (MODIFIED)
Updated `getRecentActivity()` method:
- Fetches activities from database instead of placeholder data
- Returns formatted activities with proper timestamps
- API endpoint: `/unipulse/public/user/dashboard/getRecentActivity`

#### `app/controllers/Volunteerreg.php` (COMPLETELY REWRITTEN)
Now handles volunteer registration form submission:
- Validates form data
- Saves volunteer registration to database
- Logs activity when volunteer registration is submitted
- Redirects on success

### 4. **Views**

#### `app/views/User/dashboard.view.php` (UNCHANGED)
- Already has the Recent Activity section structure
- No changes needed - uses existing HTML

### 5. **Assets**

#### `public/assets/css/User/dashboard-style.css` (UNCHANGED)
- Already has styles for `.recent-activity`, `.activity-item`, etc.
- No changes needed

#### `public/assets/js/User/dashboard-app.js` (UNCHANGED)
- Already calls `loadRecentActivity()`
- Already has `displayRecentActivity()` function
- No changes needed

---

## Installation Steps

### Step 1: Run Database Migrations
Execute these migration scripts in order:

```bash
# Using PHP CLI from UniPulse root directory
php database/create_user_activities.php
php database/create_volunteer_registrations.php
```

Or manually run these SQL queries in your database:

**For user_activities table:**
```sql
CREATE TABLE IF NOT EXISTS user_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_type ENUM('university', 'public', 'publisher', 'sponsor') NOT NULL,
    activity_type ENUM('event_registration', 'volunteer_registration', 'event_cancellation', 'profile_update', 'badge_earned') NOT NULL,
    event_id INT NULL,
    event_title VARCHAR(255) NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    icon VARCHAR(50) DEFAULT 'calendar',
    activity_data JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_activity_type (activity_type),
    INDEX idx_created_at (created_at),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**For volunteer_registrations table:**
```sql
CREATE TABLE IF NOT EXISTS volunteer_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_type ENUM('university', 'public', 'publisher', 'sponsor') NOT NULL,
    event_id INT NOT NULL,
    volunteer_position VARCHAR(255) NOT NULL,
    availability VARCHAR(100) NOT NULL,
    experience TEXT NOT NULL,
    motivation TEXT NOT NULL,
    skills TEXT NOT NULL,
    have_transportation TINYINT(1) DEFAULT 0,
    commitment_understanding TINYINT(1) DEFAULT 0,
    receive_updates TINYINT(1) DEFAULT 0,
    terms_accepted TINYINT(1) DEFAULT 1,
    status ENUM('pending', 'accepted', 'rejected', 'withdrawn') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_volunteer_registration (event_id, user_id, user_type),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Step 2: Copy/Update Files
All required files have been created and modified. No additional action needed if files were properly placed.

### Step 3: Verify Implementation
1. Open the user dashboard
2. Register for an event
3. Check the "Recent Activity" section - should show "Registered for [Event Name]"
4. Go to an event page and apply as a volunteer
5. Activity should appear as "Applied as volunteer for [Event Name]"

---

## Activity Types and Icons

| Activity Type | Icon | Description | Duration |
|---------------|------|-------------|----------|
| **event_registration** | plus | User registered for an event | 7 days |
| **volunteer_registration** | bell | User applied as a volunteer | 7 days |
| **event_cancellation** | calendar | User cancelled event registration | 7 days |
| **profile_update** | plus | User updated their profile | 7 days |
| **badge_earned** | award | User earned a badge/achievement | 30 days |

---

## How Activities Are Logged

### Event Registration Activity
**When:** User registers for an event through the event page
**Where:** `EventRegistration::registerUser()` method
**Data Logged:**
- Registration type (free/paid)
- Status
- Amount paid

### Volunteer Registration Activity
**When:** User completes volunteer registration form
**Where:** `Volunteerreg` controller handles form submission
**Data Logged:**
- Volunteer position
- Availability
- Status (pending)

### Event Cancellation Activity
**When:** User cancels their event registration
**Where:** `EventRegistration::cancelRegistration()` method
**Data Logged:**
- Cancellation reason

---

## API Endpoints

### Get Recent Activities
```
GET /unipulse/public/user/dashboard/getRecentActivity
```

**Response:**
```json
{
    "success": true,
    "activities": [
        {
            "id": 1,
            "title": "Registered for Tech Summit 2024",
            "description": "You registered for the event \"Tech Summit 2024\"",
            "icon": "plus",
            "time": "2 hours ago",
            "timestamp": "2024-02-06 10:30:00",
            "activityType": "event_registration",
            "eventId": 5,
            "eventTitle": "Tech Summit 2024"
        },
        ...
    ],
    "count": 5
}
```

---

## Database Schema Details

### `user_activities` Table
Columns:
- `id` - Primary key
- `user_id` - Associated user
- `user_type` - Type of user (university, public, etc.)
- `activity_type` - Type of activity
- `event_id` - Associated event (if applicable)
- `event_title` - Event title for context
- `title` - Display title (up to 255 chars)
- `description` - Detailed description
- `icon` - Icon type (calendar, plus, bell, award)
- `activity_data` - JSON data for additional details
- `created_at` - When activity occurred
- `expires_at` - When activity expires from "recent" list

### `volunteer_registrations` Table
Columns:
- `id` - Primary key
- `user_id` - Volunteer user ID
- `user_type` - User type
- `event_id` - Event they're volunteering for
- `volunteer_position` - Position applied for
- `availability` - Availability timeframe
- `experience` - Relevant experience
- `motivation` - Why they want to volunteer
- `skills` - Special skills
- `have_transportation` - Boolean flag
- `commitment_understanding` - Boolean flag
- `receive_updates` - Boolean flag
- `terms_accepted` - Boolean flag
- `status` - Current status (pending/accepted/rejected/withdrawn)
- `created_at` - Registration timestamp
- `updated_at` - Last update

---

## Testing the Feature

### Test Case 1: Event Registration Activity
1. Log in as a user
2. Navigate to an event page
3. Click "Register" or "Join Event"
4. Go to dashboard
5. Verify activity appears in "Recent Activity" section

### Test Case 2: Volunteer Registration Activity
1. Log in as a user
2. On event page, click "Apply as Volunteer"
3. Fill out the volunteer form
4. Submit the form
5. Go to dashboard
6. Verify volunteer activity appears

### Test Case 3: Activity Expiration
1. Verify activities older than 7 days don't appear
2. Check `expires_at` column in database

---

## Customization

### Change Activity Expiration Time
Edit `Activity::logActivity()` in `app/models/Activity.php`:

```php
// Default: 7 days
$expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));

// Change to different period (e.g., 30 days)
$expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
```

### Add New Activity Types
1. Update the `activity_type` ENUM in `user_activities` table
2. Add new case in `Activity::logActivity()` method
3. Add corresponding icon in JavaScript

### Modify Activity Display
Edit `public/assets/js/User/dashboard-app.js`:
- `createActivityItem()` - Change HTML structure
- `getActivityIcon()` - Add/modify icons
- `formatActivityForDisplay()` - Change formatting

---

## Troubleshooting

### Activities Not Appearing
1. Check tables exist: `user_activities`, `volunteer_registrations`
2. Verify Activity model is being loaded
3. Check browser console for errors
4. Check PHP error logs

### Activities Disappearing Too Quickly
- Adjust `expires_at` calculation in `Activity::logActivity()`
- Run database cleanup less frequently

### Volunteer Form Not Submitting
1. Verify `volunteer_registrations` table exists
2. Check `VolunteerRegistration` model is loaded in controller
3. Verify user is authenticated (check session)
4. Check error logs for specific error messages

---

## Files Summary

| File | Type | Status |
|------|------|--------|
| `database/create_user_activities.php` | Migration | NEW |
| `database/create_volunteer_registrations.php` | Migration | NEW |
| `app/models/Activity.php` | Model | NEW |
| `app/models/VolunteerRegistration.php` | Model | NEW |
| `app/models/EventRegistration.php` | Model | MODIFIED |
| `app/controllers/User/Dashboard.php` | Controller | MODIFIED |
| `app/controllers/Volunteerreg.php` | Controller | REWRITTEN |
| `app/views/User/dashboard.view.php` | View | UNCHANGED |
| `public/assets/css/User/dashboard-style.css` | CSS | UNCHANGED |
| `public/assets/js/User/dashboard-app.js` | JavaScript | UNCHANGED |

---

## Next Steps (Optional Features)

1. **Activity Cleanup Job** - Run periodic cleanup to delete expired activities
2. **Activity Filtering** - Allow users to filter by activity type
3. **Activity Notifications** - Send notifications when activities occur
4. **Activity Archive** - Keep archived activities beyond 7 days
5. **Activity Statistics** - Show activity summary/statistics

---

## Support

For issues or questions, refer to the code comments in:
- `app/models/Activity.php` - Core activity logic
- `app/controllers/User/Dashboard.php` - API endpoint
- `app/controllers/Volunteerreg.php` - Volunteer registration handling
