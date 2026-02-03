<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireLogin();

$user = getUserInfo($conn, $_SESSION['user_id']);
$session_id = $_GET['id'] ?? 0;

// Get session details
$stmt = $conn->prepare("SELECT * FROM interview_sessions WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $session_id, $_SESSION['user_id']);
$stmt->execute();
$session = $stmt->get_result()->fetch_assoc();

if (!$session) {
    header('Location: sessions.php');
    exit();
}

// Get all Q&A for this session
$stmt = $conn->prepare("SELECT * FROM interview_qa WHERE session_id = ? ORDER BY created_at ASC");
$stmt->bind_param("i", $session_id);
$stmt->execute();
$questions = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Details - JobHandaAI</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
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
                <li><a href="interview.php"><span class="icon">💬</span> Start Interview</a></li>
                <li><a href="sessions.php" class="active"><span class="icon">📝</span> My Sessions</a></li>
                <li><a href="profile.php"><span class="icon">👤</span> Profile</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="topbar">
                <div class="topbar-left">
                    <h1>Session Details</h1>
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

            <div class="content-area">
                <a href="sessions.php" style="display: inline-block; margin-bottom: 1rem; color: var(--primary); text-decoration: none;">
                    ← Back to Sessions
                </a>

                <div class="card">
                    <div class="card-header">
                        <h2><?php echo strtoupper($session['session_type']); ?> Interview - Session #<?php echo $session['id']; ?></h2>
                    </div>
                    <div class="card-body">
                        <div class="stats-grid" style="margin-bottom: 2rem;">
                            <div class="stat-card">
                                <div class="stat-label">Total Questions</div>
                                <div class="stat-value"><?php echo $session['total_questions']; ?></div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-label">Correct Answers</div>
                                <div class="stat-value"><?php echo $session['correct_answers']; ?></div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-label">Final Score</div>
                                <div class="stat-value"><?php echo number_format($session['score'], 1); ?>%</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-label">Status</div>
                                <div class="stat-value" style="font-size: 1.2rem;">
                                    <?php echo ucfirst($session['status']); ?>
                                </div>
                            </div>
                        </div>

                        <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Questions & Answers</h3>
                        
                        <?php if ($questions->num_rows > 0): ?>
                            <?php $qnum = 1; while ($qa = $questions->fetch_assoc()): ?>
                                <div style="background: var(--bg); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid <?php echo $qa['is_correct'] ? '#28a745' : '#dc3545'; ?>;">
                                    <h4 style="color: var(--primary); margin-bottom: 1rem;">Question <?php echo $qnum++; ?></h4>
                                    <p style="margin-bottom: 1rem; font-weight: 500;"><?php echo htmlspecialchars($qa['question']); ?></p>
                                    
                                    <?php if ($qa['user_answer']): ?>
                                        <div style="background: var(--white); padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
                                            <strong style="color: var(--text-light);">Your Answer:</strong>
                                            <p style="margin-top: 0.5rem;"><?php echo htmlspecialchars($qa['user_answer']); ?></p>
                                        </div>
                                        
                                        <?php if ($qa['ai_feedback']): ?>
                                            <div style="background: #fff3cd; padding: 1rem; border-radius: 6px;">
                                                <strong style="color: #856404;">AI Feedback:</strong>
                                                <p style="margin-top: 0.5rem; color: #856404;">
                                                    <?php echo nl2br(htmlspecialchars($qa['ai_feedback'])); ?>
                                                </p>
                                                <p style="margin-top: 0.5rem;"><strong>Points: <?php echo $qa['points']; ?>/10</strong></p>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p style="color: var(--text-light); font-style: italic;">Not answered</p>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p style="text-align: center; color: var(--text-light); padding: 2rem;">No questions in this session.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
