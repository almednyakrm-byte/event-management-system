<?php
// Session validation
session_start();
if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

// Current user info
$current_user = $_SESSION['username'];

// HTML content
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-200">
    <header class="bg-orange-500 text-white p-4">
        <nav class="container mx-auto flex justify-between">
            <a href="index.php" class="text-lg font-bold">Back to Index</a>
            <span>Welcome, <?php echo $current_user; ?></span>
            <a href="logout.php" class="text-lg font-bold">Logout</a>
        </nav>
    </header>
    <main class="container mx-auto p-4 pt-6 md:p-6 lg:p-12 xl:p-24">
        <h1 class="text-3xl font-bold mb-4">Events List</h1>
        <div class="flex justify-between mb-4">
            <a href="create_events.php" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">Add New Item</a>
            <input type="search" id="search" class="px-4 py-2 border border-gray-400 rounded" placeholder="Search...">
        </div>
        <table id="events-table" class="w-full table-auto border border-gray-400">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Event Name</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody id="events-tbody">
                <!-- Table content will be populated via AJAX -->
            </tbody>
        </table>
    </main>

    <script>
        // Fetch API to get events list
        fetch('../backend/events.php')
            .then(response => response.json())
            .then(data => {
                const eventsTbody = document.getElementById('events-tbody');
                eventsTbody.innerHTML = '';
                data.forEach(event => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-4 py-2">${event.id}</td>
                        <td class="px-4 py-2">${event.name}</td>
                        <td class="px-4 py-2">
                            <a href="edit_events.php?id=${event.id}" class="text-orange-500 hover:text-orange-700">Edit</a>
                            <button class="text-red-500 hover:text-red-700" onclick="deleteEvent(${event.id})">Delete</button>
                        </td>
                    `;
                    eventsTbody.appendChild(row);
                });
            });

        // Delete event via AJAX
        function deleteEvent(id) {
            fetch('../backend/events.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove deleted event from table
                    const eventsTbody = document.getElementById('events-tbody');
                    const rows = eventsTbody.children;
                    for (let i = 0; i < rows.length; i++) {
                        if (rows[i].children[0].textContent == id) {
                            eventsTbody.removeChild(rows[i]);
                            break;
                        }
                    }
                }
            });
        }

        // Search bar filtering
        const searchInput = document.getElementById('search');
        searchInput.addEventListener('input', () => {
            const searchValue = searchInput.value.toLowerCase();
            const rows = document.getElementById('events-tbody').children;
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const eventName = row.children[1].textContent.toLowerCase();
                if (eventName.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>