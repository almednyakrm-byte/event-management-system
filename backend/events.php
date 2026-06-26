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

// Handle GET requests
if ($method === 'GET') {
    // Validate the request
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    // Sanitize the ID
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // Prepare the SQL query
    $sql = 'SELECT * FROM events WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Fetch the event
    $event = $stmt->fetch();

    // Check if the event exists
    if (!$event) {
        http_response_code(404);
        echo json_encode(['error' => 'Event not found']);
        exit;
    }

    // Return the event
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($event);
}

// Handle POST requests
elseif ($method === 'POST') {
    // Validate the request
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['title'], $data['description'], $data['date'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    // Sanitize the input
    $title = filter_var($data['title'], FILTER_SANITIZE_STRING);
    $description = filter_var($data['description'], FILTER_SANITIZE_STRING);
    $date = filter_var($data['date'], FILTER_SANITIZE_STRING);

    // Prepare the SQL query
    $sql = 'INSERT INTO events (title, description, date, user_id) VALUES (:title, :description, :date, :user_id)';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();

    // Return the created event
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode(['id' => $pdo->lastInsertId()]);
}

// Handle PUT requests
elseif ($method === 'PUT') {
    // Validate the request
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['id'], $data['title'], $data['description'], $data['date'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    // Sanitize the input
    $id = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);
    $title = filter_var($data['title'], FILTER_SANITIZE_STRING);
    $description = filter_var($data['description'], FILTER_SANITIZE_STRING);
    $date = filter_var($data['date'], FILTER_SANITIZE_STRING);

    // Check if the user is an admin
    if ($userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Prepare the SQL query
    $sql = 'UPDATE events SET title = :title, description = :description, date = :date WHERE id = :id AND user_id = :user_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();

    // Check if the update was successful
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Event not found']);
        exit;
    }

    // Return the updated event
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['id' => $id]);
}

// Handle DELETE requests
elseif ($method === 'DELETE') {
    // Validate the request
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    // Sanitize the ID
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // Check if the user is an admin
    if ($userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Prepare the SQL query
    $sql = 'DELETE FROM events WHERE id = :id AND user_id = :user_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();

    // Check if the deletion was successful
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Event not found']);
        exit;
    }

    // Return a success message
    http_response_code(204);
    echo json_encode(['message' => 'Event deleted successfully']);
}