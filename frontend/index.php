<?php
session_start();

// Check if user is authenticated
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة أحداث وقاعات</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
        }
        .glassmorphism-card {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .emerald-600 {
            color: #008E73;
        }
        .teal-500 {
            color: #0097A7;
        }
    </style>
</head>
<body>
    <div class="flex justify-center items-center h-screen">
        <div class="glassmorphism-card w-1/2 p-10">
            <h1 class="text-3xl font-bold text-center mb-5">نظام إدارة أحداث وقاعات</h1>
            <p class="text-lg text-center mb-10">مرحباً <?php echo $_SESSION['username']; ?></p>
            <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded mb-5" onclick="location.href='logout.php'">تسجيل خروج</button>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h2 class="text-lg font-bold mb-2">إحصائيات</h2>
                    <div id="stats-grid"></div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h2 class="text-lg font-bold mb-2">إدارة الأحداث</h2>
                    <button class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded mb-2">إضافة حدث</button>
                    <button class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded mb-2">عرض الأحداث</button>
                </div>
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h2 class="text-lg font-bold mb-2">إدارة الغرف</h2>
                    <button class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded mb-2">إضافة غرفة</button>
                    <button class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded mb-2">عرض الغرف</button>
                </div>
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h2 class="text-lg font-bold mb-2">إدارة الإقامة</h2>
                    <button class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded mb-2">إضافة إقامة</button>
                    <button class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded mb-2">عرض الإقامة</button>
                </div>
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h2 class="text-lg font-bold mb-2">إدارة الوجبات</h2>
                    <button class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded mb-2">إضافة وجبة</button>
                    <button class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded mb-2">عرض الوجبات</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        fetch('/api/stats')
            .then(response => response.json())
            .then(data => {
                const statsGrid = document.getElementById('stats-grid');
                data.forEach(stat => {
                    const statCard = document.createElement('div');
                    statCard.classList.add('bg-white', 'rounded-lg', 'shadow-md', 'p-4', 'mb-4');
                    statCard.innerHTML = `
                        <h2 class="text-lg font-bold mb-2">${stat.title}</h2>
                        <p class="text-lg">${stat.value}</p>
                    `;
                    statsGrid.appendChild(statCard);
                });
            })
            .catch(error => console.error(error));
    </script>
</body>
</html>


This code uses Tailwind CSS for styling and includes a session check to redirect to the login page if the user is not authenticated. The dashboard layout includes a welcome message, logout button, overview stats grid, and quick links to manage modules. The stats grid is populated dynamically via a JavaScript API call to the backend files.

Note: You will need to create a backend API to fetch the stats data. The API endpoint should return a JSON response with the stats data. You can use a PHP framework like Laravel or a simple PHP script to create the API.

Also, you will need to create the logout.php file to handle the logout functionality.

This code assumes that you have the following files and folders in your project:

* `index.php` (this file)
* `login.php`
* `logout.php`
* `api/stats` (backend API endpoint to fetch stats data)
* `public/css` (folder for CSS files)
* `public/js` (folder for JavaScript files)

You will need to adjust the file paths and folder structure according to your project setup.