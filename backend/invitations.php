<?php
// Import database connection
require_once 'db.php';

// Initialize database connection
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);

// Function to validate user role
function validateUserRole($role) {
    // For demonstration purposes, assume a logged-in user with admin role
    // Replace with actual authentication logic
    return $role === 'admin' || $role === 'user';
}

// Function to get user role from session
function getUserRole() {
    // For demonstration purposes, assume a logged-in user with admin role
    // Replace with actual authentication logic
    return $_SESSION['role'] ?? null;
}

// Handle HTTP requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Validate user role
    $userRole = getUserRole();
    if (!validateUserRole($userRole)) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    // Retrieve invitations
    $stmt = $pdo->prepare('SELECT * FROM invitations');
    $stmt->execute();
    $invitations = $stmt->fetchAll();

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($invitations);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate user role
    $userRole = getUserRole();
    if (!validateUserRole($userRole)) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    // Read input data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input data
    if (!isset($data['name']) || !isset($data['email'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request data']);
        exit;
    }

    // Insert invitation
    $stmt = $pdo->prepare('INSERT INTO invitations (name, email) VALUES (:name, :email)');
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->execute();

    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Invitation created successfully']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Validate user role (admin-only for edits)
    $userRole = getUserRole();
    if ($userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Read input data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input data
    if (!isset($data['id']) || !isset($data['name']) || !isset($data['email'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request data']);
        exit;
    }

    // Update invitation
    $stmt = $pdo->prepare('UPDATE invitations SET name = :name, email = :email WHERE id = :id');
    $stmt->bindParam(':id', $data['id']);
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->execute();

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Invitation updated successfully']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Validate user role (admin-only for deletions)
    $userRole = getUserRole();
    if ($userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Read input data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input data
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request data']);
        exit;
    }

    // Delete invitation
    $stmt = $pdo->prepare('DELETE FROM invitations WHERE id = :id');
    $stmt->bindParam(':id', $data['id']);
    $stmt->execute();

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Invitation deleted successfully']);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}