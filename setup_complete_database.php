<?php
// Complete Database Setup for Smart Horizon Hackathon
// This script will create the database and tables if they don't exist

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Smart Horizon Hackathon - Database Setup</h2>";

try {
    // First, connect to MySQL without specifying a database
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<p>✅ Connected to MySQL server</p>";
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS smart_horizon_hackathon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✅ Database 'smart_horizon_hackathon' created/verified</p>";
    
    // Switch to the database
    $pdo->exec("USE smart_horizon_hackathon");
    echo "<p>✅ Using database 'smart_horizon_hackathon'</p>";
    
    // Create submissions table
    $submissions_sql = "
    CREATE TABLE IF NOT EXISTS submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        org_name VARCHAR(255) NOT NULL,
        domain VARCHAR(100),
        spoc_name VARCHAR(255) NOT NULL,
        country_code VARCHAR(10) NOT NULL DEFAULT '+91',
        spoc_contact VARCHAR(20) NOT NULL,
        contact_email VARCHAR(255) NOT NULL,
        ps_title VARCHAR(500) NOT NULL,
        ps_description TEXT NOT NULL,
        dataset_link VARCHAR(500),
        logo_path VARCHAR(500),
        logo_original_name VARCHAR(255),
        logo_file_size INT,
        documents_path TEXT,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_status (status),
        INDEX idx_created_at (created_at),
        INDEX idx_email (contact_email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($submissions_sql);
    echo "<p>✅ Table 'submissions' created/verified</p>";
    
    // Create supporting documents table
    $documents_sql = "
    CREATE TABLE IF NOT EXISTS supporting_documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        submission_id INT NOT NULL,
        filename VARCHAR(255) NOT NULL,
        original_name VARCHAR(255) NOT NULL,
        file_size INT NOT NULL,
        file_type VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (submission_id) REFERENCES submissions(id) ON DELETE CASCADE,
        INDEX idx_submission_id (submission_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($documents_sql);
    echo "<p>✅ Table 'supporting_documents' created/verified</p>";
    
    // Check if we have any submissions
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM submissions");
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        // Insert a test record
        $test_sql = "INSERT INTO submissions (
            org_name, domain, spoc_name, country_code, spoc_contact, 
            contact_email, ps_title, ps_description
        ) VALUES (
            'Test Organization', 
            'AI/ML/DL', 
            'Test User', 
            '+91', 
            '9876543210', 
            'test@example.com', 
            'Test Problem Statement', 
            'This is a test problem statement to verify the database setup is working correctly.'
        )";
        
        $pdo->exec($test_sql);
        echo "<p>✅ Test submission added</p>";
    } else {
        echo "<p>✅ Found {$count} existing submissions</p>";
    }
    
    // Test the database connection using our config
    require_once 'config/database_config.php';
    $test_pdo = getDBConnection();
    echo "<p>✅ Database connection test successful using config file</p>";
    
    echo "<h3>🎉 Database setup completed successfully!</h3>";
    echo "<p><a href='admin.php'>Go to Admin Panel</a> | <a href='index1.html'>Go to Main Site</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please make sure:</p>";
    echo "<ul>";
    echo "<li>XAMPP is running</li>";
    echo "<li>MySQL service is started</li>";
    echo "<li>You have proper permissions</li>";
    echo "</ul>";
}
?>