**edit_حجز-تذاكر.php**

<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

require_once '../backend/config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $record = fetchRecord($id);
    if (!$record) {
        header('Location: list_حجز-تذاكر.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل حجز تذاكر</title>
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
        <h1 class="text-3xl font-bold emerald-600 mb-4">تعديل حجز تذاكر</h1>
        <form id="edit-form">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">اسم الحجز</label>
                <input type="text" id="name" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $record['name']; ?>">
            </div>
            <div class="mb-4">
                <label for="date" class="block text-gray-700 text-sm font-bold mb-2">تاريخ الحجز</label>
                <input type="date" id="date" name="date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $record['date']; ?>">
            </div>
            <div class="mb-4">
                <label for="time" class="block text-gray-700 text-sm font-bold mb-2">ساعة الحجز</label>
                <input type="time" id="time" name="time" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $record['time']; ?>">
            </div>
            <div class="mb-4">
                <label for="price" class="block text-gray-700 text-sm font-bold mb-2">سعر الحجز</label>
                <input type="number" id="price" name="price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $record['price']; ?>">
            </div>
            <button type="submit" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">تعديل</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#edit-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'PUT',
                    url: '../backend/حجز-تذاكر.php',
                    data: formData,
                    success: function(response) {
                        if (response === 'success') {
                            window.location.href = 'list_حجز-تذاكر.php';
                        } else {
                            alert('Error: ' + response);
                        }
                    }
                });
            });

            // Fetch existing record details via GET
            $.ajax({
                type: 'GET',
                url: '../backend/حجز-تذاكر.php?id=<?php echo $id; ?>',
                success: function(response) {
                    var data = JSON.parse(response);
                    $('#name').val(data.name);
                    $('#date').val(data.date);
                    $('#time').val(data.time);
                    $('#price').val(data.price);
                }
            });
        });
    </script>
</body>
</html>


**backend/حجز-تذاكر.php**

<?php
require_once 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $record = fetchRecord($id);
    if ($record) {
        echo json_encode($record);
    } else {
        echo 'Error: Record not found';
    }
} elseif (isset($_POST['id'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $price = $_POST['price'];

    $update = updateRecord($id, $name, $date, $time, $price);
    if ($update) {
        echo 'success';
    } else {
        echo 'Error: Update failed';
    }
}


**backend/config.php**

<?php
// Database connection settings
$db_host = 'localhost';
$db_username = 'username';
$db_password = 'password';
$db_name = 'database';

// Establish database connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Function to fetch record by ID
function fetchRecord($id) {
    global $conn;
    $query = "SELECT * FROM حجز_تذاكر WHERE id = '$id'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}

// Function to update record
function updateRecord($id, $name, $date, $time, $price) {
    global $conn;
    $query = "UPDATE حجز_تذاكر SET name = '$name', date = '$date', time = '$time', price = '$price' WHERE id = '$id'";
    return $conn->query($query);
}
?>