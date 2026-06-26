<?php

require_once 'db.php';

// Get user role and ID from session
$userRole = $_SESSION['userRole'];
$userID = $_SESSION['userID'];

// Get input data from JSON or POST
$inputData = json_decode(file_get_contents('php://input'), true);
if (empty($inputData)) {
    $inputData = $_POST;
}

// Validate input data
if (empty($inputData)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input data']);
    exit;
}

// Sanitize input data
$inputData = array_map('trim', $inputData);

// Define CRUD operations
$op = $inputData['op'] ?? null;

// Handle GET operation
if ($op === 'get') {
    try {
        // Prepare SELECT statement
        $stmt = $pdo->prepare('SELECT * FROM فرق');
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($result);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Handle POST operation
elseif ($op === 'create') {
    try {
        // Validate input data
        if (empty($inputData['name']) || empty($inputData['description'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input data']);
            exit;
        }

        // Prepare INSERT statement
        $stmt = $pdo->prepare('INSERT INTO فرق (name, description) VALUES (:name, :description)');
        $stmt->bindParam(':name', $inputData['name']);
        $stmt->bindParam(':description', $inputData['description']);
        $stmt->execute();
        $userID = $pdo->lastInsertId();
        http_response_code(201);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'فرق created successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Handle PUT operation
elseif ($op === 'update') {
    try {
        // Validate input data
        if (empty($inputData['id']) || empty($inputData['name']) || empty($inputData['description'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input data']);
            exit;
        }

        // Prepare UPDATE statement
        $stmt = $pdo->prepare('UPDATE فرق SET name = :name, description = :description WHERE id = :id');
        $stmt->bindParam(':id', $inputData['id']);
        $stmt->bindParam(':name', $inputData['name']);
        $stmt->bindParam(':description', $inputData['description']);
        $stmt->execute();
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'فرق updated successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Handle DELETE operation
elseif ($op === 'delete') {
    try {
        // Validate input data
        if (empty($inputData['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input data']);
            exit;
        }

        // Check if user is admin
        if ($userRole !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }

        // Prepare DELETE statement
        $stmt = $pdo->prepare('DELETE FROM فرق WHERE id = :id');
        $stmt->bindParam(':id', $inputData['id']);
        $stmt->execute();
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'فرق deleted successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Handle unknown operation
else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid operation']);
}