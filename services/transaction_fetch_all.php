<?php

require_once("../shared/actions/db/dao.php");

$page = "";
if (isset($_POST['type']) && isset($_POST['value'])) {
    $type = htmlspecialchars($_POST['type']);
    $value = htmlspecialchars($_POST['value']);
} else {
    echo $_POST;
}

$db = new sqlHelper();

// Update SQL query for the ticket table
$FetchAllSQL = 
"SELECT
    ticket.uniq_id,
    ticket.problem_stmt,
    ticket.created_on,
    ticket.status,
    CONCAT(cust_mstr.company_nm) AS company_nm,
    ticket.service_typ,
    CONCAT(created_agent.agent_nm) AS created_by_name,
    CONCAT(updated_agent.agent_nm) AS updated_by_name
FROM
    ticket
JOIN
    cust_mstr ON ticket.customer_id = cust_mstr.id
LEFT JOIN
    agent AS created_agent ON ticket.created_by = created_agent.id
LEFT JOIN
    agent AS updated_agent ON ticket.updated_by = updated_agent.id
WHERE ticket.is_deleted = 0
";

if($type == "serviceType") {
    switch ($value) {
        case 'AMC':
            $FetchAllSQL .= " and service_typ = 'AMC'";
            break;
        case 'Cloud':
            $FetchAllSQL .= " and service_typ = 'Cloud'";
            break;
        case 'Tally':
            $FetchAllSQL .= " and ticket.service_typ = 'Tally'";
            break;
        case 'Cloud':
            $FetchAllSQL .= " and ticket.service_typ = 'Cloud'";
            break;
        case 'OneTime':
            $FetchAllSQL .= " and service_typ = 'One Time'";
            break;
        default:
            $FetchAllSQL .= ";";
            break;
    }
} else {
    switch ($value) {
        case 'OnCall':
            $FetchAllSQL .= " and ticket.service_thru = 'Phone Call'";
            break;
        case 'Remote':
            $FetchAllSQL .= " and ticket.service_thru = 'Remote'";
            break;
        case 'PhysicalVisits':
            $FetchAllSQL .= " and ticket.service_thru = 'Physical Visit'";
            break;
        default:
            $FetchAllSQL .= "";
            break;
    }
} 

$FetchAllSQL .= " ORDER BY ticket.created_on DESC";

$db->prepareStatement($FetchAllSQL);
$db->execPreparedStatement();
$FetchAllSQLResultSet = $db->getResultSet();

if ($FetchAllSQLResultSet->num_rows > 0) {
    
    $data = array();
    while ($row = $FetchAllSQLResultSet->fetch_assoc()) {

        $btn = '
        <div class="btn-group">
            <button type="button" class="btn btn-inverse-primary btn-fw" data-toggle="modal" data-target="#viewTransactionModal" id="viewTransactionModalBtn" onclick="viewTicket(\'' . $row['uniq_id'] . '\')">
                <i class="fa fa-2x fa-ellipsis-v"></i>
            </button>
            <button type="button" class="btn btn-inverse-secondary btn-fw" data-toggle="modal" data-target="#editTransactionModal" id="editTransactionModalBtn" onclick="editTicket(\'' . $row['uniq_id'] . '\')">
                <i class="fa fa-2x fa-pencil-square-o"></i>
            </button>
            <button type="button" class="btn btn-inverse-dark btn-fw" data-toggle="modal" data-target="#removeTicketModal" id="removeTicketModalBtn" onclick="removeTicket(\'' . $row['uniq_id'] . '\')">
                <i class="fa fa-2x fa-trash-o"></i>
            </button>
        </div>
        ';

        // Adjust the data array to reflect the columns in the ticket table
        $data[] = array(
            $row['created_on'],  
            $row['uniq_id'], 
            $row['problem_stmt'],  
            $row['company_nm'],  
            $row['service_typ'],  
            $row['created_by_name'], 
            $row['updated_by_name'], 
            $row['status'],  
            $btn  
        );
    }
    echo json_encode(array("success" => true, "data" => $data, "message" => "Data found."));
} else {
    echo json_encode(array("success" => false, "data" => [], "message" => "No data found."));
}
