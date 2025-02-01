<?php
session_start();
include('db.php');

// Get the payment details
$consultant_id = $_POST['consultant_id'] ?? null;
$client_id = $_POST['client_id'] ?? null;
$booking_date = $_POST['booking_date'] ?? null;
$amount = $_POST['amount'] ?? null;

if (!$consultant_id || !$client_id || !$booking_date || !$amount) {
    echo "Error: Missing payment details.";
    exit();
}

// Here you would integrate the payment gateway (e.g., Stripe, PayPal) to process the payment

// Assuming the payment is successful:
$query = "UPDATE bookings SET status = 'paid' WHERE consultant_id = $1 AND client_id = $2 AND booking_date = $3";
$result = pg_query_params($con, $query, [$consultant_id, $client_id, $booking_date]);

if ($result) {
    echo "Payment successful! Your booking has been confirmed.";
} else {
    echo "Error: Payment processing failed.";
}
?>