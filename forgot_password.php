<?php
include('db.php');

if (isset($_POST['reset'])) {
    $email = $_POST['email'];
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = pg_query($con, $query);
    $user = pg_fetch_assoc($result);

    if ($user) {
        $token = bin2hex(random_bytes(50));
        $reset_link = "http://yourdomain.com/reset_password.php?token=$token";

        // Save the token in the database
        $query = "UPDATE users SET reset_token = '$token', token_expiry = NOW() + INTERVAL '1 hour' WHERE email = '$email'";
        pg_query($con, $query);

        // Send the reset email
        $subject = "Password Reset Request";
        $message = "Click on the link below to reset your password:\n\n$reset_link";
        $headers = "From: no-reply@yourdomain.com";

        // Mail function returns false if it fails
        $mail_sent = mail($email, $subject, $message, $headers);

        if ($mail_sent) {
            $success = "Password reset link has been sent to your email!";
        } else {
            $error = "Failed to send reset email. Please try again later.";
        }
    } else {
        $error = "No user found with this email!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="reset-container">
        <h2>Reset Password</h2>
        <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="post">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" name="reset">Send Reset Link</button>
        </form>
        <p><a href="login.php">Back to Login</a></p>
    </div>
</body>
</html>