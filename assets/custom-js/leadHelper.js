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

function viewLead(params = null) {
  console.log("params: ", params);
  console.log($("#viewLeadForm #leadNm"));
  console.log($("#leadNm"));

  if (params) {
    $.ajax({
      type: "POST",
      url: "./services/lead_fetch_single.php",
      data: { leadId: params },
      dataType: "json",
      success: function (response) {
        if (response.success == true) {
          console.log("response: ", response);
          console.log("response: ", response.data[0].lead_name);

          $("#viewLeadModal").modal("show");

          $("#viewLeadForm #leadNm").val(response.data[0].lead_name);
          $("#viewLeadForm #email").val(response.data[0].email).attr("readonly", true);
          $("#viewLeadForm #companyNm").val(response.data[0].company_name).attr("readonly", true);
          $("#viewLeadForm #contact").val(response.data[0].contact).attr("readonly", true);
          $("#viewLeadForm #requirement").val(response.data[0].requirement).attr("readonly", true);
          $("#viewLeadForm #description").val(response.data[0].description).attr("readonly", true);
          $("#viewLeadForm #notes").val(response.data[0].notes).attr("readonly", true);
          $("#viewLeadForm #addressLn").val(response.data[0].address_line).attr("readonly", true);
          $("#viewLeadForm #area").val(response.data[0].area).attr("readonly", true);
          $("#viewLeadForm #city").val(response.data[0].city).attr("readonly", true);
          $("#viewLeadForm #pincode").val(response.data[0].pincode).attr("readonly", true);
          $("#viewLeadForm #pincode").val(response.data[0].pincode).attr("readonly", true);
          $("#viewLeadForm #followUpDt").val(response.data[0].follow_up_date).attr("readonly", true);
          $("#viewLeadForm #createdBy").val(response.data[0].created_by).attr("readonly", true);


          $("#viewLeadForm #leadStatus").val(response.data[0].lead_status).attr("disabled", true);

        } else {
          alert("Failed to Fetch Lead...!");
        }
      },
      error: function () {
        alert("Failed to Fetch Lead");
      },
    });
  }
}
