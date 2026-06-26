<?php

require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// Define validation rules
$validationRules = [
    'name' => ['required', 'string'],
    'address' => ['required', 'string'],
    'city' => ['required', 'string'],
    'state' => ['required', 'string'],
    'zip' => ['required', 'string'],
    'phone' => ['required', 'string'],
    'email' => ['required', 'email'],
];

// Validate input data
foreach ($validationRules as $field => $rules) {
    if (!isset($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required field: ' . $field]);
        exit;
    }
    if (!in_array('required', $rules) || empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => 'Field ' . $field . ' is required']);
        exit;
    }
    if (in_array('string', $rules) && !is_string($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => 'Field ' . $field . ' must be a string']);
        exit;
    }
    if (in_array('email', $rules) && !filter_var($input[$field], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Field ' . $field . ' must be a valid email address']);
        exit;
    }
}

// Sanitize input data
$input = array_map('trim', $input);

// Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

// Handle GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->prepare('SELECT * FROM venues');
        $stmt->execute();
        $venues = $stmt->fetchAll(PDO::FETCH_ASSOC);
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($venues);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal Server Error']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare('INSERT INTO venues (name, address, city, state, zip, phone, email) VALUES (:name, :address, :city, :state, :zip, :phone, :email)');
        $stmt->execute($input);
        http_response_code(201);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Venue created successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal Server Error']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    try {
        $stmt = $pdo->prepare('UPDATE venues SET name = :name, address = :address, city = :city, state = :state, zip = :zip, phone = :phone, email = :email WHERE id = :id');
        $stmt->execute(array_merge($input, ['id' => $_GET['id']]));
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Venue updated successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal Server Error']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    try {
        $stmt = $pdo->prepare('DELETE FROM venues WHERE id = :id');
        $stmt->execute(['id' => $_GET['id']]);
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Venue deleted successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal Server Error']);
    }
}