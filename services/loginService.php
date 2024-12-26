<?php

require_once("../shared/actions/db/dao.php");
require_once("../shared/actions/login/authLogin.php");

// Get form data
$u = $_POST['username'];
$p = $_POST['password'];

// Check if form data is not empty
if (!empty($u) && !empty($p)) {
    
    $db = new sqlHelper();
    $query = "SELECT * FROM agent WHERE email = ? AND pass_code = ?";
    $db->prepareStatement($query);
    $db->setParameters([$u, $p], 'ss');
    $db->execPreparedStatement();
    $resultSet = $db->getResultSet();

    if ($resultSet->num_rows > 0) {
        $sess = new SessUsers();
        $sess->SetAgentSession($resultSet->fetch_assoc());
        echo json_encode(array("success" => true));
    } else {
        echo json_encode(array("success" => false));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Username and password are required."));
}
?>