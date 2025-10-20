# University Column Addition to Moderator System

## Overview
Added university-specific functionality to the moderator system to allow moderators to only moderate content from their assigned university.

## Changes Made

### 1. Database Schema Changes

#### Modified `database/setup.php`
- Added `university` VARCHAR(100) NOT NULL column to moderators table
- Added `university_name` VARCHAR(255) NOT NULL column to moderators table  
- Added database index for university column for better query performance

#### Created `database/add_university_to_moderators.php`
- Migration script for existing installations
- Safely adds university columns if they don't exist
- Sets default university to 'University of Moratuwa' for existing moderators
- Includes proper error handling

### 2. Model Changes

#### Modified `app/models/Moderator.php`
- Added `university` and `university_name` to `$allowedColumns` array
- Enhanced validation to require university selection
- Added `getAvailableUniversities()` static method with predefined university list:
  - University of Moratuwa
  - University of Peradeniya
  - University of Colombo
  - University of Kelaniya
  - University of Sri Jayewardenepura
- Added `getByUniversity()` method to filter moderators by university

### 3. Controller Changes

#### Modified `app/controllers/Admin/Moderator_create.php`
- Added university data to view context
- Enhanced form validation to require university selection
- Updated moderator creation logic to include university fields
- Automatic mapping of university key to university name

### 4. View Changes

#### Modified `app/views/Admin/moderator_create.view.php`
- Added university dropdown selection field
- Styled select element to match existing form controls
- Added proper form validation and error handling for university field
- Maintains selected value on form validation errors

#### Modified `app/views/Admin/moderators_list.view.php`
- Added University column to moderators table
- Displays university_name for better readability
- Maintains table responsiveness and styling

## Available Universities

The system includes the following universities:
- `university-of-moratuwa` → University of Moratuwa
- `university-of-peradeniya` → University of Peradeniya
- `university-of-colombo` → University of Colombo
- `university-of-kelaniya` → University of Kelaniya
- `university-of-sri-jayewardenepura` → University of Sri Jayewardenepura

## Database Schema

```sql
-- New moderators table structure
CREATE TABLE moderators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    university VARCHAR(100) NOT NULL,
    university_name VARCHAR(255) NOT NULL,
    assigned_by INT NOT NULL,
    permissions JSON NULL DEFAULT ('[]'),
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_university (university),
    INDEX idx_assigned_by (assigned_by),
    FOREIGN KEY (assigned_by) REFERENCES admins(id) ON DELETE CASCADE
);
```

## Installation Instructions

### For New Installations
1. Run the regular setup: `php database/setup.php`
2. The university columns will be included automatically

### For Existing Installations
1. Run the migration script: `php database/add_university_to_moderators.php`
2. Update existing moderator university assignments as needed
3. Default assignment is 'University of Moratuwa'

## Usage

1. **Creating Moderators**: Admins must now select a university when creating moderators
2. **University Restriction**: Moderators can only moderate content from their assigned university
3. **Listing**: Moderator lists now display university information for easy identification

## Future Enhancements

Consider implementing:
- University-specific event filtering for moderators
- University-specific user management
- Bulk moderator university updates
- University hierarchy/grouping features

## Testing

After implementation:
1. Test moderator creation with university selection
2. Verify form validation works for university field
3. Check moderator listing displays university information
4. Test existing moderator functionality remains intact
5. Verify database migration works for existing installations