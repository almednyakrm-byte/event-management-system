**edit_tickets.php**

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Get ticket ID from URL
$id = $_GET['id'];

// Fetch existing ticket record
$ticket = json_decode(file_get_contents('../backend/tickets.php?id=' . $id), true);

// Check if ticket exists
if (empty($ticket)) {
    echo 'Ticket not found.';
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ticket</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-200">
    <div class="max-w-md mx-auto p-4 bg-white rounded shadow-md">
        <h1 class="text-lg font-bold text-orange-500 mb-4">Edit Ticket</h1>
        <form id="edit-ticket-form">
            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
                <input type="text" id="title" name="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?= $ticket['title'] ?>">
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description:</label>
                <textarea id="description" name="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?= $ticket['description'] ?></textarea>
            </div>
            <div class="mb-4">
                <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
                <select id="status" name="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="open" <?= $ticket['status'] == 'open' ? 'selected' : '' ?>>Open</option>
                    <option value="closed" <?= $ticket['status'] == 'closed' ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>
            <button type="submit" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">Save Changes</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#edit-ticket-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'PUT',
                    url: '../backend/tickets.php',
                    data: formData,
                    success: function() {
                        window.location.href = 'list_mod_slug.php';
                    }
                });
            });
        });
    </script>
</body>
</html>


**Note:** Replace `list_mod_slug.php` with the actual URL of the list page for the current module. Also, make sure to update the `../backend/tickets.php` URL to match your actual backend API endpoint.