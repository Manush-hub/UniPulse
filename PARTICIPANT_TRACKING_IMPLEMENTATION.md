# Participant Tracking System Implementation

## Overview
This document describes the implementation of the participant tracking system for events in UniPulse. The system tracks actual registrations and ticket purchases, and only displays participant counts when the publisher sets a maximum participant limit.

## Core Concept

### The Story
- Publishers can set a **maximum participants** limit for their events (optional)
- This maximum includes BOTH:
  - Free registrations
  - Paid ticket purchases
- The system tracks **current participants** (actual registrations + ticket purchases)
- **Available spots** = Maximum participants - Current participants

### Display Logic
- **If publisher sets max_participants**: Show "Participants: X/Y" and "Available Spots: Z"
- **If publisher doesn't set max_participants**: Don't show these fields (unlimited capacity)

---

## Database Changes

### Migration: `add_current_participants.php`

#### New Column
```sql
ALTER TABLE events 
ADD COLUMN current_participants INT DEFAULT 0 NOT NULL 
AFTER participants
```

#### Modified Column
```sql
ALTER TABLE events 
MODIFY COLUMN max_participants INT NULL
```
- Changed from NOT NULL to NULL (optional)
- NULL means unlimited participants

#### Index Added
```sql
CREATE INDEX idx_current_participants ON events(current_participants)
```

### Field Descriptions

| Field | Type | Default | Description |
|-------|------|---------|-------------|
| `participants` | INT | 0 | Legacy field (kept for backward compatibility) |
| `current_participants` | INT | 0 | **NEW**: Actual count of registrations + ticket purchases |
| `max_participants` | INT | NULL | Maximum allowed participants (NULL = unlimited) |

---

## Backend Implementation

### Event Model (`app/models/Event.php`)

#### New Methods

```php
/**
 * Update current participants count
 */
public function updateCurrentParticipants($id, $newCount)

/**
 * Increment current participants (when user registers or buys ticket)
 * Returns false if event is full
 */
public function incrementParticipants($id)

/**
 * Decrement current participants (when user cancels registration)
 */
public function decrementParticipants($id)

/**
 * Check if event has available spots
 * Returns true if max_participants is NULL (unlimited)
 */
public function hasAvailableSpots($id)

/**
 * Get available spots count
 * Returns NULL if unlimited, otherwise returns remaining spots
 */
public function getAvailableSpots($id)
```

#### Usage Example

```php
$event = new Event();

// When user registers or buys ticket
if ($event->hasAvailableSpots($eventId)) {
    if ($event->incrementParticipants($eventId)) {
        // Success: participant count incremented
    } else {
        // Error: event is full or doesn't exist
    }
}

// When user cancels
$event->decrementParticipants($eventId);

// Check available spots
$availableSpots = $event->getAvailableSpots($eventId);
// Returns: integer (spots left) or NULL (unlimited)
```

---

## Frontend Implementation

### Create Event Form (`createevent.view.php`)

#### Updated Field

```html
<div class="form-group">
    <label class="form-label">Maximum Participants</label>
    <p>Set the maximum number of people who can attend. Leave empty for unlimited participants.</p>
    <input type="number" name="max_participants" class="form-input" 
        min="1" max="100000"
        placeholder="Leave empty for unlimited participants">
    <p><i class="fas fa-info-circle"></i> This includes both free registrations and paid ticket purchases.</p>
</div>
```

#### Key Points
- Field is now **optional** (not required)
- Empty value = unlimited participants
- Clear messaging to publishers

---

### Event View Pages

#### Modified Files
1. `app/views/User/eventview.view.php`
2. `app/views/Publisher/eventview.view.php`
3. `app/views/Sponsor/eventview.view.php`

#### Changes Made

