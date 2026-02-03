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

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");
?>
