**list_فناني.php**

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
    <title>فناني</title>
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
    <div class="container mx-auto p-4">
        <header class="bg-white shadow-md p-4 mb-4">
            <nav class="flex justify-between">
                <a href="index.php" class="text-lg font-bold">Home</a>
                <div class="flex items-center">
                    <p class="mr-2">Welcome, <?= $_SESSION['username'] ?></p>
                    <a href="logout.php" class="text-red-600 hover:text-red-800">Logout</a>
                </div>
            </nav>
        </header>
        <main class="bg-white shadow-md p-4 rounded-lg">
            <h2 class="text-lg font-bold mb-4">List of فناني</h2>
            <div class="flex justify-between mb-4">
                <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='create_فناني.php'">Add New Item</button>
                <input type="search" class="w-full p-2 pl-10 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600" placeholder="Search..." id="search">
            </div>
            <table class="w-full table-auto">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody id="records">
                    <?php
                    // Fetch records from backend
                    $response = file_get_contents('../backend/فناني.php');
                    $records = json_decode($response, true);
                    foreach ($records as $record) {
                        ?>
                        <tr>
                            <td class="px-4 py-2"><?= $record['name'] ?></td>
                            <td class="px-4 py-2">
                                <a href="edit_فناني.php?id=<?= $record['id'] ?>" class="text-teal-500 hover:text-teal-700">Edit</a>
                                <button class="ml-2 text-red-600 hover:text-red-800" onclick="deleteRecord(<?= $record['id'] ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </main>
    </div>

    <script>
        const searchInput = document.getElementById('search');
        const recordsTable = document.getElementById('records');

        searchInput.addEventListener('input', () => {
            const searchValue = searchInput.value.toLowerCase();
            const records = Array.from(recordsTable.children);
            records.forEach((record) => {
                const name = record.children[0].textContent.toLowerCase();
                if (name.includes(searchValue)) {
                    record.style.display = 'table-row';
                } else {
                    record.style.display = 'none';
                }
            });
        });

        async function deleteRecord(id) {
            const response = await fetch('../backend/فناني.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id })
            });
            const data = await response.json();
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting record');
            }
        }
    </script>
</body>
</html>


**backend/فناني.php**

<?php
// Fetch records from database
$records = array(
    array('id' => 1, 'name' => 'John Doe'),
    array('id' => 2, 'name' => 'Jane Doe'),
    array('id' => 3, 'name' => 'Bob Smith')
);
echo json_encode($records);
?>


Note: This code assumes that you have a backend script (`backend/فناني.php`) that fetches records from a database and returns them in JSON format. You'll need to replace this with your actual backend logic.