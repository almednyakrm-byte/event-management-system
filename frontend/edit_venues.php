**edit_venues.php**

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get venue ID from URL
$id = $_GET['id'];

// Fetch existing venue details via AJAX
$js = "
    $(document).ready(function() {
        $.get('../backend/venues.php?id=" . $id . "')
            .done(function(data) {
                $('#venue_name').val(data.name);
                $('#venue_address').val(data.address);
                $('#venue_phone').val(data.phone);
                $('#venue_email').val(data.email);
            })
            .fail(function() {
                alert('Error fetching venue details');
            });
    });
";

// Update venue details on form submit
$js .= "
    $(document).ready(function() {
        $('#edit-venue-form').submit(function(event) {
            event.preventDefault();
            $.ajax({
                type: 'PUT',
                url: '../backend/venues.php',
                data: {
                    id: " . $id . ",
                    name: $('#venue_name').val(),
                    address: $('#venue_address').val(),
                    phone: $('#venue_phone').val(),
                    email: $('#venue_phone').val()
                },
                success: function(data) {
                    window.location.href = 'list_" . $_SESSION['mod_slug'] . ".php';
                },
                error: function() {
                    alert('Error updating venue details');
                }
            });
        });
    });
";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Venue</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto p-4 bg-white rounded-lg shadow-md">
        <h2 class="text-lg font-bold text-emerald-600 mb-4">Edit Venue</h2>
        <form id="edit-venue-form" class="space-y-4">
            <div>
                <label for="venue_name" class="block text-sm font-medium text-gray-700">Venue Name</label>
                <input type="text" id="venue_name" name="name" class="block w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div>
                <label for="venue_address" class="block text-sm font-medium text-gray-700">Venue Address</label>
                <input type="text" id="venue_address" name="address" class="block w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div>
                <label for="venue_phone" class="block text-sm font-medium text-gray-700">Venue Phone</label>
                <input type="tel" id="venue_phone" name="phone" class="block w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div>
                <label for="venue_email" class="block text-sm font-medium text-gray-700">Venue Email</label>
                <input type="email" id="venue_email" name="email" class="block w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500">
            </div>
            <button type="submit" class="px-4 py-2 text-white bg-emerald-600 rounded-md hover:bg-emerald-700">Save Changes</button>
        </form>
    </div>

    <script>
        <?= $js ?>
    </script>
</body>
</html>


**Note:** This code assumes that you have a `backend/venues.php` file that handles the AJAX requests and updates the venue details. The `list_" . $_SESSION['mod_slug'] . ".php` file is also assumed to be the page that the user will be redirected to after updating the venue details.