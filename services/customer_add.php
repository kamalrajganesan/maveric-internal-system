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
    $customerUniqCode = $_POST['customerUniqCode'];
    $customerCompanyName = $_POST['companyName'];
    $customerPhone = $_POST['contact'];
    $customerEmail = $_POST['email'];
    $customerPincode = $_POST['pincode'];
    $customerCity = $_POST['city'];
    $customerServiceType = implode(',', $_POST['serviceType']);
    
    // service-type-option-based fields
    $customerAMCStartDate = $_POST['amcStartDate'];
    $customerAMCEndDate = $_POST['amcEndDate'];
    $customerTallySubStartDate = $_POST['tallyStartDate'];
    $customerTallySubEndDate = $_POST['tallyEndDate'];
    $customerCloudStartDate = $_POST['cloudStartDate'];
    $customerCloudEndDate = $_POST['cloudEndDate'];
    $customerTallyEmail = $_POST['tallyEmail'];
    $customerLicenseType = $_POST['licenseType'];
    
    // nullable fields
    $customerName = $_POST['customerName'];
    $customerTelephone = $_POST['telephone'];
    $customerAddress = $_POST['address'];
    $customerArea = $_POST['area'];
    $customerSpecialNote = $_POST['specialNote'];
    $customerReferredBy = $_POST['referredBy'];
    $customersAuditor = $_POST['auditor'];

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