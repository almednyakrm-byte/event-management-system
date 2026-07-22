**edit_invitations.php**

<?php
// Session validation
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get invitation ID from URL
$id = $_GET['id'];

// Fetch existing record details via GET
$existingRecord = json_decode(file_get_contents('../backend/invitations.php?id=' . $id), true);

// Check if record exists
if (empty($existingRecord)) {
    echo 'Record not found';
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Invitation</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-200">
    <div class="max-w-md mx-auto p-4 bg-white rounded-lg shadow-md">
        <h2 class="text-lg font-bold text-orange-500 mb-4">Edit Invitation</h2>
        <form id="edit-invitation-form">
            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                <input type="text" id="title" name="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?= $existingRecord['title'] ?>">
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <textarea id="description" name="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?= $existingRecord['description'] ?></textarea>
            </div>
            <div class="mb-4">
                <label for="date" class="block text-gray-700 text-sm font-bold mb-2">Date</label>
                <input type="date" id="date" name="date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?= $existingRecord['date'] ?>">
            </div>
            <button type="submit" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">Update</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#edit-invitation-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'PUT',
                    url: '../backend/invitations.php',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            window.location.href = 'list_invitations.php';
                        } else {
                            alert('Error updating invitation');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>


**invitations.php (backend)**

<?php
// Check if invitation ID is set
if (!isset($_GET['id'])) {
    echo json_encode(array('error' => 'Invalid request'));
    exit;
}

// Get invitation ID
$id = $_GET['id'];

// Connect to database
$conn = new mysqli('your_host', 'your_username', 'your_password', 'your_database');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get existing record details
$query = "SELECT * FROM invitations WHERE id = '$id'";
$result = $conn->query($query);

// Check if record exists
if ($result->num_rows > 0) {
    // Fetch record details
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(array('error' => 'Record not found'));
}

// Close database connection
$conn->close();
?>


**invitations.php (backend) - Update record**

<?php
// Check if invitation ID is set
if (!isset($_GET['id'])) {
    echo json_encode(array('error' => 'Invalid request'));
    exit;
}

// Get invitation ID
$id = $_GET['id'];

// Get form data
$title = $_POST['title'];
$description = $_POST['description'];
$date = $_POST['date'];

// Connect to database
$conn = new mysqli('your_host', 'your_username', 'your_password', 'your_database');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to update record
$query = "UPDATE invitations SET title = '$title', description = '$description', date = '$date' WHERE id = '$id'";
$result = $conn->query($query);

// Check if update was successful
if ($result) {
    echo json_encode(array('success' => true));
} else {
    echo json_encode(array('error' => 'Error updating invitation'));
}

// Close database connection
$conn->close();
?>