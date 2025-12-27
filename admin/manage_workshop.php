<?php
// Buffer output to allow redirects
ob_start();
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}
require_once __DIR__ . '/../db.php';

// Flash message helper
$flash_success = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);

// Ensure server-side timezone matches your expected locale
// Change to your timezone if needed
date_default_timezone_set('Asia/Kolkata');

// Normalize various time formats to 24h H:i:s
function normalize_time_str($t) {
  $t = trim((string)$t);
  if ($t === '') return '';
  // Replace dot with colon, collapse spaces
  $t = preg_replace('/\s+/', ' ', str_replace('.', ':', $t));
  // Handle compact numeric like 530 -> 5:30
  if (preg_match('/^\d{3,4}$/', $t)) {
    // split last 2 digits as minutes
    $len = strlen($t);
    $h = substr($t, 0, $len - 2);
    $m = substr($t, -2);
    $t = $h . ':' . $m;
  }
  // AM/PM handling
  if (preg_match('/\b(am|pm)\b/i', $t)) {
    $fmtVariants = ['g:i A', 'g:i:s A', 'h:i A', 'h:i:s A', 'g:iA', 'h:iA'];
    foreach ($fmtVariants as $fmt) {
      $dt = DateTime::createFromFormat($fmt, strtoupper($t));
      if ($dt) return $dt->format('H:i:s');
    }
  }
  // If matches H:i or H:i:s, ensure seconds
  if (preg_match('/^(\d{1,2}):(\d{2})(:(\d{2}))?$/', $t, $m)) {
    $h = (int)$m[1]; $i = (int)$m[2]; $s = isset($m[4]) ? (int)$m[4] : 0;
    return sprintf('%02d:%02d:%02d', $h, $i, $s);
  }
  // Fallback: let strtotime try
  $ts = strtotime($t);
  return $ts ? date('H:i:s', $ts) : '';
}

function combine_date_time_to_ts($dateStr, $timeStr) {
  $dateStr = trim((string)$dateStr);
  if ($dateStr === '') return 0;
  $timeNorm = normalize_time_str($timeStr);
  $combo = trim($dateStr . ' ' . $timeNorm);
  // Prefer strict parsing
  $dt = DateTime::createFromFormat('Y-m-d H:i:s', $combo);
  if ($dt) return $dt->getTimestamp();
  $ts = strtotime($combo);
  return $ts ?: 0;
}

// Detect available columns in workshops to adapt to schema
$workshopCols = [];
$colsRes = mysqli_query($conn, "SHOW COLUMNS FROM workshops");
if ($colsRes) {
  while ($c = mysqli_fetch_assoc($colsRes)) { $workshopCols[] = $c['Field']; }
}

$hasStartDatetime = in_array('start_datetime', $workshopCols, true);
$hasScheduledAt   = in_array('scheduled_at', $workshopCols, true);
$hasDatetime      = in_array('datetime', $workshopCols, true) || in_array('date_time', $workshopCols, true);
$datetimeField    = $hasStartDatetime ? 'start_datetime' : ($hasScheduledAt ? 'scheduled_at' : (in_array('datetime', $workshopCols, true) ? 'datetime' : (in_array('date_time', $workshopCols, true) ? 'date_time' : '')));
$hasDate          = in_array('workshop_date', $workshopCols, true) || in_array('date', $workshopCols, true) || in_array('start_date', $workshopCols, true);
$hasTime          = in_array('workshop_time', $workshopCols, true) || in_array('time', $workshopCols, true) || in_array('start_time', $workshopCols, true);
$dateField        = in_array('workshop_date', $workshopCols, true) ? 'workshop_date' : (in_array('date', $workshopCols, true) ? 'date' : (in_array('start_date', $workshopCols, true) ? 'start_date' : ''));
$timeField        = in_array('workshop_time', $workshopCols, true) ? 'workshop_time' : (in_array('time', $workshopCols, true) ? 'time' : (in_array('start_time', $workshopCols, true) ? 'start_time' : ''));
$descField        = in_array('short_description', $workshopCols, true) ? 'short_description' : (in_array('description', $workshopCols, true) ? 'description' : '');
$thumbField       = in_array('thumbnail', $workshopCols, true) ? 'thumbnail' : (in_array('image', $workshopCols, true) ? 'image' : '');
$hasCreatedAt     = in_array('created_at', $workshopCols, true);

