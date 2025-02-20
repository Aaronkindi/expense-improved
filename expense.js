// Function to fetch all expenses using AJAX and display them in a div

let expenseArray = [];
let budgetArray = [];

// Add this delete function to your JavaScript
async function deleteExpense(id) {
  try {
    const response = await fetch("delete_expense.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `id=${id}`,
    });

    const data = await response.json();

    if (data.success) {
      // Refresh the expenses list after successful deletion
      fetchExpenses();
    } else {
      alert(data.message || "Failed to delete expense");
    }
  } catch (error) {
    console.error("Delete error:", error);
    alert("An error occurred while deleting the expense");
  }
}

// Fetch expenses and display
async function fetchExpenses() {
  try {
    const response = await fetch("fetch_expenses.php");
    const data = await response.json();

    if (data.success) {
      displayExpenses(data.expenses);
      const expenseArray = data.expenses;
      renderExpenseChart(expenseArray); // Render chart
      console.log(expenseArray);
    } else {
      console.error(data.message);
      alert(data.message);
    }
  } catch (error) {
    console.error("Error fetching expenses:", error);
    alert("An error occurred while fetching expenses. Please try again.");
  }
}

// Render doughnut chart
function renderExpenseChart(expenses) {
  // Get categories and amounts
  const categories = expenses.map((expense) => expense.expense); // Replace 'category' with the correct key
  const amounts = expenses.map((expense) => expense.amount); // Replace 'amount' with the correct key

  // Chart.js configuration
  const ctx = document.getElementById("expenseChart").getContext("2d");
  new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: categories,
      datasets: [
        {
          label: "Expenses",
          data: amounts,
          backgroundColor: [
            "#FF6384",
            "#36A2EB",
            "#FFCE56",
            "#4BC0C0",
            "#9966FF",
            "#FF9F40",
          ], // Add more colors as needed
          hoverBackgroundColor: [
            "#FF6384",
            "#36A2EB",
            "#FFCE56",
            "#4BC0C0",
            "#9966FF",
            "#FF9F40",
          ],
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "top",
        },
        tooltip: {
          enabled: true,
        },
      },
    },
  });
}

// Function to display expenses in the container
function displayExpenses(expenses) {
  const container = document.getElementById("expenses-container");
  container.innerHTML = ""; // Clear the container

  // Loop through the expenses and display them in the container
  expenses.forEach((expense) => {
    const expenseElement = document.createElement("div");
    expenseElement.className = "expense-item";
    expenseElement.innerHTML = `
            <p>${expense.expense}</p>
            <p>${expense.amount}</p>
            <button class="btn-del" onclick="deleteExpense(${expense.id})">Delete</button>
            
        
        `;
    container.appendChild(expenseElement);
  });
}

// Call the fetchExpenses function when the page loads
window.onload = fetchExpenses;
