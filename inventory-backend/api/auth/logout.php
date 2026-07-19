<?php
header("Access-Control-Allow-Origin: [localhost](http://localhost:3000)");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();
session_unset();
session_destroy();

echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
?>
