<?php
session_start();
require __DIR__ . '/app/Core/init.php';
require_once __DIR__ . '/app/Core/AuthService.php';
header('Content-Type: application/json');

try {
    if (!AuthService::isLoggedIn()) {
        echo json_encode(['success' => false, 'error' => 'Not authenticated', 'debug' => 'Please log in first']);
        exit;
    }

    $u = AuthService::getCurrentUser();

    // Choose model based on user type
    if ($u['type'] === 'university') {
        require_once __DIR__ . '/app/models/UniversityUser.php';
        $model = new UniversityUser();
    } else {
        require_once __DIR__ . '/app/models/PublicUser.php';
        $model = new PublicUser();
    }

    // Read JSON payload if provided, else use defaults
    $payload = json_decode(file_get_contents('php://input'), true) ?: [
        'firstname' => 'Test',
        'lastname' => 'User',
        'phone' => '0712345678',
        'gender' => 'male',
        'bio' => 'Updated from test endpoint.'
    ];

    $fields = [
        'full_name' => trim(($payload['firstname'] ?? '') . ' ' . ($payload['lastname'] ?? '')),
        'phone' => $payload['phone'] ?? null,
        'gender' => $payload['gender'] ?? null,
        'bio' => $payload['bio'] ?? null,
    ];
    $fields = array_filter($fields, fn($v) => $v !== null);

    $ok = $model->update($u['id'], $fields);

    $after = $model->find($u['id']);

    echo json_encode([
        'success' => (bool)$ok,
        'updated_fields' => $fields,
        'user_type' => $u['type'],
        'row_after' => $after,
    ]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
