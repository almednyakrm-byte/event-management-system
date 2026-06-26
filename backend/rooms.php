<?php
require_once 'db.php';

// Get user role and ID from session
$userRole = $_SESSION['userRole'];
$userID = $_SESSION['userID'];

// Check if user is logged in
if (!$userID) {
    http_response_code(401);
    echo json_encode(array('error' => 'Unauthorized'));
    exit;
}

// Handle GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $roomId = $_GET['id'] ?? null;
    if ($roomId) {
        // Retrieve room by ID
        $stmt = $pdo->prepare('SELECT * FROM rooms WHERE id = :id');
        $stmt->bindParam(':id', $roomId);
        $stmt->execute();
        $room = $stmt->fetch();
        if ($room) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($room);
        } else {
            http_response_code(404);
            echo json_encode(array('error' => 'Room not found'));
        }
    } else {
        // Retrieve all rooms
        $stmt = $pdo->prepare('SELECT * FROM rooms');
        $stmt->execute();
        $rooms = $stmt->fetchAll();
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($rooms);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid request body'));
        exit;
    }

    // Validate input
    $requiredFields = array('name', 'capacity');
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            http_response_code(400);
            echo json_encode(array('error' => 'Missing required field: ' . $field));
            exit;
        }
    }

    // Sanitize input
    $input['name'] = trim($input['name']);
    $input['capacity'] = (int) $input['capacity'];

    // Insert new room
    $stmt = $pdo->prepare('INSERT INTO rooms (name, capacity) VALUES (:name, :capacity)');
    $stmt->bindParam(':name', $input['name']);
    $stmt->bindParam(':capacity', $input['capacity']);
    $stmt->execute();
    $roomId = $pdo->lastInsertId();
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode(array('id' => $roomId, 'name' => $input['name'], 'capacity' => $input['capacity']));
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Check if user is admin
    if ($userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    // Read JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid request body'));
        exit;
    }

    // Validate input
    $requiredFields = array('id', 'name', 'capacity');
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            http_response_code(400);
            echo json_encode(array('error' => 'Missing required field: ' . $field));
            exit;
        }
    }

    // Sanitize input
    $input['name'] = trim($input['name']);
    $input['capacity'] = (int) $input['capacity'];

    // Retrieve room by ID
    $stmt = $pdo->prepare('SELECT * FROM rooms WHERE id = :id');
    $stmt->bindParam(':id', $input['id']);
    $stmt->execute();
    $room = $stmt->fetch();
    if (!$room) {
        http_response_code(404);
        echo json_encode(array('error' => 'Room not found'));
        exit;
    }

    // Update room
    $stmt = $pdo->prepare('UPDATE rooms SET name = :name, capacity = :capacity WHERE id = :id');
    $stmt->bindParam(':id', $input['id']);
    $stmt->bindParam(':name', $input['name']);
    $stmt->bindParam(':capacity', $input['capacity']);
    $stmt->execute();
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(array('id' => $input['id'], 'name' => $input['name'], 'capacity' => $input['capacity']));
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Check if user is admin
    if ($userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    // Retrieve room by ID
    $roomId = $_GET['id'] ?? null;
    if (!$roomId) {
        http_response_code(400);
        echo json_encode(array('error' => 'Missing required field: id'));
        exit;
    }

    // Delete room
    $stmt = $pdo->prepare('DELETE FROM rooms WHERE id = :id');
    $stmt->bindParam(':id', $roomId);
    $stmt->execute();
    http_response_code(204);
}