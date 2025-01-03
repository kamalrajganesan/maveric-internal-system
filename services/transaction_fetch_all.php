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
$FetchAllSQL = "SELECT ticket.*, 
       (SELECT customer_nm FROM cust_mstr WHERE id = ticket.customer_id) AS customer_name,
       (SELECT agent_nm FROM agent WHERE id = ticket.assignd_agent_id) AS assignd_agent
FROM ticket
WHERE ticket.is_deleted = 0
";

if($type == "serviceType") {
    switch ($value) {

        case 'OneTime':
            $FetchAllSQL .= "and service_typ = 'One Time';";
            break;
        case 'Tally':
            $FetchAllSQL .= " and service_typ = 'Tally';";
            break;
        case 'AMC':
            $FetchAllSQL .= " and service_typ = 'AMC';";
            break;
        default:
            $FetchAllSQL .= ";";
            break;
    }
} else {
    switch ($value) {

        case 'OnCall':
            $FetchAllSQL .= " and service_thru = 'Phone Call';";
            break;
        case 'Remote':
            $FetchAllSQL .= " and service_thru = 'Remote';";
            break;
        case 'PhysicalVisits':
            $FetchAllSQL .= " and service_thru = 'Physical Visit';";
            break;
        default:
            $FetchAllSQL .= ";";
            break;
    }
} 

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
            $row['uniq_id'], 
            $row['created_on'],  
            $row['problem_stmt'],  
            $row['customer_name'],  
            $row['service_typ'],  
            $row['assignd_agent'], 
            $row['status'],  
            $btn  
        );
    }
    echo json_encode(array("success" => true, "data" => $data, "message" => "Data found."));
} else {
    echo json_encode(array("success" => false, "data" => [], "message" => "No data found."));
}
