<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireAdmin();

$user = getUserInfo($conn, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - JobHandaAI</title>
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
                <li><a href="users.php"><span class="icon">👥</span> Users</a></li>
                <li><a href="sessions.php"><span class="icon">📝</span> All Sessions</a></li>
                <li><a href="settings.php" class="active"><span class="icon">⚙️</span> Settings</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="topbar">
                <div class="topbar-left">
                    <h1>Settings</h1>
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
                <div class="card">
                    <div class="card-header">
                        <h2>System Information</h2>
                    </div>
                    <div class="card-body">
                        <div style="background: var(--bg); padding: 1.5rem; border-radius: 8px;">
                            <h3 style="color: var(--primary); margin-bottom: 1rem;">Database Configuration</h3>
                            <p><strong>Database Name:</strong> <?php echo DB_NAME; ?></p>
                            <p><strong>Database Host:</strong> <?php echo DB_HOST; ?></p>
                            <p style="margin-top: 1.5rem;"><strong>AI Model:</strong> <?php echo AI_MODEL; ?></p>
                            <p><strong>API Endpoint:</strong> <?php echo API_URL; ?></p>
                        </div>

                        <div style="background: #fff3cd; padding: 1.5rem; border-radius: 8px; margin-top: 1.5rem; border-left: 4px solid #856404;">
                            <h3 style="color: #856404; margin-bottom: 1rem;">⚠️ Important Security Note</h3>
                            <p style="color: #856404;">
                                The Hugging Face API token is stored in <code>/includes/config.php</code>. 
                                Make sure to keep this file secure and never commit it to version control.
                                Replace "hide" with your actual Hugging Face API token.
                            </p>
                        </div>

                        <div style="margin-top: 2rem;">
                            <h3 style="color: var(--primary); margin-bottom: 1rem;">Quick Actions</h3>
                            <p style="margin-bottom: 1rem;">
                                To change a user's role to admin, manually update the database:
                            </p>
                            <pre style="background: var(--bg); padding: 1rem; border-radius: 6px; overflow-x: auto;">UPDATE users SET role = 'admin' WHERE username = 'your_username';</pre>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
