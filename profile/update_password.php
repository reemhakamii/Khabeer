<?php
session_start();
include('../db.php'); // Adjust the path to where db.php is located

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $query = "UPDATE users SET password = $1 WHERE id = $2";
    $result = pg_query_params($con, $query, [$new_password, $user_id]);
    header('Location: index.php');
    exit();
}
?>

<form method="POST">
    <label>New Password:</label>
    <input type="password" name="password" required>
    <button type="submit">Update Password</button>
</form>