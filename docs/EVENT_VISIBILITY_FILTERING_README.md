# Event Visibility Filtering README

## Overview

This document explains how event visibility works in UniPulse, including the **organizer-to-organizer (publisher-to-publisher)** behavior.

Visibility is controlled by `events.visibility` with four values:
- `faculty-only`
- `university-only`
- `all-universities`
- `public`

## Organizer-to-Organizer Rules (Publisher View)

When a logged-in publisher views events created by another publisher:

1. **Faculty Only**
   - Event is visible only if:
     - viewer publisher `university` matches event `university`, and
     - viewer publisher `faculty` matches event `faculty_department`

2. **University Only**
   - Event is visible only if:
     - viewer publisher `university` matches event `university`

3. **All Universities**
   - Event is visible to publisher accounts.

4. **Public**
   - Event is visible to publisher accounts.

This is implemented by comparing publisher profile values from the `publishers` table (`university`, `faculty`) against event audience fields.

## Event Creation Behavior

When organizers publish events:

- If visibility is `faculty-only`:
  - Event `university` is set from the organizer publisher profile.
  - Event `faculty_department` is set from the organizer publisher profile.

- If visibility is `university-only`:
  - Event `university` is set from the organizer publisher profile.

This prevents mismatched audience values during creation.

## Core Implementation Points

- `app/models/Event.php`
  - `buildVisibilityFilter($currentUser)`
  - Applies visibility logic for event queries across modules.

- `app/models/Publisher.php`
  - `getUpcomingEvents($publisherId, $currentUser = null)`
  - `getPastEvents($publisherId, $currentUser = null)`
  - `getAllPublisherEvents($publisherId, $currentUser = null)`
  - Enforces organizer-to-organizer visibility filtering.

- `app/Core/AuthService.php`
  - Stores `user_university` and `user_faculty` in session for publisher/university users.

## Database Fields Used

- `events.visibility`
- `events.university`
- `events.faculty_department`
- `publishers.university`
- `publishers.faculty`

## Access Summary (Publisher Viewer)

- Sees `public` and `all-universities` events.
- Sees `university-only` events only for same university.
- Sees `faculty-only` events only for same university + same faculty.

## Test Checklist

1. Login as publisher A (University X, Faculty Y), create one event for each visibility type.
2. Login as publisher B (University X, Faculty Y):
   - should see all 4 events.
3. Login as publisher C (University X, Faculty Z):
   - should not see `faculty-only` from A.
4. Login as publisher D (University Q, Faculty R):
   - should see only `public` and `all-universities` from A.
5. Verify results on:
   - publisher profile upcoming/past event lists,
   - public publisher page event lists,
   - any organizer-facing event list endpoint.

## Important Note

After deploying visibility changes, users should log out and log in again so session values (`user_university`, `user_faculty`) are refreshed.
