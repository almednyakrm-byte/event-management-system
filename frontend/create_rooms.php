**create_rooms.php**

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Include header and navigation
include 'header.php';
include 'navigation.php';
?>

<div class="container mx-auto p-4 pt-6 md:p-6 lg:p-12">
    <div class="flex justify-center">
        <div class="w-full xl:w-3/5 p-6">
            <h2 class="text-lg font-bold text-emerald-600 mb-4">Create New Room</h2>
            <form id="create-room-form" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Room Name:</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" placeholder="Room Name">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Room Description:</label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="description" placeholder="Room Description"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="capacity">Room Capacity:</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="capacity" type="number" placeholder="Room Capacity">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="price">Room Price:</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="price" type="number" placeholder="Room Price">
                </div>
                <button type="submit" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Create Room</button>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#create-room-form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: '../backend/rooms.php',
                data: formData,
                success: function(response) {
                    if (response === 'success') {
                        window.location.href = 'list_rooms.php';
                    } else {
                        alert('Error creating room');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error creating room: ' + error);
                }
            });
        });
    });
</script>

<?php
// Include footer
include 'footer.php';
?>


**rooms.php (backend)**

<?php
// Include database connection
include 'db.php';

// Check if form data is submitted
if (isset($_POST['name']) && isset($_POST['description']) && isset($_POST['capacity']) && isset($_POST['price'])) {
    // Prepare SQL query
    $sql = "INSERT INTO rooms (name, description, capacity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $_POST['name'], $_POST['description'], $_POST['capacity'], $_POST['price']);
    
    // Execute query
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'Error creating room';
    }
    
    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo 'Invalid form data';
}
?>