<?php

class AuthService
{

    use Model;

    /**
     * Authenticate user across all user tables
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public function authenticate($email, $password)
    {
        error_log("AuthService::authenticate called for email: $email");

        $userTables = [
            'admins' => [
                'model' => 'Admin',
                'dashboard' => '/unipulse/public/admin/dashboard',
                'type' => 'admin'
            ],
            'moderators' => [
                'model' => 'Moderator',
                'dashboard' => '/unipulse/public/moderator/dashboard',
                'type' => 'moderator'
            ],
            'public_users' => [
                'model' => 'PublicUser',
                'dashboard' => '/unipulse/public/user/dashboard',
                'type' => 'public'
            ],
            'university_users' => [
                'model' => 'UniversityUser',
                'dashboard' => '/unipulse/public/user/dashboard',
                'type' => 'university'
            ],
            'sponsors' => [
                'model' => 'Sponsor',
                'dashboard' => '/unipulse/public/sponsor/dashboard',
                'type' => 'sponsor'
            ],
            'publishers' => [
                'model' => 'Publisher',
                'dashboard' => '/unipulse/public/publisher/dashboard',
                'type' => 'publisher'
            ]
        ];

        foreach ($userTables as $table => $config) {
            error_log("Checking table: $table");
            $user = $this->findUserInTable($table, $email, $password);
            if ($user === 'suspended') {
                error_log("User is suspended");
                return 'suspended';
            }
            if ($user) {
                error_log("User found in table: $table");
                return [
                    'user' => $user,
                    'type' => $config['type'],
                    'dashboard' => $config['dashboard'],
                    'table' => $table
                ];
            }
        }

        error_log("User not found in any table");
        return false;
    }

    /**
     * Find user in specific table and verify password
     */
    private function findUserInTable($table, $email, $password)
    {
        error_log("Searching in table $table for email: $email");

        $query = "SELECT * FROM $table WHERE email = :email LIMIT 1";
        $user = $this->getRow($query, ['email' => $email]);

        if ($user) {
            error_log("User found in $table, verifying password");

            // Check if account is suspended
            if (isset($user->is_suspended) && $user->is_suspended) {
                error_log("Login denied - account is suspended");
                $_SESSION['suspension_info'] = [
                    'email' => $email,
                    'reason' => $user->suspension_reason ?? 'No reason provided',
                    'user_type' => $table,
                    'user_id' => $user->id
                ];
                return 'suspended';
            }

            // Special check for publishers - they must be approved
            if ($table === 'publishers') {
                if ($user->approval_status !== 'approved' || !$user->is_active) {
                    error_log("Publisher login denied - not approved or inactive");
                    return false;
                }
            }

            if ($this->verifyPassword($password, $user->password_hash)) {
                // Moderators edited as inactive are reactivated on successful sign-in.
                if ($table === 'moderators' && isset($user->is_active) && (int)$user->is_active === 0) {
                    $reactivated = $this->reactivateModeratorAccount((int)$user->id);
                    if ($reactivated) {
                        $user->is_active = 1;
                        error_log('Moderator account reactivated on login for user ID: ' . (int)$user->id);
                    } else {
                        error_log('Failed to reactivate moderator account on login for user ID: ' . (int)$user->id);
                    }
                }

                // Soft-deleted accounts are reactivated on successful sign-in.
                if (isset($user->is_deleted) && (int)$user->is_deleted === 1) {
                    $this->reactivateSoftDeletedAccount($table, (int)$user->id);
                    $user->is_deleted = 0;
                    $this->queueAccountReactivatedMessage($user->email ?? '', $table);
                }

                error_log("Password verification successful for $table");
                return $user;
            } else {
                error_log("Password verification failed for $table");
            }
        } else {
            error_log("No user found in $table");
        }

        return false;
    }

