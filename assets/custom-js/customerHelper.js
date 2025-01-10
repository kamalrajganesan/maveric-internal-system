var manageCustDataTbl;

$(document).ready(function () {
  manageCustDataTbl = $("#customerMasterTbl").DataTable({
    type: "Post",
    ajax: {
      url: "./services/customer_fetch_all.php",
      type: "POST",
      data: {
        param: customer_page,
      },
      dataType: "json",
    },
  });

  $("#addCustomerDataBtn").on("click", function (e) {
    e.preventDefault();
    var data = $("#addCustomerForm").serialize();

    $.ajax({
      type: "POST",
      url: "./services/customer_add.php",
      data: data,
      dataType: "json",
      success: function (response) {
        if (response.success == true) {
          $("#addCustomerForm")[0].reset();
          $("#addCustomerModal").modal("hide");
          manageCustDataTbl.ajax.reload(null, true);
        } else {
          alert("Failed to Add Customer...!");
        }
      },
      error: function () {
        alert("Failed to Add Customer");
      },
    });
  });
});

function removeCustomer(params = null) {
  // console.log("params: ", params);
  if (params) {
    $.ajax({
      type: "POST",
      url: "./services/customer_remove.php",
      data: { customerId: params },
      dataType: "json",
      success: function (response) {
        if (response.success == true) {
          manageCustDataTbl.ajax.reload(null, false);
        } else {
          alert("Failed to Remove Customer...!");
        }
      },
      error: function () {
        alert("Failed to Remove Customer");
      },
    });
  }
}

function viewCustomer(params = null) {
  // console.log("params: ", params);
  if (params) {
    $.ajax({
      type: "POST",
      url: "./services/customer_fetch_single.php",
      data: { customerId: params },
      dataType: "json",
      success: function (response) {
        if (response.success == true) {
          console.log("response: ", response);
          $("#viewCustomerModal").modal("show");

          $("#viewCustomerForm #customerUniqCode").val(response.data[0].customer_uniq_code).attr("readonly", true);
          $("#viewCustomerForm #customerName").val(response.data[0].customer_nm).attr("readonly", true);
          $("#viewCustomerForm #companyName").val(response.data[0].company_nm).attr("readonly", true);
          $("#viewCustomerForm #contact").val(response.data[0].contact).attr("readonly", true);
          $("#viewCustomerForm #telephone").val(response.data[0].telephone).attr("readonly", true);
          $("#viewCustomerForm #email").val(response.data[0].email).attr("readonly", true);
          $("#viewCustomerForm #address").val(response.data[0].address_ln).attr("readonly", true);
          $("#viewCustomerForm #area").val(response.data[0].area).attr("readonly", true);
          $("#viewCustomerForm #pincode").val(response.data[0].pincode).attr("readonly", true);
          $("#viewCustomerForm #customerStatus").val(response.data[0].is_active).attr("disabled", true);
          $("#viewCustomerForm #createdBy").val(response.data[0].created_by).attr("readonly", true);
          $("#viewCustomerForm #createdOn").val(response.data[0].created_on).attr("readonly", true);
          
          $('#viewCustomerForm input[name="serviceType"][value="AMC"]')
            .prop("checked", response.data[0].service_type.includes("AMC"))
            .attr("disabled", true);
          $('#viewCustomerForm input[name="serviceType"][value="Tally Subscription"]')
            .prop("checked", response.data[0].service_type.includes("Tally"))
            .attr("disabled", true);
          $('#viewCustomerForm input[name="serviceType"][value="Cloud"]')
            .prop("checked", response.data[0].service_type.includes("Cloud"))
            .attr("disabled", true);
          $('#viewCustomerForm input[name="serviceType"][value="One Time"]')
            .prop("checked", response.data[0].service_type.includes("One Time"))
            .attr("disabled", true);
          $("#viewCustomerForm #city").val(response.data[0].city).attr("readonly", true);
          $("#viewCustomerForm #amcStartDate").val(response.data[0].amc_st_date).attr("readonly", true);
          $("#viewCustomerForm #amcEndDate").val(response.data[0].service_end_date).attr("readonly", true);
          $("#viewCustomerForm #tallyStartDate").val(response.data[0].service_st_date).attr("readonly", true);
          $("#viewCustomerForm #tallyEndDate").val(response.data[0].service_end_date).attr("readonly", true);
          $("#viewCustomerForm #licenseType").val(response.data[0].license_typ).attr("readonly", true);
          $("#viewCustomerForm #tallyEmail").val(response.data[0].sys_email).attr("readonly", true);
          $("#viewCustomerForm #specialNote").val(response.data[0].spl_cust_note).attr("readonly", true);
          $("#viewCustomerForm #referredBy").val(response.data[0].referredBy).attr("readonly", true);
          $("#viewCustomerForm #auditor").val(response.data[0].auditor).attr("readonly", true);
          $("#viewCustomerForm #updatedBy").val(response.data[0].updated_by).attr("readonly", true);
        } else {
          alert("Failed to fetch selected customer...!");
        }
      },
      error: function () {
        alert("Failed to create a request");
      },
    });
  }
}

