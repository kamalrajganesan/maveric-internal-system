var manageLeadDatatable;
$(document).ready(function () {
  manageLeadDatatable = $("#leadMasterTbl").DataTable({
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
        console.log(response);
        if (response["success"] == true) {
          $("#addLeadForm")[0].reset();
          $("#addLeadForm").modal("hide");
          manageLeadDatatable.ajax.reload(null, true);
          alert("Lead Added Successfully");
        } else {
          alert("Failed to Add lead");
        }
      },
    });
  });
});


function removeLead(params = null) {
  console.log("params: ", params);
  if (params) {
    $.ajax({
      type: "POST",
      url: "./services/lead_remove.php",
      data: { leadId: params },
      dataType: "json",
      success: function (response) {
        if (response.success == true) {
          manageLeadDatatable.ajax.reload(null, false);
        } else {
          alert("Failed to Remove Lead...!");
        }
      },
      error: function () {
        alert("Failed to Remove Lead");
      },
    });
  }
}
