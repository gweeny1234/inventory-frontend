<?php

$allowedOrigins = [
    "http://localhost:5173",
    "http://localhost:5175"
];

// Get the incoming origin and strip any trailing slashes
$origin = isset($_SERVER['HTTP_ORIGIN']) ? rtrim($_SERVER['HTTP_ORIGIN'], '/') : '';

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $origin);
} else {
    // FALLBACK: Always default to your primary development origin if no match is found
    header("Access-Control-Allow-Origin: http://localhost:5173");
}

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Handle preflight (OPTIONS) requests immediately
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}