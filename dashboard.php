<?php
// Start session
session_start();

// Database connection
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'expense_tracker';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Initialize variables
$response = [
    'amount' => 0.0,
    'currency' => 'R',
];

// Check if the user ID is in the session (check if logged in)
if (!isset($_SESSION['id'])) {
    header('Location: signin.php'); // Redirect to the sign-in page if not logged in
    exit;
}

// Query to fetch the budget amount and currency from the database
$stmt = $con->prepare('SELECT budget_amount, currency FROM budgets WHERE user_id = ?');
if ($stmt) {
    $stmt->bind_param('i', $_SESSION['id']);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user has a budget set
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($response['amount'], $response['currency']);
        $stmt->fetch();
    } else {
        // Handle case where no budget exists for the user
        $response['amount'] = 0.0;
        $response['currency'] = 'R';
    }

    $stmt->close();
} else {
    echo 'Failed to fetch the buget and currency.';
}
//querry to fetch and add the expenses of the current day
$stmt = $con->prepare('SELECT COALESCE(SUM(amount), 0) AS total FROM expenses WHERE user_id = ? AND DATE(expense_date) = CURDATE()');
if ($stmt) {
    $stmt->bind_param('i', $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($total);
    $stmt->fetch();
    $stmt->close();
} else {
    echo 'Failed to fetch the expenses.';
}

//querry to fetch and add the expenses of the current month
$stmt = $con->prepare('SELECT COALESCE(SUM(amount), 0) AS total FROM expenses WHERE user_id = ? AND MONTH(expense_date) = MONTH(CURDATE())');
if ($stmt) {
    $stmt->bind_param('i', $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($totalmonth);
    $stmt->fetch();
    $stmt->close();
} else {
    echo 'Failed to fetch the expenses.';
}

//querry to fetch the highest expense of the current day
$stmt = $con->prepare('SELECT COALESCE(MAX(amount), 0) AS total FROM expenses WHERE user_id = ? AND DATE(expense_date) = CURDATE()');
if ($stmt) {
    $stmt->bind_param('i', $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($highest);
    $stmt->fetch();
    $stmt->close();
} else {
    echo 'Failed to fetch the expenses.';
}

// Close the database connection
mysqli_close($con);


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<body>
    <?php if (isset($_SESSION['success'])): ?>
        <script>
            alert('<?php echo $_SESSION['success']; ?>');
            <?php unset($_SESSION['success']); ?>
        </script>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <script>
            alert('<?php echo $_SESSION['error']; ?>');
            <?php unset($_SESSION['error']); ?>
        </script>
    <?php endif; ?>

    <!-- Rest of your HTML content -->
    <div class="main-content">
        <div class="sidebar">
            <div class="top">
                <div class="logo">
                    <span><h5>TRACKIT</h5></span>
                </div>
            </div>
            <i class="bx bx-menu" id="btn"></i>
            
    
            <ul>
                <li>
                    <a href="">
                        <i class="bx bx-user-circle"></i>
                        <span class="nav-item">
                        <p class="bold">
                <!-- Display the username if logged in -->
                <?php
                if (isset($_SESSION['username'])) {
                    echo htmlspecialchars($_SESSION['username']);
                } else {
                    echo 'Guest';
                }
                ?>
                        </p>
                        </span>
                    </a>
    
                   
                </li>
                <li>
                    <a href="#">
                        <i class="bx bx-grid-alt "></i>
                        <span class="nav-item">Dash</span>
                    </a>
                   
                </li>
                <li>
                    <a href="budget.php">
                        <i class="bx bx-money "></i>
                        <span class="nav-item">Budget</span>
                    </a>
                   
                </li>

                <li onclick="openPopup()">
                    <a href="#">
                        
                            <i class="bx bx-plus-circle "></i>
                        <span class="nav-item">expense</span>
                    
                    </a>
                   
                </li>
                <li>
                    <a href="#">
                        <i class="bx bx-history "></i>
                        <span class="nav-item">History</span>
                    </a>
                    
                </li>
                <li>
                    <a href="setting.php">
                        <i class="bx bx-cog "></i>
                        <span class="nav-item">Settings</span>
                    </a>
                    
                </li>
                <li>
                    <a href="logout.php">
                        <i class="bx bx-log-out "></i>
                        <span class="nav-item">Logout</span>
                    </a>
                   
                </li>
            </ul>
        </div>
    
    <div class="main">
           <div class="topbar">
                <div class="header">
                    <h3>Dashboard</h3>
                    <p>Track your expenses and budget with ease</p>
                </div>
                <div class="date" id="dateDisplay"></div>
           </div>
            <div class="main-con">
                <div class="budgets">
                <div class="budget">
                    <h3>Monthly budget</h3>
                    <p><?php echo $response['currency'] . ' ' . number_format($response['amount'], 2); ?></p>
                </div>
                <div class="budget-1">
                    <h3>Total expenses today</h3>
                    <p>  <?php echo htmlspecialchars($response['currency'] ?? 'USD') . ' ' . number_format($total, 2); ?></p>
                </div>
                <div class="budget-1">
                    <h3>Total monthly expenses</h3>
                    <p>  <?php echo htmlspecialchars($response['currency'] ?? 'USD') . ' ' . number_format($totalmonth, 2); ?></p>
                </div>
                
           </div>

           <div class="expenses">
           <div class="ex-head">
        <p class="text-des-1">Expenses today</p>
        <div class="list-ex" id="expenses-container">
            <!-- Expenses will be displayed here -->
        </div>
    </div>
           </div>

        </div>
           
        <div class="main-con-2">
        <div class="savings">
            <div class="saving">
            <p class="text-des">Savings</p>
            <p class="num">R2000</p>
            </div>
            <div class="saving">
            <p class="text-des">Highest exp</p>
            <p class="num"><?php echo htmlspecialchars($response['currency'] ?? 'USD') . ' ' . number_format($highest, 1); ?></p>
            </div>
           </div>
           <div class="gr">
             
            </div>
        </div>

           



    
            <div class="popupForm" class="popup">
                <div class="popup-content">
                    
                    <span class="close-btn" onclick="closePopup()"><i class="bx bx-x"></i></span>
                    <h2>Add expense</h2>
                    <form action="expenses.php" method="POST">
                        <select name="expenses" id="expenses">
                            <option value="Rent/Mortgage">Rent/Mortgage</option>
                            <option value="Property Tax">Property Tax</option>
                            <option value="Electricity">Electricity</option>
                            <option value="water">Water</option>
                            <option value="Gas">Gas</option>
                            <option value="Phone/data">Phone/data</option>
                            <option value="wifi">Wifi</option>
                            <option value="Food">Food</option>
                            <option value="Household items">Household items</option>
                            <option value="Fuel">Fuel</option>
                            <option value="Car insurrance">Car insurrance</option>
                            <option value="Car loan repayment">Car loan repayment</option>
                            <option value="Transportation">Transport</option>
                            <option value="Maitenance">Maintenace</option>
                            <option value="Medical aid">Medical aid</option>
                            <option value="Prescription">Prescription</option>
                            <option value="Loans/Debt">Loans</option>
                            <option value="Dining out">Dining Out</option>
                            <option value="Streaming">Streaming</option>
                            <option value="Leisure">Leisure</option>
                            <option value="Miscellenous">other expenses</option>
        
                        </select>
                        <input type="number" placeholder="Amount" class="input-field" id="Amount" name="Amount" required>
                        <button type="submit" class="btn-get-started">Save</button>
                    </form>
                </div>
                <div class="backdrop"></div>
            </div>
           
        </div>
    
    </div>
    
    
</body>
</html>
<script src='expense.js'></script>
<script>
// Toggle sidebar
let btn = document.querySelector('#btn');
let sidebar = document.querySelector('.sidebar');

btn.onclick = function () {
    sidebar.classList.toggle('active');
};

// Open and close pop-up
function openPopup() {
    const popup = document.querySelector('.popupForm');
    const backdrop = document.querySelector('.backdrop');
    const mainContent = document.querySelector('.main');

    popup.classList.add('show');
    backdrop.classList.add('show');
    mainContent.classList.add('blur');
}

function closePopup() {
    const popup = document.querySelector('.popupForm');
    const backdrop = document.querySelector('.backdrop');
    const mainContent = document.querySelector('.main');

    popup.classList.remove('show');
    backdrop.classList.remove('show');
    mainContent.classList.remove('blur');
}

// Display the current date
function updateDate() {
    // Get the current date
    const now = new Date();

    // Format the date as a string (e.g., "Thursday, October 5")
    const options = { month: 'long', day: 'numeric' };
    const dateString = now.toLocaleDateString(undefined, options); // Use `undefined` for default locale

    // Update the div to display the date
    document.getElementById('dateDisplay').textContent = dateString;
}

// Call updateDate immediately to display the date when the page loads
updateDate();

// Update the date every second
setInterval(updateDate, 1000);
</script>
</html>