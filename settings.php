<?php
session_start();

// Default language is English
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'english';
}

// Change language if selected
if (isset($_GET['lang']) && in_array($_GET['lang'], ['english', 'arabic'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Include the appropriate language file
if ($_SESSION['lang'] == 'arabic') {
    include('arabic.php');
} else {
    include('english.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['title']; ?></title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style> body[lang="arabic"] {
    direction: rtl;
}

body[lang="english"] {
    direction: ltr;
}</style>
</head>
<body>
    <div class="container">
        <div class="settings">
            <h1><?php echo $lang['title']; ?></h1>
            
            <!-- Language Selection -->
            <form method="get" action="">
                <select name="lang" onchange="this.form.submit()">
                    <option value="english" <?php if ($_SESSION['lang'] == 'english') echo 'selected'; ?>>English</option>
                    <option value="arabic" <?php if ($_SESSION['lang'] == 'arabic') echo 'selected'; ?>>العربية</option>
                </select>
            </form>

            <div class="profile-settings">
                <label for="name"><?php echo $lang['name']; ?>:</label>
                <input type="text" id="name" name="name">

                <label for="bio"><?php echo $lang['bio']; ?>:</label>
                <textarea id="bio" name="bio"></textarea>

                <button type="submit"><?php echo $lang['update']; ?></button>
            </div>
            
            <div class="account-settings">
                <a href="update_password.php"><?php echo $lang['change_password']; ?></a>
                <a href="logout.php"><?php echo $lang['logout']; ?></a>
            </div>
        </div>
    </div>
    <!-- Language Switcher Button -->
<button onclick="toggleLanguage()">Switch Language</button>

<script>
    function toggleLanguage() {
        const currentLang = document.body.getAttribute('data-lang');
        const newLang = currentLang === 'english' ? 'arabic' : 'english';
        document.body.setAttribute('data-lang', newLang);

        // Send the selected language to the server (using fetch or AJAX)
        fetch(`settings.php?lang=${newLang}`).then(response => {
            return response.text();
        }).then(data => {
            // Update the page content dynamically with the new language data
            document.body.innerHTML = data;
        });
    }
</script>
</body>
</html>