<?php
// Session handling
session_start();

// Database connection
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'expense_tracker';

// Connect to the database
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

if (isset($_POST['Amount'], $_POST['expenses'], $_SESSION['id'])) {
    $amount = $_POST['Amount'];
    $expenses = $_POST['expenses'];
    $user_id = $_SESSION['id'];

    // Validate input
    if (!is_numeric($amount) || $amount <= 0) {
        $_SESSION['error'] = 'Invalid amount. Please enter a positive number.';
        header('Location: dashboard.php');
        exit();
    }

    // Check if the user has a budget set
    if ($stmt = $con->prepare('SELECT budget_amount FROM budgets WHERE user_id = ?')) {
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($budget_amount);
            $stmt->fetch();

            // Check if the budget is empty or zero
            if (empty($budget_amount) || $budget_amount <= 0) {
                $_SESSION['error'] = 'You have not set a budget. Please set a budget before adding expenses.';
                header('Location: dashboard.php');
                exit();
            }
        } else {
            $_SESSION['error'] = 'You have not set a budget. Please set a budget before adding expenses.';
            header('Location: dashboard.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'Failed to check budget. Please try again.';
        header('Location: dashboard.php');
        exit();
    }

    // Insert the new expense
    if ($stmt = $con->prepare('INSERT INTO expenses (user_id, amount, expense, expense_date) VALUES (?, ?, ?, NOW())')) {
        $stmt->bind_param('ids', $user_id, $amount, $expenses);
        $stmt->execute();

        // Set success message in session
        $_SESSION['success'] = 'Expense added successfully!';
        header('Location: dashboard.php');
        exit();
    } else {
        $_SESSION['error'] = 'Failed to save expense!';
        header('Location: dashboard.php');
        exit();
    }
} else {
    $_SESSION['error'] = 'Please fill in all required fields!';
    header('Location: dashboard.php');
    exit();
}
?>