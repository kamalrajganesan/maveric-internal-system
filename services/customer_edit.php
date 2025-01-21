<?php

require_once("../shared/actions/db/dao.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if(!session_id()) {
        session_start();
    }

    $valid['success'] = false;
    $valid['message'] = "";

    // print_r($_POST);

    // mandatory fields
    $cId = isset($_POST['cId']) ? htmlspecialchars($_POST['cId']) : ""; 
    $customerUniqCode = isset($_POST['customerUniqCode']) ? htmlspecialchars($_POST['customerUniqCode']) : "";
    $customerCompanyName = isset($_POST['companyName']) ? htmlspecialchars($_POST['companyName']) : "";
    $customerPhone = isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : "";
    $customerEmail = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : "";
    $customerPincode = isset($_POST['pincode']) ? htmlspecialchars($_POST['pincode']) : "";
    $customerCity = isset($_POST['city']) ? htmlspecialchars($_POST['city']) : "";
    $customerServiceType = isset($_POST['serviceType']) ? htmlspecialchars(implode(',', $_POST['serviceType'])) : "";
    
    // service-type-option-based fields ) ? htmlspecialchars() : ""
    $customerAMCStartDate = isset($_POST['amcStartDate']) ? htmlspecialchars($_POST['amcStartDate']) : "";
    $customerAMCEndDate = isset($_POST['amcEndDate']) ? htmlspecialchars($_POST['amcEndDate']) : "";
    $customerTallySubStartDate = isset($_POST['tallyStartDate']) ? htmlspecialchars($_POST['tallyStartDate']) : "";
    $customerTallySubEndDate = isset($_POST['tallyEndDate']) ? htmlspecialchars($_POST['tallyEndDate']) : "";
    $customerTallyEmail = isset($_POST['tallyEmail']) ? htmlspecialchars($_POST['tallyEmail']) : "";
    $customerCloudStartDate = isset($_POST['cloudStartDate']) ? htmlspecialchars($_POST['cloudStartDate']) : "";
    $customerCloudEndDate = isset($_POST['cloudEndDate']) ? htmlspecialchars($_POST['cloudEndDate']) : "";
    $customerLicenseType = isset($_POST['licenseType']) ? htmlspecialchars($_POST['licenseType']) : "";
    
    // nullable fields
    $customerName = isset($_POST['customerName']) ? htmlspecialchars($_POST['customerName']) : "";
    $customerTelephone = isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : "";
    $customerAddress = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : "";
    $customerArea = isset($_POST['area']) ? htmlspecialchars($_POST['area']) : "";
    $customerSpecialNote = isset($_POST['specialNote']) ? htmlspecialchars($_POST['specialNote']) : "";
    $customerReferredBy = isset($_POST['referredBy']) ? htmlspecialchars($_POST['referredBy']) : "";
    $customersAuditor = isset($_POST['auditor']) ? htmlspecialchars($_POST['auditor']) : "";
    $customerActiveStatus = isset($_POST["customerStatus"]) ? htmlspecialchars($_POST["customerStatus"]) : "";

    // auto generating fields
    $updatedBy = $_SESSION['user']['id'];


    $validationFlag = true;



    // Make sure all the mandatory fields are filled
    if (empty($customerUniqCode) || empty($customerCompanyName) || empty($customerPhone) || empty($customerEmail) || 
        empty($customerPincode) || empty($customerCity) || empty($customerServiceType)) {

        $validationFlag = false;
        $valid["message"] = "Mandatory";
    } 

    if($validationFlag) {
        
        $db = new sqlHelper();

        $query = "
            UPDATE cust_mstr SET 
                customer_nm = ?, company_nm = ?, address_ln = ?, contact = ?, updated_by = ?, 
                amc_st_date = ?, amc_end_date = ?, spl_cust_note = ?, license_typ = ?, 
                email = ?, sys_email = ?, pincode = ?, city = ?, area = ?, 
                service_type = ?, is_active = ?, customer_uniq_code = ?, telephone = ?,
                referredBy = ?, auditor = ?, tally_st_date = ?, tally_end_date = ?,
                cloud_st_date = ?, cloud_end_date = ?
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
                $customerCloudStartDate, $customerCloudEndDate,
                $cId
            ];
            $types = 'sssssssssssssssissssssssi'; 
            $db->setParameters($params, $types);

            // Execute the statement
            $db->execPreparedStatement();
            $valid['success'] = true;
            $valid["message"] = 'Customer updated successfully!';
            $valid["detailed"] = "";

        } catch (Exception $e) {
            $valid['success'] = false;
            $valid["message"] = 'Error in updating Customer details';
            $valid["detailed"] = $e->getMessage();
        }

    } 
    
    echo json_encode($valid);
}

?>