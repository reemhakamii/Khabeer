<?php
session_start();
include('db.php');
include('authFunctions.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prevent SQL injection by using prepared statements
    $query = "SELECT * FROM users WHERE email = $1";
    $result = pg_query_params($con, $query, [$email]);
    $user = pg_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        // Store user ID and type in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type']; // Store user type in session

        // Redirect based on user type
        switch ($user['user_type']) {
            case 'organization':
                header('Location: organization_interface.php');
                break;
            case 'individual':
                header('Location: individual_interface.php');
                break;
            case 'consultant':
                header('Location: consultant_interface.php');
                break;
            default:
                $error = "Invalid user type!";
                break;
        }
        exit();
    } else {
        $error = "Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #183041, #035a66);
            color: #EEEEEE;
        }

        .login-container {
            background: rgba(24, 48, 65, 0.95);
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.4);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        .login-container h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
        }

        .login-container input[type="email"],
        .login-container input[type="password"] {
            background: #EEEEEE;
            color: #183041;
            border: none;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            font-size: 1rem;
            outline: none;
            transition: box-shadow 0.3s;
        }

        .login-container input[type="email"]:focus,
        .login-container input[type="password"]:focus {
            box-shadow: 0px 0px 10px rgba(4, 208, 126, 0.8);
        }

        .login-container button {
            background: #04d07e;
            color: #183041;
            border: none;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .login-container button:hover {
            background: #035a66;
            color: #EEEEEE;
        }

        .login-container p {
            margin: 1rem 0;
            font-size: 0.9rem;
        }

        .login-container p a {
            color: #04d07e;
            text-decoration: none;
            transition: color 0.3s;
        }

        .login-container p a:hover {
            color: #c5f122;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1rem 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #EEEEEE;
        }

        .divider::before {
            margin-right: 0.5em;
        }

        .divider::after {
            margin-left: 0.5em;
        }

        .social-login {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }

        .social-login button {
            flex: 1;
            background: transparent;
            border: 2px solid #EEEEEE;
            color: #EEEEEE;
            padding: 0.75rem;
            border-radius: 5px;
            margin: 0 0.5rem;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .social-login button:hover {
            background: #04d07e;
            color: #183041;
            border-color: #04d07e;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form method="post">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Sign in</button>
        </form>
        <p><a href="forgot_password.php">Forgot Password?</a></p>
        <div class="divider">or continue with</div>
        <div class="social-login">
            <button>Google</button>
            <button>GitHub</button>
            <button>Facebook</button>
        </div>
        <p>Don’t have an account yet? <a href="register.php">Register for free</a></p>
    </div>
</body>
</html>
