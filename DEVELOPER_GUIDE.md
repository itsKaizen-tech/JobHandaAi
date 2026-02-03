# Developer Quick Start - JobHandaAI Enhanced

## 🚀 Quick Setup (5 Minutes)

### 1. Prerequisites Check
```bash
# Check PHP version (need 7.4+)
php -v

# Check MySQL
mysql --version

# Check if mysqli extension is enabled
php -m | grep mysqli
```

### 2. Database Setup
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE jobhandaai;"

# Import schema
mysql -u root -p jobhandaai < database.sql

# Apply optimizations
mysql -u root -p jobhandaai < database_optimization.sql
```

### 3. Configuration
```bash
# Edit config file
nano includes/config.php

# Update this line:
define('HF_TOKEN', 'your_token_here');
```

### 4. Test
```
http://localhost/JobHandaAI-Enhanced
Login: admin / admin123
```

## 📁 Project Structure

```
JobHandaAI-Enhanced/
├── admin/              # Admin panel pages
│   ├── dashboard.php   # Stats overview
│   ├── users.php       # User management + role changes
│   ├── sessions.php    # All sessions + pagination
│   └── settings.php    # Platform settings
├── user/               # User interface
│   ├── dashboard.php   # Enhanced Quick Start
│   ├── interview.php   # Modal + interview flow
│   ├── sessions.php    # User sessions + pagination
│   ├── profile.php     # User profile
│   └── api/            # Backend API
│       ├── generate_question.php  # AI question generation
│       ├── submit_answer.php      # Answer evaluation
│       └── end_session.php        # Session completion
├── includes/           # Core functionality
│   ├── config.php      # DB + performance config
│   └── auth.php        # Authentication logic
├── assets/
│   ├── css/
│   │   ├── main.css         # Global styles
│   │   └── dashboard.css    # Dashboard + pagination styles
│   └── js/
│       └── main.js          # Frontend scripts
└── *.sql               # Database files
```

## 🔑 Key Features Explained

### 1. Interview Configuration Modal
**Location**: `user/interview.php`

```php
// Shows modal when type or difficulty not set
if (!$session_type || !$difficulty) {
    $show_modal = true;
} else {
    // Create session with selected type and difficulty
    $stmt->prepare("INSERT INTO interview_sessions 
                   (user_id, session_type, difficulty, status) 
                   VALUES (?, ?, ?, 'active')");
}
```

**Frontend**: Modal with dropdowns
```html
<select id="sessionType">
    <option value="hr">HR Interview</option>
    <option value="technical">Technical</option>
    <option value="leetcode">LeetCode</option>
</select>
```

### 2. Enhanced Quick Start
**Location**: `user/dashboard.php`

```html
<!-- Grouped by type with difficulty buttons -->
<div class="type-card-group">
    <h4>💼 HR Interview</h4>
    <div class="difficulty-buttons">
        <a href="interview.php?type=hr&difficulty=easy" 
           class="difficulty-btn easy">Easy</a>
        <!-- ... more difficulty buttons -->
    </div>
</div>
```

**Styling**: Color-coded in `assets/css/dashboard.css`
```css
.difficulty-btn.easy { color: #22c55e; }
.difficulty-btn.medium { color: #eab308; }
.difficulty-btn.hard { color: #ef4444; }
```

### 3. Pagination System
**Location**: Multiple pages

```php
// Pagination logic
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$total_count = /* COUNT query */;
$total_pages = ceil($total_count / $limit);

// Query with LIMIT and OFFSET
$query = "SELECT * FROM table LIMIT $limit OFFSET $offset";
```

**UI Component**:
```html
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>">« Previous</a>
    <?php endif; ?>
    
    <!-- Page numbers -->
    
    <?php if ($page < $total_pages): ?>
        <a href="?page=<?= $page + 1 ?>">Next »</a>
    <?php endif; ?>
</div>
```

### 4. Role Management
**Location**: `admin/users.php`

```php
// Handle role update
if ($_POST['update_role']) {
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['role'];
    
    if (in_array($new_role, ['user', 'admin']) && 
        $user_id != $_SESSION['user_id']) {
        $stmt->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $new_role, $user_id);
        $stmt->execute();
    }
}
```

**UI**:
```html
<form method="POST">
    <select name="role" onchange="if(confirm('...')) this.form.submit();">
        <option value="user" <?= $u['role']==='user'?'selected':'' ?>>User</option>
        <option value="admin" <?= $u['role']==='admin'?'selected':'' ?>>Admin</option>
    </select>
</form>
```

### 5. Performance Optimizations
**Database Indexes**: `database_optimization.sql`

```sql
-- Speed up user session queries
CREATE INDEX idx_sessions_user_id ON interview_sessions(user_id);
CREATE INDEX idx_sessions_created_at ON interview_sessions(created_at DESC);
CREATE INDEX idx_sessions_user_date ON interview_sessions(user_id, created_at DESC);
```

**PHP Config**: `includes/config.php`
```php
ini_set('max_execution_time', 120);
ini_set('memory_limit', '256M');
ini_set('session.gc_maxlifetime', 3600);
```

## 🛠️ Common Development Tasks

### Adding a New Interview Type

1. **Update Database**:
```sql
ALTER TABLE interview_sessions 
MODIFY session_type ENUM('hr', 'technical', 'leetcode', 'NEW_TYPE');
```

2. **Update Interview Page**:
```html
<!-- In interview.php modal -->
<option value="new_type">🔥 New Type</option>
```

3. **Update Dashboard**:
```html
<!-- In dashboard.php Quick Start -->
<div class="type-card-group">
    <h4>🔥 New Type</h4>
    <!-- Difficulty buttons -->
</div>
```

4. **Update Question Generator**:
```php
// In generate_question.php
$prompts['new_type'] = "Generate a question for...";
```

### Adding a New Difficulty Level

1. **Database**:
```sql
ALTER TABLE interview_sessions 
MODIFY difficulty ENUM('easy', 'medium', 'hard', 'expert');
```

2. **Update All Dropdowns**:
```html
<option value="expert">⚫ Expert</option>
```

3. **Add CSS**:
```css
.difficulty-btn.expert {
    color: #6b21a8;
    border-color: #6b21a8;
}
```

### Customizing AI Prompts

Edit `user/api/generate_question.php`:

```php
$difficulty_instructions = [
    'easy' => 'Make it beginner-friendly...',
    'medium' => 'Moderately challenging...',
    'hard' => 'Advanced level...'
];

$prompts = [
    'hr' => "Generate a behavioral question..." . $difficulty_instructions[$difficulty],
    // ... more types
];
```

### Adding New Statistics

1. **Create Database View**:
```sql
CREATE VIEW user_stats AS 
SELECT user_id, COUNT(*) as total, AVG(score) as avg_score
FROM interview_sessions GROUP BY user_id;
```

2. **Query in PHP**:
```php
$stats = $conn->query("SELECT * FROM user_stats WHERE user_id = $user_id");
```

3. **Display in Dashboard**:
```html
<div class="stat-card">
    <div class="stat-value"><?= $stats['total'] ?></div>
    <div class="stat-label">Total</div>
</div>
```

## 🧪 Testing Checklist

### Manual Testing
```
□ Register new user
□ Login successfully
□ Click "Start Interview"
□ Modal appears with dropdowns
□ Select HR + Medium
□ Question generates
□ Submit answer
□ Receive feedback
□ Next question works
□ End session
□ Score displays
□ Sessions page shows pagination
□ Admin login
□ View users with pagination
□ Change user role
□ View all sessions
```

### Database Testing
```sql
-- Check indexes exist
SHOW INDEX FROM interview_sessions;

-- Check session counts
SELECT session_type, difficulty, COUNT(*) 
FROM interview_sessions GROUP BY session_type, difficulty;

-- Check pagination query performance
EXPLAIN SELECT * FROM interview_sessions ORDER BY created_at DESC LIMIT 10;
```

## 📊 Monitoring & Debugging

### Enable Error Logging
```php
// In config.php (development only)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');
```

### Check Slow Queries
```sql
-- Enable slow query log in MySQL
SET GLOBAL slow_query_log = 1;
SET GLOBAL long_query_time = 1;

-- View slow queries
SELECT * FROM mysql.slow_log;
```

### Monitor Sessions
```php
// Check active sessions
$active = $conn->query("SELECT COUNT(*) FROM interview_sessions WHERE status='active'");

// Average session duration
$avg_time = $conn->query("SELECT AVG(time_spent) FROM interview_sessions WHERE status='completed'");
```

## 🔐 Security Best Practices

```php
// Always use prepared statements
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

// Escape output
echo htmlspecialchars($user['name']);

// Validate input
$difficulty = in_array($_POST['difficulty'], ['easy','medium','hard']) 
              ? $_POST['difficulty'] : 'medium';

// Check permissions
if (!isAdmin()) {
    header('Location: /user/dashboard.php');
    exit();
}
```

## 📚 Additional Resources

- **Hugging Face API**: https://huggingface.co/docs/api-inference
- **PHP MySQL**: https://www.php.net/manual/en/book.mysqli.php
- **Session Security**: https://www.php.net/manual/en/session.security.php

## 🆘 Common Issues

### Question Not Generating
```php
// Check in generate_question.php
error_log("API Response: " . $response);
error_log("HTTP Code: " . $http_code);
```

### Pagination Not Working
```sql
-- Verify indexes
SHOW INDEX FROM interview_sessions;

-- If missing, run:
CREATE INDEX idx_sessions_created_at ON interview_sessions(created_at DESC);
```

### Role Change Fails
```php
// Check user isn't changing own role
if ($user_id == $_SESSION['user_id']) {
    die("Cannot change own role");
}
```

---

**Happy Coding! 🚀**

For more details, see:
- README_ENHANCED.md (Features)
- INSTALLATION_GUIDE.md (Setup)
- CHANGELOG.md (What's new)
