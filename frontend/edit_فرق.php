**edit_فرق.php**

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Get ID from URL
$id = $_GET['id'];

// Fetch existing record details via GET
$url = '../backend/فرق.php?id=' . $id;
$response = file_get_contents($url);
$data = json_decode($response, true);

// Check if data is available
if ($data) {
    $name = $data['name'];
    $description = $data['description'];
} else {
    header('Location: list_فرق.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit فرق</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto p-4 bg-white rounded shadow-md">
        <h1 class="text-lg font-bold text-emerald-600 mb-4">Edit فرق</h1>
        <form id="edit-form" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name" class="block w-full p-2 border-gray-300 rounded-md" value="<?= $name ?>">
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea id="description" name="description" class="block w-full p-2 border-gray-300 rounded-md" rows="4"><?= $description ?></textarea>
            </div>
            <button type="submit" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">Update</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#edit-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'PUT',
                    url: '../backend/فرق.php',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            window.location.href = 'list_فرق.php';
                        } else {
                            alert('Error updating record');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr, status, error);
                    }
                });
            });
        });
    </script>
</body>
</html>


**backend/فرق.php**

<?php
// Check if ID is provided
if (!isset($_GET['id'])) {
    header('Location: list_فرق.php');
    exit;
}

// Get ID from URL
$id = $_GET['id'];

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Fetch existing record details
$query = "SELECT * FROM فرق WHERE id = '$id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Check if record exists
if ($row) {
    echo json_encode($row);
} else {
    header('Location: list_فرق.php');
    exit;
}

// Close database connection
mysqli_close($conn);
?>


Note: This code assumes you have a `login.php` page for user authentication and a `list_فرق.php` page for displaying the list of records. You should replace `../backend/فرق.php` with the actual URL of your backend script. Also, make sure to replace `mysqli` with your preferred database library (e.g., PDO).