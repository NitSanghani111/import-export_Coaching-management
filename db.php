<?php
$host = "mysql";   // docker service name
$user = "root";
$pass = "root";
$db   = "coachingdb";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    error_log("DB error: " . $conn->connect_error);
    exit;
}

$conn->set_charset("utf8mb4");
