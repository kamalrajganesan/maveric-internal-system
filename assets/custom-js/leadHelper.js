$(document).ready(function () {
  $("#leadMasterTbl").DataTable({
    method: "POST",
    ajax: {
      url: "./services/lead_fetch_all.php",
      type: "POST",
      data: {
        param: lead_page,
      },
      dataType: "json",
    },
  });

  // Handle form submission
  $("#addLeadDataBtn").on("click", function (e) {
    e.preventDefault(); // Prevent the default form submission

    // Serialize the form data using the form's ID or class
    var data = $("#addLeadForm").serialize(); // Serialize the form data

    // Send AJAX request
    $.ajax({
      type: "POST",
      url: "./services/lead_add.php",
      data: data, // Send the form data
      dataType: "json", // Expect JSON response
      success: function (response) {
        console.log(response)
        if (response["success"] == true) {
          alert("Lead Added Successfully");
          location.reload(); // Reload the page to reflect the new data
        } else {
          alert("Failed to Add lead");
        }
      },
    });
  });
});