// workshops_img directory
$uploadDir = __DIR__ . '/workshops_img';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0777, true);
}

// Helper to sanitize output
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Handle actions: create, update, delete
$error = '';
$success = $flash_success;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $start = trim($_POST['start_datetime'] ?? '');

        if ($title === '' || $start === '') {
            $error = 'Title and Start Date/Time are required.';
        } else {
            // Handle image upload
            $imageName = '';
            if (!empty($_FILES['thumbnail']['name'])) {
                $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
                $safeBase = preg_replace('/[^a-zA-Z0-9-_]/', '_', pathinfo($_FILES['thumbnail']['name'], PATHINFO_FILENAME));
                $imageName = $safeBase . '_' . time() . '.' . strtolower($ext);
                $target = $uploadDir . '/' . $imageName;
                if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target)) {
                    $error = 'Failed to upload image.';
                }
            }

            if ($error === '') {
              // Build INSERT with actual schema fields
              $columns = [];
              $placeholders = [];
              $bindTypes = '';
              $bindValues = [];

              // title
              $columns[] = 'title'; $placeholders[] = '?'; $bindTypes .= 's'; $bindValues[] = $title;

              // description field
              if ($descField !== '') { $columns[] = $descField; $placeholders[] = '?'; $bindTypes .= 's'; $bindValues[] = $description; }

              // thumbnail/image field
              if ($thumbField !== '') { $columns[] = $thumbField; $placeholders[] = '?'; $bindTypes .= 's'; $bindValues[] = $imageName; }

              // date/time or single datetime
              if ($datetimeField !== '') {
                $columns[] = $datetimeField; $placeholders[] = '?'; $bindTypes .= 's'; $bindValues[] = $start;
              } elseif ($dateField !== '' && $timeField !== '') {
                $dateVal = ''; $timeVal = '';
                $ts = strtotime($start);
                if ($ts) { $dateVal = date('Y-m-d', $ts); $timeVal = date('H:i:s', $ts); }
                $columns[] = $dateField; $placeholders[] = '?'; $bindTypes .= 's'; $bindValues[] = $dateVal;
                $columns[] = $timeField; $placeholders[] = '?'; $bindTypes .= 's'; $bindValues[] = $timeVal;
              }

              if ($hasCreatedAt) { $columns[] = 'created_at'; $placeholders[] = 'NOW()'; }

              $sql = 'INSERT INTO workshops (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';
              $stmt = mysqli_prepare($conn, $sql);
              if ($stmt) {
                if ($bindTypes !== '') {
                  mysqli_stmt_bind_param($stmt, $bindTypes, ...$bindValues);
                }
                if (mysqli_stmt_execute($stmt)) {
                  // PRG: avoid duplicate create on refresh
                  $_SESSION['flash_success'] = 'Workshop created successfully!';
                  header('Location: manage_workshop.php');
                  ob_end_flush();
                  exit;
                } else {
                  $error = 'Failed to create workshop.';
                }
                mysqli_stmt_close($stmt);
              } else { $error = 'Failed to prepare create statement.'; }
            }
        }
    }

    if ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $start = trim($_POST['start_datetime'] ?? '');
        $currentImage = trim($_POST['current_image'] ?? '');

        if ($id <= 0 || $title === '' || $start === '') {
            $error = 'Invalid workshop data.';
        } else {
            $imageName = $currentImage;
            if (!empty($_FILES['thumbnail']['name'])) {
                $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
                $safeBase = preg_replace('/[^a-zA-Z0-9-_]/', '_', pathinfo($_FILES['thumbnail']['name'], PATHINFO_FILENAME));
                $imageName = $safeBase . '_' . time() . '.' . strtolower($ext);
                $target = $uploadDir . '/' . $imageName;
                if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target)) {
                    $error = 'Failed to upload image.';
                } else {
                    // remove old image
                    if (!empty($currentImage)) {
                        $oldPath = $uploadDir . '/' . $currentImage;
                        if (is_file($oldPath)) @unlink($oldPath);
                    }
                }
            }

            if ($error === '') {
              // Build UPDATE with actual schema
              $setParts = [];
              $bindTypes = '';
              $bindValues = [];

              $setParts[] = 'title = ?'; $bindTypes .= 's'; $bindValues[] = $title;
              if ($descField !== '') { $setParts[] = $descField . ' = ?'; $bindTypes .= 's'; $bindValues[] = $description; }
              if ($thumbField !== '') { $setParts[] = $thumbField . ' = ?'; $bindTypes .= 's'; $bindValues[] = $imageName; }

              if ($datetimeField !== '') { $setParts[] = $datetimeField . ' = ?'; $bindTypes .= 's'; $bindValues[] = $start; }
              elseif ($dateField !== '' && $timeField !== '') {
                $dateVal = ''; $timeVal = '';
                $ts = strtotime($start);
                if ($ts) { $dateVal = date('Y-m-d', $ts); $timeVal = date('H:i:s', $ts); }
                $setParts[] = $dateField . ' = ?'; $bindTypes .= 's'; $bindValues[] = $dateVal;
                $setParts[] = $timeField . ' = ?'; $bindTypes .= 's'; $bindValues[] = $timeVal;
              }

              if (in_array('updated_at', $workshopCols, true)) { $setParts[] = 'updated_at = NOW()'; }

              $sql = 'UPDATE workshops SET ' . implode(', ', $setParts) . ' WHERE id = ?';
              $bindTypes .= 'i'; $bindValues[] = $id;
              $stmt = mysqli_prepare($conn, $sql);
              if ($stmt) {
                mysqli_stmt_bind_param($stmt, $bindTypes, ...$bindValues);
                if (mysqli_stmt_execute($stmt)) {
                  $_SESSION['flash_success'] = 'Workshop updated successfully!';
                  header('Location: manage_workshop.php');
                  ob_end_flush();
                  exit;
                } else { $error = 'Failed to update workshop.'; }
                mysqli_stmt_close($stmt);
              } else { $error = 'Failed to prepare update statement.'; }
            }
        }
    }

    if ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) {
            $error = 'Invalid workshop.';
        } else {
            // get thumbnail to delete
            $selectCol = $thumbField !== '' ? $thumbField : 'thumbnail';
            $res = mysqli_query($conn, "SELECT `".$selectCol."` AS thumb FROM workshops WHERE id=" . $id);
            $row = $res ? mysqli_fetch_assoc($res) : null;
            $img = $row && !empty($row['thumb']) ? $row['thumb'] : '';

            if (mysqli_query($conn, "DELETE FROM workshops WHERE id=" . $id)) {
                if (!empty($img)) {
                    $p = $uploadDir . '/' . $img;
                    if (is_file($p)) @unlink($p);
                }
              $_SESSION['flash_success'] = 'Workshop deleted successfully!';
              header('Location: manage_workshop.php');
              ob_end_flush();
              exit;
            } else {
                $error = 'Failed to delete workshop.';
            }
        }
    }
}

