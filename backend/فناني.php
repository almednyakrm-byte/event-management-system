<?php

// Import database connection settings
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    http_response_code(401);
    echo json_encode(array('error' => 'Unauthorized'));
    exit;
}

// Get input data from JSON body
$input = json_decode(file_get_contents('php://input'), true);

// Define allowed roles for CRUD operations
$allowedRoles = array('admin' => array('create', 'read', 'update', 'delete'), 'user' => array('read'));

// Define HTTP response status codes
$statusCodes = array(
    'success' => 200,
    'created' => 201,
    'updated' => 200,
    'deleted' => 204,
    'not_found' => 404,
    'invalid_request' => 400,
    'unauthorized' => 401
);

// Define HTTP response headers
$headers = array(
    'Content-Type: application/json'
);

// Define PDO database connection
$db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);

// Define CRUD operations
function getArtisans($db, $input) {
    // Validate input parameters
    if (!isset($input['limit']) || !isset($input['offset'])) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid request'));
        exit;
    }

    // Sanitize input parameters
    $limit = (int) $input['limit'];
    $offset = (int) $input['offset'];

    // Prepare SQL query
    $stmt = $db->prepare('SELECT * FROM artisans LIMIT :limit OFFSET :offset');
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch and return results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    echo json_encode($results);
}

function createArtisan($db, $input) {
    // Validate input parameters
    if (!isset($input['name']) || !isset($input['email'])) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid request'));
        exit;
    }

    // Sanitize input parameters
    $name = $db->quote($input['name']);
    $email = $db->quote($input['email']);

    // Prepare SQL query
    $stmt = $db->prepare('INSERT INTO artisans (name, email) VALUES (:name, :email)');
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    // Return created ID
    $id = $db->lastInsertId();
    http_response_code(201);
    echo json_encode(array('id' => $id));
}

function updateArtisan($db, $input) {
    // Validate input parameters
    if (!isset($input['id']) || !isset($input['name']) || !isset($input['email'])) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid request'));
        exit;
    }

    // Sanitize input parameters
    $id = (int) $input['id'];
    $name = $db->quote($input['name']);
    $email = $db->quote($input['email']);

    // Prepare SQL query
    $stmt = $db->prepare('UPDATE artisans SET name = :name, email = :email WHERE id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    // Return updated ID
    http_response_code(200);
    echo json_encode(array('id' => $id));
}

function deleteArtisan($db, $input) {
    // Validate input parameters
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid request'));
        exit;
    }

    // Sanitize input parameters
    $id = (int) $input['id'];

    // Prepare SQL query
    $stmt = $db->prepare('DELETE FROM artisans WHERE id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Return deleted ID
    http_response_code(204);
    echo json_encode(array('id' => $id));
}

// Handle HTTP requests
if (isset($input['action'])) {
    switch ($input['action']) {
        case 'get':
            getArtisans($db, $input);
            break;
        case 'create':
            if (!in_array($_SESSION['role'], $allowedRoles['admin'])) {
                http_response_code(401);
                echo json_encode(array('error' => 'Unauthorized'));
                exit;
            }
            createArtisan($db, $input);
            break;
        case 'update':
            if (!in_array($_SESSION['role'], $allowedRoles['admin'])) {
                http_response_code(401);
                echo json_encode(array('error' => 'Unauthorized'));
                exit;
            }
            updateArtisan($db, $input);
            break;
        case 'delete':
            if (!in_array($_SESSION['role'], $allowedRoles['admin'])) {
                http_response_code(401);
                echo json_encode(array('error' => 'Unauthorized'));
                exit;
            }
            deleteArtisan($db, $input);
            break;
        default:
            http_response_code(400);
            echo json_encode(array('error' => 'Invalid request'));
            exit;
    }
} else {
    http_response_code(400);
    echo json_encode(array('error' => 'Invalid request'));
    exit;
}

// Close database connection
$db = null;

?>