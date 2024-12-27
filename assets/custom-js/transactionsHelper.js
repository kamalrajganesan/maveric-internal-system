
var manageTicketDataTbl;

$(document).ready(function () {
  manageTicketDataTbl = $("#transactionMasterTbl").DataTable({
    method: "POST",
    ajax: {
      url: "./services/transaction_fetch_all.php",
      type: "POST",
      data: {
        param: "new",
      },
      dataType: "json",
    },
  });

  // Handle form submission
  $("#addTransactionDataBtn").on("click", function (e) {
    e.preventDefault(); // Prevent the default form submission

    // Serialize the form data using the form's ID or class
    var data = $("#addTransactionForm").serialize(); // Serialize the form data

    // Send AJAX request
    $.ajax({
      type: "POST",
      url: "./services/transaction_add.php",
      data: data, // Send the form data
      dataType: "json", // Expect JSON response
      success: function (response) {
        console.log(response);
        if (response["success"] == true) {
          $("#addTransactionForm")[0].reset();
          $('#addTransactionForm').modal('hide');
          manageCustDataTbl.ajax.reload(null, true);
          
        } else {
          alert("Failed to Add transaction");
        }
      },
    });
  });

});

function removeTicket(params = null) {
  console.log("params: ", params);
  if (params) {
    $.ajax({
      type: "POST",
      url: "./services/transaction_remove.php",
      data: { ticketId: params },
      dataType: "json",
      success: function (response) {
        if (response.success == true) {
          manageTicketDataTbl.ajax.reload(null, false);
        } else {
          alert("Failed to Remove Ticket...!");
        }
      },
      error: function () {
        alert("Failed to Remove Ticket");
      },
    });
  }
}