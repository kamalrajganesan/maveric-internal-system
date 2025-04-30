<?php

require_once("../shared/actions/db/dao.php");

if(isset($_POST['agentId'])) {
    
    $db = new sqlHelper();
    $agentFetchSingleSQL = 
        "SELECT * FROM agent a 
        WHERE id = ? and
            is_deleted = 0";

    $db->prepareStatement($agentFetchSingleSQL);
    $db->setParameters([$_POST['agentId']], 's');
    $db->execPreparedStatement();
    $agentFetchSingleSQLResultSet = $db->getResultSet();

    if ($agentFetchSingleSQLResultSet->num_rows > 0) {
        
        $data = array();
        while ($row = $agentFetchSingleSQLResultSet->fetch_assoc()) {
            $data[] = $row;
        }

        $data[0]["created_on"] = date('d-m-Y h:i:s A', strtotime($data[0]["created_on"]));


        echo json_encode(array("success" => true, "data" => $data, "message" => "Data found."));
    } else {
        echo json_encode(array("success" => false, "data" => [], "message" => "No data found."));
    }
}

?>