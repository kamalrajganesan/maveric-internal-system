$(document).ready(function() {

    // Initialize Flatpickr for range and single date
    $("#transactionDateRange").flatpickr({
        mode: "range",
        dateFormat: "d/m/Y"
    });

    $("#transactionDate").flatpickr({
        dateFormat: "d/m/Y"
    });

    // Initialize DataTable
    let manageTicketDataTbl = $("#transactionMasterTbl").DataTable({
        scrollX: true,
        order: [[5, 'desc']], // sort by last serviced date
        processing: true,
        serverSide: true, // server-side processing for large datasets
        ajax: {
            url: "./services/customer_report_fetch.php",
            type: "POST",
            data: function(d) {
                // Send filter values
                d.transac_range = $("#transactionDateRange").val() || '';
                d.transac_single_date = $("#transactionDate").val() || '';
                d.serviceType = $("#serviceTypeFilter").val() || '';
            },
            dataSrc: function(json) {
                // Check for proper JSON response
                if (!json || json.success === false) {
                    alert(json?.message || "Failed to fetch data!");
                    return [];
                }
                return json.data;
            },
            error: function(xhr, error, thrown) {
                // Show detailed error in console
                console.error("AJAX Error:", xhr.responseText);
                alert("Failed to fetch data. Check console for details.");
            }
        },
        columns: [
            { data: "id", title: "S.No" },
            { data: "customer_name", title: "Company Name" },
            { data: "contact_number", title: "Contact" },
            { data: "transactions_count", title: "Transactions" },
            { data: "contact_person", title: "Contact Person" },
            { data: "last_serviced_date", title: "Last Serviced Date" },
            { 
                data: "id",
                title: "Actions",
                render: function(data) {
                    return `
                        <button class="btn btn-sm btn-primary" onclick="viewTicket(${data})">View</button>
                        <button class="btn btn-sm btn-warning" onclick="editTicket(${data})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="removeTicket(${data})">Delete</button>
                    `;
                }
            }
        ]
    });

    // Filter button
    $("#filterBtn").click(() => manageTicketDataTbl.ajax.reload());

    // Reset button
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

// Example Action Functions (placeholders)
function viewTicket(id) {
    alert("View Ticket ID: " + id);
}

function editTicket(id) {
    alert("Edit Ticket ID: " + id);
}

function removeTicket(id) {
    if (confirm("Are you sure you want to delete this ticket?")) {
        $.ajax({
            url: "./services/ticket_remove.php",
            type: "POST",
            data: { ticketId: id },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $("#transactionMasterTbl").DataTable().ajax.reload(null, false);
                } else {
                    alert("Failed to remove ticket!");
                }
            },
            error: function() {
                alert("Error while removing ticket.");
            }
        });
    }
}
