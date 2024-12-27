<?php

require_once("../shared/actions/db/dao.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if(!session_id()) {
        session_start();
    }

    // Create connection
    $db = new sqlHelper();

    $customerName = $_POST['customerName'];
    $customerCompanyName = $_POST['companyName'];
    $customerPhone = $_POST['contact'];
    $customerEmail = $_POST['email'];
    $customerAddress = $_POST['address'];
    $customerArea = $_POST['area'];
    $customerPincode = $_POST['pincode'];
    $customerServiceType = $_POST['serviceType'];
    $customerCity = $_POST['city'];
    $customerSpecialNote = $_POST['specialNote'];
    $customerLicenseType = $_POST['licenseType'];
    $customerSystemEmail = $_POST['systemEmail'];
    $customerUniqCode = $_POST['customerUniqCode'];
    $customerServiceStartDate = $_POST['serviceStartDate'];

    $createdBy = $_SESSION['user']['id'];
    $updatedBy = $_SESSION['user']['id'];
    $createdOn = date('Y-m-d H:i:s');

    $sql = `
        INSERT INTO 
         cust_mstr(
            customer_nm, 
            company_nm, 
            address_ln, 
            contact, 
            updated_by, 
            created_by, 
            service_st_date, // service start date
            spl_cust_note, 
            license_typ, 
            email, 
            sys_email, 
            pincode, // pincode
            city, 
            area, 
            service_type, 
            is_active, 
            customer_uniq_code, 
            is_deleted
         )  
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1,?,0)
    `;

    $db->prepareStatement($sql);
    $db->setParameters([
        $customerName,
        $customerCompanyName,
        $customerAddress,
        $customerPhone,
        $updatedBy,
        $createdBy,
        $customerServiceStartDate,
        $customerSpecialNote,
        $customerLicenseType,
        $customerEmail,
        $customerSystemEmail,
        $customerPincode,
        $customerCity,
        $customerArea,
        $customerServiceType,
        $customerUniqCode,
    ], 'sssssssssssissss');
    $db->execPreparedStatement();
    $resultSet = $db->getResultSet();


    $conn->close();
}
?>