# UniPulse Event System Database & UI Updates

## Summary of Changes Made

This document outlines all the changes made to update the UniPulse event system database and user interface to match the comprehensive create event form.

## 1. Database Changes

### New Columns Added to `events` Table:

#### Location-related fields:
- `location_type` ENUM('inside-university', 'outside-university') - Type of event location
- `venue_name` VARCHAR(255) - External venue name
- `street_address` VARCHAR(255) - Street address for external venues
- `city` VARCHAR(100) - City for external venues
- `district_province` VARCHAR(100) - District/Province for external venues  
- `faculty_department` VARCHAR(255) - Faculty/Department for university events

#### Audience and participation fields:
- `target_audience` ENUM('university-students', 'public-users', 'both') - Target audience
- `event_end_time` TIME - Event end time

#### Ticket-related fields:
- `ticket_type` ENUM('free-all', 'paid-all', 'mixed') - Ticket pricing strategy
- `registration_limit` INT - Maximum registrations
- `registration_start_date` DATE - Registration opening date
- `registration_start_time` TIME - Registration opening time
- `registration_end_date` DATE - Registration closing date
- `registration_end_time` TIME - Registration closing time
- `ticket_types` JSON - Detailed ticket type information

#### Additional functionality fields:
- `custom_fields` JSON - Custom registration fields
- `needs_volunteers` BOOLEAN - Whether event needs volunteers
- `volunteer_sources` JSON - Where to recruit volunteers from
- `volunteers_needed` INT - Number of volunteers needed
- `volunteer_positions` JSON - Available volunteer positions
- `accepts_donations` BOOLEAN - Whether event accepts donations
- `cover_image` VARCHAR(500) - Event cover image path

#### Enhanced category support:
- Updated `category` ENUM to include 'business' and 'music' options

## 2. Model Updates

### Event.php Model Changes:
- Updated `$allowedColumns` array to include all new fields
- Enhanced `getEventById()` method to decode all JSON fields
- Updated `createEvent()` method to handle JSON encoding for new fields

## 3. Controller Updates

### Publisher/Createevent.php Changes:
- Extended form data collection to capture all new fields
- Added handling for location type (inside/outside university)
- Added support for audience targeting
- Added ticket type and registration period handling
- Added volunteer recruitment functionality
- Added donation acceptance option
- Enhanced image upload to use cover_image field
- Added helper method `getUniversityName()` for university mapping

## 4. View Updates

### Create Event Form (createevent.view.php):
- Added proper `name` attributes to all form inputs
- Form now captures:
  - Location type and detailed location information
  - Target audience selection
  - Ticket pricing options
  - Registration periods and limits
  - Volunteer requirements and positions
  - Custom fields for additional information
  - Donation acceptance option

### Event View Pages (eventview.view.php):
All event view pages (User, Publisher, Sponsor) updated with:

#### New display sections:
- **Location Details Card**: Shows detailed venue information
- **Ticket Information Card**: Displays ticket types and pricing
- **Custom Fields Card**: Shows additional registration fields
- **Volunteer Information Card**: Lists volunteer opportunities
- **Donation Card**: Donation support option with modal

#### Enhanced hero section:
- Added target audience display
- Added ticket type information
- Improved event details grid with more information

#### New modals:
- **Donation Modal**: Allows users to make donations with preset amounts or custom amounts

## 5. JavaScript Updates

### eventview-app.js Enhancements:
- Updated `displayEventDetails()` function to handle all new fields
- Added helper functions:
  - `formatAudience()`: Formats audience type for display
  - `formatTicketType()`: Formats ticket type for display
  - `displayLocationDetails()`: Shows detailed location information
  - `displayTicketDetails()`: Shows ticket and registration information
  - `displayCustomFields()`: Displays custom registration fields
  - `displayVolunteerInfo()`: Shows volunteer opportunities
- Added donation functionality:
  - `openDonationModal()`, `closeDonationModal()`: Modal controls
  - `processDonation()`: Handles donation processing
  - `applyAsVolunteer()`: Volunteer application handler

## 6. CSS Updates

### eventview-style.css Enhancements:
- Added styles for new content cards
- Added location detail styling
- Added ticket information styling  
- Added custom fields styling
- Added volunteer information styling
- Added donation modal styling with:
  - Grid layout for donation amounts
  - Selection states for donation buttons
  - Custom amount input styling
  - Responsive design for mobile devices

## 7. Files Modified

### Database:
- `/database/update_events_table.php` - Database migration script

### Models:
- `/app/models/Event.php` - Enhanced with new fields and JSON handling

### Controllers:
- `/app/controllers/Publisher/Createevent.php` - Extended form processing

### Views:
- `/app/views/Publisher/createevent.view.php` - Enhanced form fields
- `/app/views/User/eventview.view.php` - Added new display sections
- `/app/views/Publisher/eventview.view.php` - Added new display sections  
- `/app/views/Sponsor/eventview.view.php` - Added new display sections

### JavaScript:
- `/public/assets/js/User/eventview-app.js` - Enhanced display logic
- `/public/assets/js/Publisher/eventview-app.js` - Enhanced display logic
- `/public/assets/js/Sponsor/eventview-app.js` - Enhanced display logic

### CSS:
- `/public/assets/css/eventview-style.css` - New styling for enhanced features

## 8. Key Features Added

1. **Flexible Location Management**: Support for both university and external venues
2. **Advanced Ticketing**: Free, paid, and mixed ticket pricing strategies
3. **Volunteer Management**: Recruitment from multiple sources with position tracking
4. **Custom Registration Fields**: Dynamic form fields for additional information
5. **Donation Support**: Integrated donation functionality with modal interface
6. **Enhanced Audience Targeting**: Specific targeting for university students, public, or both
7. **Registration Period Management**: Time-bound registration with limits
8. **Improved Visual Design**: Better information display with organized cards and sections

## 9. Database Migration

The database has been successfully updated with all new columns. The migration script handles:
- Adding new columns with appropriate data types
- Setting default values for existing records
- Updating ENUM values for expanded options
- Proper indexing for performance

## 10. Backward Compatibility

All changes maintain backward compatibility with existing events:
- New fields have appropriate defaults
- Existing event data remains functional
- Legacy events display correctly with enhanced views
- No breaking changes to existing functionality

The system now provides a comprehensive event management platform that supports complex event types, multiple pricing strategies, volunteer coordination, and enhanced user engagement through donations and detailed information display.