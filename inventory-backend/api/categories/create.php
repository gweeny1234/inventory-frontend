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

try {

    $database = new Database();
    $db = $database->connect();

    if (!$db) {
        throw new Exception("Database connection failed");
    }

    $rawData = file_get_contents("php://input");

    if (empty($rawData)) {
        throw new Exception("No data received");
    }

    $data = json_decode($rawData);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON received");
    }

    if (empty($data->name)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Category name is required'
        ]);
        exit();
    }

    // Check duplicate category
    $checkQuery = "SELECT id FROM categories WHERE name = :name LIMIT 1";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':name', $data->name);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Category already exists'
        ]);
        exit();
    }

    $description = isset($data->description)
        ? trim($data->description)
        : '';

    $query = "INSERT INTO categories (name, description)
              VALUES (:name, :description)";

    $stmt = $db->prepare($query);

    $stmt->bindParam(':name', $data->name);
    $stmt->bindParam(':description', $description);

    if ($stmt->execute()) {

        echo json_encode([
            'success' => true,
            'message' => 'Category created successfully',
            'id' => $db->lastInsertId()
        ]);

    } else {

        $errorInfo = $stmt->errorInfo();

        echo json_encode([
            'success' => false,
            'message' => 'Database error',
            'error' => $errorInfo
        ]);
    }

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'PDO Error',
        'error' => $e->getMessage()
    ]);
}