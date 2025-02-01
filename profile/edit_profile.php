<?php
session_start();
include('../db.php'); // Adjust the path to where db.php is located

// Language handling
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'english'; // Default language
}

// Change language if selected
if (isset($_GET['lang']) && in_array($_GET['lang'], ['english', 'arabic'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Include the appropriate language file
if ($_SESSION['lang'] == 'arabic') {
    include('languages/arabic.php');

} else {
    include('languages/english.php');}

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $bio = $_POST['bio'];

    // Handle profile picture upload
    if ($_FILES['profile_picture']['name']) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);
        $query = "UPDATE users SET name = $1, bio = $2, profile_picture = $3 WHERE id = $4";
        $params = [$name, $bio, basename($_FILES["profile_picture"]["name"]), $user_id];
    } else {
        $query = "UPDATE users SET name = $1, bio = $2 WHERE id = $3";
        $params = [$name, $bio, $user_id];
    }

    $result = pg_query_params($con, $query, $params);
    header('Location: index.php');
    exit();
}

// Fetch user details
$query = "SELECT * FROM users WHERE id = $1";
$result = pg_query_params($con, $query, [$user_id]);
$user = pg_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] == 'arabic' ? 'ar' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['edit_profile']; ?></title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container">
        <!-- Language Selection -->
        <form method="get" action="">
            <select name="lang" onchange="this.form.submit()">
                <option value="english" <?php if ($_SESSION['lang'] == 'english') echo 'selected'; ?>>English</option>
                <option value="arabic" <?php if ($_SESSION['lang'] == 'arabic') echo 'selected'; ?>>العربية</option>
            </select>
        </form>

        <!-- Include Sidebar -->

        <div class="main-content">
            <h1><?php echo $lang['edit_profile']; ?></h1>
            <form method="POST" enctype="multipart/form-data">
                <label><?php echo $lang['name']; ?>:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

                <label><?php echo $lang['bio']; ?>:</label>
                <textarea name="bio"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>

                <label><?php echo $lang['profile_picture']; ?>:</label>
                <input type="file" name="profile_picture">

                <button type="submit"><?php echo $lang['update_profile']; ?></button>
            </form>
        </div>
    </div>
</body>
</html>