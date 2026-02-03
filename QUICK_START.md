# Quick Setup Guide - JobHandaAI Enhanced

## 5-Minute Setup

### Step 1: Database Setup (2 minutes)
```bash
# Login to MySQL
mysql -u root -p

# Run the database script
mysql> source /path/to/JobHandaAI_Enhanced/database.sql
mysql> exit
```

### Step 2: Configure (1 minute)
Edit `includes/config.php`:
```php
// Update these lines only:
define('DB_USER', 'your_mysql_username');
define('DB_PASS', 'your_mysql_password');
define('HF_TOKEN', 'your_huggingface_token');
```

**Get Hugging Face Token:**
1. Go to https://huggingface.co/
2. Sign up/Login
3. Go to Settings → Access Tokens
4. Create new token
5. Copy and paste into config.php

### Step 3: Access (1 minute)
```
http://localhost/JobHandaAI_Enhanced
```

**Default Login:**
- Username: `admin`
- Password: `admin123`

### Step 4: Test (1 minute)
✅ Login
✅ Start Interview → Choose type & difficulty
✅ Answer a question
✅ Try Resume Builder
✅ Check Resources

## Troubleshooting

### "Cannot connect to database"
- Check DB_USER and DB_PASS in config.php
- Verify MySQL is running: `systemctl status mysql`

### "API Error" when generating questions
- Check HF_TOKEN in config.php
- Verify token is valid at huggingface.co
- Check internet connection

### "Page not found"
- Verify web server is running
- Check file paths and permissions
- Ensure .php files are readable

### Database errors
- Run database.sql again
- Check MySQL logs
- Verify user has CREATE/INSERT permissions

## Quick Feature Guide

### 🎯 Start Interview
1. Click "Start Interview"
2. Select: HR / Technical / LeetCode
3. Choose: Easy / Medium / Hard
4. Answer questions
5. Get AI feedback

### 📄 Resume Builder
1. Click "Resume Builder"
2. Fill all sections
3. Click "Analyze with AI"
4. Review score and tips
5. Improve and re-analyze

### 📚 Resources
1. Click "Resources"
2. Browse categories
3. Click company badges
4. Read tips and guides

## Default Accounts

**Admin:**
- Username: admin
- Password: admin123
- **Change this immediately!**

**Create New User:**
1. Click "Register"
2. Fill form
3. Login with new credentials

## File Permissions

If you get permission errors:

```bash
cd /var/www/html
sudo chown -R www-data:www-data JobHandaAI_Enhanced
sudo chmod -R 755 JobHandaAI_Enhanced
```

## Verification Checklist

After setup, verify:
- [ ] Can login with admin account
- [ ] Can register new user
- [ ] Can start interview
- [ ] Questions are generated
- [ ] Can submit answers
- [ ] Feedback is shown
- [ ] Can build resume
- [ ] Resume analysis works
- [ ] Resources page loads
- [ ] Dashboard shows stats

## Need Help?

1. Check README.md for detailed docs
2. Check IMPROVEMENTS.md for features
3. Review error logs: `/var/log/apache2/error.log`
4. Verify database tables created
5. Test API token at huggingface.co

## Advanced Configuration

### Change AI Model
In `includes/config.php`:
```php
define('AI_MODEL', 'meta-llama/Llama-3.3-70B-Instruct');
```

### Adjust Difficulty Thresholds
In `user/api/submit_answer.php`:
```php
$thresholds = [
    'easy' => 6,
    'medium' => 7,
    'hard' => 8
];
```

### Customize Theme
In `assets/css/main.css`:
```css
:root {
    --primary-color: #6366f1;
    --secondary-color: #a855f7;
}
```

That's it! You're ready to go! 🚀
