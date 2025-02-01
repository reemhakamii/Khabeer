<?php
session_start();
require_once 'db.php'; // Database connection

// Check if the user is logged in
$client_id = $_SESSION['user_id'] ?? null;
if (!$client_id) {
    header('Location: login.php');
    exit();
}

// Fetch all reviews for the logged-in user
$query_reviews = "
    SELECT r.id AS review_id, r.rating, r.comment, r.created_at, 
           b.booking_date, b.status, 
           u.name AS consultant_name, u.specialization
    FROM reviews r
    JOIN bookings b ON r.booking_id = b.id
    JOIN consultants c ON b.consultant_id = c.id
    JOIN users u ON c.user_id = u.id
    WHERE b.client_id = $1
    ORDER BY r.created_at DESC"; // Most recent reviews first

$result_reviews = pg_query_params($con, $query_reviews, [$client_id]);
if (!$result_reviews) {
    echo "Error fetching reviews: " . pg_last_error($con);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reviews</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>

<div class="dashboard">
    <aside class="sidebar">
        <h2>Dashboard</h2>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="profile/index.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="booking.php"><i class="fas fa-calendar-alt"></i> Booking</a></li>
            <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <h2>My Reviews</h2>

        <?php if (pg_num_rows($result_reviews) > 0): ?>
            <div class="reviews-list">
                <?php while ($review = pg_fetch_assoc($result_reviews)): ?>
                    <div class="review-box">
                        <p><strong>Consultant:</strong> <?php echo htmlspecialchars($review['consultant_name']); ?></p>
                        <p><strong>Specialization:</strong> <?php echo htmlspecialchars($review['specialization'] ?? 'N/A'); ?></p>                        <p><strong>Booking Date:</strong> <?php echo date('F j, Y, g:i A', strtotime($review['booking_date'])); ?></p>
                        <p><strong>Rating:</strong> <?php echo $review['rating']; ?> ⭐</p>
                        <p><strong>Comment:</strong> <?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                        <p class="review-date">Reviewed on: <?php echo date('F j, Y, g:i A', strtotime($review['created_at'])); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="no-reviews">You have not submitted any reviews yet.</p>
        <?php endif; ?>
    </main>
</div>

<style>
    .review-box {
        background: #f9f9f9;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        border: 1px solid #ccc;
    }
    .review-date {
        color: #888;
        font-size: 14px;
    }
    .no-reviews {
        color: red;
        font-size: 18px;
    }
</style>

</body>
</html>