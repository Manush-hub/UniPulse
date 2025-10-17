<?php

/**
 * Header Service
 * Provides easy access to header data for all views
 */
class HeaderService {
    
    private static $headerController = null;
    
    /**
     * Get header controller instance
     */
    private static function getHeaderController() {
        if (self::$headerController === null) {
            self::$headerController = new HeaderController();
        }
        return self::$headerController;
    }
    
    /**
     * Get current user header data
     */
    public static function getCurrentUserData() {
        return self::getHeaderController()->getCurrentUserHeaderData();
    }
    
    /**
     * Get navigation items for current user
     */
    public static function getNavigationItems() {
        return self::getHeaderController()->getNavigationItems();
    }
    
    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated() {
        $userData = self::getCurrentUserData();
        return isset($userData['is_authenticated']) && $userData['is_authenticated'];
    }
    
    /**
     * Get user type
     */
    public static function getUserType() {
        $userData = self::getCurrentUserData();
        return $userData['user_type'] ?? 'guest';
    }
    
    /**
     * Get formatted user display name
     */
    public static function getUserDisplayName() {
        $userData = self::getCurrentUserData();
        return $userData['full_name'] ?? 'Guest';
    }
    
    /**
     * Get user's role/label for display
     */
    public static function getUserDisplayLabel() {
        $userData = self::getCurrentUserData();
        return $userData['display_label'] ?? '';
    }
    
    /**
     * Get user's avatar URL
     */
    public static function getUserAvatarUrl() {
        $userData = self::getCurrentUserData();
        return $userData['avatar_url'] ?? '/unipulse/public/assets/images/default-avatar.png';
    }
    
    /**
     * Get dashboard URL for current user
     */
    public static function getDashboardUrl() {
        $userData = self::getCurrentUserData();
        return $userData['dashboard_url'] ?? '/unipulse/public/';
    }
    
    /**
     * Get profile URL for current user
     */
    public static function getProfileUrl() {
        $userData = self::getCurrentUserData();
        return $userData['profile_url'] ?? '/unipulse/public/user/profile';
    }
}