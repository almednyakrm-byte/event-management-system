**create_programs.php**

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

// Include header and navigation
include_once 'header.php';
include_once 'nav.php';

// Include form script
include_once 'create_programs_form.php';
?>

<?php
// Include footer
include_once 'footer.php';
?>


**create_programs_form.php**

<?php
// Define form fields
$program_name = '';
$program_description = '';
$program_start_date = '';
$program_end_date = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $program_name = filter_var($_POST['program_name'], FILTER_SANITIZE_STRING);
    $program_description = filter_var($_POST['program_description'], FILTER_SANITIZE_STRING);
    $program_start_date = filter_var($_POST['program_start_date'], FILTER_SANITIZE_STRING);
    $program_end_date = filter_var($_POST['program_end_date'], FILTER_SANITIZE_STRING);

    // AJAX request to create program
    $url = '../backend/programs.php';
    $data = array(
        'program_name' => $program_name,
        'program_description' => $program_description,
        'program_start_date' => $program_start_date,
        'program_end_date' => $program_end_date
    );

    $options = array(
        'http' => array(
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );

    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === 'success') {
        // Redirect back to list page
        header('Location: list_programs.php');
        exit;
    } else {
        // Display error message
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">';
        echo '<strong class="font-bold">Error!</strong> ' . $response;
        echo '</div>';
    }
}
?>

<!-- Form -->
<div class="max-w-md mx-auto p-4 bg-white rounded-lg shadow-md">
    <h2 class="text-lg font-bold text-slate-900 mb-4">Create Program</h2>
    <form id="create-program-form" method="post">
        <div class="mb-4">
            <label for="program_name" class="block text-sm font-medium text-slate-700">Program Name</label>
            <input type="text" id="program_name" name="program_name" class="block w-full p-2 mt-1 text-sm text-slate-900 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
        </div>
        <div class="mb-4">
            <label for="program_description" class="block text-sm font-medium text-slate-700">Program Description</label>
            <textarea id="program_description" name="program_description" class="block w-full p-2 mt-1 text-sm text-slate-900 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required></textarea>
        </div>
        <div class="mb-4">
            <label for="program_start_date" class="block text-sm font-medium text-slate-700">Program Start Date</label>
            <input type="date" id="program_start_date" name="program_start_date" class="block w-full p-2 mt-1 text-sm text-slate-900 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
        </div>
        <div class="mb-4">
            <label for="program_end_date" class="block text-sm font-medium text-slate-700">Program End Date</label>
            <input type="date" id="program_end_date" name="program_end_date" class="block w-full p-2 mt-1 text-sm text-slate-900 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
        </div>
        <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Create Program</button>
    </form>
</div>

<!-- JavaScript -->
<script>
    $(document).ready(function() {
        $('#create-program-form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '../backend/programs.php',
                data: $(this).serialize(),
                success: function(response) {
                    if (response === 'success') {
                        window.location.href = 'list_programs.php';
                    } else {
                        alert(response);
                    }
                }
            });
        });
    });
</script>


**header.php**

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body>
    <?php include_once 'nav.php'; ?>
    <div class="container mx-auto p-4">
        <?php include_once 'create_programs.php'; ?>
    </div>
</body>
</html>


**nav.php**

<nav class="bg-slate-900 py-4">
    <div class="container mx-auto p-4 flex justify-between items-center">
        <a href="#" class="text-lg font-bold text-white">Programs</a>
        <ul class="flex items-center space-x-4">
            <li><a href="#" class="text-white hover:text-indigo-500">Home</a></li>
            <li><a href="#" class="text-white hover:text-indigo-500">About</a></li>
            <li><a href="#" class="text-white hover:text-indigo-500">Contact</a></li>
        </ul>
    </div>
</nav>


**footer.php**

<footer class="bg-slate-900 py-4">
    <div class="container mx-auto p-4 text-center text-white">
        &copy; 2023 Programs. All rights reserved.
    </div>
</footer>


**programs.php (backend)**

<?php
// Database connection
$conn = new mysqli('localhost', 'username', 'password', 'database');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create program
if (isset($_POST['program_name']) && isset($_POST['program_description']) && isset($_POST['program_start_date']) && isset($_POST['program_end_date'])) {
    $program_name = $_POST['program_name'];
    $program_description = $_POST['program_description'];
    $program_start_date = $_POST['program_start_date'];
    $program_end_date = $_POST['program_end_date'];

    $sql = "INSERT INTO programs (program_name, program_description, program_start_date, program_end_date) VALUES ('$program_name', '$program_description', '$program_start_date', '$program_end_date')";

    if ($conn->query($sql) === TRUE) {
        echo 'success';
    } else {
        echo 'Error: ' . $sql . '<br>' . $conn->error;
    }
}

$conn->close();
?>