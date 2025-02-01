<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Error: User is not logged in.";
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details, including user type
$user_query = "SELECT * FROM users WHERE id = $1";
$user_result = pg_query_params($con, $user_query, array($user_id));

if (!$user_result) {
    echo "Error fetching user details: " . pg_last_error($con);
    exit();
}

$user = pg_fetch_assoc($user_result);

// Check user type and display relevant content
switch ($user['user_type']) {
    case 'individual':
        include('individual_interface.php');
        break;

    case 'consultant':
        include('consultant_interface.php');
        break;

    case 'organization':
        include('organization_interface.php');
        break;

    default:
        echo "Unknown user type.";
        exit();
}
?>