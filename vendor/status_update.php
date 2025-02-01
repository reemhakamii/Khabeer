<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id']);
    $new_status = $_POST['status'];

    $query = "UPDATE bookings SET status = $1 WHERE id = $2";
    pg_query_params($con, $query, [$new_status, $booking_id]);

    echo json_encode(['message' => 'Status updated successfully']);
}
?>