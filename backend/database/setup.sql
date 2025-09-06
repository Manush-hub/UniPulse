-- UniPulse Database Schema
-- Users table for authentication system

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS unipulse_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE unipulse_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    user_type ENUM('student', 'organizer', 'admin') DEFAULT 'student',
    email_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(100) NULL,
    reset_token VARCHAR(100) NULL,
    reset_token_expires TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_user_type (user_type),
    INDEX idx_verification_token (verification_token),
    INDEX idx_reset_token (reset_token)
) ENGINE=InnoDB;

-- Create sessions table for session management
CREATE TABLE IF NOT EXISTS user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent VARCHAR(500),
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB;

-- Insert a test admin user (password: admin123)
INSERT INTO users (username, email, password_hash, first_name, last_name, user_type, email_verified, is_active) VALUES 
('admin', 'admin@unipulse.lk', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewfyVKp7VQ9x2I.m', 'Admin', 'User', 'admin', TRUE, TRUE),
('demo', 'demo@unipulse.lk', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewfyVKp7VQ9x2I.m', 'Demo', 'User', 'student', TRUE, TRUE)
ON DUPLICATE KEY UPDATE email = VALUES(email);