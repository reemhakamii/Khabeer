<?php
session_start();
include('db.php'); // Include database connection

// Ensure consultant is logged in
$consultant_id = $_SESSION['user_id'] ?? null;
if (!$consultant_id) {
    header("Location: login.php");
    exit();
}

// Fetch consultant ID from the database
$query_consultant_id = "SELECT id FROM consultants WHERE user_id = $1";
$result_consultant_id = pg_query_params($con, $query_consultant_id, [$consultant_id]);

if (!$result_consultant_id) {
    die('Error fetching consultant ID: ' . pg_last_error($con));
}

$consultant_data = pg_fetch_assoc($result_consultant_id);
$consultant_id_real = $consultant_data['id'] ?? null;

if (!$consultant_id_real) {
    die('Consultant profile not found.');
}

// Handle status update request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['booking_id'], $_POST['status'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['status'];

    // Validate status
    $allowed_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
    if (!in_array($new_status, $allowed_statuses)) {
        $_SESSION['error'] = "Invalid status selected.";
        header("Location: consultant_interface.php");
        exit();
    }

    // Update status
    $update_status_query = "UPDATE bookings SET status = $1 WHERE id = $2 AND consultant_id = $3";
    $update_result = pg_query_params($con, $update_status_query, [$new_status, $booking_id, $consultant_id_real]);

    if ($update_result) {
        $_SESSION['message'] = "Booking status updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update booking status: " . pg_last_error($con);
    }

    header("Location: consultant_interface.php");
    exit();
}

// Fetch past consultations
$query_past_consultations = "
    SELECT 
        b.id AS booking_id, 
        b.booking_date, 
        u.name AS client_name, 
        b.status, 
        u.user_type AS client_type 
    FROM bookings b 
    JOIN users u ON b.client_id = u.id 
    WHERE b.consultant_id = $1 
    ORDER BY b.booking_date DESC";

$result_past_consultations = pg_query_params($con, $query_past_consultations, [$consultant_id_real]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultant Dashboard</title>
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
                <li><a href="#"><i class="fas fa-calendar-alt"></i> Consultations</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <h2>Welcome to Your Consultant Dashboard</h2>

            <!-- Display Messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <p class="message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
            <?php endif; ?>

            <!-- Past Consultations Section -->
            <div class="section">
                <div class="section-title">Past Consultations</div>
                <?php if (pg_num_rows($result_past_consultations) > 0): ?>
                    <?php while ($row = pg_fetch_assoc($result_past_consultations)): ?>
                        <div class="booking">
                            <p><strong>Client:</strong> <?php echo htmlspecialchars($row['client_name']); ?></p>
                            <p><strong>Type:</strong> <?php echo htmlspecialchars($row['client_type']); ?></p>
                            <p><strong>Date & Time:</strong> <?php echo date("F j, Y, g:i A", strtotime($row['booking_date'])); ?></p>

                            <!-- Status Update Form -->
                            <form method="POST" action="">
                                <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($row['booking_id']); ?>">
                                <label for="status"><strong>Status:</strong></label>
                                <select name="status">
                                    <?php
                                    $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
                                    foreach ($statuses as $status) {
                                        $selected = ($row['status'] == $status) ? 'selected' : '';
                                        echo "<option value='$status' $selected>$status</option>";
                                    }
                                    ?>
                                </select>
                                <button type="submit">Update</button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-data">No past consultations found.</p>
                <?php endif; ?>
            </div>

        </main>
    </div>

</body>
</html>