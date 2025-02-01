<?php
session_start();
include('db.php'); // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure the review and booking_id are set
    $booking_id = $_POST['booking_id'] ?? null;
    $review = $_POST['review'] ?? '';

    if (!$booking_id) {
        $_SESSION['review_message'] = "Booking ID is required.";
        header('Location: individual_interface.php');
        exit();
    }

    if (empty($review)) {
        $_SESSION['review_message'] = "Review cannot be empty.";
        header('Location: individual_interface.php');
        exit();
    }

    // Check if the user has already reviewed this booking
    $check_query = "SELECT COUNT(*) FROM reviews WHERE booking_id = $1 AND client_id = $2";
    $check_result = pg_query_params($con, $check_query, [$booking_id, $_SESSION['user_id']]);
    $check_row = pg_fetch_assoc($check_result);

    if ($check_row['count'] > 0) {
        $_SESSION['review_message'] = "You have already submitted a review for this booking.";
        header('Location: individual_interface.php');
        exit();
    }

    // Insert the review into the database
    $query = "INSERT INTO reviews (booking_id, comment, client_id) VALUES ($1, $2, $3)";
    $result = pg_query_params($con, $query, [$booking_id, $review, $_SESSION['user_id']]);

    if ($result) {
        $_SESSION['review_message'] = "Thank you for your review!";
    } else {
        $_SESSION['review_message'] = "Error submitting review.";
    }

    header('Location: individual_interface.php');
    exit();
}
?>