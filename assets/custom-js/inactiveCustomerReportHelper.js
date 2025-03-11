var manageInactiveCustomerMasterTbl;

$(document).ready(function () {
  manageInactiveCustomerMasterTbl = $("#inactiveCustomerMasterTbl").DataTable({
    type: "Post",
    scrollX: true,
    ajax: {
      url: "./services/getAllInactiveCustomers.php",
      type: "POST",
      dataType: "json",
    },
  });
});



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
          $("#viewCustomerForm #amcEndDate").val(response.data[0].amc_end_date).attr("readonly", true);
          $("#viewCustomerForm #tallyStartDate").val(response.data[0].tally_st_date).attr("readonly", true);
          $("#viewCustomerForm #tallyEndDate").val(response.data[0].tally_end_date).attr("readonly", true);
          $("#viewCustomerForm #cloudStartDate").val(response.data[0].cloud_st_date).attr("readonly", true);
          $("#viewCustomerForm #cloudEndDate").val(response.data[0].cloud_end_date).attr("readonly", true);
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
