var manageCustDataTbl;

$(document).ready(function () {
  manageCustDataTbl = $("#customerMasterTbl").DataTable({
    type: "Post",
    scrollX: true,
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
          let errorMessage = "";
          switch (response.message) {
            case "Duplicate entry - Customer Serial Number":
              errorMessage = "Customer Serial number is repeated...!"
              break;
            case "Mandatory":
              errorMessage = "Please make sure you filled all the mandatory fields...!"
              break;
            case "Invalid Request":
              errorMessage = "Invalid request...!"
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
        alert("Failed to Add Customer");
      },
    });
  });

  // Handle Create Transaction form submission
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

          $("#editCustomerForm #customerUniqCode").val(response.data[0].customer_uniq_code);
          $("#editCustomerForm #customerName").val(response.data[0].customer_nm);
          $("#editCustomerForm #companyName").val(response.data[0].company_nm);
          $("#editCustomerForm #contact").val(response.data[0].contact);
          $("#editCustomerForm #telephone").val(response.data[0].telephone);
          $("#editCustomerForm #email").val(response.data[0].email);
          $("#editCustomerForm #address").val(response.data[0].address_ln);
          $("#editCustomerForm #area").val(response.data[0].area);
          $("#editCustomerForm #pincode").val(response.data[0].pincode);
          $("#editCustomerForm #customerStatus").val(response.data[0].is_active).attr;
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
          $("#editCustomerForm #amcEndDate").val(response.data[0].amc_end_date);
          $("#editCustomerForm #tallyStartDate").val(response.data[0].tally_st_date);
          $("#editCustomerForm #tallyEndDate").val(response.data[0].tally_end_date);
          $("#editCustomerForm #cloudStartDate").val(response.data[0].cloud_st_date);
          $("#editCustomerForm #cloudEndDate").val(response.data[0].cloud_end_date);
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

        $("#editCustomerDataBtn").unbind().bind("click", function (e) {
          
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
                let errorMessage = "";
                switch (response.message) {
                  case "Duplicate entry - Customer Serial Number":
                    errorMessage = "Customer Serial number is repeated...!"
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
              alert("Failed to Edit Customer");
            },
          });
        });
      },
      error: function () {
        alert("Failed to Fetch Customer");
      },
    });
  }
}

function setCustomerDetails(customer) {
  var customerDetailsHTML = `
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="row">
          <div class="col-md-12">
            <p><strong>Customer Name</strong>: ${customer.customer_nm}</p>
          </div>
          <div class="col-md-12">
            <p><strong>Contact</strong>: ${customer.contact}</p>
          </div>
          <div class="col-md-12">
            <p><strong>Telephone</strong>: ${customer.telephone}</p>
          </div>
          <div class="col-md-12">
            <p><strong>Communication Email</strong>: ${customer.email}</p>
          </div>
          <div class="col-md-12">
            <p><strong>Address</strong>: ${customer.address_ln}</p>
          </div>
          <div class="col-md-12">
            <p><strong>Area</strong>: ${customer.area}</p>
          </div>
          <div class="col-md-12">
            <p><strong>City</strong>: ${customer.city}</p>
          </div>
          <div class="col-md-12">
            <p><strong>Pincode</strong>: ${customer.pincode}</p>
          </div>
          <div class="col-md-12">
            <p><strong>License Type</strong>: ${customer.license_typ}</p>
          </div>
          <div class="col-md-12">
            <p><strong>Customer Uniq Code</strong>: ${customer.customer_uniq_code}</p>
          </div>
        </div>
      </div>

      <div class="col-md-5">
        <div class="row">
          <div class="col-md-12">
            <p><strong>Customer Company</strong>: ${customer.company_nm}</p>
          </div>
          <div class="col-md-12">
            <p><strong>Service Type</strong>: ${customer.service_type}</p>
          </div>
          <div class="col-md-12">
            <p><strong>Tally Email</strong>: ${customer.sys_email}</p>
          </div>
          <div class="col-md-12">
            <p><strong style="font-size: 1.5em;">AMC End Date</strong>: <span style="font-size: 1.5em;"> ${customer.amc_end_date} </span></p>
          </div>
          <div class="col-md-12">
            <p><strong style="font-size: 1.5em;">Tally Subscription End Date</strong>: <span style="font-size: 1.5em;">${customer.tally_end_date}</span></p>
          </div>
          <div class="col-md-12">
            <p><strong style="font-size: 1.5em;">Cloud End Date</strong>: <span style="font-size: 1.5em;">${customer.cloud_end_date}</span></p>
          </div>
        </div>
      </div>
    </div>
  `;

  $("#addTransactionModal #customerDetails").html(customerDetailsHTML);

}

function addTransactionOfACustomer(params) {

  if (params) {
    $.ajax({
      type: "POST",
      url: "./services/customer_fetch_single.php",
      data: { customerId: params },
      dataType: "json",
      success: function (response) {
        
        if (response.success == true) {

          setCustomerDetails(response.data[0], "edit");
          
          // console.log("response: ", response);
          $("#addTransactionModal").modal("show");

          $("#addTransactionModal #companyName").val(response.data[0].company_nm).attr("readonly", true);
          $("#addTransactionModal #serviceType").val(response.data[0].service_type).attr("disabled", true);
          
          $("#addTransactionModal").append('<input type="hidden" name="customerId" id="customerId" value="'+ response.data[0].uniq_id +'" />');
        } else {
          alert("Failed to fetch selected customer...!");
        }
      },
      error: function () {
          alert("Failed to fetch selected customer...!");
      },
    });
  }
}