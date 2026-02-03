# JobHandaAI - Quick Installation Guide

## 🚀 Quick Start (5 Minutes)

### Step 1: Requirements Check
- ✅ PHP 7.4+ installed
- ✅ MySQL 5.7+ installed
- ✅ Web server (Apache/Nginx) running
- ✅ Hugging Face account created

### Step 2: Database Setup

1. **Create Database**
   ```sql
   CREATE DATABASE jobhandaai;
   ```

2. **Import Schema**
   - Open phpMyAdmin
   - Select `jobhandaai` database
   - Click "Import"
   - Choose `database.sql`
   - Click "Go"

### Step 3: Configure Application

1. **Edit Database Config** (`/includes/config.php`)
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');           // Your MySQL username
   define('DB_PASS', '');               // Your MySQL password
   define('DB_NAME', 'jobhandaai');
   ```

2. **Add Hugging Face Token** (Same file)
   ```php
   define('HF_TOKEN', 'your_token_here'); // Replace with actual token
   ```
   
   Get token from: https://huggingface.co/settings/tokens

### Step 4: Access Application

1. **Place files in web root**
   - XAMPP: `C:/xampp/htdocs/JobHandaAI/`
   - WAMP: `C:/wamp64/www/JobHandaAI/`
   - Linux: `/var/www/html/JobHandaAI/`

2. **Open in browser**
   ```
   http://localhost/JobHandaAI/
   ```

### Step 5: Login

**Default Admin Account:**
- Username: `admin`
- Password: `admin123`

**⚠️ Change this password immediately!**

## 📋 What You Get

### User Features
✨ AI-powered interview questions  
✨ Instant feedback on answers  
✨ Score tracking and analytics  
✨ Session history review  
✨ Progress monitoring  

### Admin Features
👥 User management  
📊 System analytics  
📝 Session monitoring  
⚙️ Settings control  

## 🔧 Troubleshooting

### Problem: "Connection failed"
**Solution**: Check database credentials in `/includes/config.php`

### Problem: "Questions not generating"
**Solution**: 
1. Verify Hugging Face token is correct
2. Check internet connection
3. Ensure token has API access permissions

### Problem: "Page not found"
**Solution**: Make sure files are in correct web server directory

### Problem: "Session not saving"
**Solution**: Check if all database tables were created properly

## 🎯 Quick Test

1. Register new user account
2. Login with new account
3. Click "Start Interview"
4. Choose interview type
5. Answer a question
6. Check feedback and score

If all steps work, installation is successful! ✅

## 📁 Important Files

| File | Purpose |
|------|---------|
| `/includes/config.php` | Database & API config |
| `/database.sql` | Database structure |
| `/index.php` | Landing page |
| `/login.php` | Login page |
| `/user/dashboard.php` | User dashboard |
| `/admin/dashboard.php` | Admin dashboard |

## 🔐 Security Notes

1. **Change default admin password**
2. **Keep API token secret** (never commit to git)
3. **Use HTTPS in production**
4. **Regular database backups**
5. **Update PHP & MySQL regularly**

## 💡 Tips

- Test with a dummy user account first
- Review session details to see AI feedback
- Check admin dashboard for system overview
- Explore all three interview types

## 📞 Need Help?

1. Check README.md for detailed documentation
2. Review code comments in PHP files
3. Verify all configuration steps
4. Check browser console for JavaScript errors
5. Check PHP error logs

---

**Ready to start?** Follow the 5 steps above and you'll be running in minutes! 🎉
