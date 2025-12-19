<?php
/**
 * Script to populate publisher_profiles table with sample data
 */

require_once __DIR__ . '/../app/Core/config.php';

try {
    // Connect to database
    $dsn = "mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME.";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DBUSER, DBPASS, $options);
    
    echo "Fetching existing publishers...\n";
    
    // Get all approved publishers
    $stmt = $pdo->query("SELECT id, society_name, email, university, faculty FROM publishers WHERE approval_status = 'approved'");
    $publishers = $stmt->fetchAll();
    
    if (empty($publishers)) {
        echo "No approved publishers found. Please create and approve publishers first.\n";
        exit;
    }
    
    echo "Found " . count($publishers) . " approved publisher(s).\n\n";
    
    // Sample data templates
    $orgTypes = ['student-org', 'academic-club', 'sports-club', 'cultural-club', 'professional-org'];
    
    $sampleData = [
        [
            'org_type' => 'student-org',
            'address' => '123 University Avenue, Campus Building A',
            'established_year' => 2018,
            'member_count' => 245,
            'headline' => 'Leading Innovation in Technology',
            'bio' => 'We are a student organization dedicated to fostering innovation and technological advancement. Our mission is to create a vibrant community of tech enthusiasts through hands-on learning experiences, workshops, and industry partnerships.',
            'mission' => 'To empower students with cutting-edge technology skills and create opportunities for innovation, collaboration, and professional growth in the tech industry.',
            'website' => 'https://techsociety.university.edu',
            'facebook' => 'https://facebook.com/techsociety',
            'instagram' => 'https://instagram.com/techsociety',
            'linkedin' => 'https://linkedin.com/company/tech-society',
            'twitter' => 'https://twitter.com/techsociety',
            'discord' => 'https://discord.gg/techsociety',
            'youtube' => 'https://youtube.com/@techsociety'
        ],
        [
            'org_type' => 'academic-club',
            'address' => '456 Campus Drive, Student Center Room 201',
            'established_year' => 2015,
            'member_count' => 180,
            'headline' => 'Advancing Academic Excellence',
            'bio' => 'An academic club focused on research, learning, and knowledge sharing. We organize seminars, workshops, and study groups to enhance academic performance and foster intellectual growth.',
            'mission' => 'To promote academic excellence and create a supportive learning environment where students can thrive intellectually and professionally.',
            'website' => 'https://academicclub.university.edu',
            'facebook' => 'https://facebook.com/academicclub',
            'instagram' => 'https://instagram.com/academicclub',
            'linkedin' => 'https://linkedin.com/company/academic-club',
            'twitter' => 'https://twitter.com/academicclub'
        ],
        [
            'org_type' => 'cultural-club',
            'address' => '789 Arts Plaza, Cultural Center',
            'established_year' => 2020,
            'member_count' => 320,
            'headline' => 'Celebrating Diversity Through Culture',
            'bio' => 'A vibrant cultural organization that celebrates diversity and promotes cultural understanding through events, performances, and workshops. We bring together students from diverse backgrounds.',
            'mission' => 'To celebrate and preserve cultural heritage while promoting diversity, inclusion, and cross-cultural understanding within our university community.',
            'website' => 'https://culturalclub.university.edu',
            'facebook' => 'https://facebook.com/culturalclub',
            'instagram' => 'https://instagram.com/culturalclub',
            'linkedin' => 'https://linkedin.com/company/cultural-club'
        ]
    ];
    
    $insertQuery = "INSERT INTO publisher_profiles 
        (publisher_id, org_type, address, established_year, member_count, headline, bio, mission, 
         website, facebook, instagram, linkedin, twitter, discord, youtube) 
        VALUES 
        (:publisher_id, :org_type, :address, :established_year, :member_count, :headline, :bio, :mission,
         :website, :facebook, :instagram, :linkedin, :twitter, :discord, :youtube)
        ON DUPLICATE KEY UPDATE
        org_type = VALUES(org_type),
        address = VALUES(address),
        established_year = VALUES(established_year),
        member_count = VALUES(member_count),
        headline = VALUES(headline),
        bio = VALUES(bio),
        mission = VALUES(mission),
        website = VALUES(website),
        facebook = VALUES(facebook),
        instagram = VALUES(instagram),
        linkedin = VALUES(linkedin),
        twitter = VALUES(twitter),
        discord = VALUES(discord),
        youtube = VALUES(youtube)";
    
    $stmt = $pdo->prepare($insertQuery);
    
    $inserted = 0;
    foreach ($publishers as $index => $publisher) {
        // Use sample data, cycle through if more publishers than samples
        $data = $sampleData[$index % count($sampleData)];
        
        // Customize with publisher info
        $data['publisher_id'] = $publisher['id'];
        $data['headline'] = $publisher['society_name'] . ' - ' . $data['headline'];
        $data['address'] = $data['address'] . ', ' . $publisher['university'];
        
        // Set nulls for optional fields
        $data['discord'] = $data['discord'] ?? null;
        $data['youtube'] = $data['youtube'] ?? null;
        $data['twitter'] = $data['twitter'] ?? null;
        
        $stmt->execute($data);
        
        echo "✓ Populated profile for: {$publisher['society_name']} (ID: {$publisher['id']})\n";
        $inserted++;
    }
    
    echo "\n✅ Successfully populated {$inserted} publisher profile(s)!\n";
    echo "\nYou can now view the profiles at:\n";
    echo "http://localhost/unipulse/public/publisher/profile\n";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