    /**
     * Reactivate a soft-deleted account.
     */
    private function reactivateSoftDeletedAccount($table, $userId)
    {
        $query = "UPDATE {$table} SET is_deleted = 0, updated_at = CURRENT_TIMESTAMP WHERE id = :id";

        try {
            $conn = $this->connect();
            $stmt = $conn->prepare($query);
            return $stmt->execute(['id' => $userId]);
        } catch (Exception $e) {
            error_log('Failed to reactivate soft-deleted account: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Reactivate an inactive moderator account.
     */
    private function reactivateModeratorAccount($moderatorId)
    {
        $query = "UPDATE moderators SET is_active = 1 WHERE id = :id";

        try {
            $conn = $this->connect();
            $stmt = $conn->prepare($query);
            return $stmt->execute(['id' => $moderatorId]);
        } catch (Exception $e) {
            error_log('Failed to reactivate moderator account: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Store a one-time message for the next dashboard load after reactivation.
     */
    private function queueAccountReactivatedMessage($email, $table)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $labels = [
            'public_users' => 'account',
            'university_users' => 'account',
            'publishers' => 'organization account',
            'sponsors' => 'sponsor account'
        ];

        $label = $labels[$table] ?? 'account';
        $_SESSION['account_reactivated_success'] = 'Your ' . $label . ' has been reactivated successfully.';
        $_SESSION['account_reactivated_email'] = $email;
    }

    /**
     * Verify password against hash
     */
    private function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Start user session
     */
    public function startSession($userData)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $userData['user']->id;
        $_SESSION['user_email'] = $userData['user']->email;
        $_SESSION['user_type'] = $userData['type'];
        $_SESSION['user_table'] = $userData['table'];
        $_SESSION['logged_in'] = true;

        // Set user name based on user type
        switch ($userData['type']) {
            case 'admin':
            case 'moderator':
            case 'public':
            case 'university':
                $_SESSION['user_name'] = $userData['user']->full_name;
                // Ensure avatar is available immediately after first login redirect.
                if (!empty($userData['user']->profile_photo)) {
                    $_SESSION['user_profile_photo'] = $userData['user']->profile_photo;
                }
                if (!empty($userData['user']->cover_photo)) {
                    $_SESSION['user_cover_photo'] = $userData['user']->cover_photo;
                }
                // Store university for university students
                if ($userData['type'] === 'university' && isset($userData['user']->university)) {
                    $_SESSION['user_university'] = $userData['user']->university;
                }
                if ($userData['type'] === 'university' && isset($userData['user']->faculty)) {
                    $_SESSION['user_faculty'] = $userData['user']->faculty;
                }
                // Store university for moderators
                if ($userData['type'] === 'moderator' && isset($userData['user']->university)) {
                    $_SESSION['user_university'] = $userData['user']->university;
                }
                break;
            case 'sponsor':
                $_SESSION['user_name'] = $userData['user']->company_name;
                // Load sponsor profile logo for header
                $sponsorModel = new Sponsor();
                $profileData = $sponsorModel->getProfileData($userData['user']->id);
                if ($profileData && isset($profileData->logo_url)) {
                    $_SESSION['user_logo'] = $profileData->logo_url;
                }
                break;
            case 'publisher':
                $_SESSION['user_name'] = $userData['user']->society_name;
                // Store university for publishers
                if (isset($userData['user']->university)) {
                    $_SESSION['user_university'] = $userData['user']->university;
                }
                if (isset($userData['user']->faculty)) {
                    $_SESSION['user_faculty'] = $userData['user']->faculty;
                }
                // Load publisher profile logo for header
                $publisherModel = new Publisher();
                $profileData = $publisherModel->getProfileData($userData['user']->id);
                if ($profileData && isset($profileData->logo_url)) {
                    $_SESSION['user_profile_photo'] = $profileData->logo_url;
                    $_SESSION['profile_photo'] = $profileData->logo_url;
                }
                break;
        }

        return true;
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Get current user data
     */
    public static function getCurrentUser()
    {
        if (!self::isLoggedIn()) {
            return false;
        }

        $user = [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'name' => $_SESSION['user_name'],
            'type' => $_SESSION['user_type'],
            'table' => $_SESSION['user_table']
        ];

        // Include university if it exists in session
        if (isset($_SESSION['user_university'])) {
            $user['university'] = $_SESSION['user_university'];
        }
        if (isset($_SESSION['user_faculty'])) {
            $user['faculty'] = $_SESSION['user_faculty'];
        }

        return $user;
    }

    /**
     * Logout user
     */
    public static function logout()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        session_destroy();
        return true;
    }

    /**
     * Redirect to appropriate dashboard based on user type
     */
    public function redirectToDashboard($userType, $userId = null)
    {
        $dashboards = [
            'admin' => '/unipulse/public/admin/dashboard',
            'moderator' => '/unipulse/public/moderator/dashboard',
            'public' => '/unipulse/public/user/landing',
            'university' => '/unipulse/public/user/landing',
            'sponsor' => '/unipulse/public/sponsor/dashboard',
            'publisher' => '/unipulse/public/publisher/dashboard'
        ];

        $dashboard = $this->getPostLoginRedirectPath($userType, $userId);
        header('Location: ' . $dashboard);
        exit();
    }

    /**
     * Resolve post-login redirect path with profile completion checks.
     */
    public function getPostLoginRedirectPath($userType, $userId = null)
    {
        $dashboards = [
            'admin' => '/unipulse/public/admin/dashboard',
            'moderator' => '/unipulse/public/moderator/dashboard',
            'public' => '/unipulse/public/user/landing',
            'university' => '/unipulse/public/user/landing',
            'sponsor' => '/unipulse/public/sponsor/dashboard',
            'publisher' => '/unipulse/public/publisher/dashboard'
        ];

        $defaultPath = $dashboards[$userType] ?? '/unipulse/public/user/dashboard';

        if (!$userId || !in_array($userType, ['sponsor', 'publisher'], true)) {
            return $defaultPath;
        }

        if ($userType === 'sponsor') {
            $sponsorModel = new Sponsor();
            $sponsorData = $sponsorModel->findById($userId);
            $profileData = $sponsorModel->getProfileData($userId);

            $hasRequiredBasics = $sponsorData
                && !empty(trim((string)($sponsorData->company_name ?? '')))
                && !empty(trim((string)($sponsorData->phone ?? '')));

            $hasRequiredProfile = $profileData
                && !empty(trim((string)($profileData->sponsor_type ?? '')))
                && !empty(trim((string)($profileData->industry ?? '')))
                && !empty(trim((string)($profileData->about ?? '')))
                && !empty(trim((string)($profileData->logo_url ?? '')))
                && !empty(trim((string)($profileData->cover_photo_url ?? '')));

            if (!$hasRequiredBasics || !$hasRequiredProfile) {
                error_log('Sponsor profile incomplete for user ' . (int)$userId . ': ' . json_encode([
                    'company_name' => !empty(trim((string)($sponsorData->company_name ?? ''))),
                    'phone' => !empty(trim((string)($sponsorData->phone ?? ''))),
                    'sponsor_type' => !empty(trim((string)($profileData->sponsor_type ?? ''))),
                    'industry' => !empty(trim((string)($profileData->industry ?? ''))),
                    'about' => !empty(trim((string)($profileData->about ?? ''))),
                    'logo_url' => !empty(trim((string)($profileData->logo_url ?? ''))),
                    'cover_photo_url' => !empty(trim((string)($profileData->cover_photo_url ?? '')))
                ]));
                return '/unipulse/public/sponsor/profile?required=1';
            }
        }

        if ($userType === 'publisher') {
            $publisherModel = new Publisher();
            $profileData = $publisherModel->getProfileData($userId);

            $hasRequiredImages = $profileData
                && !empty(trim((string)($profileData->logo_url ?? '')))
                && !empty(trim((string)($profileData->cover_photo_url ?? '')));

            if (!$hasRequiredImages) {
                return '/unipulse/public/publisher/profile?required=1';
            }
        }

        return $defaultPath;
    }
}
