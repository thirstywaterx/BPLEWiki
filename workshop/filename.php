<?php
// Include database connection file
include "../php/connect.php";
$conn = connect();

// Check if ID is provided
if (isset($_GET['id'])) {
    // Get the ID from the URL
    $id = intval($_GET['id']); // Ensure it's an integer to prevent SQL injection

    // Prepare SQL statement
    $sql = "SELECT name FROM fileinfo WHERE id = ?";
    $stmt = $conn->prepare($sql); // Use prepared statements for security
    $stmt->bind_param("i", $id); // Bind the ID parameter
    $stmt->execute(); // Execute the query
    $stmt->bind_result($name); // Bind the result to the $name variable

    // Fetch the result
    if ($stmt->fetch()) {
        // Print the name
        echo htmlspecialchars($name); // Use htmlspecialchars to prevent XSS
    } else {
        echo "No record found for ID: " . htmlspecialchars($id);
    }

    // Close the statement
    $stmt->close();
} else {
    echo "No ID provided.";
}

// Close the database connection
$conn->close();
?>