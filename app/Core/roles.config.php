<?php

// Role-based configuration
return [
    'roles' => [
        'User' => [
            'level' => 1,
            'permissions' => ['view_events', 'join_events', 'view_profile'],
            'default_dashboard' => 'dashboard',
            'allowed_controllers' => ['Dashboard', 'Landing', 'Events', 'Profile']
        ],
        'Sponsor' => [
            'level' => 2,
            'permissions' => ['view_events', 'sponsor_events', 'view_analytics'],
            'default_dashboard' => 'dashboard',
            'allowed_controllers' => ['Dashboard', 'Events', 'Analytics', 'Profile']
        ],
        'Publisher' => [
            'level' => 3,
            'permissions' => ['view_events', 'create_events', 'edit_events', 'view_analytics'],
            'default_dashboard' => 'dashboard',
            'allowed_controllers' => ['Dashboard', 'Events', 'Analytics', 'Profile']
        ],
        'Moderator' => [
            'level' => 4,
            'permissions' => ['view_events', 'moderate_events', 'manage_users', 'view_reports'],
            'default_dashboard' => 'dashboard',
            'allowed_controllers' => ['Dashboard', 'Events', 'Users', 'Reports', 'Profile']
        ],
        'Admin' => [
            'level' => 5,
            'permissions' => ['*'], // All permissions
            'default_dashboard' => 'dashboard',
            'allowed_controllers' => ['*'] // All controllers
        ]
    ],
    
    'default_role' => 'User',
    
    'navigation' => [
        'User' => [
            ['name' => 'Dashboard', 'url' => '/user/dashboard', 'icon' => 'dashboard'],
            ['name' => 'Events', 'url' => '/user/events', 'icon' => 'events'],
            ['name' => 'Profile', 'url' => '/user/profile', 'icon' => 'profile']
        ],
        'Publisher' => [
            ['name' => 'Dashboard', 'url' => '/publisher/dashboard', 'icon' => 'dashboard'],
            ['name' => 'My Events', 'url' => '/publisher/events', 'icon' => 'events'],
            ['name' => 'Analytics', 'url' => '/publisher/analytics', 'icon' => 'analytics'],
            ['name' => 'Profile', 'url' => '/publisher/profile', 'icon' => 'profile']
        ],
        'Admin' => [
            ['name' => 'Dashboard', 'url' => '/admin/dashboard', 'icon' => 'dashboard'],
            ['name' => 'Users', 'url' => '/admin/users', 'icon' => 'users'],
            ['name' => 'Events', 'url' => '/admin/events', 'icon' => 'events'],
            ['name' => 'Reports', 'url' => '/admin/reports', 'icon' => 'reports'],
            ['name' => 'Settings', 'url' => '/admin/settings', 'icon' => 'settings']
        ]
    ]
];
