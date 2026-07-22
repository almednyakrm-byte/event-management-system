**list_tickets.php**

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
    <title>Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .bg-orange-500 {
            background-color: #FF9900;
        }
        .text-gray-200 {
            color: #D3D3D3;
        }
    </style>
</head>
<body>
    <div class="container mx-auto p-4">
        <div class="flex justify-between mb-4">
            <a href="index.php" class="text-gray-200 hover:text-gray-900">Back to Index</a>
            <div class="flex items-center">
                <p class="text-gray-200 mr-2">Welcome, <?php echo $_SESSION['username']; ?></p>
                <a href="logout.php" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">Logout</a>
            </div>
        </div>
        <div class="flex justify-between mb-4">
            <h2 class="text-gray-200">Tickets</h2>
            <a href="create_tickets.php" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">Add New Item</a>
        </div>
        <div class="flex justify-between mb-4">
            <input type="search" id="search" class="w-full p-2 text-gray-200" placeholder="Search...">
            <button id="search-btn" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">Search</button>
        </div>
        <table class="w-full border-collapse border border-gray-200">
            <thead>
                <tr>
                    <th class="border border-gray-200 p-2">ID</th>
                    <th class="border border-gray-200 p-2">Title</th>
                    <th class="border border-gray-200 p-2">Description</th>
                    <th class="border border-gray-200 p-2">Actions</th>
                </tr>
            </thead>
            <tbody id="tickets-list">
                <!-- List of records will be fetched here -->
            </tbody>
        </table>
    </div>

    <script>
        const searchInput = document.getElementById('search');
        const searchBtn = document.getElementById('search-btn');
        const ticketsList = document.getElementById('tickets-list');

        searchBtn.addEventListener('click', fetchTickets);

        function fetchTickets() {
            const searchQuery = searchInput.value.trim();
            fetch('../backend/tickets.php?search=' + searchQuery)
                .then(response => response.json())
                .then(data => {
                    const html = data.map(ticket => `
                        <tr>
                            <td class="border border-gray-200 p-2">${ticket.id}</td>
                            <td class="border border-gray-200 p-2">${ticket.title}</td>
                            <td class="border border-gray-200 p-2">${ticket.description}</td>
                            <td class="border border-gray-200 p-2">
                                <a href="edit_tickets.php?id=${ticket.id}" class="text-orange-500 hover:text-orange-700">Edit</a>
                                <button class="text-red-500 hover:text-red-700" onclick="deleteTicket(${ticket.id})">Delete</button>
                            </td>
                        </tr>
                    `).join('');
                    ticketsList.innerHTML = html;
                })
                .catch(error => console.error(error));
        }

        function deleteTicket(id) {
            if (confirm('Are you sure you want to delete this ticket?')) {
                fetch('../backend/tickets.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchTickets();
                    } else {
                        alert('Error deleting ticket');
                    }
                })
                .catch(error => console.error(error));
            }
        }

        fetchTickets();
    </script>
</body>
</html>


**backend/tickets.php**

<?php
// Database connection
$db = new PDO('mysql:host=localhost;dbname=tickets', 'username', 'password');

// Search query
$searchQuery = $_GET['search'] ?? '';

// Fetch records
$stmt = $db->prepare('SELECT * FROM tickets WHERE title LIKE :search OR description LIKE :search');
$stmt->bindParam(':search', '%' . $searchQuery . '%');
$stmt->execute();

// Fetch data
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($data);


**backend/tickets.php (DELETE request)**

<?php
// Database connection
$db = new PDO('mysql:host=localhost;dbname=tickets', 'username', 'password');

// Delete record
$id = $_POST['id'];
$stmt = $db->prepare('DELETE FROM tickets WHERE id = :id');
$stmt->bindParam(':id', $id);
$stmt->execute();

// Return success message
header('Content-Type: application/json');
echo json_encode(['success' => true]);