**Participants Detail Item:**
```html
<!-- Before -->
<div class="detail-item">
    <i class="fas fa-users"></i>
    <div>
        <strong>Participants</strong>
        <span id="eventParticipants">Loading...</span>
    </div>
</div>

<!-- After -->
<div class="detail-item" id="participantsInfo" style="display: none;">
    <i class="fas fa-users"></i>
    <div>
        <strong>Participants</strong>
        <span id="eventParticipants">Loading...</span>
    </div>
</div>
```

**Event Statistics Card:**
```html
<!-- Before -->
<div class="content-card">
    <h3><i class="fas fa-chart-bar"></i> Event Statistics</h3>
    ...
</div>

<!-- After -->
<div class="content-card" id="eventStatsCard" style="display: none;">
    <h3><i class="fas fa-chart-bar"></i> Event Statistics</h3>
    ...
</div>
```

---

### JavaScript Implementation

#### Modified Files
1. `public/assets/js/User/eventview-app.js`
2. `public/assets/js/Publisher/eventview-app.js`
3. `public/assets/js/Sponsor/eventview-app.js`

#### Key Changes

**Display Event Details:**
```javascript
function displayEventDetails(event) {
    const maxParticipants = event.max_participants || event.maxParticipants;
    const currentParticipants = event.current_participants || event.currentParticipants || 0;
    
    // Show participants info only if max_participants is set
    if (maxParticipants !== null && maxParticipants !== undefined) {
        document.getElementById('participantsInfo').style.display = 'flex';
        document.getElementById('eventParticipants').textContent = 
            `${currentParticipants}/${maxParticipants}`;
    } else {
        document.getElementById('participantsInfo').style.display = 'none';
    }
    
    // ... other code
}
```

**Display Statistics:**
```javascript
// Statistics - only show if max_participants is set
if (maxParticipants !== null && maxParticipants !== undefined) {
    document.getElementById('eventStatsCard').style.display = 'block';
    document.getElementById('totalParticipants').textContent = currentParticipants;
    document.getElementById('availableSpots').textContent = maxParticipants - currentParticipants;
    
    // Participation percentage
    const percentage = maxParticipants > 0 
        ? Math.round((currentParticipants / maxParticipants) * 100) 
        : 0;
    document.getElementById('participationPercentage').textContent = `${percentage}%`;
    document.getElementById('participationFill').style.width = `${percentage}%`;
} else {
    document.getElementById('eventStatsCard').style.display = 'none';
}
```

---

## Integration Points

### When to Call `incrementParticipants()`

1. **Free Event Registration**
   ```php
   // In registration controller
   if ($registrationSuccess) {
       $eventModel->incrementParticipants($eventId);
   }
   ```

2. **Ticket Purchase (Paid Events)**
   ```php
   // In ticket purchase controller
   if ($paymentSuccess) {
       $eventModel->incrementParticipants($eventId);
   }
   ```

3. **Mixed Events** (Free for students, paid for others)
   ```php
   // For both registration and ticket purchase
   if ($success) {
       $eventModel->incrementParticipants($eventId);
   }
   ```

### When to Call `decrementParticipants()`

1. **Registration Cancellation**
   ```php
   if ($cancellationSuccess) {
       $eventModel->decrementParticipants($eventId);
   }
   ```

2. **Refund Processing**
   ```php
   if ($refundSuccess) {
       $eventModel->decrementParticipants($eventId);
   }
   ```

---

## UI/UX Behavior

### Scenario 1: Event with Maximum Participants Set

**Publisher creates event:**
- Sets max_participants = 100

**Event View displays:**
```
Participants: 45/100
Available Spots: 55
[Progress bar showing 45%]
```

### Scenario 2: Event with Unlimited Participants

**Publisher creates event:**
- Leaves max_participants empty (NULL)

**Event View displays:**
- ❌ Participants section hidden
- ❌ Event Statistics card hidden
- ✅ All other event details shown normally

---

## API Response Format

When returning event data via API, include:

```json
{
    "id": 123,
    "title": "Tech Conference 2025",
    "max_participants": 100,
    "current_participants": 45,
    "participants": 45,  // Legacy field
    // ... other fields
}
```

