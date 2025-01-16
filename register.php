<?php
// Database connection credentials
$host = "localhost"; // Change if hosted elsewhere
$db_user = "root"; // Your database username
$db_password = ""; // Your database password
$db_name = "expense_tracker"; // Name of your database

// Establish a database connection
$conn = mysqli_connect($host, $db_user, $db_password, $db_name);

// Check the connection
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Check if the form data is set
if (!isset($_POST['username'], $_POST['email'], $_POST['password'])) {
    exit("Please fill out the form");
}

// Check if email and password are not empty
if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
    exit('Please complete the registration form!');
}

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password']; // (Consider hashing this password in production)

// Check if the email already exists
if ($stmt = $conn->prepare('SELECT username FROM users WHERE username = ?')) {
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo 'This username is already registered!';
        exit();
    }
    $stmt->close();
}

// Insert the new user into the database
if ($stmt = $conn->prepare('INSERT INTO users (password, email, username) VALUES (?, ?, ?)')) {
    $stmt->bind_param('sss', $password, $email, $username);
    $stmt->execute();

    // Redirect to the signin page after successful registration
    header('Location: budget.html');
    exit();
} else {
    echo 'Failed to prepare statement!';
}

$conn->close();
?>
