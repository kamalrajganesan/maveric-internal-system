<?php

require_once("../shared/actions/db/dao.php");

if (isset($_POST['leadId'])) {
    $db = new sqlHelper();
    
    // SQL query to fetch the lead details
    $leadFetchSingleSQL = "
        SELECT 
            l.id, 
            l.lead_nm AS lead_name, 
            l.email, 
            l.company_nm AS company_name, 
            l.contact, 
            l.requirement, 
            l.description, 
            l.notes, 
            l.address_ln AS address_line, 
            l.pincode, 
            l.city, 
            l.area, 
            l.follow_up_dt AS follow_up_date, 
            l.lead_status, 
            l.is_active, 
            (SELECT agent_nm FROM agent a WHERE a.id = l.created_by) AS created_by
        FROM 
            lead_call_tracker l
        WHERE 
            l.is_deleted = 0 
            AND l.id = ?
    ";

    // Prepare, bind, and execute the SQL statement
    $db->prepareStatement($leadFetchSingleSQL);
    $db->setParameters([$_POST['leadId']], 'i'); // 'i' for integer parameter
    $db->execPreparedStatement();
    $leadFetchSingleResultSet = $db->getResultSet();

    if ($leadFetchSingleResultSet->num_rows > 0) {
        $data = array();
        while ($row = $leadFetchSingleResultSet->fetch_assoc()) {
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
