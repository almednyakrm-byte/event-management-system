<?php
require_once 'db.php';

// Get user data from session
$user = $_SESSION['user'];

// Check if user is logged in
if (!$user) {
    http_response_code(401);
    echo json_encode(array('error' => 'Unauthorized'));
    exit;
}

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle GET request
if ($method === 'GET') {
    // Check if user is admin
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    // Get accommodations
    $stmt = $pdo->prepare('SELECT * FROM accommodations');
    $stmt->execute();
    $accommodations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return accommodations
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($accommodations);
    exit;
}

// Handle POST request
if ($method === 'POST') {
    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate data
    if (!isset($data['name']) || !isset($data['description']) || !isset($data['price'])) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid request'));
        exit;
    }

    // Sanitize data
    $name = filter_var($data['name'], FILTER_SANITIZE_STRING);
    $description = filter_var($data['description'], FILTER_SANITIZE_STRING);
    $price = filter_var($data['price'], FILTER_SANITIZE_NUMBER_INT);

    // Insert accommodation
    $stmt = $pdo->prepare('INSERT INTO accommodations (name, description, price) VALUES (:name, :description, :price)');
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->execute();

    // Return accommodation
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode(array('message' => 'Accommodation created successfully'));
    exit;
}

// Handle PUT request
if ($method === 'PUT') {
    // Check if user is admin
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate data
    if (!isset($data['id']) || !isset($data['name']) || !isset($data['description']) || !isset($data['price'])) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid request'));
        exit;
    }

    // Sanitize data
    $id = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);
    $name = filter_var($data['name'], FILTER_SANITIZE_STRING);
    $description = filter_var($data['description'], FILTER_SANITIZE_STRING);
    $price = filter_var($data['price'], FILTER_SANITIZE_NUMBER_INT);

    // Update accommodation
    $stmt = $pdo->prepare('UPDATE accommodations SET name = :name, description = :description, price = :price WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->execute();

    // Return accommodation
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(array('message' => 'Accommodation updated successfully'));
    exit;
}

// Handle DELETE request
if ($method === 'DELETE') {
    // Check if user is admin
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate data
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid request'));
        exit;
    }

    // Sanitize data
    $id = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);

    // Delete accommodation
    $stmt = $pdo->prepare('DELETE FROM accommodations WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Return message
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(array('message' => 'Accommodation deleted successfully'));
    exit;
}