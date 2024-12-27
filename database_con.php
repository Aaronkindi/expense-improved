<?php
// Database configuration
$host = "localhost";  // Database server (e.g., localhost or an IP address)
$db_name = "expense_tracker";  // Name of your database
$username = "root";  // Database username
$password = "";  // Database password (leave empty for local setups like XAMPP)

// Create a connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
