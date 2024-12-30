<?php

require_once("../shared/php/connect.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if(!session_id()) {
        session_start();
    }

    $cId = $_POST['cId']; 
    $customerName = $_POST['customerName'];
    $customerCompanyName = $_POST['companyName'];
    $customerPhone = $_POST['contact'];
    $customerEmail = $_POST['email'];
    $customerAddress = $_POST['address'];
    $customerArea = $_POST['area'];
    $customerPincode = $_POST['pincode'];
    // $customerServiceType = implode(',', $_POST['serviceType[]']);
    $customerCity = $_POST['city'];
    $customerSpecialNote = $_POST['specialNote'];
    $customerLicenseType = $_POST['licenseType'];
    $customerSystemEmail = $_POST['systemEmail'];
    $customerServiceStartDate = $_POST['serviceStartDate'];
    $customerServiceEndDate = $_POST['serviceEndDate'];

    $updatedBy = $_SESSION['user']['id'];


    $sql = "UPDATE cust_mstr SET 
                customer_nm = '". $customerName ."', 
                company_nm = '". $customerCompanyName ."', 
                address_ln = '". $customerAddress ."', 
                contact = '". $customerPhone ."', 
                updated_by = '". $updatedBy ."', 
                service_st_date = '". $customerServiceStartDate ."', 
                service_end_date = '". $customerServiceEndDate ."', 
                spl_cust_note = '". $customerSpecialNote ."', 
                license_typ = '". $customerLicenseType ."', 
                email = '". $customerEmail ."', 
                sys_email = '". $customerSystemEmail ."', 
                pincode = '". $customerPincode ."', 
                city = '". $customerCity ."', 
                area = '". $customerArea ."'
            WHERE id = ". $cId ."";

    $connect = createConn();
    if($connect->query($sql) === TRUE) {
        $valid['success'] = true;
        $valid['message'] = "Successfully updated customer";
    } else {
        $valid['success'] = false;
        $valid['message'] = "Error while updating the data";
    }

    $connect->close();
    echo json_encode($valid);

}
?>