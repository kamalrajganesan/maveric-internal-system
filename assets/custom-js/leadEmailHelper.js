var manageLeadDatatable, currentLead;
var selectedLeads = [];

$(document).ready(function () {
  manageLeadDatatable = $("#leadMasterTbl").DataTable({
    method: "POST",
    scrollX: true,
    ajax: {
      url: "./services/lead_email_fetch_all.php",
      dataType: "json",
    },
    layout: {
      top1Start: {
        buttons: [
          {
            text: 'Act on selected data',
            action: function () {
              
              selectedLeads = [];
              manageLeadDatatable.rows({ selected: true }).data().toArray().forEach(element => {
                selectedLeads.push(Number(element[8].match(/viewLead\((\d+)\)/)[1]));
              });;

              console.log(selectedLeads);
              openMultiActionModal();
            }
          }
        ]
      }
    },
    dom: null,
    columns: [
      { data: null, orderable: false, searchable: false, render: DataTable.render.select() },
      { data: 0 },
      { data: 1 },
      { data: 2 },
      { data: 3 },
      { data: 4 },
      { data: 5 },
      { data: 6 },
      { data: 7 },
      { data: 8 }
    ],
    select: true
  });

  // Handle form submission
  $("#addLeadDataBtn").on("click", function (e) {
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
          alert("Failed to Add lead");
        }
      },
    });
  });
});

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
      url: "./services/lead_email_fetch_single.php",
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
                url: "./services/lead_email_edit.php",
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

function openMultiActionModal() {
  
  // reset defaults or preloaded content
  $(".selectedLeadCount").empty();
  $("#multiActionLeadForm")[0].reset();

  // adding the selected no. of leads into the modal
  $(".selectedLeadCount").append("Totally, " + selectedLeads.length + " lead(s) will be affected...!");
  
  // Show the modal
  $("#multiActionLeadModal").modal("show");
  
  // Handle lead's multi-action form submission
  $("#multiActionDataBtn").on("click", function (e) {

    e.preventDefault();

    // Send AJAX request
    $.ajax({
      type: "POST",
      url: "./services/lead_email_multi_action.php",
      data: {
        followUpDt: $("#multiActionLeadForm #followUpDt").val(),
        leadStatus: $("#multiActionLeadForm #leadStatus").val(),
        leads: selectedLeads
      }, // Send the form data
      dataType: "json",
      success: function (response) {
        
        if (response.success == true) {
          manageLeadDatatable.ajax.reload(null, true); // Reload lead data table
        } else {
          alert("Failed to update lead details.");
        }
        
        $("#multiActionLeadModal").modal("hide");
      },
      error: function () {
        
        alert("Error updating lead details.");
        $("#multiActionLeadModal").modal("hide");
      },
    });
  });
}