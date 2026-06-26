**edit_rooms.php**

<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Get room ID from URL
$id = $_GET['id'];

// Fetch existing room details via AJAX
$roomDetails = json_decode(file_get_contents('../backend/rooms.php?id=' . $id), true);

// Set page title and mod slug
$pageTitle = 'Edit Room';
$modSlug = 'rooms';

// Include header and navigation
include 'header.php';
?>

<!-- Main content -->
<main class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <h1 class="text-3xl font-bold mb-4"><?= $pageTitle ?></h1>

    <!-- Form -->
    <form id="edit-room-form" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
            <input type="text" id="name" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?= $roomDetails['name'] ?>">
        </div>
        <div class="mb-4">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description:</label>
            <textarea id="description" name="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?= $roomDetails['description'] ?></textarea>
        </div>
        <div class="mb-4">
            <label for="capacity" class="block text-gray-700 text-sm font-bold mb-2">Capacity:</label>
            <input type="number" id="capacity" name="capacity" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?= $roomDetails['capacity'] ?>">
        </div>
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Update Room</button>
    </form>
</main>

<!-- JavaScript -->
<script>
    // Fetch existing room details via AJAX
    fetch('../backend/rooms.php?id=' + <?= $id ?>)
        .then(response => response.json())
        .then(data => {
            document.getElementById('name').value = data.name;
            document.getElementById('description').value = data.description;
            document.getElementById('capacity').value = data.capacity;
        })
        .catch(error => console.error(error));

    // Submit form via AJAX
    document.getElementById('edit-room-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch('../backend/rooms.php', {
            method: 'PUT',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'list_' + '<?= $modSlug ?>' + '.php';
                } else {
                    console.error(data.error);
                }
            })
            .catch(error => console.error(error));
    });
</script>

<!-- Include footer -->
<?php include 'footer.php'; ?>


**rooms.php (backend)**

<?php
// Check if room ID is set
if (!isset($_GET['id'])) {
    http_response_code(404);
    exit;
}

// Connect to database
$conn = new mysqli('localhost', 'username', 'password', 'database');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get room details
$query = "SELECT * FROM rooms WHERE id = " . $_GET['id'];
$result = $conn->query($query);

// Check if room exists
if ($result->num_rows > 0) {
    // Fetch room details
    $room = $result->fetch_assoc();
    echo json_encode($room);
} else {
    http_response_code(404);
    exit;
}

// Close database connection
$conn->close();
?>


**list_rooms.php (example)**

<?php
// Include header and navigation
include 'header.php';

// Get room list
$roomList = json_decode(file_get_contents('../backend/rooms.php'), true);

// Display room list
foreach ($roomList as $room) {
    echo '<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">';
    echo '<h2 class="text-2xl font-bold mb-2">' . $room['name'] . '</h2>';
    echo '<p>' . $room['description'] . '</p>';
    echo '<p>Capacity: ' . $room['capacity'] . '</p>';
    echo '</div>';
}

// Include footer
include 'footer.php';
?>