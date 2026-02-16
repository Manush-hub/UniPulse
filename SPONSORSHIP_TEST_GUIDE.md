# Sponsorship System - Quick Test Guide

## 🚀 Quick Start Testing

### Prerequisites
- Database tables created (from previous implementation)
- At least one event with `accepts_sponsorships = 1`
- At least one active sponsorship package for that event
- Sponsor account created and logged in

---

## Test URLs

### 1. Sponsor Events Page
```
URL: http://localhost/unipulse/public/Sponsor/events
```
**What to check**:
- Sponsorship opportunities section appears at the top
- Badge shows correct number of events
- Event cards display package count and slots
- Cards are clickable

### 2. Event Details Page
```
URL: http://localhost/unipulse/public/Sponsor/event/[EVENT_ID]
```
Replace `[EVENT_ID]` with actual event ID.

**What to check**:
- "Available Sponsorship Packages" section appears
- Packages display with correct colors
- Bank details modal opens on "Select This Package"
- Modal shows all bank account information

---

## Quick Database Check

### Check if events exist with sponsorships
```sql
SELECT e.id, e.title, e.accepts_sponsorships, 
       COUNT(esp.id) as package_count
FROM events e
LEFT JOIN event_sponsorship_packages esp ON e.id = esp.event_id
WHERE e.accepts_sponsorships = 1
GROUP BY e.id;
```

### Check sponsorship packages
```sql
SELECT * FROM event_sponsorship_packages 
WHERE is_active = 1 
AND (available_slots - filled_slots) > 0;
```

### Check event bank details
```sql
SELECT id, title, accepts_sponsorships, 
       sponsorship_bank_name, 
       sponsorship_account_number 
FROM events 
WHERE accepts_sponsorships = 1;
```

---

## Test Scenarios (5 Minutes)

### ✅ Test 1: View Sponsorship Section (1 min)
1. Log in as Sponsor
2. Go to events page
3. Look for purple "Sponsorship Opportunities" section
4. **Pass**: Section visible with event cards  
   **Fail**: Section not showing → Check if events exist with sponsorships

### ✅ Test 2: Click Sponsorship Event (1 min)
1. Click on any sponsorship event card
2. Page loads event details
3. **Pass**: Event details page loads  
   **Fail**: 404 error → Check routing or event ID

### ✅ Test 3: View Packages (1 min)
1. On event details page, scroll down
2. Find "Available Sponsorship Packages" section
3. See color-coded package cards
4. **Pass**: Packages display with correct colors  
   **Fail**: Section not showing → Check if packages exist and are active

### ✅ Test 4: Select Package (1 min)
1. Click "Select This Package" button
2. Modal opens with bank details
3. Package name and amount display correctly
4. **Pass**: Modal shows bank account details  
   **Fail**: Modal doesn't open → Check JavaScript console for errors

### ✅ Test 5: Confirm Sponsorship (1 min)
1. In modal, click "Confirm & Contact Organizer"
2. Email client opens (or browser prompts)
3. Email subject and body are pre-filled
4. **Pass**: Email draft created  
   **Fail**: Nothing happens → Check if publisher email exists

---

## Common Issues & Fixes

### Issue 1: Sponsorship section not showing on events page
**Cause**: No events with sponsorships or role not detected  
**Fix**:
```sql
-- Create a test event with sponsorship
UPDATE events SET accepts_sponsorships = 1 WHERE id = 1;

-- Add bank details
UPDATE events SET 
    sponsorship_bank_name = 'Bank of Ceylon',
    sponsorship_account_name = 'Test Publisher',
    sponsorship_account_number = '123456789'
WHERE id = 1;

-- Verify
SELECT id, title, accepts_sponsorships FROM events WHERE id = 1;
```

### Issue 2: No packages showing on event details
**Cause**: No active packages with available slots  
**Fix**:
```sql
-- Check packages
SELECT * FROM event_sponsorship_packages WHERE event_id = 1;

-- Create test package if none exist
INSERT INTO event_sponsorship_packages (
    event_id, package_type, package_name, amount, 
    available_slots, filled_slots, is_active
) VALUES (
    1, 'gold', 'Gold Sponsor', 50000.00, 5, 0, 1
);
```

### Issue 3: Bank details modal not opening
**Cause**: JavaScript error or missing elements  
**Check**:
1. Open browser console (F12)
2. Look for JavaScript errors
3. Check if modal element exists: `document.getElementById('bankDetailsModal')`

**Fix**: Clear browser cache and reload page

### Issue 4: Email not opening
**Cause**: Browser settings or no email client configured  
**Alternative**: 
- Use webmail (Gmail, Outlook)
- Copy email content manually
- Future enhancement: Add in-app messaging

---

## Quick Data Setup Script

Run this SQL to create a complete test scenario:

