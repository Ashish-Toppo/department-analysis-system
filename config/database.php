<?php
// Database credentials
$host = "localhost"; // Database host
$dbname = "adbu_daf_departments"; // Database name
$username = "root"; // Database username
$password = ""; // Database password













// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
