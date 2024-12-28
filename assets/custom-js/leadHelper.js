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
          $("#addLeadModal").modal("hide");
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
          manageLeadDatatable.ajax.reload(null, true);
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

          $("#viewLeadForm #leadNm").val(response.data[0].lead_name).attr("readonly", true);
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

function editLead(leadId = null) {
  if (leadId) {
    $.ajax({
      type: "POST",
      url: "./services/lead_fetch_single.php",
      data: { leadId: leadId },
      dataType: "json",
      success: function (response) {
        if (response.success === true) {
          const lead = response.data[0];

          // Populate modal fields
          $("#currentLeadCode").text(lead.lead_name);
          $("#editLeadForm #leadNm").val(lead.lead_name);
          $("#editLeadForm #email").val(lead.email);
          $("#editLeadForm #companyNm").val(lead.company_name);
          $("#editLeadForm #contact").val(lead.contact);
          $("#editLeadForm #requirement").val(lead.requirement);
          $("#editLeadForm #description").val(lead.description);
          $("#editLeadForm #notes").val(lead.notes);
          $("#editLeadForm #addressLn").val(lead.address_line);
          $("#editLeadForm #area").val(lead.area);
          $("#editLeadForm #city").val(lead.city);
          $("#editLeadForm #pincode").val(lead.pincode);
          $("#editLeadForm #followUpDt").val(lead.follow_up_date);
          $("#editLeadForm #leadStatus").val(lead.lead_status);
          $("#editLeadForm").append('<input type="hidden" name="lId" id="lId" value="'+ lead.id +'" />');


          // Show the modal
          $("#editLeadModal").modal("show");

          // Handle edit form submission
          $("#editLeadDataBtn")
            .unbind("click")
            .bind("click", function (e) {
              console.log("hello");

              e.preventDefault();

              const formData = $("#editLeadForm").serialize();


              $.ajax({
                type: "POST",
                url: "./services/lead_edit.php",
                data: formData,
                dataType: "json",
                success: function (response) {
                  if (response.success === true) {
                    $("#editLeadForm")[0].reset();
                    $("#editLeadModal").modal("hide");
                    // Reload lead data table
                    manageLeadDatatable.ajax.reload(null, true);
                  } else {
                    alert("Failed to update lead details.");
                  }
                },
                error: function () {
                  alert("Error updating lead details.");
                },
              });
            });
        } else {
          alert("Failed to fetch lead details.");
        }
      },
      error: function () {
        alert("Error fetching lead details.");
      },
    });
  }
}
