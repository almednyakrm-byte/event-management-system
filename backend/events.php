<?php
// Import database connection
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Handle different HTTP methods
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Retrieve all events or a single event by id
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $pdo->prepare('SELECT * FROM events WHERE id = :id');
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $event = $stmt->fetch();
            if ($event) {
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode($event);
            } else {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Event not found']);
            }
        } else {
            $stmt = $pdo->prepare('SELECT * FROM events');
            $stmt->execute();
            $events = $stmt->fetchAll();
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($events);
        }
        break;

    case 'POST':
        // Create a new event
        if ($_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['name']) || !isset($data['date']) || !isset($data['description'])) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid request data']);
            exit;
        }
        $stmt = $pdo->prepare('INSERT INTO events (name, date, description) VALUES (:name, :date, :description)');
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':date', $data['date']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->execute();
        $eventId = $pdo->lastInsertId();
        http_response_code(201);
        header('Content-Type: application/json');
        echo json_encode(['id' => $eventId]);
        break;

    case 'PUT':
        // Update an existing event
        if ($_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }
        $id = $_GET['id'];
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($id) || !isset($data['name']) || !isset($data['date']) || !isset($data['description'])) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid request data']);
            exit;
        }
        $stmt = $pdo->prepare('UPDATE events SET name = :name, date = :date, description = :description WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':date', $data['date']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->execute();
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Event updated successfully']);
        break;

    case 'DELETE':
        // Delete an existing event
        if ($_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }
        $id = $_GET['id'];
        if (!isset($id)) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid request data']);
            exit;
        }
        $stmt = $pdo->prepare('DELETE FROM events WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Event deleted successfully']);
        break;

    default:
        http_response_code(405);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Method not allowed']);
        break;
}