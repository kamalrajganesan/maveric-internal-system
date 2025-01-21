<?php

require_once("../shared/actions/db/dao.php");

date_default_timezone_set('Asia/Kolkata');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if(!session_id()) {
        session_start();
    }

    $valid['success'] = false;
    $valid['message'] = "";

    // print_r($_POST);

    // mandatory fields
    $customerUniqCode = isset($_POST['customerUniqCode'])? htmlspecialchars($_POST['customerUniqCode']) : "";
    $customerCompanyName = isset($_POST['companyName'])? htmlspecialchars($_POST['']) : "";
    $customerPhone = isset($_POST['contact'])? htmlspecialchars($_POST['']) : "";
    $customerEmail = isset($_POST['email'])? htmlspecialchars($_POST['']) : "";
    $customerPincode = isset($_POST['pincode'])? htmlspecialchars($_POST['']) : "";
    $customerCity = isset($_POST['city'])? htmlspecialchars($_POST['']) : "";
    $customerServiceType = (isset($_POST['serviceType']))? htmlspecialchars(implode(',', $_POST['serviceType'])) : "";
    
    // service-type-option-based fields
    $customerAMCStartDate = isset($_POST['amcStartDate'])? htmlspecialchars($_POST['amcStartDate']) : "";
    $customerAMCEndDate = isset($_POST['amcEndDate'])? htmlspecialchars($_POST['amcEndDate']) : "";
    $customerTallySubStartDate = isset($_POST['tallyStartDate'])? htmlspecialchars($_POST['tallyStartDate']) : "";
    $customerTallySubEndDate = isset($_POST['tallyEndDate'])? htmlspecialchars($_POST['tallyEndDate']) : "";
    $customerCloudStartDate = isset($_POST['cloudStartDate'])? htmlspecialchars($_POST['cloudStartDate']) : "";
    $customerCloudEndDate = isset($_POST['cloudEndDate'])? htmlspecialchars($_POST['cloudEndDate']) : "";
    $customerTallyEmail = isset($_POST['tallyEmail'])? htmlspecialchars($_POST['tallyEmail']) : "";
    $customerLicenseType = isset($_POST['licenseType'])? htmlspecialchars($_POST['licenseType']) : "";
    
    // nullable fields
    $customerName = isset($_POST['customerName'])? htmlspecialchars($_POST['customerName']) : "";
    $customerTelephone = isset($_POST['telephone'])? htmlspecialchars($_POST['telephone']) : "";
    $customerAddress = isset($_POST['address'])? htmlspecialchars($_POST['address']) : "";
    $customerArea = isset($_POST['area'])? htmlspecialchars($_POST['area']) : "";
    $customerSpecialNote = isset($_POST['specialNote'])? htmlspecialchars($_POST['specialNote']) : "";
    $customerReferredBy = isset($_POST['referredBy'])? htmlspecialchars($_POST['referredBy']) : "";
    $customersAuditor = isset($_POST['auditor'])? htmlspecialchars($_POST['auditor']) : "";

    // auto generating fields
    $createdBy = $_SESSION['user']['id'];
    $updatedBy = $_SESSION['user']['id'];
    $createdOn = date('Y-m-d H:i:s');

    
    $validationFlag = true;


    // Make sure all the mandatory fields are filled
    if (empty($customerUniqCode) || empty($customerCompanyName) || empty($customerPhone) || empty($customerEmail) || 
        empty($customerPincode) || empty($customerCity) || empty($customerServiceType)) {

        $validationFlag = false;
        $valid["message"] = "Mandatory";
    } 
    
    if($validationFlag) {
        
        $db = new sqlHelper();
        $query = "INSERT INTO cust_mstr( 
                    customer_nm, company_nm, address_ln, contact, updated_by, 
                    created_by, amc_st_date, amc_end_date, spl_cust_note, license_typ, 
                    email, sys_email, pincode, city, area, 
                    service_type, is_active, customer_uniq_code, is_deleted, telephone,
                    referredBy, auditor, tally_st_date, tally_end_date, cloud_st_date,
                    cloud_end_date
                )  
                VALUES (
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, 
                    ?, ?, ?, ?, ?, 
                    ?, 1, ?, 0, ?,
                    ?, ?, ?, ?, ?,
                    ?
                )";

        try {
            // Prepare the statement
            $stmt = $db->prepareStatement($query);

            // Parameters for binding
            $params = [
                $customerName, $customerCompanyName, $customerAddress, $customerPhone, $updatedBy,
                $createdBy, $customerAMCStartDate, $customerAMCEndDate, $customerSpecialNote, $customerLicenseType,
                $customerEmail, $customerTallyEmail, $customerPincode, $customerCity, $customerArea,
                $customerServiceType, $customerUniqCode, $customerTelephone,
                $customerReferredBy, $customersAuditor, $customerTallySubStartDate, $customerTallySubEndDate, $customerCloudStartDate,
                $customerCloudEndDate
            ];
            $types = 'ssssssssssssssssssssssss'; 
            $db->setParameters($params, $types);

            // Execute the statement
            $resp = $db->execPreparedStatement();
            if($resp['success']){ 
                $valid['success'] = true;
                $valid["message"] = 'Customer created successfully!';
            } else {

                $valid['success'] = false;
                if(str_contains($resp["message"], 'Duplicate entry') && str_contains($resp["message"], 'customer_uniq_code')) {
                    $valid["message"] = 'Duplicate entry - Customer Serial Number';
                } else {
                    $valid["message"] = 'Default';
                }
            }

        } catch (Exception $e) {
            
            $valid['success'] = false;
            $valid["message"] = 'Exception';
            $valid["detailed"] = $e->getMessage();
        }

    } 

    echo json_encode($valid);

}
?>