**For unlimited events:**
```json
{
    "id": 124,
    "title": "Open Workshop",
    "max_participants": null,
    "current_participants": 25,
    // ... other fields
}
```

---

## Validation Rules

### Event Creation
- `max_participants`: Optional
- If provided: Must be integer between 1 and 100,000
- If empty: Set to NULL in database

### Registration/Ticket Purchase
```php
// Before allowing registration
if ($event->max_participants !== null) {
    if (!$event->hasAvailableSpots($eventId)) {
        return error("Event is full");
    }
}
// Proceed with registration...
```

---

## Testing Checklist

### Database
- [x] Migration runs successfully
- [x] `current_participants` column exists with default 0
- [x] `max_participants` is nullable
- [x] Index created on `current_participants`

### Backend (Event Model)
- [x] `incrementParticipants()` increases count
- [x] `incrementParticipants()` returns false when full
- [x] `incrementParticipants()` allows unlimited if NULL
- [x] `decrementParticipants()` decreases count
- [x] `hasAvailableSpots()` returns true for NULL
- [x] `getAvailableSpots()` returns NULL for unlimited

### Create Event Form
- [x] Can create event without max_participants
- [x] Can create event with max_participants
- [x] Validation works correctly
- [x] Help text is clear

### Event View Pages
- [x] Participants shown when max_participants set
- [x] Participants hidden when max_participants is NULL
- [x] Statistics card shown when max_participants set
- [x] Statistics card hidden when max_participants is NULL
- [x] Progress bar calculates correctly
- [x] Available spots calculates correctly

---

## Migration Guide

### For Existing Events

Run the migration script:
```bash
php database/add_current_participants.php
```

This will:
1. Add `current_participants` column (default 0)
2. Make `max_participants` nullable
3. Add performance index

**Note:** Existing events will have:
- `current_participants = 0` (needs manual sync if needed)
- `max_participants = [existing value]` (non-NULL)

### Sync Current Participants (Optional)

If you need to sync existing participant counts:
```sql
UPDATE events 
SET current_participants = participants 
WHERE current_participants = 0 AND participants > 0;
```

---

## Future Enhancements

### Possible Additions
1. **Waiting List**: When event is full, allow users to join waiting list
2. **Overbooking**: Allow publishers to set overbooking percentage
3. **Real-time Updates**: WebSocket notifications when spots fill up
4. **Analytics**: Track registration velocity, fill rates, etc.
5. **Auto-close**: Automatically close registration when full
6. **Capacity Alerts**: Notify publisher at 50%, 75%, 90% full

---

## Troubleshooting

### Issue: Participant count not updating
**Check:**
1. Is `incrementParticipants()` being called after successful registration?
2. Is the event ID correct?
3. Check database logs for errors

### Issue: Event shows full but has spots
**Solution:**
```php
// Recalculate from registrations table
$actualCount = $db->query("SELECT COUNT(*) FROM registrations WHERE event_id = ?", [$eventId]);
$eventModel->updateCurrentParticipants($eventId, $actualCount);
```

### Issue: Statistics card not showing
**Check:**
1. Is `max_participants` NULL in database?
2. Is JavaScript loading correctly?
3. Check browser console for errors

---

## Summary

✅ **Implemented:**
- Database migration with `current_participants` column
- `max_participants` is now optional (NULL = unlimited)
- Event model methods for participant tracking
- Conditional display in UI based on max_participants
- Updated JavaScript for all user types
- Proper validation and error handling

✅ **Key Benefits:**
- Publishers have flexibility (limited or unlimited capacity)
- Accurate tracking of registrations + ticket purchases
- Clean UI that only shows relevant information
- Backward compatible with existing events
- Performance optimized with database indexes

✅ **Ready for Integration:**
- Registration controllers can call `incrementParticipants()`
- Ticket purchase controllers can call `incrementParticipants()`
- Cancellation controllers can call `decrementParticipants()`

---

**Implementation Date:** October 22, 2025  
**Status:** ✅ Complete and Ready for Testing
