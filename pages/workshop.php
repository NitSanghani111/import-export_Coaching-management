<?php
require_once __DIR__ . '/../db.php';
date_default_timezone_set('Asia/Kolkata');

// Detect available columns in workshops table
$workshopCols = [];
$colsRes = mysqli_query($conn, "SHOW COLUMNS FROM workshops");
if ($colsRes) {
    while ($c = mysqli_fetch_assoc($colsRes)) { 
        $workshopCols[] = $c['Field']; 
    }
}

// Determine which datetime column to use
$hasStartDatetime = in_array('start_datetime', $workshopCols, true);
$hasScheduledAt   = in_array('scheduled_at', $workshopCols, true);
$hasDatetime      = in_array('datetime', $workshopCols, true);
$datetimeField    = $hasStartDatetime ? 'start_datetime' : ($hasScheduledAt ? 'scheduled_at' : ($hasDatetime ? 'datetime' : ''));

// Also detect separate date/time columns (fallback)
$dateField = in_array('workshop_date', $workshopCols, true) ? 'workshop_date' : (in_array('date', $workshopCols, true) ? 'date' : (in_array('start_date', $workshopCols, true) ? 'start_date' : ''));
$timeField = in_array('workshop_time', $workshopCols, true) ? 'workshop_time' : (in_array('time', $workshopCols, true) ? 'time' : (in_array('start_time', $workshopCols, true) ? 'start_time' : ''));

// Determine which description column to use
$descField = in_array('short_description', $workshopCols, true) ? 'short_description' : (in_array('description', $workshopCols, true) ? 'description' : '');

// Determine which image column to use
$thumbField = in_array('thumbnail', $workshopCols, true) ? 'thumbnail' : (in_array('image', $workshopCols, true) ? 'image' : '');

// build datetime select: use native datetime column if present, otherwise create alias `start_dt` from date+time
$selectCols = ['id', 'title'];
if ($datetimeField) {
  $selectCols[] = $datetimeField;
  $datetimeSelect = $datetimeField; // column name (use in SQL expressions)
  $datetimeAlias = $datetimeField; // name present in result rows
  $datetimeExpr = $datetimeField; // expression to use in WHERE/ORDER
} elseif ($dateField) {
  // create an aliased datetime string from date + optional time
  if ($timeField) {
    $expr = "CONCAT_WS(' ', $dateField, IFNULL($timeField, '00:00:00'))";
    $selectCols[] = $expr . " AS start_dt";
  } else {
    $expr = "CONCAT($dateField, ' 00:00:00')";
    $selectCols[] = $expr . " AS start_dt";
  }
  $datetimeSelect = 'start_dt';
  $datetimeAlias = 'start_dt';
  $datetimeExpr = $expr; // use full expression in WHERE/ORDER (alias not available there)
} else {
  $datetimeSelect = '';
  $datetimeAlias = '';
  $datetimeExpr = '';
}

$selectCols[] = $descField ?: null;
$selectCols[] = $thumbField ?: null;
$selectCols = array_values(array_filter($selectCols, function($v){ return $v !== null && $v !== ''; }));

$selectStr = implode(', ', $selectCols);

