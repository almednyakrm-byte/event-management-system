**create_tickets.php**

<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Include header
include 'header.php';

// Include navigation
include 'navigation.php';

?>

<div class="container mx-auto p-4 pt-6 md:p-6 lg:px-12 xl:px-24">
    <div class="bg-white rounded-lg shadow-md p-4 md:p-6 lg:p-8 xl:p-12">
        <h2 class="text-lg font-bold text-orange-500 mb-4">Create Ticket</h2>
        <form id="create-ticket-form">
            <div class="mb-4">
                <label for="subject" class="block text-gray-200 text-sm font-bold mb-2">Subject</label>
                <input type="text" id="subject" name="subject" class="block w-full p-2 text-gray-200 border border-gray-200 rounded-lg focus:outline-none focus:ring-orange-500 focus:border-orange-500" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-200 text-sm font-bold mb-2">Description</label>
                <textarea id="description" name="description" class="block w-full p-2 text-gray-200 border border-gray-200 rounded-lg focus:outline-none focus:ring-orange-500 focus:border-orange-500" required></textarea>
            </div>
            <div class="mb-4">
                <label for="priority" class="block text-gray-200 text-sm font-bold mb-2">Priority</label>
                <select id="priority" name="priority" class="block w-full p-2 text-gray-200 border border-gray-200 rounded-lg focus:outline-none focus:ring-orange-500 focus:border-orange-500" required>
                    <option value="">Select Priority</option>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="status" class="block text-gray-200 text-sm font-bold mb-2">Status</label>
                <select id="status" name="status" class="block w-full p-2 text-gray-200 border border-gray-200 rounded-lg focus:outline-none focus:ring-orange-500 focus:border-orange-500" required>
                    <option value="">Select Status</option>
                    <option value="Open">Open</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Resolved">Resolved</option>
                </select>
            </div>
            <button type="submit" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg">Create Ticket</button>
        </form>
    </div>
</div>

<?php
// Include footer
include 'footer.php';
?>

<script>
    $(document).ready(function() {
        $('#create-ticket-form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: '../backend/tickets.php',
                data: formData,
                success: function(response) {
                    if (response === 'success') {
                        window.location.href = 'list_tickets.php';
                    } else {
                        alert('Error creating ticket');
                    }
                }
            });
        });
    });
</script>


**tickets.php (backend)**

<?php
// Include database connection
include 'db.php';

// Check if form data is submitted
if (isset($_POST['subject']) && isset($_POST['description']) && isset($_POST['priority']) && isset($_POST['status'])) {
    // Insert data into database
    $subject = $_POST['subject'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];
    $sql = "INSERT INTO tickets (subject, description, priority, status) VALUES ('$subject', '$description', '$priority', '$status')";
    if (mysqli_query($conn, $sql)) {
        echo 'success';
    } else {
        echo 'Error creating ticket';
    }
}
?>


**Note:** This is a basic example and does not include any validation or security measures. In a real-world application, you should always validate and sanitize user input to prevent SQL injection and other security vulnerabilities.