-- Database Performance Optimizations for JobHandaAI
-- Run this file after setting up the initial database to improve performance for multiple concurrent users

USE jobhandaai;

-- Add indexes for faster queries
-- These indexes will significantly improve query performance for pagination and filtering

-- Index on user_id for faster session lookups
CREATE INDEX idx_sessions_user_id ON interview_sessions(user_id);

-- Index on created_at for faster date-based sorting
CREATE INDEX idx_sessions_created_at ON interview_sessions(created_at DESC);

-- Index on status for filtering active/completed sessions
CREATE INDEX idx_sessions_status ON interview_sessions(status);

-- Composite index for user sessions with date
CREATE INDEX idx_sessions_user_date ON interview_sessions(user_id, created_at DESC);

-- Index on session_id for QA lookups
CREATE INDEX idx_qa_session_id ON interview_qa(session_id);

-- Index on user email and username for login queries
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);

-- Index on role for admin queries
CREATE INDEX idx_users_role ON users(role);

-- Optimize tables for better performance
OPTIMIZE TABLE users;
OPTIMIZE TABLE interview_sessions;
OPTIMIZE TABLE interview_qa;
OPTIMIZE TABLE user_progress;

-- Set appropriate InnoDB buffer pool size (adjust based on available RAM)
-- Uncomment and modify in my.cnf or my.ini:
-- innodb_buffer_pool_size = 256M
-- innodb_log_file_size = 64M
-- max_connections = 200
-- query_cache_size = 32M
-- query_cache_type = 1

-- For production, also consider:
-- 1. Enable query caching
-- 2. Use connection pooling in PHP (e.g., PDO with persistent connections)
-- 3. Implement Redis or Memcached for session storage
-- 4. Use a CDN for static assets
-- 5. Enable gzip compression on the web server
