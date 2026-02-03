<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

requireLogin();

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$question_id = $input['question_id'] ?? null;
$answer = $input['answer'] ?? '';
$session_type = $input['session_type'] ?? 'hr';

if (!$question_id || empty($answer)) {
    echo json_encode(['success' => false, 'message' => 'Question ID and answer required']);
    exit();
}

// Get the question
$stmt = $conn->prepare("SELECT question, session_id FROM interview_qa WHERE id = ?");
$stmt->bind_param("i", $question_id);
$stmt->execute();
$result = $stmt->get_result();
$qa = $result->fetch_assoc();

if (!$qa) {
    echo json_encode(['success' => false, 'message' => 'Question not found']);
    exit();
}

// Create evaluation prompt based on session type
$evaluation_prompts = [
    'hr' => "You are an expert HR interviewer. Evaluate this candidate's answer to an HR interview question. Provide constructive feedback on their response, focusing on structure (STAR method if applicable), clarity, relevance, and professionalism. Rate the answer from 0-10 and explain your rating briefly.",
    'technical' => "You are an expert technical interviewer. Evaluate this candidate's answer to a technical question. Assess their understanding of concepts, accuracy, depth of knowledge, and clarity of explanation. Rate the answer from 0-10 and provide specific feedback.",
    'leetcode' => "You are an expert coding interviewer. Evaluate this candidate's approach to solving the coding problem. Consider their problem-solving approach, algorithm choice, time/space complexity awareness, and explanation clarity. Rate from 0-10 with feedback."
];

$prompt = $evaluation_prompts[$session_type] . "\n\nQuestion: " . $qa['question'] . "\n\nCandidate's Answer: " . $answer . "\n\nProvide: 1) A score from 0-10, 2) Brief constructive feedback (2-3 sentences). Format: Score: X/10\nFeedback: [your feedback]";

// Call AI for evaluation
$data = [
    'model' => AI_MODEL,
    'messages' => [
        ['role' => 'system', 'content' => 'You are an expert interview evaluator. Be constructive and encouraging while providing honest feedback.'],
        ['role' => 'user', 'content' => $prompt]
    ],
    'max_tokens' => 300,
    'temperature' => 0.5
];

$ch = curl_init(API_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . HF_TOKEN,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    $result = json_decode($response, true);
    $feedback = $result['choices'][0]['message']['content'] ?? 'Unable to generate feedback';
    
    // Extract score from feedback
    preg_match('/(\d+)\/10/', $feedback, $matches);
    $points = isset($matches[1]) ? (int)$matches[1] : 5;
    
    // Determine if answer is correct (score >= 7)
    $is_correct = $points >= 7;
    
    // Update question with answer and feedback
    $stmt = $conn->prepare("UPDATE interview_qa SET user_answer = ?, ai_feedback = ?, is_correct = ?, points = ? WHERE id = ?");
    $stmt->bind_param("ssiii", $answer, $feedback, $is_correct, $points, $question_id);
    $stmt->execute();
    
    // Update session statistics
    if ($is_correct) {
        $stmt = $conn->prepare("UPDATE interview_sessions SET correct_answers = correct_answers + 1 WHERE id = ?");
        $stmt->bind_param("i", $qa['session_id']);
        $stmt->execute();
    }
    
    echo json_encode([
        'success' => true,
        'feedback' => $feedback,
        'points' => $points,
        'is_correct' => $is_correct
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to evaluate answer',
        'error' => $response
    ]);
}
?>
