# Import/Export Coaching Management Website

A dynamic, modern website for managing and marketing import/export coaching services. Built with PHP, MySQL, HTML, Tailwind CSS, and JavaScript using procedural PHP with mysqli.

## Features

### Public Pages
- **Home** - Hero section with latest blog posts and call-to-action
- **Blog** - Blog listing with pagination and individual blog posts
- **Events** - Upcoming and past events display
- **About** - Static page about the coaching services
- **Contact** - Contact form for inquiries

### Admin Panel
- **Secure Login** - Session-based authentication
- **Dashboard** - Overview with statistics and quick actions
- **Blog Management** - Full CRUD operations with image upload
- **Event Management** - Full CRUD operations with image upload
- **Responsive Design** - Dark modern UI with Tailwind CSS

### Security Features
- XSS protection through input sanitization
- SQL injection prevention with mysqli_real_escape_string
- CSRF token protection
- Secure password hashing with password_hash
- File upload validation
- Session-based authentication

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher / MariaDB 10.2 or higher
- Apache web server with mod_rewrite enabled
- 5MB+ upload file size limit

## Installation

### 1. Clone or Download

Clone this repository or download and extract the ZIP file to your web server's document root.

```bash
git clone https://github.com/NitSanghani111/import-export_Coaching-management-.git
cd import-export_Coaching-management-
```

### 2. Database Setup

1. Create a new MySQL database named `coaching_management`
2. Import the database schema:

```bash
mysql -u your_username -p coaching_management < database.sql
```

Or use phpMyAdmin:
- Open phpMyAdmin
- Create a new database named `coaching_management`
- Import the `database.sql` file

### 3. Configure Database Connection

Edit `config/db.php` and update with your database credentials:

```php
define('DB_HOST', 'localhost');      // Your database host
define('DB_USER', 'your_username');   // Your database username
define('DB_PASS', 'your_password');   // Your database password
define('DB_NAME', 'coaching_management'); // Your database name
```

### 4. Set Permissions

Ensure the uploads directories are writable:

```bash
chmod 755 uploads/
chmod 755 uploads/blogs/
chmod 755 uploads/events/
```

### 5. Access the Website

- **Public Website**: `http://yourdomain.com/`
- **Admin Login**: `http://yourdomain.com/admin/login.php`

**Default Admin Credentials:**
- Username: `admin`
- Password: `admin123`

**⚠️ IMPORTANT:** Change the default password immediately after first login!

## Folder Structure

```
├── admin/                  # Admin panel files
│   ├── header.php         # Admin header
│   ├── footer.php         # Admin footer
│   ├── index.php          # Admin dashboard
│   ├── login.php          # Admin login
│   ├── logout.php         # Admin logout
│   ├── blogs.php          # Blog management
│   ├── blog-add.php       # Add new blog
│   ├── blog-edit.php      # Edit blog
│   ├── events.php         # Event management
│   ├── event-add.php      # Add new event
│   └── event-edit.php     # Edit event
├── assets/                # Static assets
│   ├── css/              # Custom CSS files
│   ├── js/               # Custom JavaScript files
│   └── images/           # Site images
├── config/               # Configuration files
│   └── db.php           # Database configuration
├── includes/            # Reusable includes
│   ├── header.php      # Public header
│   ├── footer.php      # Public footer
│   └── functions.php   # Helper functions
├── uploads/            # Uploaded files
│   ├── blogs/         # Blog images
│   └── events/        # Event images
├── index.php          # Home page
├── blog.php           # Blog listing
├── blog-single.php    # Single blog post
├── events.php         # Events page
├── about.php          # About page
├── contact.php        # Contact page
├── database.sql       # Database schema
├── .htaccess         # Apache configuration
└── .gitignore        # Git ignore rules
```

## Usage

### Managing Blogs

1. Login to admin panel
2. Navigate to "Blogs" in the menu
3. Click "Add New Blog" to create a post
4. Fill in title, content, and optionally upload an image
5. Choose status (Draft or Published)
6. Click "Publish Blog Post"

### Managing Events

1. Login to admin panel
2. Navigate to "Events" in the menu
3. Click "Add New Event"
4. Fill in event details (title, description, date, time, location)
5. Upload an optional event image
6. Set status (Upcoming, Completed, or Cancelled)
7. Click "Create Event"

### Customization

#### Changing Site Colors

The site uses Tailwind CSS with a custom dark theme. To customize colors, edit the Tailwind config in `includes/header.php`:

```javascript
tailwind.config = {
    theme: {
        extend: {
            colors: {
                dark: {
                    bg: '#0a0e17',      // Main background
                    card: '#141824',     // Card background
                    border: '#1f2937',   // Border color
                }
            }
        }
    }
}
```

#### Updating Contact Information

Edit the footer in `includes/footer.php` and contact page in `contact.php` to update your contact information.

## Deployment to Shared Hosting (Hostinger)

1. **Upload Files**
   - Use FTP/SFTP or Hostinger's File Manager
   - Upload all files to `public_html` directory

2. **Create Database**
   - Go to Hostinger control panel
   - Create a new MySQL database
   - Note the database name, username, and password

3. **Import Database**
   - Access phpMyAdmin from Hostinger panel
   - Import the `database.sql` file

4. **Configure Database**
   - Edit `config/db.php` with your database credentials

5. **Set Permissions**
   - Ensure `uploads/blogs/` and `uploads/events/` are writable (755)

6. **Test**
   - Visit your domain
   - Login to admin panel
   - Verify all features work correctly

## Security Recommendations

1. **Change Default Password**
   - Login with default credentials
   - Update admin password immediately

2. **Secure Database Credentials**
   - Keep `config/db.php` secure
   - Never commit real credentials to version control

3. **Enable HTTPS**
   - Get an SSL certificate (free with Let's Encrypt)
   - Uncomment HTTPS redirect in `.htaccess`

4. **Regular Backups**
   - Backup database regularly
   - Keep backups of uploaded files

5. **Update PHP**
   - Keep PHP version up to date
   - Monitor for security updates

## Browser Compatibility

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Support

For issues or questions:
- Open an issue on GitHub
- Contact: info@iecoaching.com

## License

This project is proprietary software. All rights reserved.

## Credits

Developed for Import/Export Coaching Management
Built with PHP, MySQL, Tailwind CSS, and ❤️
