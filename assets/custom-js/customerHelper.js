var manageCustDataTbl;

$(document).ready(function() {

    manageCustDataTbl = $('#customerMasterTbl').DataTable( {
        type: "Post",
        ajax: './services/customer_fetch_all.php',
    } );

    $('#addCustomerDataBtn').on('click', function(e) {

        e.preventDefault();        
        var data = $('#addCustomerForm').serialize();
        
        $.ajax({
            type: "POST",
            url: './services/customer_add.php',
            data: data,
            dataType: 'json',
            success: function(response) {
                if(response.success == true) {
                    $("#addCustomerForm")[0].reset();
                    $('#addCustomerModal').modal('hide');
                    manageCustDataTbl.ajax.reload(null, true);
                } else {
                    alert('Failed to Add Customer...!');
                }
            },
            error: function() {
                alert('Failed to Add Customer');
            }
        });
    });

});

function removeCustomer(params = null) {
    console.log("params: ", params);
    if(params) {
        $.ajax({
            type: "POST",
            url: './services/customer_remove.php',
            data: {customerId: params},
            dataType: 'json',
            success: function(response) {
                if(response.success == true) {
                    manageCustDataTbl.ajax.reload(null, false);
                } else {
                    alert('Failed to Remove Customer...!');
                }
            },
            error: function() {
                alert('Failed to Remove Customer');
            }
        });
    }
}

function viewCustomer(params = null) {
    console.log("params: ", params);
    if(params) {
        $.ajax({
            type: "POST",
            url: './services/customer_fetch_single.php',
            data: {customerId: params},
            dataType: 'json',
            success: function(response) {
                if(response.success == true) {
                    console.log("response: ", response);
                    $('#viewCustomerModal').modal('show');

                    $('#viewCustomerForm #customerName').val(response.data[0].customer_nm).attr('readonly', true);
                    $('#viewCustomerForm #companyName').val(response.data[0].company_nm).attr('readonly', true);
                    $('#viewCustomerForm #address').val(response.data[0].address_ln).attr('readonly', true);
                    $('#viewCustomerForm #contact').val(response.data[0].contact).attr('readonly', true);
                    $('#viewCustomerForm #email').val(response.data[0].email).attr('readonly', true);
                    $('#viewCustomerForm #systemEmail').val(response.data[0].sys_email).attr('readonly', true);
                    $('#viewCustomerForm #pincode').val(response.data[0].pincode).attr('readonly', true);
                    $('#viewCustomerForm #city').val(response.data[0].city).attr('readonly', true);
                    $('#viewCustomerForm #area').val(response.data[0].area).attr('readonly', true);
                    
                    $('#viewCustomerForm input[name="serviceType"][value="AMC"]').prop('checked', response.data[0].service_type == 'AMC').attr('disabled', true);
                    $('#viewCustomerForm input[name="serviceType"][value="Tally"]').prop('checked', response.data[0].service_type == 'Tally').attr('disabled', true);
                    $('#viewCustomerForm input[name="serviceType"][value="On Call"]').prop('checked', response.data[0].service_type == 'On Call').attr('disabled', true);
                    $('#viewCustomerForm input[name="serviceType"][value="One Time"]').prop('checked', response.data[0].service_type == 'One Time').attr('disabled', true);

                    $('#viewCustomerForm #customerStatus').val(response.data[0].is_active).attr('disabled', true);

                    $('#viewCustomerForm #licenseType').val(response.data[0].license_typ).attr('readonly', true);
                    $('#viewCustomerForm #serviceStartDate').val(response.data[0].service_st_date).attr('readonly', true);
                    $('#viewCustomerForm #specialNote').val(response.data[0].spl_cust_note).attr('readonly', true);
                    $('#viewCustomerForm #isActive').val(response.data[0].is_active).attr('readonly', true);
                    $('#viewCustomerForm #createdBy').val(response.data[0].created_by).attr('readonly', true);
                    $('#viewCustomerForm #createdOn').val(response.data[0].created_on).attr('readonly', true);
                    $('#viewCustomerForm #updatedBy').val(response.data[0].updated_by).attr('readonly', true);
                    $('#viewCustomerForm #customerUniqCode').val(response.data[0].customer_uniq_code).attr('readonly', true);

                    
                } else {
                    alert('Failed to Fetch Customer...!');
                }
            },
            error: function() {
                alert('Failed to Fetch Customer');
            }
        });
    }
}