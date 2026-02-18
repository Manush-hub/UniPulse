# Events & Eventview Pages - Fix Summary

## Issues Fixed

### 1. Events Page Not Loading Properly
**Problem:** Events were not displaying on the events page for User role.

**Root Causes:**
- Missing event placeholder image was causing potential display issues
- No debug logging to identify problems

**Fixes Applied:**
- ✅ Updated `events-app.js` to use existing `default-avatar.png` instead of non-existent `event-placeholder.jpg`
- ✅ Added console logging to help diagnose issues:
  ```javascript
  console.log('Events page loaded. Role:', currentRole);
  console.log('Server data:', window.serverData);
  console.log('Initial events count:', allEvents.length);
  ```

**Files Modified:**
- `/Applications/MAMP/htdocs/unipulse/public/assets/js/events-app.js`

---

### 2. Eventview Page Not Working for User Role
**Problem:** Clicking on events didn't load event details page properly.

**Root Causes:**
1. `UserEventview` controller wasn't passing `userRole` to the view
2. `eventview.view.php` was trying to load non-existent unified JavaScript file
3. No debug logging to identify where the issue occurred

**Fixes Applied:**
- ✅ Added `'userRole' => 'User'` to the data array in `UserEventview` controller
- ✅ Changed JavaScript file loading in `eventview.view.php`:
  ```php
  <!-- OLD (incorrect) -->
  <script src="/unipulse/public/assets/js/eventview-app.js"></script>
  
  <!-- NEW (correct) -->
  <script src="<?php echo $config['jsFile']; ?>"></script>
  ```
- ✅ Added console logging to `User/eventview-app.js`:
  ```javascript
  console.log('Eventview page loaded');
  console.log('Server data:', window.serverData);
  console.log('Current event:', currentEvent);
  ```

**Files Modified:**
- `/Applications/MAMP/htdocs/unipulse/app/controllers/User/Eventview.php`
- `/Applications/MAMP/htdocs/unipulse/app/views/eventview.view.php`
- `/Applications/MAMP/htdocs/unipulse/public/assets/js/User/eventview-app.js`

---

## Testing Instructions

### Test Events Page
1. Navigate to `/unipulse/public/user/events`
2. Open browser console (F12 → Console tab)
3. You should see:
   ```
   Events page loaded. Role: User
   Server data: {events: Array(X), currentPage: 1, ...}
   Initial events count: X
   ```
4. Verify events are displayed in a grid layout
5. Test search and filter functionality
6. Test category filtering (click on category badges)

### Test Eventview Page
1. From events page, click on any event card
2. Open browser console (F12 → Console tab)
3. You should see:
   ```
   Eventview page loaded
   Server data: {event: {...}, similarEvents: [...], ...}
   Current event: {id: X, title: "...", ...}
   ```
4. Verify event details are displayed correctly:
   - Event title, description, date, time
   - Location and university
   - Organizer information
   - Ticket information
   - Similar events section
5. Test "Join Event" button functionality
6. Test sharing functionality

---

## Data Flow Architecture

### Events Page Flow:
```
User Request → UserEvents Controller
              ↓
              Gets events from Event model
              ↓
              Passes data to view:
              - events array
              - userRole = 'User'
              - serverData object
              ↓
              events.view.php template
              - Extracts data with extract()
              - Outputs window.serverData
              - Includes unified events-app.js
              ↓
              events-app.js
              - Reads window.serverData
              - Uses roleConfig['User'] settings
              - Displays events in grid
```

### Eventview Page Flow:
```
User Clicks Event → /user/eventview?id=123
                   ↓
                   UserEventview Controller
                   ↓
                   Gets event by ID from Event model
                   ↓
                   Passes data to view:
                   - event object
                   - similarEvents array
                   - userRole = 'User'
                   - serverData object
                   ↓
                   eventview.view.php template
                   - Extracts data
                   - Uses roleConfig['User'] settings
                   - Loads User/eventview-app.js
                   - Outputs window.serverData
                   ↓
                   User/eventview-app.js
                   - Reads window.serverData
                   - Displays event details
                   - Shows similar events
```

---

## Technical Details

