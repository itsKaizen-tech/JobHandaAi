<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'jobhandaai');

// Hugging Face API Configuration
define('HF_TOKEN', 'hide'); // Replace with your actual token
define('API_URL', 'https://router.huggingface.co/v1/chat/completions');
define('AI_MODEL', 'meta-llama/Llama-3.1-8B-Instruct:novita');

// Performance settings for multiple concurrent users
ini_set('max_execution_time', 120);  // Increase for AI requests
ini_set('memory_limit', '256M');     // Increase for concurrent processing

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Session configuration for better performance and security
ini_set('session.gc_maxlifetime', 3600);        // 1 hour session lifetime
ini_set('session.cookie_httponly', 1);          // Prevent XSS
ini_set('session.use_strict_mode', 1);          // Security enhancement
ini_set('session.cookie_samesite', 'Strict');   // CSRF protection

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
