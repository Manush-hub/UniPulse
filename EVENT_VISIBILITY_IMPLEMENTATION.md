# Event Visibility Implementation Guide

## Overview
The Event Visibility feature is a core component of the UniPulse system that controls who can view and access events based on the event organizer's visibility settings.

## Visibility Levels

### 1. Faculty Only (`faculty-only`)
**Most Restrictive**
- Event is visible ONLY to users from the **same faculty AND university** as the event organizer (publisher)
- Example: If a publisher from "University of Colombo, UCSC" creates a faculty-only event, only students/staff from "University of Colombo, UCSC" can see it

**Who can see:**
- University users (university_users table) with matching university AND faculty
- Publishers, Admins, Moderators (can see all events)

### 2. University Only (`university-only`)
**Moderately Restrictive**
- Event is visible ONLY to users from the **same university** as the event organizer
- Faculty doesn't matter - all faculties from that university can see it
- Example: If a publisher from "University of Colombo" creates a university-only event, all students/staff from "University of Colombo" can see it

**Who can see:**
- University users (university_users table) with matching university
- Publishers, Admins, Moderators (can see all events)

### 3. All Universities (`all-universities`)
**Open to University Community**
- Event is visible to ALL university users, regardless of which university they're from
- Public users (public_users table) CANNOT see these events
- Example: A tech conference open to all university students

**Who can see:**
- All university users (anyone in university_users table)
- Publishers, Admins, Moderators (can see all events)

### 4. Public (`public`)
**Least Restrictive**
- Event is visible to EVERYONE, including university users and public users
- No restrictions
- Example: A public concert or open community event

**Who can see:**
- University users (university_users table)
- Public users (public_users table)
- Publishers, Admins, Moderators
- Non-logged-in visitors

## Database Schema

### Events Table
The following columns are used for visibility filtering:

```sql
visibility ENUM('faculty-only', 'university-only', 'all-universities', 'public') DEFAULT 'public'
university VARCHAR(100) -- Stores the PUBLISHER's university
faculty_department VARCHAR(255) -- Stores the PUBLISHER's faculty
```

**Important:** The `university` and `faculty_department` fields store the **event organizer's (publisher's)** university and faculty, NOT the event location's university/faculty. This is crucial for visibility filtering.

### Users Tables

#### university_users
```sql
university VARCHAR(100)
faculty VARCHAR(100)
```

#### public_users
```sql
-- No university or faculty fields
```

#### publishers
```sql
university VARCHAR(100)
faculty VARCHAR(100)
```

## Implementation Details

### Event Creation Flow

When a publisher creates an event:

1. **Publisher Authentication**: Check that the user is logged in as a publisher
2. **Get Publisher Data**: Fetch the publisher's university and faculty from their profile
3. **Store Visibility Data**:
   ```php
   $formData = [
       'visibility' => $_POST['event_visibility'], // faculty-only, university-only, etc.
       'university' => $publisherUniversity,       // Publisher's university
       'faculty_department' => $publisherFaculty,  // Publisher's faculty
       // ... other fields
   ];
   ```
4. **Save Event**: Insert into events table with the visibility settings

### Event Filtering Logic

When displaying events to users, the `buildVisibilityFilter()` method in `Event.php` applies these rules:

```php
private function buildVisibilityFilter($currentUser = null) {
    // No user logged in -> Only public events
    if (!$currentUser) {
        return "e.visibility = 'public'";
    }
    
    $userType = $currentUser['type'];
    
    // Admin/Moderator/Publisher -> See all events
    if (in_array($userType, ['publisher', 'admin', 'moderator'])) {
        return ''; // No filter
    }
    
    // Build conditions based on user type
    $conditions = [];
    
    // Everyone can see public events
    $conditions[] = "e.visibility = 'public'";
    
    // University users get additional visibility
    if ($userType === 'university_user') {
        // All-universities events
        $conditions[] = "e.visibility = 'all-universities'";
        
        // University-only events (matching university)
        if ($userUniversity) {
            $conditions[] = "(e.visibility = 'university-only' AND e.university = :user_university)";
        }
        
        // Faculty-only events (matching university AND faculty)
        if ($userUniversity && $userFaculty) {
            $conditions[] = "(e.visibility = 'faculty-only' AND e.university = :user_university AND e.faculty_department = :user_faculty)";
        }
    }
    
    // Public users only see public events (already added above)
    
    return '(' . implode(' OR ', $conditions) . ')';
}
```