### Role Configuration (events.view.php)
```php
$roleConfig = [
    'User' => [
        'pageTitle' => 'UniPulse - All Events',
        'showCategories' => true,
        'cssFile' => '/unipulse/public/assets/css/events-style.css',
        'searchInputId' => 'eventNameFilter'
    ],
    // ... other roles
];

$currentRole = $userRole ?? 'User';
$config = $roleConfig[$currentRole];
```

### JavaScript Role Configuration (events-app.js)
```javascript
const roleConfig = {
    User: {
        apiEndpoint: '/unipulse/public/user/events/getEvents',
        searchInputId: 'eventNameFilter',
        eventDetailsUrl: '/unipulse/public/user/eventview/',
        showCategoryHeader: true,
        showHideButton: false
    },
    // ... other roles
};
```

### Event Card Structure
Each event card displays:
- Cover image with status badge (Upcoming/Ongoing/Completed)
- Boosted badge (if applicable)
- Category label
- Event title
- Date and time
- Location/university
- Organizer name
- Current participants / Max participants
- Click handler to view details

---

## Common Issues & Solutions

### Issue: Events not displaying
**Check:**
1. Open browser console for error messages
2. Verify `window.serverData.events` has data
3. Check if `allEvents.length > 0`
4. Verify Event model is returning data from database

### Issue: Event images not showing
**Solution:** Images default to `/unipulse/public/assets/images/default-avatar.png` if cover_image is null

### Issue: Clicking event doesn't load details
**Check:**
1. Verify event ID is being passed correctly
2. Check console for "Eventview page loaded" message
3. Verify `window.serverData.event` contains data
4. Check if UserEventview controller is querying correct event ID

### Issue: Role-specific features not working
**Check:**
1. Verify `userRole` is being passed from controller
2. Check console log shows correct role
3. Verify `roleConfig` has configuration for that role

---

## Files Involved

### Controllers:
- `/app/controllers/User/Events.php` - Handles events listing
- `/app/controllers/User/Eventview.php` - Handles event details

### Views:
- `/app/views/events.view.php` - Unified events template
- `/app/views/eventview.view.php` - Unified eventview template

### JavaScript:
- `/public/assets/js/events-app.js` - Unified events logic
- `/public/assets/js/User/eventview-app.js` - User-specific eventview logic

### CSS:
- `/public/assets/css/events-style.css` - Events page styling
- `/public/assets/css/eventview-style.css` - Eventview page styling

---

## Browser Console Debugging

### Expected Console Output (Events Page):
```
Events page loaded. Role: User
Server data: {
  events: Array(3) [...],
  currentPage: 1,
  totalPages: 1,
  filters: {},
  apiEndpoint: "/unipulse/public/user/events/getEvents"
}
Initial events count: 3
```

### Expected Console Output (Eventview Page):
```
Eventview page loaded
Server data: {
  event: {
    id: 123,
    title: "Event Title",
    category: "cultural",
    event_date: "2026-01-16",
    ...
  },
  similarEvents: [...],
  isRegistered: false,
  apiEndpoint: "/unipulse/public/user/eventview/getEvent",
  joinEndpoint: "/unipulse/public/user/eventview/joinEvent"
}
Current event: {id: 123, title: "Event Title", ...}
Has error: null
```

---

## Next Steps

1. **Test the pages** using the testing instructions above
2. **Check browser console** for any errors or warnings
3. **Verify data flow** by examining console.log output
4. **Test all user interactions**:
   - Search events
   - Filter by category/university/status
   - Click on events to view details
   - Join events
   - Share events

If issues persist:
- Check database connection
- Verify Event model methods are working
- Check PHP error logs in MAMP
- Inspect network tab for failed API calls

---

## Success Criteria

✅ Events page loads and displays events in grid  
✅ Search and filter functionality works  
✅ Category badges show correct counts  
✅ Clicking event opens eventview page  
✅ Eventview displays complete event details  
✅ Similar events are shown  
✅ Join Event button works  
✅ Share functionality works  
✅ Console shows correct debug information  
✅ No JavaScript errors in console  
✅ No PHP errors in MAMP logs  

---

**Date Fixed:** February 14, 2026  
**Branch:** 2.14.2-template  
**Related Files:** 5 modified files
