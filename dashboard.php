<?php
session_start();
include('db.php'); // Database connection

// Check if the user is logged in
$client_id = $_SESSION['user_id'] ?? null;

if (!$client_id) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Fetch upcoming bookings
$query_upcoming = "
    SELECT b.booking_date, u.name AS consultant_name, u.specialization
    FROM bookings b
    JOIN consultants c ON b.consultant_id = c.id
    JOIN users u ON c.user_id = u.id
    WHERE b.client_id = $1 AND b.booking_date >= NOW()
    ORDER BY b.booking_date ASC";

// Execute query for upcoming bookings
$result_upcoming = pg_query_params($con, $query_upcoming, [$client_id]);

// Check for errors in the query execution
if (!$result_upcoming) {
    die("Error executing query: " . pg_last_error($con));
}

// Fetch past bookings
$query_past = "
    SELECT b.booking_date, u.name AS consultant_name, u.specialization
    FROM bookings b
    JOIN consultants c ON b.consultant_id = c.id
    JOIN users u ON c.user_id = u.id
    WHERE b.client_id = $1 AND b.booking_date < NOW()
    ORDER BY b.booking_date DESC";

// Execute query for past bookings
$result_past = pg_query_params($con, $query_past, [$client_id]);

// Check for errors in the query execution for past bookings
if (!$result_past) {
    die("Error executing past bookings query: " . pg_last_error($con));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1d3c52, #4f9a8a);
            color: #EEEEEE;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 2rem 1rem;
        }

        .dashboard-container {
            background: rgba(24, 48, 65, 0.9);
            border-radius: 15px;
            padding: 2rem;
            width: 100%;
            max-width: 800px;
            box-shadow: 0px 4px 40px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #04d07e;
        }

        .section {
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #d6fcba;
        }

        .booking {
            background: #183041;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            color: #EEEEEE;
        }

        .booking p {
            margin: 0.2rem 0;
        }

        .no-data {
            color: #AAAAAA;
            font-size: 1rem;
            margin: 0.5rem 0;
        }

        .button {
            display: inline-block;
            padding: 1rem 2rem;
            background: #04d07e;
            color: #183041;
            font-size: 1.1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
            text-decoration: none;
            text-align: center;
        }

        .button:hover {
            background: #035a66;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Welcome to Your Dashboard</h2>

        <!-- Upcoming Bookings Section -->
        <div class="section">
            <div class="section-title">Upcoming Bookings</div>
            <?php if (pg_num_rows($result_upcoming) > 0): ?>
                <?php while ($upcoming = pg_fetch_assoc($result_upcoming)): ?>
                    <div class="booking">
                        <p><strong>Consultant:</strong> <?php echo htmlspecialchars($upcoming['consultant_name']); ?></p>
                        <p><strong>Specialization:</strong> <?php echo htmlspecialchars($upcoming['specialization']); ?></p>
                        <p><strong>Date & Time:</strong> <?php echo date('F j, Y, g:i A', strtotime($upcoming['booking_date'])); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-data">You have no upcoming bookings.</p>
            <?php endif; ?>
        </div>

        <!-- Past Bookings Section -->
        <div class="section">
            <div class="section-title">Past Bookings</div>
            <?php if (pg_num_rows($result_past) > 0): ?>
                <?php while ($past = pg_fetch_assoc($result_past)): ?>
                    <div class="booking">
                        <p><strong>Consultant:</strong> <?php echo htmlspecialchars($past['consultant_name']); ?></p>
                        <p><strong>Specialization:</strong> <?php echo htmlspecialchars($past['specialization']); ?></p>
                        <p><strong>Date & Time:</strong> <?php echo date('F j, Y, g:i A', strtotime($past['booking_date'])); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-data">You have no past bookings.</p>
            <?php endif; ?>
        </div>

        <!-- Book New Consultation -->
        <a href="booking.php" class="button">Book a New Consultation</a>
    </div>
</body>
</html>