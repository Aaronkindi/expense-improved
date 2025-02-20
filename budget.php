<?php
session_start(); // Start the session

// Database connection
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'expense_tracker';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Handle budget setup
if (isset($_POST['budget'], $_POST['currencies']) && isset($_SESSION['id'])) {
    $budget = $_POST['budget'];
    $currency = $_POST['currencies'];
    $user_id = $_SESSION['id'];

    // Validate input
    if (!is_numeric($budget) || $budget <= 0) {
        echo 'Invalid budget amount. Please enter a positive number.';
        exit();
    }
    

    // Update the existing budget for the user or insert if no budget exists
    if ($stmt = $con->prepare('UPDATE budgets SET budget_amount = ?, currency = ?, budget_date = CURRENT_TIMESTAMP WHERE user_id = ?')) {
        $stmt->bind_param('dsi', $budget, $currency, $user_id);
        $stmt->execute();

        // Check if a row was updated
        if ($stmt->affected_rows === 0) {
            // If no row was updated, insert a new budget (first-time setup)
            $stmt = $con->prepare('INSERT INTO budgets (user_id, budget_amount, currency, budget_date ) VALUES (?, ?, ?, CURRENT_TIMESTAMP)');
            $stmt->bind_param('ids', $user_id, $budget, $currency);
            $stmt->execute();
        }

        // Redirect to the dashboard after successful operation
        header('Location: dashboard.php');
        exit();
    } else {
        echo 'Failed to prepare statement!';
    }
} 

$con->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Budget</title>
    <link rel="stylesheet" href="style3.css">
</head>
<body>
    <div class="login-container">
        <div class="form-container">
            <div class="image-content">
                <img src="images/4860244.jpg" alt="Image" class="login-image">
            </div>
            <div class="form-content">
                <h2>Welcome to Trackit</h2>
                <p class="welcome-text">Please set your monthly budget</p>

                <form action="" method="POST"> <!-- Action points to the same file -->
                <label for="currency">Choose your currency</label>
                    <select name="currencies" id="currencies" class="options" required>
                        <option value="R">R</option>
                        <option Value="FC">FC</option>
                        <option value="$">$</option>
                    </select>
                    <input type="number" placeholder="budget" class="input-field" id="budget" name="budget" required>
                    <button type="submit" class="btn-get-started">Continue</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
