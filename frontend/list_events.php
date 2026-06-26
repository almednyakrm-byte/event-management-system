**list_events.php**

<?php
session_start();

// Check if user is authenticated
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .emerald-600 {
            color: #008E73;
        }
        .teal-500 {
            color: #0097A7;
        }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-md p-4">
        <nav class="flex justify-between">
            <a href="index.php" class="text-lg font-bold">Home</a>
            <div class="flex items-center">
                <p class="mr-2">Welcome, <?php echo $_SESSION['username']; ?></p>
                <a href="logout.php" class="text-red-600 hover:text-red-800">Logout</a>
            </div>
        </nav>
    </header>
    <main class="max-w-7xl mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4">Events</h1>
        <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='create_events.php'">Add New Item</button>
        <div class="mt-4">
            <input type="search" id="search" class="w-full p-2 pl-10 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600" placeholder="Search...">
            <div id="search-results"></div>
        </div>
        <table class="w-full mt-4">
            <thead>
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Title</th>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody id="events-table">
                <!-- Table rows will be populated here -->
            </tbody>
        </table>
    </main>

    <script>
        const searchInput = document.getElementById('search');
        const searchResults = document.getElementById('search-results');
        const eventsTable = document.getElementById('events-table');

        searchInput.addEventListener('input', async (e) => {
            const query = e.target.value;
            const response = await fetch('../backend/events.php', {
                method: 'GET',
                params: { query }
            });
            const data = await response.json();
            const html = data.map((event) => `
                <div class="bg-white shadow-md p-4 mb-4">
                    <h2 class="text-lg font-bold">${event.title}</h2>
                    <p>${event.date}</p>
                    <button class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='edit_events.php?id=${event.id}'">Edit</button>
                    <button class="bg-red-600 hover:bg-red-800 text-white font-bold py-2 px-4 rounded" onclick="deleteEvent(${event.id})">Delete</button>
                </div>
            `).join('');
            searchResults.innerHTML = html;
        });

        async function deleteEvent(id) {
            const response = await fetch('../backend/events.php', {
                method: 'DELETE',
                params: { id }
            });
            if (response.ok) {
                const html = await response.text();
                eventsTable.innerHTML = html;
            } else {
                alert('Error deleting event');
            }
        }

        async function fetchEvents() {
            const response = await fetch('../backend/events.php');
            const data = await response.json();
            const html = data.map((event) => `
                <tr>
                    <td class="px-4 py-2">${event.id}</td>
                    <td class="px-4 py-2">${event.title}</td>
                    <td class="px-4 py-2">${event.date}</td>
                    <td class="px-4 py-2">
                        <button class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='edit_events.php?id=${event.id}'">Edit</button>
                        <button class="bg-red-600 hover:bg-red-800 text-white font-bold py-2 px-4 rounded" onclick="deleteEvent(${event.id})">Delete</button>
                    </td>
                </tr>
            `).join('');
            eventsTable.innerHTML = html;
        }

        fetchEvents();
    </script>
</body>
</html>


**events.php (backend)**

<?php
require_once 'db.php';

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $results = [];
    $sql = "SELECT * FROM events WHERE title LIKE '%$query%' OR date LIKE '%$query%'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll();
    echo json_encode($results);
} elseif (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM events WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    echo 'Event deleted successfully';
} else {
    $sql = "SELECT * FROM events";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll();
    echo json_encode($results);
}
?>