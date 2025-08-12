var manageLeadDatatable, currentLead;
var agents = [];
var userType = '';
$(document).ready(function () {
    // Promise to fetch agents
    function fetchAgents() {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "GET",
                url: "./services/agent_fetch_few_details.php",
                dataType: "json",
                success: function (response) {
                    if (response.success == true) {
                        agents = {};
                        response.data.forEach((agent) => {
                            agents[Number(agent.id)] = agent.name || "Unknown Agent";
                        });
                        agents[0] = "Admin"; // Ensure Admin is always available
                        console.log("Agents loaded:", agents); // Debug: Verify agents object
                        resolve();
                    } else {
                        let errorMessage = "";
                        switch (response.message) {
                            case "Exception":
                                errorMessage = "An error occurred while fetching Agents. Please contact system admin...!";
                                break;
                            case "Invalid Request":
                                errorMessage = "Invalid request...!";
                                break;
                            default:
                                errorMessage = "Failed to get agents. Please contact system admin!";
                                break;
                        }
                        alert(errorMessage);
                        agents[0] = "Admin"; // Fallback for admin
                        resolve();
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching agents:", error, xhr.responseText);
                    agents[0] = "Admin"; // Fallback for admin
                    alert("Failed to fetch agents. Please try again.");
                    resolve();
                }
            });
        });
    }

    // Initialize DataTable after agents are fetched
    fetchAgents().then(() => {
        manageLeadDatatable = $("#leadMasterTbl").DataTable({
            method: "POST",
            scrollX: true,
            ajax: {
                url: "./services/unassigned_call_fetch.php",
                dataType: "json",
            },
        });
    });

    // Handle form submission
    $("#addLeadDataBtn").on("click", function (e) {
        e.preventDefault();
        var data = $("#addLeadForm").serialize();
        $.ajax({
            type: "POST",
            url: "./services/lead_call_add.php",
            data: data,
            dataType: "json",
            success: function (response) {
                if (response.success == true) {
                    manageLeadDatatable.ajax.reload(null, true);
                    $("#addLeadModal").modal("hide");
                    $("#addLeadForm")[0].reset();
                } else {
                    let errorMessage = "";
                    switch (response.message) {
                        case "Mandatory":
                            errorMessage = "Please make sure you filled all the mandatory fields...!";
                            break;
                        case "Duplicate":
                            errorMessage = "This Call Lead Entry is repeated... Please check the details once again";
                            break;
                        case "Exception":
                            errorMessage = "An error occurred while creating a new Call Lead. Please contact system admin...!";
                            break;
                        case "Invalid Request":
                            errorMessage = "Invalid request...!";
                            break;
                        default:
                            errorMessage = "Failed to create Call Lead. Please contact system admin!";
                            break;
                    }
                    alert(errorMessage);
                }
            },
            error: function () {
                alert("Error creating Call Lead.");
            }
        });
    });
});

// Helper function to get user name by ID - Modified existing version
function getUserNameById(userId) {
    // First check if userId is valid
    if (!userId || userId === "0" || userId === 0) {
        return "Admin";
    }
    
    // Check if agents exists and has this userId in any format
    if (agents) {
        // Check direct match
        if (agents[userId] !== undefined) return agents[userId];
        
        // Check numeric version if userId is string
        const numId = Number(userId);
        if (!isNaN(numId) && agents[numId] !== undefined) return agents[numId];
        
        // Check all keys for match (handles string vs number cases)
        for (const id in agents) {
            if (id == userId) { // Loose equality comparison
                return agents[id];
            }
        }
    }
    
    // Final fallback - return ID only
    return userId;
}

// Existing assignee helper function (unchanged)
function getAssigneeNameById(userId) {
    if (!userId || userId === "0" || userId === 0 || userId === "") {
        return "Unassigned";
    }
    return getUserNameById(userId);
}


