<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../../config/Database.php';

$database = new Database();
$db = $database->connect();

try {
    $query = "SELECT c.*, 
              (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count
              FROM categories c
              ORDER BY c.name ASC";

    $stmt = $db->prepare($query);
    $stmt->execute();

    $categories = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'count' => count($categories),
        'data' => $categories
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
