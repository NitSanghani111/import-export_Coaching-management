# Testing Checklist

This document provides a comprehensive checklist for testing your Import/Export Coaching Management website.

## Pre-Testing Setup

- [ ] Database created successfully
- [ ] database.sql imported without errors
- [ ] config/db.php configured with correct credentials
- [ ] File permissions set (755 for uploads directories)
- [ ] All files uploaded to web server
- [ ] install-test.php shows all green checkmarks

## Public Pages Testing

### Home Page (index.php)
- [ ] Page loads without errors
- [ ] Hero section displays correctly
- [ ] Features section shows all 3 feature cards
- [ ] Latest blog posts display (up to 3)
- [ ] All images load correctly
- [ ] Navigation menu works
- [ ] Footer displays with all links
- [ ] Mobile menu works on small screens
- [ ] Responsive design works on mobile/tablet
- [ ] Call-to-action buttons link correctly

### Blog Page (blog.php)
- [ ] Page loads without errors
- [ ] All published blogs display in grid
- [ ] Pagination works (if more than 9 posts)
- [ ] Blog images display correctly
- [ ] Blog titles and excerpts show
- [ ] Date and author information displays
- [ ] "Read More" links work
- [ ] Empty state shows if no blogs

### Single Blog Page (blog-single.php)
- [ ] Individual blog post loads
- [ ] Full content displays correctly
- [ ] Featured image shows (if exists)
- [ ] Date and author information displays
- [ ] "Back to Blog" link works
- [ ] Related articles show at bottom
- [ ] Line breaks preserved in content
- [ ] Invalid slug redirects to blog listing

### Events Page (events.php)
- [ ] Page loads without errors
- [ ] Upcoming events section displays
- [ ] Past events section displays (if any)
- [ ] Event images display correctly
- [ ] Event dates formatted correctly
- [ ] Event times display (if set)
- [ ] Location information shows
- [ ] Status badges display correctly
- [ ] "Register Now" buttons work
- [ ] Empty state shows if no events

### About Page (about.php)
- [ ] Page loads without errors
- [ ] All sections display correctly
- [ ] Statistics show correctly
- [ ] Feature cards display
- [ ] Icons/images load
- [ ] Content is readable
- [ ] Call-to-action button works

### Contact Page (contact.php)
- [ ] Page loads without errors
- [ ] Contact form displays
- [ ] All form fields work
- [ ] Required field validation works
- [ ] Email validation works
- [ ] Form submission works
- [ ] Success message displays after submission
- [ ] Error messages show for invalid input
- [ ] Contact information displays correctly
- [ ] Phone numbers and emails shown

## Admin Panel Testing

### Login Page (admin/login.php)
- [ ] Login page loads correctly
- [ ] Form displays properly
- [ ] Can login with default credentials (admin/admin123)
- [ ] Invalid credentials show error message
- [ ] Empty fields show validation error
- [ ] Successful login redirects to dashboard
- [ ] "Back to Website" link works
- [ ] Already logged in users redirect to dashboard

### Dashboard (admin/index.php)
- [ ] Dashboard loads after login
- [ ] Welcome message shows admin username
- [ ] Statistics cards display correctly:
  - Total blog posts count
  - Published blogs count
  - Total events count
  - Upcoming events count
- [ ] Quick actions section works:
  - Add New Blog link
  - Add New Event link
  - Manage Blogs link
  - Manage Events link
- [ ] Recent blogs list displays
- [ ] Recent events table displays
- [ ] All navigation links work
- [ ] Logout button works
- [ ] "View Site" link opens in new tab

### Blog Management (admin/blogs.php)
- [ ] Blog list page loads
- [ ] All blogs display in table
- [ ] Blog images show in list
- [ ] Title, author, status, and date display
- [ ] Pagination works (if more than 10 blogs)
- [ ] "Add New Blog" button works
- [ ] View icon opens blog in new tab
- [ ] Edit icon opens edit page
- [ ] Delete icon shows confirmation
- [ ] Delete functionality works
- [ ] Success messages display correctly
- [ ] Empty state shows if no blogs

### Add Blog (admin/blog-add.php)
- [ ] Add blog page loads
- [ ] All form fields display
- [ ] Title field works
- [ ] Content textarea works
- [ ] Author field works (defaults to "Admin")
- [ ] Status dropdown works
- [ ] Image upload field works
- [ ] File type validation works (only images)
- [ ] File size validation works (max 5MB)
- [ ] Required field validation works
- [ ] Slug auto-generates from title
- [ ] Duplicate slug handling works
- [ ] Blog saves to database
- [ ] Image uploads successfully
- [ ] Success redirect to blog list
- [ ] Cancel button returns to list

### Edit Blog (admin/blog-edit.php)
- [ ] Edit page loads with blog data
- [ ] All fields pre-filled with existing data
- [ ] Title can be updated
- [ ] Content can be updated
- [ ] Author can be updated
- [ ] Status can be changed
- [ ] Current image displays
- [ ] New image can be uploaded
- [ ] "Remove image" checkbox works
- [ ] Old image deletes when new uploaded
- [ ] Update saves to database
- [ ] Success redirect to blog list
- [ ] Cancel button returns to list

### Event Management (admin/events.php)
- [ ] Event list page loads
- [ ] All events display in table
- [ ] Event images show in list
- [ ] Title, date, location, status display
- [ ] Pagination works (if more than 10 events)
- [ ] "Add New Event" button works
- [ ] Edit icon opens edit page
- [ ] Delete icon shows confirmation
- [ ] Delete functionality works
- [ ] Success messages display correctly
- [ ] Empty state shows if no events