function removeLead(params = null) {
    if (params) {
        $.ajax({
            type: "POST",
            url: "./services/lead_call_remove.php",
            data: { leadId: params },
            dataType: "json",
            success: function (response) {
                if (response.success == true) {
                    manageLeadDatatable.ajax.reload(null, true);
                } else {
                    alert("Failed to Remove Lead...!");
                }
            },
            error: function () {
                alert("Failed to Remove Lead");
            }
        });
    }
}



function createCommentHtml(status, creatorName, date) {
    return `<li class="d-block">
        <div class="form-check w-100">
            <label class="form-check-label m-0">Lead created <i class="input-helper rounded"></i></label>
            <div class="d-flex mt-2">
                <div class="badge badge-opacity-info me-3">${status}</div>
                <div class="text-small me-3">On <strong>${formatDate(date) || 'Unknown Date'}</strong></div>
                <div class="text-small me-3">By <strong>${creatorName}</strong></div>
            </div>
        </div>
    </li>`;
}

// Helper function to format date
function formatDateTime(dateString) {
    if (!dateString) return 'Unknown Date';

    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return 'Invalid Date';

        // Format date as "Jun 15, 2023 02:30 PM"
        return date.toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    } catch (e) {
        console.error('Date formatting error:', e);
        return dateString; // Return the original string if formatting fails
    }
}

