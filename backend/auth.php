<?php
// Start the session to handle user authentication
session_start();

// Import the database connection
require_once 'db.php';

// Check if the request method is GET or POST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check the current session status
    if (isset($_SESSION['user_id'])) {
        // User is logged in, return the user ID
        echo json_encode(['status' => 'logged_in', 'user_id' => $_SESSION['user_id']]);
    } else {
        // User is not logged in, return a not logged in status
        echo json_encode(['status' => 'not_logged_in']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check the action parameter
    if (isset($_POST['action'])) {
        // Handle login action
        if ($_POST['action'] === 'login') {
            // Check if the username and password fields are set
            if (isset($_POST['username']) && isset($_POST['password'])) {
                // Prepare the SQL query to select the user by username
                $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
                $stmt->bind_param('s', $_POST['username']);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();

                // Check if the user exists and the password is correct
                if ($user && password_verify($_POST['password'], $user['password'])) {
                    // Login successful, set the user ID in the session
                    $_SESSION['user_id'] = $user['id'];
                    echo json_encode(['status' => 'login_success']);
                } else {
                    // Login failed, return an error message
                    echo json_encode(['status' => 'login_failed', 'error' => 'Invalid username or password']);
                }
            } else {
                // Missing username or password field, return an error message
                echo json_encode(['status' => 'login_failed', 'error' => 'Please fill in all fields']);
            }
        } 
        // Handle register action
        elseif ($_POST['action'] === 'register') {
            // Check if the username, email, and password fields are set
            if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
                // Check if the username, email, and password fields are not empty
                if (!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password'])) {
                    // Prepare the SQL query to insert a new user
                    $stmt = $db->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
                    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt->bind_param('sss', $_POST['username'], $_POST['email'], $password_hash);
                    if ($stmt->execute()) {
                        // Registration successful, return a success message
                        echo json_encode(['status' => 'register_success']);
                    } else {
                        // Registration failed, return an error message
                        echo json_encode(['status' => 'register_failed', 'error' => 'Failed to register user']);
                    }
                } else {
                    // Empty username, email, or password field, return an error message
                    echo json_encode(['status' => 'register_failed', 'error' => 'Please fill in all fields']);
                }
            } else {
                // Missing username, email, or password field, return an error message
                echo json_encode(['status' => 'register_failed', 'error' => 'Please fill in all fields']);
            }
        } 
        // Handle logout action
        elseif ($_POST['action'] === 'logout') {
            // Unset the user ID from the session
            unset($_SESSION['user_id']);
            // Destroy the session
            session_destroy();
            echo json_encode(['status' => 'logout_success']);
        }
    }
} else {
    // Invalid request method, return an error message
    echo json_encode(['status' => 'error', 'error' => 'Invalid request method']);
}