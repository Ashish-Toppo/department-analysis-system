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





// all necessary db functins
/**
 * Fetch data from the database using a custom SQL query
 * 
 * @param string $query The SQL query to execute
 * @return array The result set as an associative array
 */
function fetch_query($query) {
    global $conn; // Use the global database connection

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Initialize an empty array to store the data
    $data = array();

    // Check if the query executed successfully
    if ($result) {
        // Fetch each row of the result set
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    } else {
        // Log or display an error if the query fails (optional)
        error_log("Error executing query: " . mysqli_error($conn));
    }

    // Return the fetched data
    return $data;
}

















?>
