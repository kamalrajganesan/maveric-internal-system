<?php

require_once("../shared/php/connect.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if(!session_id()) {
        session_start();
    }

    $valid['success'] = false;
    $valid['message'] = "";

    // print_r($_POST);

    // mandatory fields
    $cId = $_POST['cId']; 
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
    $updatedBy = $_SESSION['user']['id'];


    $validationFlag = true;



    // Make sure all the mandatory fields are filled
    if (empty($customerUniqCode) || empty($customerCompanyName) || empty($customerPhone) || empty($customerEmail) || 
        empty($customerPincode) || empty($customerCity) || empty($customerServiceType)) {

        $validationFlag = false;
        $valid["message"] = "Please fill all the required fields!";
    } 

    if($validationFlag){
        
        $db = new sqlHelper();

        $query = "
            UPDATE cust_mstr SET 
                customer_nm = ?, company_nm = ?, address_ln = ?, contact = ?, updated_by = ?, 
                amc_st_date = ?, amc_end_date = ?, spl_cust_note = ?, license_typ = ?, 
                email = ?, sys_email = ?, pincode = ?, city = ?, area = ?, 
                service_type = ?, is_active = ?, customer_uniq_code = ?, telephone = ?,
                referredBy = ?, auditor = ?, tally_st_date = ?, tally_end_date = ?
            WHERE id = ?
        ";

        try {
            // Prepare the statement
            $stmt = $db->prepareStatement($query);

            // Parameters for binding
            $params = [
                $customerName, $customerCompanyName, $customerAddress, $customerPhone, $updatedBy,
                $customerAMCStartDate, $customerAMCEndDate, $customerSpecialNote, $customerLicenseType,
                $customerEmail, $customerTallyEmail, $customerPincode, $customerCity, $customerArea,
                $customerServiceType, $customerActiveStatus, $customerUniqCode, $customerTelephone,
                $customerReferredBy, $customersAuditor, $customerTallySubStartDate, $customerTallySubEndDate, 
                $cId
            ];
            $types = 'sssssssssssssssissssssi'; 
            $db->setParameters($params, $types);

            // Execute the statement
            $db->execPreparedStatement();
            
            $valid["message"] = 'Customer updated successfully!';
            $valid["detailed"] = "";

        } catch (Exception $e) {
            $valid = false;
            $valid["message"] = 'Error in updating Customer details';
            $valid["detailed"] = $e->getMessage();
        }

    } 
    
    echo json_encode($valid);

}
?>