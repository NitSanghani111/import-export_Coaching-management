# Quick Installation Guide

This guide will help you set up your Import/Export Coaching Management website quickly.

## Prerequisites

Before starting, ensure you have:
- Web hosting with PHP 7.4+ and MySQL 5.7+
- FTP/SFTP access or cPanel File Manager
- phpMyAdmin or MySQL command line access
- Basic knowledge of uploading files and database management

## Step-by-Step Installation

### Step 1: Upload Files

1. **Download or Clone** the repository
2. **Upload all files** to your web server:
   - If using shared hosting: Upload to `public_html` or `www` directory
   - If using subdomain: Upload to the subdomain's directory
   - Use FTP client (FileZilla) or hosting File Manager

### Step 2: Create Database

#### Using cPanel (Hostinger/most shared hosts):

1. Login to cPanel
2. Go to "MySQL Databases"
3. Create a new database:
   - Database name: `coaching_management` (or your choice)
   - Click "Create Database"
4. Create a database user:
   - Username: `coaching_user` (or your choice)
   - Password: Create a strong password
   - Click "Create User"
5. Add user to database:
   - Select the user and database
   - Grant "ALL PRIVILEGES"
   - Click "Add"

#### Using Command Line:

```bash
mysql -u root -p
CREATE DATABASE coaching_management;
CREATE USER 'coaching_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON coaching_management.* TO 'coaching_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Import Database Schema

#### Using phpMyAdmin:

1. Open phpMyAdmin from your hosting control panel
2. Click on your database name (e.g., `coaching_management`)
3. Click the "Import" tab
4. Click "Choose File" and select `database.sql`
5. Click "Go" at the bottom of the page
6. Wait for success message

#### Using Command Line:

```bash
mysql -u coaching_user -p coaching_management < database.sql
```

### Step 4: Configure Database Connection

1. Open `config/db.php` in a text editor
2. Update these values:

```php
define('DB_HOST', 'localhost');           // Usually 'localhost'
define('DB_USER', 'coaching_user');       // Your database username
define('DB_PASS', 'your_password');       // Your database password
define('DB_NAME', 'coaching_management'); // Your database name
```

3. Save the file
4. Re-upload to your server

### Step 5: Set Permissions

Make sure upload directories are writable:

#### Using FTP:
1. Right-click on `uploads` folder
2. Select "File permissions" or "CHMOD"
3. Set to `755` (rwxr-xr-x)
4. Apply to subdirectories

#### Using SSH:
```bash
chmod 755 uploads/
chmod 755 uploads/blogs/
chmod 755 uploads/events/
```

### Step 6: Test Installation

1. Open your browser
2. Visit: `http://yourdomain.com/install-test.php`
3. Check all tests pass (green checkmarks)
4. If any tests fail, follow the instructions provided
5. **Delete `install-test.php`** after successful testing

### Step 7: Access Your Website

#### Public Website:
- Visit: `http://yourdomain.com/`
- You should see the home page with sample blogs

#### Admin Panel:
- Visit: `http://yourdomain.com/admin/login.php`
- **Default Credentials:**
  - Username: `admin`
  - Password: `admin123`

### Step 8: Change Admin Password

**IMPORTANT:** Change the default password immediately!

1. Login to admin panel
2. The default password is: `admin123`
3. To change it:
   - Go to phpMyAdmin
   - Select `coaching_management` database
   - Click on `admin` table
   - Click "Edit" on the admin user
   - Generate new password hash:
     ```php
     echo password_hash('your_new_password', PASSWORD_DEFAULT);
     ```
   - Or use online tool: https://bcrypt-generator.com/
   - Replace the password field with new hash
   - Save changes

## Post-Installation Configuration

### 1. Update Site Content

#### Contact Information:
- Edit `includes/footer.php` - Update footer contact details
- Edit `contact.php` - Update contact form information

#### About Page:
- Edit `about.php` - Update company information and statistics

#### Home Page:
- Edit `index.php` - Update hero section and features

### 2. Add Your Content

#### Create Blog Posts:
1. Login to admin panel
2. Go to "Blogs"
3. Click "Add New Blog"
4. Fill in details and upload image
5. Publish

#### Create Events:
1. Go to "Events"
2. Click "Add New Event"
3. Fill in event details
4. Upload event image
5. Save

### 3. Enable HTTPS (Recommended)

1. Get SSL certificate (free with Let's Encrypt)
2. Install certificate on your hosting
3. Edit `.htaccess` file
4. Uncomment these lines:
   ```apache
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

### 4. Security Checklist

- [ ] Change default admin password
- [ ] Delete `install-test.php`
- [ ] Ensure `config/db.php` is not publicly accessible
- [ ] Enable HTTPS
- [ ] Keep PHP and MySQL updated
- [ ] Set up regular backups
- [ ] Review and update `.htaccess` security rules

## Common Issues & Solutions

### Database Connection Failed

**Problem:** "Connection failed" error
**Solution:** 
- Check database credentials in `config/db.php`
- Verify database exists
- Check if database user has proper permissions
- For remote databases, update `DB_HOST`

### Upload Errors

**Problem:** Image upload fails
**Solution:**
- Check folder permissions (755 for folders, 644 for files)
- Verify `upload_max_filesize` in PHP settings
- Check available disk space

### Blank Pages

**Problem:** Pages show blank/white screen
**Solution:**
- Enable error reporting: Add to top of PHP files:
  ```php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  ```
- Check PHP error log
- Verify all files uploaded correctly

### .htaccess Not Working

**Problem:** URLs not working / 404 errors
**Solution:**
- Verify `mod_rewrite` is enabled on server
- Check `.htaccess` uploaded to root directory
- For some servers, contact support to enable mod_rewrite

### Images Not Displaying

**Problem:** Uploaded images don't show
**Solution:**
- Check file paths are correct
- Verify images uploaded to correct folder
- Check folder permissions
- Clear browser cache

## Backup Your Website

### Database Backup (Using phpMyAdmin):
1. Open phpMyAdmin
2. Select your database
3. Click "Export" tab
4. Choose "Quick" export method
5. Click "Go"
6. Save the .sql file

### Files Backup:
1. Download entire website folder via FTP
2. Or use cPanel "Backup" feature
3. Store backups in secure location
4. Schedule regular backups (weekly recommended)

## Support

If you encounter any issues:

1. Check the error messages carefully
2. Review the Common Issues section above
3. Check PHP error logs on your server
4. Verify all installation steps completed
5. Contact your hosting support for server-specific issues

## Next Steps

After successful installation:

1. Customize the design and content
2. Add your blog posts and events
3. Set up email forwarding for contact form
4. Optimize images for web
5. Submit sitemap to search engines
6. Set up analytics (Google Analytics)
7. Regular content updates and backups

---

**Congratulations!** Your Import/Export Coaching Management website is now ready to use.

For detailed documentation, see the main [README.md](README.md) file.
