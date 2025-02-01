<?php
include('db.php'); 
include('authFunctions.php');

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user input
    $name = pg_escape_string($con, $_POST['name']);
    $email = pg_escape_string($con, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user_type = pg_escape_string($con, $_POST['user_type']);
    $specialization = pg_escape_string($con, $_POST['specialization'] ?? '');

    // Check if the email already exists
    $checkEmailQuery = "SELECT * FROM users WHERE email = '$email'";
    $checkEmailResult = pg_query($con, $checkEmailQuery);
    
    if (pg_num_rows($checkEmailResult) > 0) {
        $errorMessage = "The email address is already registered. You can <a href='login.php'>log in</a> instead.";
    } else {
        // Insert into users table
        $query = "INSERT INTO users (name, email, password, user_type, specialization) 
                  VALUES ('$name', '$email', '$password', '$user_type', '$specialization') RETURNING id";
        $result = pg_query($con, $query);

        if ($result) {
            $user = pg_fetch_assoc($result);
            $user_id = $user['id'];

            // Handle consultant-specific fields
            if ($user_type === 'consultant') {
                $expertise = pg_escape_string($con, $_POST['expertise'] ?? '');
                $certifications = pg_escape_string($con, $_POST['certifications'] ?? '');
                $profile_picture = '';

                // Handle profile picture upload
                if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES['profile_picture']['tmp_name'];
                    $profile_picture = 'uploads/profile_pictures/' . basename($_FILES['profile_picture']['name']);
                    
                    if (!is_dir('uploads/profile_pictures')) {
                        mkdir('uploads/profile_pictures', 0777, true);
                    }

                    if (move_uploaded_file($tmp_name, $profile_picture)) {
                        $profile_picture = pg_escape_string($con, $profile_picture);
                    } else {
                        $errorMessage = "Failed to upload profile picture.";
                    }
                }

                $consultant_query = "INSERT INTO consultants (user_id, expertise, certifications, profile_picture) 
                                     VALUES ('$user_id', '$expertise', '$certifications', '$profile_picture')";
                pg_query($con, $consultant_query);
            }

            // Handle organization-specific fields
            if ($user_type === 'organization') {
                $org_name = pg_escape_string($con, $_POST['org_name'] ?? '');
                $org_address = pg_escape_string($con, $_POST['org_address'] ?? '');

                $organization_query = "INSERT INTO organizations (user_id, org_name, org_address) 
                                       VALUES ('$user_id', '$org_name', '$org_address')";
                pg_query($con, $organization_query);
            }

            header('Location: login.php');
            exit();
        } else {
            $errorMessage = "An unexpected error occurred: " . pg_last_error($con);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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

        .register-container {
            background: rgba(24, 48, 65, 0.95);
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.4);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        .register-container h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .register-container form {
            display: flex;
            flex-direction: column;
        }

        .register-container input[type="text"],
        .register-container input[type="email"],
        .register-container input[type="password"],
        .register-container select,
        .register-container textarea {
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

        .register-container input[type="text"]:focus,
        .register-container input[type="email"]:focus,
        .register-container input[type="password"]:focus,
        .register-container select:focus,
        .register-container textarea:focus {
            box-shadow: 0px 0px 10px rgba(4, 208, 126, 0.8);
        }

        .register-container button {
            background: #04d07e;
            color: #183041;
            border: none;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .register-container button:hover {
            background: #035a66;
            color: #EEEEEE;
        }

        .register-container p {
            margin: 1rem 0;
            font-size: 0.9rem;
        }

        .register-container p a {
            color: #04d07e;
            text-decoration: none;
            transition: color 0.3s;
        }

        .register-container p a:hover {
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

    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="user_type" required>
                <option value="">Select User Type</option>
                <option value="consultant">Consultant</option>
                <option value="individual">Individual</option>
                <option value="organization">Organization</option>
            </select>
            
            <div id="consultantFields" style="display:none;">
                <input type="text" name="expertise" placeholder="Expertise">
                <textarea name="certifications" placeholder="Certifications"></textarea>
                <input type="file" name="profile_picture" placeholder="Profile Picture">
                <input type="text" name="specialization" placeholder="Specialization">
            </div>

            <div id="organizationFields" style="display:none;">
                <input type="text" name="org_name" placeholder="Organization Name">
                <input type="text" name="org_address" placeholder="Organization Address">
            </div>
            
            <button type="submit">Register</button>
        </form>
        
        <?php if (!empty($errorMessage)): ?>
            <div class="error-message"><?= $errorMessage; ?></div>
        <?php endif; ?>

        <p>Already have an account? <a href="login.php" class="login-button">Log in</a></p>
    </div>

    <script>
        document.querySelector('select[name="user_type"]').addEventListener('change', function () {
            const userType = this.value;
            document.getElementById('consultantFields').style.display = (userType === 'consultant') ? 'block' : 'none';
            document.getElementById('organizationFields').style.display = (userType === 'organization') ? 'block' : 'none';
        });
    </script>
</body>
</html>