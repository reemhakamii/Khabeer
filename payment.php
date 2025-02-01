<?php
session_start(); // Start the session to access session variables
include('db.php'); // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Get the booking ID from the URL
$booking_id = $_GET['booking_id'] ?? null;

if (!$booking_id) {
    // Handle error if booking ID is not provided
    echo "Error: Booking ID is missing.";
    exit();
}

// Get the amount for the booking based on the booking ID
$query = "SELECT amount FROM bookings WHERE id = $1";
$result = pg_query_params($con, $query, [$booking_id]);

if ($result && pg_num_rows($result) > 0) {
    $row = pg_fetch_assoc($result);
    $amount = $row['amount'];
} else {
    echo "Error: Booking not found.";
    exit();
}

// Sanitize and validate the amount (assuming it's in SAR)
if (!is_numeric($amount) || $amount <= 0) {
    echo "Error: Invalid amount.";
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <h2>Complete Payment for Your Booking</h2>
    <form action="payment.php?booking_id=<?php echo htmlspecialchars($booking_id); ?>" method="POST">
        <script src="https://checkout.stripe.com/checkout.js" 
            class="stripe-button"
            data-key="pk_test_your_public_key"
            data-amount="<?php echo $amount * 100; ?>"
            data-name="Consultancy Platform"
            data-description="Payment for Consultation"
            data-image="logo.png"
            data-currency="sar"
            data-email="user@example.com">
        </script>
    </form>
</body>
</html>