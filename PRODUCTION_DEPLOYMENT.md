# 🚀 Production Deployment Guide

## ⚠️ CRITICAL: What NOT to Push to Git

### ❌ NEVER Push These Files:
```bash
.env                    # Contains your email password!
/vendor/                # PHPMailer library (too large)
*.log                   # Log files
/admin/uploads/*        # User uploaded files
/admin/workshops_img/*  # Workshop images
```

**These are already protected in `.gitignore`** ✅

---

## ✅ What TO Push to Git

Push all your code files:
- `/ajax/` (contact.php, workshop-register.php)
- `/includes/` (MailService.php)
- `/config/` (mail.php)
- `/pages/` (workshop.php, contact.html, etc.)
- `composer.json`
- `.env.example` (template only, not the real .env)
- All other PHP, HTML, CSS, JS files

---

## 📋 Step-by-Step Production Deployment

### Step 1: Push Your Code to Git

```powershell
# Check what will be committed
git status

# Add all files (except those in .gitignore)
git add .

# Commit your changes
git commit -m "Add PHPMailer email system with contact & workshop forms"

# Push to repository
git push origin main
```

**✅ Safe:** `.env` and `/vendor/` won't be pushed (protected by .gitignore)

---

### Step 2: On Production Server

After deploying your code to production server:

#### A. Install PHPMailer

**Option 1: Using Composer (Recommended)**
```bash
composer install
```

**Option 2: Manual Installation (if no Composer)**
```bash
# Download PHPMailer
wget https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.9.1.zip
unzip v6.9.1.zip
mv PHPMailer-6.9.1 vendor
rm v6.9.1.zip
```

#### B. Create Production .env File

```bash
# Copy template
cp .env.example .env

# Edit with production values
nano .env
```

**Production .env:**
```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-production-email@gmail.com
SMTP_PASSWORD=your-production-app-password
SMTP_FROM_EMAIL=your-production-email@gmail.com
SMTP_FROM_NAME=Coaching Management System
SMTP_ADMIN_EMAIL=admin@yourcompany.com

APP_NAME=Coaching Management
APP_URL=https://yourdomain.com
```

#### C. Set Proper Permissions

```bash
# Secure .env file
chmod 600 .env

# Set directory permissions
chmod 755 ajax/
chmod 755 includes/
chmod 755 config/
chmod 777 admin/uploads/
chmod 777 admin/workshops_img/
```

#### D. Update Configuration

1. **Remove Test/Debug Mode**
   - Check all files for `localhost` references
   - Update URLs to production domain

2. **Enable Error Logging**
   ```php
   // In php.ini or .htaccess
   error_reporting(E_ALL);
   log_errors = On
   display_errors = Off  // Hide errors from users
   error_log = /path/to/logs/error.log
   ```

---

## 🔒 Security Checklist for Production

### Before Going Live:

- [ ] ✅ `.env` file has production credentials (NOT development)
- [ ] ✅ `.env` file is NOT in Git repository
- [ ] ✅ File permissions are set correctly (600 for .env)
- [ ] ✅ Display errors is OFF (`display_errors = Off`)
- [ ] ✅ Error logging is ON and going to secure log file
- [ ] ✅ All "localhost" URLs changed to production domain
- [ ] ✅ Test emails send successfully from production
- [ ] ✅ SSL/HTTPS is enabled for your domain
- [ ] ✅ Database credentials are production values
- [ ] ✅ Remove or disable `test-mail.php` (or protect it)

---

## 🧪 Testing After Deployment

### 1. Test Contact Form
Visit: `https://yourdomain.com/pages/contact.html`
- Fill out form
- Submit
- Check admin email receives message

### 2. Test Workshop Registration
Visit: `https://yourdomain.com/pages/workshop.php`
- Click "Register Now"
- Fill form
- Submit
- Check admin email receives notification
- Check user receives confirmation (if different email)

### 3. Check Error Logs
```bash
tail -f /path/to/error.log
```

---

## 🔄 Future Updates Workflow

When you make changes locally and want to deploy:

```powershell
# 1. Test locally first
docker-compose up -d

# 2. Commit and push
git add .
git commit -m "Description of changes"
git push origin main

# 3. On production server
git pull origin main

# 4. Test production
# Visit your website and test functionality
```

---

## 🚨 Important Notes

### Gmail App Passwords
- Each environment (dev/prod) should have its own Gmail App Password
- Don't reuse passwords across environments
- Generate at: https://myaccount.google.com/apppasswords

### Environment-Specific Settings
- **Development** (.env on your computer):
  - Can use test email addresses
  - Can have verbose error reporting
  
- **Production** (.env on server):
  - Use real business email
  - Hide errors from users
  - Log errors to secure file

### Database Considerations
If using the workshop registration database saving feature:
- Export development database structure
- Import to production database
- Update `db.php` with production credentials

---

## 📂 Production File Structure

```
production-server/
├── .env                    # ⚠️ CREATE ON SERVER (don't push)
├── .env.example            # ✅ Safe to push (template)
├── .gitignore              # ✅ Push this
├── vendor/                 # ⚠️ INSTALL ON SERVER (don't push)
│   └── PHPMailer files
├── ajax/                   # ✅ Push all files
│   ├── contact.php
│   └── workshop-register.php
├── includes/               # ✅ Push all files
│   └── MailService.php
├── config/                 # ✅ Push all files
│   └── mail.php
├── pages/                  # ✅ Push all files
│   ├── workshop.php
│   └── contact.html
└── ... (all other files)   # ✅ Push
```

---

## 🆘 Troubleshooting Production Issues

### Emails Not Sending
1. Check `.env` file exists and has correct credentials
2. Check `vendor/` folder exists with PHPMailer
3. Check error logs: `tail -f error.log`
4. Verify Gmail App Password is correct
5. Check firewall allows outbound SMTP (port 587)

### Permission Errors
```bash
# Fix permissions
chmod 600 .env
chmod 755 ajax/ includes/ config/
chown www-data:www-data -R /path/to/project
```

### File Not Found Errors
```bash
# Ensure vendor exists
ls -la vendor/src/PHPMailer.php

# Reinstall if missing
composer install
# or manual installation (see Step 2A above)
```

---

## ✅ Quick Pre-Deploy Checklist

**Before `git push`:**
- [ ] Test locally (both forms work)
- [ ] Check `.gitignore` includes `.env` and `/vendor/`
- [ ] Verify no sensitive data in code
- [ ] Remove any debug/test code

**After Deploy to Production:**
- [ ] Install PHPMailer on server
- [ ] Create production `.env` file
- [ ] Set file permissions
- [ ] Test contact form
- [ ] Test workshop registration
- [ ] Monitor error logs

---

## 🎉 You're Ready!

Your email system is production-ready. Just remember:

1. **DON'T push** `.env` or `/vendor/`
2. **DO push** all your code files
3. **CREATE** `.env` manually on production server
4. **INSTALL** PHPMailer on production server
5. **TEST** everything after deployment

Good luck with your deployment! 🚀
