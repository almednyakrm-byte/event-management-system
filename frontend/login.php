<!-- login.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <style>
        body {
            background-image: linear-gradient(to bottom, #1a1d23, #2b2f3a);
            background-size: 100% 300px;
            background-position: 0% 100%;
            transition: background-position 1s linear;
        }
        .glassmorphic {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .gradient {
            background-image: linear-gradient(to bottom, #1a1d23, #2b2f3a);
            background-size: 100% 300px;
            background-position: 0% 100%;
            transition: background-position 1s linear;
        }
    </style>
</head>
<body class="h-screen w-screen flex justify-center items-center bg-gray-200">
    <div class="glassmorphic w-96 p-10 rounded-lg shadow-md">
        <div class="text-center mb-4 text-3xl font-bold text-emerald-600">Login</div>
        <form id="login-form" class="space-y-4">
            <div class="space-y-1">
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="username" name="username" class="block w-full px-3 py-2 placeholder-gray-400 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-emerald-600 focus:border-emerald-600" placeholder="Username" pattern="[A-Za-z\u0600-\u06FF0-9\s]+">
                <div id="username-error" class="text-red-500 text-sm"></div>
            </div>
            <div class="space-y-1">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" class="block w-full px-3 py-2 placeholder-gray-400 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-emerald-600 focus:border-emerald-600" placeholder="Password">
                <div id="password-error" class="text-red-500 text-sm"></div>
            </div>
            <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-teal-500 border border-teal-500 rounded-md hover:bg-teal-700 focus:outline-none focus:ring focus:ring-emerald-600 focus:border-emerald-600">Login</button>
            <div class="text-center text-sm text-gray-500">Don't have an account? <a href="register.php" class="text-emerald-600 hover:text-emerald-800">Register</a></div>
        </form>
    </div>
    <script>
        const form = document.getElementById('login-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const response = await fetch('../backend/auth.php?action=login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, password })
            });
            const data = await response.json();
            if (data.success) {
                window.location.href = 'dashboard.php';
            } else {
                const errors = data.errors;
                if (errors.username) {
                    document.getElementById('username-error').textContent = errors.username;
                } else {
                    document.getElementById('username-error').textContent = '';
                }
                if (errors.password) {
                    document.getElementById('password-error').textContent = errors.password;
                } else {
                    document.getElementById('password-error').textContent = '';
                }
            }
        });
    </script>
</body>
</html>


This code uses Tailwind CSS to create a premium-looking login page with a glassmorphic layout and gradients. The form includes validation rules for the username and password fields, and uses AJAX with the Fetch API to submit the credentials to the backend. The response from the backend is handled dynamically, displaying any error messages to the user. The page also includes a link to the registration page.