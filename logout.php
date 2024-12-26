<?php

if (!session_id()) {
    session_start();
    echo "Session started; destroyed";
    session_destroy();
    header("Location: index.php");
    exit();
} 

?>