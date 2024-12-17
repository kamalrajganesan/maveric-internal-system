<?php

$servername = "localhost";
$dbname = "amc_mgmt";
$username = "root";
$password = "";

// Create connection
function createConn() {
    global $servername, $dbname, $username, $password;
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if( $conn -> connect_error ) {
        return null;
    }
    else {
        return $conn;
    } 
}

// close connection
function closeConn($conn) {
    if($conn) {
        mysqli_close($conn);
    }
}

?>
