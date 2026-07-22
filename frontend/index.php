<?php
// Session check
session_start();
if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تطبيق إدارةvents وفعاليات مع إدارة دعوات وتذاكر</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="h-screen bg-gray-200">
    <header class="bg-orange-500 text-white p-4 text-center">
        <h1 class="text-3xl font-bold">تطبيق إدارةvents وفعاليات مع إدارة دعوات وتذاكر</h1>
    </header>
    <main class="container mx-auto p-4 pt-6 md:p-6 lg:p-12 xl:p-24">
        <div class="glassmorphism-card bg-white bg-opacity-20 rounded-2xl p-4">
            <h2 class="text-2xl font-bold mb-4">مرحبا <?php echo $_SESSION['username']; ?></h2>
            <button class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded" onclick="logout()">تسجيل الخروج</button>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                <div class="glassmorphism-card bg-white bg-opacity-20 rounded-2xl p-4">
                    <h3 class="text-xl font-bold mb-2">عدد الفعاليات</h3>
                    <p id="events-count" class="text-3xl font-bold">0</p>
                </div>
                <div class="glassmorphism-card bg-white bg-opacity-20 rounded-2xl p-4">
                    <h3 class="text-xl font-bold mb-2">عدد الدعوات</h3>
                    <p id="invitations-count" class="text-3xl font-bold">0</p>
                </div>
                <div class="glassmorphism-card bg-white bg-opacity-20 rounded-2xl p-4">
                    <h3 class="text-xl font-bold mb-2">عدد التذاكر</h3>
                    <p id="tickets-count" class="text-3xl font-bold">0</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                <a href="events.php" class="glassmorphism-card bg-white bg-opacity-20 rounded-2xl p-4 hover:shadow-lg">
                    <h3 class="text-xl font-bold mb-2">إدارة الفعاليات</h3>
                    <p class="text-lg font-bold">إضافة و تعديل و حذف الفعاليات</p>
                </a>
                <a href="invitations.php" class="glassmorphism-card bg-white bg-opacity-20 rounded-2xl p-4 hover:shadow-lg">
                    <h3 class="text-xl font-bold mb-2">إدارة الدعوات</h3>
                    <p class="text-lg font-bold">إضافة و تعديل و حذف الدعوات</p>
                </a>
                <a href="tickets.php" class="glassmorphism-card bg-white bg-opacity-20 rounded-2xl p-4 hover:shadow-lg">
                    <h3 class="text-xl font-bold mb-2">إدارة التذاكر</h3>
                    <p class="text-lg font-bold">إضافة و تعديل و حذف التذاكر</p>
                </a>
            </div>
        </div>
    </main>

    <script>
        // Fetch stats dynamically via Javascript API calls from the backend files
        fetch('api/events-count.php')
            .then(response => response.json())
            .then(data => document.getElementById('events-count').innerHTML = data.count);

        fetch('api/invitations-count.php')
            .then(response => response.json())
            .then(data => document.getElementById('invitations-count').innerHTML = data.count);

        fetch('api/tickets-count.php')
            .then(response => response.json())
            .then(data => document.getElementById('tickets-count').innerHTML = data.count);

        // Logout function
        function logout() {
            fetch('api/logout.php')
                .then(response => response.json())
                .then(data => window.location.href = 'login.php');
        }
    </script>
</body>
</html>