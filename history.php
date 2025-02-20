<?php
//session start
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



// Query to fetch and sum the monthly expenses
$stmt = $con->prepare('SELECT COALESCE(SUM(amount), 0) AS total FROM expenses WHERE user_id = ?');
if ($stmt) {
    $stmt->bind_param('i', $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($total);
    $stmt->fetch();
    $stmt->close();
} else {
    echo 'Failed to fetch the expenses.';
}

// Query to fetch the budget amount and currency from the database
$stmt = $con->prepare('SELECT currency FROM budgets WHERE user_id = ?');
if ($stmt) {
    $stmt->bind_param('i', $_SESSION['id']);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user has a budget set
    if ($stmt->num_rows > 0) {
        $stmt->bind_result( $response['currency']);
        $stmt->fetch();
    } else {
        // Handle case where no budget exists for the user
        $response['currency'] = 'R';
    }

    $stmt->close();
} else {
    echo 'Failed to fetch the buget and currency.';
}


// Query to fetch the day with the highest spending and the sum of expenses
$stmt = $con->prepare('
    SELECT DATE(expense_date) AS day, SUM(amount) AS total_spent
    FROM expenses
    WHERE user_id = ?
    GROUP BY DATE(expense_date)
    ORDER BY total_spent DESC
    LIMIT 1
');

if ($stmt) {
    $stmt->bind_param('i', $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($day, $total_spent);
    if ($stmt->fetch()) {
        $response['day'] = $day;
        $response['total'] = number_format($total_spent, 2);
    } else {
        $response['day'] = 'No data';
        $response['total'] = '0.00';
    }
    $stmt->close();
} else {
    $response['day'] = 'Error';
    $response['total'] = '0.00';
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dashboard.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>History</title>
</head>
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
                    <a href="dashboard.php">
                        <i class="bx bx-grid-alt "></i>
                        <span class="nav-item">Dashboard</span>
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
                    <a href="history.php">
                        <i class="bx bx-history "></i>
                        <span class="nav-item">History</span>
                    </a>
                    
                </li>
                <li>
                    <a href="#">
                        <i class="bx bx-cog "></i>
                        <span class="nav-item">Settings</span>
                    </a>
                    
                </li>
                <li onclick="openlogoutPopup()">
                    <a href="#">
                        <i class="bx bx-log-out "></i>
                        <span class="nav-item">Logout</span>
                    </a>
                   
                </li>
            </ul>
        </div>

        <div class="main">
            <div class="topbar">
                    <div class="header">
                        <h3>History</h3>
                        <p>Track your expenses and budget with ease</p>
                    </div>
                    <div class="date" id="dateDisplay"></div>
            </div>
            <div class="main-con">
                    <div class="budgets">
                    <div class="history">
                        <h3>Total expenses</h3>
                        <p><?php echo $response['currency'] . ' ' . number_format($total, 2); ?></p>
                    </div>
                    
                    
            </div>

        <div class="expenses">
           <div class="ex-head">
            <p class="text-des-1">All times expenses</p>
            <div class="list-ex" id="expenses-container">
                <!-- Expenses will be displayed here -->
            </div>
            </div>
        </div>

       

        </div>

        <div class="main-con-2">
            <div class="savings">
                <div class="saving">
                    <p class="text-des" >Highest spending day</p>
                    <p class="num" style="font-size: 12px"><?php echo  $response['day']; ?></p>
                </div>
                <div class="saving">
                    <p class="text-des" >Highet amount</p>
                    <p class="num" style="font-size: 12px"><?php echo $response['currency'] . ' ' . $response['total']; ?></p>
                </div>

                
            </div>

            <div class="gr">
                <canvas class="chart" id="expensesPieChart" width="100" height="100"></canvas>
            </div>

            
        </div>

       

    </div>

    <div class="logoutform">
                <div class="logout-content">
                    
                    <span class="close-btn" onclick="closelogoutPopup()"><i class="bx bx-x"></i></span>
                    <h2>Logout</h2>
                    <p>Are you sure you want to logout?</p>
                    
                        <button type="button" class="btn-get-started" onclick="window.location.href='logout.php';">Logout</button>
                </div>
              
            </div>
            <div class="backdrop"></div>
    </div>


    
</body>
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

function openlogoutPopup() {
    const popup = document.querySelector('.logoutform');
    const backdrop = document.querySelector('.backdrop');
    const mainContent = document.querySelector('.main');

    popup.classList.add('show');
    backdrop.classList.add('show');
    mainContent.classList.add('blur');
}

function closelogoutPopup() {
    const popup = document.querySelector('.logoutform');
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

// Fetch all expenses from the server

async function fetchExpenses() {
  try {
    const response = await fetch("fetchAll.php");
    const data = await response.json();

    if (data.success) {
      displayExpenses(data.expenses);
      createPieChart(data.expenses);
    } else {
      console.error(data.message);
      alert(data.message);
    }
  } catch (error) {
    console.error("Error fetching expenses:", error);
    alert("An error occurred while fetching expenses. Please try again.");
  }
}

function displayExpenses(expenses) {
  const container = document.getElementById("expenses-container");
  container.innerHTML = ""; // Clear the container

  expenses.forEach((expense) => {
    const expenseElement = document.createElement("div");
    expenseElement.className = "expense-item";
    expenseElement.innerHTML = `
      <p>${expense.expense}</p>
      <p>${expense.amount}</p>
    `;
    container.appendChild(expenseElement);
  });
}

function createPieChart(expenses) {
  const ctx = document.getElementById('expensesPieChart').getContext('2d');

  const labels = expenses.map(exp => exp.expense);
  const amounts = expenses.map(exp => exp.amount);

  new Chart(ctx, {
    type: 'pie',
    data: {
      labels: labels,
      datasets: [{
        label: 'Expenses Breakdown',
        data: amounts,
        backgroundColor: [
          'rgba(255, 99, 132, 0.6)',
          'rgba(54, 162, 235, 0.6)',
          'rgba(255, 206, 86, 0.6)',
          'rgba(75, 192, 192, 0.6)',
          'rgba(153, 102, 255, 0.6)',
          'rgba(255, 159, 64, 0.6)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
        },
        title: {
          display: true,
          text: 'Expenses Distribution'
        }
      }
    }
  });
}

// Call the function to fetch expenses and render the chart
fetchExpenses();

</script>
</html>