// Add these global variable declarations at the very top of your unassignedemail.js file:
var manageLeadDatatable, currentLead;
var agents = [];
var selectedLeads = [];

$(document).ready(function() {
    // Initialize DataTable
    manageLeadDatatable = $('#leadMasterTbl').DataTable({ // Changed from leadMasterTbl to manageLeadDatatable
        dom: 'Bfrtip',
        buttons: [
      {
        text: 'Act on selected data',
        action: function () {
          selectedLeads = [];
          manageLeadDatatable.rows({ selected: true }).every(function() {
            let rowData = this.data();
            let temp = [];
            temp.push(Number(rowData[5].match(/viewLead\((\d+)\)/)[1]));
            temp.push(rowData[0]);
            selectedLeads.push(temp);
          });
          openMultiActionModal();
        }
      }
    ],
        select: {
            style: 'multi',
            selector: 'td.select-checkbox'
        },
        responsive: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: "./services/unassigned_email_fetch.php", // Update this path
            type: 'POST',
            data: function(d) {
                d.param = ''; // You can pass parameters if needed
            },
            dataSrc: function(json) {
                if (json.success) {
                    return json.data;
                } else {
                    console.error(json.message);
                    return [];
                }
            }
        },
        columns: [
            { 
                data: null,
                defaultContent: '',
                className: 'select-checkbox',
                orderable: false
            },
            { data: 0 }, // Email
            { data: 1 }, // Contact
            { data: 2 }, // Company Name
            { data: 3 }, // Status
            { data: 4 }, // Follow up
            { data: 5 }  // Actions
            
        ],
        order: [[1, 'asc']]
    });

  // Handle "Select All" checkbox functionality
  $('#selectAllCheckbox').on('click', function() {
    var isChecked = $(this).is(':checked');
    
    if (isChecked) {
      // Select all rows
      manageLeadDatatable.rows({ page: 'current' }).select();
    } else {
      // Deselect all rows
      manageLeadDatatable.rows().deselect();
    }
  });

  // Update "Select All" checkbox state when individual rows are selected/deselected
  manageLeadDatatable.on('select deselect', function() {
    var totalRows = manageLeadDatatable.rows({ page: 'current' }).count();
    var selectedRows = manageLeadDatatable.rows({ selected: true, page: 'current' }).count();
    
    // Update the select all checkbox state
    if (selectedRows === 0) {
      $('#selectAllCheckbox').prop('indeterminate', false);
      $('#selectAllCheckbox').prop('checked', false);
    } else if (selectedRows === totalRows) {
      $('#selectAllCheckbox').prop('indeterminate', false);
      $('#selectAllCheckbox').prop('checked', true);
    } else {
      $('#selectAllCheckbox').prop('indeterminate', true);
    }
  });

  // Reset select all checkbox when table is redrawn (pagination, search, etc.)
  manageLeadDatatable.on('draw', function() {
    $('#selectAllCheckbox').prop('checked', false);
    $('#selectAllCheckbox').prop('indeterminate', false);
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

    console.log("selectedLeads: ", selectedLeads);

    e.preventDefault(); // Prevent the default form submission

    // Serialize the form data using the form's ID or class
    var data = $("#addLeadForm").serialize(); // Serialize the form data

    // Send AJAX request
    $.ajax({
      type: "POST",
      url: "./services/lead_email_add.php",
      data: data, // Send the form data
      dataType: "json", // Expect JSON response
      success: function (response) {
        console.log(response);
        if (response["success"] == true) {
          $("#addLeadForm")[0].reset();
          $("#addLeadModal").modal("hide");
          manageLeadDatatable.ajax.reload(null, true);
        } else {
          let errorMessage = "";
          switch (response.message) {            
            case "Mandatory":
              errorMessage = "Please make sure you filled all the mandatory fields...!"
              break;
            case "Duplicate":
              errorMessage = "This Email Lead Entry is repeated... Please check the details once again"
              break;
            case "Exception":
              errorMessage = "An error occured while creating a new Email lead. Please contact system admin...!"
              break;
            case "Invalid Request":
              errorMessage = "Invalid request...!"
              break;
            default:
              errorMessage = "Failed to create Email Lead. Please contact system admin!"
              break;
          }
          alert(errorMessage);
        }
      },
    });
  });
});

function getUserNameById(userId) {
  if (userId == 0 || userId === "0") {
    return "Admin";
  }
  if (userId && agents[Number(userId)]) {
    return agents[Number(userId)];
  }
  return "Unknown User";
}

// Helper function specifically for assignee (shows empty if unassigned)
function getAssigneeNameById(userId) {
  if (!userId || userId == 0 || userId === "0" || userId === "") {
    return "Unassigned";
  }
  if (agents[Number(userId)]) {
    return agents[Number(userId)];
  }
  return "Unknown User";
}

