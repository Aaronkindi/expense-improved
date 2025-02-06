// Function to fetch all expenses using AJAX and display them in a div

let expenseArray = [];
async function fetchExpenses() {
  try {
    // Make GET request to the PHP server
    const response = await fetch("fetch_expenses.php");
    const data = await response.json();

    if (data.success) {
      // Display the expenses in the div
      displayExpenses(data.expenses);
      expenseArray = data.expenses;
      console.log(expenseArray);
    } else {
      console.error(data.message); // Log the error message to the console
      alert(data.message); // Display the error message in an alert box
    }
  } catch (error) {
    console.error("Error fetching expenses:", error); // Log the error message to the console
    alert("An error occurred while fetching expenses. Please try again."); // Display the error message in an alert box
  }
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
