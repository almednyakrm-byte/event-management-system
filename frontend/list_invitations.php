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
    <title>Invitations</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-orange-500 text-white p-4">
        <nav class="flex justify-between">
            <a href="index.php" class="text-lg font-bold">Back to Index</a>
            <span class="text-lg font-bold">Welcome, <?php echo $current_user; ?></span>
            <a href="logout.php" class="text-lg font-bold">Logout</a>
        </nav>
    </header>
    <main class="p-4">
        <h1 class="text-3xl font-bold mb-4">Invitations</h1>
        <div class="flex justify-between mb-4">
            <button class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                <a href="create_invitations.php">Add New Item</a>
            </button>
            <input type="text" id="search" class="bg-gray-200 p-2 rounded" placeholder="Search...">
        </div>
        <table id="invitations-table" class="w-full table-auto border-collapse border border-gray-200">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-gray-200 p-2">ID</th>
                    <th class="border border-gray-200 p-2">Name</th>
                    <th class="border border-gray-200 p-2">Actions</th>
                </tr>
            </thead>
            <tbody id="invitations-tbody">
                <!-- Table content will be populated via AJAX -->
            </tbody>
        </table>
    </main>

    <script>
        // Fetch API to get invitations list
        fetch('../backend/invitations.php')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('invitations-tbody');
                data.forEach(invitation => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="border border-gray-200 p-2">${invitation.id}</td>
                        <td class="border border-gray-200 p-2">${invitation.name}</td>
                        <td class="border border-gray-200 p-2">
                            <a href="edit_invitations.php?id=${invitation.id}" class="text-orange-500 hover:text-orange-700">Edit</a>
                            <button class="text-red-500 hover:text-red-700" onclick="deleteInvitation(${invitation.id})">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            });

        // Delete invitation via AJAX
        function deleteInvitation(id) {
            fetch(`../backend/invitations.php?id=${id}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const row = document.querySelector(`#invitations-tbody tr:nth-child(${id})`);
                    row.remove();
                } else {
                    console.error('Error deleting invitation:', data.error);
                }
            });
        }

        // Search bar filtering
        const searchInput = document.getElementById('search');
        searchInput.addEventListener('input', () => {
            const filter = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll('#invitations-tbody tr');
            rows.forEach(row => {
                const nameCell = row.cells[1];
                if (nameCell.textContent.toLowerCase().includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>