function message(message) {
  let el = document.querySelector('#events');
  let div = document.createElement('div');

  div.textContent = message;
  el.prepend(div);
}

function removeLead(params = null) {
  // console.log("params: ", params);
  if (params) {
    $.ajax({
      type: "POST",
      url: "./services/lead_email_remove.php",
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
      url: "./services/lead_email_fetch_single.php",
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
          $("#viewLeadForm #followUpDt").val(response.data[0].follow_up_date).attr("readonly", true);
          
          // Use appropriate helper functions
          $("#viewLeadForm #createdBy").val(getUserNameById(response.data[0].created_by)).attr("readonly", true);
          $("#viewLeadForm #updatedBy").val(getUserNameById(response.data[0].updated_by)).attr("readonly", true);
          $("#viewLeadForm #assignee").val(getAssigneeNameById(response.data[0].assignee)).attr("readonly", true);

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
      url: "./services/lead_email_fetch_single.php",
      data: { leadId: leadId },
      dataType: "json",
      success: function (response) {
        if (response.success === true) {
          const lead = response.data[0];
          currentLead = lead;
          
          // Populate modal fields
          $("#currentEditLeadCode").text(lead.lead_name);
          $("#editLeadForm #leadNm").val(lead.lead_name);
          $("#editLeadForm #email").val(lead.email).attr("readonly", true);
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

          // Populate assignee dropdown
          $("#editLeadForm #assignee").empty();
          $("#editLeadForm #assignee").append('<option value="">Unassigned</option>');
          $.each(agents, function(id, name) {
            if (id != 0) { // Skip admin (id=0)
              const selected = (id == lead.assignee) ? 'selected' : '';
              $("#editLeadForm #assignee").append(`<option value="${id}" ${selected}>${name}</option>`);
            }
          });

          // Remove any existing hidden fields to avoid duplicates
          $("#editLeadForm input[name='lId']").remove();
          // Add hidden field for lead ID
          $("#editLeadForm").append('<input type="hidden" name="lId" id="lId" value="'+ lead.id +'" />');

          // Show the modal
          $("#editLeadModal").modal("show");

          // Handle edit form submission
          $("#editLeadDataBtn").off("click").on("click", function(e) {
            e.preventDefault();
            
            const formData = $("#editLeadForm").serialize();
            
            $.ajax({
              type: "POST",
              url: "./services/lead_email_edit.php",
              data: formData,
              dataType: "json",
              success: function(response) {
                if (response.success === true) {
                  $("#editLeadForm")[0].reset();
                  $("#editLeadModal").modal("hide");
                  manageLeadDatatable.ajax.reload(null, true);
                } else {
                  alert("Failed to update lead: " + (response.message || "Unknown error"));
                }
              },
              error: function(xhr, status, error) {
                alert("Error updating lead: " + error);
              }
            });
          });
        } else {
          alert("Failed to fetch lead details: " + (response.message || "Unknown error"));
        }
      },
      error: function(xhr, status, error) {
        alert("Error fetching lead details: " + error);
      }
    });
  }
}

function openMultiActionModal() {
  // reset defaults or preloaded content
  $(".selectedLeadCount").empty();
  $("#multiActionLeadForm")[0].reset();

  // adding the selected no. of leads into the modal
  var selectedEmails = selectedLeads.map(function(innerArray) {
    return innerArray[1]; // Get the element at index 1 of each nested array
  });
  
  let dispHtml = "You have selected " + selectedLeads.length + " record, the emails are: <br>";
  for (let i = 0; i < selectedLeads.length; i++) {
    dispHtml += "<strong>" + selectedEmails[i] + " </strong><br> ";
  }
  $(".selectedLeadCount").append(dispHtml);
  
  // Show the modal
  $("#multiActionLeadModal").modal("show");
  
  // Remove existing event handlers to prevent multiple bindings
  $("#multiActionDataBtn").off("click").on("click", function(e) {
    e.preventDefault();
    
    // Get form values
    const formData = {
      followUpDt: $("#multiActionLeadForm #followUpDt").val(),
      leadStatus: $("#multiActionLeadForm #leadStatus").val(),
      leads: selectedLeads
    };
    
    // Remove empty/null values
    const cleanData = {};
    for (const key in formData) {
      if (formData[key] !== null && formData[key] !== '' && formData[key] !== undefined) {
        cleanData[key] = formData[key];
      }
    }
    
    $.ajax({
      type: "POST",
      url: "./services/lead_email_multi_action.php",
      data: cleanData,
      dataType: "json",
      success: function(response) {
        if (response.success) {
          manageLeadDatatable.ajax.reload(null, true);
        } else {
          alert("Error: " + response.message);
        }
        $("#multiActionLeadModal").modal("hide");
      }
    });
  });
}