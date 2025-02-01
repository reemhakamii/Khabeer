<?php
session_start();
include('db.php'); // Database connection

// Check if the user is logged in
$client_id = $_SESSION['user_id'] ?? null;

if (!$client_id) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Fetch upcoming bookings for the client
$query_upcoming = "SELECT b.id AS booking_id, b.booking_date, b.status, u.name AS consultant_name, u.specialization
                   FROM bookings b
                   JOIN consultants c ON b.consultant_id = c.id
                   JOIN users u ON c.user_id = u.id
                   WHERE b.client_id = $1 AND b.booking_date >= NOW()
                   ORDER BY b.booking_date ASC";
$result_upcoming = pg_query_params($con, $query_upcoming, [$client_id]);

// Fetch past bookings for the client
$query_past = "SELECT b.id AS booking_id, b.booking_date, b.status, u.name AS consultant_name, u.specialization
               FROM bookings b
               JOIN consultants c ON b.consultant_id = c.id
               JOIN users u ON c.user_id = u.id
               WHERE b.client_id = $1 AND b.booking_date < NOW()
               ORDER BY b.booking_date DESC";
$result_past = pg_query_params($con, $query_past, [$client_id]);

// Fetch all reviews submitted by this user
$query_reviews = "SELECT booking_id FROM reviews WHERE client_id = $1";
$result_reviews = pg_query_params($con, $query_reviews, [$client_id]);

$reviewed_bookings = [];
while ($review = pg_fetch_assoc($result_reviews)) {
    $reviewed_bookings[] = $review['booking_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
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
            <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="profile/index.php"><i class="fas fa-user"></i> Profile</a></li> 
                <li><a href="booking.php"><i class="fas fa-calendar-alt"></i> Booking</a></li>
                <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
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
                                <p><strong>Consultant:</strong> 
                                    <span class="consultant-name-box"><?php echo htmlspecialchars($upcoming['consultant_name']); ?></span>
                                </p>
                                <p><strong>Specialization:</strong> <?php echo htmlspecialchars($upcoming['specialization']); ?></p>
                                <p><strong>Date & Time:</strong> <?php echo date('F j, Y, g:i A', strtotime($upcoming['booking_date'])); ?></p>
                                <p><strong>Status:</strong> <?php echo htmlspecialchars($upcoming['status']); ?></p>
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
                                <p><strong>Consultant:</strong> 
                                    <span class="consultant-name-box"><?php echo htmlspecialchars($past['consultant_name'] ?? 'N/A'); ?></span>
                                </p>
                                <p><strong>Specialization:</strong> <?php echo htmlspecialchars($past['specialization'] ?? 'N/A'); ?></p>
                                <p><strong>Date & Time:</strong> <?php echo date('F j, Y, g:i A', strtotime($past['booking_date'])); ?></p>
                                <p><strong>Status:</strong> <?php echo htmlspecialchars($past['status']); ?></p>

                                <?php if ($past['status'] === 'completed'): ?>
                                    <?php if (in_array($past['booking_id'], $reviewed_bookings)): ?>
                                        <p class="thank-you-message" style="color: green;">Thank you! You have already submitted a review for this booking.</p>
                                    <?php else: ?>
                                        <!-- Review Form for Completed Booking -->
                                        <form action="submit_review.php" method="POST">
                                            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($past['booking_id']); ?>">
                                            <textarea name="review" placeholder="Write your review here" required></textarea>
                                            <button type="submit">Submit Review</button>
                                        </form>
                                    <?php endif; ?>
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

    <script>
        // Search Functionality for Consultants
        document.getElementById('search')?.addEventListener('input', function() {
            let searchTerm = this.value.toLowerCase();
            let bookings = document.querySelectorAll('.booking');

            bookings.forEach(function(booking) {
                let consultantName = booking.querySelector('.consultant-name-box').textContent.toLowerCase();
                booking.style.display = consultantName.includes(searchTerm) ? 'block' : 'none';
            });
        });
    </script>

</body>
</html>