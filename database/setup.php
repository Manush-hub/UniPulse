<?php
require_once __DIR__ . '/../app/Core/config.php';

try {
    // Connect to MySQL server (without selecting database first)
    $dsn = "mysql:host=".DBHOST.";port=".DBPORT.";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DBUSER, DBPASS, $options);
    
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
            university VARCHAR(100) NOT NULL,
            university_name VARCHAR(255) NOT NULL,
            assigned_by INT NOT NULL,
            permissions JSON NULL DEFAULT ('[]'),
            is_active BOOLEAN DEFAULT TRUE,
            last_login TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_university (university),
            INDEX idx_assigned_by (assigned_by),
            FOREIGN KEY (assigned_by) REFERENCES admins(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($moderatorsTable);
    echo "Table 'moderators' created successfully.\n";
    
    // Create events table
    $eventsTable = "
        CREATE TABLE IF NOT EXISTS events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            category ENUM('academic', 'sports', 'cultural', 'technology', 'social', 'workshop', 'business', 'music', 'other') NOT NULL,
            university VARCHAR(100) NOT NULL,
            university_name VARCHAR(255) NOT NULL,
            status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
            event_date DATE NOT NULL,
            event_time TIME NOT NULL,
            location VARCHAR(255) NOT NULL,
            organizer VARCHAR(255) NOT NULL,
            organizer_email VARCHAR(255) NULL,
            participants INT DEFAULT 0,
            max_participants INT NOT NULL,
            requirements JSON NULL,
            schedule JSON NULL,
            image_url VARCHAR(500) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_category (category),
            INDEX idx_university (university),
            INDEX idx_status (status),
            INDEX idx_event_date (event_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($eventsTable);
    echo "Table 'events' created successfully.\n";
    
    // Insert sample events data
    $sampleEvents = [
        [
            'title' => 'Tech Conference 2025',
            'description' => 'Annual technology conference featuring latest innovations in AI, blockchain, and cloud computing. Join industry experts and students for networking and learning opportunities. This conference will cover cutting-edge technologies and their practical applications in various industries.',
            'category' => 'technology',
            'university' => 'university-of-moratuwa',
            'university_name' => 'University of Moratuwa',
            'status' => 'upcoming',
            'event_date' => '2025-09-15',
            'event_time' => '09:00:00',
            'location' => 'Main Auditorium, UOM',
            'organizer' => 'Computer Society',
            'organizer_email' => 'computersociety@uom.lk',
            'participants' => 250,
            'max_participants' => 300,
            'requirements' => json_encode([
                'Laptop with internet connection',
                'Basic programming knowledge recommended',
                'Student ID for registration',
                'Notebook for taking notes'
            ]),
            'schedule' => json_encode([
                ['time' => '09:00 AM', 'activity' => 'Registration & Welcome Coffee'],
                ['time' => '10:00 AM', 'activity' => 'Keynote: Future of AI'],
                ['time' => '11:30 AM', 'activity' => 'Workshop: Blockchain Basics'],
                ['time' => '01:00 PM', 'activity' => 'Lunch Break'],
                ['time' => '02:00 PM', 'activity' => 'Panel: Cloud Computing Trends'],
                ['time' => '04:00 PM', 'activity' => 'Networking Session'],
                ['time' => '05:00 PM', 'activity' => 'Closing Remarks']
            ])
        ],
        [
            'title' => 'Inter-University Cricket Championship',
            'description' => 'Annual cricket tournament between top universities in Sri Lanka. Support your university team in this exciting championship that brings together the best cricket talent from across the country.',
            'category' => 'sports',
            'university' => 'university-of-peradeniya',
            'university_name' => 'University of Peradeniya',
            'status' => 'ongoing',
            'event_date' => '2025-08-25',
            'event_time' => '08:00:00',
            'location' => 'University Cricket Ground',
            'organizer' => 'Sports Club',
            'organizer_email' => 'sports@pdn.ac.lk',
            'participants' => 150,
            'max_participants' => 500,
            'requirements' => json_encode([
                'University sports pass',
                'Comfortable sports attire recommended',
                'Sun protection (hat, sunscreen)',
                'Water bottle'
            ]),
            'schedule' => json_encode([
                ['time' => '08:00 AM', 'activity' => 'Team Registration'],
                ['time' => '09:00 AM', 'activity' => 'Opening Ceremony'],
                ['time' => '10:00 AM', 'activity' => 'First Match: UOM vs UOC'],
                ['time' => '02:00 PM', 'activity' => 'Second Match: UOP vs UOK'],
                ['time' => '06:00 PM', 'activity' => 'Day 1 Wrap-up']
            ])
        ],
        [
            'title' => 'Cultural Night 2025',
            'description' => 'Celebrate diverse cultures with traditional dances, music performances, and cultural exhibitions from different communities. Experience the rich cultural heritage of Sri Lanka and other nations.',
            'category' => 'cultural',
            'university' => 'university-of-colombo',
            'university_name' => 'University of Colombo',
            'status' => 'upcoming',
            'event_date' => '2025-09-20',
            'event_time' => '18:00:00',
            'location' => 'Arts Theatre',
            'organizer' => 'Cultural Society',
            'organizer_email' => 'cultural@cmb.ac.lk',
            'participants' => 180,
            'max_participants' => 400,
            'requirements' => json_encode([
                'Formal or traditional attire preferred',
                'Camera for memorable moments',
                'Appetite for cultural foods',
                'Open mind for cultural exchange'
            ]),
            'schedule' => json_encode([
                ['time' => '06:00 PM', 'activity' => 'Welcome & Cultural Exhibition'],
                ['time' => '06:30 PM', 'activity' => 'Traditional Dance Performances'],
                ['time' => '07:30 PM', 'activity' => 'Music & Song Presentations'],
                ['time' => '08:30 PM', 'activity' => 'Cultural Food Festival'],
                ['time' => '09:30 PM', 'activity' => 'Grand Finale & Awards'],
                ['time' => '10:00 PM', 'activity' => 'Closing Ceremony']
            ])
        ],
        [
            'title' => 'Academic Research Symposium',
            'description' => 'Present your research findings and learn from peer researchers across various academic disciplines.',
            'category' => 'academic',
            'university' => 'university-of-kelaniya',
            'university_name' => 'University of Kelaniya',
            'status' => 'upcoming',
            'event_date' => '2025-09-10',
            'event_time' => '09:30:00',
            'location' => 'Research Center',
            'organizer' => 'Graduate School',
            'organizer_email' => 'graduate@kln.ac.lk',
            'participants' => 120,
            'max_participants' => 200,
            'requirements' => json_encode([
                'Research abstract submission',
                'Academic credentials',
                'Formal attire required',
                'Presentation materials if presenting'
            ]),
            'schedule' => json_encode([
                ['time' => '09:30 AM', 'activity' => 'Registration & Coffee'],
                ['time' => '10:00 AM', 'activity' => 'Opening Keynote'],
                ['time' => '11:00 AM', 'activity' => 'Research Presentations Session 1'],
                ['time' => '12:30 PM', 'activity' => 'Lunch Break'],
                ['time' => '01:30 PM', 'activity' => 'Research Presentations Session 2'],
                ['time' => '03:00 PM', 'activity' => 'Panel Discussion'],
                ['time' => '04:00 PM', 'activity' => 'Awards & Closing']
            ])
        ],
        [
            'title' => 'Community Service Day',
            'description' => 'Join hands to make a difference in local communities through various volunteer activities and social initiatives.',
            'category' => 'social',
            'university' => 'university-of-sri-jayewardenepura',
            'university_name' => 'University of Sri Jayewardenepura',
            'status' => 'completed',
            'event_date' => '2025-08-15',
            'event_time' => '07:00:00',
            'location' => 'Campus & Local Communities',
            'organizer' => 'Volunteer Club',
            'organizer_email' => 'volunteer@sjp.ac.lk',
            'participants' => 200,
            'max_participants' => 200,
            'requirements' => json_encode([
                'Comfortable work clothes',
                'Willingness to help communities',
                'Transportation arrangements',
                'Basic first aid knowledge helpful'
            ]),
            'schedule' => json_encode([
                ['time' => '07:00 AM', 'activity' => 'Assembly & Team Formation'],
                ['time' => '08:00 AM', 'activity' => 'Travel to Community Sites'],
                ['time' => '09:00 AM', 'activity' => 'Community Service Activities'],
                ['time' => '12:00 PM', 'activity' => 'Lunch with Community'],
                ['time' => '01:00 PM', 'activity' => 'Afternoon Service Activities'],
                ['time' => '04:00 PM', 'activity' => 'Wrap-up & Return']
            ])
        ],
        [
            'title' => 'AI & Machine Learning Workshop',
            'description' => 'Hands-on workshop covering fundamentals of artificial intelligence and machine learning with practical coding sessions.',
            'category' => 'workshop',
            'university' => 'university-of-moratuwa',
            'university_name' => 'University of Moratuwa',
            'status' => 'upcoming',
            'event_date' => '2025-09-05',
            'event_time' => '14:00:00',
            'location' => 'Computer Lab 1',
            'organizer' => 'IEEE Student Branch',
            'organizer_email' => 'ieee@uom.lk',
            'participants' => 45,
            'max_participants' => 50,
            'requirements' => json_encode([
                'Laptop with Python installed',
                'Basic programming knowledge',
                'Jupyter notebook setup',
                'Enthusiasm to learn AI/ML'
            ]),
            'schedule' => json_encode([
                ['time' => '02:00 PM', 'activity' => 'Introduction to AI/ML'],
                ['time' => '02:30 PM', 'activity' => 'Setting up Environment'],
                ['time' => '03:00 PM', 'activity' => 'Hands-on Python for ML'],
                ['time' => '04:00 PM', 'activity' => 'Break'],
                ['time' => '04:15 PM', 'activity' => 'Building First ML Model'],
                ['time' => '05:30 PM', 'activity' => 'Project Showcase & Q&A']
            ])
        ]
    ];

    // Insert sample events
    $insertEventQuery = "
        INSERT INTO events (
            title, description, category, university, university_name, status, 
            event_date, event_time, location, organizer, organizer_email, 
            participants, max_participants, requirements, schedule
        ) VALUES (
            :title, :description, :category, :university, :university_name, :status,
            :event_date, :event_time, :location, :organizer, :organizer_email,
            :participants, :max_participants, :requirements, :schedule
        )
    ";

    $stmt = $pdo->prepare($insertEventQuery);
    
    foreach ($sampleEvents as $event) {
        $stmt->execute($event);
    }
    
    echo "Sample events data inserted successfully.\n";
    
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
