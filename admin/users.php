<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireAdmin();

$user = getUserInfo($conn, $_SESSION['user_id']);

// Handle role update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['role'];
    
    if (in_array($new_role, ['user', 'admin']) && $user_id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $new_role, $user_id);
        $stmt->execute();
        $success_message = "User role updated successfully!";
    }
}

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Get total count
$total_count = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$total_pages = ceil($total_count / $limit);

// Get paginated users
$users = $conn->query("
    SELECT u.*, up.total_sessions, up.average_score 
    FROM users u 
    LEFT JOIN user_progress up ON u.id = up.user_id 
    ORDER BY u.created_at DESC
    LIMIT $limit OFFSET $offset
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - JobHandaAI</title>
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
                <li><a href="users.php" class="active"><span class="icon">👥</span> Users</a></li>
                <li><a href="sessions.php"><span class="icon">📝</span> All Sessions</a></li>
                <li><a href="settings.php"><span class="icon">⚙️</span> Settings</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="topbar">
                <div class="topbar-left">
                    <h1>Users Management</h1>
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
                <?php if (isset($success_message)): ?>
                    <div style="background: #22c55e; color: white; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center;">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h2>All Users</h2>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Sessions</th>
                                    <th>Avg Score</th>
                                    <th>Registered</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($u = $users->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $u['id']; ?></td>
                                        <td><?php echo htmlspecialchars($u['username']); ?></td>
                                        <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $u['role'] === 'admin' ? 'badge-warning' : 'badge-info'; ?>">
                                                <?php echo ucfirst($u['role']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $u['total_sessions'] ?? 0; ?></td>
                                        <td><?php echo number_format($u['average_score'] ?? 0, 1); ?>%</td>
                                        <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                                        <td><?php echo $u['last_login'] ? date('M d, Y H:i', strtotime($u['last_login'])) : 'Never'; ?></td>
                                        <td>
                                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                                    <select name="role" onchange="if(confirm('Change role for <?php echo htmlspecialchars($u['username']); ?>?')) this.form.submit();" style="padding: 0.5rem; border: 2px solid var(--border); border-radius: 4px; cursor: pointer;">
                                                        <option value="user" <?php echo $u['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                                        <option value="admin" <?php echo $u['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                    </select>
                                                    <input type="hidden" name="update_role" value="1">
                                                </form>
                                            <?php else: ?>
                                                <span style="color: var(--text-light); font-style: italic;">Current User</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        
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
                                Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $total_count); ?> of <?php echo $total_count; ?> users
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
