<?php
session_start();
include('db.php'); // Database connection

// Check if the user is logged in and is a consultant
$consultant_id = $_SESSION['user_id'] ?? null;
$user_type = $_SESSION['user_type'] ?? null;

if (!$consultant_id || $user_type !== 'consultant') {
    header('Location: login.php'); // Redirect to login if not logged in or not a consultant
    exit();
}

// Fetch the booking details
$booking_id = $_GET['booking_id'] ?? null;

if (!$booking_id) {
    echo "Invalid booking ID.";
    exit();
}

// Get the current status of the booking
$query_booking = "SELECT b.status
                  FROM bookings b
                  WHERE b.id = $1 AND b.consultant_id = $2";
$result_booking = pg_query_params($con, $query_booking, [$booking_id, $consultant_id]);

if (pg_num_rows($result_booking) === 0) {
    echo "Booking not found or you are not authorized to update this booking.";
    exit();
}

$booking = pg_fetch_assoc($result_booking);

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'] ?? '';

    // Validate status
    if (empty($new_status)) {
        echo "Please select a valid status.";
        exit();
    }

    // Update the status in the database
    $query_update = "UPDATE bookings SET status = $1 WHERE id = $2";
    pg_query_params($con, $query_update, [$new_status, $booking_id]);

    // Redirect back to the consultant dashboard after updating
    header('Location: consultant_dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Status</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="status-change-container">
        <h2>Change Status for Booking</h2>
        <form action="change_status.php?booking_id=<?php echo $booking_id; ?>" method="POST">
            <label for="status">Select Status:</label>
            <select name="status" id="status">
                <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="completed" <?php echo $booking['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
            <button type="submit" class="button">Update Status</button>
        </form>
    </div>
</body>
</html>