$(document).ready(function () {
    var transactionMasterTbl = $("#transactionMasterTbl").DataTable({
        ajax: {
            url: "./services/agent-lead-fetch.php",
            type: "POST",
            data: function (d) {
                d.dateRange  = $("#transactionDateRange").val();
                d.singleDate = $("#transactionDate").val();
                d.leadType   = $("#serviceTypeFilter").val();
                 d.leadStatus = $("#leadStatus").val(); 
            },
            dataType: "json",
            dataSrc: function (json) {
                if (!json.success) {
                    console.error("Server error:", json.message);
                    return [];
                }
                return json.data || [];
            }
        },
        columns: [
            { data: "sno" },
            { data: "agent_name" },
            { data: "leads_handled" },
            { data: "leads_in_hand" },
            { data: "leads_converted" },
            { data: "leads_lost" }
        ]
    });

    // Filter button
    $("#filterBtn").on("click", function () {
        const dateRange = $("#transactionDateRange").val();
        const singleDate = $("#transactionDate").val();

        if (dateRange && singleDate) {
            alert("Please select either Date Range OR Single Date, not both.");
            return;
        }

        transactionMasterTbl.ajax.reload();
    });
    $("#leadStatus").on("change", function () {
        var status = $(this).val();
        var newTitle = "Leads Converted"; // default

        if (status) {
            if (status === "New") newTitle = "New Leads";
            else if (status === "Contacted") newTitle = "Contacted Leads";
            else if (status === "Following") newTitle = "Following Up Leads";
            else if (status === "Converted") newTitle = "Converted Leads";
            else if (status === "Lost") newTitle = "Lost Leads";
            else if (status === "Emailed and Waiting for reply") newTitle = "Waiting for Reply Leads"; // email lead
        }

        // Change header text
        $("#transactionMasterTbl thead th").eq(4).text(newTitle);

        // Reload DataTable
        transactionMasterTbl.ajax.reload();
    });
    $("#filterBtn").on("click", function () { transactionMasterTbl.ajax.reload(); });
    // Reset button
    $("#resetBtn").on("click", function () {
        $("#transactionDateRange").val("");
        $("#transactionDate").val("");
        $("#serviceTypeFilter").val("");

        if (typeof dateRangePicker !== "undefined") dateRangePicker.clear();
        if (typeof singleDatePicker !== "undefined") singleDatePicker.clear();

        $("#transactionDate").prop("disabled", false);
        $("#transactionDateRange").prop("disabled", false);

        transactionMasterTbl.ajax.reload();
    });

    // Auto-filter on lead type change
    $("#serviceTypeFilter").on("change", function () {
        transactionMasterTbl.ajax.reload();
    });

    // Prevent both date fields
    $("#transactionDateRange").on("change", function () {
        $("#transactionDate").prop("disabled", !!$(this).val());
    });
    $("#transactionDate").on("change", function () {
        $("#transactionDateRange").prop("disabled", !!$(this).val());
    });
});
