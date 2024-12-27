$(document).ready(function () {
    $("#transactionMasterTbl").DataTable({
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
          console.log(response)
          if (response["success"] == true) {
            alert("Lead Added Successfully");
            location.reload(); // Reload the page to reflect the new data
          } else {
            alert("Failed to Add transaction");
          }
        },
      });
    });
  });
  