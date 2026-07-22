<?php
// Import database connection file
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Initialize database connection
$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle GET requests
if ($method == 'GET') {
    // Validate and sanitize input
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

    // Check if the user is an admin for editing/deleting
    if (isset($id) && $_SESSION['user_role'] != 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // SQL query structure: Select all tickets or a specific ticket by id
    if (isset($id)) {
        $stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = :id');
        $stmt->bindParam(':id', $id);
    } else {
        $stmt = $pdo->prepare('SELECT * FROM tickets');
    }

    // Execute the query
    $stmt->execute();

    // Output processing: Return the tickets in JSON format
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($tickets);
}

// Handle POST requests
elseif ($method == 'POST') {
    // Validate and sanitize input
    $data = json_decode(file_get_contents('php://input'), true);
    $title = filter_var($data['title'] ?? null, FILTER_SANITIZE_STRING);
    $description = filter_var($data['description'] ?? null, FILTER_SANITIZE_STRING);

    // Check if the input is valid
    if (!$title || !$description) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    // SQL query structure: Insert a new ticket
    $stmt = $pdo->prepare('INSERT INTO tickets (title, description, user_id) VALUES (:title, :description, :user_id)');
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);

    // Execute the query
    $stmt->execute();

    // Output processing: Return the newly created ticket in JSON format
    $ticket = $pdo->lastInsertId();
    $stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = :id');
    $stmt->bindParam(':id', $ticket);
    $stmt->execute();
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode($ticket);
}

// Handle PUT requests
elseif ($method == 'PUT') {
    // Validate and sanitize input
    $data = json_decode(file_get_contents('php://input'), true);
    $id = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);
    $title = filter_var($data['title'] ?? null, FILTER_SANITIZE_STRING);
    $description = filter_var($data['description'] ?? null, FILTER_SANITIZE_STRING);

    // Check if the user is an admin for editing
    if ($_SESSION['user_role'] != 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Check if the input is valid
    if (!$id || !$title || !$description) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    // SQL query structure: Update a ticket
    $stmt = $pdo->prepare('UPDATE tickets SET title = :title, description = :description WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);

    // Execute the query
    $stmt->execute();

    // Output processing: Return the updated ticket in JSON format
    $stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($ticket);
}

// Handle DELETE requests
elseif ($method == 'DELETE') {
    // Validate and sanitize input
    $data = json_decode(file_get_contents('php://input'), true);
    $id = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);

    // Check if the user is an admin for deleting
    if ($_SESSION['user_role'] != 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Check if the input is valid
    if (!$id) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    // SQL query structure: Delete a ticket
    $stmt = $pdo->prepare('DELETE FROM tickets WHERE id = :id');
    $stmt->bindParam(':id', $id);

    // Execute the query
    $stmt->execute();

    // Output processing: Return a success message in JSON format
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Ticket deleted successfully']);
}

// Handle other requests
else {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Method not allowed']);
}