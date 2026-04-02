<?php
$pageConfig = isset($pageConfig) && is_array($pageConfig) ? $pageConfig : [];

if (empty($headerCssLoaded)) {
    echo '<link rel="stylesheet" href="/unipulse/public/assets/css/Components/header-style.css">';
    $headerCssLoaded = true;
}

$currentUserType = strtolower($_SESSION['user_type'] ?? '');
$isAuthenticated = !empty($_SESSION['user_id']) && !empty($currentUserType);
$roleHeaderMap = [
    'admin' => 'Admin',
    'moderator' => 'Moderator',
    'publisher' => 'Publisher',
    'sponsor' => 'Sponsor',
    'public' => 'User',
    'university' => 'User',
    'user' => 'User'
];

if ($isAuthenticated) {
    $headerRole = $roleHeaderMap[$currentUserType] ?? 'User';
    $roleHeaderPath = __DIR__ . '/../' . $headerRole . '/components/header.php';

    if (file_exists($roleHeaderPath)) {
        include $roleHeaderPath;
    } else {
        include __DIR__ . '/../header.php';
    }
} else {
    include __DIR__ . '/../header.php';
}
