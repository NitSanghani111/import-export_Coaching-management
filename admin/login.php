<?php
session_start();
require "../db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin["password"])) {
            $_SESSION["admin"] = $admin["username"];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "User not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<meta name="robots" content="noindex, nofollow">
<title>Admin Login</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-black flex items-center justify-center px-6">

<!-- Background glow -->
<div class="fixed inset-0 pointer-events-none">
  <div class="absolute w-[500px] h-[500px] bg-white/10 rounded-full blur-[120px] top-[-120px] left-[-120px]"></div>
  <div class="absolute w-[420px] h-[420px] bg-white/5 rounded-full blur-[140px] bottom-[-120px] right-[-120px]"></div>
</div>

<!-- Login Card -->
<div class="relative w-full max-w-5xl bg-white rounded-[26px] shadow-[0_40px_120px_rgba(0,0,0,0.85)] overflow-hidden grid grid-cols-1 md:grid-cols-2 bg-white-5">

  <!-- LEFT FORM -->
  <div class="px-12 py-16">
    <h1 class="text-4xl font-serif mb-2">Admin Login</h1>
    <p class="text-gray-600 mb-10">Access your learning and growth tools.</p>

    <?php if ($error): ?>
      <div class="mb-6 rounded-xl bg-red-100 text-red-700 px-4 py-3 text-sm">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" autocomplete="off" class="space-y-6">
      <div>
        <label class="block mb-2 text-sm">Username</label>
        <input
          type="text"
          name="username"
          placeholder="Enter username"
          required
          class="w-full rounded-full border border-black px-5 py-3 focus:outline-none"
        />
      </div>

      <div>
        <label class="block mb-2 text-sm">Password</label>
        <input
          type="password"
          name="password"
          placeholder="Enter password"
          required
          class="w-full rounded-full border border-black px-5 py-3 focus:outline-none"
        />
      </div>

      <button
        type="submit"
        class="w-full rounded-full bg-[#FFC261] py-3 font-semibold text-lg transition hover:shadow-lg hover:-translate-y-[1px]"
      >
        Login
      </button>
    </form>
  </div>

  <!-- RIGHT IMAGE -->
  <div class="bg-black p-4 flex items-center justify-center">
    <img
      src="../img/imgs/login.jpeg"
      alt="Login"
      class="w-full h-full object-cover rounded-[18px] grayscale contrast-110 brightness-90"
    />
  </div>

</div>

</body>
</html>
