# Profile-Based Database Management Implementation

## Overview
This implementation adds profile-based access control to the UniPulse event management system, ensuring users can only see events appropriate to their user type and university affiliation.

## Features Implemented

### 1. Database Schema Changes
- **Visibility Field**: Added `visibility` enum field to events table ('public', 'university')
- **Created By Tracking**: Added `created_by` and `created_by_type` fields for audit trails
- **Indexes**: Added performance indexes on visibility and created_by fields

### 2. User Access Control Rules

#### Public Users
- Can only see events marked as 'public'
- Cannot see university-specific events
- Must sign in to access university events if they are university users

#### University Users
- Can see their own university's events (both public and university-only)
- Can see public events from other universities
- Cannot see university-only events from other universities

#### Anonymous Users
- Can only see public events
- Prompted to sign in for full access

### 3. Code Changes

#### Event Model (`app/models/Event.php`)
- **New Method**: `getEventsForUser($userType, $userUniversity, $filters)` - Filters events based on user permissions
- **New Method**: `getUserUniversity($userId, $userType, $userTable)` - Retrieves user's university affiliation
- **Updated**: `allowedColumns` array to include 'visibility' field
- **Enhanced**: Filtering logic respects user permissions

#### Controllers Updated

##### Find_events Controller (`app/controllers/Find_events.php`)
- **Enhanced**: Now checks user authentication status
- **Enhanced**: Filters events based on user profile
- **Added**: Support for search and category filters
- **Added**: University-specific filtering

##### User Dashboard Controller (`app/controllers/User/Dashboard.php`)
- **Enhanced**: Shows events relevant to user's profile
- **Added**: University affiliation display
- **Added**: Personalized event recommendations

##### User Profile Controller (`app/controllers/User/Profile.php`)
- **Enhanced**: Complete profile management system
- **Added**: University-specific profile fields
- **Added**: Profile editing capabilities
- **Added**: User event history

##### Event View Controller (`app/controllers/Event.php`)
- **New**: Individual event viewing with permission checks
- **Added**: Event joining functionality
- **Added**: Similar events recommendations
- **Added**: Access control validation

#### Views Updated

##### Find Events View (`app/views/find_events.view.php`)
- **Complete Rewrite**: Dynamic event display from database
- **Added**: User status display in header
- **Added**: Profile-based messaging
- **Added**: Event visibility indicators
- **Added**: Advanced filtering interface
- **Added**: Responsive design improvements

### 4. Database Migration

#### Migration Script (`database/migrate_event_visibility.php`)
- **Adds**: `visibility` column to events table
- **Adds**: `created_by` and `created_by_type` columns
- **Updates**: Existing events with appropriate visibility settings
- **Creates**: Performance indexes
- **Sets**: Inter-university events as public

### 5. Security Features

#### Access Control
- **Session Validation**: All protected actions require valid authentication
- **Permission Checks**: Every event access validated against user permissions
- **University Validation**: University users' affiliation verified from database
- **Error Handling**: Graceful handling of unauthorized access attempts

#### Data Protection
- **SQL Injection Prevention**: All queries use prepared statements
- **XSS Prevention**: All output escaped with `htmlspecialchars()`
- **CSRF Protection**: Forms use proper HTTP methods
- **Input Validation**: All user inputs validated before processing

### 6. User Experience Improvements

#### Personalized Interface
- **Context-Aware Messaging**: Different messages for different user types
- **Personalized Recommendations**: Events filtered by user's university
- **Clear Visual Indicators**: Events tagged with visibility status
- **Smart Navigation**: Appropriate redirects based on user permissions

#### Search and Filtering
- **Advanced Search**: Text search across multiple event fields
- **Category Filtering**: Filter by event category
- **University Filtering**: Filter by university (respecting permissions)
- **Real-time Updates**: Search results update as user types

## How to Deploy

### 1. Run Database Migration
```bash
cd /Applications/MAMP/htdocs/unipulse
php database/migrate_event_visibility.php
```

### 2. Verify Database Changes
- Check that `visibility` column exists in events table
- Verify existing events have appropriate visibility settings
- Confirm indexes are created

### 3. Test User Scenarios

#### Test as Public User
1. Access find_events without login - should see only public events
2. Sign up as public user
3. Login and verify access to public events only

#### Test as University User
1. Sign up as university user with specific university
2. Login and verify access to own university events + public events
3. Verify cannot see other universities' private events

#### Test Event Filtering
1. Test search functionality
2. Test category filtering
3. Test university filtering (where permitted)

### 4. Verify Security
- Test unauthorized access attempts
- Verify proper error messages
- Check that sensitive data is not exposed

## Configuration

### Event Visibility Settings
Events can be set to:
- `'public'`: Visible to all users (default for inter-university events)
- `'university'`: Visible only to users from the same university

### User Types Supported
- `'public'`: General public users
- `'university'`: University-affiliated users
- `'admin'`: System administrators
- `'moderator'`: Content moderators
- `'sponsor'`: Event sponsors
- `'publisher'`: Event publishers

## Future Enhancements

### Potential Improvements
1. **Faculty-Level Filtering**: Further restrict events by faculty within university
2. **Event Categories**: More granular category-based permissions
3. **Approval Workflow**: Require approval for cross-university event visibility
4. **Event Analytics**: Track which user types engage with which events
5. **Social Features**: Allow users to follow universities or event types
6. **Notification System**: Notify users of relevant new events

### Scalability Considerations
1. **Caching**: Implement Redis/Memcached for event queries
2. **Database Optimization**: Add more indexes for complex queries
3. **API Development**: Create REST API for mobile app integration
4. **Load Balancing**: Design for horizontal scaling

## Troubleshooting

### Common Issues
1. **Migration Fails**: Check database permissions and connection settings
2. **Events Not Showing**: Verify user authentication and university affiliation
3. **Permission Denied**: Check event visibility settings and user type
4. **Search Not Working**: Verify database indexes and query syntax

### Debug Steps
1. Check PHP error logs
2. Verify database connection
3. Test queries directly in database
4. Check session data and user authentication status

This implementation provides a robust, secure, and user-friendly profile-based event management system that respects university boundaries while promoting public events appropriately.