<?php require_once("./shared/components/pre-header.php");

if (isset($_GET['page'])) {
    $page = htmlspecialchars($_GET['page']);

    echo "<script>
        var agent_page = '" . $page . "'
    </script>";
} else {
    echo "Check the URL param";
}

?>
<title> My Agents - Tejas </title>

<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">

<?php require_once("./shared/components/post-header.php");  ?>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="row">
            <div class="col-sm-12 short-l">
                <div class="d-sm-flex align-items-center justify-content-end border-bottom">
                    <div>
                        <div class="btn-wrapper">
                            <button class="btn btn-otline-dark align-items-center" data-bs-toggle="modal" data-bs-target="#addAgentModal" id="addAgentModalBtn">
                                <i class="fa fa-plus"></i> Add New Agent
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row py-3">
            <div class="col-sm-12">
                <div class="card card-rounded">
                    <div class="card-body fs-14">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="agentMasterTbl" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>City/ Pincode</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- content-wrapper ends -->

    <!-- modals -->

    <!-- add agent modal -->
    <div class="modal fade" id="addAgentModal" tabindex="-1" role="dialog" aria-labelledby="addAgentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addAgentModalLabel">Add New Agent</h4>
                    <button type="button" class="btn btn-inverse-light btn-fw" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="addAgentForm" class="form-sample">
                        <div class="row justify-content-center">
                            <div class="col-sm-5">
                                <div class="form-group view-form-group">
                                    <label for="agentName">Agent Name <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="agentName" name="agentName" placeholder="Agent Name" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="companyName">Company Name <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="companyName" name="companyName" placeholder="Company Name" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="contact">Contact <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="email">Email <strong><code>*</code></strong></label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="address">Address <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Address">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="area">Area</label>
                                    <input type="text" class="form-control" id="area" name="area" placeholder="Area">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="pincode">Pincode <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode" required>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group view-form-group">
                                    <label for="serviceType[]"> Service Type <strong><code>*</code></strong> </label>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" name="serviceType[]" value="AMC"> AMC </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="serviceType[]" value="Tally Subscription"> Tally Subscription </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="serviceType[]" value="One Time"> One Time </label>
                                    </div>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="city">City <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="city" name="city" placeholder="City" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="serviceStartDate">Service Start Date <strong><code>*</code></strong></label>
                                    <input type="date" class="form-control" id="serviceStartDate" name="serviceStartDate" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="serviceEndDate">Service End Date <strong><code>*</code></strong> </label>
                                    <input type="date" class="form-control" id="serviceEndDate" name="serviceEndDate" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="specialNote">Special Note</label>
                                    <input type="text" class="form-control" id="specialNote" name="specialNote" placeholder="Special Note">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="licenseType"> License Type  <strong><code>*</code></strong></label>
                                    <select class="form-control required" id="licenseType" name="licenseType" required aria-readonly="true" required>
                                        <option value="" selected hidden>Select the License Type</option>
                                        <option value="Single User">Single User</option>
                                        <option value="Multi-user">Multi-user</option>
                                        <option value="Auditor Pack">Auditor Pack</option>
                                        <option value="Rental">Rental</option>
                                    </select>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="systemEmail">System Email <strong><code>*</code></strong></label>
                                    <input type="email" class="form-control" id="systemEmail" name="systemEmail" placeholder="System Email" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="agentUniqCode">Agent Serial Number <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="agentUniqCode" name="agentUniqCode" placeholder="Agent Unique Code" required>
                                </div>
                                
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal"> Close</button>
                    <button type="submit" class="btn btn-primary" id="addAgentDataBtn" data-loading-text="Loading..." autocomplete="off"> Create Agent</button>
                </div> <!-- /modal-footer -->
            </div>
        </div>
    </div>
    <!-- end add agent modal -->

    <!-- view agent modal -->
    <div class="modal fade" id="viewAgentModal" tabindex="-1" role="dialog" aria-labelledby="viewAgentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="viewAgentModalLabel">Agent Details</h4>
                    <button type="button" class="btn btn-inverse-light btn-fw" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="viewAgentForm" class="form-sample">
                        <div class="row justify-content-center">
                            <div class="col-sm-5">
                                <div class="form-group view-form-group">
                                    <label for="agentName">Agent Name  <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="agentName" name="agentName" placeholder="Agent Name" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="companyName">Company Name <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="companyName" name="companyName" placeholder="Company Name" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="contact">Contact <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="email">Email <strong><code>*</code></strong></label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="address">Address <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Address" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="area">Area</label>
                                    <input type="text" class="form-control" id="area" name="area" placeholder="Area" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="pincode">Pincode <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="Active Status">Agent Status</label>
                                    <select class="form-control" id="agentStatus" name="agentStatus" required aria-readonly="true">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="createdBy">Agent Created By</label>
                                    <input type="text" class="form-control" id="createdBy" name="createdBy" placeholder="Created By" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="createdOn">Agent Created On</label>
                                    <input type="text" class="form-control" id="createdOn" name="createdOn" placeholder="Created On" required>
                                </div>

                            </div>
                            <div class="col-sm-5">
                                <div class="form-group view-form-group">
                                    <label for="serviceType"> Service Type  <strong><code>*</code></strong></label>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="serviceType" value="AMC"> AMC </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="serviceType" value="Tally Subscription"> Tally Subscription </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="serviceType" value="One Time"> One Time </label>
                                    </div>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="city">City <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="city" name="city" placeholder="City" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="city">Service Start Date <strong><code>*</code></strong></label>
                                    <input type="date" class="form-control" id="serviceStartDate" name="serviceStartDate" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="serviceEndDate">Service End Date <strong><code>*</code></strong></label>
                                    <input type="date" class="form-control" id="serviceEndDate" name="serviceEndDate" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="specialNote">Special Note</label>
                                    <input type="text" class="form-control" id="specialNote" name="specialNote" placeholder="Special Note" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="licenseType"> License Type  <strong><code>*</code></strong></label>
                                    <select class="form-control" id="licenseType" name="licenseType" disabled aria-readonly="true">
                                        <option value="" selected hidden>Select the License Type</option>
                                        <option value="Single User">Single User</option>
                                        <option value="Multi-user">Multi-user</option>
                                        <option value="Auditor Pack">Auditor Pack</option>
                                        <option value="Rental">Rental</option>
                                    </select>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="systemEmail">System Email <strong><code>*</code></strong></label>
                                    <input type="email" class="form-control" id="systemEmail" name="systemEmail" placeholder="System Email" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="agentUniqCode">Agent Serial Number <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="agentUniqCode" name="agentUniqCode" placeholder="Agent Unique Code" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="updatedBy">Agent Updated By</label>
                                    <input type="text" class="form-control" id="updatedBy" name="updatedBy" placeholder="Updated By" required>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"> Close</button>
                </div> <!-- /modal-footer -->
            </div>
        </div>
    </div>
    <!-- end view agent modal -->

    <!-- edit agent modal -->
    <div class="modal fade" id="editAgentModal" tabindex="-1" role="dialog" aria-labelledby="editAgentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editAgentModalLabel">Edit Agent Details</h4>
                    <button type="button" class="btn btn-inverse-light btn-fw" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="editAgentForm" class="form-sample">
                        <div class="row justify-content-center">
                            <div class="col-sm-5">
                                <div class="form-group view-form-group">
                                    <label for="agentName">Agent Name <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="agentName" name="agentName" placeholder="Agent Name" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="companyName">Company Name <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="companyName" name="companyName" placeholder="Company Name" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="contact">Contact <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="email">Email <strong><code>*</code></strong></label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="address">Address <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Address" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="area">Area</label>
                                    <input type="text" class="form-control" id="area" name="area" placeholder="Area" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="pincode">Pincode <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="Active Status">Agent Status <strong><code>*</code></strong></label>
                                    <select class="form-control" id="agentStatus" name="agentStatus" required aria-readonly="true">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="createdBy">Agent Created By</label>
                                    <input type="text" class="form-control" id="createdBy" name="createdBy" placeholder="Created By" required readonly>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="createdOn">Agent Created On</label>
                                    <input type="text" class="form-control" id="createdOn" name="createdOn" placeholder="Created On" required readonly>
                                </div>

                            </div>
                            <div class="col-sm-5">
                                <div class="form-group view-form-group">
                                    <label for="serviceType[]"> Service Type <strong><code>*</code></strong></label>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="serviceType[]" value="AMC"> AMC </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="serviceType[]" value="Tally Subscription"> Tally Subscription </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="serviceType[]" value="One Time"> One Time </label>
                                    </div>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="city">City <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="city" name="city" placeholder="City" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="city">Service Start Date <strong><code>*</code></strong></label>
                                    <input type="date" class="form-control" id="serviceStartDate" name="serviceStartDate" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="serviceEndDate">Service End Date <strong><code>*</code></strong></label>
                                    <input type="date" class="form-control" id="serviceEndDate" name="serviceEndDate" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="specialNote">Special Note</label>
                                    <input type="text" class="form-control" id="specialNote" name="specialNote" placeholder="Special Note" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="licenseType"> License Type  <strong><code>*</code></strong></label>
                                    <select class="form-control required" id="licenseType" name="licenseType" required aria-readonly="true">
                                        <option value="0" selected hidden>Select the License Type</option>
                                        <option value="Single User">Single User</option>
                                        <option value="Multi-user">Multi-user</option>
                                        <option value="Auditor Pack">Auditor Pack</option>
                                        <option value="Rental">Rental</option>
                                    </select>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="systemEmail">System Email <strong><code>*</code></strong></label>
                                    <input type="email" class="form-control" id="systemEmail" name="systemEmail" placeholder="System Email" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="agentUniqCode">Agent Serial Number <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="agentUniqCode" name="agentUniqCode" placeholder="Agent Unique Code" required readonly>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer editAgentDataFooter">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"> Close</button>
                    <button type="submit" class="btn btn-primary" id="editAgentDataBtn" data-loading-text="Loading..." autocomplete="off"> Update Agent</button>
                </div> <!-- /modal-footer -->
            </div>
        </div>
    </div>
    <!-- end edit agent modal -->

    <!-- end modals -->

    <?php require_once("./shared/components/pre-footer.php");  ?>


    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>

    <script src="assets/custom-js/agentHelper.js"></script>
    <script src="assets/default-js/jquery.cookie.js" type="text/javascript"></script>

    <script src="assets/vendors/chart.js/chart.umd.js"></script>
    <script src="assets/vendors/progressbar.js/progressbar.min.js"></script>

    <?php require_once("./shared/components/post-footer.php");  ?>