function editCustomer(params = null) {
  if (params) {
    $.ajax({
      type: "POST",
      url: "./services/customer_fetch_single.php",
      data: { customerId: params },
      dataType: "json",
      success: function (response) {
        if (response.success == true) {
          
          console.log("response: ", response);
          $("#editCustomerModal").modal("show");

          if (response.success == true) {
            console.log("response: ", response);
            $("#viewCustomerModal").modal("show");
  
            $("#editCustomerForm #customerUniqCode").val(response.data[0].customer_uniq_code);
            $("#editCustomerForm #customerName").val(response.data[0].customer_nm);
            $("#editCustomerForm #companyName").val(response.data[0].company_nm);
            $("#editCustomerForm #contact").val(response.data[0].contact);
            $("#editCustomerForm #telephone").val(response.data[0].telephone);
            $("#editCustomerForm #email").val(response.data[0].email);
            $("#editCustomerForm #address").val(response.data[0].address_ln);
            $("#editCustomerForm #area").val(response.data[0].area);
            $("#editCustomerForm #pincode").val(response.data[0].pincode);
            $("#editCustomerForm #customerStatus").val(response.data[0].is_active).attr("disabled", true);
            $("#editCustomerForm #createdBy").val(response.data[0].created_by);
            $("#editCustomerForm #createdOn").val(response.data[0].created_on);
            
            $('#editCustomerForm input[name="serviceType[]"][value="AMC"]')
              .prop("checked", response.data[0].service_type.includes("AMC"));
            $('#editCustomerForm input[name="serviceType[]"][value="Tally Subscription"]')
              .prop("checked", response.data[0].service_type.includes("Tally"));
            $('#editCustomerForm input[name="serviceType[]"][value="Cloud"]')
              .prop("checked", response.data[0].service_type.includes("Cloud"));
            $('#editCustomerForm input[name="serviceType[]"][value="One Time"]')
              .prop("checked", response.data[0].service_type.includes("One Time"));
            $("#editCustomerForm #city").val(response.data[0].city);
            $("#editCustomerForm #amcStartDate").val(response.data[0].amc_st_date);
            $("#editCustomerForm #amcEndDate").val(response.data[0].service_end_date);
            $("#editCustomerForm #tallyStartDate").val(response.data[0].service_st_date);
            $("#editCustomerForm #tallyEndDate").val(response.data[0].service_end_date);
            $("#editCustomerForm #licenseType").val(response.data[0].license_typ);
            $("#editCustomerForm #tallyEmail").val(response.data[0].sys_email);
            $("#editCustomerForm #specialNote").val(response.data[0].spl_cust_note);
            $("#editCustomerForm #referredBy").val(response.data[0].referredBy);
            $("#editCustomerForm #auditor").val(response.data[0].auditor);
          } else {
            alert("Failed to fetch selected customer...!");
          }

          $("#editCustomerForm").append(
            '<input type="hidden" name="cId" id="cId" value="' + response.data[0].id + '" />'
          );

          $("#editCustomerDataBtn").on("click", function (e) {
            
            e.preventDefault();
            var data = $("#editCustomerForm").serialize();
            $.ajax({
              type: "POST",
              url: "./services/customer_edit.php",
              data: data,
              dataType: "json",
              success: function (response) {
                if (response.success == true) {
                  $("#editCustomerForm")[0].reset();
                  $("#editCustomerModal").modal("hide");
                  manageCustDataTbl.ajax.reload(null, true);
                } else {
                  alert("Failed to Edit Customer...!");
                }
              },
              error: function () {
                alert("Failed to Edit Customer");
              },
            });
          });
        } else {
          alert("Failed to Fetch Customer...!");
        }
      },
      error: function () {
        alert("Failed to Fetch Customer");
      },
    });
  }
}
