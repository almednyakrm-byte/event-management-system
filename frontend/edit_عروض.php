**edit_عروض.php**

<?php
// Session validation
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get id from URL
$id = $_GET['id'];

// Fetch existing record details via AJAX
$js = "
<script>
    $(document).ready(function() {
        $.get('../backend/عروض.php?id=" . $id . "').done(function(data) {
            $('#name').val(data.name);
            $('#description').val(data.description);
            $('#price').val(data.price);
        });
    });
</script>
";

// Include header and JS
include 'header.php';
echo $js;

// Form
?>
<div class="max-w-md mx-auto p-8 bg-white rounded-lg shadow-md">
    <h2 class="text-lg font-bold text-emerald-600 mb-4">Edit عروض</h2>
    <form id="edit-form" class="space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" id="name" name="name" class="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:border-teal-500 focus:ring-teal-500">
        </div>
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea id="description" name="description" class="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:border-teal-500 focus:ring-teal-500"></textarea>
        </div>
        <div>
            <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
            <input type="number" id="price" name="price" class="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:border-teal-500 focus:ring-teal-500">
        </div>
        <button type="submit" class="w-full px-4 py-2 text-white bg-emerald-600 rounded-md hover:bg-emerald-700">Save Changes</button>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#edit-form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'PUT',
                url: '../backend/عروض.php',
                data: $(this).serialize() + '&id=' + <?php echo $id; ?>,
                success: function() {
                    window.location.href = 'list_عروض.php';
                }
            });
        });
    });
</script>


**header.php**

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit عروض</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body>
    <?php echo $js; ?>
    <div class="container mx-auto p-4">
        <?php echo $content; ?>
    </div>
</body>
</html>


**Note:** Make sure to replace `list_عروض.php` with the actual URL of the list page. Also, this code assumes that you have jQuery and Tailwind CSS installed. If not, you can include them by adding the following lines to your `<head>` section:

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">