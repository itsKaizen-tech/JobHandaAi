<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

requireLogin();

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$session_id = $input['session_id'] ?? null;

if (!$session_id) {
    echo json_encode(['success' => false, 'message' => 'Session ID required']);
    exit();
}

// Get session details
$stmt = $conn->prepare("SELECT user_id, total_questions, correct_answers FROM interview_sessions WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $session_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$session = $result->fetch_assoc();

if (!$session) {
    echo json_encode(['success' => false, 'message' => 'Session not found']);
    exit();
}

// Calculate score
$total = $session['total_questions'];
$correct = $session['correct_answers'];
$score = $total > 0 ? ($correct / $total) * 100 : 0;

// Update session as completed
$stmt = $conn->prepare("UPDATE interview_sessions SET status = 'completed', score = ?, completed_at = CURRENT_TIMESTAMP WHERE id = ?");
$stmt->bind_param("di", $score, $session_id);
$stmt->execute();

// Update user progress
$stmt = $conn->prepare("
    UPDATE user_progress 
    SET total_sessions = total_sessions + 1,
        total_questions = total_questions + ?,
        correct_answers = correct_answers + ?,
        average_score = (
            SELECT AVG(score) 
            FROM interview_sessions 
            WHERE user_id = ? AND status = 'completed'
        )
    WHERE user_id = ?
");
$stmt->bind_param("iiii", $total, $correct, $_SESSION['user_id'], $_SESSION['user_id']);
$stmt->execute();

// Clear session
unset($_SESSION['current_session_id']);
unset($_SESSION['question_count']);

echo json_encode([
    'success' => true,
    'score' => round($score, 1),
    'total' => $total,
    'correct' => $correct
]);
?>
