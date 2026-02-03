<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireAdmin();

$user = getUserInfo($conn, $_SESSION['user_id']);

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch_assoc()['count'];
$total_sessions = $conn->query("SELECT COUNT(*) as count FROM interview_sessions")->fetch_assoc()['count'];
$completed_sessions = $conn->query("SELECT COUNT(*) as count FROM interview_sessions WHERE status = 'completed'")->fetch_assoc()['count'];
$total_questions = $conn->query("SELECT COUNT(*) as count FROM interview_qa")->fetch_assoc()['count'];

// Get recent sessions
$recent_sessions = $conn->query("
    SELECT s.*, u.username, u.full_name 
    FROM interview_sessions s 
    JOIN users u ON s.user_id = u.id 
    ORDER BY s.created_at DESC 
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - JobHandaAI</title>
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
                <li><a href="users.php"><span class="icon">👥</span> Users</a></li>
                <li><a href="sessions.php"><span class="icon">📝</span> All Sessions</a></li>
                <li><a href="settings.php"><span class="icon">⚙️</span> Settings</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="topbar">
                <div class="topbar-left">
                    <h1>Admin Dashboard</h1>
                </div>
                <div class="topbar-right">
                    <div class="user-info">
                        <div class="user-avatar"><?php echo strtoupper(substr($user['username'], 0, 2)); ?></div>
                        <div class="user-details">
                            <div class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                            <div class="user-role">Admin</div>
                        </div>
                    </div>
                    <a href="../logout.php" class="btn-logout">Logout</a>
                </div>
            </div>

            <div class="content-area">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-icon">👥</div>
                        </div>
                        <div class="stat-label">Total Users</div>
                        <div class="stat-value"><?php echo $total_users; ?></div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-icon">📚</div>
                        </div>
                        <div class="stat-label">Total Sessions</div>
                        <div class="stat-value"><?php echo $total_sessions; ?></div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-icon">✅</div>
                        </div>
                        <div class="stat-label">Completed Sessions</div>
                        <div class="stat-value"><?php echo $completed_sessions; ?></div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-icon">❓</div>
                        </div>
                        <div class="stat-label">Questions Asked</div>
                        <div class="stat-value"><?php echo $total_questions; ?></div>
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
                                        <th>ID</th>
                                        <th>User</th>
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
                                            <td><?php echo $session['id']; ?></td>
                                            <td><?php echo htmlspecialchars($session['full_name']); ?></td>
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
                                            <td><?php echo date('M d, Y H:i', strtotime($session['created_at'])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p style="text-align: center; color: var(--text-light); padding: 2rem;">No sessions yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
