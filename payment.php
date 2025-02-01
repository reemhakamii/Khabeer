<?php
session_start();
include('db.php'); // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Retrieve booking details from the URL
$consultant_id = $_GET['consultant_id'] ?? null;
$client_id = $_GET['client_id'] ?? null;
$booking_date = $_GET['booking_date'] ?? null;

// Just set a fixed amount for the payment or fetch it if necessary
$amount = 1000;  // Example amount (can be adjusted based on your system)

if (!$consultant_id || !$client_id || !$booking_date) {
    echo "Error: Missing booking details.";
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
    <form action="payment.php?consultant_id=<?php echo htmlspecialchars($consultant_id); ?>&client_id=<?php echo htmlspecialchars($client_id); ?>&booking_date=<?php echo htmlspecialchars($booking_date); ?>" method="POST">
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