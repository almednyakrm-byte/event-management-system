**edit_attendance.php**

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

// Get attendance ID from URL
$id = $_GET['id'];

// Fetch existing record details via GET
$attendance = json_decode(file_get_contents('../backend/attendance.php?id=' . $id), true);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto p-4 bg-white rounded-lg shadow-md">
        <h2 class="text-lg font-bold text-slate-900 mb-4">Edit Attendance</h2>
        <form id="edit-attendance-form">
            <div class="mb-4">
                <label for="date" class="block text-sm font-medium text-slate-700">Date</label>
                <input type="date" id="date" name="date" value="<?= $attendance['date'] ?>" class="block w-full p-2 pl-10 text-sm text-gray-700 placeholder-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Date">
            </div>
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                <select id="status" name="status" class="block w-full p-2 pl-10 text-sm text-gray-700 placeholder-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="present" <?= $attendance['status'] == 'present' ? 'selected' : '' ?>>Present</option>
                    <option value="absent" <?= $attendance['status'] == 'absent' ? 'selected' : '' ?>>Absent</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Update</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#edit-attendance-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'PUT',
                    url: '../backend/attendance.php',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            window.location.href = 'list_attendance.php';
                        } else {
                            alert(response.message);
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>


**attendance.php (backend)**

<?php
// Check if attendance ID is set
if (isset($_GET['id'])) {
    // Connect to database
    $conn = new PDO('mysql:host=localhost;dbname=your_database', 'your_username', 'your_password');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch attendance record
    $stmt = $conn->prepare('SELECT * FROM attendance WHERE id = :id');
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();
    $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return attendance record as JSON
    echo json_encode($attendance);
} else {
    // Return error message
    echo json_encode(['error' => 'Attendance ID not set']);
}
?>


**update_attendance.php (backend)**

<?php
// Check if attendance ID and form data are set
if (isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Connect to database
    $conn = new PDO('mysql:host=localhost;dbname=your_database', 'your_username', 'your_password');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get attendance ID and form data
    $id = $_GET['id'];
    $formData = $_POST;

    // Update attendance record
    $stmt = $conn->prepare('UPDATE attendance SET date = :date, status = :status WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':date', $formData['date']);
    $stmt->bindParam(':status', $formData['status']);
    $stmt->execute();

    // Return success message
    echo json_encode(['success' => true, 'message' => 'Attendance updated successfully']);
} else {
    // Return error message
    echo json_encode(['error' => 'Invalid request']);
}
?>