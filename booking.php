<?php
session_start(); // Start the session to access $_SESSION variables
include('db.php'); // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form values
    $consultant_id = $_POST['consultant_id'] ?? null;
    $booking_date = $_POST['booking_date'] ?? null;
    $client_id = $_SESSION['user_id'] ?? null;

    // Validate form input
    if (empty($consultant_id) || empty($booking_date) || empty($client_id)) {
        $error_message = "Error: All fields are required.";
        echo $error_message; // Debugging message
    } else {
        // Prepare and execute the SQL query using pg_query_params to prevent SQL injection
        $query = "INSERT INTO bookings (consultant_id, client_id, booking_date) VALUES ($1, $2, $3)";
        $result = pg_query_params($con, $query, [$consultant_id, $client_id, $booking_date]);

        // Check if the booking was successful
        if ($result) {
            // Redirect to payment page with the necessary booking details
            header("Location: payment.php?consultant_id=$consultant_id&client_id=$client_id&booking_date=$booking_date");
            exit();  // Ensure no further code is executed
        } else {
            // Error handling if database insertion fails
            $error_message = "Error: " . pg_last_error($con); 
            echo $error_message; // Debugging message
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Consultant</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .booking-container {
            background: rgba(24, 48, 65, 0.9);
            border-radius: 15px;
            padding: 3rem;
            width: 100%;
            max-width: 600px;
            box-shadow: 0px 4px 40px rgba(0, 0, 0, 0.3);
            text-align: center;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #04d07e;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        label {
            font-size: 1.2rem;
            color: #d6fcba;
            text-align: left;
        }

        input, select {
            padding: 0.75rem;
            font-size: 1rem;
            border: 2px solid #4f9a8a;
            border-radius: 8px;
            background: #183041;
            color: #EEEEEE;
            transition: border-color 0.3s;
        }

        input:focus, select:focus {
            border-color: #04d07e;
            outline: none;
        }

        button {
            padding: 1rem;
            background: #04d07e;
            color: #183041;
            font-size: 1.1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #035a66;
        }

        /* Error Message */
        .error-message {
            color: #ff4d4d;
            font-size: 1rem;
            margin-top: 1rem;
        }

        /* Loading Spinner */
        .loading {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #04d07e;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 2rem auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Mobile Responsiveness */
        @media (max-width: 600px) {
            .booking-container {
                padding: 2rem;
            }

            h2 {
                font-size: 2rem;
            }

            label, button {
                font-size: 1rem;
            }
        }

        /* Stunning Date-Time Picker Styling */
        input[type="datetime-local"] {
            background-color: #183041; /* Dark background */
            color: #EEEEEE; /* Light text */
            border: 2px solid #4f9a8a; /* Subtle border */
            padding: 12px;
            font-size: 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            width: 100%; /* Full width */
        }

        input[type="datetime-local"]:focus {
            outline: none;
            border-color: #04d07e; /* Highlight border on focus */
        }

        input[type="datetime-local"]::-webkit-calendar-picker-indicator {
            background-color: #4f9a8a; /* Customize the calendar icon */
            border-radius: 50%;
            padding: 4px;
        }

        /* Customize the calendar dropdown */
        input[type="datetime-local"]::-webkit-datetime-edit {
            color: #EEEEEE; /* Text color */
            background-color: #183041; /* Background for date input */
        }

        /* Placeholder styling */
        input[type="datetime-local"]::placeholder {
            color: #AAAAAA;
        }
    </style>
</head>
<body>
    <div class="booking-container">
        <h2>Book a Consultation</h2>
        
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

<form method="post" action="booking.php">
    <!-- Booking Date -->
    <label for="booking_date">Choose a date and time:</label>
    <input type="datetime-local" id="booking_date" name="booking_date" required>
    <br>

    <!-- Select Consultant -->
    <label for="consultant_id">Choose a Consultant:</label>
    <select id="consultant_id" name="consultant_id" required>
        <option value="">Select Consultant</option>
        <?php
        // Fetch consultants with their full name from the database
        $query = "SELECT c.id, u.name , u.specialization 
                  FROM consultants c 
                  JOIN users u ON c.user_id = u.id";
        $result = pg_query($con, $query);

        // Check if any consultants exist
        if (pg_num_rows($result) > 0) {
            // Display the consultant's name in the dropdown
            while ($consultant = pg_fetch_assoc($result)) {
                echo "<option value='" . $consultant['id'] . "'>" 
                . htmlspecialchars($consultant['name']) . " - " 
                . htmlspecialchars($consultant['specialization']) . "</option>";
            }
        } else {
            echo "<option disabled>No consultants available</option>";
        }
        ?>
    </select>
    <br>

    <!-- Submit Button -->
    <button type="submit">Book Consultation</button>
</form>

        <!-- Loading Spinner (Visible while the form is being processed) -->
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="loading"></div>
        <?php endif; ?>
    </div>
</body>
</html>