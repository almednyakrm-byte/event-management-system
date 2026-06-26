**create_حجز-تذاكر.php**

<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

// Include database connection
require_once '../config/database.php';

// Check if form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form data
    $data = array(
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'date' => $_POST['date'],
        'time' => $_POST['time'],
        'price' => $_POST['price'],
        'status' => $_POST['status']
    );

    // Validate form data
    $errors = array();
    if (empty($data['title'])) {
        $errors[] = 'Title is required';
    }
    if (empty($data['description'])) {
        $errors[] = 'Description is required';
    }
    if (empty($data['date'])) {
        $errors[] = 'Date is required';
    }
    if (empty($data['time'])) {
        $errors[] = 'Time is required';
    }
    if (empty($data['price'])) {
        $errors[] = 'Price is required';
    }
    if (empty($data['status'])) {
        $errors[] = 'Status is required';
    }

    // Check for errors
    if (!empty($errors)) {
        // Display errors
        foreach ($errors as $error) {
            echo '<div class="bg-red-500 text-white p-2 mb-4">' . $error . '</div>';
        }
    } else {
        // Insert data into database
        $query = "INSERT INTO حجز_تذاكر (title, description, date, time, price, status) VALUES (:title, :description, :date, :time, :price, :status)";
        $stmt = $pdo->prepare($query);
        $stmt->execute($data);

        // Redirect to list page
        header('Location: list_حجز-تذاكر.php');
        exit;
    }
}

// Include header
require_once '../includes/header.php';

// Include form
?>

<div class="container mx-auto p-4">
    <div class="bg-emerald-600 p-4 rounded-lg shadow-md">
        <h2 class="text-2xl text-white font-bold mb-4">Create New حجز_تذاكر</h2>
        <form id="create-form" method="post">
            <div class="mb-4">
                <label for="title" class="block text-white text-sm font-bold mb-2">Title:</label>
                <input type="text" id="title" name="title" class="bg-gray-100 text-gray-700 py-2 px-4 rounded-lg w-full" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-white text-sm font-bold mb-2">Description:</label>
                <textarea id="description" name="description" class="bg-gray-100 text-gray-700 py-2 px-4 rounded-lg w-full" required></textarea>
            </div>
            <div class="mb-4">
                <label for="date" class="block text-white text-sm font-bold mb-2">Date:</label>
                <input type="date" id="date" name="date" class="bg-gray-100 text-gray-700 py-2 px-4 rounded-lg w-full" required>
            </div>
            <div class="mb-4">
                <label for="time" class="block text-white text-sm font-bold mb-2">Time:</label>
                <input type="time" id="time" name="time" class="bg-gray-100 text-gray-700 py-2 px-4 rounded-lg w-full" required>
            </div>
            <div class="mb-4">
                <label for="price" class="block text-white text-sm font-bold mb-2">Price:</label>
                <input type="number" id="price" name="price" class="bg-gray-100 text-gray-700 py-2 px-4 rounded-lg w-full" required>
            </div>
            <div class="mb-4">
                <label for="status" class="block text-white text-sm font-bold mb-2">Status:</label>
                <select id="status" name="status" class="bg-gray-100 text-gray-700 py-2 px-4 rounded-lg w-full" required>
                    <option value="">Select Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded-lg">Create</button>
        </form>
    </div>
</div>

<?php
// Include footer
require_once '../includes/footer.php';
?>

<script>
    $(document).ready(function() {
        $('#create-form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '../backend/حجز-تذاكر.php',
                data: $(this).serialize(),
                success: function(response) {
                    if (response === 'success') {
                        window.location.href = 'list_حجز-تذاكر.php';
                    } else {
                        alert('Error creating new record');
                    }
                }
            });
        });
    });
</script>

**Note:** This code assumes you have a `config/database.php` file that contains your database connection settings, and a `backend/حجز-تذاكر.php` file that handles the form submission and inserts the data into the database. You will need to create these files and modify the code to match your specific database schema and backend logic.