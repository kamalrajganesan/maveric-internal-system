var manageLeadDatatable, currentLead;
var agents = [];
var selectedLeads = [];

$(document).ready(function () {
    // Load agents data first
    $.ajax({
        type: "GET",
        url: "./services/agent_fetch_few_details.php",
        dataType: "json",
        success: function (response) {
            if (response.success) {
                agents = {};
                response.data.forEach(agent => {
                    agents[agent.id] = agent.name;
                });
                agents[0] = "Admin"; // Ensure Admin is always available
                initializeLeadDatatable();
            } else {
                console.error("Failed to load agents");
                agents = {0: "Admin"};
                initializeLeadDatatable();
            }
        },
        error: function() {
            console.error("Error loading agents");
            agents = {0: "Admin"};
            initializeLeadDatatable();
        }
    });

    function initializeLeadDatatable() {
        manageLeadDatatable = $('#leadMasterTbl').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    text: 'Act on selected data',
                    action: function () {
                        selectedLeads = [];
                        manageLeadDatatable.rows({ selected: true }).every(function() {
                            let rowData = this.data();
                            let temp = [];
                            temp.push(Number(rowData[5].match(/viewLead\((\d+)\)/)[1]));
                            temp.push(rowData[0]);
                            selectedLeads.push(temp);
                        });
                        openMultiActionModal();
                    }
                }
            ],
            select: {
                style: 'multi',
                selector: 'td.select-checkbox'
            },
            responsive: true,
            processing: true,
            serverSide: false,
            ajax: {
                url: "./services/lead_email_fetch_all.php",
                type: 'POST',
                data: function(d) {
                    d.assignedOnly = true;
                    d.param = '';
                },
                dataSrc: function(json) {
                    if (json.success) {
                        return json.data;
                    } else {
                        console.error(json.message);
                        return [];
                    }
                }
            },
            columns: [
                { 
                    data: null,
                    defaultContent: '',
                    className: 'select-checkbox',
                    orderable: false
                },
                { data: 0 }, // Email
                { data: 1 }, // Contact
                { data: 2 }, // Company Name
                { data: 3 }, // Status
                { data: 4 }, // Follow up
                { data: 5 }  // Actions
            ],
            order: [[1, 'asc']]
        });

        // Handle "Select All" checkbox functionality
        $('#selectAllCheckbox').on('click', function() {
            var isChecked = $(this).is(':checked');
            if (isChecked) {
                manageLeadDatatable.rows({ page: 'current' }).select();
            } else {
                manageLeadDatatable.rows().deselect();
            }
        });

        // Update "Select All" checkbox state
        manageLeadDatatable.on('select deselect', function() {
           
            var totalRows = manageLeadDatatable.rows({ page: 'current' }).count();
            var selectedRows = manageLeadDatatable.rows({ selected: true, page: 'current' }).count();
            
            if (selectedRows === 0) {
                $('#selectAllCheckbox').prop('indeterminate', false);
                $('#selectAllCheckbox').prop('checked', false);
            } else if (selectedRows === totalRows) {
                $('#selectAllCheckbox').prop('indeterminate', false);
                $('#selectAllCheckbox').prop('checked', true);
            } else {
                $('#selectAllCheckbox').prop('indeterminate', true);
            }
        });

        // Reset select all checkbox when table is redrawn
        manageLeadDatatable.on('draw', function() {
            $('#selectAllCheckbox').prop('checked', false);
            $('#selectAllCheckbox').prop('indeterminate', false);
        });
    }

    // Helper function to get user name by ID
    function getUserNameById(userId) {
        if (!userId || userId === "0" || userId === 0) {
            return "Admin";
        }
        return agents[userId] || `Unknown User' (${userId})`;
    }

    // Helper function for assignee name
    function getAssigneeNameById(userId) {
        if (!userId || userId === "0" || userId === 0 || userId === "") {
            return "Unassigned";
        }
        return getUserNameById(userId);
    }

    // Handle form submission for adding a lead
    $("#addLeadDataBtn").on("click", function (e) {
        e.preventDefault();
        var data = $("#addLeadForm").serialize();

        $.ajax({
            type: "POST",
            url: "./services/lead_email_add.php",
            data: data,
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    $("#addLeadForm")[0].reset();
                    $("#addLeadModal").modal("hide");
                    manageLeadDatatable.ajax.reload(null, true);
                } else {
                    let errorMessage;
                    switch (response.message) {
                        case "Mandatory":
                            errorMessage = "Please fill all mandatory fields.";
                            break;
                        case "Duplicate":
                            errorMessage = "This email is already in use.";
                            break;
                        case "Exception":
                            errorMessage = "An error occurred while creating the lead.";
                            break;
                        case "Invalid Request":
                            errorMessage = "Invalid request.";
                            break;
                        default:
                            errorMessage = "Failed to create lead.";
                            break;
                    }
                    alert(errorMessage);
                }
            },
            error: function () {
                alert("Error creating lead.");
            }
        });
    });
});

