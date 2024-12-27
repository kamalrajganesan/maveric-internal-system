$(document).ready(function() {

    $('#customerMasterTbl').DataTable( {
        type: "Post",
        ajax: './services/customer_fetch_all.php',
    } );

    $('#addCustomerDataBtn').on('click', function(e) {

        e.preventDefault();
        
        var data = $('#addCustomerForm').serialize();
        console.log('Form Submitted:', data);
        
        $.ajax({
            type: "POST",
            url: './services/customer_add.php',
            data: data,
            dataType: 'json',
            success: function(response) {
                if(response == 'success') {
                    alert('Customer Added Successfully');
                    location.reload();
                } else {
                    alert('Failed to Add Customer');
                }
            },
            error: function() {
                alert('Failed to Add Customer');
            }
        });
    });



});

