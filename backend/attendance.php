<?php
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle GET request
if ($method === 'GET') {
    // Check if user is admin
    if ($_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Get attendance data
    $stmt = $pdo->prepare('SELECT * FROM attendance');
    $stmt->execute();
    $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return attendance data
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($attendance);
    exit;
}

// Handle POST request
if ($method === 'POST') {
    // Get attendance data from request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate attendance data
    if (!isset($data['employee_id']) || !isset($data['date']) || !isset($data['status'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    // Sanitize attendance data
    $employee_id = (int) $data['employee_id'];
    $date = date('Y-m-d', strtotime($data['date']));
    $status = (int) $data['status'];

    // Insert attendance data
    $stmt = $pdo->prepare('INSERT INTO attendance (employee_id, date, status) VALUES (:employee_id, :date, :status)');
    $stmt->bindParam(':employee_id', $employee_id);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':status', $status);
    $stmt->execute();

    // Return success message
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Attendance created successfully']);
    exit;
}

// Handle PUT request
if ($method === 'PUT') {
    // Check if user is admin
    if ($_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Get attendance data from request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate attendance data
    if (!isset($data['id']) || !isset($data['employee_id']) || !isset($data['date']) || !isset($data['status'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    // Sanitize attendance data
    $id = (int) $data['id'];
    $employee_id = (int) $data['employee_id'];
    $date = date('Y-m-d', strtotime($data['date']));
    $status = (int) $data['status'];

    // Update attendance data
    $stmt = $pdo->prepare('UPDATE attendance SET employee_id = :employee_id, date = :date, status = :status WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':employee_id', $employee_id);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':status', $status);
    $stmt->execute();

    // Return success message
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Attendance updated successfully']);
    exit;
}

// Handle DELETE request
if ($method === 'DELETE') {
    // Check if user is admin
    if ($_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Get attendance data from request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate attendance data
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    // Sanitize attendance data
    $id = (int) $data['id'];

    // Delete attendance data
    $stmt = $pdo->prepare('DELETE FROM attendance WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Return success message
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Attendance deleted successfully']);
    exit;
}

// Return error message for invalid request method
http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
exit;