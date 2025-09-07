<?php

class DatabaseInitializer {
    
    use Database;
    
    private $requiredTables = [
        'university_users',
        'public_users', 
        'publishers',
        'sponsors',
        'users'
    ];
    
    public function initializeDatabase() {
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
    
    private function createDatabaseIfNotExists() {
        // Connect without selecting database first
        $string = "mysql:host=".DBHOST.";port=".DBPORT;
        $conn = new PDO($string, DBUSER, DBPASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database if it doesn't exist
        $conn->exec("CREATE DATABASE IF NOT EXISTS ".DBNAME." CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Select the database
        $conn->exec("USE ".DBNAME);
    }
    
    private function createTablesIfNotExist() {
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
    
    private function getExistingTables($conn) {
        $stmt = $conn->prepare("SHOW TABLES");
        $stmt->execute();
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $tables;
    }
    
    private function createTable($conn, $tableName) {
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
        }
        
        if ($sql) {
            $conn->exec($sql);
            error_log("Created table: $tableName");
        }
    }
    
    private function getUniversityUsersTableSQL() {
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
    
    private function getPublicUsersTableSQL() {
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
                email_verified_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_nic (nic)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
    }
    
    private function getPublishersTableSQL() {
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
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_university (university),
                INDEX idx_faculty (faculty),
                INDEX idx_verification_status (verification_status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
    }
    
    private function getSponsorsTableSQL() {
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
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_company_type (company_type),
                INDEX idx_verification_status (verification_status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
    }
    
    private function getUsersTableSQL() {
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
    
    public function isDatabaseInitialized() {
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
