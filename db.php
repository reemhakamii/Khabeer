<?php
// Database connection details
$host = "localhost";
$port = "8000";
$dbname = "php";
$user = "postgres";
$password = "";

// Create connection
$con = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Check connection
if (!$con) {
    die("Connection failed: " . pg_last_error());
}
?>