//fetch total expenses and display
async function fetchTotalExpenses() {
  try {
    const response = await fetch("fetch_budget.php");
    const data = await response.json();

    if (data.success) {
      const totalExpenseArray = data.totalExpenses;
      renderTotalExpenseChart(totalExpenseArray); // Render chart
      console.log(totalExpenseArray);
    } else {
      console.error(data.message);
      alert(data.message);
    }
  } catch (error) {
    console.error("Error fetching total expenses:", error);
    alert("An error occurred while fetching total expenses. Please try again.");
  }
}

// Render doughnut chart
function renderTotalExpenseChart(totalExpenses) {
  // Get categories and amounts
  const categories = totalExpenses.map((totalExpense) => totalExpense.expense); // Replace 'category' with the correct key
  const amounts = totalExpenses.map((totalExpense) => totalExpense.amount); // Replace 'amount' with the correct key

  // Chart.js configuration
  const ctx = document.getElementById("totalExpenseChart").getContext("2d");
  new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: categories,
      datasets: [
        {
          label: "Total Expenses",
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

// Display total expenses

window.onload = fetchTotalExpenses;