```sql
-- Update an existing event for sponsorship
UPDATE events SET 
    accepts_sponsorships = 1,
    sponsorship_bank_name = 'Bank of Ceylon',
    sponsorship_account_name = 'Test University Events',
    sponsorship_account_number = '0123456789',
    sponsorship_branch = 'Colombo Main',
    sponsorship_swift_code = 'BCEYLKLX',
    sponsorship_instructions = 'Please include your company name and event title in the payment reference.'
WHERE id = 1;

-- Create sponsorship packages
INSERT INTO event_sponsorship_packages (
    event_id, package_type, package_name, amount, 
    available_slots, filled_slots, description, benefits, terms_conditions, is_active
) VALUES 
(1, 'platinum', 'Platinum Partner', 100000.00, 2, 0, 
 'Exclusive premier sponsorship package with maximum visibility.',
 'Logo on all materials\nOpening ceremony mention\nExclusive booth space\nSocial media features',
 'Payment must be received 2 weeks before event date.',
 1),
(1, 'gold', 'Gold Sponsor', 50000.00, 5, 1, 
 'Premium sponsorship with excellent brand exposure.',
 'Logo on event materials\nBooth space\nSocial media mention',
 'Payment deadline: 1 week before event.',
 1),
(1, 'silver', 'Silver Sponsor', 25000.00, 10, 3, 
 'Great value sponsorship opportunity.',
 'Logo on website\nEvent program mention\nTable at venue',
 'Subject to availability.',
 1);

-- Verify setup
SELECT e.id, e.title, e.accepts_sponsorships,
       COUNT(esp.id) as package_count,
       SUM(esp.available_slots - esp.filled_slots) as available_slots
FROM events e
LEFT JOIN event_sponsorship_packages esp ON e.id = esp.event_id
WHERE e.id = 1
GROUP BY e.id;
```

---

## Expected Results

### Events Page
```
┌─────────────────────────────────────────┐
│ 🤝 Sponsorship Opportunities            │
│ [3 Events Available]                    │
├─────────────────────────────────────────┤
│ ┌────────┐ ┌────────┐ ┌────────┐      │
│ │Event 1 │ │Event 2 │ │Event 3 │      │
│ │3 Pkgs  │ │2 Pkgs  │ │4 Pkgs  │      │
│ │12 Slots│ │5 Slots │ │8 Slots │      │
│ └────────┘ └────────┘ └────────┘      │
└─────────────────────────────────────────┘
─────────────────────────────────────────
          All Public Events
┌────────┐ ┌────────┐ ┌────────┐
│Event A │ │Event B │ │Event C │
└────────┘ └────────┘ └────────┘
```

### Event Details Page
```
┌─────────────────────────────────────────┐
│ Event Title                             │
│ Date, Venue, Details...                 │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│ 🤝 Available Sponsorship Packages       │
├─────────────────────────────────────────┤
│ ┌─────────────┐ ┌─────────────┐        │
│ │  PLATINUM   │ │    GOLD     │        │
│ │             │ │             │        │
│ │ LKR 100,000 │ │ LKR 50,000  │        │
│ │ 2 slots     │ │ 4 slots     │        │
│ │             │ │             │        │
│ │ Benefits... │ │ Benefits... │        │
│ │ [Select]    │ │ [Select]    │        │
│ └─────────────┘ └─────────────┘        │
└─────────────────────────────────────────┘
```

### Bank Details Modal
```
┌────────────────────────────────────┐
│ 🏦 Bank Account Details        [×]│
├────────────────────────────────────┤
│ Selected Package:                  │
│ Gold Sponsor - LKR 50,000.00       │
│                                    │
│ Bank Name: Bank of Ceylon          │
│ Account Name: Test Publisher       │
│ Account Number: 0123456789         │
│ Branch: Colombo Main               │
│ SWIFT: BCEYLKLX                    │
│                                    │
│ [Confirm & Contact] [Cancel]       │
└────────────────────────────────────┘
```

---

## Performance Benchmarks

- Events page load: < 2 seconds
- Event details page: < 1.5 seconds
- Modal open animation: < 0.3 seconds
- Package cards render: Instant
- SQL query execution: < 0.1 seconds

---

## Browser Compatibility

✅ Chrome 90+  
✅ Firefox 88+  
✅ Safari 14+  
✅ Edge 90+  
⚠️ IE 11 (Not tested, likely needs polyfills)

---

## Mobile Testing Checklist

- [ ] Sponsorship cards stack vertically
- [ ] Modal fits screen without horizontal scroll
- [ ] Touch targets are at least 44x44px
- [ ] Text is readable without zooming
- [ ] Email link works on mobile

---

## Next Steps After Testing

1. **If all tests pass**: System is ready for production use
2. **If tests fail**: Check error logs and console
3. **Future enhancements**: See `SPONSORSHIP_SPONSOR_VIEW_COMPLETE.md` Phase 1

---

## Support

- Full documentation: `SPONSORSHIP_SPONSOR_VIEW_COMPLETE.md`
- Publisher setup: `SPONSORSHIP_SYSTEM_COMPLETE.md`
- Database schema: `database/create_event_sponsorships.php`

---

**Happy Testing! 🎉**