// Fetch workshops
// If we have a native single datetime column, use SQL to split upcoming/past strictly.
$upcomingWorkshops = [];
$pastWorkshops = [];
if ($datetimeField) {
  $selectStrSql = implode(', ', $selectCols);
  $upcomingQuery = "SELECT $selectStrSql FROM workshops WHERE $datetimeField > NOW() ORDER BY $datetimeField ASC";
  $pastQuery = "SELECT $selectStrSql FROM workshops WHERE $datetimeField < NOW() ORDER BY $datetimeField DESC LIMIT 20";

  $upcomingResult = mysqli_query($conn, $upcomingQuery);
  if ($upcomingResult) {
    while ($row = mysqli_fetch_assoc($upcomingResult)) {
      $upcomingWorkshops[] = $row;
    }
  } else {
    error_log("Query error (upcoming sql): " . mysqli_error($conn) . " -- SQL: " . $upcomingQuery);
  }

  $pastResult = mysqli_query($conn, $pastQuery);
  if ($pastResult) {
    while ($row = mysqli_fetch_assoc($pastResult)) {
      $pastWorkshops[] = $row;
    }
  } else {
    error_log("Query error (past sql): " . mysqli_error($conn) . " -- SQL: " . $pastQuery);
  }

} else {
  // No native datetime column — fetch all rows and decide in PHP using strtotime on available fields
  $allQuery = "SELECT " . implode(', ', $selectCols) . " FROM workshops ORDER BY id DESC";
  $res = mysqli_query($conn, $allQuery);
  if (!$res) {
    error_log("Query error (all): " . mysqli_error($conn) . " -- SQL: " . $allQuery);
  } else {
    // helper to get timestamp from a row (try several fields/formats)
    $getTs = function($row) use ($datetimeField, $dateField, $timeField) {
      // try common names first
      $candidates = [];
      if (!empty($row['start_dt'])) $candidates[] = $row['start_dt'];
      if (!empty($row['start_datetime'])) $candidates[] = $row['start_datetime'];
      if (!empty($row['scheduled_at'])) $candidates[] = $row['scheduled_at'];
      if (!empty($row['datetime'])) $candidates[] = $row['datetime'];
      if (!empty($row['date_time'])) $candidates[] = $row['date_time'];
      // date+time fields
      if (!empty($dateField) && !empty($timeField) && !empty($row[$dateField]) && !empty($row[$timeField])) {
        $candidates[] = $row[$dateField] . ' ' . $row[$timeField];
      }
      if (!empty($dateField) && !empty($row[$dateField])) $candidates[] = $row[$dateField];
      if (!empty($timeField) && !empty($row[$timeField])) $candidates[] = $row[$timeField];

      foreach ($candidates as $s) {
        $ts = strtotime($s);
        if ($ts !== false && $ts > 0) return $ts;
      }
      return 0;
    };

    while ($row = mysqli_fetch_assoc($res)) {
      $ts = $getTs($row);
      if ($ts > time()) {
        $upcomingWorkshops[] = $row;
      } elseif ($ts > 0) {
        $pastWorkshops[] = $row;
      } else {
        // undated — treat as upcoming
        $upcomingWorkshops[] = $row;
      }
    }
    // sort upcoming by timestamp asc, past by timestamp desc (attempt)
    usort($upcomingWorkshops, function($a, $b) use ($getTs) { return $getTs($a) <=> $getTs($b); });
    usort($pastWorkshops, function($a, $b) use ($getTs) { return $getTs($b) <=> $getTs($a); });
  }
}

// Helper function to format date
function formatDate($datetimeStr) {
    if (!$datetimeStr) return 'Date TBA';
    $dt = new DateTime($datetimeStr);
    return $dt->format('d F Y');
}

// Helper function to get image
function getWorkshopImage($workshop, $thumbField, $altField = null) {
    if ($thumbField && !empty($workshop[$thumbField])) {
        return '../admin/workshops_img/' . htmlspecialchars($workshop[$thumbField]);
    }
    if ($altField && !empty($workshop[$altField])) {
        return '../admin/workshops_img/' . htmlspecialchars($workshop[$altField]);
    }
    return 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d';
}