## File Locations

### Controller
- **File**: `app/controllers/Publisher/Createevent.php`
- **Key Method**: `handleFormSubmission()`
  - Lines ~90-150: Sets publisher's university and faculty for visibility filtering

### Model
- **File**: `app/models/Event.php`
- **Key Methods**:
  - `buildVisibilityFilter($currentUser)`: Lines ~136-199 - Builds SQL WHERE clause for visibility
  - `getAllEvents($filters, $currentUser)`: Uses visibility filter
  - `getEventsSeekingSponsors($filters, $currentUser)`: Uses visibility filter
  - `getSimilarEvents(...)`: Uses visibility filter

### View
- **File**: `app/views/Publisher/createevent.view.php`
- **Lines**: 193-245 - Event Visibility section with radio buttons

## Testing Scenarios

### Test Case 1: Faculty-Only Event
1. **Setup**:
   - Publisher: University of Colombo, UCSC faculty
   - Creates event with "Faculty Only" visibility
2. **Expected Results**:
   - ✅ Visible to: University of Colombo + UCSC faculty students/staff
   - ❌ Not visible to: University of Colombo + other faculties
   - ❌ Not visible to: Other universities
   - ❌ Not visible to: Public users

### Test Case 2: University-Only Event
1. **Setup**:
   - Publisher: University of Moratuwa, Engineering faculty
   - Creates event with "University Only" visibility
2. **Expected Results**:
   - ✅ Visible to: All University of Moratuwa students/staff (any faculty)
   - ❌ Not visible to: Other universities
   - ❌ Not visible to: Public users

### Test Case 3: All Universities Event
1. **Setup**:
   - Publisher: SLIIT, IT faculty
   - Creates event with "All Universities" visibility
2. **Expected Results**:
   - ✅ Visible to: All university users from any university
   - ❌ Not visible to: Public users (non-university)

### Test Case 4: Public Event
1. **Setup**:
   - Publisher: Any university
   - Creates event with "Public" visibility
2. **Expected Results**:
   - ✅ Visible to: Everyone (university users + public users + guests)

### Test Case 5: Admin/Moderator/Publisher View
1. **Expected Results**:
   - ✅ Can see ALL events regardless of visibility settings

## Security Considerations

1. **Publisher Verification**: Always verify that the publisher's university and faculty data is accurate and cannot be manipulated
2. **SQL Injection Prevention**: Use parameterized queries for all database operations
3. **Authorization Checks**: Verify user type before showing/hiding events
4. **Data Integrity**: Ensure visibility enum values match exactly (`faculty-only`, not `Faculty Only`)

## Common Issues and Solutions

### Issue 1: Publisher's faculty is null
**Problem**: Faculty-only filtering breaks if publisher's faculty is not set
**Solution**: Validate that publishers have both university and faculty set during registration

### Issue 2: Event location vs. Publisher location confusion
**Problem**: The event location university/faculty is different from publisher's university/faculty
**Solution**: Store publisher's university/faculty in the event record, separately from the event location details

### Issue 3: Public users see university-only events
**Problem**: Incorrect visibility filter logic
**Solution**: Ensure `public_user` type only sees `public` visibility events

## API Endpoints

### Get All Events
```php
GET /unipulse/public/user/events/getEvents
GET /unipulse/public/publisher/events/getEvents
GET /unipulse/public/sponsor/events/getEvents

// Automatically filters based on current user's session
```

### Create Event
```php
POST /unipulse/public/publisher/createevent

// Form fields:
event_visibility: 'faculty-only' | 'university-only' | 'all-universities' | 'public'
```

## Future Enhancements

1. **Custom Visibility Rules**: Allow publishers to specify custom universities or faculties
2. **Visibility Preview**: Show publishers who will be able to see their event before publishing
3. **Visibility Analytics**: Track how many users from each category viewed the event
4. **Visibility Change History**: Log when visibility settings are changed

## Summary

The Event Visibility system ensures that:
- **Faculty-Only** events go to the publisher's faculty members only
- **University-Only** events go to the publisher's university members only
- **All-Universities** events go to all university users
- **Public** events go to everyone

The system uses the publisher's university and faculty (not the event location) to determine who can see the event, ensuring accurate filtering based on the event organizer's institution.
