**edit_meals.php**

<?php
// Session validation
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get meal ID from URL
$id = $_GET['id'];

// Fetch existing record details via GET
$data = json_decode(file_get_contents('../backend/meals.php?id=' . $id), true);

// Check if meal exists
if (empty($data)) {
    echo 'Meal not found.';
    exit;
}

// Set page title
$page_title = 'Edit Meal';

// Include header
include 'header.php';

?>

<!-- Main content -->
<main class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <h1 class="text-3xl font-bold mb-4"><?= $page_title ?></h1>

    <!-- Form -->
    <form id="edit-meal-form" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
            <input type="text" id="name" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?= $data['name'] ?>">
        </div>
        <div class="mb-4">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
            <textarea id="description" name="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?= $data['description'] ?></textarea>
        </div>
        <div class="mb-4">
            <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Price</label>
            <input type="number" id="price" name="price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?= $data['price'] ?>">
        </div>
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded">Save Changes</button>
    </form>
</main>

<!-- JavaScript -->
<script>
    // Fetch existing record details via GET
    fetch('../backend/meals.php?id=<?= $id ?>')
        .then(response => response.json())
        .then(data => {
            // Populate form fields
            document.getElementById('name').value = data.name;
            document.getElementById('description').value = data.description;
            document.getElementById('price').value = data.price;
        })
        .catch(error => console.error(error));

    // Submit form via AJAX PUT request
    document.getElementById('edit-meal-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        fetch('../backend/meals.php', {
            method: 'PUT',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'list_meals.php';
                } else {
                    console.error(data.error);
                }
            })
            .catch(error => console.error(error));
    });
</script>

<!-- Include footer -->
<?php include 'footer.php'; ?>


**meals.php (backend)**

<?php
// Check if meal ID is set
if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Meal ID not set.']);
    exit;
}

// Get meal ID
$id = $_GET['id'];

// Connect to database
$conn = new mysqli('localhost', 'username', 'password', 'database');

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Get meal details
$stmt = $conn->prepare("SELECT * FROM meals WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch meal details
$meal = $result->fetch_assoc();

// Close connection
$conn->close();

// Output meal details
echo json_encode($meal);


**list_meals.php (example)**

<?php
// Include header
include 'header.php';

?>

<!-- Main content -->
<main class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <h1 class="text-3xl font-bold mb-4">Meals</h1>

    <!-- Table -->
    <table class="border-collapse border border-gray-500">
        <thead>
            <tr>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Description</th>
                <th class="px-4 py-2">Price</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Connect to database
            $conn = new mysqli('localhost', 'username', 'password', 'database');

            // Check connection
            if ($conn->connect_error) {
                echo 'Connection failed: ' . $conn->connect_error;
                exit;
            }

            // Get meals
            $stmt = $conn->prepare("SELECT * FROM meals");
            $stmt->execute();
            $result = $stmt->get_result();

            // Fetch meals
            while ($meal = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td class="px-4 py-2"><?= $meal['name'] ?></td>
                    <td class="px-4 py-2"><?= $meal['description'] ?></td>
                    <td class="px-4 py-2"><?= $meal['price'] ?></td>
                    <td class="px-4 py-2">
                        <a href="edit_meals.php?id=<?= $meal['id'] ?>" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">Edit</a>
                    </td>
                </tr>
                <?php
            }

            // Close connection
            $conn->close();
            ?>
        </tbody>
    </table>
</main>

<!-- Include footer -->
<?php include 'footer.php'; ?>