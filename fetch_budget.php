<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

// Database connection
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'expense_tracker';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to connect to MySQL: ' . mysqli_connect_error()]);
    exit;
}

// Query to fetch total expenses for the current month
$stmt = $con->prepare('SELECT amount, expense FROM expenses WHERE user_id = ? AND  MONTH(expense_date) = MONTH(CURDATE())');
if ($stmt) {
    $stmt->bind_param('i', $_SESSION['id']);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user has expenses for the current day
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($amount, $expense);
        $expenses = [];
        while ($stmt->fetch()) {
            $expenses[] = [
                'amount' => $amount,
                'expense' => $expense,
            ];
        }

        // Return the expenses as a JSON response
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'expenses' => $expenses]);
    } else {
        // No expenses found
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No expenses found for today.']);
    }

    $stmt->close();
} else {
    // Database error
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to prepare the SQL statement.']);
}


// Close the database connection
mysqli_close($con);
?>