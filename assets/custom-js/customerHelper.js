$(document).ready(function() {

    $('#customerMasterTbl').DataTable( {
        type: "Post",
        ajax: './services/customer_fetch_all.php',
    } );

    $('.addCustomerForm').on('submit', function(e) {
        e.preventDefault();
        var data = $(this).serialize();
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
            }
        });
    });

    

});

