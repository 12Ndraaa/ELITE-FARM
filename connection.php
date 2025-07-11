<?php
    $con = new mysqli("localhost", "root", "", "elite-farm"); // Changed variable name to $con

    // Check connection
    if ($con->connect_errno) { // Updated to use $con
        echo "Failed to connect to MySQL: " . $con->connect_error;
        exit();
    }
?>