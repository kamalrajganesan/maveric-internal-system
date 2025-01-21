
var manageTicketDataTbl, currentTransaction;

$(document).ready(function () {
  manageTicketDataTbl = $("#transactionMasterTbl").DataTable({
    method: "POST",
    scrollX: true,
    order: [[0, 'desc']],    
    ajax: {
      url: "./services/transaction_fetch_all.php",
      type: "POST",
      data: {
        type: transaction_type,
        value: transaction_value
      },
      dataType: "json"
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
        if (response["success"] == true) {
          $("#addTransactionForm")[0].reset();
          $('#addTransactionModal').modal('hide');
          manageTicketDataTbl.ajax.reload(null, true);
        } else {
          let errorMessage = "";
          switch (response.message) {            
            case "Mandatory":
              errorMessage = "Please make sure you filled all the mandatory fields...!"
              break;
            case "Exception":
              errorMessage = "An error occured while creating a new transaction. Please contact system admin...!"
              break;
            case "Invalid Request":
              errorMessage = "Invalid request...!"
              break;
            default:
              errorMessage = "Failed to create Transaction. Please contact system admin!"
              break;
          }
          alert(errorMessage);
        }
      },
    });
  });

});

function removeTicket(params = null) {
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

function setCustomersDropdown(param) {
  // Fetch customer details for customerId field
  $.ajax({
    type: "GET",
    url: "./services/customer_fetch_few_details.php",
    dataType: "json",
    success: function (response) {
      console.log("setCustomersDropdown: ", response);
      console.log("setCustomersDropdown: ", currentTransaction);
      if (response.success == true) {
        var customerDropdown;
        if(param == 'add') {
          customerDropdown = $("#addTransactionForm #customerId");
        } else if(param == 'edit') {
          customerDropdown = $("#editTransactionForm #customerId");
        } else {
          customerDropdown = $("#viewTransactionForm #customerId");
        }
        customerDropdown.empty(); // Clear existing options
        customerDropdown.append(
          $("<option></option>")
            .attr("value", "")
            .text("Select A Customer")
        );
        $.each(response.data, function (key, customer) {
          customerDropdown.append(
            $("<option></option>")
              .attr("value", customer.id)
              .text(customer.name)
          );
        });
        if(param != 'add') {
          customerDropdown.val(currentTransaction.customer_id).attr('disabled', true);
        }
      } else {
        alert("Failed to fetch customer details");
      }
    },
    error: function () {
      alert("Error fetching customer details");
    },
  });
}
function setAgentsDropdowns(param) {
  // Fetch agent details for customerId field
  $.ajax({
    type: "GET",
    url: "./services/agent_fetch_few_details.php",
    dataType: "json",
    success: function (response) {
      if (response.success == true) {
        
        var agentDropdown1, agentDropdown2;
        agentDropdown1 = $("#viewTransactionForm #agentId");
        agentDropdown2 = $("#viewTransactionForm #aAgentId");
        
        agentDropdown1.empty(); // Clear existing options
        agentDropdown2.empty(); // Clear existing options
        $.each(response.data, function (key, agent) {
          agentDropdown1.append(
            $("<option></option>")
              .attr("value", agent.id)
              .text(agent.name)
          );
          agentDropdown2.append(
            $("<option></option>")
              .attr("value", agent.id)
              .text(agent.name)
          );
        });
        agentDropdown1.val(currentTransaction.updated_by).attr('disabled', true);
        agentDropdown2.val(currentTransaction.assignd_agent_id).attr('disabled', true);
      } else {
        alert("Failed to fetch agent details");
      }
    },
    error: function () {
      alert("Error fetching agent details");
    },
  });
}

function onClickAddTransaction() {
  $("#addTransactionForm")[0].reset();
  $("#addTransactionModal").modal("show");
  setCustomersDropdown('add');
}

function editTicket(params = null) {

  if (params) {
    $.ajax({
      type: "POST",
      url: "./services/transaction_fetch_single.php",
      data: { tId: params },
      dataType: "json",
      success: function (response) {
        if (response.success == true) {

          currentTransaction = response.data[0];

          setCustomersDropdown('edit');

          $("#currentEditTransactionCode").text(response.data[0].uniq_id);
          $("#editTransactionForm #problemStmt").val(response.data[0].problem_stmt);
          $("#editTransactionForm #problemDesc").val(response.data[0].problem_desc);
          
          let commentsHtml = '';
          let comments = '['+response.data[0].comments+']';
          JSON.parse(comments).forEach(function(comment) {
            commentsHtml += '<li class="d-block">'
            commentsHtml += '<div class="form-check w-100">'
            commentsHtml += '<label class="form-check-label m-0">'
            commentsHtml += comment.message +' <i class="input-helper rounded"></i></label>'
            commentsHtml += '<div class="d-flex mt-2"><div class="badge badge-opacity-warning me-3"> '+ comment.status +' </div>'
            commentsHtml += '<div class="text-small me-3"> On <strong>'+ comment.date +'</strong></div>'
            commentsHtml += '<div class="text-small me-3"> By <strong>'+ comment.commentBy +'</strong></div></div></div></li>'
          });
          $("#editTransactionForm #pastCommentsOfThisTransaction").html(commentsHtml);

          let notesHtml = '';
          let notes = '['+response.data[0].notes+']';
          JSON.parse(notes).forEach(function(notes) {
            if(notes.message == "") {
              return;
            }
            notesHtml += '<li class="d-block">'
            notesHtml += '<div class="form-check w-100">'
            notesHtml += '<label class="form-check-label m-0">'
            notesHtml += notes.message +' <i class="input-helper rounded"></i></label>'
            notesHtml += '<div class="text-small me-3"> On <strong>'+ notes.date +'</strong></div>'
            notesHtml += '<div class="text-small me-3"> By <strong>'+ notes.noteBy +'</strong></div></div></div></li>'
          });
          $("#editTransactionForm #pastNotesOfThisTransaction").html(notesHtml);

          $("#editTransactionForm #pastNotesOfThisTransaction").html(notesHtml);
          $("#editTransactionForm #status").val(response.data[0].status);
          $("#editTransactionForm #serviceType").val(response.data[0].service_typ);
          $("#editTransactionForm #serviceThrough").val(response.data[0].service_thru);
          // if (response.data[0].is_under_amc == 1) {
          //   $("#editTransactionForm #isUnderAMCYES").prop("checked", true);
          // } else {
          //   $("#editTransactionForm #isUnderAMCNO").prop("checked", true);
          // }
          // $("#editTransactionForm input[id^=isUnderAMC]:radio").attr("disabled",true);
          $("#editTransactionForm #createdDate").val(response.data[0].created_on).attr('disabled', true);
          $("#editTransactionForm").append('<input type="hidden" name="tId" id="tId" value="'+ response.data[0].uniq_id +'" />');

          $("#editTransactionModal").modal("show");

          // Handle edit form submission
          $("#editTransactionDataBtn").unbind('click').bind("click", function (e) {
            
            e.preventDefault();
            var data = $("#editTransactionForm").serialize();
            console.log("data: ", data);
            $.ajax({
              type: "POST",
              url: "./services/transaction_edit.php",
              data: data,
              dataType: "json",
              success: function (response) {
                if (response.success == true) {
                  $("#editTransactionForm")[0].reset();
                  $("#editTransactionModal").modal("hide");
                  manageTicketDataTbl.ajax.reload(null, true);
                } else {
                  let errorMessage = "";
                  switch (response.message) {
                    case "Duplicate entry ":
                      errorMessage = "An Entry is repeated...!"
                      break;
                    case "Invalid Request":
                      errorMessage = "Invalid request...!"
                      break;
                    case "Mandatory":
                      errorMessage = "Please make sure you filled all the mandatory fields...!"
                      break;
                    case "Exception":
                      errorMessage = "An error occured while creating customer. Please contact system admin...!"
                      break;
                    default:
                      errorMessage = "Failed to create customer. Please contact system admin!"
                      break;
                  }
                  alert(errorMessage);
                }
              },
              error: function () {
                alert("Failed to Edit Transaction");
              },
            });
          });
        } else {
          alert("Failed to fetch transaction details");
        }
      },
      error: function () {
        alert("Error fetching transaction details");
      },
    });
  }
}

function viewTicket(params = null) {
  if (params) {
    $.ajax({
      type: "POST",
      url: "./services/transaction_fetch_single.php",
      data: { tId: params },
      dataType: "json",
      success: function (response) {
        if (response.success == true) {
          // console.log("transaction: ", response.data);
          setCustomersDropdown('view');
          setAgentsDropdowns('view');
          currentTransaction = response.data[0];

          $("#currentViewTransactionCode").text(response.data[0].uniq_id);
          $("#viewTransactionForm #problemStmt").val(response.data[0].problem_stmt);
          $("#viewTransactionForm #problemDesc").val(response.data[0].problem_desc);

          let commentsHtml = '';
          let comments = '['+response.data[0].comments+']';
          JSON.parse(comments).forEach(function(comment) {
            commentsHtml += '<li class="d-block">'
            commentsHtml += '<div class="form-check w-100">'
            commentsHtml += '<label class="form-check-label m-0">'
            commentsHtml += comment.message +' <i class="input-helper rounded"></i></label>'
            commentsHtml += '<div class="d-flex mt-2"><div class="badge badge-opacity-warning me-3"> '+ comment.status +' </div>'
            commentsHtml += '<div class="text-small me-3"> On <strong>'+ comment.date +'</strong></div>'
            commentsHtml += '<div class="text-small me-3"> By <strong>'+ comment.commentBy +'</strong></div></div></div></li>'
          });
          $("#viewTransactionForm #pastCommentsOfThisTransaction").html(commentsHtml);

          let notesHtml = '';
          let notes = '['+response.data[0].notes+']';
          JSON.parse(notes).forEach(function(notes) {
            if(notes.message == "") {
              return;
            }
            notesHtml += '<li class="d-block">'
            notesHtml += '<div class="form-check w-100">'
            notesHtml += '<label class="form-check-label m-0">'
            notesHtml += notes.message +' <i class="input-helper rounded"></i></label>'
            notesHtml += '<div class="text-small me-3"> On <strong>'+ notes.date +'</strong></div>'
            notesHtml += '<div class="text-small me-3"> By <strong>'+ notes.noteBy +'</strong></div></div></div></li>'
          });
          $("#viewTransactionForm #pastNotesOfThisTransaction").html(notesHtml);

          $("#viewTransactionForm #status").val(response.data[0].status);
          $("#viewTransactionForm #serviceType").val(response.data[0].service_typ);
          $("#viewTransactionForm #serviceThrough").val(response.data[0].service_thru);
          
          $("#viewTransactionForm #createdDate").val(response.data[0].created_on).attr('disabled', true);
          $("#viewTransactionForm #lastUpdatedOn").val(response.data[0].updated_on).attr('disabled', true);
          $("#viewTransactionForm #lastUpdatedBy").val(response.data[0].updated_by).attr('disabled', true);

          $("#viewTransactionModal").modal("show");
        } else {
          alert("Failed to fetch transaction details");
        }
      },
      error: function () {
        alert("Error fetching transaction details");
      }
    });
  }
}