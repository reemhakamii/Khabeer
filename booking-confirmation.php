<?php
session_start();
include('db.php');

// Get booking details from URL
$consultant_id = $_GET['consultant_id'] ?? null;
$booking_date = $_GET['booking_date'] ?? null;
$client_id = $_SESSION['user_id'] ?? null;

if (!$consultant_id || !$booking_date || !$client_id) {
    echo "Invalid booking details.";
    exit();
}

// Fetch consultant details from the database
$query = "SELECT u.name, u.specialization FROM users u 
          JOIN consultants c ON c.user_id = u.id WHERE c.id = $1";
$result = pg_query_params($con, $query, [$consultant_id]);
$consultant = pg_fetch_assoc($result);

if (!$consultant) {
    echo "Consultant not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
</head>
<body>
    <h2>Booking Confirmation</h2>
    <p><strong>Consultant:</strong> <?php echo htmlspecialchars($consultant['name']); ?></p>
    <p><strong>Specialization:</strong> <?php echo htmlspecialchars($consultant['specialization']); ?></p>
    <p><strong>Date & Time:</strong> <?php echo date('F j, Y, g:i A', strtotime($booking_date)); ?></p>

    <form action="payment.php" method="POST">
        <input type="hidden" name="consultant_id" value="<?php echo $consultant_id; ?>">
        <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
        <input type="hidden" name="booking_date" value="<?php echo $booking_date; ?>">
        <button type="submit">Confirm & Proceed to Payment</button>
    </form>
</body>
</html>