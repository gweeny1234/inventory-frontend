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

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit();
}

try {
    $query = "SELECT 
                p.*,
                c.name as category_name,
                s.name as supplier_name
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.id
              LEFT JOIN suppliers s ON p.supplier_id = s.id
              WHERE p.id = :id
              LIMIT 1";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit();
    }

    $product = $stmt->fetch();

    echo json_encode([
        'success' => true,
        'data' => $product
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
