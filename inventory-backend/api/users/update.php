<?php
// -------------------------------------------------------------
// 1. CORS HEADERS (Allowing your actual React app ports)
// -------------------------------------------------------------
$allowedOrigins = [
    "http://localhost:5173",
    "http://localhost:5175"
];

$origin = isset($_SERVER['HTTP_ORIGIN']) ? rtrim($_SERVER['HTTP_ORIGIN'], '/') : '';

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $origin);
} else {
    header("Access-Control-Allow-Origin: http://localhost:5173");
}

header("Access-Control-Allow-Methods: PUT, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed. Use PUT or POST."]);
    exit();
}

// -------------------------------------------------------------
// 2. DATABASE CONNECTION
// -------------------------------------------------------------
include_once '../../config/Database.php';

$database = new Database();
$db = $database->connect();

// -------------------------------------------------------------
// 3. READ INPUT AND UPDATE USER
// -------------------------------------------------------------
$data = json_decode(file_get_contents("php://input"));

if (empty($data->id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit();
}

try {
    // Build dynamic query based on provided fields safely
    $fields = [];
    $params = [':id' => intval($data->id)];

    if (!empty($data->firstname)) {
        $fields[] = "firstname = :firstname";
        $params[':firstname'] = trim($data->firstname);
    }
    if (!empty($data->lastname)) {
        $fields[] = "lastname = :lastname";
        $params[':lastname'] = trim($data->lastname);
    }
    if (!empty($data->email)) {
        $fields[] = "email = :email";
        $params[':email'] = trim($data->email);
    }
    if (!empty($data->role)) {
        $fields[] = "role = :role";
        $params[':role'] = trim($data->role);
    }
    // Only update the password if the user actually typed a new one
    if (!empty($data->password)) {
        $fields[] = "password = :password";
        $params[':password'] = password_hash($data->password, PASSWORD_DEFAULT);
    }

    if (empty($fields)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No fields to update']);
        exit();
    }

    $query = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
    $stmt = $db->prepare($query);

    foreach ($params as $key => $value) {
        // Use PARAM_INT for ID, PARAM_STR for everything else
        if ($key === ':id') {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
    }

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'User updated successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to update user profile.'
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>