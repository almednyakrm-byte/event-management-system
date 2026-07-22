**create_invitations.php**

<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

// Include header and navigation
include 'header.php';
include 'navigation.php';
?>

<div class="container mx-auto p-4 pt-6 md:p-6 lg:p-12 xl:p-12 2xl:p-12">
    <div class="bg-white rounded-lg shadow-md p-4 md:p-6 lg:p-8 xl:p-8 2xl:p-8">
        <h2 class="text-lg font-bold text-orange-500 mb-4">Create Invitation</h2>
        <form id="create-invitation-form">
            <div class="mb-4">
                <label for="title" class="block text-gray-200 text-sm font-bold mb-2">Title</label>
                <input type="text" id="title" name="title" class="block w-full p-2 pl-10 text-gray-700 bg-gray-200 border border-gray-200 rounded-lg focus:ring-orange-500 focus:border-orange-500" placeholder="Enter title">
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-200 text-sm font-bold mb-2">Description</label>
                <textarea id="description" name="description" class="block w-full p-2 pl-10 text-gray-700 bg-gray-200 border border-gray-200 rounded-lg focus:ring-orange-500 focus:border-orange-500" placeholder="Enter description"></textarea>
            </div>
            <div class="mb-4">
                <label for="date" class="block text-gray-200 text-sm font-bold mb-2">Date</label>
                <input type="date" id="date" name="date" class="block w-full p-2 pl-10 text-gray-700 bg-gray-200 border border-gray-200 rounded-lg focus:ring-orange-500 focus:border-orange-500">
            </div>
            <div class="mb-4">
                <label for="time" class="block text-gray-200 text-sm font-bold mb-2">Time</label>
                <input type="time" id="time" name="time" class="block w-full p-2 pl-10 text-gray-700 bg-gray-200 border border-gray-200 rounded-lg focus:ring-orange-500 focus:border-orange-500">
            </div>
            <div class="mb-4">
                <label for="location" class="block text-gray-200 text-sm font-bold mb-2">Location</label>
                <input type="text" id="location" name="location" class="block w-full p-2 pl-10 text-gray-700 bg-gray-200 border border-gray-200 rounded-lg focus:ring-orange-500 focus:border-orange-500" placeholder="Enter location">
            </div>
            <button type="submit" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">Create Invitation</button>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#create-invitation-form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: '../backend/invitations.php',
                data: formData,
                success: function(response) {
                    if (response === 'success') {
                        window.location.href = 'list_invitations.php';
                    } else {
                        alert('Error creating invitation');
                    }
                }
            });
        });
    });
</script>

<?php
// Include footer
include 'footer.php';
?>


**invitations.php (backend)**

<?php
// Include database connection
include 'db.php';

// Check if form data is submitted
if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['date']) && isset($_POST['time']) && isset($_POST['location'])) {
    // Prepare SQL query
    $query = "INSERT INTO invitations (title, description, date, time, location) VALUES (?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sssss", $_POST['title'], $_POST['description'], $_POST['date'], $_POST['time'], $_POST['location']);
    // Execute query
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'Error creating invitation';
    }
    // Close statement
    $stmt->close();
}
?>


Note: This code assumes you have a `db.php` file that includes your database connection settings and a `footer.php` file that includes your footer HTML. You'll need to modify the code to fit your specific needs. Additionally, this code uses a simple AJAX request to send the form data to the backend, but you may want to consider using a more robust library like jQuery or Axios for production use.