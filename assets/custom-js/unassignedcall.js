var manageLeadDatatable, currentLead;
var agents = [];

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



// Helper function to create comment HTML
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
function formatDate(dateString) {
    if (!dateString) return null;
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    } catch (e) {
        return null;
    }
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

                    // Get creator ID and name
                    let creatorId = response.data[0].created_by;
                    let creatorName = getUserNameById(creatorId);
                    console.log("Creator Info - ID:", creatorId, "Name:", creatorName);

                    // Populate history
                    let commentsHtml = '';
                    let comments = response.data[0].log || '';

                    // Process all comments including "Newly Added"
                    if (comments && comments.trim() !== '') {
                        try {
                            let parsedComments = comments.startsWith('[') ? JSON.parse(comments) : JSON.parse('[' + comments + ']');
                            if (Array.isArray(parsedComments) && parsedComments.length > 0) {
                                parsedComments.forEach(function(comment) {
                                    let commenterId = comment.commentBy || creatorId;
                                    let commenterName = getUserNameById(commenterId);
                                    
                                    commentsHtml += '<li class="d-block">';
                                    commentsHtml += '<div class="form-check w-100">';
                                    commentsHtml += '<label class="form-check-label m-0">';
                                    commentsHtml += (comment.message ? $('<div/>').text(comment.message).html() : 'Lead created') + ' <i class="input-helper rounded"></i></label>';
                                    
                                    // Status badge
                                    let badgeClass = "badge-opacity-light";
                                    switch (comment.status) {
                                        case "Newly Added": badgeClass = "badge-opacity-info"; break;
                                        case "Contacted": badgeClass = "badge-opacity-purple"; break;
                                        case "Converted": badgeClass = "badge-opacity-success"; break;
                                        case "Following": badgeClass = "badge-opacity-warning"; break;
                                        case "Lost": badgeClass = "badge-opacity-danger"; break;
                                    }
                                    
                                    commentsHtml += `<div class="d-flex mt-2"><div class="badge ${badgeClass} me-3">${comment.status || 'Unknown'}</div>`;
                                    commentsHtml += `<div class="text-small me-3">On <strong>${comment.date ? formatDate(comment.date) : formatDate(response.data[0].created_at) || 'Unknown Date'}</strong></div>`;
                                    commentsHtml += `<div class="text-small me-3">By <strong>${commenterName}</strong></div></div>`;
                                    commentsHtml += '</div></li>';
                                });
                            } else {
                                // Fallback for no comments
                                commentsHtml = createCommentHtml("Newly Added", creatorName, response.data[0].created_at);
                            }
                        } catch (e) {
                            console.error("Error parsing log:", e, comments);
                            // Fallback if parsing fails
                            commentsHtml = createCommentHtml("Newly Added", creatorName, response.data[0].created_at);
                        }
                    } else {
                        // No comments case
                        commentsHtml = createCommentHtml("Newly Added", creatorName, response.data[0].created_at);
                    }
                    
                    $("#viewLeadForm #pastCommentsOfThisLead").html(commentsHtml);
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
                console.log("Edit lead fetch response:", response); // Debug: Log response
                if (response.success === true && response.data && response.data.length > 0) {
                    const lead = response.data[0];
                    currentLead = lead;
                    $("#currentLeadCode").text(lead.lead_name || "N/A");

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
                    $("#editLeadForm #assignee").val(getUserNameById(lead.assignee)).attr("readonly", true);

                    $("#editLeadForm").append('<input type="hidden" name="lId" id="lId" value="' + lead.id + '" />');

                    // Show the modal
                    $("#editLeadModal").modal("show");
                    // Populate assignee dropdown
                    $("#editLeadForm #assignee").empty();
                    $("#editLeadForm #assignee").append('<option value="">Unassigned</option>');
                    $.each(agents, function(id, name) {
                        if (id != 0) {
                            const selected = (id == lead.assignee_id) ? 'selected' : '';
                            $("#editLeadForm #assignee").append(`<option value="${id}" ${selected}>${name}</option>`);
                        }
                    });


                    // Handle edit form submission
                    $("#editLeadDataBtn").unbind("click").bind("click", function (e) {
                        e.preventDefault();
                        const formData = $("#editLeadForm").serialize();
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
                                } else {
                                    alert("Failed to update lead details: " + (response.message || "Unknown error"));
                                }
                            },
                            error: function () {
                                alert("Error updating lead details.");
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