// Keep this for backward compatibility
function formatDate(dateString) {
    return formatDateTime(dateString);
}
function viewLead(params = null) {
    if (params) {
        $.ajax({
            type: "POST",
            url: "./services/lead_call_fetch_single.php",
            data: { leadId: params },
            dataType: "json",
            success: function (response) {
                console.log("Lead fetch response:", response);
                if (response.success == true && response.data && response.data.length > 0) {
                    currentLead = response.data[0];

                    $("#viewLeadModal").modal("show");
                    $("#currentLeadCode").text(response.data[0].lead_name || "N/A");

                    // Populate fields
                    $("#viewLeadForm #leadNm").val(response.data[0].lead_name || "").attr("readonly", true);
                    $("#viewLeadForm #email").val(response.data[0].email || "").attr("readonly", true);
                    $("#viewLeadForm #companyNm").val(response.data[0].company_name || "").attr("readonly", true);
                    $("#viewLeadForm #contact").val(response.data[0].contact || "").attr("readonly", true);
                    $("#viewLeadForm #requirement").val(response.data[0].requirement || "").attr("readonly", true);
                    $("#viewLeadForm #description").val(response.data[0].description || "").attr("readonly", true);
                    $("#viewLeadForm #notes").val(response.data[0].notes || "").attr("readonly", true);
                    $("#viewLeadForm #addressLn").val(response.data[0].address_line || "").attr("readonly", true);
                    $("#viewLeadForm #area").val(response.data[0].area || "").attr("readonly", true);
                    $("#viewLeadForm #city").val(response.data[0].city || "").attr("readonly", true);
                    $("#viewLeadForm #pincode").val(response.data[0].pincode || "").attr("readonly", true);
                    $("#viewLeadForm #followUpDt").val(response.data[0].follow_up_date || "").attr("readonly", true);
                    $("#viewLeadForm #leadStatus").val(response.data[0].lead_status || "").attr("disabled", true);
                    $("#viewLeadForm #createdBy").val(getUserNameById(response.data[0].created_by)).attr("readonly", true);
                    $("#viewLeadForm #updatedBy").val(getUserNameById(response.data[0].updated_by)).attr("readonly", true);
                    $("#viewLeadForm #assignee").val(getUserNameById(response.data[0].assignee)).attr("readonly", true);

                    // Display history using the new structure
                    if (response.data[0].history && Array.isArray(response.data[0].history)) {
                        displayLeadHistory(response.data[0].history);
                    } else {
                        // Fallback to old structure if needed
                        let history = [];
                        try {
                            if (response.data[0].log) {
                                history = JSON.parse(response.data[0].log);
                            }
                        } catch (e) {
                            console.error("Error parsing history:", e);
                        }

                        if (history.length === 0) {
                            // Create default history entry
                            history = [{
                                action: 'created',
                                changed_by: getUserNameById(response.data[0].created_by),
                                date: response.data[0].created_at || new Date().toISOString(),
                                changes: [],
                                status_change: {
                                    from: '',
                                    to: response.data[0].lead_status
                                }
                            }];
                        }

                        displayLeadHistory(history);
                    }
                } else {
                    alert("Failed to Fetch Lead: " + (response.message || "No data found"));
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching lead:", error, xhr.responseText);
                alert("Failed to Fetch Lead");
            }
        });
    }
}

function editLead(leadId = null) {
    if (leadId) {
        $.ajax({
            type: "POST",
            url: "./services/lead_call_fetch_single.php",
            data: { leadId: leadId },
            dataType: "json",
            success: function (response) {
                console.log("Edit lead fetch response:", response);
                if (response.success === true && response.data && response.data.length > 0) {
                    const lead = response.data[0];
                    currentLead = lead;
                    
                    // Clear any existing hidden fields
                    $("#editLeadForm input[name='lId']").remove();
                    
                    // Populate modal fields
                    $("#currentEditLeadCode").text(lead.lead_name || "N/A");
                    $("#editLeadForm #leadNm").val(lead.lead_name || "");
                    $("#editLeadForm #email").val(lead.email || "");
                    $("#editLeadForm #companyNm").val(lead.company_name || "");
                    $("#editLeadForm #contact").val(lead.contact || "").attr("readonly", true);
                    $("#editLeadForm #requirement").val(lead.requirement || "");
                    $("#editLeadForm #description").val(lead.description || "");
                    $("#editLeadForm #notes").val(lead.notes || "");
                    $("#editLeadForm #addressLn").val(lead.address_line || "");
                    $("#editLeadForm #area").val(lead.area || "");
                    $("#editLeadForm #city").val(lead.city || "");
                    $("#editLeadForm #pincode").val(lead.pincode || "");
                    $("#editLeadForm #followUpDt").val(lead.follow_up_date || "");
                    $("#editLeadForm #leadStatus").val(lead.lead_status || "");

                    // Populate assignee dropdown
                    $("#editLeadForm #assignee").empty();
                    
                    // Add unassigned option for admins only
                    if (userType === "admin") {
                        $("#editLeadForm #assignee").append('<option value="">-- Select Assignee --</option>');
                    }
                    
                    // Populate agents
                    $.each(agents, function(id, name) {
                        if (id != 0) { // Skip admin from dropdown
                            const selected = (id == lead.assignee) ? 'selected' : '';
                            $("#editLeadForm #assignee").append(`<option value="${id}" ${selected}>${name}</option>`);
                        }
                    });

                    // Handle assignee field based on user type
                    if (userType === "agent") {
                        // For agents: make assignee field non-editable
                        $("#editLeadForm #assignee").prop("disabled", true);
                        // Show current assignee even if disabled
                        if (lead.assignee) {
                            $("#editLeadForm #assignee").val(lead.assignee);
                        }
                    } else {
                        // For admins: keep it editable
                        $("#editLeadForm #assignee").prop("disabled", false);
                    }

                    // Add hidden lead ID field
                    $("#editLeadForm").append('<input type="hidden" name="lId" value="' + lead.id + '" />');
                    
                    // Show modal
                    $("#editLeadModal").modal("show");
                    
                    // Handle edit form submission
                    $("#editLeadDataBtn").off("click").on("click", function (e) {
                        e.preventDefault();
                        
                        let formData = $("#editLeadForm").serialize();
                        
                        // If agent and assignee is disabled, we need to add the current assignee to form data
                        if (userType === "agent" && $("#editLeadForm #assignee").prop("disabled")) {
                            formData += "&assignee=" + (lead.assignee || "");
                        }
                        
                        $.ajax({
                            type: "POST",
                            url: "./services/lead_call_edit.php",
                            data: formData,
                            dataType: "json",
                            success: function (response) {
                                if (response.success === true) {
                                    $("#editLeadForm")[0].reset();
                                    $("#editLeadModal").modal("hide");
                                    manageLeadDatatable.ajax.reload(null, true);
                                    showToast("Lead updated successfully");
                                } else {
                                    alert("Failed to update lead: " + (response.message || "Unknown error"));
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error("Update error:", error, xhr.responseText);
                                alert("Error updating lead. Please try again.");
                            }
                        });
                    });
                } else {
                    alert("Failed to fetch lead details: " + (response.message || "No data found"));
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching lead details:", error, xhr.responseText);
                alert("Error fetching lead details.");
            }
        });
    }
}

function displayLeadHistory(history) {
    const historyContainer = $("#pastCommentsOfThisLead");
    historyContainer.empty();

    if (!history || !Array.isArray(history) ){
        historyContainer.append('<li class="text-center py-4 text-muted">No history available</li>');
        return;
    }

    // Sort by date (newest first)
    history.sort((a, b) => new Date(b.date || b.created_at) - new Date(a.date || a.created_at));

    if (history.length === 0) {
        historyContainer.append('<li class="text-center py-4 text-muted">No history available</li>');
        return;
    }

    history.forEach((entry, index) => {
        if (!entry) return;

        // Determine action type and styling
        const action = entry.action || 'updated';
        let actionClass = 'text-muted';
        let actionIcon = '<i class="fa fa-clock"></i>';
        
        if (action === 'created') {
            actionClass = 'text-success';
            actionIcon = '<i class="fa fa-plus-circle"></i>';
        } else if (action === 'updated') {
            actionClass = 'text-info';
            actionIcon = '<i class="fa fa-edit"></i>';
        }

        // Prepare changed by information
        const changedBy = entry.changed_by || 
                         entry.user_id || 
                         (action === 'created' ? currentLead.created_by : currentLead.updated_by) || 
                         'Unknown User';
        const changedByName = typeof changedBy === 'number' || typeof changedBy === 'string' 
                           ? getUserNameById(changedBy) 
                           : changedBy;

        // Prepare date
        const entryDate = entry.date || entry.created_at || entry.timestamp;
        const formattedDate = formatDateTime(entryDate);

        // Prepare changes list
        let changesHtml = '';
        if (entry.changes && Array.isArray(entry.changes)) {
            changesHtml = '<div class="changes-list mt-2">';
            entry.changes.forEach(change => {
                if (change && change.field) {
                    changesHtml += `
                        <div class="change-item small">
                            <strong>${change.field}:</strong> 
                            <span class="text-muted">${change.old_value || 'empty'}</span> 
                            <i class="fa fa-arrow-right mx-1"></i> 
                            <span class="text-primary">${change.new_value || 'empty'}</span>
                        </div>
                    `;
                }
            });
            changesHtml += '</div>';
        }

        // Prepare status change
        let statusChangeHtml = '';
        const statusChange = entry.status_change || {};
        if (statusChange.from !== undefined || statusChange.to !== undefined) {
            const fromStatus = statusChange.from || (action === 'created' ? 'None' : 'Unknown');
            const toStatus = statusChange.to || 'Unknown';
            
            statusChangeHtml = `
                <div class="status-change mt-2">
                    <strong>Status:</strong> 
                    <span class="badge bg-light text-dark">${fromStatus}</span> 
                    <i class="fa fa-arrow-right mx-1"></i> 
                    <span class="badge bg-primary">${toStatus}</span>
                </div>
            `;
        }

        // Create the history item HTML
        const historyItem = `
            <li class="d-flex border-bottom py-3 ${index === 0 ? 'border-top' : ''}">
                <div class="flex-shrink-0 me-3">
                    <div class="history-icon ${actionClass}">
                        ${actionIcon}
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-bold ${actionClass}">
                                ${changedByName} ${action} this lead
                            </div>
                            <div class="text-muted small">
                                ${formattedDate}
                            </div>
                        </div>
                    </div>
                    ${changesHtml}
                    ${statusChangeHtml}
                </div>
            </li>
        `;

        historyContainer.append(historyItem);
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