# 🚨 QUICK FIX REFERENCE CARD

## Problem: 500 Error + "Unexpected end of JSON input"

### ⚡ INSTANT DIAGNOSIS (30 seconds)

Open browser console (F12) and check:

```javascript
// ✅ GOOD - Valid JSON error
{"success":false,"message":"Error details..."}

// ❌ BAD - HTML returned (500 error)
<!DOCTYPE html>...Internal Server Error...

// ❌ BAD - Empty response (PHP crashed)
(empty)
```

---

## 🎯 3-STEP FIX

### Step 1: Check .env File (99% of production issues)
```bash
# cPanel File Manager → Check if .env exists
# If missing, create it with:

SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=nitsanghani05@gmail.com
SMTP_PASSWORD=your_app_password
SMTP_FROM_EMAIL=nitsanghani05@gmail.com
SMTP_FROM_NAME=Coaching Management
ADMIN_EMAIL=nitsanghani05@gmail.com
ADMIN_NAME=Admin
APP_NAME=Coaching Management

# Set permissions: 600 (Owner: Read+Write only)
```

### Step 2: Check vendor/ Folder
```bash
# Verify: vendor/src/PHPMailer.php exists
# If missing: Upload vendor/ folder or run composer install
```

### Step 3: Use New API Files
```bash
# Upload these files:
includes/JsonResponse.php
ajax/workshop-register-new.php
ajax/contact-new.php

# Update pages to use -new.php endpoints
```

---

## 🐛 ERROR DECODER

| Console Shows | Means | Fix |
|--------------|-------|-----|
| `<!DOCTYPE html>` | 500 error page | Missing .env or vendor/ |
| Empty response | PHP fatal error | Check error_log in cPanel |
| `JSON parse error` | Not valid JSON | Server returned HTML |
| `Status: 500` | Server error | See error_log for details |
| `Status: 404` | File not found | Check file path |
| `SMTP connect failed` | Port blocked | Use port 587 or 465 |

---

## 📱 PRODUCTION CHECKLIST (2 minutes)

```bash
✓ .env file exists (600 permissions)
✓ vendor/src/PHPMailer.php exists
✓ includes/JsonResponse.php uploaded
✓ ajax/*-new.php files uploaded
✓ Frontend uses -new.php endpoints
✓ Test: Try to access yourdomain.com/.env (should show 403)
✓ Test: Submit form and check console logs
```

---

## 🔍 DEBUGGING COMMANDS

### Test .env Access (Should be BLOCKED):
```
https://yourdomain.com/.env
Expected: 403 Forbidden ✅
```

### Test JSON Response:
```php
// Create test-json.php:
<?php
require_once 'includes/JsonResponse.php';
JsonResponse::init(true);
JsonResponse::success('Works!');

// Access: https://yourdomain.com/test-json.php
// Expected: {"success":true,"message":"Works!"}
```

### Check PHP Error Log:
```
cPanel → Metrics → Errors → error_log
Look for lines with timestamps matching your test
```

---

## 💊 QUICK FIXES

### Fix #1: Enable Debug Mode
```php
// In ajax/workshop-register-new.php:
JsonResponse::init(true);  // ← Temporarily enable for debugging
```

### Fix #2: Test MailService Separately
```php
// Create test-mail.php:
<?php
require_once 'includes/JsonResponse.php';
require_once 'includes/MailService.php';

JsonResponse::init(true);

try {
    $mail = new MailService();
    JsonResponse::success('MailService initialized!');
} catch (Exception $e) {
    JsonResponse::error('MailService failed', 500, ['error' => $e->getMessage()]);
}
```

### Fix #3: Check File Paths
```php
// Create test-files.php:
<?php
require_once 'includes/JsonResponse.php';
JsonResponse::init(true);

$files = [
    '.env' => file_exists(__DIR__ . '/.env'),
    'JsonResponse.php' => file_exists(__DIR__ . '/includes/JsonResponse.php'),
    'MailService.php' => file_exists(__DIR__ . '/includes/MailService.php'),
    'PHPMailer.php' => file_exists(__DIR__ . '/vendor/src/PHPMailer.php'),
];

JsonResponse::success('File check', $files);
```

---

## 🚀 DEPLOYMENT IN 5 MINUTES

```bash
# 1. Upload to cPanel File Manager:
   - includes/JsonResponse.php
   - ajax/workshop-register-new.php
   - ajax/contact-new.php
   - pages/workshop.php (updated)
   - pages/Contact.html (updated)

# 2. Create .env file (see Step 1 above)

# 3. Set .env permissions to 600

# 4. Upload vendor/ folder (if not exists)

# 5. Test:
   - Submit workshop form
   - Check browser console
   - Verify email received

# 6. If works, disable debug mode:
   JsonResponse::init(false);  // in both ajax files
```

---

## 📞 STILL NOT WORKING?

Copy this and send to support:

```
Browser Console Output:
[paste the [Workshop] or [Contact] logs]

PHP Error Log (last 10 lines):
[paste from cPanel → Errors]

File Check:
.env exists? [yes/no]
vendor/ exists? [yes/no]
.env permissions? [paste result of: ls -la .env]

Test URL Results:
yourdomain.com/.env shows: [403 / content / 404]
yourdomain.com/ajax/workshop-register-new.php returns: [paste result]
```

---

**Remember**: 99% of production errors = missing .env or vendor/ folder!

**Debug Mode**: Always enable temporarily, then disable after fixing.

**File Permissions**: .env must be 600, nothing else works!
