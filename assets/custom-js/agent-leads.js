$(document).ready(function () {
    // Initialize date pickers
    const dateRangePicker = flatpickr("#transactionDateRange", {
        mode: "range",
        dateFormat: "d/m/Y",
        allowInput: true,
        placeholder: "DD/MM/YYYY to DD/MM/YYYY"
    });

    const singleDatePicker = flatpickr("#transactionDate", {
        dateFormat: "d/m/Y",
        allowInput: true,
        placeholder: "DD/MM/YYYY"
    });

    // Show/hide status options based on lead type
    function updateStatusOptions() {
        const leadType = $("#serviceTypeFilter").val();
        const $leadStatus = $("#leadStatus");
        
        // Hide all options first
        $leadStatus.find("option").hide();
        
        // Show relevant options based on lead type
        if (leadType === "Phone Call") {
            $leadStatus.find(".call-lead").show();
        } else if (leadType === "Email") {
            $leadStatus.find(".email-lead").show();
        } else {
            // Show all options when no specific type selected
            $leadStatus.find("option").show();
        }
        
        // Always show "All Statuses" option
        $leadStatus.find('option[value=""]').show();
        
        // Reset to "All Statuses" if current selection is not available
        const currentVal = $leadStatus.val();
        if (currentVal && $leadStatus.find(`option[value="${currentVal}"]:visible`).length === 0) {
            $leadStatus.val("");
        }
    }

    // Initialize status options
    updateStatusOptions();

    // DataTable initialization
    var transactionMasterTbl = $("#transactionMasterTbl").DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        ajax: {
            url: "./services/agent-lead-fetch.php",
            type: "POST",
            data: function (d) {
                return {
                    dateRange: $("#transactionDateRange").val(),
                    singleDate: $("#transactionDate").val(),
                    leadType: $("#serviceTypeFilter").val(),
                    leadStatus: $("#leadStatus").val()
                };
            },
            dataType: "json",
            dataSrc: function (json) {
                if (!json.success) {
                    console.error("Server error:", json.message);
                    showNotification("Error fetching data: " + json.message, "error");
                    return [];
                }
                return json.data || [];
            },
            error: function (xhr, error, thrown) {
                console.error("AJAX error:", error, thrown);
                showNotification("Failed to load data. Please try again.", "error");
            }
        },
     columns: [
    { data: "sno", className: "text-center" },
    { data: "agent_name", className: "text-left" },
    { data: "new_leads", className: "text-center", render: d => `<span class="badge badge-primary">${d}</span>` },
    { data: "following_up", className: "text-center", render: d => `<span class="badge badge-info">${d}</span>` },
    { data: "converted", className: "text-center", render: d => `<span class="badge badge-success">${d}</span>` },
    { data: "contacted_emailed", className: "text-center", render: d => `<span class="badge badge-warning">${d}</span>` },
    { data: "lost", className: "text-center", render: d => `<span class="badge badge-danger">${d}</span>` },
    { data: "leads_handled", className: "text-center", render: d => `<span class="badge badge-dark">${d}</span>` }
]
,

        language: {
            emptyTable: "No data available in table",
            zeroRecords: "No matching records found"
        },
        responsive: true,
        searching: true,
        ordering: true,
        paging: true,
        pageLength: 10
    });

    // Filter button handler
    $("#filterBtn").on("click", function () {
        const dateRange = $("#transactionDateRange").val();
        const singleDate = $("#transactionDate").val();
        
        // Validate date inputs
        if (dateRange && singleDate) {
            showNotification("Please select either Date Range OR Single Date, not both.", "warning");
            return;
        }

        // Validate date range format
        if (dateRange && dateRange.split(' to ').length !== 2) {
            showNotification("Please select a valid date range (start date to end date).", "warning");
            return;
        }

        // Show loading state
        const $btn = $(this);
        const originalText = $btn.html();
        $btn.html('<i class="fa fa-spinner fa-spin"></i> Filtering...').prop('disabled', true);

        // Reload table with new filters
        transactionMasterTbl.ajax.reload(function() {
            // Re-enable button
            $btn.html(originalText).prop('disabled', false);
            
            // Show success message
            const records = transactionMasterTbl.rows().count();
            showNotification(`Filter applied successfully. Found ${records} records.`, "success");
        });
    });

    // Status filter change handler
    $("#leadStatus").on("change", function () {
        const status = $(this).val();
        let newTitle = "News";
        
        // Update column title based on status
        switch(status) {
            case "New":
                newTitle = "New Leads";
                break;
            case "Contacted":
            case "Emailed and Waiting for reply":
                newTitle = "Contacted Leads";
                break;
            case "Following":
                newTitle = "Following Up Leads";
                break;
            case "Converted":
                newTitle = "Converted Leads";
                break;
            case "Lost":
                newTitle = "Lost Leads";
                break;
            default:
                newTitle = "New";
        }
        
        $("#transactionMasterTbl thead th").eq(3).text(newTitle);
        
        // Auto-apply filter when status changes
        if (status) {
            transactionMasterTbl.ajax.reload();
        }
    });

    // Lead type filter change handler
    $("#serviceTypeFilter").on("change", function () {
        updateStatusOptions();
        
        // Auto-apply filter when type changes
        if ($(this).val()) {
            transactionMasterTbl.ajax.reload();
        }
    });

    // Reset button handler
    $("#resetBtn").on("click", function () {
        // Clear all filters
        $("#transactionDateRange").val("");
        $("#transactionDate").val("");
        $("#serviceTypeFilter").val("");
        $("#leadStatus").val("");

        // Clear date pickers
        if (dateRangePicker) dateRangePicker.clear();
        if (singleDatePicker) singleDatePicker.clear();

        // Enable both date inputs
        $("#transactionDate").prop("disabled", false);
        $("#transactionDateRange").prop("disabled", false);

        // Reset column title
        $("#transactionMasterTbl thead th").eq(3).text("New");

        // Reset status options
        updateStatusOptions();

        // Reload table
        transactionMasterTbl.ajax.reload(function() {
            showNotification("All filters have been reset.", "info");
        });
    });

    // Prevent both date inputs from being used simultaneously
    $("#transactionDateRange").on("change", function () {
        const hasValue = !!$(this).val();
        $("#transactionDate").prop("disabled", hasValue);
        if (hasValue) {
            $("#transactionDate").val("");
            if (singleDatePicker) singleDatePicker.clear();
        }
    });

    $("#transactionDate").on("change", function () {
        const hasValue = !!$(this).val();
        $("#transactionDateRange").prop("disabled", hasValue);
        if (hasValue) {
            $("#transactionDateRange").val("");
            if (dateRangePicker) dateRangePicker.clear();
        }
    });

    // Utility function for notifications
    function showNotification(message, type = "info") {
        // You can use Toastr or any other notification library
        // For now, using alert as fallback
        const bgColor = type === "error" ? "#f44336" : 
                       type === "success" ? "#4CAF50" : 
                       type === "warning" ? "#ff9800" : "#2196F3";
        
        // Simple toast notification
        if (typeof Toastr !== "undefined") {
            toastr[type](message);
        } else {
            // Fallback alert
            alert(message);
        }
    }

    // Export functionality
    $(document).on('click', '.export-btn', function() {
        const format = $(this).data('format');
        exportData(format);
    });

    function exportData(format) {
        const params = {
            dateRange: $("#transactionDateRange").val(),
            singleDate: $("#transactionDate").val(),
            leadType: $("#serviceTypeFilter").val(),
            leadStatus: $("#leadStatus").val(),
            export: true,
            format: format
        };

        // Create download URL
        const url = "./services/agent-lead-fetch.php?" + $.param(params);
        window.open(url, '_blank');
    }

    // Auto-refresh data every 30 seconds
    setInterval(function() {
        if ($("#transactionMasterTbl").is(":visible")) {
            transactionMasterTbl.ajax.reload(null, false); // false means don't reset paging
        }
    }, 30000);
});