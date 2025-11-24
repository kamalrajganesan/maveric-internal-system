var expiringServicesTable;

$(document).ready(function() {

    // Initialize DataTable
    expiringServicesTable = $("#expiringServicesTable").DataTable({
        scrollX: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: "./services/expiring_services_fetch.php",
            type: "POST",
            data: function(d) {
                d.dateRange = $("#dateRange").val() || '';
                // send single selected service (select) as 'service' and also in 'services' array to be flexible
                var svc = $("#serviceFilter").val() || '';
                d.service = svc;
                d.services = svc ? [svc] : [];
            },
            dataSrc: function(json) {
                if (json && json.success && json.data) {
                    return json.data;
                }
                return [];
            }
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        columns: [
            { title: "S. No." },
            { title: "Company Name" },
            { title: "Agent Name" },
            { title: "Contact Number" },
            { title: "Services Offered" },
            { title: "Expiry Date" },
            { title: "Action" }
        ],
        columnDefs: [
            { targets: [6], orderable: false }
        ],
        language: {
            emptyTable: "No data found",
            zeroRecords: "No matching records found"
        }
    });

    // Flatpickr initialization for dateRange (if loaded)
    if (typeof flatpickr !== "undefined") {
        flatpickr("#dateRange", { 
            mode: "range", 
            dateFormat: "d/m/Y",
            // use 'to' as separator for compatibility with server parsing (we accept both)
            conjunction: " to "
        });
    }

    // Filter button
    $("#filterBtn").on('click', function(e) {
        e.preventDefault();
        expiringServicesTable.ajax.reload(null, false);
    });

    // Reset button
    $("#resetBtn").on('click', function(e) {
        e.preventDefault();
        $("#dateRange").val("");

        if (typeof flatpickr !== "undefined") {
            var dateRangeEl = document.getElementById('dateRange');
            if (dateRangeEl && dateRangeEl._flatpickr) {
                dateRangeEl._flatpickr.clear();
            }
        }

        $("#serviceFilter").val("");
        expiringServicesTable.ajax.reload();
    });

    // Press Enter to trigger filter (on dateRange)
    $("#dateRange").on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $("#filterBtn").trigger('click');
        }
    });



    // Delegate click for view buttons (existing modal behavior)
    $('#expiringServicesTable').on('click', '.view-btn', function() {
        var customerUniqCode = $(this).data('customer');
        if (customerUniqCode) {
            viewCustomer(customerUniqCode);
        }
    });
});

// View customer function (keeps your existing modal population logic; adjust field names if your customer_fetch_single returns different keys)
function viewCustomer(customerUniqCode) {
    if (!customerUniqCode) return;
    
    $.ajax({
        type: "POST",
        url: "./services/customer_fetch_single.php",
        data: { customerId: customerUniqCode },
        dataType: "json",
        success: function(response) {
            if (response.success == true && response.data && response.data.length > 0) {
                $("#viewCustomerModal").modal("show");
                
                var data = response.data[0];

                $("#viewCustomerForm #customerUniqCode").val(data.customer_uniq_code || '').attr("readonly", true);
                $("#viewCustomerForm #customerName").val(data.customer_nm || '').attr("readonly", true);
                $("#viewCustomerForm #companyName").val(data.company_nm || '').attr("readonly", true);
                $("#viewCustomerForm #contact").val(data.contact || '').attr("readonly", true);
                $("#viewCustomerForm #telephone").val(data.telephone || '').attr("readonly", true);
                $("#viewCustomerForm #email").val(data.email || '').attr("readonly", true);
                $("#viewCustomerForm #address").val(data.address_ln || '').attr("readonly", true);
                $("#viewCustomerForm #area").val(data.area || '').attr("readonly", true);
                $("#viewCustomerForm #pincode").val(data.pincode || '').attr("readonly", true);
                $("#viewCustomerForm #customerStatus").val(data.is_active || '').attr("disabled", true);
                $("#viewCustomerForm #createdBy").val(data.created_by || '').attr("readonly", true);
                $("#viewCustomerForm #createdOn").val(data.created_on || '').attr("readonly", true);
                
                var serviceType = data.service_type || '';
                $('#viewCustomerForm input[name="serviceType"][value="AMC"]')
                    .prop("checked", serviceType.includes("AMC"))
                    .attr("disabled", true);
                $('#viewCustomerForm input[name="serviceType"][value="Tally Subscription"]')
                    .prop("checked", serviceType.includes("Tally"))
                    .attr("disabled", true);
                $('#viewCustomerForm input[name="serviceType"][value="Cloud"]')
                    .prop("checked", serviceType.includes("Cloud"))
                    .attr("disabled", true);
                $('#viewCustomerForm input[name="serviceType"][value="One Time"]')
                    .prop("checked", serviceType.includes("One Time"))
                    .attr("disabled", true);
                    
                $("#viewCustomerForm #city").val(data.city || '').attr("readonly", true);
                $("#viewCustomerForm #amcStartDate").val(data.amc_st_date || '').attr("readonly", true);
                $("#viewCustomerForm #amcEndDate").val(data.amc_end_date || '').attr("readonly", true);
                $("#viewCustomerForm #tallyStartDate").val(data.tally_st_date || '').attr("readonly", true);
                $("#viewCustomerForm #tallyEndDate").val(data.tally_end_date || '').attr("readonly", true);
                $("#viewCustomerForm #cloudStartDate").val(data.cloud_st_date || '').attr("readonly", true);
                $("#viewCustomerForm #cloudEndDate").val(data.cloud_end_date || '').attr("readonly", true);
                $("#viewCustomerForm #licenseType").val(data.license_typ || '').attr("readonly", true);
                $("#viewCustomerForm #tallyEmail").val(data.sys_email || '').attr("readonly", true);
                $("#viewCustomerForm #specialNote").val(data.spl_cust_note || '').attr("readonly", true);
                $("#viewCustomerForm #referredBy").val(data.referredBy || '').attr("readonly", true);
                $("#viewCustomerForm #auditor").val(data.auditor || '').attr("readonly", true);
                $("#viewCustomerForm #updatedBy").val(data.updated_by || '').attr("readonly", true);
            } else {
                alert("Failed to fetch selected customer...!");
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", xhr, status, error);
            alert("Failed to create a request");
        }
    });
}
