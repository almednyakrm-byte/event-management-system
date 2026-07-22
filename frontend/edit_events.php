<?php
// edit_events.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: list_events.php');
    exit;
}

$id = $_GET['id'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="max-w-md mx-auto mt-10 p-4 bg-gray-200 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-orange-500 mb-4">Edit Event</h2>
        <form id="edit-event-form">
            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                <input type="text" id="title" name="title" class="block w-full p-2 bg-gray-200 border border-gray-200 rounded-lg focus:outline-none focus:ring-orange-500 focus:border-orange-500">
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <textarea id="description" name="description" class="block w-full p-2 bg-gray-200 border border-gray-200 rounded-lg focus:outline-none focus:ring-orange-500 focus:border-orange-500"></textarea>
            </div>
            <div class="mb-4">
                <label for="date" class="block text-gray-700 text-sm font-bold mb-2">Date</label>
                <input type="date" id="date" name="date" class="block w-full p-2 bg-gray-200 border border-gray-200 rounded-lg focus:outline-none focus:ring-orange-500 focus:border-orange-500">
            </div>
            <button type="submit" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg">Update Event</button>
        </form>
    </div>

    <script>
        // Fetch existing record details
        fetch('../backend/events.php?id=<?= $id ?>')
            .then(response => response.json())
            .then(data => {
                document.getElementById('title').value = data.title;
                document.getElementById('description').value = data.description;
                document.getElementById('date').value = data.date;
            });

        // Submit form using AJAX
        document.getElementById('edit-event-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            fetch('../backend/events.php', {
                method: 'PUT',
                body: JSON.stringify(Object.fromEntries(formData)),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'list_events.php';
                } else {
                    console.error(data.error);
                }
            })
            .catch(error => console.error(error));
        });
    </script>
</body>
</html>