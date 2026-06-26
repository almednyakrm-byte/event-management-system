**list_programs.php**

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
    <title>Programs</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .bg-slate-900 {
            background-color: #1a1d23;
        }
        .text-indigo-500 {
            color: #6b7280;
        }
    </style>
</head>
<body class="bg-slate-900">
    <div class="container mx-auto p-4 pt-6">
        <header class="bg-indigo-500 p-4 rounded-t-lg">
            <nav class="flex justify-between">
                <a href="index.php" class="text-indigo-500 hover:text-white">Back to Index</a>
                <div class="flex items-center">
                    <p class="text-indigo-500 mr-2">Hello, <?= $_SESSION['username'] ?></p>
                    <a href="logout.php" class="text-indigo-500 hover:text-white">Logout</a>
                </div>
            </nav>
        </header>
        <main class="bg-slate-900 p-4 rounded-b-lg">
            <h1 class="text-indigo-500 text-2xl mb-4">Programs</h1>
            <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='create_programs.php'">Add New Item</button>
            <div class="flex justify-between mt-4">
                <input type="search" class="bg-slate-900 text-indigo-500 p-2 rounded-lg w-full" id="search" placeholder="Search...">
                <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" onclick="searchPrograms()">Search</button>
            </div>
            <table class="w-full mt-4">
                <thead>
                    <tr>
                        <th class="text-indigo-500">ID</th>
                        <th class="text-indigo-500">Name</th>
                        <th class="text-indigo-500">Actions</th>
                    </tr>
                </thead>
                <tbody id="programs-list">
                    <?php
                    // Fetch programs list from backend
                    $programs = fetchPrograms();
                    foreach ($programs as $program) {
                        ?>
                        <tr>
                            <td><?= $program['id'] ?></td>
                            <td><?= $program['name'] ?></td>
                            <td>
                                <a href="edit_programs.php?id=<?= $program['id'] ?>" class="text-indigo-500 hover:text-white">Edit</a>
                                <button class="text-red-500 hover:text-white" onclick="deleteProgram(<?= $program['id'] ?>)">Delete</button>
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
        function searchPrograms() {
            const searchInput = document.getElementById('search');
            const searchQuery = searchInput.value.trim();
            if (searchQuery) {
                fetch('../backend/programs.php?search=' + searchQuery)
                    .then(response => response.json())
                    .then(data => {
                        const programsList = document.getElementById('programs-list');
                        programsList.innerHTML = '';
                        data.forEach(program => {
                            const programRow = document.createElement('tr');
                            programRow.innerHTML = `
                                <td>${program.id}</td>
                                <td>${program.name}</td>
                                <td>
                                    <a href="edit_programs.php?id=${program.id}" class="text-indigo-500 hover:text-white">Edit</a>
                                    <button class="text-red-500 hover:text-white" onclick="deleteProgram(${program.id})">Delete</button>
                                </td>
                            `;
                            programsList.appendChild(programRow);
                        });
                    });
            } else {
                fetchPrograms();
            }
        }

        function fetchPrograms() {
            fetch('../backend/programs.php')
                .then(response => response.json())
                .then(data => {
                    const programsList = document.getElementById('programs-list');
                    programsList.innerHTML = '';
                    data.forEach(program => {
                        const programRow = document.createElement('tr');
                        programRow.innerHTML = `
                            <td>${program.id}</td>
                            <td>${program.name}</td>
                            <td>
                                <a href="edit_programs.php?id=${program.id}" class="text-indigo-500 hover:text-white">Edit</a>
                                <button class="text-red-500 hover:text-white" onclick="deleteProgram(${program.id})">Delete</button>
                            </td>
                        `;
                        programsList.appendChild(programRow);
                    });
                });
        }

        function deleteProgram(id) {
            if (confirm('Are you sure you want to delete this program?')) {
                fetch('../backend/programs.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchPrograms();
                    } else {
                        alert('Error deleting program');
                    }
                });
            }
        }
    </script>
</body>
</html>

<?php
function fetchPrograms() {
    $programs = array();
    // Fetch programs from database
    // For demonstration purposes, assume we have a function to fetch programs
    // Replace this with your actual database query
    $programs = array(
        array('id' => 1, 'name' => 'Program 1'),
        array('id' => 2, 'name' => 'Program 2'),
        array('id' => 3, 'name' => 'Program 3')
    );
    return $programs;
}
?>

**Note:** This code assumes you have a `fetchPrograms()` function that fetches programs from your database. You should replace this with your actual database query. Additionally, this code uses the `fetch()` API to make AJAX requests to the backend. Make sure to update the backend API to handle these requests accordingly.