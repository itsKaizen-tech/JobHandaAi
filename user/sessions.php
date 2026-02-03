<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireLogin();

$user = getUserInfo($conn, $_SESSION['user_id']);

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Get total count
$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM interview_sessions WHERE user_id = ?");
$count_stmt->bind_param("i", $_SESSION['user_id']);
$count_stmt->execute();
$total_count = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_count / $limit);

// Get paginated user sessions
$stmt = $conn->prepare("
    SELECT * FROM interview_sessions 
    WHERE user_id = ? 
    ORDER BY created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $_SESSION['user_id'], $limit, $offset);
$stmt->execute();
$sessions = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Sessions - JobHandaAI</title>
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
                    <h1>My Sessions</h1>
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
                <div class="card">
                    <div class="card-header">
                        <h2>All Interview Sessions</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($sessions->num_rows > 0): ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Difficulty</th>
                                        <th>Questions</th>
                                        <th>Correct</th>
                                        <th>Score</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($session = $sessions->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $session['id']; ?></td>
                                            <td><?php echo strtoupper($session['session_type']); ?></td>
                                            <td><?php echo ucfirst($session['difficulty']); ?></td>
                                            <td><?php echo $session['total_questions']; ?></td>
                                            <td><?php echo $session['correct_answers']; ?></td>
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
                                            <td>
                                                <a href="session_details.php?id=<?php echo $session['id']; ?>" 
                                                   style="color: var(--primary); text-decoration: none;">
                                                    View Details
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p style="text-align: center; color: var(--text-light); padding: 3rem;">
                                No sessions yet. <a href="interview.php" style="color: var(--primary);">Start your first interview!</a>
                            </p>
                        <?php endif; ?>
                        
                        <?php if ($total_pages > 1): ?>
                            <div class="pagination">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?>">« Previous</a>
                                <?php else: ?>
                                    <span class="disabled">« Previous</span>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <?php if ($i == $page): ?>
                                        <span class="active"><?php echo $i; ?></span>
                                    <?php else: ?>
                                        <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <a href="?page=<?php echo $page + 1; ?>">Next »</a>
                                <?php else: ?>
                                    <span class="disabled">Next »</span>
                                <?php endif; ?>
                            </div>
                            <div class="pagination-info">
                                Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $total_count); ?> of <?php echo $total_count; ?> sessions
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
