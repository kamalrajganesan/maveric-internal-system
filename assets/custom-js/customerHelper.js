$(document).ready(function() {
    $('#customerMasterTbl').DataTable( {
        type: "Post",
        ajax: './services/customer_fetch_all.php',
    } );
});