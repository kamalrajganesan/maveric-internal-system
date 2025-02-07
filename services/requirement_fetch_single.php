<?php

require_once("../shared/actions/db/dao.php");

if (isset($_POST['requirementId'])) {
    $db = new sqlHelper();
    
    $requirementFetchSingleSQL = "
        SELECT 
            r.id, 
            r.nm AS requirement_name, 
            r.brief, 
            r.detailed, 
            r.cust_id, 
            r.phone, 
            r.updated_on, 
            r.updated_by, 
            r.created_on, 
            r.created_by
        FROM 
            req_tracker r
        WHERE 
            r.is_deleted = 0 
            AND r.id = ?
    ";

    // Prepare, bind, and execute the SQL statement
    $db->prepareStatement($requirementFetchSingleSQL);
    $db->setParameters([$_POST['requirementId']], 'i'); // 'i' for integer parameter
    $db->execPreparedStatement();
    $requirementFetchSingleResultSet = $db->getResultSet();

    if ($requirementFetchSingleResultSet->num_rows > 0) {
        $data = array();
        while ($row = $requirementFetchSingleResultSet->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode(array("success" => true, "data" => $data, "message" => "Data found."));
    } else {
        echo json_encode(array("success" => false, "data" => [], "message" => "No data found."));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Invalid request."));
}

?>