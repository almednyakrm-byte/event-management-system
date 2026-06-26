<?php

require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    http_response_code(401);
    echo json_encode(array('error' => 'Unauthorized'));
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// Define table name
$table_name = 'حجز_تذاكر';

// Define columns
$columns = array('id', 'user_id', 'date', 'time', 'seat_number', 'price');

// Define validation rules
$validation_rules = array(
    'id' => 'required|integer',
    'user_id' => 'required|integer',
    'date' => 'required|date',
    'time' => 'required|string',
    'seat_number' => 'required|string',
    'price' => 'required|numeric'
);

// Validate input data
foreach ($validation_rules as $column => $rules) {
    foreach ($rules as $rule) {
        if (!isset($input[$column]) || !preg_match('/^' . $rule . '$/', $input[$column])) {
            http_response_code(400);
            echo json_encode(array('error' => 'Invalid input data'));
            exit;
        }
    }
}

// Sanitize input data
foreach ($columns as $column) {
    $input[$column] = htmlspecialchars($input[$column]);
}

// Handle GET request
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Check if user is admin
    if ($_SESSION['role'] != 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    // Get all records
    try {
        $stmt = $pdo->prepare('SELECT * FROM ' . $table_name);
        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        http_response_code(200);
        echo json_encode($records);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array('error' => 'Internal Server Error'));
    }
}

// Handle POST request
elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Insert new record
    try {
        $stmt = $pdo->prepare('INSERT INTO ' . $table_name . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', array_fill(0, count($columns), '?')) . ')');
        $stmt->execute(array_values($input));
        http_response_code(201);
        echo json_encode(array('message' => 'Record created successfully'));
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array('error' => 'Internal Server Error'));
    }
}

// Handle PUT request
elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Check if user is admin
    if ($_SESSION['role'] != 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    // Update existing record
    try {
        $stmt = $pdo->prepare('UPDATE ' . $table_name . ' SET ' . implode(', ', array_map(function($column) { return $column . ' = ?'; }, $columns)) . ' WHERE id = ?');
        $stmt->execute(array_merge(array_values($input), array($input['id'])));
        http_response_code(200);
        echo json_encode(array('message' => 'Record updated successfully'));
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array('error' => 'Internal Server Error'));
    }
}

// Handle DELETE request
elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Check if user is admin
    if ($_SESSION['role'] != 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    // Delete existing record
    try {
        $stmt = $pdo->prepare('DELETE FROM ' . $table_name . ' WHERE id = ?');
        $stmt->execute(array($input['id']));
        http_response_code(200);
        echo json_encode(array('message' => 'Record deleted successfully'));
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array('error' => 'Internal Server Error'));
    }
}

// Set Content-Type header
header('Content-Type: application/json');