<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireLogin();

$user = getUserInfo($conn, $_SESSION['user_id']);

// Get session parameters from URL or show selection modal
$session_type = isset($_GET['type']) ? $_GET['type'] : null;
$difficulty = isset($_GET['difficulty']) ? $_GET['difficulty'] : null;
$allowed_types = ['hr', 'technical', 'leetcode'];
$allowed_difficulties = ['easy', 'medium', 'hard'];

if (!$session_type || !$difficulty || !in_array($session_type, $allowed_types) || !in_array($difficulty, $allowed_difficulties)) {
    $show_modal = true;
} else {
    $show_modal = false;
    
    // Initialize or get current session
    if (!isset($_SESSION['current_session_id'])) {
        // Create new session
        $stmt = $conn->prepare("INSERT INTO interview_sessions (user_id, session_type, difficulty, status) VALUES (?, ?, ?, 'active')");
        $stmt->bind_param("iss", $_SESSION['user_id'], $session_type, $difficulty);
        $stmt->execute();
        $_SESSION['current_session_id'] = $stmt->insert_id;
        $_SESSION['question_count'] = 0;
    }
    
    $session_id = $_SESSION['current_session_id'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Practice - JobHandaAI</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        }
        
        .modal-content {
            background: var(--white);
            padding: 3rem;
            border-radius: 16px;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .modal-title {
            font-family: 'Crimson Pro', serif;
            font-size: 2rem;
            margin-bottom: 2rem;
            color: var(--primary);
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 2rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text);
            font-size: 1rem;
        }
        
        .form-select {
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Work Sans', sans-serif;
            background: var(--white);
            color: var(--text);
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .form-select:hover {
            border-color: var(--primary);
        }
        
        .form-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
        }
        
        .btn-start {
            width: 100%;
            background: var(--primary);
            color: var(--white);
            padding: 1rem 2rem;
            border: 2px solid var(--primary);
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
        }
        
        .btn-start:hover {
            background: var(--white);
            color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        
        .btn-start:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .select-description {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <span class="logo-icon">🎯</span>
                <span class="logo-text">JobHandaAI</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><span class="icon">📊</span> Dashboard</a></li>
                <li><a href="interview.php" class="active"><span class="icon">💬</span> Start Interview</a></li>
                <li><a href="sessions.php"><span class="icon">📝</span> My Sessions</a></li>
                <li><a href="profile.php"><span class="icon">👤</span> Profile</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="topbar">
                <div class="topbar-left">
                    <h1><?php echo $show_modal ? 'Interview Practice' : strtoupper($session_type) . ' Interview - ' . ucfirst($difficulty); ?></h1>
                </div>
                <div class="topbar-right">
                    <div class="user-info">
                        <div class="user-avatar"><?php echo strtoupper(substr($user['username'], 0, 2)); ?></div>
                        <div class="user-details">
                            <div class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                        </div>
                    </div>
                    <a href="../logout.php" class="btn-logout">Logout</a>
                </div>
            </div>

            <?php if ($show_modal): ?>
                <div class="modal-overlay" id="sessionModal">
                    <div class="modal-content">
                        <h2 class="modal-title">Configure Your Interview Session</h2>
                        <form id="sessionForm">
                            <div class="form-group">
                                <label class="form-label">Interview Type</label>
                                <select class="form-select" id="sessionType" required>
                                    <option value="">Select interview type...</option>
                                    <option value="hr">💼 HR Interview</option>
                                    <option value="technical">⚙️ Technical Interview</option>
                                    <option value="leetcode">🧩 LeetCode Challenge</option>
                                </select>
                                <p class="select-description">Choose the type of interview questions you want to practice</p>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Difficulty Level</label>
                                <select class="form-select" id="difficulty" required>
                                    <option value="">Select difficulty...</option>
                                    <option value="easy">🟢 Easy - Beginner friendly questions</option>
                                    <option value="medium">🟡 Medium - Intermediate level challenges</option>
                                    <option value="hard">🔴 Hard - Advanced and complex questions</option>
                                </select>
                                <p class="select-description">Select the difficulty level that matches your skill</p>
                            </div>
                            
                            <button type="submit" class="btn-start" id="startBtn">Start Interview Session</button>
                        </form>
                    </div>
                </div>
                
                <script>
                    document.getElementById('sessionForm').addEventListener('submit', function(e) {
                        e.preventDefault();
                        const type = document.getElementById('sessionType').value;
                        const difficulty = document.getElementById('difficulty').value;
                        
                        if (type && difficulty) {
                            window.location.href = `interview.php?type=${type}&difficulty=${difficulty}`;
                        }
                    });
                </script>
            <?php else: ?>

            <div class="content-area">
                <div class="interview-container">
                    <div id="interviewArea">
                        <div class="question-box">
                            <div id="loadingIndicator" class="loading">
                                <div class="spinner"></div>
                                <p>Generating your question...</p>
                            </div>
                            
                            <div id="questionContainer" style="display: none;">
                                <p class="question-text" id="questionText"></p>
                                <textarea 
                                    id="answerInput" 
                                    class="answer-input" 
                                    placeholder="Type your answer here..."
                                ></textarea>
                                <div id="feedbackContainer"></div>
                                <div class="session-controls">
                                    <button onclick="submitAnswer()" class="btn-action btn-submit-answer" id="submitBtn">
                                        Submit Answer
                                    </button>
                                    <button onclick="nextQuestion()" class="btn-action btn-next" id="nextBtn" style="display: none;">
                                        Next Question
                                    </button>
                                    <button onclick="endSession()" class="btn-action btn-end">
                                        End Session
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="sessionComplete" style="display: none;">
                        <div class="card">
                            <div class="card-body">
                                <div class="score-display">
                                    <h2>Session Complete! 🎉</h2>
                                    <div class="score-circle" id="finalScore">0%</div>
                                    <p id="scoreDetails"></p>
                                    <div style="margin-top: 2rem;">
                                        <a href="dashboard.php" class="btn-action btn-submit-answer">Back to Dashboard</a>
                                        <a href="interview.php" class="btn-action btn-next">Start New Session</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                const sessionType = '<?php echo $session_type; ?>';
                const difficulty = '<?php echo $difficulty; ?>';
                const sessionId = <?php echo $session_id; ?>;
                let currentQuestionId = null;

                // Load first question on page load
                window.addEventListener('DOMContentLoaded', generateQuestion);

                async function generateQuestion() {
                    document.getElementById('loadingIndicator').style.display = 'block';
                    document.getElementById('questionContainer').style.display = 'none';
                    document.getElementById('feedbackContainer').innerHTML = '';
                    document.getElementById('answerInput').value = '';
                    
                    try {
                        const response = await fetch('api/generate_question.php', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({
                                session_id: sessionId,
                                session_type: sessionType,
                                difficulty: difficulty
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            currentQuestionId = data.question_id;
                            document.getElementById('questionText').textContent = data.question;
                            document.getElementById('loadingIndicator').style.display = 'none';
                            document.getElementById('questionContainer').style.display = 'block';
                            document.getElementById('submitBtn').style.display = 'inline-block';
                            document.getElementById('nextBtn').style.display = 'none';
                        } else {
                            alert('Error generating question: ' + data.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Failed to generate question');
                    }
                }

                async function submitAnswer() {
                    const answer = document.getElementById('answerInput').value.trim();
                    
                    if (!answer) {
                        alert('Please provide an answer');
                        return;
                    }
                    
                    document.getElementById('submitBtn').disabled = true;
                    document.getElementById('submitBtn').textContent = 'Evaluating...';
                    
                    try {
                        const response = await fetch('api/submit_answer.php', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({
                                question_id: currentQuestionId,
                                answer: answer,
                                session_type: sessionType,
                                difficulty: difficulty
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            const feedbackHtml = `
                                <div class="feedback-box">
                                    <h4>AI Feedback</h4>
                                    <p>${data.feedback}</p>
                                    <p style="margin-top: 1rem;"><strong>Score: ${data.points}/10</strong></p>
                                </div>
                            `;
                            document.getElementById('feedbackContainer').innerHTML = feedbackHtml;
                            document.getElementById('submitBtn').style.display = 'none';
                            document.getElementById('nextBtn').style.display = 'inline-block';
                        } else {
                            alert('Error submitting answer: ' + data.message);
                            document.getElementById('submitBtn').disabled = false;
                            document.getElementById('submitBtn').textContent = 'Submit Answer';
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Failed to submit answer');
                        document.getElementById('submitBtn').disabled = false;
                        document.getElementById('submitBtn').textContent = 'Submit Answer';
                    }
                }

                function nextQuestion() {
                    generateQuestion();
                }

                async function endSession() {
                    if (!confirm('Are you sure you want to end this session?')) {
                        return;
                    }
                    
                    try {
                        const response = await fetch('api/end_session.php', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({session_id: sessionId})
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            document.getElementById('interviewArea').style.display = 'none';
                            document.getElementById('sessionComplete').style.display = 'block';
                            document.getElementById('finalScore').textContent = data.score + '%';
                            document.getElementById('scoreDetails').textContent = 
                                `You answered ${data.correct} out of ${data.total} questions correctly!`;
                        } else {
                            alert('Error ending session');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Failed to end session');
                    }
                }
            </script>
        <?php endif; ?>
        </main>
    </div>
</body>
</html>
