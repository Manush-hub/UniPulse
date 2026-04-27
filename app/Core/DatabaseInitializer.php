<?php

class DatabaseInitializer
{

    use Database;

    private $requiredTables = [
        'university_users',
        'public_users',
        'publishers',
        'sponsors',
        'users',
        'events',
        'event_comments',
        'notifications'
    ];

    public function initializeDatabase()
    {
        try {
            // First ensure database exists
            $this->createDatabaseIfNotExists();

            // Check and create tables if needed
            $this->createTablesIfNotExist();

            return true;
        } catch (Exception $e) {
            error_log("Database initialization failed: " . $e->getMessage());
            return false;
        }
    }

    private function createDatabaseIfNotExists()
    {
        // Connect without selecting database first
        $string = "mysql:host=" . DBHOST . ";port=" . DBPORT;
        $conn = new PDO($string, DBUSER, DBPASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create database if it doesn't exist
        $conn->exec("CREATE DATABASE IF NOT EXISTS " . DBNAME . " CHARACTER SET " . DB_CHARSET . " COLLATE " . DB_COLLATION);

        // Select the database
        $conn->exec("USE " . DBNAME);
    }

    private function createTablesIfNotExist()
    {
        $conn = $this->connect();

        // Check which tables exist
        $existingTables = $this->getExistingTables($conn);

        // Create missing tables
        foreach ($this->requiredTables as $table) {
            if (!in_array($table, $existingTables)) {
                $this->createTable($conn, $table);
            }
        }
    }

    private function getExistingTables($conn)
    {
        $stmt = $conn->prepare("SHOW TABLES");
        $stmt->execute();
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $tables;
    }

    private function createTable($conn, $tableName)
    {
        $sql = '';

        switch ($tableName) {
            case 'university_users':
                $sql = $this->getUniversityUsersTableSQL();
                break;
            case 'public_users':
                $sql = $this->getPublicUsersTableSQL();
                break;
            case 'publishers':
                $sql = $this->getPublishersTableSQL();
                break;
            case 'sponsors':
                $sql = $this->getSponsorsTableSQL();
                break;
            case 'users':
                $sql = $this->getUsersTableSQL();
                break;
            case 'events':
                $sql = $this->getEventsTableSQL();
                break;
            case 'event_comments':
                $sql = $this->getEventCommentsTableSQL();
                break;
            case 'notifications':
                $sql = $this->getNotificationsTableSQL();
                break;
        }

        if ($sql) {
            $sql = str_replace(
                'DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
                'DEFAULT CHARSET=' . DB_CHARSET . ' COLLATE=' . DB_COLLATION,
                $sql
            );
            $conn->exec($sql);
            error_log("Created table: $tableName");
        }
    }

    private function getUniversityUsersTableSQL()
    {
        return "
            CREATE TABLE university_users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                full_name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                phone VARCHAR(20) NOT NULL,
                country_code VARCHAR(5) NOT NULL DEFAULT '+94',
                password_hash VARCHAR(255) NOT NULL,
                university VARCHAR(100) NOT NULL,
                faculty VARCHAR(100) NOT NULL,
                student_staff_id VARCHAR(50) NOT NULL,
                academic_year VARCHAR(50) NOT NULL,
                nic VARCHAR(20) NOT NULL UNIQUE,
                gender ENUM('male', 'female', 'other', 'prefer-not-to-say') NULL,
                interests JSON NULL,
                user_role ENUM('student', 'staff') DEFAULT 'student',
                is_verified BOOLEAN DEFAULT FALSE,
                is_deleted TINYINT(1) NOT NULL DEFAULT 0,
                email_verified_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_university (university),
                INDEX idx_faculty (faculty),
                INDEX idx_student_staff_id (student_staff_id),
                INDEX idx_nic (nic)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
    }

    private function getPublicUsersTableSQL()
    {
        return "
            CREATE TABLE public_users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                full_name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                phone VARCHAR(20) NOT NULL,
                country_code VARCHAR(5) NOT NULL DEFAULT '+94',
                password_hash VARCHAR(255) NOT NULL,
                nic VARCHAR(20) NOT NULL UNIQUE,
                gender ENUM('male', 'female', 'other', 'prefer-not-to-say') NULL,
                interests JSON NULL,
                is_verified BOOLEAN DEFAULT FALSE,
                is_deleted TINYINT(1) NOT NULL DEFAULT 0,
                email_verified_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_nic (nic)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
    }

    private function getPublishersTableSQL()
    {
        return "
            CREATE TABLE publishers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                society_name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                phone VARCHAR(20) NOT NULL,
                country_code VARCHAR(5) NOT NULL DEFAULT '+94',
                password_hash VARCHAR(255) NOT NULL,
                university VARCHAR(100) NOT NULL,
                faculty VARCHAR(100) NOT NULL,
                confirmation_document VARCHAR(255) NULL,
                verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
                verification_notes TEXT NULL,
                is_active BOOLEAN DEFAULT TRUE,
                is_deleted TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_university (university),
                INDEX idx_faculty (faculty),
                INDEX idx_verification_status (verification_status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
    }

    private function getSponsorsTableSQL()
    {
        return "
            CREATE TABLE sponsors (
                id INT AUTO_INCREMENT PRIMARY KEY,
                company_name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                phone VARCHAR(20) NOT NULL,
                country_code VARCHAR(5) NOT NULL DEFAULT '+94',
                password_hash VARCHAR(255) NOT NULL,
                company_type ENUM('corporation', 'small_business', 'individual', 'nonprofit') DEFAULT 'corporation',
                verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
                verification_notes TEXT NULL,
                is_active BOOLEAN DEFAULT TRUE,
                is_deleted TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_company_type (company_type),
                INDEX idx_verification_status (verification_status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
    }

    private function getUsersTableSQL()
    {
        return "
            CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                user_type ENUM('university', 'public', 'publisher', 'sponsor') NOT NULL,
                user_id INT NOT NULL,
                last_login TIMESTAMP NULL,
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_user_type (user_type),
                INDEX idx_user_id (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
    }

    private function getEventsTableSQL()
    {
        return "
            CREATE TABLE events (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                category ENUM('academic', 'sports', 'cultural', 'technology', 'social', 'workshop') NOT NULL,
                university VARCHAR(100) NOT NULL,
                university_name VARCHAR(255) NOT NULL,
                visibility ENUM('public', 'university', 'faculty') DEFAULT 'public',
                status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
                event_date DATE NOT NULL,
                event_time TIME NOT NULL,
                event_end_time TIME NULL,
                location VARCHAR(255) NOT NULL,
                location_type ENUM('physical', 'virtual', 'hybrid') DEFAULT 'physical',
                venue_name VARCHAR(255) NULL,
                street_address VARCHAR(255) NULL,
                city VARCHAR(100) NULL,
                district_province VARCHAR(100) NULL,
                faculty_department VARCHAR(255) NULL,
                organizer VARCHAR(255) NOT NULL,
                organizer_email VARCHAR(255) NULL,
                created_by INT NOT NULL,
                created_by_type ENUM('publisher', 'admin', 'moderator') NOT NULL,
                participants INT DEFAULT 0,
                max_participants INT NOT NULL,
                target_audience ENUM('students', 'staff', 'public', 'all') DEFAULT 'all',
                requirements JSON NULL,
                schedule JSON NULL,
                ticket_type ENUM('free-all', 'free-registration', 'paid') DEFAULT 'free-all',
                registration_limit INT NULL,
                registration_start_date DATE NULL,
                registration_start_time TIME NULL,
                registration_end_date DATE NULL,
                registration_end_time TIME NULL,
                ticket_types JSON NULL,
                custom_fields JSON NULL,
                needs_volunteers BOOLEAN DEFAULT FALSE,
                volunteer_sources JSON NULL,
                volunteers_needed INT NULL,
                volunteer_positions JSON NULL,
                accepts_donations BOOLEAN DEFAULT FALSE,
                image_url VARCHAR(500) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_category (category),
                INDEX idx_university (university),
                INDEX idx_status (status),
                INDEX idx_event_date (event_date),
                INDEX idx_created_by (created_by, created_by_type),
                INDEX idx_visibility (visibility)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
    }

    private function getEventCommentsTableSQL()
    {
        return "
            CREATE TABLE event_comments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_id INT NOT NULL,
                user_id INT NOT NULL,
                user_type ENUM('university', 'public', 'publisher', 'sponsor') NOT NULL,
                user_table ENUM('university_users', 'public_users', 'publishers', 'sponsors') NOT NULL,
                comment_text TEXT NOT NULL,
                rating INT NULL DEFAULT NULL CHECK (rating >= 1 AND rating <= 5),
                is_edited BOOLEAN DEFAULT FALSE,
                is_deleted BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL,
                INDEX idx_event_id (event_id),
                INDEX idx_user_id (user_id),
                INDEX idx_user_type (user_type),
                INDEX idx_created_at (created_at),
                INDEX idx_active_comments (event_id, is_deleted),
                FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
    }

    private function getNotificationsTableSQL()
    {
        return "
            CREATE TABLE notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                recipient_id INT NOT NULL,
                recipient_type ENUM('publisher', 'admin', 'moderator') NOT NULL,
                type ENUM('new_comment', 'comment_edited', 'comment_deleted', 'event_comment') NOT NULL,
                title VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                related_id INT NULL,
                related_type ENUM('event', 'comment', 'user') NULL,
                is_read BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_recipient (recipient_id, recipient_type),
                INDEX idx_unread (recipient_id, recipient_type, is_read),
                INDEX idx_type (type),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
    }

    public function isDatabaseInitialized()
    {
        try {
            $conn = $this->connect();
            $existingTables = $this->getExistingTables($conn);

            // Check if all required tables exist
            foreach ($this->requiredTables as $table) {
                if (!in_array($table, $existingTables)) {
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
