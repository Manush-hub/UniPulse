# Hidden Events Repost Feature Implementation

## Overview
A comprehensive feature has been added to allow moderators to view and repost (restore) hidden events from their dashboard.

## What Was Implemented

### 1. Database/Model Layer
**File: `/app/models/Event.php`**
- Added `getHiddenEvents()` method to retrieve all soft-deleted events with moderator information
- Enhanced `restore()` method to properly restore hidden events and notify publishers
- Added `notifyPublisherOfRestoration()` method to send notifications when events are restored

### 2. Controller Layer
**File: `/app/controllers/Moderator/Events.php`**
- Added `hiddenEvents()` method to display the hidden events page
- Added `getHiddenEvents()` API endpoint for AJAX loading of hidden events
- Enhanced `restoreEvent()` method to handle event restoration with proper notifications

### 3. View Layer
**File: `/app/views/Moderator/hidden_events.view.php`**
- Created a new page to display all hidden events
- Includes search and filter functionality
- Shows hidden event details including:
  - When the event was hidden
  - Who hid the event
  - Reason for hiding
  - All original event details

### 4. Navigation
**File: `/app/views/Moderator/components/header.php`**
- Added "Hidden Events" link to the moderator navigation menu
- Link is positioned between "Events" and "Messages"

### 5. JavaScript Layer
**File: `/public/assets/js/Moderator/hidden-events-app.js`**
- Complete event loading and filtering system
- AJAX-based pagination
- Repost confirmation modal
- Event restoration functionality with API integration
- Success/error message notifications
- Real-time event list updates after restoration

### 6. Styling
**File: `/public/assets/css/Moderator/hidden-events-style.css`**
- Custom styling for hidden event cards with red accents
- Hidden event information display boxes
- Repost modal styling with animations
- Success/error message notifications
- Responsive design for mobile devices
- Action button styles (View Details, Repost)

## Features

### Hidden Events Page
1. **Navigation Access**: Accessible via "Hidden Events" in the moderator header
2. **Search & Filter**: Filter by category, university, or search by keywords
3. **Event Display**: Shows all hidden events with clear visual indicators
4. **Event Information**:
   - Event title, description, and original details
   - Date and location
   - Hidden timestamp
   - Moderator who hid the event
   - Reason for hiding (if provided)

### Repost Functionality
1. **Repost Button**: Each hidden event has a "Repost" button
2. **Confirmation Modal**: Shows a confirmation dialog before reposting
3. **Restoration Process**:
   - Removes soft-delete flag
   - Clears deletion metadata
   - Makes event visible again
   - Notifies publisher of restoration
4. **Feedback**: Success/error messages after restoration
5. **Auto-refresh**: Event list automatically updates after restoration

### User Experience
- **Visual Indicators**: Hidden events have red borders and "Hidden" badges
- **Detailed Information**: Shows why and when events were hidden
- **Easy Restoration**: One-click restore with confirmation
- **Real-time Updates**: No page refresh needed after actions
- **Responsive Design**: Works on desktop and mobile devices

## API Endpoints

### GET `/moderator/events/hiddenEvents`
- Displays the hidden events page
- Supports pagination and filtering

### GET `/moderator/events/getHiddenEvents`
- AJAX endpoint to fetch hidden events
- Accepts parameters: `category`, `university`, `search`, `page`, `limit`
- Returns JSON with events and pagination data

### POST `/moderator/events/restoreEvent`
- Restores a hidden event
- Requires: `event_id` in JSON body
- Returns success/error response

## Database Schema
The feature uses existing soft-delete columns in the `events` table:
- `is_deleted` (BOOLEAN): Flag for soft-deleted events
- `deleted_at` (TIMESTAMP): When the event was hidden
- `deleted_by` (INT): Moderator ID who hid the event
- `deletion_reason` (TEXT): Reason for hiding

## How to Use

### For Moderators:
1. Click "Hidden Events" in the navigation menu
2. Browse the list of hidden events
3. Use search/filters to find specific events
4. Click "View Details" to see full event information
5. Click "Repost" button to restore an event
6. Confirm the restoration in the modal
7. Event will be restored and made visible again

### Event Flow:
1. Moderator hides event → Event marked as deleted
2. Event appears in "Hidden Events" page
3. Moderator reposts event → Soft-delete flag removed
4. Event becomes visible on main events page
5. Publisher receives notification about restoration

## Benefits
- **Content Management**: Better control over hidden content
- **Flexibility**: Easy to reverse moderation decisions
- **Transparency**: Clear audit trail of who hid events and why
- **User-Friendly**: Simple interface for managing hidden content
- **Communication**: Publishers are notified of restorations

## Technical Highlights
- Uses existing soft-delete architecture
- RESTful API design
- AJAX-based for smooth UX
- Proper error handling
- Responsive design
- Secure authentication checks
- Database query optimization with pagination

## Future Enhancements (Optional)
- Bulk restore functionality
- Filter by date range
- Export hidden events list
- More detailed moderation logs
- Email notifications to publishers
- Scheduled auto-restore option

---

**Implementation Date**: December 24, 2025
**Status**: ✅ Complete and Ready to Use
