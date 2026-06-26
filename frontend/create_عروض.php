**create_عروض.php**

<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

// Include header and navigation
require_once 'header.php';
require_once 'navigation.php';
?>

<div class="container mx-auto p-4 pt-6 md:p-6 lg:p-12 xl:p-12">
    <div class="bg-white rounded-lg shadow-md p-4">
        <h2 class="text-lg font-bold text-emerald-600 mb-4">إضافة عرض جديد</h2>
        <form id="create-offer-form">
            <div class="mb-4">
                <label for="name" class="block text-sm font-bold text-gray-700 mb-2">اسم العرض:</label>
                <input type="text" id="name" name="name" class="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg focus:ring-emerald-600 focus:border-emerald-600" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-bold text-gray-700 mb-2">وصف العرض:</label>
                <textarea id="description" name="description" class="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg focus:ring-emerald-600 focus:border-emerald-600" required></textarea>
            </div>
            <div class="mb-4">
                <label for="price" class="block text-sm font-bold text-gray-700 mb-2">سعر العرض:</label>
                <input type="number" id="price" name="price" class="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg focus:ring-emerald-600 focus:border-emerald-600" required>
            </div>
            <div class="mb-4">
                <label for="start_date" class="block text-sm font-bold text-gray-700 mb-2">تاريخ بداية العرض:</label>
                <input type="date" id="start_date" name="start_date" class="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg focus:ring-emerald-600 focus:border-emerald-600" required>
            </div>
            <div class="mb-4">
                <label for="end_date" class="block text-sm font-bold text-gray-700 mb-2">تاريخ نهاية العرض:</label>
                <input type="date" id="end_date" name="end_date" class="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg focus:ring-emerald-600 focus:border-emerald-600" required>
            </div>
            <button type="submit" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">إضافة عرض</button>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#create-offer-form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '../backend/عروض.php',
                data: $(this).serialize(),
                success: function(response) {
                    if (response === 'success') {
                        window.location.href = 'list_عروض.php';
                    } else {
                        alert('Error adding offer');
                    }
                }
            });
        });
    });
</script>

<?php
// Include footer
require_once 'footer.php';
?>


**Note:** This code assumes that you have jQuery and Bootstrap installed in your project. Also, make sure to replace `../backend/عروض.php` with the actual URL of your backend script that handles the form submission.