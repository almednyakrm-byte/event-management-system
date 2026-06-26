**create_meals.php**

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

// Include header and navigation
require_once 'header.php';
?>

<div class="container mx-auto p-4 mt-12">
    <h1 class="text-3xl font-bold text-emerald-600">Create Meal</h1>
    <form id="create-meal-form" class="bg-white p-8 shadow-md rounded-lg">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name" class="block w-full p-2 mt-1 text-sm text-gray-700 border-gray-300 rounded-md" required>
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea id="description" name="description" class="block w-full p-2 mt-1 text-sm text-gray-700 border-gray-300 rounded-md" required></textarea>
            </div>
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                <input type="number" id="price" name="price" class="block w-full p-2 mt-1 text-sm text-gray-700 border-gray-300 rounded-md" required>
            </div>
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                <select id="category" name="category" class="block w-full p-2 mt-1 text-sm text-gray-700 border-gray-300 rounded-md" required>
                    <option value="">Select a category</option>
                    <option value="breakfast">Breakfast</option>
                    <option value="lunch">Lunch</option>
                    <option value="dinner">Dinner</option>
                </select>
            </div>
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                <input type="file" id="image" name="image" class="block w-full p-2 mt-1 text-sm text-gray-700 border-gray-300 rounded-md" required>
            </div>
        </div>
        <button type="submit" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">Create Meal</button>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#create-meal-form').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: '../backend/meals.php',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data == 'success') {
                        window.location.href = 'list_meals.php';
                    } else {
                        alert('Error creating meal');
                    }
                }
            });
        });
    });
</script>

<?php
require_once 'footer.php';
?>


**meals.php** (backend)

<?php
// Include database connection
require_once 'db.php';

// Check if form data is submitted
if (isset($_POST['name']) && isset($_POST['description']) && isset($_POST['price']) && isset($_POST['category']) && isset($_POST['image'])) {
    // Prepare SQL query
    $query = "INSERT INTO meals (name, description, price, category, image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $_POST['name'], $_POST['description'], $_POST['price'], $_POST['category'], $_POST['image']);
    $stmt->execute();
    $stmt->close();
    echo 'success';
} else {
    echo 'Error creating meal';
}
?>


**Note:** This code assumes you have a `db.php` file that establishes a connection to your database and a `header.php` and `footer.php` file that includes your website's header and footer respectively. You will need to modify the code to fit your specific database schema and website structure.