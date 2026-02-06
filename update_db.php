<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = ''; // your password
$database = 'tigaBelasCafe';

// Path to your SQL file
$sqlFile = 'assets/db/tigaBelasCafe.sql';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read SQL file
$sql = file_get_contents($sqlFile);

if ($sql === false) {
    die("Error reading SQL file: " . $sqlFile);
}

// Execute SQL queries
if ($conn->multi_query($sql)) {
    echo "Database updated successfully from $sqlFile";

    // Clear remaining results
    while ($conn->more_results() && $conn->next_result()) {
        // Free each result
        $result = $conn->store_result();
        if ($result) {
            $result->free();
        }
    }
} else {
    echo "Error updating database: " . $conn->error;
}

$conn->close();
?>
