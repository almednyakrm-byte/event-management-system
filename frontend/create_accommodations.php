<?php
// Session validation
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

// Define module slug
$mod_slug = 'accommodations';

// Define page title
$page_title = 'Create Accommodation';

// Include header
include 'header.php';
?>

<main class="md:flex md:justify-center md:items-center h-screen">
    <div class="md:w-1/2 xl:w-1/3 p-6 mx-auto bg-emerald-600 rounded-lg shadow-md">
        <h2 class="text-3xl text-teal-500 font-bold mb-4">Create Accommodation</h2>
        <form id="create-accommodation-form">
            <div class="mb-4">
                <label for="name" class="block text-teal-500 text-sm font-bold mb-2">Name</label>
                <input type="text" id="name" name="name" class="block w-full p-2 bg-gray-100 border border-gray-200 rounded-lg focus:outline-none focus:ring-teal-500 focus:border-teal-500" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-teal-500 text-sm font-bold mb-2">Description</label>
                <textarea id="description" name="description" class="block w-full p-2 bg-gray-100 border border-gray-200 rounded-lg focus:outline-none focus:ring-teal-500 focus:border-teal-500" required></textarea>
            </div>
            <div class="mb-4">
                <label for="price" class="block text-teal-500 text-sm font-bold mb-2">Price</label>
                <input type="number" id="price" name="price" class="block w-full p-2 bg-gray-100 border border-gray-200 rounded-lg focus:outline-none focus:ring-teal-500 focus:border-teal-500" required>
            </div>
            <div class="mb-4">
                <label for="capacity" class="block text-teal-500 text-sm font-bold mb-2">Capacity</label>
                <input type="number" id="capacity" name="capacity" class="block w-full p-2 bg-gray-100 border border-gray-200 rounded-lg focus:outline-none focus:ring-teal-500 focus:border-teal-500" required>
            </div>
            <div class="mb-4">
                <label for="type" class="block text-teal-500 text-sm font-bold mb-2">Type</label>
                <select id="type" name="type" class="block w-full p-2 bg-gray-100 border border-gray-200 rounded-lg focus:outline-none focus:ring-teal-500 focus:border-teal-500" required>
                    <option value="">Select Type</option>
                    <option value="hotel">Hotel</option>
                    <option value="resort">Resort</option>
                    <option value="hostel">Hostel</option>
                </select>
            </div>
            <button type="submit" class="w-full p-2 bg-teal-500 text-emerald-600 font-bold rounded-lg hover:bg-teal-700 hover:text-emerald-700">Create Accommodation</button>
        </form>
    </div>
</main>

<script>
    $(document).ready(function() {
        $('#create-accommodation-form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '../backend/accommodations.php',
                data: $(this).serialize(),
                success: function() {
                    window.location.href = 'list_<?php echo $mod_slug; ?>.php';
                }
            });
        });
    });
</script>

<?php
// Include footer
include 'footer.php';
?>