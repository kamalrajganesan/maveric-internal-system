var manageLeadDatatable, currentLead;
var agents = [];

$(document).ready(function () {
  manageLeadDatatable = $("#leadMasterTbl").DataTable({
    method: "POST",
    scrollX: true,
    ajax: {
      url: "./services/lead_call_fetch_all.php",
      dataType: "json",
    },
  });

  // Send AJAX request to get all Agents
  $.ajax({
    type: "GET",
    url: "./services/agent_fetch_few_details.php",
    dataType: "json", // Expect JSON response
    success: function (response) {
      // console.log(response);
      if (response["success"] == true) {
        response["data"].forEach((agent) => {
          agents[Number(agent.id)] = agent.name;
        })
      } else {
        let errorMessage = "";
        switch (response.message) {            
          case "Exception":
            errorMessage = "An error occured while fetching Agents. Please contact system admin...!"
            break;
          case "Invalid Request":
            errorMessage = "Invalid request...!"
            break;
          default:
            errorMessage = "Failed to get agents. Please contact system admin!"
            break;
        }
        alert(errorMessage);
      }
    },
    error: function (xhr, status, error) {
      // Handle any errors that occurred during the request
      console.log(error);
    }
  });

  // Handle form submission
  $("#addLeadDataBtn").on("click", function (e) {
    e.preventDefault(); // Prevent the default form submission

    // Serialize the form data using the form's ID or class
    var data = $("#addLeadForm").serialize(); // Serialize the form data

    // Send AJAX request
    $.ajax({
      type: "POST",
      url: "./services/lead_call_add.php",
      data: data, // Send the form data
      dataType: "json", // Expect JSON response
      success: function (response) {
        // console.log(response);
        if (response["success"] == true) {
          manageLeadDatatable.ajax.reload(null, true);
          $("#addLeadModal").modal("hide");
          $("#addLeadForm")[0].reset();
        } else {
          let errorMessage = "";
          switch (response.message) {            
            case "Mandatory":
              errorMessage = "Please make sure you filled all the mandatory fields...!"
              break;
            case "Duplicate":
              errorMessage = "This Call Lead Entry is repeated... Please check the details once again"
              break;
            case "Exception":
              errorMessage = "An error occured while creating a new Call Lead. Please contact system admin...!"
              break;
            case "Invalid Request":
              errorMessage = "Invalid request...!"
              break;
            default:
              errorMessage = "Failed to create Call Lead. Please contact system admin!"
              break;
          }
          alert(errorMessage);
        }
      },
    });
  });
});

function removeLead(params = null) {
  // console.log("params: ", params);
  if (params) {
    $.ajax({
      type: "POST",
      url: "./services/lead_call_remove.php",
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
      url: "./services/lead_call_fetch_single.php",
      data: { leadId: params },
      dataType: "json",
      success: function (response) {
        if (response.success == true) {

          currentLead = response.data[0];

          $("#viewLeadModal").modal("show");
          $("#currentLeadCode").text(response.data[0].lead_name);

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

          let commentsHtml = '';
          let comments = '['+response.data[0].log+']';
          JSON.parse(comments).forEach(function(comment) {
            commentsHtml += '<li class="d-block">'
            commentsHtml += '<div class="form-check w-100">'
            commentsHtml += '<label class="form-check-label m-0">'
            commentsHtml += comment.message +' <i class="input-helper rounded"></i></label>'
            switch (comment.status) {
              case "Newly Added":
                commentsHtml += '<div class="d-flex mt-2"><div class="badge badge-opacity-info me-3"> '+ comment.status +' </div>'
                break;
              case "Contacted":
                commentsHtml += '<div class="d-flex mt-2"><div class="badge badge-opacity-purple me-3"> '+ comment.status +' </div>'
                break;
              case "Converted":
                commentsHtml += '<div class="d-flex mt-2"><div class="badge badge-opacity-success me-3"> '+ comment.status +' </div>'
                break;
              case "Following":
                commentsHtml += '<div class="d-flex mt-2"><div class="badge badge-opacity-warning me-3"> '+ comment.status +' </div>'
                break;
              case "Lost":
                commentsHtml += '<div class="d-flex mt-2"><div class="badge badge-opacity-danger me-3"> '+ comment.status +' </div>'
                break;
              default:
                commentsHtml += '<div class="d-flex mt-2"><div class="badge badge-opacity-light me-3"> '+ comment.status +' </div>'
                break;
            }
            commentsHtml += '<div class="text-small me-3"> On <strong>'+ formatDate(comment.date) +'</strong></div>'
            commentsHtml += '<div class="text-small me-3"> By <strong>'+ comment.commentBy +'</strong></div></div></div></li>'
          });
          $("#viewLeadForm #pastCommentsOfThisLead").html(commentsHtml);


          $("#viewLeadForm #createdBy").val(response.data[0].created_by).attr("readonly", true);
          $("#viewLeadForm #updatedBy").val(response.data[0].updated_by).attr("readonly", true);
          $("#viewLeadForm #assignee").val(agents[response.data[0].assignee]).attr("readonly", true);

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
      url: "./services/lead_call_fetch_single.php",
      data: { leadId: leadId },
      dataType: "json",
      success: function (response) {
        if (response.success === true) {
          
          const lead = response.data[0];
          currentLead = lead;
          $("#currentLeadCode").text(response.data[0].lead_name);

          // Populate modal fields
          $("#currentEditLeadCode").text(lead.lead_name);
          $("#editLeadForm #leadNm").val(lead.lead_name);
          $("#editLeadForm #email").val(lead.email);
          $("#editLeadForm #companyNm").val(lead.company_name);
          $("#editLeadForm #contact").val(lead.contact).attr("readonly", true);;
          $("#editLeadForm #requirement").val(lead.requirement);
          $("#editLeadForm #description").val(lead.description);
          $("#editLeadForm #notes").val(lead.notes);
          $("#editLeadForm #addressLn").val(lead.address_line);
          $("#editLeadForm #area").val(lead.area);
          $("#editLeadForm #city").val(lead.city);
          $("#editLeadForm #pincode").val(lead.pincode);
          $("#editLeadForm #followUpDt").val(lead.follow_up_date);
          $("#editLeadForm #leadStatus").val(lead.lead_status);
          $("#editLeadForm #assignee").val(agents[response.data[0].assignee]).attr("readonly", true);

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
                url: "./services/lead_call_edit.php",
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
