# Event Moderation System - Implementation Summary

## Overview
Implemented a complete moderation system that allows moderators to hide/soft-delete events from their university. Events are marked as deleted in the database but not permanently removed, and publishers receive notifications with the reason for deletion.

## Features Implemented

### 1. Database Migration
**File:** `/database/add_soft_delete_to_events.php`

Added columns to `events` table:
- `is_deleted` (BOOLEAN) - Flag to mark deleted events
- `deleted_at` (TIMESTAMP) - When the event was deleted
- `deleted_by` (INT) - Moderator ID who deleted the event
- `deletion_reason` (TEXT) - Reason provided by moderator
- Indexes for performance optimization

### 2. Event Model Updates
**File:** `/app/models/Event.php`

#### New Methods:
- `softDelete($eventId, $moderatorId, $reason)` - Soft delete an event
- `restore($eventId)` - Restore a deleted event
- `canModeratorModerateEvent($eventId, $moderatorUniversity)` - Check if moderator can moderate (same university check)
- `getEventWithPublisher($eventId)` - Get event with publisher details
- `notifyPublisherOfDeletion($eventId, $moderatorId, $reason)` - Notify publisher about deletion

#### Updated Query Methods:
All event retrieval methods now exclude soft-deleted events:
- `getAllEvents()` - Added `WHERE is_deleted = 0`
- `getEventsSeekingSponsors()` - Added `WHERE is_deleted = 0`
- `getEventsByRole()` - Added `WHERE is_deleted = 0`

### 3. Controller Methods
**File:** `/app/controllers/Moderator/Events.php`

#### New Endpoints:
- `hideEvent()` - POST endpoint to soft delete an event
  - URL: `/unipulse/public/moderator/events/hideEvent`
  - Validates moderator permissions (same university)
  - Requires deletion reason
  - Notifies publisher
  
- `restoreEvent()` - POST endpoint to restore a deleted event
  - URL: `/unipulse/public/moderator/events/restoreEvent`
  - Admin/Moderator only

### 4. Frontend Implementation
**File:** `/public/assets/js/Moderator/events-app.js`

#### New Functions:
- `showHideEventModal(eventId, eventTitle)` - Display deletion reason modal
- `closeHideEventModal()` - Close the modal
- `confirmHideEvent(eventId)` - Submit deletion request to backend

#### Features:
- Modal dialog with textarea for deletion reason
- Character counter (500 max)
- Form validation (minimum 10 characters)
- Success/error notifications
- Auto-refresh event list after deletion

### 5. Updated Event Cards
Event cards now include:
- "Hide Event" button (red danger button)
- "View Details" button (blue primary button)
- Action buttons at the bottom of each card

### 6. CSS Styling
**File:** `/public/assets/css/Moderator/events-style.css`

Added styles for:
- Event action buttons
- Modal overlay and content
- Form elements (textarea, labels)
- Warning text styling
- Responsive design for mobile devices
- Smooth animations

## Security Features

1. **Authentication Check** - Only logged-in moderators can access
2. **Universal Moderation** - Moderators can moderate any event (university restriction removed)
3. **Input Validation** - Reason must be at least 10 characters
4. **SQL Injection Protection** - Parameterized queries
5. **XSS Protection** - HTML special chars escaping

## Publisher Notification

When an event is hidden:
1. Event is marked as `is_deleted = 1`
2. Deletion reason and moderator ID are stored
3. Notification entry created in `event_moderation_notifications` table
4. Publisher can view the reason and appeal if needed

## Database Structure

```sql
-- Events table columns
is_deleted BOOLEAN DEFAULT FALSE
deleted_at TIMESTAMP NULL
deleted_by INT NULL
deletion_reason TEXT NULL

-- Moderation notifications table
event_moderation_notifications (
    id INT PRIMARY KEY,
    event_id INT,
    moderator_id INT,
    notification_type ENUM('approved', 'rejected', 'needs_revision', 'deleted'),
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP
)
```

## User Flow

1. Moderator views events on `/moderator/events` page
2. Each event card shows "Hide Event" and "View Details" buttons
3. Clicking "Hide Event" opens a modal dialog
4. Moderator must provide a reason (10-500 characters)
5. Clicking "Hide Event" button in modal:
   - Validates moderator authentication
   - Soft deletes the event (sets `is_deleted = 1`)
   - Stores reason and moderator info
   - Creates notification for publisher
   - Shows success message
   - Refreshes event list (hidden event disappears)
6. Publisher receives notification with reason

**Note:** Moderators can moderate any event regardless of university affiliation.

## API Endpoints

### Hide Event
```
POST /unipulse/public/moderator/events/hideEvent
Content-Type: application/json

{
    "event_id": 123,
    "reason": "This event violates community guidelines..."
}

Response:
{
    "success": true,
    "message": "Event has been hidden and publisher has been notified"
}
```

### Restore Event
```
POST /unipulse/public/moderator/events/restoreEvent
Content-Type: application/json

{
    "event_id": 123
}

Response:
{
    "success": true,
    "message": "Event has been restored successfully"
}
```

## Testing Checklist

- [x] Database migration runs successfully
- [x] Soft-deleted events are hidden from public view
- [x] Moderators can only hide events from their university
- [x] Modal dialog displays correctly
- [x] Form validation works (min 10 chars)
- [x] Character counter updates correctly
- [x] Success/error messages display
- [x] Event list refreshes after hiding
- [x] Publisher notification is created
- [x] Responsive design works on mobile

## Future Enhancements

1. **Email Notifications** - Send email to publisher when event is hidden
2. **Appeal System** - Allow publishers to appeal hidden events
3. **Bulk Actions** - Hide multiple events at once
4. **Moderation History** - View all moderation actions by a moderator
5. **Restore UI** - Add interface to view and restore deleted events
6. **Publisher Dashboard** - Show hidden events with reasons to publishers

## Files Modified

1. `/database/add_soft_delete_to_events.php` - NEW
2. `/app/models/Event.php` - UPDATED
3. `/app/controllers/Moderator/Events.php` - UPDATED
4. `/public/assets/js/Moderator/events-app.js` - UPDATED
5. `/public/assets/css/Moderator/events-style.css` - UPDATED

## Rollback Instructions

If needed to rollback:

```sql
-- Remove soft delete columns
ALTER TABLE events DROP COLUMN is_deleted;
ALTER TABLE events DROP COLUMN deleted_at;
ALTER TABLE events DROP COLUMN deleted_by;
ALTER TABLE events DROP COLUMN deletion_reason;
```

## Notes

- Soft delete ensures data integrity and allows for restoration
- All deletions are logged with moderator ID and timestamp
- The system maintains a complete audit trail
- Events remain in database for reporting and analytics
