<?php
require_once '../app/Core/config.php';

try {
    // Connect to MySQL server (without selecting database first)
    $pdo = new PDO("mysql:host=".DBHOST.";port=".DBPORT, DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS ".DBNAME." CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database '".DBNAME."' created successfully or already exists.\n";
    
    // Select the database
    $pdo->exec("USE ".DBNAME);
    
    // Create university_users table
    $universityUsersTable = "
        CREATE TABLE IF NOT EXISTS university_users (
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
            INDEX idx_student_staff_id (student_staff_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($universityUsersTable);
    echo "Table 'university_users' created successfully.\n";
    
    // Create public_users table
    $publicUsersTable = "
        CREATE TABLE IF NOT EXISTS public_users (
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
    
    $pdo->exec($publicUsersTable);
    echo "Table 'public_users' created successfully.\n";
    
    // Create a general users table for login (optional - combines both user types)
    $usersTable = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            user_type ENUM('university', 'public') NOT NULL,
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
    
    $pdo->exec($usersTable);
    echo "Table 'users' created successfully.\n";
    
    // Create admins table
    $adminsTable = "
        CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NULL,
            is_active BOOLEAN DEFAULT TRUE,
            last_login TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($adminsTable);
    echo "Table 'admins' created successfully.\n";
    
    // Create moderators table
    $moderatorsTable = "
        CREATE TABLE IF NOT EXISTS moderators (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NULL,
            assigned_by INT NOT NULL,
            permissions JSON NULL DEFAULT ('[]'),
            is_active BOOLEAN DEFAULT TRUE,
            last_login TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_assigned_by (assigned_by),
            FOREIGN KEY (assigned_by) REFERENCES admins(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($moderatorsTable);
    echo "Table 'moderators' created successfully.\n";
    
    // Insert default admin user
    $defaultAdminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $insertAdmin = "
        INSERT IGNORE INTO admins (full_name, email, password_hash, phone) 
        VALUES ('System Administrator', 'admin@unipulse.com', :password, '+94712345678')
    ";
    
    $stmt = $pdo->prepare($insertAdmin);
    $stmt->execute(['password' => $defaultAdminPassword]);
    echo "Default admin user created (email: admin@unipulse.com, password: admin123)\n";
    
    echo "\nDatabase setup completed successfully!\n";
    
} catch(PDOException $e) {
    echo "Database setup failed: " . $e->getMessage() . "\n";
}
?>
