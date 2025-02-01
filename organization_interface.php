<?php
session_start();
include('db.php'); // Database connection

// Check if the user is logged in
$client_id = $_SESSION['user_id'] ?? null;

if (!$client_id) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Fetch upcoming and past bookings for client (organization user)
$query_upcoming = "SELECT b.booking_date, u.name AS consultant_name, u.specialization, b.status, b.id AS booking_id
                   FROM bookings b
                   JOIN consultants c ON b.consultant_id = c.id
                   JOIN users u ON c.user_id = u.id
                   WHERE b.client_id = $1 AND b.booking_date >= NOW()
                   ORDER BY b.booking_date ASC";
$result_upcoming = pg_query_params($con, $query_upcoming, [$client_id]);

$query_past = "SELECT b.booking_date, u.name AS consultant_name, u.specialization, b.status, b.id AS booking_id
               FROM bookings b
               JOIN consultants c ON b.consultant_id = c.id
               JOIN users u ON c.user_id = u.id
               WHERE b.client_id = $1 AND b.booking_date < NOW()
               ORDER BY b.booking_date DESC";
$result_past = pg_query_params($con, $query_past, [$client_id]);

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $booking_id = $_POST['booking_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Insert review into the database
    $insert_review_query = "INSERT INTO reviews (booking_id, rating, comment) VALUES ($1, $2, $3)";
    pg_query_params($con, $insert_review_query, [$booking_id, $rating, $comment]);

    echo "<p>Review submitted successfully!</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Dashboard</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <script src="assets/js/dashboard.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Dashboard</h2>
            <ul>
                <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="profile/index.php"><i class="fas fa-user"></i> Profile</a></li>
                <li><a href="booking.php"><i class="fas fa-calendar-alt"></i> Booking</a></li>
                <li><a href="reviews.php"><i class="fas fa-calendar-alt"></i> Reviews</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <h2>Welcome to Your Dashboard</h2>

            <!-- Upcoming Bookings Section -->
            <div class="section">
                <div class="section-title">Upcoming Bookings</div>
                <?php if (pg_num_rows($result_upcoming) > 0): ?>
                    <div id="upcoming-bookings">
                        <?php while ($upcoming = pg_fetch_assoc($result_upcoming)): ?>
                            <div class="booking">
                                <p><strong>Consultant:</strong> <?php echo htmlspecialchars($upcoming['consultant_name']); ?></p>
                                <p><strong>Specialization:</strong> <?php echo htmlspecialchars($upcoming['specialization']); ?></p>
                                <p><strong>Date & Time:</strong> <?php echo date('F j, Y, g:i A', strtotime($upcoming['booking_date'])); ?></p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="no-data">You have no upcoming bookings.</p>
                <?php endif; ?>
            </div>

            <!-- Past Bookings Section -->
            <div class="section">
                <div class="section-title">Past Bookings</div>
                <?php if (pg_num_rows($result_past) > 0): ?>
                    <div id="past-bookings">
                        <?php while ($past = pg_fetch_assoc($result_past)): ?>
                            <div class="booking">
                                <p><strong>Consultant:</strong> <?php echo htmlspecialchars($past['consultant_name']); ?></p>
                                <p><strong>Specialization:</strong> <?php echo htmlspecialchars($past['specialization']); ?></p>
                                <p><strong>Date & Time:</strong> <?php echo date('F j, Y, g:i A', strtotime($past['booking_date'])); ?></p>
                                
                                <!-- Show review section if booking is completed -->
                                <?php if ($past['status'] == 'completed'): ?>
                                    <form action="" method="POST">
                                        <input type="hidden" name="booking_id" value="<?php echo $past['booking_id']; ?>">
                                        <label for="rating">Rating (1-5):</label>
                                        <input type="number" name="rating" min="1" max="5" required>
                                        <label for="comment">Comment:</label>
                                        <textarea name="comment" required></textarea>
                                        <button type="submit" name="submit_review">Submit Review</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="no-data">You have no past bookings.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html> 