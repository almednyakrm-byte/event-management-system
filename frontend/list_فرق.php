**list_فرق.php**

<?php
// Session validation
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فرق</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
        }
        .header {
            background-color: #2c3e50;
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
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 1rem;
            text-align: center;
        }
        .table th {
            background-color: #2c3e50;
            color: #fff;
        }
        .search-bar {
            width: 50%;
            padding: 1rem;
            border: 1px solid #ccc;
            border-radius: 0.5rem;
        }
        .search-bar input[type="search"] {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 0.5rem;
        }
        .search-bar input[type="search"]:focus {
            outline: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="header">
        <a href="index.php">الرئيسية</a>
        <span class="text-lg font-bold">مرحباً, <?php echo $_SESSION['username']; ?></span>
        <a href="logout.php">تسجيل خروج</a>
    </div>
    <div class="container mx-auto p-4">
        <div class="flex justify-between mb-4">
            <h1 class="text-2xl font-bold">فرق</h1>
            <a href="create_فرق.php" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded">إضافة جديد</a>
        </div>
        <div class="flex justify-between mb-4">
            <input type="search" id="search" class="search-bar" placeholder="بحث...">
            <button class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded" id="search-btn">بحث</button>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>اسم الفرق</th>
                    <th>تاريخ الإضافة</th>
                    <th>حالة الفرق</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody id="records">
                <!-- Records will be loaded here -->
            </tbody>
        </table>
    </div>

    <script>
        const searchInput = document.getElementById('search');
        const searchBtn = document.getElementById('search-btn');
        const recordsTable = document.getElementById('records');

        searchBtn.addEventListener('click', async () => {
            const searchQuery = searchInput.value.trim();
            if (searchQuery) {
                const response = await fetch('../backend/فرق.php', {
                    method: 'GET',
                    params: {
                        search: searchQuery
                    }
                });
                const data = await response.json();
                recordsTable.innerHTML = '';
                data.forEach(record => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${record.اسم الفرق}</td>
                        <td>${record.تاريخ الإضافة}</td>
                        <td>${record.حالة الفرق}</td>
                        <td>
                            <a href="edit_فرق.php?id=${record.id}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded">تعديل</a>
                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deleteRecord(${record.id})">حذف</button>
                        </td>
                    `;
                    recordsTable.appendChild(row);
                });
            }
        });

        async function deleteRecord(id) {
            if (confirm('هل أنت متأكد من حذف هذا الفرق؟')) {
                const response = await fetch('../backend/فرق.php', {
                    method: 'DELETE',
                    params: {
                        id: id
                    }
                });
                if (response.ok) {
                    alert('تم حذف الفرق بنجاح');
                    window.location.reload();
                } else {
                    alert('حدث خطأ أثناء حذف الفرق');
                }
            }
        }
    </script>
</body>
</html>


**backend/فرق.php**

<?php
// Database connection
$conn = mysqli_connect('localhost', 'username', 'password', 'database');

if (!$conn) {
    die('Could not connect: ' . mysqli_error($conn));
}

// Search query
$search = $_GET['search'] ?? '';

// SQL query
$query = "SELECT * FROM فرق";
if ($search) {
    $query .= " WHERE اسم الفرق LIKE '%$search%'";
}

$result = mysqli_query($conn, $query);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// JSON output
header('Content-Type: application/json');
echo json_encode($data);

// Delete record
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'];
    $query = "DELETE FROM فرق WHERE id = '$id'";
    if (mysqli_query($conn, $query)) {
        echo 'Record deleted successfully';
    } else {
        echo 'Error deleting record';
    }
}

mysqli_close($conn);
?>