# Installation Guide - JobHandaAI Enhanced

## Prerequisites

Before you begin, ensure you have:
- ✅ PHP 7.4 or higher installed
- ✅ MySQL 5.7 or higher installed
- ✅ Apache or Nginx web server
- ✅ Hugging Face account (free) for API token

## Step-by-Step Installation

### 1. Download and Extract

Extract the JobHandaAI-Enhanced folder to your web server directory:
```bash
# For XAMPP
C:\xampp\htdocs\JobHandaAI-Enhanced

# For WAMP
C:\wamp64\www\JobHandaAI-Enhanced

# For Linux/Apache
/var/www/html/JobHandaAI-Enhanced
```

### 2. Database Setup

#### Create the Database
```sql
-- Open MySQL command line or phpMyAdmin and run:
CREATE DATABASE jobhandaai;
```

#### Import Main Database
```bash
# From command line:
mysql -u root -p jobhandaai < database.sql

# Or use phpMyAdmin:
# 1. Select 'jobhandaai' database
# 2. Click 'Import' tab
# 3. Choose 'database.sql' file
# 4. Click 'Go'
```

#### Apply Performance Optimizations (IMPORTANT!)
```bash
# From command line:
mysql -u root -p jobhandaai < database_optimization.sql

# This adds indexes and optimizations for multiple concurrent users
```

### 3. Configure API Keys

#### Get Hugging Face API Token
1. Go to https://huggingface.co/
2. Create a free account
3. Go to Settings → Access Tokens
4. Create a new token (Read access is sufficient)
5. Copy the token

#### Update config.php
Edit `includes/config.php`:
```php
// Replace 'hide' with your actual Hugging Face token
define('HF_TOKEN', 'hf_xxxxxxxxxxxxxxxxxxxxx');
```

### 4. Set Permissions (Linux/Mac only)

```bash
# Make sure web server can write to logs directory
mkdir logs
chmod 755 logs

# If on Linux, set proper ownership
sudo chown -R www-data:www-data /var/www/html/JobHandaAI-Enhanced
```

### 5. Web Server Configuration

#### For Apache (.htaccess already included)
Make sure mod_rewrite is enabled:
```bash
sudo a2enmod rewrite
sudo service apache2 restart
```

#### For Nginx
Add this to your server block:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 6. Access the Application

Open your browser and navigate to:
```
http://localhost/JobHandaAI-Enhanced
```

### 7. First Login

**Default Admin Account:**
- Username: `admin`
- Email: `admin@jobhandaai.com`
- Password: `admin123`

**⚠️ IMPORTANT: Change this password immediately!**

## Post-Installation Steps

### 1. Change Admin Password
1. Login with default credentials
2. Go to Profile
3. Update password
4. Save changes

### 2. Test the System

#### Test Interview Functionality
1. Click "Start Interview"
2. Select HR → Medium
3. Verify question generates
4. Submit an answer
5. Check feedback appears
6. End session

#### Test Admin Features
1. Go to Users page
2. Try changing a user's role
3. Check pagination works
4. View sessions page

### 3. Create Test User
1. Logout from admin
2. Click "Register"
3. Create a new account
4. Test user features
5. Login as admin
6. Verify user appears in admin panel

## Troubleshooting

### Database Connection Failed
**Error:** "Connection failed: Access denied"
**Solution:**
1. Check MySQL is running
2. Verify credentials in `config.php`
3. Ensure database 'jobhandaai' exists

### AI Questions Not Generating
**Error:** "Failed to generate question"
**Solutions:**
1. Verify Hugging Face token is correct
2. Check internet connection
3. Ensure `curl` PHP extension is enabled
4. Check API_URL is correct in config.php

### Session Errors
**Error:** "Session failed to start"
**Solutions:**
1. Check PHP session directory is writable
2. Verify session settings in php.ini
3. Clear browser cookies
4. Restart web server

### Pagination Not Working
**Error:** Pages don't change
**Solutions:**
1. Clear browser cache
2. Ensure database_optimization.sql was run
3. Check for JavaScript errors in console

### Permission Denied Errors (Linux)
**Error:** Can't write to files/directories
**Solution:**
```bash
sudo chown -R www-data:www-data /var/www/html/JobHandaAI-Enhanced
sudo chmod -R 755 /var/www/html/JobHandaAI-Enhanced
```

## Performance Optimization

### For Production Deployment

#### 1. MySQL Configuration
Edit MySQL config file (`my.cnf` or `my.ini`):
```ini
[mysqld]
innodb_buffer_pool_size = 512M
innodb_log_file_size = 128M
max_connections = 200
query_cache_size = 64M
query_cache_type = 1
```

#### 2. PHP Configuration
Edit `php.ini`:
```ini
memory_limit = 256M
max_execution_time = 120
upload_max_filesize = 10M
post_max_size = 10M
```

#### 3. Enable OPcache
```ini
[opcache]
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
```

#### 4. Use Redis for Sessions (Optional)
```bash
# Install Redis
sudo apt-get install redis-server php-redis

# Update config.php
ini_set('session.save_handler', 'redis');
ini_set('session.save_path', 'tcp://127.0.0.1:6379');
```

## Security Recommendations

### 1. Production Checklist
- [ ] Change default admin password
- [ ] Update HF_TOKEN to your own
- [ ] Set display_errors to 0 in config.php
- [ ] Use HTTPS (SSL certificate)
- [ ] Set secure session cookies
- [ ] Regular database backups
- [ ] Keep PHP and MySQL updated

### 2. File Permissions
```bash
# Files should be 644
find . -type f -exec chmod 644 {} \;

# Directories should be 755
find . -type d -exec chmod 755 {} \;
```

### 3. Database Backups
```bash
# Create backup script
mysqldump -u root -p jobhandaai > backup_$(date +%Y%m%d).sql

# Schedule daily backups with cron
0 2 * * * mysqldump -u root -p jobhandaai > /backups/jobhandaai_$(date +\%Y\%m\%d).sql
```

## Upgrade Guide

### From Original Version

1. **Backup everything**
```bash
# Backup files
cp -r JobHandaAI JobHandaAI-backup

# Backup database
mysqldump -u root -p jobhandaai > jobhandaai_backup.sql
```

2. **Run database updates**
```sql
-- Add difficulty column if not exists
ALTER TABLE interview_sessions ADD COLUMN difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'medium' AFTER session_type;
```

3. **Apply optimizations**
```bash
mysql -u root -p jobhandaai < database_optimization.sql
```

4. **Update files**
- Replace all files except config.php
- Manually merge config.php settings
- Keep your HF_TOKEN

5. **Test thoroughly**
- Test all interview types
- Test pagination
- Test admin role changes
- Test user sessions

## Verification Checklist

After installation, verify:
- [ ] Can access homepage
- [ ] Can register new user
- [ ] Can login successfully
- [ ] Interview modal appears with dropdowns
- [ ] Questions generate correctly
- [ ] AI feedback works
- [ ] Sessions are saved
- [ ] Pagination shows on sessions page
- [ ] Admin can access admin panel
- [ ] Admin can change user roles
- [ ] All pages load without errors
- [ ] Console shows no JavaScript errors

## Support

If you encounter issues:
1. Check error logs in `logs/error.log`
2. Check MySQL error logs
3. Check Apache/Nginx error logs
4. Review this installation guide
5. Verify all requirements are met

## Success! 🎉

You should now have a fully functional JobHandaAI Enhanced installation with:
- ✅ Interview type and difficulty selection
- ✅ Enhanced dashboard design
- ✅ Pagination on all list pages
- ✅ Admin role management
- ✅ Performance optimizations

Start practicing for your interviews!
