# Publisher Approval System Implementation

## Overview
The publisher approval system has been successfully implemented to ensure that only verified publishers can publish events on the UniPulse platform. This system requires moderator approval for all publisher registrations before they can access their dashboard.

## Key Components

### 1. Database Structure
- **Publishers Table**: Stores publisher registration data with approval status
  - `approval_status`: ENUM('pending', 'approved', 'rejected')
  - `approved_by`: Foreign key to moderators table
  - `approved_at`: Timestamp of approval
  - `rejection_reason`: Text field for rejection reasons
  - `confirmation_document`: Path to uploaded verification document

- **Publisher Approval Notifications Table**: Tracks notifications between publishers and moderators
  - Links publishers and moderators for notification tracking
  - Stores notification types (pending_approval, approved, rejected)

### 2. Publisher Registration Flow
1. **Registration**: Publishers fill out the registration form with:
   - Society/Club information
   - University and faculty details
   - Contact information
   - Verification document upload
   - Status automatically set to 'pending'

2. **Moderator Notification**: Upon registration:
   - System notifies moderators of the same university
   - Creates notification entries in the database
   - Moderators see pending approvals in their dashboard

3. **Approval Process**: Moderators can:
   - View pending publisher registrations
   - Review uploaded verification documents
   - Approve or reject applications
   - Add rejection reasons when necessary

4. **Access Control**: 
   - Publishers cannot login until approved
   - Clear error messages explain approval status
   - Approved publishers gain full access to publish events

### 3. Moderator Dashboard Integration
- **Statistics Display**: Shows pending, approved, and rejected publisher counts
- **Quick Actions**: Direct links to publisher approval page
- **Recent Pending**: Preview of latest publisher registrations awaiting approval
- **Permission-Based Access**: Only moderators with approval permissions can access

### 4. Security Features
- **University-Based Access**: Moderators can only approve publishers from their university
- **Document Verification**: File upload validation (PDF, DOC, images up to 5MB)
- **Authentication Checks**: Prevents unauthorized access to approval functions
- **Audit Trail**: Tracks who approved/rejected each publisher and when

## File Structure

### Controllers
- `app/controllers/Moderator/PublisherApproval.php` - Handles approval workflow
- `app/controllers/Moderator/Dashboard.php` - Updated with publisher stats
- `app/controllers/Publisherreg.php` - Updated with approval status handling
- `app/controllers/Signin.php` - Enhanced with publisher status error messages

### Models
- `app/models/Publisher.php` - Extended with approval management methods
- `app/models/Moderator.php` - Added publisher approval permissions and stats
- `app/models/User.php` - Updated for publisher integration

### Views
- `app/views/Moderator/publisher_approval.view.php` - Main approval interface
- `app/views/Moderator/dashboard.view.php` - Updated with publisher sections
- `app/views/Moderator/access_denied.view.php` - Permission denied page

### Database Migration
- `database/create_publishers_table.php` - Creates all necessary tables

### Assets
- `public/assets/css/Moderator/publisher-approval-style.css` - Styling for approval interface

## Usage Instructions

### For Publishers
1. Register through `/unipulse/public/publisherreg`
2. Upload required verification document
3. Wait for university moderator approval
4. Receive email notification upon approval/rejection
5. Login to access dashboard after approval

### For Moderators
1. Access dashboard at `/unipulse/public/moderator/dashboard`
2. View pending approvals in "Publisher Approvals" section
3. Click "View All" to see full approval interface
4. Review publisher information and documents
5. Approve or reject with appropriate reasons

### For System Administrators
1. Run database migration: `php database/create_publishers_table.php`
2. Ensure moderators have proper university assignments
3. Monitor approval notifications and statistics

## Database Migration

### Important Note
If you have an existing publishers table with different column names, run the migration script to update it:

```bash
php database/update_publishers_table.php
```

This script will:
- Rename `verification_status` to `approval_status`
- Rename `verification_notes` to `rejection_reason`
- Add required columns (`approved_by`, `approved_at`)
- Create the `publisher_approval_notifications` table
- Update the `users` table to support publisher user type

### Data Format
The system returns database results as objects (stdClass), not arrays. All view templates and controllers have been updated to handle this format using object notation (e.g., `$publisher->society_name` instead of `$publisher['society_name']`).

## Technical Implementation

### Key Methods
- `Publisher::approve($publisherId, $moderatorId)` - Approves publisher
- `Publisher::reject($publisherId, $moderatorId, $reason)` - Rejects with reason
- `Publisher::getPendingByUniversity($university)` - Gets pending for university
- `Moderator::hasPermission($moderatorId, $permission)` - Checks permissions
- `AuthService::findUserInTable()` - Updated with publisher approval checks

### API Endpoints
- `POST /moderator/publisherapproval/approve/{id}` - Approve publisher
- `POST /moderator/publisherapproval/reject/{id}` - Reject publisher
- `GET /moderator/publisherapproval/view/{id}` - View publisher details

### Security Measures
- CSRF protection through form validation
- University-based access control
- File upload security with type and size validation
- SQL injection prevention through prepared statements
- XSS prevention through proper escaping

## Benefits
1. **Quality Control**: Ensures only legitimate societies/clubs can publish events
2. **University Autonomy**: Each university controls its own publisher approvals
3. **Audit Trail**: Complete tracking of approval decisions
4. **User Experience**: Clear feedback on application status
5. **Scalability**: System handles multiple universities and moderators
6. **Security**: Robust authentication and authorization controls

The system is now fully functional and ready for production use. All publisher registrations will require moderator approval before publishers can access their dashboards and publish events.