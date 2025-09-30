var manageInactiveCustomerMasterTbl;

$(document).ready(function() {
    // Initialize DataTable
manageInactiveCustomerMasterTbl = $("#inactiveCustomerMasterTbl").DataTable({
    scrollX: true,
    processing: true,
    serverSide: false,
    ajax: {
        url: "./services/getAllInactiveCustomers.php",
        type: "POST",
        data: function(d) {
            d.dateRange      = $("#dateRange").val();
            d.singleDate     = $("#singleDate").val();
            d.serviceType    = $("#serviceType").val();
            d.pincode        = $("#pincode").val();
            d.serviceThrough = $("#serviceThrough").val();
        }
    },
    pageLength: 10,  // ← ADD THIS
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],  // ← ADD THIS
    columnDefs: [
        { targets: [4,5], orderable: false }
    ]
});

    // Flatpickr
    if (typeof flatpickr !== "undefined") {
        flatpickr("#dateRange", { mode: "range", dateFormat: "d/m/Y" });
        flatpickr("#singleDate", { dateFormat: "d/m/Y" });
    }

    // Filter button
    $("#filterBtn").click(function() {
        manageInactiveCustomerMasterTbl.ajax.reload();
    });

    // Reset button
    $("#resetBtn").click(function() {
    $("#dateRange, #singleDate, #serviceType, #pincode, #serviceThrough").val("");
    if (flatpickr) {
        document.getElementById('dateRange')._flatpickr.clear();
        document.getElementById('singleDate')._flatpickr.clear();
    }
    manageInactiveCustomerMasterTbl.ajax.reload();
});

    // Delegate click for dynamic buttons
    $('#inactiveCustomerMasterTbl').on('click', '.view-btn', function() {
        var customerUniqCode = $(this).data('customer');
        if (customerUniqCode) viewCustomer(customerUniqCode);
    });
});

// View customer function
function viewCustomer(customerUniqCode) {
    if (!customerUniqCode) return;
    $.ajax({
        type: "POST",
        url: "./services/customer_fetch_single.php",
        data: { customerId: customerUniqCode },
        dataType: "json",
        success: function(res) {
            if (res.success) {
                const data = res.data[0];
                $('#viewCustomerForm').find('input, select').each(function() {
                    const id = $(this).attr('id');
                    if(data[id] !== undefined){
                        $(this).val(data[id]).prop('readonly', true).prop('disabled', true);
                    }
                });
                $("#viewCustomerModal").modal("show");
            } else {
                alert("Failed to fetch customer details!");
            }
        },
        error: function() {
            alert("AJAX error while fetching customer!");
        }
    });
}




function viewCustomer(params = null) {
  if (params) {
    $.ajax({
      type: "POST",
      url: "./services/customer_fetch_single.php",
      data: { customerId: params },
      dataType: "json",
      success: function (response) {
        if (response.success == true) {
          console.log("Customer view response:", response);
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
          console.error("Failed to fetch customer:", response);
          alert("Failed to fetch selected customer...!");
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", xhr, status, error);
        alert("Failed to create a request");
      },
    });
  }
}

// Additional utility functions for filter management
function getFilterValues() {
  return {
    dateRange: $("#dateRange").val(),
    serviceType: $("#serviceType").val(),
    singleDate: $("#singleDate").val()
  };
}

function applyFilters(filters) {
  if (filters.dateRange) $("#dateRange").val(filters.dateRange);
  if (filters.serviceType) $("#serviceType").val(filters.serviceType);
  if (filters.singleDate) $("#singleDate").val(filters.singleDate);
  
  $("#filterBtn").click();
}

function clearAllFilters() {
  $("#resetBtn").click();
}

// Alternative initialization function if you need to reinitialize DataTable programmatically
function initializeDataTable() {
  // Destroy existing DataTable if it exists
  if ($.fn.DataTable.isDataTable('#inactiveCustomerMasterTbl')) {
    manageInactiveCustomerMasterTbl.destroy();
  }
  
  // Clear the table HTML
  $('#inactiveCustomerMasterTbl').empty();
  
  // Reinitialize
  manageInactiveCustomerMasterTbl = $("#inactiveCustomerMasterTbl").DataTable({
    type: "Post",
    scrollX: true,
    ajax: {
      url: "./services/getAllInactiveCustomers.php",
      type: "POST",
      dataType: "json",
      data: function(d) {
        var filters = getFilterValues();
        d.dateRange = filters.dateRange;
        d.serviceType = filters.serviceType;
        d.singleDate = filters.singleDate;
        return d;
      }
    },
  });
}