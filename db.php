<?php
/**
 * Database Connection
 * Works for:
 * - Docker (local)
 * - ByetHost (demo)
 * - Hostinger shared cPanel (live)
 */

$serverName = $_SERVER['SERVER_NAME'] ?? '';
$isLocalDocker = ($serverName === 'localhost' || $serverName === '127.0.0.1');

// ===============================
// LOCAL (Docker)
// ===============================
if ($isLocalDocker) {
    $host = "mysql";        // Docker service name
    $user = "root";
    $pass = "root";
    $db   = "coachingdb";
}
// ===============================
// LIVE (ByetHost / Hostinger)
// ===============================
else {
    $host = "localhost";   // cPanel MySQL host
    $user="u722755904_Parth_jethva";     // change this
    $pass = "Parth902"; // change this
    $db   = "u722755904_coachingdb";     // change this
}

// ===============================
// CONNECTION
// ===============================
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    exit('Database connection error.');
}

$conn->set_charset("utf8mb4");