function removeLead(params = null) {
    if (params) {
        $.ajax({
            type: "POST",
            url: "./services/lead_email_remove.php",
            data: { leadId: params },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    manageLeadDatatable.ajax.reload(null, true);
                } else {
                    alert("Failed to remove lead.");
                }
            },
            error: function () {
                alert("Error removing lead.");
            }
        });
    }
}

function viewLead(leadId) {
    if (!leadId) return;

    $.ajax({
        type: "POST",
        url: "./services/lead_email_fetch_single.php",
        data: { leadId: leadId },
        dataType: "json",
        success: function (response) {
            if (response.success && response.data) {
                const lead = response.data[0];
                $("#viewLeadModal").modal("show");
                $("#currentLeadCode").text(lead.lead_name || "N/A");

                // Populate fields
                $("#viewLeadForm #leadNm").val(lead.lead_name || "").prop("readonly", true);
                $("#viewLeadForm #email").val(lead.email || "").prop("readonly", true);
                $("#viewLeadForm #companyNm").val(lead.company_name || "").prop("readonly", true);
                $("#viewLeadForm #contact").val(lead.contact || "").prop("readonly", true);
                $("#viewLeadForm #requirement").val(lead.requirement || "").prop("readonly", true);
                $("#viewLeadForm #description").val(lead.description || "").prop("readonly", true);
                $("#viewLeadForm #notes").val(lead.notes || "").prop("readonly", true);
                $("#viewLeadForm #addressLn").val(lead.address_line || "").prop("readonly", true);
                $("#viewLeadForm #area").val(lead.area || "").prop("readonly", true);
                $("#viewLeadForm #city").val(lead.city || "").prop("readonly", true);
                $("#viewLeadForm #pincode").val(lead.pincode || "").prop("readonly", true);
                $("#viewLeadForm #followUpDt").val(lead.follow_up_date || "").prop("readonly", true);
                $("#viewLeadForm #leadStatus").val(lead.lead_status || "").prop("readonly", true);
                $("#viewLeadForm #createdBy").val(lead.created_by || "Admin").prop("readonly", true);
                $("#viewLeadForm #updatedBy").val(lead.updated_by || "Admin").prop("readonly", true);
                $("#viewLeadForm #assignee").val(lead.assignee || "Unassigned").prop("readonly", true);
            } else {
                alert("Error: " + (response.message || "No data received"));
            }
        },
        error: function(xhr, status, error) {
            alert("AJAX Error: " + error);
        }
    });
}

