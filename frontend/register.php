<!-- register.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            color: #333;
            font-weight: bold;
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #666;
        }
        .form-group input {
            width: 100%;
            height: 40px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            cursor: pointer;
        }
        .form-group input[type="submit"]:hover {
            background-color: #3e8e41;
        }
        .error {
            color: #f00;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Register</h2>
        </div>
        <form id="register-form">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required pattern="[A-Za-z\u0600-\u06FF0-9\s]+" oninvalid="this.setCustomValidity('Username must contain only letters, numbers and spaces.')">
                <div class="error" id="username-error"></div>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required oninvalid="this.setCustomValidity('Please enter a valid email address.')">
                <div class="error" id="email-error"></div>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required pattern="[A-Za-z0-9!@#$%^&*()_+=-{};:'<>,./?]" oninvalid="this.setCustomValidity('Password must contain at least one uppercase letter, one lowercase letter, one number and one special character.')">
                <div class="error" id="password-error"></div>
            </div>
            <div class="form-group">
                <input type="submit" value="Register">
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#register-form').submit(function(e) {
                e.preventDefault();
                var username = $('#username').val();
                var email = $('#email').val();
                var password = $('#password').val();
                var error = false;

                if (!username) {
                    $('#username-error').text('Please enter a username.');
                    error = true;
                } else {
                    $('#username-error').text('');
                }

                if (!email) {
                    $('#email-error').text('Please enter an email address.');
                    error = true;
                } else {
                    $('#email-error').text('');
                }

                if (!password) {
                    $('#password-error').text('Please enter a password.');
                    error = true;
                } else {
                    $('#password-error').text('');
                }

                if (!error) {
                    $.ajax({
                        type: 'POST',
                        url: '../backend/auth.php?action=register',
                        data: {
                            username: username,
                            email: email,
                            password: password
                        },
                        success: function(response) {
                            if (response === 'success') {
                                alert('Registration successful. Please login to continue.');
                                window.location.href = 'login.php';
                            } else {
                                alert('Registration failed. Please try again.');
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Error: ' + error);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>


This code uses Tailwind CSS to create a premium-looking registration form. It includes validation rules for the form fields and uses AJAX to submit the form to the backend PHP script. The PHP script is not included in this code snippet, but it should be created in a separate file named `auth.php` in the `backend` directory.