<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireLogin();

$user = getUserInfo($conn, $_SESSION['user_id']);

// Get user statistics
$stmt = $conn->prepare("SELECT * FROM user_progress WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$progress = $stmt->get_result()->fetch_assoc();

// Get recent sessions
$stmt = $conn->prepare("
    SELECT * FROM interview_sessions 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 5
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$recent_sessions = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - JobHandaAI</title>
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
                <li><a href="dashboard.php" class="active"><span class="icon">📊</span> Dashboard</a></li>
                <li><a href="interview.php"><span class="icon">💬</span> Start Interview</a></li>
                <li><a href="sessions.php"><span class="icon">📝</span> My Sessions</a></li>
                <li><a href="profile.php"><span class="icon">👤</span> Profile</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="topbar">
                <div class="topbar-left">
                    <h1>Dashboard</h1>
                </div>
                <div class="topbar-right">
                    <div class="user-info">
                        <div class="user-avatar"><?php echo strtoupper(substr($user['username'], 0, 2)); ?></div>
                        <div class="user-details">
                            <div class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                            <div class="user-role">User</div>
                        </div>
                    </div>
                    <a href="../logout.php" class="btn-logout">Logout</a>
                </div>
            </div>

            <div class="content-area">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-icon">📚</div>
                        </div>
                        <div class="stat-label">Total Sessions</div>
                        <div class="stat-value"><?php echo $progress['total_sessions'] ?? 0; ?></div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-icon">✅</div>
                        </div>
                        <div class="stat-label">Correct Answers</div>
                        <div class="stat-value"><?php echo $progress['correct_answers'] ?? 0; ?></div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-icon">⭐</div>
                        </div>
                        <div class="stat-label">Average Score</div>
                        <div class="stat-value"><?php echo number_format($progress['average_score'] ?? 0, 1); ?>%</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-icon">🎯</div>
                        </div>
                        <div class="stat-label">Total Questions</div>
                        <div class="stat-value"><?php echo $progress['total_questions'] ?? 0; ?></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>Quick Start</h2>
                        <p style="color: var(--text-light); font-size: 0.9rem; margin-top: 0.5rem;">Select your interview type and difficulty to begin</p>
                    </div>
                    <div class="card-body">
                        <div class="session-type-selector">
                            <div class="type-card-group">
                                <h4 style="margin-bottom: 1rem; color: var(--primary); font-weight: 600;">💼 HR Interview</h4>
                                <div class="difficulty-buttons">
                                    <a href="interview.php?type=hr&difficulty=easy" class="difficulty-btn easy">
                                        <span class="difficulty-icon">🟢</span>
                                        <span>Easy</span>
                                    </a>
                                    <a href="interview.php?type=hr&difficulty=medium" class="difficulty-btn medium">
                                        <span class="difficulty-icon">🟡</span>
                                        <span>Medium</span>
                                    </a>
                                    <a href="interview.php?type=hr&difficulty=hard" class="difficulty-btn hard">
                                        <span class="difficulty-icon">🔴</span>
                                        <span>Hard</span>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="type-card-group">
                                <h4 style="margin-bottom: 1rem; color: var(--primary); font-weight: 600;">⚙️ Technical Interview</h4>
                                <div class="difficulty-buttons">
                                    <a href="interview.php?type=technical&difficulty=easy" class="difficulty-btn easy">
                                        <span class="difficulty-icon">🟢</span>
                                        <span>Easy</span>
                                    </a>
                                    <a href="interview.php?type=technical&difficulty=medium" class="difficulty-btn medium">
                                        <span class="difficulty-icon">🟡</span>
                                        <span>Medium</span>
                                    </a>
                                    <a href="interview.php?type=technical&difficulty=hard" class="difficulty-btn hard">
                                        <span class="difficulty-icon">🔴</span>
                                        <span>Hard</span>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="type-card-group">
                                <h4 style="margin-bottom: 1rem; color: var(--primary); font-weight: 600;">🧩 LeetCode Challenge</h4>
                                <div class="difficulty-buttons">
                                    <a href="interview.php?type=leetcode&difficulty=easy" class="difficulty-btn easy">
                                        <span class="difficulty-icon">🟢</span>
                                        <span>Easy</span>
                                    </a>
                                    <a href="interview.php?type=leetcode&difficulty=medium" class="difficulty-btn medium">
                                        <span class="difficulty-icon">🟡</span>
                                        <span>Medium</span>
                                    </a>
                                    <a href="interview.php?type=leetcode&difficulty=hard" class="difficulty-btn hard">
                                        <span class="difficulty-icon">🔴</span>
                                        <span>Hard</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>Recent Sessions</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($recent_sessions->num_rows > 0): ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Questions</th>
                                        <th>Score</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($session = $recent_sessions->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo strtoupper($session['session_type']); ?></td>
                                            <td><?php echo $session['total_questions']; ?></td>
                                            <td><?php echo number_format($session['score'], 1); ?>%</td>
                                            <td>
                                                <?php
                                                $badge_class = 'badge-info';
                                                if ($session['status'] === 'completed') $badge_class = 'badge-success';
                                                if ($session['status'] === 'abandoned') $badge_class = 'badge-danger';
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?>">
                                                    <?php echo ucfirst($session['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($session['created_at'])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p style="text-align: center; color: var(--text-light); padding: 2rem;">
                                No sessions yet. Start your first interview practice!
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
