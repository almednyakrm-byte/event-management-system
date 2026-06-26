**edit_programs.php**

<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get program ID from URL
$id = $_GET['id'];

// Fetch existing program details
$programs = file_get_contents('../backend/programs.php?id=' . $id);
$programs = json_decode($programs, true);

// Set page title and mod slug
$page_title = 'Edit Program';
$mod_slug = 'programs';

// Include header
include 'header.php';
?>

<!-- Main content -->
<main class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <h1 class="text-3xl font-bold mb-4 text-slate-900"><?= $page_title ?></h1>

    <!-- Form -->
    <form id="edit-program-form" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-slate-700">Name</label>
            <input type="text" id="name" name="name" class="block w-full px-4 py-2 text-sm text-gray-700 placeholder-gray-300 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" value="<?= $programs['name'] ?>">
        </div>
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
            <textarea id="description" name="description" class="block w-full px-4 py-2 text-sm text-gray-700 placeholder-gray-300 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" rows="4"><?= $programs['description'] ?></textarea>
        </div>
        <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
            <select id="status" name="status" class="block w-full px-4 py-2 text-sm text-gray-700 placeholder-gray-300 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                <option value="active" <?= $programs['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $programs['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Update Program</button>
    </form>
</main>

<!-- JavaScript -->
<script>
    // Fetch existing program details via GET
    fetch('../backend/programs.php?id=<?= $id ?>')
        .then(response => response.json())
        .then(data => {
            // Populate form fields
            document.getElementById('name').value = data.name;
            document.getElementById('description').value = data.description;
            document.getElementById('status').value = data.status;
        })
        .catch(error => console.error(error));

    // Submit form via AJAX PUT request
    document.getElementById('edit-program-form').addEventListener('submit', event => {
        event.preventDefault();
        const formData = new FormData(event.target);
        fetch('../backend/programs.php', {
            method: 'PUT',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to list page
                    window.location.href = 'list_<?= $mod_slug ?>.php';
                } else {
                    console.error(data.error);
                }
            })
            .catch(error => console.error(error));
    });
</script>

<?php
// Include footer
include 'footer.php';
?>


**header.php**

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body>
    <header class="bg-slate-900 py-4">
        <nav class="container mx-auto flex justify-between items-center">
            <a href="#" class="text-white font-bold text-2xl">Programs</a>
            <ul class="flex items-center space-x-4">
                <li><a href="#" class="text-white hover:text-indigo-500">Home</a></li>
                <li><a href="#" class="text-white hover:text-indigo-500">About</a></li>
                <li><a href="#" class="text-white hover:text-indigo-500">Contact</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <?= $content ?>
    </main>
    <footer class="bg-slate-900 py-4">
        <p class="text-center text-white">&copy; 2023 Programs</p>
    </footer>
</body>
</html>


**footer.php**

<script src="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.js"></script>


**programs.php (backend)**

<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

// Get program ID from URL
$id = $_GET['id'];

// Fetch existing program details
$program = get_program($id);

// Return program details as JSON
echo json_encode($program);

function get_program($id) {
    // Database connection
    $db = new PDO('mysql:host=localhost;dbname=programs', 'username', 'password');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch program details
    $stmt = $db->prepare('SELECT * FROM programs WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $program = $stmt->fetch();

    // Return program details
    return $program;
}
?>


**list_programs.php (example)**

<?php
// Include header
include 'header.php';

// Get programs from database
$programs = get_programs();

// Display programs
foreach ($programs as $program) {
    echo '<h2>' . $program['name'] . '</h2>';
    echo '<p>' . $program['description'] . '</p>';
}

// Include footer
include 'footer.php';
?>

<?php
function get_programs() {
    // Database connection
    $db = new PDO('mysql:host=localhost;dbname=programs', 'username', 'password');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch programs
    $stmt = $db->prepare('SELECT * FROM programs');
    $stmt->execute();
    $programs = $stmt->fetchAll();

    // Return programs
    return $programs;
}
?>