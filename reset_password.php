<?php
include('db.php');

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $query = "SELECT * FROM users WHERE reset_token = '$token' AND token_expiry > NOW()";
    $result = pg_query($con, $query);
    $user = pg_fetch_assoc($result);

    if ($user) {
        if (isset($_POST['reset_password'])) {
            $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
            $query = "UPDATE users SET password = '$new_password', reset_token = NULL, token_expiry = NULL WHERE id = {$user['id']}";
            pg_query($con, $query);
            $success = "Your password has been reset! <a href='login.php'>Login</a>";
        }
    } else {
        $error = "Invalid or expired token!";
    }
} else {
    header('Location: forgot_password.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="reset-password-container">
        <h2>Set a New Password</h2>
        <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <?php if (!isset($success)) { ?>
        <form method="post">
            <input type="password" name="new_password" placeholder="New Password" required>
            <button type="submit" name="reset_password">Reset Password</button>
        </form>
        <?php } ?>
    </div>
</body>
</html>