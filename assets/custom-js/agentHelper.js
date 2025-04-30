var manageAgentDataTbl;

$(document).ready(function () {

  $(".make-text-visible").hide();
  $(".make-text-visible, .make-text-invisible").on('click', function() {
      var password = $(this).parents('div').find('input.password');
      if ($(this).hasClass('make-text-visible')) {
          $(password).attr("type", "text");
          $(this).parent().find(".make-text-visible").hide();
          $(this).parent().find(".make-text-invisible").show();
      } else {
          $(password).attr("type", "password");
          $(this).parent().find(".make-text-invisible").hide();
          $(this).parent().find(".make-text-visible").show();
      }
  });

  manageAgentDataTbl = $("#manageAgentDataTbl").DataTable({
    method: "POST",
    scrollX: true,
    ajax: {
      url: "./services/agent_fetch_all.php",
      type: "GET",
      dataType: "json",
    },
  });

  $("#addAgentDataBtn").on("click", function (e) {
    e.preventDefault();
    var data = $("#addAgentForm").serialize();

    $.ajax({
      type: "POST",
      url: "./services/agent_add.php",
      data: data,
      dataType: "json",
      success: function (response) {
        if (response.success == true) {
          $("#addAgentForm")[0].reset();
          $("#addAgentModal").modal("hide");
          manageAgentDataTbl.ajax.reload(null, true);
        } else {
          alert("Failed to Add Agent...!");
        }
      },
      error: function () {
        alert("Failed to Add Agent");
      },
    });
  });
});

function removeAgent(params = null) {
  // console.log("params: ", params);
  if (params) {
    $.ajax({
      type: "POST",
      url: "./services/agent_remove.php",
      data: { aId: params },
      dataType: "json",
      success: function (response) {
        if (response.success == true) {
          manageAgentDataTbl.ajax.reload(null, false);
        } else {
          alert("Failed to Remove Agent...!");
        }
      },
      error: function () {
        alert("Failed to Remove Agent");
      },
    });
  }
}

function viewAgent(params = null) {
  // console.log("params: ", params);
  if (params) {
    $.ajax({
      type: "POST",
      url: "./services/agent_fetch_single.php",
      data: { agentId: params },
      dataType: "json",
      success: function (response) {
        if (response.success == true) {
          // console.log("response: ", response);
          $("#viewAgentModal").modal("show");

          $("#viewAgentForm #agentName").val(response.data[0].agent_nm).attr("readonly", true);
          $("#viewAgentForm #email").val(response.data[0].email).attr("readonly", true);
          $("#viewAgentForm #contact1").val(response.data[0].primary_contact).attr("readonly", true);
          $("#viewAgentForm #contact2").val(response.data[0].secondary_contact).attr("readonly", true);
          $("#viewAgentForm #address").val(response.data[0].address_ln).attr("readonly", true);
          $("#viewAgentForm #pincode").val(response.data[0].pincode).attr("readonly", true);
          $("#viewAgentForm #city").val(response.data[0].city).attr("readonly", true);
          $("#viewAgentForm #area").val(response.data[0].area).attr("readonly", true);
          $("#viewAgentForm #password").val(response.data[0].pass_code).attr("readonly", true);

          $("#viewAgentForm #activeStatus").val(response.data[0].is_active).attr("disabled", true);
          $("#viewAgentForm #createdOn").val(response.data[0].created_on).attr("readonly", true);
        } else {
          alert("Failed to Fetch Agent...!");
        }
      },
      error: function () {
        alert("Failed to Fetch Agent");
      },
    });
  }
}

function editAgent(params = null) {
  if (params) {
    $.ajax({
      type: "POST",
      url: "./services/agent_fetch_single.php",
      data: { agentId: params },
      dataType: "json",
      success: function (response) {
        if (response.success == true) {
          console.log("response: ", response);
          $("#editAgentModal").modal("show");

          $("#editAgentForm #agentName").val(response.data[0].agent_nm);
          $("#editAgentForm #email").val(response.data[0].email);
          $("#editAgentForm #contact1").val(response.data[0].primary_contact);
          $("#editAgentForm #contact2").val(response.data[0].secondary_contact);
          $("#editAgentForm #address").val(response.data[0].address_ln);
          $("#editAgentForm #pincode").val(response.data[0].pincode);
          $("#editAgentForm #city").val(response.data[0].city);
          $("#editAgentForm #area").val(response.data[0].area);
          $("#editAgentForm #password").val(response.data[0].pass_code);

          $("#editAgentForm #activeStatus").val(response.data[0].is_active);
          $("#editAgentForm #createdOn").val(response.data[0].created_on);

          $("#editAgentForm").append(
            '<input type="hidden" name="aId" id="aId" value="' + response.data[0].id + '" />'
          );

          $("#editAgentDataBtn").on("click", function (e) {
            e.preventDefault();
            var data = $("#editAgentForm").serialize();

            $.ajax({
              type: "POST",
              url: "./services/agent_edit.php",
              data: data,
              dataType: "json",
              success: function (response) {
                if (response.success == true) {
                  $("#editAgentForm")[0].reset();
                  $("#editAgentModal").modal("hide");
                  manageAgentDataTbl.ajax.reload(null, true);
                } else {
                  alert("Failed to Edit Agent...!");
                }
              },
              error: function () {
                alert("Failed to Edit Agent");
              },
            });
          });
        } else {
          alert("Failed to Fetch Agent...!");
        }
      },
      error: function () {
        alert("Failed to Fetch Agent");
      },
    });
  }
}
