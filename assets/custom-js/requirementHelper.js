var manageRequirementDatatable, currentRequirement;

$(document).ready(function () {
  manageRequirementDatatable = $("#requirementTbl").DataTable({
    method: "POST",
    ajax: {
      url: "./services/requirement_fetch_all.php",
      type: "POST",
      data: {
        param: requirement_page,
      },
      dataType: "json",
    },
  });

  // Handle form submission
  $("#addRequirementDataBtn").on("click", function (e) {
    e.preventDefault(); // Prevent the default form submission

    // Serialize the form data using the form's ID or class
    var data = $("#addRequirementForm").serialize(); // Serialize the form data

    // Send AJAX request
    $.ajax({
      type: "POST",
      url: "./services/requirement_add.php",
      data: data, // Send the form data
      dataType: "json", // Expect JSON response
      success: function (response) {
        console.log(response);
        if (response["success"] == true) {
          $("#addRequirementForm")[0].reset();
          $("#addRequirementModal").modal("hide");
          manageRequirementDatatable.ajax.reload(null, true);
        } else {
          alert("Failed to Add requirement");
        }
      },
    });
  });
});

function removeRequirement(params = null) {
  // console.log("params: ", params);
  if (params) {
    $.ajax({
      type: "POST",
      url: "./services/requirement_remove.php",
      data: { requirementId: params },
      dataType: "json",
      success: function (response) {
        if (response.success == true) {
          manageRequirementDatatable.ajax.reload(null, true);
        } else {
          alert("Failed to Remove Requirement...!");
        }
      },
      error: function () {
        alert("Failed to Remove Requirement");
      },
    });
  }
}

function viewRequirement(params = null) {
  if (params) {
    $.ajax({
      type: "POST",
      url: "./services/requirement_fetch_single.php",
      data: { requirementId: params },
      dataType: "json",
      success: function (response) {
        if (response.success == true) {

          currentRequirement = response.data[0];

          $("#viewRequirementModal").modal("show");
          $("#currentRequirementCode").text(response.data[0].nm);

          $("#viewRequirementForm #nm").val(response.data[0].nm).attr("readonly", true);
          $("#viewRequirementForm #brief").val(response.data[0].brief).attr("readonly", true);
          $("#viewRequirementForm #detailed").val(response.data[0].detailed).attr("readonly", true);
          $("#viewRequirementForm #cust_id").val(response.data[0].cust_id).attr("readonly", true);
          $("#viewRequirementForm #phone").val(response.data[0].phone).attr("readonly", true);
          $("#viewRequirementForm #requirementStatus").val(response.data[0].requirement_status).attr("readonly", true);
          $("#viewRequirementForm #updated_on").val(response.data[0].updated_on).attr("readonly", true);
          $("#viewRequirementForm #updated_by").val(response.data[0].updated_by).attr("readonly", true);
          $("#viewRequirementForm #created_on").val(response.data[0].created_on).attr("readonly", true);
          $("#viewRequirementForm #created_by").val(response.data[0].created_by).attr("readonly", true);
        } else {
          alert("Failed to Fetch Requirement...!");
        }
      },
      error: function () {
        alert("Failed to Fetch Requirement");
      },
    });
  }
}

function editRequirement(requirementId = null) {
  if (requirementId) {
    $.ajax({
      type: "POST",
      url: "./services/requirement_fetch_single.php",
      data: { requirementId: requirementId },
      dataType: "json",
      success: function (response) {
        if (response.success === true) {
          
          const requirement = response.data[0];
          currentRequirement = requirement;
          $("#currentRequirementCode").text(response.data[0].nm);

          // Populate modal fields
          $("#currentEditRequirementCode").text(requirement.nm);
          $("#editRequirementForm #nm").val(requirement.nm);
          $("#editRequirementForm #brief").val(requirement.brief);
          $("#editRequirementForm #detailed").val(requirement.detailed);
          $("#editRequirementForm #cust_id").val(requirement.cust_id);
          $("#editRequirementForm #phone").val(requirement.phone);
          $("#editRequirementForm #requirementStatus").val(requirement.requirement_status);
          $("#editRequirementForm").append('<input type="hidden" name="rId" id="rId" value="'+ requirement.id +'" />');

          // Show the modal
          $("#editRequirementModal").modal("show");

                    // Handle edit form submission
                    $("#editRequirementDataBtn")
                    .unbind("click")
                    .bind("click", function (e) {
                      console.log("hello");
        
                      e.preventDefault();
        
                      const formData = $("#editRequirementForm").serialize();
        
                      $.ajax({
                        type: "POST",
                        url: "./services/requirement_edit.php",
                        data: formData,
                        dataType: "json",
                        success: function (response) {
                          if (response.success === true) {
                            $("#editRequirementForm")[0].reset();
                            $("#editRequirementModal").modal("hide");
                            // Reload requirement data table
                            manageRequirementDatatable.ajax.reload(null, true);
                          } else {
                            alert("Failed to update requirement details.");
                          }
                        },
                        error: function () {
                          alert("Error updating requirement details.");
                        },
                      });
                    });
                } else {
                  alert("Failed to fetch requirement details.");
                }
              },
              error: function () {
                alert("Error fetching requirement details.");
              },
            });
          }
}