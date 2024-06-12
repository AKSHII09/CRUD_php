<?php
// Database configuration
$servername = "localhost";
$username = "root"; // replace with your MySQL username
$password = ""; // replace with your MySQL password (if any)
$dbname = "contacts"; // correct database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $first_name = $_POST['fname'];
    $last_name = $_POST['lname'];
    $mobile_no = $_POST['mobo'];
    $messages = $_POST['msg'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO `dbinfo` (`first_name`, `last_name`, `mobile_no`, `message`) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $first_name, $last_name, $mobile_no, $messages);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Record inserted successfully";
    } else {
        die("Query failed: " . $stmt->error);
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
