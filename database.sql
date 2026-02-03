-- Create database
CREATE DATABASE IF NOT EXISTS jobhandaai;
USE jobhandaai;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Interview sessions table
CREATE TABLE IF NOT EXISTS interview_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_type ENUM('hr', 'technical', 'leetcode') NOT NULL,
    difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
    total_questions INT DEFAULT 0,
    correct_answers INT DEFAULT 0,
    score DECIMAL(5,2) DEFAULT 0.00,
    time_spent INT DEFAULT 0, -- in seconds
    status ENUM('active', 'completed', 'abandoned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Interview questions and answers table
CREATE TABLE IF NOT EXISTS interview_qa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    question TEXT NOT NULL,
    user_answer TEXT,
    ai_feedback TEXT,
    is_correct BOOLEAN DEFAULT FALSE,
    points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES interview_sessions(id) ON DELETE CASCADE
);

-- User progress tracking
CREATE TABLE IF NOT EXISTS user_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_sessions INT DEFAULT 0,
    total_questions INT DEFAULT 0,
    correct_answers INT DEFAULT 0,
    average_score DECIMAL(5,2) DEFAULT 0.00,
    hr_sessions INT DEFAULT 0,
    technical_sessions INT DEFAULT 0,
    leetcode_sessions INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY (user_id)
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, role) VALUES 
('admin', 'admin@jobhandaai.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');
