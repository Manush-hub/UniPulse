# 🎯 EVENT SPONSORSHIP - QUICK REFERENCE

## Database Tables

### event_sponsorship_packages
- Stores package definitions (bronze/silver/gold/platinum/custom)
- Fields: name, type, amount, description, benefits, terms, slots

### event_sponsorships  
- Tracks actual sponsorships
- Statuses: pending → approved/rejected → completed

### events (new columns)
- accepts_sponsorships
- sponsorship_bank_name
- sponsorship_account_name
- sponsorship_account_number
- sponsorship_branch
- sponsorship_swift_code
- sponsorship_instructions

## Files Modified

```
✅ Database
   /database/create_event_sponsorships.php (NEW)

✅ Frontend  
   /app/views/Publisher/createevent.view.php
   - Added sponsorship section with toggle
   - Bank details form
   - Package builder interface
   - Live package preview

✅ JavaScript
   /public/assets/js/create-event-app.js
   - toggleSponsorshipDetails()
   - addSponsorshipPackage()
   - displaySponsorshipPackages()
   - removeSponsorshipPackage()

✅ Backend
   /app/controllers/Publisher/Createevent.php
   - Added sponsorship fields to form data
   - saveSponsorshipPackages() method
```

## Package Types & Colors

| Type | Color | Icon | Hex Color |
|------|-------|------|-----------|
| Bronze | 🥉 | medal | #CD7F32 |
| Silver | 🥈 | medal | #C0C0C0 |
| Gold | 🥇 | crown | #FFD700 |
| Platinum | 💎 | gem | #E5E4E2 |
| Custom | ⭐ | star | #6366F1 |

## How to Create Event with Sponsorships

1. ✅ Enable "Request Sponsorship" toggle
2. ✅ Fill bank account details (required)
3. ✅ Select package type
4. ✅ Enter package name, amount, slots
5. ✅ Add description, benefits, terms
6. ✅ Click "Add Package"
7. ✅ Repeat for multiple packages
8. ✅ Publish event

## Data Flow

```
Form Submit
    ↓
POST: sponsorshipToggle = 1
POST: sponsorship_bank_name, account_name, account_number, etc.
POST: sponsorship_packages = [JSON array]
    ↓
Controller: Createevent.php
    ↓
Event created with sponsorship fields
    ↓
saveSponsorshipPackages($eventId, $packages)
    ↓
Packages inserted into event_sponsorship_packages
    ↓
✅ Event published with sponsorship opportunities
```

## Key JavaScript Functions

```javascript
// Toggle sponsorship section
sponsorshipToggle.addEventListener('change', toggleSponsorshipDetails);

// Add package
addSponsorshipPackage()
  - Validates inputs
  - Creates package object
  - Adds to sponsorshipPackages array
  - Calls displaySponsorshipPackages()

// Display packages with styled cards  
displaySponsorshipPackages()
  - Renders color-coded package cards
  - Updates sponsorship_packages_input hidden field

// Remove package
removeSponsorshipPackage(id)
  - Filters out package by id
  - Updates display
```

## Hidden Input

```html
<input type="hidden" 
       name="sponsorship_packages" 
       id="sponsorship_packages_input" 
       value='[{"id":1,"type":"gold","name":"Gold Sponsor",...}]'>
```

## SQL Queries

**Insert Package:**
```sql
INSERT INTO event_sponsorship_packages 
(event_id, package_name, package_type, amount, description, 
 benefits, terms_conditions, available_slots, display_order, is_active) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
```

**Get Event Packages:**
```sql
SELECT * FROM event_sponsorship_packages 
WHERE event_id = ? AND is_active = 1 
ORDER BY display_order ASC
```

**Check Availability:**
```sql
SELECT (available_slots - filled_slots) as remaining 
FROM event_sponsorship_packages WHERE id = ?
```

## Testing

✅ **Run Migration:**
```bash
/Applications/MAMP/bin/php/php8.4.1/bin/php database/create_event_sponsorships.php
```

✅ **Create Test Event:**
- Enable sponsorship toggle
- Fill bank details
- Add 2-3 packages
- Submit form
- Check database tables

✅ **Verify Data:**
```sql
SELECT * FROM events WHERE accepts_sponsorships = 1;
SELECT * FROM event_sponsorship_packages;
```

## Quick Troubleshooting

**Packages not saving?**
- Check console for JavaScript errors
- Verify hidden input has JSON data
- Ensure toggle is checked

**Bank details missing?**
- Check form field names match POST keys
- Verify fields have values

**Display issues?**
- Check CSS for .hidden class
- Verify toggle event listener attached
- Check DOM elements exist

## Next Steps

1. ✅ Add sponsor view of packages (in eventview)
2. ✅ Create sponsorship request form
3. ✅ Add approval/rejection workflow
4. ✅ Payment verification system
5. ✅ Email notifications
6. ✅ Sponsor dashboard

---

**Status:** ✅ Backend & Database Complete  
**Next:** Build sponsor-facing UI
