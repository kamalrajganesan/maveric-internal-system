$(document).ready(function() {

    // Initialize Flatpickr
    $("#transactionDateRange").flatpickr({ mode: "range", dateFormat: "d/m/Y" });
    $("#transactionDate").flatpickr({ dateFormat: "d/m/Y" });

    let manageTicketDataTbl = $("#transactionMasterTbl").DataTable({
        scrollX: true,
        order: [[5, 'desc']],
        processing: true,
        serverSide: true,
        ajax: {
            url: "./services/customer_report_fetch.php",
            type: "POST",
            data: function(d) {
                d.transac_range = $("#transactionDateRange").val() || '';
                d.transac_single_date = $("#transactionDate").val() || '';
                d.serviceType = $("#serviceTypeFilter").val() || '';
            },
            dataSrc: function(json) {
                if (!json || json.success === false) {
                    alert(json?.message || "Failed to fetch data!");
                    return [];
                }
                return json.data;
            }
        },
        columns: [
            { 
                data: null, 
                title: "S.No",
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { 
                data: "customer_name", 
                title: "Company Name",
                render: function(data, type, row) {
                    return `<a href="javascript:void(0)" class="customer-detail-link" data-id="${row.id}">${data}</a>`;
                }
            },
            { data: "contact_number", title: "Contact" },
            { data: "transactions_count", title: "Transactions" },
           
            { data: "last_serviced_date", title: "Last Serviced Date" },
            { 
                data: "id",
                title: "Actions",
                className: "text-center",
                render: function(data, type, row) {
    return `
        <button class="btn btn-sm view-btn custom-view-btn" data-id="${data}" title="View Info">
            <i class="fa fa-eye"></i> View 
        </button>
    `;
}

            }
        ]
    });

    // --- View button click --- (Replace the existing view button click handler)
$(document).on('click', '.customer-detail-link, .view-btn', function() {
    const customerId = $(this).data('id');

    $.ajax({
        url: './services/customer_report_fetch.php',
        type: 'POST',
        dataType: 'json',
        data: { customerIdForPopup: customerId },
        success: function(response) {
            if(response.success && response.popupCustomer){
                let customer = response.popupCustomer;
                $('#customerInfo').html(`
                    <p><strong>Customer Name:</strong> ${customer.customer_name}</p>
                    <p><strong>Contact:</strong> ${customer.contact_number}</p>
                `);

                let rows = '';
                if(customer.transactions.length > 0) {
                    customer.transactions.forEach((t, index) => {
                        rows += `<tr>
                            <td>${index+1}</td>
                            <td>${t.created_on}</td>
                            <td>${t.service_type}</td>
                             <td>${t.agent_name}</td>
                            <td>${t.comments || '-'}</td>
                            
                            
                        </tr>`;
                    });
                } else {
                    rows = '<tr><td colspan="6" class="text-center">No transactions found</td></tr>';
                }
                
                $('#customerTransactionsTbl thead').html(`
                    <tr>
                        <th>S.No</th>
                        <th>Date of Service</th>
                        <th>Service Type</th>
                        <th>Agent</th>
                        <th>Comments</th>
                        
                        
                    </tr>
                `);
                
                $('#customerTransactionsTbl tbody').html(rows);

                $('#customerDetailsModal').modal('show');
            } else {
                alert("No customer data found.");
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert("Error fetching customer transactions.");
        }
    });
});
    // Filters
    $("#filterBtn").click(() => manageTicketDataTbl.ajax.reload());
    $("#resetBtn").click(() => {
        $("#transactionDateRange").val('');
        $("#transactionDate").val('');
        $("#serviceTypeFilter").val('');
        manageTicketDataTbl.ajax.reload();
    });

    // Search box
    $("#searchInput").on('keyup', function() {
        manageTicketDataTbl.search(this.value).draw();
    });

    // Entries per page
    $("#entriesPerPage").on('change', function() {
        manageTicketDataTbl.page.len($(this).val()).draw();
    });
});
