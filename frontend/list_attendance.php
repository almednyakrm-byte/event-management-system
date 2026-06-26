**list_attendance.php**

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
    <title>Attendance Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .header {
            background-color: #1a1d23;
            color: #fff;
            padding: 1rem;
            text-align: center;
        }
        .header a {
            color: #fff;
            text-decoration: none;
        }
        .header a:hover {
            color: #ccc;
        }
        .table {
            border-collapse: collapse;
            width: 100%;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 1rem;
            text-align: left;
        }
        .table th {
            background-color: #1a1d23;
            color: #fff;
        }
        .table td {
            background-color: #f7f7f7;
        }
        .table tr:hover {
            background-color: #f2f2f2;
        }
        .search-bar {
            padding: 1rem;
            border: 1px solid #ccc;
            border-radius: 0.5rem;
            width: 50%;
        }
        .search-bar input[type="search"] {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 0.5rem;
        }
        .search-bar button[type="submit"] {
            background-color: #1a1d23;
            color: #fff;
            border: none;
            padding: 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
        }
        .search-bar button[type="submit"]:hover {
            background-color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php">Back to Index</a>
        <span class="text-lg font-bold">Welcome, <?php echo $_SESSION['username']; ?></span>
        <a href="logout.php">Logout</a>
    </div>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4">Attendance Management</h1>
        <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='create_attendance.php'">Add New Item</button>
        <div class="search-bar mt-4">
            <input type="search" id="search-input" placeholder="Search...">
            <button type="submit" id="search-button">Search</button>
        </div>
        <table class="table mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="attendance-list">
                <?php
                // Fetch attendance records from backend
                $response = file_get_contents('../backend/attendance.php');
                $attendance = json_decode($response, true);
                foreach ($attendance as $record) {
                    echo '<tr>';
                    echo '<td>' . $record['id'] . '</td>';
                    echo '<td>' . $record['name'] . '</td>';
                    echo '<td>' . $record['date'] . '</td>';
                    echo '<td>';
                    echo '<a href="edit_attendance.php?id=' . $record['id'] . '">Edit</a> | ';
                    echo '<button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deleteAttendance(' . $record['id'] . ')">Delete</button>';
                    echo '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        // Search functionality
        const searchInput = document.getElementById('search-input');
        const searchButton = document.getElementById('search-button');
        const attendanceList = document.getElementById('attendance-list');

        searchButton.addEventListener('click', () => {
            const searchQuery = searchInput.value.trim();
            if (searchQuery !== '') {
                fetch('../backend/attendance.php?search=' + searchQuery)
                    .then(response => response.json())
                    .then(data => {
                        attendanceList.innerHTML = '';
                        data.forEach(record => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${record.id}</td>
                                <td>${record.name}</td>
                                <td>${record.date}</td>
                                <td>
                                    <a href="edit_attendance.php?id=${record.id}">Edit</a> | 
                                    <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deleteAttendance(${record.id})">Delete</button>
                                </td>
                            `;
                            attendanceList.appendChild(row);
                        });
                    });
            } else {
                fetch('../backend/attendance.php')
                    .then(response => response.json())
                    .then(data => {
                        attendanceList.innerHTML = '';
                        data.forEach(record => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${record.id}</td>
                                <td>${record.name}</td>
                                <td>${record.date}</td>
                                <td>
                                    <a href="edit_attendance.php?id=${record.id}">Edit</a> | 
                                    <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deleteAttendance(${record.id})">Delete</button>
                                </td>
                            `;
                            attendanceList.appendChild(row);
                        });
                    });
            }
        });

        // Delete attendance record
        function deleteAttendance(id) {
            if (confirm('Are you sure you want to delete this attendance record?')) {
                fetch('../backend/attendance.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Attendance record deleted successfully!');
                        window.location.reload();
                    } else {
                        alert('Error deleting attendance record!');
                    }
                });
            }
        }
    </script>
</body>
</html>

**Note:** This code assumes that you have a backend PHP script (`attendance.php`) that returns a JSON array of attendance records. The `deleteAttendance` function sends a DELETE request to the backend to delete the attendance record with the specified ID.