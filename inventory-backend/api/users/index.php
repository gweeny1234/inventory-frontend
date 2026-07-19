<?php
header("Access-Control-Allow-Origin: [localhost](http://localhost:3000)");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../../config/Database.php';

$database = new Database();
$db = $database->connect();

try {
    $query = "SELECT id, firstname, lastname, email, role, profile_image, created_at 
              FROM users 
              ORDER BY created_at DESC";

    $stmt = $db->prepare($query);
    $stmt->execute();

    $users = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'count' => count($users),
        'data' => $users
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
