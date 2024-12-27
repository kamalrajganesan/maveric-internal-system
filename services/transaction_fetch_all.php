<?php

require_once("../shared/actions/db/dao.php");

$page = "";
if (isset($_POST['param'])) {
    $page = htmlspecialchars($_POST['param']);
} else {
    echo $_POST;
}

$db = new sqlHelper();

// Update SQL query for the ticket table
$FetchAllSQL = "SELECT ticket.*, 
       (SELECT customer_nm FROM cust_mstr WHERE id = ticket.customer_id) AS customer_name,
       (SELECT agent_nm FROM agent WHERE id = ticket.assignd_agent_id) AS assignd_agent
FROM ticket
WHERE ticket.is_deleted = 0;
";

$db->prepareStatement($FetchAllSQL);
$db->execPreparedStatement();
$FetchAllSQLResultSet = $db->getResultSet();

if ($FetchAllSQLResultSet->num_rows > 0) {
    $data = array();
    while ($row = $FetchAllSQLResultSet->fetch_assoc()) {

        $btn = '
        <div class="btn-group">
            <button type="button" class="btn btn-inverse-primary btn-fw" data-toggle="modal" data-target="#viewTicketModal" id="viewTicketModalBtn" onclick="viewTicket(' . $row['id'] . ')">
                <i class="fa fa-2x fa-ellipsis-v"></i>
            </button>
            <button type="button" class="btn btn-inverse-secondary btn-fw" data-toggle="modal" data-target="#editTicketModal" id="editTicketModalBtn" onclick="editTicket(' . $row['id'] . ')">
                <i class="fa fa-2x fa-pencil-square-o"></i>
            </button>
            <button type="button" class="btn btn-inverse-dark btn-fw" data-toggle="modal" data-target="#removeTicketModal" id="removeTicketModalBtn" onclick="removeTicket(' . $row['id'] . ')">
                <i class="fa fa-2x fa-trash-o"></i>
            </button>
        </div>
        ';

        // Adjust the data array to reflect the columns in the ticket table
        $data[] = array(
            $row['uniq_id'],  // Customer ID
            $row['customer_name'],  // Customer ID
            $row['problem_desc'],  
            $row['service_typ'],  // New require    ment
            $row['assignd_agent'],  // Problem description
            $row['created_on'],  // Ticket status
            $row['status'],  // Service type
            $btn  // Action buttons
        );
    }
    echo json_encode(array("success" => true, "data" => $data, "message" => "Data found."));
} else {
    echo json_encode(array("success" => false, "data" => [], "message" => "No data found."));
}
