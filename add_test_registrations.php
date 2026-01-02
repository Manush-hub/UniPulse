<?php
/**
 * Add test registration data for admin dashboard
 */

require_once 'app/Core/init.php';

echo "<h1>Adding Test Registration Data</h1>";

try {
    $db = new Database();
    
    // Add test university users
    echo "<h2>Adding University Users</h2>";
    $universityUsers = [
        ['John Doe', 'john.doe@uni.lk', '0771234567', 'University of Colombo', 'Engineering', 'UNI001'],
        ['Jane Smith', 'jane.smith@uni.lk', '0771234568', 'University of Moratuwa', 'Computing', 'UNI002'],
        ['Bob Johnson', 'bob.johnson@uni.lk', '0771234569', 'University of Peradeniya', 'Science', 'UNI003']
    ];
    
    foreach ($universityUsers as $user) {
        $query = "INSERT INTO university_users (full_name, email, phone, country_code, password_hash, university, faculty, student_staff_id, is_active) 
                  VALUES (?, ?, ?, '+94', ?, ?, ?, ?, 1)
                  ON DUPLICATE KEY UPDATE email=email";
        
        $conn = $db->connect();
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $user[0],
            $user[1],
            $user[2],
            password_hash('password123', PASSWORD_DEFAULT),
            $user[3],
            $user[4],
            $user[5]
        ]);
        echo "Added: {$user[0]}<br>";
    }
    
    // Add test public users
    echo "<h2>Adding Public Users</h2>";
    $publicUsers = [
        ['Alice Brown', 'alice.brown@gmail.com', '0771234570', '987654321V'],
        ['Charlie Davis', 'charlie.davis@gmail.com', '0771234571', '987654322V'],
        ['Diana Evans', 'diana.evans@gmail.com', '0771234572', '987654323V']
    ];
    
    foreach ($publicUsers as $user) {
        $query = "INSERT INTO public_users (full_name, email, phone, country_code, password_hash, nic, is_verified) 
                  VALUES (?, ?, ?, '+94', ?, ?, 1)
                  ON DUPLICATE KEY UPDATE email=email";
        
        $conn = $db->connect();
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $user[0],
            $user[1],
            $user[2],
            password_hash('password123', PASSWORD_DEFAULT),
            $user[3]
        ]);
        echo "Added: {$user[0]}<br>";
    }
    
    // Add test publishers
    echo "<h2>Adding Publishers</h2>";
    $publishers = [
        ['Computer Society', 'compsoc@uni.lk', '0771234573', 'University of Colombo', 'Engineering', 'pending'],
        ['Drama Club', 'drama@uni.lk', '0771234574', 'University of Moratuwa', 'Arts', 'approved'],
        ['Sports Club', 'sports@uni.lk', '0771234575', 'University of Peradeniya', 'Sports', 'pending']
    ];
    
    foreach ($publishers as $pub) {
        $query = "INSERT INTO publishers (society_name, email, phone, country_code, password_hash, university, faculty, approval_status, is_active) 
                  VALUES (?, ?, ?, '+94', ?, ?, ?, ?, 1)
                  ON DUPLICATE KEY UPDATE email=email";
        
        $conn = $db->connect();
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $pub[0],
            $pub[1],
            $pub[2],
            password_hash('password123', PASSWORD_DEFAULT),
            $pub[3],
            $pub[4],
            $pub[5]
        ]);
        echo "Added: {$pub[0]} (Status: {$pub[5]})<br>";
    }
    
    // Add test sponsors
    echo "<h2>Adding Sponsors</h2>";
    $sponsors = [
        ['TechCorp Ltd', 'contact@techcorp.lk', '0771234576', 'pending'],
        ['Food Palace', 'info@foodpalace.lk', '0771234577', 'verified'],
        ['Mega Store', 'hello@megastore.lk', '0771234578', 'pending']
    ];
    
    foreach ($sponsors as $sponsor) {
        $query = "INSERT INTO sponsors (company_name, email, phone, country_code, password_hash, verification_status, is_active) 
                  VALUES (?, ?, ?, '+94', ?, ?, 1)
                  ON DUPLICATE KEY UPDATE email=email";
        
        $conn = $db->connect();
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $sponsor[0],
            $sponsor[1],
            $sponsor[2],
            password_hash('password123', PASSWORD_DEFAULT),
            $sponsor[3]
        ]);
        echo "Added: {$sponsor[0]} (Status: {$sponsor[3]})<br>";
    }
    
    echo "<hr>";
    echo "<h2 style='color: green;'>✓ Test data added successfully!</h2>";
    echo "<p><a href='/unipulse/public/admin'>Go to Admin Dashboard</a> to see the results.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
