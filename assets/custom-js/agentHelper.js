var manageCustDataTbl;

$(document).ready(function () {
  manageCustDataTbl = $("#agentMasterTbl").DataTable({
    type: "Post",
    ajax: {
      url: "./services/agent_fetch_all.php",
      type: "POST",
      data: {
        param: agent_page,
      },
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
          manageCustDataTbl.ajax.reload(null, true);
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
      data: { agentId: params },
      dataType: "json",
      success: function (response) {
        if (response.success == true) {
          manageCustDataTbl.ajax.reload(null, false);
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
          console.log("response: ", response);
          $("#viewAgentModal").modal("show");

          $("#viewAgentForm #agentName").val(response.data[0].agent_nm).attr("readonly", true);
          $("#viewAgentForm #companyName").val(response.data[0].company_nm).attr("readonly", true);
          $("#viewAgentForm #address").val(response.data[0].address_ln).attr("readonly", true);
          $("#viewAgentForm #contact").val(response.data[0].contact).attr("readonly", true);
          $("#viewAgentForm #email").val(response.data[0].email).attr("readonly", true);
          $("#viewAgentForm #systemEmail").val(response.data[0].sys_email).attr("readonly", true);
          $("#viewAgentForm #pincode").val(response.data[0].pincode).attr("readonly", true);
          $("#viewAgentForm #city").val(response.data[0].city).attr("readonly", true);
          $("#viewAgentForm #area").val(response.data[0].area).attr("readonly", true);

          $('#viewAgentForm input[name="serviceType"][value="AMC"]')
            .prop("checked", response.data[0].service_type.includes("AMC"))
            .attr("disabled", true);
          $('#viewAgentForm input[name="serviceType"][value="Tally Subscription"]')
            .prop("checked", response.data[0].service_type.includes("Tally"))
            .attr("disabled", true);
          $('#viewAgentForm input[name="serviceType"][value="One Time"]')
            .prop("checked", response.data[0].service_type.includes("One Time"))
            .attr("disabled", true);

          $("#viewAgentForm #agentStatus").val(response.data[0].is_active).attr("disabled", true);

          $("#viewAgentForm #licenseType").val(response.data[0].license_typ).attr("readonly", true);
          $("#viewAgentForm #serviceStartDate").val(response.data[0].service_st_date).attr("readonly", true);
          $("#viewAgentForm #serviceEndDate").val(response.data[0].service_end_date).attr("readonly", true);
          $("#viewAgentForm #specialNote").val(response.data[0].spl_cust_note).attr("readonly", true);
          $("#viewAgentForm #isActive").val(response.data[0].is_active).attr("readonly", true);
          $("#viewAgentForm #createdBy").val(response.data[0].created_by).attr("readonly", true);
          $("#viewAgentForm #createdOn").val(response.data[0].created_on).attr("readonly", true);
          $("#viewAgentForm #updatedBy").val(response.data[0].updated_by).attr("readonly", true);
          $("#viewAgentForm #agentUniqCode").val(response.data[0].agent_uniq_code).attr("readonly", true);
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

          $("#editAgentForm #agentId").val(response.data[0].agent_id);
          $("#editAgentForm #agentName").val(response.data[0].agent_nm);
          $("#editAgentForm #companyName").val(response.data[0].company_nm);
          $("#editAgentForm #address").val(response.data[0].address_ln);
          $("#editAgentForm #contact").val(response.data[0].contact);
          $("#editAgentForm #email").val(response.data[0].email);
          $("#editAgentForm #systemEmail").val(response.data[0].sys_email);
          $("#editAgentForm #pincode").val(response.data[0].pincode);
          $("#editAgentForm #city").val(response.data[0].city);
          $("#editAgentForm #area").val(response.data[0].area);

          $('#editAgentForm input[name="serviceType[]"][value="AMC"]')
            .prop("checked", response.data[0].service_type.includes("AMC"))
            .attr("disabled", true);
          $('#editAgentForm input[name="serviceType[]"][value="Tally Subscription"]')
            .prop("checked", response.data[0].service_type.includes("Tally"))
            .attr("disabled", true);
          $('#editAgentForm input[name="serviceType[]"][value="One Time"]')
            .prop("checked", response.data[0].service_type.includes("One Time"))
            .attr("disabled", true);

          $("#editAgentForm #agentStatus").val(response.data[0].is_active);

          $("#editAgentForm #licenseType").val(response.data[0].license_typ);
          $("#editAgentForm #serviceStartDate").val(response.data[0].service_st_date);
          $("#editAgentForm #serviceEndDate").val(response.data[0].service_end_date);
          $("#editAgentForm #specialNote").val(response.data[0].spl_cust_note);
          $("#editAgentForm #isActive").val(response.data[0].is_active);
          $("#editAgentForm #createdBy").val(response.data[0].created_by);
          $("#editAgentForm #createdOn").val(response.data[0].created_on);
          $("#editAgentForm #updatedBy").val(response.data[0].updated_by);
          $("#editAgentForm #agentUniqCode").val(response.data[0].agent_uniq_code);

          $("#editAgentForm").append(
            '<input type="hidden" name="cId" id="cId" value="' + response.data[0].id + '" />'
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
                  manageCustDataTbl.ajax.reload(null, true);
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
