<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

requireLogin();

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$required_fields = ['full_name', 'email', 'title', 'summary', 'experience', 'education', 'skills'];
foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
        exit();
    }
}

$user_id = $_SESSION['user_id'];
$full_name = $input['full_name'];
$email = $input['email'];
$phone = $input['phone'] ?? '';
$title = $input['title'];
$summary = $input['summary'];
$experience = $input['experience'];
$education = $input['education'];
$skills = $input['skills'];

// Check if user already has a resume
$stmt = $conn->prepare("SELECT id FROM user_resumes WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();

if ($existing) {
    // Update existing resume
    $stmt = $conn->prepare("
        UPDATE user_resumes 
        SET full_name = ?, email = ?, phone = ?, title = ?, summary = ?, experience = ?, education = ?, skills = ?
        WHERE user_id = ?
    ");
    $stmt->bind_param("ssssssssi", $full_name, $email, $phone, $title, $summary, $experience, $education, $skills, $user_id);
} else {
    // Create new resume
    $stmt = $conn->prepare("
        INSERT INTO user_resumes (user_id, full_name, email, phone, title, summary, experience, education, skills)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issssssss", $user_id, $full_name, $email, $phone, $title, $summary, $experience, $education, $skills);
}

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Resume saved successfully',
        'resume_id' => $existing ? $existing['id'] : $stmt->insert_id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to save resume'
    ]);
}
?>
