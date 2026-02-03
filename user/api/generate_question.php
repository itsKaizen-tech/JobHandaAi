<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

requireLogin();

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$session_id = $input['session_id'] ?? null;
$session_type = $input['session_type'] ?? 'hr';
$difficulty = $input['difficulty'] ?? 'medium';

if (!$session_id) {
    echo json_encode(['success' => false, 'message' => 'Session ID required']);
    exit();
}

// Define question prompts based on session type and difficulty
$difficulty_instructions = [
    'easy' => 'The question should be suitable for beginners and entry-level candidates.',
    'medium' => 'The question should be moderately challenging for mid-level candidates.',
    'hard' => 'The question should be complex and challenging for senior-level candidates.'
];

$prompts = [
    'hr' => "Generate a single behavioral interview question that would be commonly asked in HR interviews. " . $difficulty_instructions[$difficulty] . " The question should focus on soft skills, teamwork, conflict resolution, leadership, or workplace scenarios. Provide ONLY the question, nothing else.",
    'technical' => "Generate a single technical interview question suitable for a software developer position. " . $difficulty_instructions[$difficulty] . " The question should test knowledge of programming concepts, algorithms, data structures, system design, or best practices. Provide ONLY the question, nothing else.",
    'leetcode' => "Generate a single coding problem similar to LeetCode questions. " . $difficulty_instructions[$difficulty] . " Include a clear problem statement that tests algorithmic thinking. Provide ONLY the problem description, nothing else."
];

$prompt = $prompts[$session_type];

// Call Hugging Face API
$data = [
    'model' => AI_MODEL,
    'messages' => [
        ['role' => 'system', 'content' => 'You are an expert interview coach. Generate realistic interview questions.'],
        ['role' => 'user', 'content' => $prompt]
    ],
    'max_tokens' => 200,
    'temperature' => 0.7
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
    $question = $result['choices'][0]['message']['content'] ?? 'Unable to generate question';
    
    // Clean up the question
    $question = trim($question);
    
    // Save question to database
    $stmt = $conn->prepare("INSERT INTO interview_qa (session_id, question) VALUES (?, ?)");
    $stmt->bind_param("is", $session_id, $question);
    $stmt->execute();
    $question_id = $stmt->insert_id;
    
    // Update session
    $stmt = $conn->prepare("UPDATE interview_sessions SET total_questions = total_questions + 1 WHERE id = ?");
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    
    echo json_encode([
        'success' => true,
        'question' => $question,
        'question_id' => $question_id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to generate question',
        'error' => $response
    ]);
}
?>
