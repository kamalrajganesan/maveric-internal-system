<?php
require_once("../shared/actions/db/dao.php");

if (isset($_POST['leadId'])) {
    $db = new sqlHelper();
    
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
            l.assignee AS assignee_id,
            COALESCE((SELECT agent_nm FROM agent a WHERE a.id = l.assignee), 'Unassigned') AS assignee,
            COALESCE((SELECT agent_nm FROM agent a WHERE a.id = l.created_by), 'Admin') AS created_by,
            COALESCE((SELECT agent_nm FROM agent a WHERE a.id = l.updated_by), 'Admin') AS updated_by,
            l.created_by AS created_by_id,
            l.updated_by AS updated_by_id
        FROM 
            lead_email_tracker l
        WHERE 
            l.is_deleted = 0 
            AND l.id = ?
    ";

    $db->prepareStatement($leadFetchSingleSQL);
    $db->setParameters([$_POST['leadId']], 'i');
    $db->execPreparedStatement();
    $leadFetchSingleResultSet = $db->getResultSet();

    if ($leadFetchSingleResultSet->num_rows > 0) {
        $data = [];
        while ($row = $leadFetchSingleResultSet->fetch_assoc()) {
            $lead = [
                'id' => $row['id'],
                'lead_name' => $row['lead_name'],
                'email' => $row['email'],
                'company_name' => $row['company_name'],
                'contact' => $row['contact'],
                'requirement' => $row['requirement'],
                'description' => $row['description'],
                'notes' => $row['notes'],
                'address_line' => $row['address_line'],
                'pincode' => $row['pincode'],
                'city' => $row['city'],
                'area' => $row['area'],
                'follow_up_date' => $row['follow_up_date'],
                'lead_status' => $row['lead_status'],
                'is_active' => $row['is_active'],
                'assignee' => $row['assignee'], // This now contains the agent name or 'Unassigned'
                'created_by' => $row['created_by'],
                'updated_by' => $row['updated_by'],
                'created_by_id' => $row['created_by_id'],
                'updated_by_id' => $row['updated_by_id'],
                'assignee_id' => $row['assignee_id']
            ];
            $data[] = $lead;
        }
        echo json_encode(["success" => true, "data" => $data, "message" => "Data found."]);
    } else {
        echo json_encode(["success" => false, "data" => [], "message" => "No data found."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>