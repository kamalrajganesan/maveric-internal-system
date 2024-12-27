<?php
// Include the database connection
require_once("../shared/actions/db/dao.php");

// Initialize response variable
$response = '';

// Check if the form data is posted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!session_id()) {
        session_start();
    }

    function generateSecureNumericUniqueId()
    {
        $uniqueId = substr(abs(crc32(uniqid())), 0, 5); // Generate unique hash and take the first 5 digits
        return "TCK_" . $uniqueId;
    }

    // Capture form data
    $uniqId = generateSecureNumericUniqueId();
    $createdBy = $_SESSION['user']['id'];
    $assignedAgentId = $createdBy;
    $customerId = isset($_POST['customerId']) ? $_POST['customerId'] : 0;
    $comments = isset($_POST['comments']) ? htmlspecialchars($_POST['comments']) : '';
    $problemDesc = isset($_POST['problemDesc']) ? htmlspecialchars($_POST['problemDesc']) : '';
    
    // $newRequirement = isset($_POST['newRequirement']) ? htmlspecialchars($_POST['newRequirement']) : '';
    
    $status = "New";    // Default status as it is a new transaction
    $problemStmt = isset($_POST['problemStmt']) ? htmlspecialchars($_POST['problemStmt']) : '';
    $serviceType = isset($_POST['serviceType']) ? htmlspecialchars($_POST['serviceType']) : '';
    $isUnderAMC = isset($_POST['isUnderAMC']) ? (int)$_POST['isUnderAMC'] : 0;
    $notes = isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : '';
    $closedDate = isset($_POST['closedDate']) ? htmlspecialchars($_POST['closedDate']) : null;
    $isDeleted = isset($_POST['isDeleted']) ? (int)$_POST['isDeleted'] : 0;

    $commentsArr = array(
        "timestamp" => date("Y-m-d H:i:s"),
        "message" => $comments,
    );

    // Make sure all the required fields are filled
    if (empty($uniqId) || empty($createdBy) || empty($assignedAgentId) || empty($customerId)) {
        
        $response = 'Some required fields are missing.';
    } else {
        
        $db = new sqlHelper();
        
        // Prepare the SQL insert query
        $query = "INSERT INTO ticket (
                uniq_id, assignd_agent_id, created_by, customer_id, 
                comments, problem_desc, status, problem_stmt, service_typ, 
                is_under_amc, notes, is_active, is_deleted
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0
            )";

        try {
            // Prepare the statement
            $stmt = $db->prepareStatement($query);

            // Parameters for binding
            $params = [
                $uniqId,
                $assignedAgentId,
                $createdBy,
                $customerId,
                json_encode($commentsArr),
                $problemDesc,
                $status,
                $problemStmt,
                $serviceType,
                $isUnderAMC,
                $solvedBy,
                $notes,

            ];

            // Bind the parameters (types: 's' for string, 'i' for integer, 'd' for double)
            $types = 'sisisssssiis'; // Adjust types if necessary
            $db->setParameters($params, $types);

            // Execute the statement
            $db->execPreparedStatement();

            $response = array("success" => true, "message" => 'Transaction created successfully!');
        } catch (Exception $e) {
            $response = 'Error: ' . $e->getMessage();
        }
    }
} else {
    $response = 'Invalid request method';
}

// Return the response as JSON
echo json_encode($response);
