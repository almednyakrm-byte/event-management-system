**list_venues.php**

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
    <title>Venues Management</title>
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
        <nav class="flex justify-between items-center">
            <a href="index.php" class="text-lg font-bold">Home</a>
            <div class="flex items-center">
                <p class="mr-2">Hello, <?= $_SESSION['username'] ?></p>
                <a href="logout.php" class="text-red-500 hover:text-red-700">Logout</a>
            </div>
        </nav>
    </header>
    <main class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4">Venues Management</h1>
        <div class="flex justify-between items-center mb-4">
            <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='create_venues.php'">Add New Item</button>
            <input type="search" class="w-full p-2 pl-10 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600" placeholder="Search venues..." id="search-input">
        </div>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Address</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody id="venues-list">
                <!-- List of venues will be rendered here -->
            </tbody>
        </table>
    </main>

    <script>
        const searchInput = document.getElementById('search-input');
        const venuesList = document.getElementById('venues-list');

        searchInput.addEventListener('input', () => {
            const searchQuery = searchInput.value.toLowerCase();
            const venues = Array.from(venuesList.children);
            venues.forEach((venue, index) => {
                const name = venue.children[0].textContent.toLowerCase();
                const address = venue.children[1].textContent.toLowerCase();
                if (name.includes(searchQuery) || address.includes(searchQuery)) {
                    venue.style.display = 'table-row';
                } else {
                    venue.style.display = 'none';
                }
            });
        });

        async function fetchVenues() {
            try {
                const response = await fetch('../backend/venues.php');
                const data = await response.json();
                renderVenues(data);
            } catch (error) {
                console.error(error);
            }
        }

        function renderVenues(venues) {
            venuesList.innerHTML = '';
            venues.forEach((venue) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-4 py-2">${venue.name}</td>
                    <td class="px-4 py-2">${venue.address}</td>
                    <td class="px-4 py-2">
                        <a href="edit_venues.php?id=${venue.id}" class="text-teal-500 hover:text-teal-700">Edit</a>
                        <button class="ml-2 text-red-500 hover:text-red-700" onclick="deleteVenue(${venue.id})">Delete</button>
                    </td>
                `;
                venuesList.appendChild(row);
            });
        }

        async function deleteVenue(id) {
            try {
                const response = await fetch('../backend/venues.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                if (response.ok) {
                    fetchVenues();
                } else {
                    console.error('Error deleting venue');
                }
            } catch (error) {
                console.error(error);
            }
        }

        fetchVenues();
    </script>
</body>
</html>

**Note:** This code assumes that you have a `venues.php` file in the `../backend` directory that handles GET and DELETE requests for the venues data. You'll need to create this file and implement the necessary logic to fetch and delete venues.