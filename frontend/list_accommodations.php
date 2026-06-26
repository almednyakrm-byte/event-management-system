<?php
// Session validation
session_start();
if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

// Get current user info
$current_user = $_SESSION['user'];

// Include database connection
include '../backend/db.php';

// Get accommodations list from database
$accommodations = [];
if ($result = $mysqli->query("SELECT * FROM accommodations")) {
    while ($row = $result->fetch_assoc()) {
        $accommodations[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accommodations</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-emerald-600 text-white p-4">
        <nav class="container mx-auto flex justify-between">
            <a href="index.php" class="text-lg font-bold">Home</a>
            <span class="text-lg font-bold">Welcome, <?= $current_user['name'] ?></span>
            <a href="logout.php" class="text-lg font-bold">Logout</a>
        </nav>
    </header>
    <main class="container mx-auto p-4 pt-6 md:p-6 lg:p-12 xl:p-24">
        <h1 class="text-3xl font-bold mb-4">Accommodations</h1>
        <div class="flex justify-between mb-4">
            <a href="create_accommodations.php" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">Add New Item</a>
            <input type="text" id="search" class="px-4 py-2 border border-gray-400 rounded" placeholder="Search...">
        </div>
        <table id="accommodations-table" class="w-full table-auto border border-gray-400">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accommodations as $accommodation) { ?>
                <tr>
                    <td class="px-4 py-2"><?= $accommodation['id'] ?></td>
                    <td class="px-4 py-2"><?= $accommodation['name'] ?></td>
                    <td class="px-4 py-2"><?= $accommodation['description'] ?></td>
                    <td class="px-4 py-2">
                        <a href="edit_accommodations.php?id=<?= $accommodation['id'] ?>" class="text-teal-500 hover:text-teal-700">Edit</a>
                        <button class="text-red-500 hover:text-red-700" onclick="deleteAccommodation(<?= $accommodation['id'] ?>)">Delete</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </main>

    <script>
        // Fetch API to get accommodations list
        async function getAccommodations() {
            const response = await fetch('../backend/accommodations.php');
            const accommodations = await response.json();
            return accommodations;
        }

        // Delete accommodation
        async function deleteAccommodation(id) {
            const response = await fetch('../backend/accommodations.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: id })
            });
            const result = await response.json();
            if (result.success) {
                location.reload();
            } else {
                alert('Error deleting accommodation');
            }
        }

        // Search accommodations
        const searchInput = document.getElementById('search');
        searchInput.addEventListener('input', (e) => {
            const searchValue = e.target.value.toLowerCase();
            const tableRows = document.querySelectorAll('#accommodations-table tbody tr');
            tableRows.forEach((row) => {
                const rowText = row.textContent.toLowerCase();
                if (rowText.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>