function editLead(leadId = null) {
    if (!leadId) return;

    $.ajax({
        type: "POST",
        url: "./services/lead_email_fetch_single.php",
        data: { leadId: leadId },
        dataType: "json",
        success: function (response) {
            if (response.success && response.data) {
                const lead = response.data[0];
                currentLead = lead;
                $("#currentEditLeadCode").text(lead.lead_name || "N/A");

                // Populate fields
                $("#editLeadForm #leadNm").val(lead.lead_name || "");
                $("#editLeadForm #email").val(lead.email || "").attr("readonly", true);
                $("#editLeadForm #companyNm").val(lead.company_name || "");
                $("#editLeadForm #contact").val(lead.contact || "");
                $("#editLeadForm #requirement").val(lead.requirement || "");
                $("#editLeadForm #description").val(lead.description || "");
                $("#editLeadForm #notes").val(lead.notes || "");
                $("#editLeadForm #addressLn").val(lead.address_line || "");
                $("#editLeadForm #area").val(lead.area || "");
                $("#editLeadForm #city").val(lead.city || "");
                $("#editLeadForm #pincode").val(lead.pincode || "");
                
                // Fix follow-up date formatting - extract only date part if datetime exists
                let followUpDateValue = "";
                if (lead.follow_up_date && lead.follow_up_date !== null && lead.follow_up_date !== "") {
                    // Extract date part from datetime (YYYY-MM-DD HH:MM:SS -> YYYY-MM-DD)
                    followUpDateValue = lead.follow_up_date.split(' ')[0];
                }
                $("#editLeadForm #followUpDt").val(followUpDateValue);
                
                // Fix lead status - ensure proper selection
                $("#editLeadForm #leadStatus").val(lead.lead_status || "");

                // Handle assignee display
                if (lead.assignee_id && lead.assignee_id != 0) {
                    const assigneeName = agents[lead.assignee_id] || 'Assigned';
                    $("#editLeadForm #assignee").replaceWith(`
                        <input type="text" class="form-control" value="${assigneeName}" readonly>
                        <input type="hidden" name="assignee" value="${lead.assignee_id}">
                    `);
                }

                // Disable for agents
                if (userType === "agent") {
                    $("#editLeadForm #assignee").prop("disabled", true);
                }

                // Hidden ID field
                $("#editLeadForm #lId").remove(); // avoid duplicates
                $("#editLeadForm").append(`<input type="hidden" name="lId" id="lId" value="${lead.id}" />`);

                // Show modal
                $("#editLeadModal").modal("show");

                // Handle edit form submission
                $("#editLeadDataBtn").off("click").on("click", function (e) {
                    e.preventDefault();

                    // Client-side follow-up date validation
                    const followUpDate = $("#followUpDt").val();
                    if (followUpDate && !/^\d{4}-\d{2}-\d{2}$/.test(followUpDate)) {
                        alert("Please enter date in YYYY-MM-DD format");
                        return false;
                    }

                    // Add original follow-up datetime to preserve time component
                    const formData = $("#editLeadForm").serialize() + 
                        "&originalFollowUpDateTime=" + encodeURIComponent(lead.follow_up_date || "");

                    $.ajax({
                        type: "POST",
                        url: "./services/lead_email_edit.php",
                        data: formData,
                        dataType: "json",
                        success: function (resp) {
                            if (resp.success) {
                                $("#editLeadForm")[0].reset();
                                $("#editLeadModal").modal("hide");
                                manageLeadDatatable.ajax.reload(null, true);
                            } else {
                                alert("Error: " + resp.message);
                            }
                        },
                        error: function () {
                            alert("Error updating lead details.");
                        }
                    });
                });

            } else {
                alert("Failed to fetch lead details.");
            }
        },
        error: function () {
            alert("Error fetching lead details.");
        }
    });
}


function openMultiActionModal() {
    $(".selectedLeadCount").empty();
    $("#multiActionLeadForm")[0].reset();

    var selectedEmails = selectedLeads.map(function(innerArray) {
        return innerArray[1];
    });
    
    let dispHtml = "You have selected " + selectedLeads.length + " record(s), the emails are: <br>";
    for (let i = 0; i < selectedLeads.length; i++) {
        dispHtml += "<strong>" + selectedEmails[i] + " </strong><br> ";
    }
    $(".selectedLeadCount").append(dispHtml);
    
    $("#multiActionLeadModal").modal("show");

    $("#multiActionDataBtn").off("click").on("click", function(e) {
        e.preventDefault();
        
        const formData = {
            followUpDt: $("#multiActionLeadForm #followUpDt").val(),
            leadStatus: $("#multiActionLeadForm #leadStatus").val(),
            leads: selectedLeads
        };
        
        const cleanData = {};
        for (const key in formData) {
            if (formData[key] !== null && formData[key] !== '' && formData[key] !== undefined) {
                cleanData[key] = formData[key];
            }
        }
        
        $.ajax({
            type: "POST",
            url: "./services/lead_email_multi_action.php",
            data: cleanData,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    manageLeadDatatable.ajax.reload(null, true);
                    $("#multiActionLeadModal").modal("hide");
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function() {
                alert("Error updating multiple leads.");
            }
        });
    });
}