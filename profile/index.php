<?php
session_start();
include('../db.php'); // Adjust the path to where db.php is located
// Check if user is logged in
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php'); 
    exit();
}

// Fetch user data
$query = "SELECT * FROM users WHERE id = $1";
$result = pg_query_params($con, $query, [$user_id]);
$user = pg_fetch_assoc($result);

if (!$user) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="assets/js/script.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="profile-card">
            <div class="profile-header">
                <img src="uploads/<?php echo htmlspecialchars($user['profile_picture'] ?? 'default.png'); ?>" alt="Profile Picture" class="profile-pic">
                <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>

            <div class="profile-details">
                <h3>About Me</h3>
                <p><?php echo htmlspecialchars($user['bio'] ?? 'No bio added yet.'); ?></p>
                <a href="edit_profile.php" class="edit-btn">Edit Profile</a>
            </div>

            <div class="settings">
                <h3>Account Settings</h3>
                <a href="update_password.php">Change Password</a>
                <a href="/logout.php" class="logout-btn">Logout</a>

            </div>
        </div>
    </div>
</body>
</html>