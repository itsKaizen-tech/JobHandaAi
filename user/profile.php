<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireLogin();

$user = getUserInfo($conn, $_SESSION['user_id']);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    
    if (empty($full_name) || empty($email)) {
        $message = '<div class="alert alert-error">Please fill in all fields</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<div class="alert alert-error">Invalid email format</div>';
    } else {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $full_name, $email, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Profile updated successfully!</div>';
            $user = getUserInfo($conn, $_SESSION['user_id']);
        } else {
            $message = '<div class="alert alert-error">Failed to update profile</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - JobHandaAI</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
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
                <li><a href="sessions.php"><span class="icon">📝</span> My Sessions</a></li>
                <li><a href="profile.php" class="active"><span class="icon">👤</span> Profile</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="topbar">
                <div class="topbar-left">
                    <h1>Profile</h1>
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
                <div class="card" style="max-width: 600px;">
                    <div class="card-header">
                        <h2>Edit Profile</h2>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        
                        <form method="POST" action="">
                            <div class="form-group">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-input" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                <small style="color: var(--text-light);">Username cannot be changed</small>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="full_name">Full Name</label>
                                <input 
                                    type="text" 
                                    id="full_name" 
                                    name="full_name" 
                                    class="form-input" 
                                    value="<?php echo htmlspecialchars($user['full_name']); ?>"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="email">Email</label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    class="form-input" 
                                    value="<?php echo htmlspecialchars($user['email']); ?>"
                                    required
                                >
                            </div>

                            <button type="submit" class="btn-submit">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