### Add Event (admin/event-add.php)
- [ ] Add event page loads
- [ ] All form fields display
- [ ] Title field works
- [ ] Description textarea works
- [ ] Event date picker works
- [ ] Event time picker works
- [ ] Location field works
- [ ] Status dropdown works
- [ ] Image upload field works
- [ ] File validation works
- [ ] Required field validation works
- [ ] Event saves to database
- [ ] Image uploads successfully
- [ ] Success redirect to event list
- [ ] Cancel button returns to list

### Edit Event (admin/event-edit.php)
- [ ] Edit page loads with event data
- [ ] All fields pre-filled with existing data
- [ ] All fields can be updated
- [ ] Current image displays
- [ ] New image can be uploaded
- [ ] "Remove image" checkbox works
- [ ] Update saves to database
- [ ] Success redirect to event list
- [ ] Cancel button returns to list

### Logout (admin/logout.php)
- [ ] Logout link works from any admin page
- [ ] Session destroyed successfully
- [ ] Redirects to login page
- [ ] Cannot access admin pages after logout

## Security Testing

### Authentication & Authorization
- [ ] Cannot access admin pages without login
- [ ] Direct URL access to admin pages redirects to login
- [ ] Session timeout works appropriately
- [ ] Password is hashed in database (not plain text)
- [ ] XSS attempts are sanitized
- [ ] SQL injection attempts are prevented

### File Upload Security
- [ ] Only image files can be uploaded
- [ ] File size limit enforced (5MB)
- [ ] File names sanitized (special characters removed)
- [ ] Uploaded files stored in correct directory
- [ ] Cannot upload PHP or executable files
- [ ] Uploads directory not directly browsable

### Data Validation
- [ ] Email validation works on contact form
- [ ] Required fields cannot be empty
- [ ] Special characters handled correctly
- [ ] HTML tags sanitized in output
- [ ] Dates validated correctly

## Performance Testing

- [ ] Pages load in reasonable time (< 3 seconds)
- [ ] Images optimized for web
- [ ] No JavaScript errors in console
- [ ] No PHP errors/warnings displayed
- [ ] Database queries optimized

## Cross-Browser Testing

Test on multiple browsers:
- [ ] Google Chrome (latest)
- [ ] Mozilla Firefox (latest)
- [ ] Safari (latest)
- [ ] Microsoft Edge (latest)
- [ ] Mobile Safari (iOS)
- [ ] Chrome Mobile (Android)

## Responsive Design Testing

Test on different screen sizes:
- [ ] Desktop (1920x1080)
- [ ] Laptop (1366x768)
- [ ] Tablet landscape (1024x768)
- [ ] Tablet portrait (768x1024)
- [ ] Mobile landscape (667x375)
- [ ] Mobile portrait (375x667)

### Responsive Elements to Check:
- [ ] Navigation collapses to hamburger menu
- [ ] Mobile menu opens/closes correctly
- [ ] Text is readable on all devices
- [ ] Images scale appropriately
- [ ] Buttons and links are tappable
- [ ] Forms work on mobile
- [ ] Tables scroll horizontally on mobile
- [ ] Footer layout adjusts
- [ ] Grid layouts stack on mobile

## Accessibility Testing

- [ ] Images have alt text
- [ ] Form labels associated with inputs
- [ ] Keyboard navigation works
- [ ] Color contrast sufficient
- [ ] Focus indicators visible
- [ ] Screen reader compatible

## SEO Testing

- [ ] Page titles set correctly
- [ ] robots.txt file present
- [ ] .htaccess configured
- [ ] Meta descriptions added (recommended)
- [ ] Images have descriptive filenames
- [ ] URLs are clean and descriptive

## Final Checks

- [ ] All sample/test data works correctly
- [ ] install-test.php deleted after testing
- [ ] Default admin password changed
- [ ] Contact form emails configured (if needed)
- [ ] Error pages created (404, 500) - optional
- [ ] SSL certificate installed
- [ ] HTTPS redirect enabled
- [ ] Database backups configured
- [ ] File backups configured
- [ ] Analytics code added (if needed)
- [ ] Favicon added (if needed)

## Deployment Checklist

- [ ] All files uploaded to production
- [ ] Database imported on production
- [ ] Database credentials updated for production
- [ ] File permissions set correctly
- [ ] .htaccess working correctly
- [ ] Error reporting disabled in production
- [ ] All links work on production domain
- [ ] Forms submit correctly on production
- [ ] Admin panel accessible on production
- [ ] Images upload correctly on production

## Post-Launch Testing

After going live:
- [ ] Test from different locations/networks
- [ ] Verify contact form submissions received
- [ ] Check all external links work
- [ ] Monitor error logs for issues
- [ ] Test on various devices
- [ ] Check page load speeds
- [ ] Verify SSL certificate valid

## Issue Tracking

Use this section to note any issues found:

| Issue | Page/Feature | Severity | Status | Notes |
|-------|-------------|----------|--------|-------|
| Example | Blog page | High | Fixed | Pagination wasn't working |
|       |             |          |        |       |
|       |             |          |        |       |

---

**Notes:**
- Mark items with [x] as you complete testing
- Document any issues immediately
- Re-test after fixing issues
- Keep this checklist for future updates

**Testing completed by:** _________________

**Date:** _________________

**Sign-off:** _________________
