<?php
/**
 * Header Selector Component
 * Determines which header component to load based on current user type
 */

// Get current user type from HeaderService
$currentUserType = HeaderService::getCurrentUserType();
$isAuthenticated = HeaderService::isAuthenticated();

// Set page config if not already set
if (!isset($pageConfig)) {
    $pageConfig = [];
}

// Determine which header component to load
if (!$isAuthenticated) {
    // Load default header for guests
    include 'header.php';
} else {
    switch ($currentUserType) {
        case 'admin':
            include 'admin-header.php';
            break;
            
        case 'moderator':
            include 'moderator-header.php';
            break;
            
        case 'sponsor':
            include 'sponsor-header.php';
            break;
            
        case 'publisher':
            include 'publisher-header.php';
            break;
            
        case 'university':
            include 'university-header.php';
            break;
            
        case 'public':
        default:
            // Load default header for public users and fallback
            include 'header.php';
            break;
    }
}
?>