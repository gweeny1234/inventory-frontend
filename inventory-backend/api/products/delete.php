<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


include_once '../../config/Database.php';

$database = new Database();
$db = $database->connect();

$data = json_decode(file_get_contents("php://input"));

if (empty($data->id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit();
}

try {
    // Check if product exists
    $checkQuery = "SELECT id FROM products WHERE id = :id LIMIT 1";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':id', $data->id, PDO::PARAM_INT);
    $checkStmt->execute();

    if ($checkStmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit();
    }

    $query = "DELETE FROM products WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $data->id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
