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
    $query = "SELECT 
                p.id,
                p.name,
                p.category_id,
                p.supplier_id,
                p.price,
                p.stock,
                p.reorder_level,
                p.date_added,
                p.created_at,
                c.name as category_name,
                s.name as supplier_name
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.id
              LEFT JOIN suppliers s ON p.supplier_id = s.id
              ORDER BY p.created_at DESC";

    $stmt = $db->prepare($query);
    $stmt->execute();

    $products = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'count' => count($products),
        'data' => $products
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
