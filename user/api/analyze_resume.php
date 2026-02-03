<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

requireLogin();

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

// Validate input
$required_fields = ['summary', 'experience', 'skills'];
foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields first']);
        exit();
    }
}

// Build resume text for analysis
$resume_text = "Professional Summary:\n" . $input['summary'] . "\n\n";
$resume_text .= "Experience:\n" . $input['experience'] . "\n\n";
$resume_text .= "Education:\n" . $input['education'] . "\n\n";
$resume_text .= "Skills:\n" . $input['skills'];

// Create analysis prompt
$prompt = "You are an expert resume reviewer and career coach. Analyze this resume and provide:

1. A score from 0-100 based on:
   - Professional summary clarity and impact
   - Experience descriptions (use of action verbs, quantified achievements)
   - Skills relevance and presentation
   - Overall ATS compatibility
   - Structure and clarity

2. Key strengths (2-3 points)
3. Areas for improvement (2-3 specific points)
4. Actionable recommendations (2-3 specific suggestions)

Resume:
$resume_text

Provide your response in this exact format:
Score: [number]
Strengths: [strengths text]
Improvements: [improvements text]
Recommendations: [recommendations text]";

// Call AI API
$data = [
    'model' => AI_MODEL,
    'messages' => [
        ['role' => 'system', 'content' => 'You are an expert resume reviewer with years of experience in recruiting and career coaching. Provide constructive, actionable feedback.'],
        ['role' => 'user', 'content' => $prompt]
    ],
    'max_tokens' => 600,
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
    $analysis = $result['choices'][0]['message']['content'] ?? '';
    
    // Parse the analysis
    $score = 50; // default
    $strengths = '';
    $improvements = '';
    $recommendations = '';
    
    if (preg_match('/Score:\s*(\d+)/', $analysis, $matches)) {
        $score = min(100, max(0, (int)$matches[1]));
    }
    
    if (preg_match('/Strengths:\s*(.+?)(?=Improvements:|$)/s', $analysis, $matches)) {
        $strengths = trim($matches[1]);
    }
    
    if (preg_match('/Improvements:\s*(.+?)(?=Recommendations:|$)/s', $analysis, $matches)) {
        $improvements = trim($matches[1]);
    }
    
    if (preg_match('/Recommendations:\s*(.+?)$/s', $analysis, $matches)) {
        $recommendations = trim($matches[1]);
    }
    
    // Get resume ID if exists
    $stmt = $conn->prepare("SELECT id FROM user_resumes WHERE user_id = ? ORDER BY updated_at DESC LIMIT 1");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $resume = $stmt->get_result()->fetch_assoc();
    $resume_id = $resume['id'] ?? null;
    
    // Save analysis to database if resume exists
    if ($resume_id) {
        $stmt = $conn->prepare("
            INSERT INTO resume_analysis (user_id, resume_id, score, strengths, improvements, recommendations)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iiisss", $_SESSION['user_id'], $resume_id, $score, $strengths, $improvements, $recommendations);
        $stmt->execute();
    }
    
    echo json_encode([
        'success' => true,
        'score' => $score,
        'strengths' => $strengths,
        'improvements' => $improvements,
        'recommendations' => $recommendations,
        'full_analysis' => $analysis
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to analyze resume. Please try again.',
        'error' => $response
    ]);
}
?>
