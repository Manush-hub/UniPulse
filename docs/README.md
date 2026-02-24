# UniPulse Documentation

This directory contains all project documentation organized by feature/module.

## 📚 Documentation Index

### Core Features

#### Landing Page
- [Landing Template Unification](LANDING_TEMPLATE_UNIFICATION.md) - Unified landing page template implementation

#### Events
- [Event Boosting Guide](EVENT_BOOSTING_GUIDE.md) - Event boosting feature documentation
- [Event Filtering by Date](EVENT_FILTERING_BY_DATE.md) - Date-based filtering implementation
- [Event Visibility Filtering README](EVENT_VISIBILITY_FILTERING_README.md) - Visibility rules for users and organizers
- [Registered Events Dashboard](REGISTERED_EVENTS_DASHBOARD_IMPLEMENTATION.md) - User event registration dashboard

#### Profile Management
- [Profile Setup Guide](PROFILE_SETUP_GUIDE.md) - Initial profile setup
- [Profile CRUD Implementation](PROFILE_CRUD_IMPLEMENTATION.md) - Profile operations
- [Profile Photo Auto Save](PROFILE_PHOTO_AUTO_SAVE.md) - Auto-save functionality
- [Profile Photo Auto Save Complete](PROFILE_PHOTO_AUTO_SAVE_COMPLETE.md) - Complete implementation
- [Profile Photo Code Examples](PROFILE_PHOTO_CODE_EXAMPLES.md) - Code samples
- [Profile Photo Docs Index](PROFILE_PHOTO_DOCS_INDEX.md) - Photo feature index
- [Profile Photo Implementation Summary](PROFILE_PHOTO_IMPLEMENTATION_SUMMARY.md) - Summary
- [Profile Photo Quick Reference](PROFILE_PHOTO_QUICK_REFERENCE.md) - Quick reference
- [Profile Photo Testing](PROFILE_PHOTO_TESTING.md) - Testing guide

#### Gallery System
- [Gallery Fix Quick Start](GALLERY_FIX_QUICK_START.md) - Quick start guide
- [Gallery Fix README](GALLERY_FIX_README.md) - Main gallery documentation
- [Gallery Persistence Changelog](GALLERY_PERSISTENCE_CHANGELOG.md) - Change history
- [Gallery Persistence Complete](GALLERY_PERSISTENCE_COMPLETE.md) - Complete implementation
- [Gallery Persistence Fix](GALLERY_PERSISTENCE_FIX.md) - Persistence fixes
- [Gallery Persistence Implementation](GALLERY_PERSISTENCE_IMPLEMENTATION.md) - Implementation guide
- [Gallery Persistence Index](GALLERY_PERSISTENCE_INDEX.md) - Feature index
- [Gallery Persistence Setup](GALLERY_PERSISTENCE_SETUP.md) - Setup guide
- [Gallery Photo Album Fix](GALLERY_PHOTO_ALBUM_FIX.md) - Album fixes
- [Gallery Quick Reference](GALLERY_QUICK_REF.md) - Quick reference
- [Gallery Save Button Fix](GALLERY_SAVE_BUTTON_FIX.md) - Save button fixes

#### Payments & Donations
- [Payment Gateway Guide](PAYMENT_GATEWAY_GUIDE.md) - Payment integration
- [Payment Implementation](PAYMENT_IMPLEMENTATION.md) - Implementation details

#### Activity Tracking
- [Recent Activity Implementation](RECENT_ACTIVITY_IMPLEMENTATION.md) - Activity tracking
- [Recent Activity Quick Setup](RECENT_ACTIVITY_QUICK_SETUP.md) - Quick setup

#### Other
- [Before and After](BEFORE_AND_AFTER.md) - Comparison documentation
- [Implementation Complete](IMPLEMENTATION_COMPLETE.md) - Completion status

## 📁 Project Structure

```
unipulse/
├── app/
│   ├── controllers/      # MVC Controllers
│   ├── models/          # Data models
│   ├── views/           # View templates (including unified landing.view.php)
│   └── Core/            # Core framework files
├── database/            # Database migrations
├── public/              # Public assets
│   ├── assets/
│   │   ├── css/        # Stylesheets
│   │   └── js/         # JavaScript files (including unified landing-app.js)
│   └── uploads/        # User uploads
└── docs/               # Documentation (this folder)
```

## 🔄 Recent Changes

### Landing Page Unification (Latest)
- Merged User, Publisher, and Sponsor landing pages into single unified template
- Created role-based configuration system
- Consolidated JavaScript into single file with role adaptation
- Removed duplicate files to reduce codebase complexity

### Cleanup Operations
- Removed test/debug files from root directory
- Organized all documentation into docs/ folder
- Removed empty migration files
- Eliminated duplicate view and JavaScript files

## 🚀 Quick Links

- **Getting Started**: [Profile Setup Guide](PROFILE_SETUP_GUIDE.md)
- **Latest Feature**: [Landing Template Unification](LANDING_TEMPLATE_UNIFICATION.md)
- **Payment Integration**: [Payment Gateway Guide](PAYMENT_GATEWAY_GUIDE.md)
- **Event Management**: [Event Boosting Guide](EVENT_BOOSTING_GUIDE.md)
- **Event Visibility**: [Event Visibility Filtering README](EVENT_VISIBILITY_FILTERING_README.md)

## 📝 Contributing

When adding new documentation:
1. Create markdown files in this `docs/` directory
2. Follow the naming convention: `FEATURE_DESCRIPTION.md`
3. Update this README with a link to your documentation
4. Keep documentation up-to-date with code changes

## 📧 Support

For questions or issues, refer to the specific feature documentation or contact the development team.
