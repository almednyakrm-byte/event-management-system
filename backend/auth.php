<?php
// Start the session
session_start();

// Include the database connection file
require_once 'db.php';

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // If the user is logged in, return a JSON response with their user data
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM users WHERE id = '$user_id'";
    $result = mysqli_query($conn, $query);
    $user_data = mysqli_fetch_assoc($result);
    echo json_encode(array('status' => 'logged_in', 'user_data' => $user_data));
    exit;
}

// Handle the login request
if (isset($_POST['action']) && $_POST['action'] == 'login') {
    // Check if the username and password are set
    if (isset($_POST['username']) && isset($_POST['password'])) {
        // Sanitize the input fields
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        // Prepare the SQL query to select the user
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        $user_data = mysqli_fetch_assoc($result);

        // Check if the user exists
        if ($user_data) {
            // Hash the input password with the user's salt
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // Check if the hashed password matches the user's password
            if (password_verify($password, $user_data['password'])) {
                // If the password is correct, log the user in
                $_SESSION['user_id'] = $user_data['id'];
                echo json_encode(array('status' => 'logged_in'));
            } else {
                // If the password is incorrect, return an error message
                echo json_encode(array('status' => 'error', 'message' => 'Invalid password'));
            }
        } else {
            // If the user does not exist, return an error message
            echo json_encode(array('status' => 'error', 'message' => 'Invalid username'));
        }
    } else {
        // If the username or password is missing, return an error message
        echo json_encode(array('status' => 'error', 'message' => 'Missing username or password'));
    }
} elseif (isset($_POST['action']) && $_POST['action'] == 'register') {
    // Check if the username, email, and password are set
    if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
        // Sanitize the input fields
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        // Check if the username and email are valid
        if (preg_match('/^[a-zA-Z0-9]+$/', $username) && preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
            // Prepare the SQL query to insert the new user
            $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'sss', $username, $email, password_hash($password, PASSWORD_DEFAULT));
            mysqli_stmt_execute($stmt);
            $user_id = mysqli_insert_id($conn);
            // Log the user in
            $_SESSION['user_id'] = $user_id;
            echo json_encode(array('status' => 'logged_in'));
        } else {
            // If the username or email is invalid, return an error message
            echo json_encode(array('status' => 'error', 'message' => 'Invalid username or email'));
        }
    } else {
        // If the username, email, or password is missing, return an error message
        echo json_encode(array('status' => 'error', 'message' => 'Missing username, email, or password'));
    }
} else {
    // If the action is not set, return an error message
    echo json_encode(array('status' => 'error', 'message' => 'Invalid action'));
}

// Handle the logout request
if (isset($_POST['action']) && $_POST['action'] == 'logout') {
    // Destroy the session
    session_destroy();
    echo json_encode(array('status' => 'logged_out'));
}

// Handle the session status request
if (isset($_GET['action']) && $_GET['action'] == 'session_status') {
    // Check if the user is logged in
    if (isset($_SESSION['user_id'])) {
        echo json_encode(array('status' => 'logged_in'));
    } else {
        echo json_encode(array('status' => 'logged_out'));
    }
}