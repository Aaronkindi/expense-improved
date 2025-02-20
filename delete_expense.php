<?php
session_start(); // Start the session
header('Content-Type: application/json');

// Add proper database connection and security measures
include 'database_con.php'; // Your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    
    
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM expenses WHERE id = ? and user_id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error deleting expense: ' . $conn->error
        ]);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>