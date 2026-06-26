**list_rooms.php**

<?php
// Session validation
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
    <title>Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .emerald-600 {
            color: #008E77;
        }
        .teal-500 {
            color: #0097A7;
        }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-md p-4">
        <nav class="flex justify-between items-center">
            <a href="index.php" class="text-lg font-bold">Home</a>
            <div class="flex items-center">
                <p class="mr-2">Welcome, <?= $_SESSION['username'] ?></p>
                <a href="logout.php" class="text-red-600 hover:text-red-800">Logout</a>
            </div>
        </nav>
    </header>
    <main class="max-w-7xl mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4">Rooms</h1>
        <div class="flex justify-between items-center mb-4">
            <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='create_rooms.php'">Add New Item</button>
            <div class="relative">
                <input type="search" id="search" class="block p-2 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search...">
                <button class="absolute top-0 right-0 p-2" onclick="searchRooms()">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </div>
        </div>
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">ID</th>
                    <th scope="col" class="px-6 py-3">Name</th>
                    <th scope="col" class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch records from backend
                $response = file_get_contents('../backend/rooms.php');
                $rooms = json_decode($response, true);
                foreach ($rooms as $room) {
                    ?>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4"><?= $room['id'] ?></td>
                        <td class="px-6 py-4"><?= $room['name'] ?></td>
                        <td class="px-6 py-4">
                            <button class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='edit_rooms.php?id=<?= $room['id'] ?>'">Edit</button>
                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deleteRoom(<?= $room['id'] ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </main>

    <script>
        function searchRooms() {
            const searchInput = document.getElementById('search');
            const searchValue = searchInput.value.toLowerCase();
            const tableBody = document.querySelector('tbody');
            const rows = tableBody.rows;

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.cells;
                let match = false;

                for (let j = 0; j < cells.length; j++) {
                    const cell = cells[j];
                    const text = cell.textContent.toLowerCase();

                    if (text.includes(searchValue)) {
                        match = true;
                        break;
                    }
                }

                if (match) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            }
        }

        async function deleteRoom(id) {
            const response = await fetch('../backend/rooms.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: id })
            });

            if (response.ok) {
                const tableBody = document.querySelector('tbody');
                const rows = tableBody.rows;
                const row = rows.find(row => row.cells[0].textContent === id.toString());
                row.style.display = 'none';
            }
        }
    </script>
</body>
</html>

This code includes a premium Tailwind UI with a specific color palette, session validation, and a list of rooms with actions. The search bar filters elements in real-time, and the AJAX JavaScript code fetches list records from the backend and handles DELETE requests.