// Fetch all workshops (avoid ORDER BY on unknown columns)
$workshops = [];
$q = mysqli_query($conn, "SELECT * FROM workshops ORDER BY id DESC");
if ($q) { while ($r = mysqli_fetch_assoc($q)) { $workshops[] = $r; } }

// Compute start timestamp for sorting and grouping
foreach ($workshops as &$w) {
  $ts = 0;
  if ($datetimeField !== '' && !empty($w[$datetimeField] ?? '')) {
    $ts = strtotime($w[$datetimeField]);
  } elseif ($dateField !== '') {
    $d = $w[$dateField] ?? '';
    $t = $timeField !== '' ? ($w[$timeField] ?? '') : '';
    $ts = combine_date_time_to_ts($d, $t);
  }
  if (($ts <= 0 || $ts === false) && $hasCreatedAt && !empty($w['created_at'])) {
    $ts = strtotime($w['created_at']);
  }
  $w['_start_ts'] = $ts && $ts > 0 ? $ts : 0;
}
unset($w);

// Sort by computed start desc
usort($workshops, function($a, $b) { return ($b['_start_ts'] <=> $a['_start_ts']); });

// Split into upcoming vs hosted
$nowTs = time();
$upcoming = []; $hosted = [];
foreach ($workshops as $w) { (($w['_start_ts'] ?: 0) >= $nowTs) ? $upcoming[] = $w : $hosted[] = $w; }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noindex, nofollow">
    <title>Manage Workshop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Sora:wght@400;600;700&display=swap');
      * { font-family: 'Poppins', sans-serif; }
      .font-sora { font-family: 'Sora', sans-serif; }
      body { background: linear-gradient(135deg, #0f0f1e 0%, #1a1a2e 50%, #0f0f1e 100%); background-attachment: fixed; }
      .gradient-accent { background: linear-gradient(135deg, #FFC261 0%, #FFB347 100%); }
      .card-hover { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
      .card-hover:hover { transform: translateY(-4px); box-shadow: 0 15px 40px rgba(255, 194, 97, 0.1); }
      .admin-form {
        display:grid;gap:12px;grid-template-columns:1fr 1fr;
        border:1px solid rgba(255,255,255,0.15);padding:16px;border-radius:12px;background:#0d0d0d;
      }
      .admin-form label { font-size:.9rem;color:#bbb }
      .admin-form input[type="text"], .admin-form input[type="datetime-local"], .admin-form textarea {
        width:100%;padding:.55rem .7rem;border-radius:8px;border:1px solid rgba(255,255,255,0.15);background:#121212;color:#fff
      }
      .admin-form textarea { grid-column:1 / span 2; min-height:120px }
      .admin-actions { display:flex;gap:8px;flex-wrap:wrap }
      .gold-border { border:1px solid #FFBE49;border-radius:12px;background:#0c0c0c }
      .table { width:100%;border-collapse:collapse;margin-top:12px }
      .table th, .table td { padding:10px;border-bottom:1px solid rgba(255,255,255,0.12);text-align:left }
      .badge { display:inline-block;padding:.2rem .5rem;border-radius:999px;font-size:.75rem }
      .badge-upcoming { border:1px solid #FFBE49;color:#FFBE49 }
      .badge-hosted { border:1px solid #888;color:#bbb }
      .thumb { width:90px;height:64px;object-fit:cover;border-radius:8px;border:1px solid rgba(255,255,255,0.15) }
      .btn { padding:.45rem .8rem;border-radius:8px;border:1px solid rgba(255,255,255,0.2);background:#141414;color:#fff;text-decoration:none;display:inline-block }
      .btn-primary { border-color:#FFBE49;color:#FFBE49 }
      .btn-danger { border-color:#b33;color:#fff;background:#2a0000 }
    </style>
  </head>
  <body class="text-white antialiased">
    <div class="flex h-screen overflow-hidden">
      <!-- SIDEBAR (same theme as manage_blog) -->
      <aside class="w-72 bg-[#0a0a14] border-r border-white/10 flex flex-col hidden lg:flex fixed h-screen left-0 top-0 overflow-y-auto">
        <!-- Logo Section -->
        <div class="p-8 border-b border-white/10 sticky top-0 bg-[#0a0a14]">
          <div class="flex items-center gap-4">
            <div class="gradient-accent w-12 h-12 rounded-xl flex items-center justify-center font-bold text-lg shadow-lg">PC</div>
            <div>
              <p class="text-white font-bold text-sm">Parth Coaching</p>
              <p class="text-gray-400 text-xs">Admin Panel</p>
            </div>
          </div>
        </div>
        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-2">
          <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9" /></svg>
            <span class="font-medium">Dashboard</span>
          </a>
          <a href="manage_blog.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2v-5.5a2 2 0 012-2H19" /></svg>
            <span class="font-medium">Blog Posts</span>
          </a>
          <a href="workshop.php" class="flex items-center gap-3 px-4 py-3 rounded-lg transition sidebar-active" style="background: rgba(255, 194, 97, 0.15); border-left: 3px solid #FFC261;">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
            <span class="font-medium">Workshops</span>
          </a>
        </nav>
        <!-- Logout -->
        <div class="border-t border-white/10 p-4 sticky bottom-0 bg-[#0a0a14]">
          <a href="logout.php" class="gradient-accent text-black w-full py-2.5 rounded-lg font-semibold text-sm transition hover:shadow-lg flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
            Logout
          </a>
        </div>
      </aside>

      <!-- MAIN CONTENT -->
      <main class="flex-1 lg:ml-72 flex flex-col h-screen overflow-auto">
        <!-- TOP BAR -->
        <header class="sticky top-0 z-40 bg-[#0a0a14]/80 backdrop-blur-md border-b border-white/10 px-6 py-4">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold font-sora">Manage Workshops</h1>
              <p class="text-sm text-gray-400 mt-1">Create, update, and organize workshops</p>
            </div>
            <a href="logout.php" class="gradient-accent text-black px-4 py-2 rounded-lg font-semibold text-sm">Logout</a>
          </div>
        </header>

        <!-- CONTENT AREA -->
        <section class="flex-1 p-6 md:p-8 space-y-8">
          <?php if (!empty($error)): ?>
            <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-4 flex items-center gap-3">
              <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
              <span class="text-red-400"><?= h($error) ?></span>
            </div>
          <?php endif; ?>
          <?php if (!empty($success)): ?>
            <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-4 flex items-center gap-3">
              <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
              <span class="text-green-400"><?= h($success) ?></span>
            </div>
          <?php endif; ?>

          <!-- Create / Edit Form -->
          <?php
            $editId = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
            $editRow = null;
            if ($editId > 0) {
                $qr = mysqli_query($conn, "SELECT * FROM workshops WHERE id=" . $editId);
                $editRow = $qr ? mysqli_fetch_assoc($qr) : null;
            }
          ?>
          <div class="border border-white/10 rounded-2xl p-6 bg-[#0a0a14]/60">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-xl font-semibold font-sora"><?= $editRow ? 'Edit Workshop' : 'Add New Workshop' ?></h2>
              <?php if ($editRow): ?><a href="manage_workshop.php" class="px-3 py-2 rounded-lg border border-white/20 text-white/80 hover:text-white">Cancel Edit</a><?php endif; ?>
            </div>
            <form class="grid gap-4 md:grid-cols-2" action="manage_workshop.php<?= $editRow ? '?edit=' . intval($editRow['id']) : '' ?>" method="post" enctype="multipart/form-data">
              <input type="hidden" name="action" value="<?= $editRow ? 'update' : 'create' ?>">
              <?php if ($editRow): ?><input type="hidden" name="id" value="<?= intval($editRow['id']) ?>"><?php endif; ?>

              <div>
                <label class="text-gray-300 text-sm">Title</label>
                <input class="w-full mt-1 px-3 py-2 rounded-lg border border-white/10 bg-white/5 text-white" type="text" name="title" placeholder="Workshop title" value="<?= h($editRow['title'] ?? '') ?>" required />
              </div>
              <div>
                <label class="text-gray-300 text-sm">Date & Time</label>
                <?php
                  $dtVal = '';
                    if (!empty($editRow['start_datetime'])) {
                      $dt = new DateTime($editRow['start_datetime']);
                      $dtVal = $dt->format('Y-m-d\\TH:i');
                    } elseif (!empty($dateField)) {
                      $d = $editRow[$dateField] ?? '';
                      $t = $timeField ? ($editRow[$timeField] ?? '') : '';
                      if ($d) {
                        $dtVal = date('Y-m-d\\TH:i', strtotime(trim($d . ' ' . $t)));
                      }
                    }
                ?>
                <input class="w-full mt-1 px-3 py-2 rounded-lg border border-white/10 bg-white/5 text-white" type="datetime-local" name="start_datetime" value="<?= h($dtVal) ?>" required />
              </div>
              <div class="md:col-span-2">
                <label class="text-gray-300 text-sm">Description</label>
                <textarea class="w-full mt-1 px-3 py-2 rounded-lg border border-white/10 bg-white/5 text-white min-h-[140px]" name="description" placeholder="Description"><?= h($editRow['description'] ?? '') ?></textarea>
              </div>

              <div>
                <label class="text-gray-300 text-sm">Thumbnail (image)</label>
                <input class="w-full mt-1" type="file" name="thumbnail" accept="image/*" />
                <?php if ($editRow && !empty($editRow['image'])): ?>
                  <div class="mt-2"><img src="workshops_img/<?= h($editRow['image']) ?>" class="w-28 h-20 object-cover rounded-lg border border-white/10" alt="thumb" /></div>
                  <input type="hidden" name="current_image" value="<?= h($editRow['image']) ?>">
                <?php else: ?>
                  <input type="hidden" name="current_image" value="">
                <?php endif; ?>
              </div>

              <div class="md:col-span-2 flex gap-3">
                <button class="gradient-accent text-black px-4 py-2 rounded-lg font-semibold text-sm" type="submit"><?= $editRow ? 'Update' : 'Create' ?></button>
              </div>
            </form>
          </div>

          <!-- Upcoming Workshops -->
          <div class="border border-white/10 rounded-2xl p-6 bg-[#0a0a14]/60">
            <div class="text-xl font-semibold font-sora mb-4">Upcoming Workshops</div>
            <table class="table">
              <thead>
                <tr>
                  <th class="text-left">Thumb</th>
                  <th class="text-left">Title</th>
                  <th class="text-left">Start</th>
                  <th class="text-left">Status</th>
                  <th class="text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($upcoming)): ?>
                  <tr><td colspan="5" class="text-gray-400">No upcoming workshops</td></tr>
                <?php else: foreach ($upcoming as $w): ?>
                  <tr>
                    <td><?php if ($thumbField !== '' && !empty($w[$thumbField])): ?><img class="w-24 h-16 object-cover rounded-lg border border-white/10" src="workshops_img/<?= h($w[$thumbField]) ?>" alt="thumb"><?php endif; ?></td>
                    <td><?= h($w['title']) ?></td>
                    <td><?php $dispTs = $w['_start_ts'] ?? 0; echo $dispTs ? h(date('M d, Y h:i A', $dispTs)) : '-'; ?></td>
                    <td><span class="inline-block px-2 py-1 rounded-full border border-yellow-500/50 text-yellow-400 text-xs">Upcoming</span></td>
                    <td class="text-right">
                      <a class="px-3 py-2 rounded-lg border border-white/20 text-white/80 hover:text-white" href="manage_workshop.php?edit=<?= intval($w['id']) ?>">Edit</a>
                      <form action="manage_workshop.php" method="post" style="display:inline">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= intval($w['id']) ?>">
                        <button class="ml-2 px-3 py-2 rounded-lg border border-red-500/50 bg-red-900/40 text-white" type="submit" onclick="return confirm('Delete this workshop?')">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>

          <!-- Hosted Workshops -->
          <div class="border border-white/10 rounded-2xl p-6 bg-[#0a0a14]/60">
            <div class="text-xl font-semibold font-sora mb-4">Hosted Workshops</div>
            <table class="table">
              <thead>
                <tr>
                  <th class="text-left">Thumb</th>
                  <th class="text-left">Title</th>
                  <th class="text-left">Start</th>
                  <th class="text-left">Status</th>
                  <th class="text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($hosted)): ?>
                  <tr><td colspan="5" class="text-gray-400">No hosted workshops</td></tr>
                <?php else: foreach ($hosted as $w): ?>
                  <tr>
                    <td><?php if ($thumbField !== '' && !empty($w[$thumbField])): ?><img class="w-24 h-16 object-cover rounded-lg border border-white/10" src="workshops_img/<?= h($w[$thumbField]) ?>" alt="thumb"><?php endif; ?></td>
                    <td><?= h($w['title']) ?></td>
                    <td><?php $dispTs = $w['_start_ts'] ?? 0; echo $dispTs ? h(date('M d, Y h:i A', $dispTs)) : '-'; ?></td>
                    <td><span class="inline-block px-2 py-1 rounded-full border border-white/20 text-white/70 text-xs">Hosted</span></td>
                    <td class="text-right">
                      <a class="px-3 py-2 rounded-lg border border-white/20 text-white/80 hover:text-white" href="manage_workshop.php?edit=<?= intval($w['id']) ?>">Edit</a>
                      <form action="manage_workshop.php" method="post" style="display:inline">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= intval($w['id']) ?>">
                        <button class="ml-2 px-3 py-2 rounded-lg border border-red-500/50 bg-red-900/40 text-white" type="submit" onclick="return confirm('Delete this workshop?')">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </section>
      </main>
    </div>
  </body>
</html>
