<?php

require_once 'db.php';

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get the user role
$userRole = $_SESSION['user_role'];

// Handle GET request
if ($method === 'GET') {
    // Get the offer ID from the URL query string
    $offerId = $_GET['id'] ?? null;

    // Check if the user is an admin to allow edit and delete operations
    if ($offerId && ($userRole !== 'admin')) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Prepare the SQL query to select all offers or a single offer
    $stmt = $pdo->prepare('SELECT * FROM عروض' . ($offerId ? ' WHERE id = :id' : ''));
    $stmt->bindParam(':id', $offerId);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetchAll();

    // Return the result as JSON
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($result);
}

// Handle POST request
elseif ($method === 'POST') {
    // Read the input data from JSON
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Validate the input data
    if (!isset($inputData['name']) || !isset($inputData['description'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request data']);
        exit;
    }

    // Sanitize the input data
    $name = filter_var($inputData['name'], FILTER_SANITIZE_STRING);
    $description = filter_var($inputData['description'], FILTER_SANITIZE_STRING);

    // Prepare the SQL query to insert a new offer
    $stmt = $pdo->prepare('INSERT INTO عروض (name, description) VALUES (:name, :description)');
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->execute();

    // Return the result as JSON
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Offer created successfully']);
}

// Handle PUT request
elseif ($method === 'PUT') {
    // Get the offer ID from the URL query string
    $offerId = $_GET['id'] ?? null;

    // Check if the user is an admin to allow edit operations
    if (!$offerId || ($userRole !== 'admin')) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Read the input data from JSON
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Validate the input data
    if (!isset($inputData['name']) || !isset($inputData['description'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request data']);
        exit;
    }

    // Sanitize the input data
    $name = filter_var($inputData['name'], FILTER_SANITIZE_STRING);
    $description = filter_var($inputData['description'], FILTER_SANITIZE_STRING);

    // Prepare the SQL query to update an offer
    $stmt = $pdo->prepare('UPDATE عروض SET name = :name, description = :description WHERE id = :id');
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':id', $offerId);
    $stmt->execute();

    // Return the result as JSON
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Offer updated successfully']);
}

// Handle DELETE request
elseif ($method === 'DELETE') {
    // Get the offer ID from the URL query string
    $offerId = $_GET['id'] ?? null;

    // Check if the user is an admin to allow delete operations
    if (!$offerId || ($userRole !== 'admin')) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Prepare the SQL query to delete an offer
    $stmt = $pdo->prepare('DELETE FROM عروض WHERE id = :id');
    $stmt->bindParam(':id', $offerId);
    $stmt->execute();

    // Return the result as JSON
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Offer deleted successfully']);
}

// Return a 405 Method Not Allowed response for unsupported methods
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}