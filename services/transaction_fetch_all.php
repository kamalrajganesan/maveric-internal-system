<?php

require_once("../shared/actions/db/dao.php");

$valid = array('success' => false, 'message' => "", 'detail' => "");

// Check if the form data is posted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $page = "";

    $type = isset($_POST['type']) ? htmlspecialchars($_POST['type']) : "";
    $value = isset($_POST['value']) ? htmlspecialchars($_POST['value']) : "";
    $transac_range = isset($_POST['transac_range']) ? htmlspecialchars($_POST['transac_range']) : "";
    $transac_date = isset($_POST['transac_single_date']) ? htmlspecialchars($_POST['transac_single_date']) : "";
    
    if( $transac_range != "" && $transac_date != "") {
        $valid["success"] = false;
        $valid["message"] = "Two Date Filters";
        $valid["data"] = [];
        echo json_encode($valid);
        exit();
    }
    
    
    if($transac_range != "") {
        $from = explode(" to ", $transac_range)[0];
        $to = explode(" to ", $transac_range)[1];
    }
    
    
    $db = new sqlHelper();


    $FetchAllSQL = 
    "SELECT
        ticket.uniq_id,
        ticket.problem_stmt,
        ticket.created_on,
        ticket.updated_on,
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
                $FetchAllSQL .= " and ticket.service_typ = 'AMC'";
                break;
            case 'Cloud':
                $FetchAllSQL .= " and ticket.service_typ = 'Cloud'";
                break;
            case 'Tally':
                $FetchAllSQL .= " and ticket.service_typ = 'Tally'";
                break;
            case 'Cloud':
                $FetchAllSQL .= " and ticket.service_typ = 'Cloud'";
                break;
            case 'OneTime':
                $FetchAllSQL .= " and ticket.service_typ = 'One Time'";
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

    if($transac_range != "") {
        $FetchAllSQL .= " and DATE(ticket.created_on) BETWEEN ? AND ?";
    } else if($transac_date != "") {
        $FetchAllSQL .= " and DATE(ticket.created_on) = ?";
    }

    // $FetchAllSQL .= " ORDER BY ticket.created_on DESC";

    $db->prepareStatement($FetchAllSQL);

    if($transac_range != "") {
        $db->setParameters([$from, $to], "ss");
    } else if($transac_date != "") {
        $db->setParameters([$transac_date], "s");
    }

    $resp = $db->execPreparedStatement();

    if($resp["success"] === TRUE) {
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

                switch ($row['status']) {
                    case "New":
                      $statusUI = '<div class="d-flex mt-2"><div class="badge badge-opacity-info me-3"> '.$row["status"].' </div>';
                      break;
                    case "Contacted/Pending":
                      $statusUI = '<div class="d-flex mt-2"><div class="badge badge-opacity-warning me-3"> '.$row["status"].' </div>';
                      break;
                    case "Following Up":
                      $statusUI = '<div class="d-flex mt-2"><div class="badge badge-opacity-purple me-3"> '.$row["status"].' </div>';
                      break;
                    case "Closed":
                      $statusUI = '<div class="d-flex mt-2"><div class="badge badge-opacity-success me-3"> '.$row["status"].' </div>';
                      break;                  
                    default:
                      $statusUI = '<div class="d-flex mt-2"><div class="badge badge-opacity-light me-3"> '.$row["status"].' </div>';
                      break;
                  }

                $cDate = new DateTime($row['created_on']);
                $created_date = '
                    <div style="text-align: right">
                        <span>' . $cDate->format('Y-m-d') . '</span>
                        <br>
                        <small>' . $cDate->diff(new DateTime())->days . ' days ago</small>
                    </div>
                '; 

                $formattedDate = new DateTime($row['updated_on']);
                
                // Adjust the data array to reflect the columns in the ticket table
                $data[] = array(
                    $formattedDate->format('d-m-Y h:i:s A'),
                    $row['uniq_id'], 
                    $row['problem_stmt'],  
                    $row['company_nm'],  
                    $statusUI,
                    $created_date,
                    $btn  
                );
            }
            $valid["success"] = true;
            $valid["message"] = "Data found.";
            $valid["data"] = $data;
        } else {
            $valid["success"] = true;
            $valid["message"] = "No Data found.";
            $valid["data"] = [];
        }
    }
}
// Return the response as JSON
echo json_encode($valid);

?>