// Helper function to get description
function getDescription($workshop, $descField) {
    if ($descField && !empty($workshop[$descField])) {
        return htmlspecialchars(substr($workshop[$descField], 0, 60));
    }
    return 'Workshop coming soon';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Workshops</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="robots" content="noindex, nofollow">

  <!-- Preconnect to external domains -->
  <link rel="preconnect" href="https://cdn.tailwindcss.com" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  
  <!-- Preload critical CSS -->
  <link rel="preload" href="../assets/css/main.css" as="style">
  
  <!-- CSS -->
  <link rel="stylesheet" href="../assets/css/main.css" />

  <!-- Tailwind (defer to prevent blocking) -->
  <link rel="preload" as="script" href="https://cdn.tailwindcss.com">
  <script src="https://cdn.tailwindcss.com" defer></script>

  <!-- Google Fonts optimized -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
  <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700;800&display=swap" rel="stylesheet"></noscript>

  <style>
    body {
      background:#000;
      color:#fff;
      font-family:'Inter', system-ui, -apple-system, sans-serif;
    }
    h1, h2, h3, h4, h5, h6 {
      font-family: 'Playfair Display', Georgia, serif;
    }
    .font-serif { font-family:'Playfair Display', Georgia, serif; }

    /* hide scrollbar */
    .no-scrollbar::-webkit-scrollbar { display:none; }
    .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
  </style>
</head>

<body>
    <!-- Page Loader -->
    <div id="page-loader">
      <div class="loader-spinner"></div>
      <div class="loader-text">Loading</div>
    </div>

    <div id="navbar-container"></div>
<!-- ================= HERO ================= -->
<section class="relative h-[70vh] md:h-[85vh] w-full overflow-hidden">

  <!-- Image -->
  <img
    src="../img/imgs/workshophere.jpeg"
    class="absolute inset-0 w-full h-full object-cover"
    alt="Workshop hero"
  />

  <!-- Dark overlay -->
  <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-black/40 to-black/70"></div>

  <!-- Text -->
  <div class="relative z-10 flex items-center justify-center h-full text-center px-6">
    <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl leading-tight max-w-5xl">
      From Chaos to Clarity: Business Systems Workshop
    </h1>
  </div>

  <!-- ================= SHADOW BAND ================= -->
  <svg
    class="absolute bottom-[-1px] w-full h-[180px]"
    viewBox="0 0 1440 180"
    preserveAspectRatio="none"
  >
    <defs>
      <linearGradient id="shadowFade" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0%" stop-color="rgba(0,0,0,0.75)" />
        <stop offset="55%" stop-color="rgba(0,0,0,0.95)" />
        <stop offset="100%" stop-color="#000" />
      </linearGradient>
    </defs>
    <path
      d="
        M0,120
        C200,150 420,90 720,110
        C980,130 1200,70 1440,50
        L1440,180
        L0,180
        Z
      "
      fill="url(#shadowFade)"
    />
  </svg>

</section>

<!-- ================= BLACK CONTENT SECTION ================= -->
<?php if (!empty($pastWorkshops)): ?>
<section class="bg-black pt-24 pb-10 px-6 md:px-16 text-center">

  <h2 class="font-serif text-3xl md:text-4xl mb-4">
    Previously Hosted Workshops
  </h2>

  <p class="text-gray-400 text-sm max-w-2xl mx-auto leading-relaxed">
    Live workshops and webinars focused on clarity, execution, and scalable growth.
    Built for entrepreneurs who want real business results.
  </p>

</section>

<!-- ================= CAROUSEL SECTION ================= -->
<section class="relative bg-black pb-20 px-6 md:px-16">

  <!-- Right fade to hint more cards -->
  <div class="pointer-events-none absolute inset-y-6 right-0 w-28 bg-gradient-to-l from-black via-black/70 to-transparent"></div>

  <!-- Arrows -->
  <div class="flex justify-end gap-3 mb-4 pr-4 md:pr-10">
    <button type="button" id="prevBtn"
      class="w-12 h-12 rounded-full border border-white/30 flex items-center justify-center text-white
             hover:bg-white hover:text-black transition">
      &larr;
    </button>

    <button type="button" id="nextBtn"
      class="w-12 h-12 rounded-full border border-white/30 flex items-center justify-center text-white
             hover:bg-white hover:text-black transition">
      &rarr;
    </button>
  </div>

  <!-- Carousel -->
  <div
    id="carousel"
    class="flex gap-4 md:gap-6 overflow-x-auto no-scrollbar relative z-10 py-4 pr-12 md:pr-20"
  >

    <?php foreach ($pastWorkshops as $workshop): ?>
    <article data-card class="min-w-[240px] md:min-w-[280px] h-[340px] md:h-[380px] rounded-2xl overflow-hidden border border-white/10 relative shrink-0 transition duration-300 ease-out hover:scale-[1.04] hover:-translate-y-1 hover:border-white/60 hover:shadow-[0_16px_40px_rgba(0,0,0,0.45)]">
      <img
        src="<?php echo getWorkshopImage($workshop, $thumbField); ?>"
        class="absolute inset-0 w-full h-full object-cover"
        alt="<?php echo htmlspecialchars($workshop['title']); ?>"
      />
      <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/60 to-transparent"></div>
      <div class="absolute bottom-6 left-6 right-6">
        <h3 class="font-serif text-lg leading-snug mb-3">
          <?php echo htmlspecialchars($workshop['title']); ?>
        </h3>
        <div class="flex items-center gap-2 text-xs text-gray-300">
          <span class="w-2 h-2 rounded-full bg-white"></span>
          <?php echo formatDate($workshop[$datetimeAlias] ?? null); ?>
        </div>
      </div>
    </article>
    <?php endforeach; ?>

  </div>

</section>
<?php endif; ?>


<!-- ================= UPCOMING WORKSHOPS ================= -->
<?php if (!empty($upcomingWorkshops)): ?>
<section class="bg-black py-28 px-6 md:px-16">

  <!-- Heading -->
  <h2 class="text-center font-serif text-3xl md:text-4xl mb-20">
    Upcoming Workshops
  </h2>

  <!-- Grid -->
  <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-12 gap-y-16">

    <?php foreach ($upcomingWorkshops as $workshop): ?>
    <div class="relative h-[380px] rounded-2xl overflow-hidden
     shadow-[0_25px_60px_rgba(0,0,0,0.85)]
     transition duration-300 ease-out
     hover:scale-[1.03] hover:-translate-y-1
     hover:border hover:border-white/60
     hover:shadow-[0_18px_48px_rgba(0,0,0,0.55)]">

      <!-- Image -->
      <img
        src="<?php echo getWorkshopImage($workshop, $thumbField); ?>"
        class="absolute inset-0 w-full h-full object-cover opacity-75"
        alt="<?php echo htmlspecialchars($workshop['title']); ?>"
      />

      <!-- Overlay -->
      <div class="absolute inset-0 bg-black/55"></div>

      <!-- CONTENT -->
      <div class="relative z-10 h-full p-6 flex flex-col justify-between">

        <!-- TOP (TITLE ONLY) -->
        <h3 class="font-serif text-xl">
          <?php echo htmlspecialchars($workshop['title']); ?>
        </h3>

        <!-- BOTTOM CONTENT -->
        <div>
          <span class="block text-sm text-gray-300 mb-2">
            <?php echo formatDate($workshop[$datetimeAlias] ?? null); ?>
          </span>

          <p class="text-sm text-gray-300 leading-relaxed mb-4">
            <?php echo getDescription($workshop, $descField); ?>...
          </p>

          <button
            type="button"
            class="open-register bg-white text-black text-xs px-4 py-2 rounded-md hover:bg-gray-200 transition"
            data-title="<?php echo htmlspecialchars($workshop['title']); ?>"
            data-id="<?php echo htmlspecialchars($workshop['id']); ?>"
            data-date="<?php echo formatDate($workshop[$datetimeAlias] ?? null); ?>">
            Register Now
          </button>
        </div>

      </div>
    </div>
    <?php endforeach; ?>

  </div>
</section>
<?php else: ?>
<section class="bg-black py-28 px-6 md:px-16">
    	
  <div class="text-center">
    <h2 class="text-center font-serif text-3xl md:text-4xl mb-8">
      No Upcoming Workshops
    </h2>
    <p class="text-gray-400 text-base">Check back soon for new workshops!</p>
  </div>
</section>
<?php endif; ?>

    <div id="footer-container"></div>

<!-- Register Modal (Professional Design) -->
<div id="registerModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm px-4" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="bg-gradient-to-br from-[#0a0a0a] to-[#1a1a1a] text-white rounded-2xl w-full max-w-4xl overflow-hidden shadow-[0_20px_80px_rgba(0,0,0,0.9)] border border-white/10 transform transition-all duration-300">
    
    <!-- Modal Header with Workshop Details -->
    <div class="relative px-8 py-6 border-b border-white/10 bg-gradient-to-r from-[#111] to-[#1a1a1a]">
      <div class="flex items-start justify-between">
        <div class="flex-1 pr-8">
          <div class="flex items-center gap-2 mb-1">
            <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
              <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
            </svg>
            <span class="text-xs font-medium text-amber-400 uppercase tracking-wider">Workshop Registration</span>
          </div>
          <h3 id="modalTitle" class="font-serif text-xl md:text-2xl font-semibold leading-tight">Register for Workshop</h3>
        </div>
        <button id="modalClose" aria-label="Close register form" class="text-gray-400 hover:text-white transition-colors duration-200 text-3xl leading-none w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/5">&times;</button>
      </div>
    </div>

    <form id="registerForm" class="p-8" novalidate>
      <input type="hidden" name="workshop_id" id="workshop_id">
      <input type="hidden" name="workshop_title" id="workshop_title">
      <input type="hidden" name="workshop_date" id="workshop_date">

      <!-- Form Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Left Column -->
        <div class="space-y-5">
          <!-- Full Name -->
          <div class="group">
            <label for="r_name" class="block text-sm font-medium text-gray-300 mb-2 flex items-center gap-2">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              Full Name <span class="text-red-400">*</span>
            </label>
            <input 
              id="r_name" 
              name="name" 
              type="text"
              placeholder="Enter your full name" 
              class="w-full px-4 py-3 bg-[#0f0f0f] border border-white/10 rounded-lg text-white placeholder-gray-500 
                     focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:border-amber-400/50 
                     transition-all duration-200 group-hover:border-white/20" 
              required>
          </div>

          <!-- Email -->
          <div class="group">
            <label for="r_email" class="block text-sm font-medium text-gray-300 mb-2 flex items-center gap-2">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              Email Address <span class="text-red-400">*</span>
            </label>
            <input 
              id="r_email" 
              name="email" 
              type="email" 
              placeholder="you@example.com" 
              class="w-full px-4 py-3 bg-[#0f0f0f] border border-white/10 rounded-lg text-white placeholder-gray-500 
                     focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:border-amber-400/50 
                     transition-all duration-200 group-hover:border-white/20" 
              required>
          </div>

          <!-- Contact Number -->
          <div class="group">
            <label for="r_contact" class="block text-sm font-medium text-gray-300 mb-2 flex items-center gap-2">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
              </svg>
              Contact Number
            </label>
            <input 
              id="r_contact" 
              name="contact" 
              type="tel"
              placeholder="Phone or WhatsApp" 
              class="w-full px-4 py-3 bg-[#0f0f0f] border border-white/10 rounded-lg text-white placeholder-gray-500 
                     focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:border-amber-400/50 
                     transition-all duration-200 group-hover:border-white/20">
          </div>

          <!-- Who Are You -->
          <div class="group">
            <label for="r_who" class="block text-sm font-medium text-gray-300 mb-2 flex items-center gap-2">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              Who Are You?
            </label>
            <input 
              id="r_who" 
              name="who" 
              type="text"
              placeholder="e.g., Entrepreneur, Student, Professional" 
              class="w-full px-4 py-3 bg-[#0f0f0f] border border-white/10 rounded-lg text-white placeholder-gray-500 
                     focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:border-amber-400/50 
                     transition-all duration-200 group-hover:border-white/20">
          </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-5">
          <!-- Message -->
          <div class="group h-full flex flex-col">
            <label for="r_message" class="block text-sm font-medium text-gray-300 mb-2 flex items-center gap-2">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
              </svg>
              Tell Us About Yourself
            </label>
            <textarea 
              id="r_message" 
              name="message" 
              rows="11" 
              placeholder="Share your goals, challenges, or what you hope to gain from this workshop..." 
              class="w-full px-4 py-3 bg-[#0f0f0f] border border-white/10 rounded-lg text-white placeholder-gray-500 
                     focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:border-amber-400/50 
                     transition-all duration-200 resize-none group-hover:border-white/20 flex-1"></textarea>
          </div>
        </div>
      </div>

      <!-- Error/Feedback Message -->
      <div id="formFeedback" class="mt-4 text-sm text-red-400 hidden"></div>

      <!-- Action Buttons -->
      <div class="mt-8 flex items-center justify-between pt-6 border-t border-white/10">
        <button 
          type="button" 
          id="modalCancel" 
          class="px-6 py-2.5 rounded-lg border border-white/10 text-gray-300 hover:text-white hover:bg-white/5 
                 transition-all duration-200 font-medium">
          Cancel
        </button>
        <button 
          type="submit" 
          id="modalSubmit" 
          class="px-8 py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 
                 text-black font-semibold flex items-center gap-2 shadow-lg shadow-amber-500/30 
                 hover:shadow-xl hover:shadow-amber-500/40 transition-all duration-200 
                 disabled:opacity-50 disabled:cursor-not-allowed">
          <span id="submitLabel">Register Now</span>
          <svg id="submitSpinner" class="w-5 h-5 hidden animate-spin" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </button>
      </div>
    </form>
  </div>
</div>
<script src="../assets/js/navbar.js" defer></script>
<script src="../assets/js/loader.js" defer></script>

<!-- ================= CAROUSEL SCRIPT ================= -->
<script>
  const carousel = document.getElementById('carousel');
  const nextBtn = document.getElementById('nextBtn');
  const prevBtn = document.getElementById('prevBtn');
  const firstCard = carousel ? carousel.querySelector('[data-card]') : null;

  const getStep = () => {
    if (!carousel || !firstCard) return 304; // fallback width + gap
    const styles = getComputedStyle(carousel);
    const gap = parseFloat(styles.columnGap || styles.gap || '0');
    return firstCard.getBoundingClientRect().width + gap;
  };

  let step = getStep();
  let animationFrame;
  let autoTimer;

  const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3);

  const smoothScrollBy = (distance, duration = 1100) => {
    if (!carousel) return;
    if (animationFrame) cancelAnimationFrame(animationFrame);

    const start = carousel.scrollLeft;
    const target = start + distance;
    const startTime = performance.now();

    const tick = (now) => {
      const elapsed = now - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const eased = easeOutCubic(progress);
      carousel.scrollLeft = start + (target - start) * eased;
      if (progress < 1) {
        animationFrame = requestAnimationFrame(tick);
      }
    };

    animationFrame = requestAnimationFrame(tick);
  };

  const scrollByStep = (direction) => {
    stopAutoPlay();
    smoothScrollBy(step * direction);
    startAutoPlay();
  };

  if (nextBtn) nextBtn.addEventListener('click', () => scrollByStep(1));
  if (prevBtn) prevBtn.addEventListener('click', () => scrollByStep(-1));

  window.addEventListener('resize', () => {
    step = getStep();
  });

  const startAutoPlay = () => {
    stopAutoPlay();
    autoTimer = setInterval(() => {
      const max = carousel ? carousel.scrollWidth - carousel.clientWidth : 0;
      const nextLeft = carousel ? carousel.scrollLeft + step : 0;
      if (carousel && max > 0 && nextLeft >= max - 4) {
        carousel.scrollLeft = 0; // jump back silently to start for seamless loop
      }
      smoothScrollBy(step);
    }, 3200);
  };

  const stopAutoPlay = () => {
    if (autoTimer) {
      clearInterval(autoTimer);
      autoTimer = null;
    }
  };

  if (carousel) {
    carousel.addEventListener('mouseenter', stopAutoPlay);
    carousel.addEventListener('mouseleave', startAutoPlay);
    startAutoPlay();
  }



        // ===== Register modal handling =====
        (function(){
          const openBtns = () => document.querySelectorAll('.open-register');
          const modal = document.getElementById('registerModal');
          const modalTitle = document.getElementById('modalTitle');
          const workshopId = document.getElementById('workshop_id');
          const workshopTitle = document.getElementById('workshop_title');
          const workshopDate = document.getElementById('workshop_date');
          const modalClose = document.getElementById('modalClose');
          const modalCancel = document.getElementById('modalCancel');
          const form = document.getElementById('registerForm');

          const openModal = (title, id, date) => {
            modalTitle.textContent = 'Register — ' + (title || 'Workshop');
            workshopId.value = id || '';
            workshopTitle.value = title || '';
            workshopDate.value = date || 'Date TBA';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
          };

          const closeModal = () => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
          };

          document.addEventListener('click', (e) => {
            const t = e.target.closest && e.target.closest('.open-register');
            if (t) {
              const title = t.getAttribute('data-title');
              const id = t.getAttribute('data-id');
              const date = t.getAttribute('data-date');
              openModal(title, id, date);
            }
          });

          modalClose?.addEventListener('click', closeModal);
          modalCancel?.addEventListener('click', closeModal);

          form?.addEventListener('submit', async function(ev){
            ev.preventDefault();
            
            const submitBtn = document.getElementById('modalSubmit');
            const submitLabel = document.getElementById('submitLabel');
            const submitSpinner = document.getElementById('submitSpinner');
            const formFeedback = document.getElementById('formFeedback');
            
            // Simple validation
            const name = document.getElementById('r_name').value.trim();
            const email = document.getElementById('r_email').value.trim();
            const formFeedbackEl = document.getElementById('formFeedback');
            
            if (!name || !email) {
              formFeedbackEl.textContent = 'Please enter name and email';
              formFeedbackEl.className = 'mt-4 text-sm text-red-400 block';
              return;
            }
            
            // Get form data
            const formData = new FormData(form);
            
            // Disable submit button
            submitBtn.disabled = true;
            submitLabel.textContent = 'Processing...';
            submitSpinner.classList.remove('hidden');
            formFeedbackEl.textContent = '';
            formFeedbackEl.className = 'mt-4 text-sm text-red-400 hidden';
            
            try {
              // Get raw response first for debugging
              const response = await fetch('../ajax/workshop-register-new.php', {
                method: 'POST',
                body: formData
              });
              
              // Log response status
              console.log('[Workshop] Response status:', response.status);
              
              // Get raw text first
              const responseText = await response.text();
              console.log('[Workshop] Raw response:', responseText.substring(0, 500));
              
              // Check if response is empty
              if (!responseText || responseText.trim() === '') {
                throw new Error('Server returned empty response. Check error logs on server.');
              }
              
              // Try to parse JSON
              let result;
              try {
                result = JSON.parse(responseText);
              } catch (parseError) {
                console.error('[Workshop] JSON parse error:', parseError);
                console.error('[Workshop] Full response:', responseText);
                
                // Check if it's HTML (500 error page)
                if (responseText.includes('<!DOCTYPE') || responseText.includes('<html')) {
                  throw new Error('Server error (500). Please check: 1) .env file exists, 2) vendor/ folder uploaded, 3) file paths correct');
                }
                
                throw new Error('Invalid server response: ' + parseError.message);
              }
              
              if (result.success) {
                // Professional success message with animation
                form.innerHTML = `
                  <div class="py-12 px-8 text-center">
                    <!-- Success Animation -->
                    <div class="mb-6 relative inline-block">
                      <div class="w-24 h-24 mx-auto bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center shadow-lg shadow-green-500/30 animate-bounce-once">
                        <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                      </div>
                      <!-- Confetti effect -->
                      <div class="absolute inset-0 pointer-events-none">
                        <div class="absolute top-0 left-1/4 w-2 h-2 bg-amber-400 rounded-full animate-confetti-1"></div>
                        <div class="absolute top-2 right-1/4 w-2 h-2 bg-blue-400 rounded-full animate-confetti-2"></div>
                        <div class="absolute top-4 left-1/2 w-2 h-2 bg-pink-400 rounded-full animate-confetti-3"></div>
                      </div>
                    </div>

                    <!-- Success Message -->
                    <h4 class="text-2xl md:text-3xl font-serif font-bold mb-3 text-white">
                      🎉 Registration Confirmed!
                    </h4>
                    <p class="text-gray-300 text-base mb-6 max-w-md mx-auto leading-relaxed">
                      ${result.message}
                    </p>

                    <!-- Info Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8 max-w-xl mx-auto">
                      <div class="bg-gradient-to-br from-amber-500/10 to-amber-600/10 border border-amber-500/20 rounded-lg p-4 text-left">
                        <div class="flex items-start gap-3">
                          <svg class="w-5 h-5 text-amber-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                          </svg>
                          <div>
                            <p class="text-xs text-gray-400 mb-0.5">Email Confirmation</p>
                            <p class="text-sm text-white">Check your inbox</p>
                          </div>
                        </div>
                      </div>
                      <div class="bg-gradient-to-br from-blue-500/10 to-blue-600/10 border border-blue-500/20 rounded-lg p-4 text-left">
                        <div class="flex items-start gap-3">
                          <svg class="w-5 h-5 text-blue-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                          </svg>
                          <div>
                            <p class="text-xs text-gray-400 mb-0.5">Workshop Details</p>
                            <p class="text-sm text-white">Coming 24-48hrs before</p>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                      <button 
                        type="button" 
                        id="closeAfter" 
                        class="px-8 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 
                               text-black font-semibold rounded-lg shadow-lg shadow-amber-500/30 
                               hover:shadow-xl hover:shadow-amber-500/40 transition-all duration-200 
                               flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Got it, Thanks!
                      </button>
                      <button 
                        type="button" 
                        onclick="window.location.href='../pages/workshop.php'" 
                        class="px-6 py-3 border border-white/10 text-gray-300 hover:text-white hover:bg-white/5 
                               rounded-lg transition-all duration-200 font-medium">
                        View More Workshops
                      </button>
                    </div>

                    <!-- Additional Note -->
                    <p class="text-xs text-gray-500 mt-8">
                      Questions? Reply to the confirmation email or contact us directly.
                    </p>
                  </div>

                  <style>
                    @keyframes bounce-once {
                      0%, 100% { transform: translateY(0); }
                      50% { transform: translateY(-20px); }
                    }
                    @keyframes confetti-1 {
                      0% { transform: translate(0, 0) rotate(0deg); opacity: 1; }
                      100% { transform: translate(-40px, 60px) rotate(180deg); opacity: 0; }
                    }
                    @keyframes confetti-2 {
                      0% { transform: translate(0, 0) rotate(0deg); opacity: 1; }
                      100% { transform: translate(40px, 70px) rotate(-180deg); opacity: 0; }
                    }
                    @keyframes confetti-3 {
                      0% { transform: translate(0, 0) rotate(0deg); opacity: 1; }
                      100% { transform: translate(20px, 80px) rotate(270deg); opacity: 0; }
                    }
                    .animate-bounce-once {
                      animation: bounce-once 0.6s ease-in-out;
                    }
                    .animate-confetti-1 {
                      animation: confetti-1 1s ease-out forwards;
                    }
                    .animate-confetti-2 {
                      animation: confetti-2 1.2s ease-out forwards;
                    }
                    .animate-confetti-3 {
                      animation: confetti-3 1.1s ease-out forwards;
                    }
                  </style>
                `;
                
                const closeAfter = document.getElementById('closeAfter');
                closeAfter?.addEventListener('click', closeModal);
              } else {
                // Error message
                formFeedbackEl.textContent = result.message;
                formFeedbackEl.className = 'mt-4 text-sm text-red-400 block';
                submitBtn.disabled = false;
                submitLabel.textContent = 'Register Now';
                submitSpinner.classList.add('hidden');
              }
            } catch (error) {
              console.error('[Workshop] Registration error:', error);
              console.error('[Workshop] Error details:', {
                message: error.message,
                stack: error.stack
              });
              
              // User-friendly error message
              let errorMsg = 'Network error. Please try again.';
              
              if (error.message.includes('Server error (500)')) {
                errorMsg = 'Server configuration error. Please contact support and mention: Missing .env or vendor files.';
              } else if (error.message.includes('empty response')) {
                errorMsg = 'Server error. Please contact support.';
              } else if (error.message) {
                errorMsg = error.message;
              }
              
              formFeedbackEl.textContent = errorMsg;
              formFeedbackEl.className = 'mt-4 text-sm text-red-400 block';
              submitBtn.disabled = false;
              submitLabel.textContent = 'Register Now';
              submitSpinner.classList.add('hidden');
            }
          });
        })();
