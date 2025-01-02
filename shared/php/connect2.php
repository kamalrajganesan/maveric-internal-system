<?php

$servername = "localhost";
$dbname = "u140987190_amc_mgmt";
$username = "u140987190_dbUsr_mcss";
$password = "MavAMC#db-898";

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
