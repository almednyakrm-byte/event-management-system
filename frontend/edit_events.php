**edit_events.php**

<?php
// Session validation
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get event ID from URL
$id = $_GET['id'];

// Fetch existing event details via GET
$url = '../backend/events.php?id=' . $id;
$response = file_get_contents($url);
$data = json_decode($response, true);

// Check if data exists
if ($data) {
    // Extract event details
    $event_name = $data['event_name'];
    $event_date = $data['event_date'];
    $event_time = $data['event_time'];
    $event_location = $data['event_location'];
    $event_description = $data['event_description'];
} else {
    echo "Error: Event not found.";
    exit;
}

// Include Tailwind CSS
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto p-4 bg-white rounded-md shadow-md">
        <h2 class="text-lg font-bold text-emerald-600 mb-4">Edit Event</h2>
        <form id="edit-event-form">
            <div class="mb-4">
                <label for="event_name" class="block text-sm font-bold text-gray-700 mb-2">Event Name:</label>
                <input type="text" id="event_name" name="event_name" class="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:border-teal-500" value="<?php echo $event_name; ?>">
            </div>
            <div class="mb-4">
                <label for="event_date" class="block text-sm font-bold text-gray-700 mb-2">Event Date:</label>
                <input type="date" id="event_date" name="event_date" class="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:border-teal-500" value="<?php echo $event_date; ?>">
            </div>
            <div class="mb-4">
                <label for="event_time" class="block text-sm font-bold text-gray-700 mb-2">Event Time:</label>
                <input type="time" id="event_time" name="event_time" class="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:border-teal-500" value="<?php echo $event_time; ?>">
            </div>
            <div class="mb-4">
                <label for="event_location" class="block text-sm font-bold text-gray-700 mb-2">Event Location:</label>
                <input type="text" id="event_location" name="event_location" class="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:border-teal-500" value="<?php echo $event_location; ?>">
            </div>
            <div class="mb-4">
                <label for="event_description" class="block text-sm font-bold text-gray-700 mb-2">Event Description:</label>
                <textarea id="event_description" name="event_description" class="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:border-teal-500"><?php echo $event_description; ?></textarea>
            </div>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-md">Update Event</button>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#edit-event-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'PUT',
                    url: '../backend/events.php',
                    data: formData,
                    success: function(response) {
                        if (response === 'success') {
                            window.location.href = 'list_events.php';
                        } else {
                            alert('Error updating event.');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>

**events.php (backend)**

<?php
// Check if event ID is set
if (isset($_GET['id'])) {
    // Connect to database
    $conn = new mysqli('localhost', 'username', 'password', 'database');
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Get event ID
    $id = $_GET['id'];
    
    // Query to fetch event details
    $query = "SELECT * FROM events WHERE id = '$id'";
    $result = $conn->query($query);
    
    // Check if event exists
    if ($result->num_rows > 0) {
        // Fetch event details
        $row = $result->fetch_assoc();
        
        // Output event details as JSON
        echo json_encode($row);
    } else {
        echo "Error: Event not found.";
    }
    
    // Close database connection
    $conn->close();
} else {
    echo "Error: Event ID not set.";
}
?>

**Note:** This code assumes you have a database connection set up and a table named `events` with columns `id`, `event_name`, `event_date`, `event_time`, `event_location`, and `event_description`. You'll need to modify the code to match your database schema and connection settings. Additionally, this code uses a simple `PUT` request to update the event, but you may want to consider using a more secure method, such as JSON Web Tokens or a secure API key.