</script>

	<script>
		const injectComponent = async (src, selector, pick) => {
			const target = document.querySelector(selector);
			if (!target) return null;
			try {
				const res = await fetch(src);
				if (!res.ok) throw new Error(`Failed to load ${src}: ${res.status}`);
				const html = await res.text();
				const doc = new DOMParser().parseFromString(html, 'text/html');
				const node = pick ? doc.querySelector(pick) : doc.body;
				target.innerHTML = '';
				if (node) target.appendChild(node.cloneNode(true));
				return target;
			} catch (err) {
				console.error(err);
				target.innerHTML = '<div class="text-red-500">Unable to load component.</div>';
				return null;
			}
		};

		const initReveal = () => {
			const items = document.querySelectorAll('.reveal');
			const observer = new IntersectionObserver(
				(entries) => {
					entries.forEach((entry) => {
						if (entry.isIntersecting) {
							entry.target.classList.add('revealed');
							observer.unobserve(entry.target);
						}
					});
				},
				{ threshold: 0.15 }
			);
			items.forEach((item) => observer.observe(item));
		};

		document.addEventListener('DOMContentLoaded', async () => {
			// Load components in parallel for faster page load
			const [navbarRoot] = await Promise.all([
				injectComponent('/components/Navbar.html', '#navbar-container'),
				injectComponent('../components/fotter.html', '#footer-container', 'footer')
			]);
			
			// Initialize navbar immediately
			if (typeof window.initNavbar === 'function') {
				window.initNavbar();
			}
			initReveal();
		});
	</script>
</body>
</html>
