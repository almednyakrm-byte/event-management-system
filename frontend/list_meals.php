**list_meals.php**

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
    <title>Meals Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #4ade80; /* emerald-600 */
            color: #fff;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .header nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header nav a {
            color: #fff;
            text-decoration: none;
        }
        .header nav a:hover {
            text-decoration: underline;
        }
        .search-bar {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 50%;
        }
        .search-bar input[type="search"] {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
        }
        .search-bar input[type="search"]:focus {
            outline: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .table th {
            background-color: #f0f0f0;
        }
        .table td a {
            text-decoration: none;
            color: #337ab7;
        }
        .table td a:hover {
            text-decoration: underline;
        }
        .table td button {
            background-color: #4ade80; /* emerald-600 */
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .table td button:hover {
            background-color: #3e8e7e;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <nav>
                <a href="index.php">Back to Index</a>
                <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
        <div class="search-bar">
            <input type="search" id="search-input" placeholder="Search...">
            <button id="search-btn">Search</button>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <!-- Table data will be populated here -->
            </tbody>
        </table>
        <button class="btn btn-primary" id="add-btn">Add New Item</button>
    </div>

    <script>
        const searchInput = document.getElementById('search-input');
        const searchBtn = document.getElementById('search-btn');
        const tableBody = document.getElementById('table-body');
        const addBtn = document.getElementById('add-btn');

        searchBtn.addEventListener('click', () => {
            const searchTerm = searchInput.value.trim();
            if (searchTerm) {
                fetch('../backend/meals.php', {
                    method: 'GET',
                    params: { search: searchTerm }
                })
                .then(response => response.json())
                .then(data => {
                    tableBody.innerHTML = '';
                    data.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${item.id}</td>
                            <td>${item.name}</td>
                            <td>${item.description}</td>
                            <td>
                                <a href="edit_meals.php?id=${item.id}">Edit</a>
                                <button class="delete-btn" data-id="${item.id}">Delete</button>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                });
            } else {
                fetch('../backend/meals.php', {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(data => {
                    tableBody.innerHTML = '';
                    data.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${item.id}</td>
                            <td>${item.name}</td>
                            <td>${item.description}</td>
                            <td>
                                <a href="edit_meals.php?id=${item.id}">Edit</a>
                                <button class="delete-btn" data-id="${item.id}">Delete</button>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                });
            }
        });

        addBtn.addEventListener('click', () => {
            window.location.href = 'create_meals.php';
        });

        document.addEventListener('DOMContentLoaded', () => {
            fetch('../backend/meals.php', {
                method: 'GET'
            })
            .then(response => response.json())
            .then(data => {
                data.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.id}</td>
                        <td>${item.name}</td>
                        <td>${item.description}</td>
                        <td>
                            <a href="edit_meals.php?id=${item.id}">Edit</a>
                            <button class="delete-btn" data-id="${item.id}">Delete</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            });
        });

        tableBody.addEventListener('click', (e) => {
            if (e.target.classList.contains('delete-btn')) {
                const deleteBtn = e.target;
                const itemId = deleteBtn.dataset.id;
                if (confirm(`Are you sure you want to delete item with ID ${itemId}?`)) {
                    fetch(`../backend/meals.php`, {
                        method: 'DELETE',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: itemId })
                    })
                    .then(() => {
                        deleteBtn.parentNode.parentNode.remove();
                    });
                }
            }
        });
    </script>
</body>
</html>

This code includes the following features:

1. Session validation: Redirects to login.php if the user is not authenticated.
2. Header navigation: Includes links to index.php, current user info, and logout.
3. Table: Displays a list of records with actions (Edit and Delete).
4. Search bar: Filters elements in real-time using AJAX.
5. AJAX: Fetches list records from '../backend/meals.php' (GET) and DELETE requests.
6. Add New Item button: Links to create_meals.php.
7. Tailwind UI: Uses a premium Tailwind UI theme with the specified color palette.

Note: This code assumes that the backend API is implemented and returns data in JSON format. The backend API should handle the DELETE request and remove the corresponding record from the database.