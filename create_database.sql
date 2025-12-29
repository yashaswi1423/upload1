-- Smart Horizon Hackathon Database Setup
-- Run this in phpMyAdmin or MySQL command line

-- Create database
CREATE DATABASE IF NOT EXISTS smart_horizon_hackathon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE smart_horizon_hackathon;

-- Drop existing tables if they exist (in correct order due to foreign keys)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS supporting_documents;
DROP TABLE IF EXISTS submissions;
SET FOREIGN_KEY_CHECKS = 1;

-- Create submissions table
CREATE TABLE submissions (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create supporting documents table
CREATE TABLE supporting_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    file_type VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (submission_id) REFERENCES submissions(id) ON DELETE CASCADE,
    INDEX idx_submission_id (submission_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert a test record to verify everything works
INSERT INTO submissions (
    org_name, domain, spoc_name, country_code, spoc_contact, 
    contact_email, ps_title, ps_description
) VALUES (
    'Test Organization', 
    'AI/ML/DL', 
    'Test User', 
    '+91', 
    '9876543210', 
    'test@gmail.com', 
    'Test Problem Statement', 
    'This is a test problem statement to verify the database setup is working correctly.'
);

-- Show success message
SELECT 'Database setup completed successfully!' as message;
SELECT COUNT(*) as total_submissions FROM submissions;