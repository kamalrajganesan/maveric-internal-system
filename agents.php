<?php require_once("./shared/components/pre-header.php");

if (isset($_GET['page'])) {
    $page = htmlspecialchars($_GET['page']);

    echo "<script>
        var agent_page = '" . $page . "'
    </script>";
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
                                <table id="manageAgentDataTbl" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>SI. No.</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Contact</th>
                                            <th>Active Status</th>
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
                                    <label for="email">Email <strong><code>*</code></strong></label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="contact1">Primary Contact <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="contact1" name="contact1" placeholder="Primary contact" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="contact2">Secondary Contact </label>
                                    <input type="text" class="form-control" id="contact2" name="contact2" placeholder="Primary contact" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="address">Address </label>
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Address">
                                </div>
                            </div>
                            <div class="col-sm-5">
                            
                                <div class="form-group view-form-group">
                                    <label for="password">Password <strong><code>*</code></strong></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control password" placeholder="Password" id="password" name="password" aria-label="Agent pass phrase....">
                                        <div class="input-group-append">
                                            <span class="input-group-text make-text-visible"> <i class="fa fa-eye"></i> </span>
                                            <span class="input-group-text make-text-invisible"> <i class="fa fa-eye-slash"></i> </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="city">City <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="city" name="city" placeholder="City" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="pincode">Pincode <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="area">Area</label>
                                    <input type="text" class="form-control" id="area" name="area" placeholder="Area">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="activeStatus">Customer Status</label>
                                    <select class="form-control" id="activeStatus" name="activeStatus" required aria-readonly="true">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
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
                                    <label for="agentName">Agent Name <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="agentName" name="agentName" placeholder="Agent Name" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="email">Email <strong><code>*</code></strong></label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="contact1">Primary Contact <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="contact1" name="contact1" placeholder="Primary contact" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="contact2">Secondary Contact </label>
                                    <input type="text" class="form-control" id="contact2" name="contact2" placeholder="Primary contact" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="address">Address </label>
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Address">
                                </div>
                            </div>
                            <div class="col-sm-5">
                            
                                <div class="form-group view-form-group">
                                    <label for="password">Password <strong><code>*</code></strong></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control password" placeholder="Password" id="password" name="password" aria-label="Agent pass phrase....">
                                        <div class="input-group-append">
                                            <span class="input-group-text make-text-visible"> <i class="fa fa-eye"></i> </span>
                                            <span class="input-group-text make-text-invisible"> <i class="fa fa-eye-slash"></i> </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="city">City <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="city" name="city" placeholder="City" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="pincode">Pincode <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="area">Area</label>
                                    <input type="text" class="form-control" id="area" name="area" placeholder="Area">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="activeStatus">Agent Activity Status</label>
                                    <select class="form-control" id="activeStatus" name="activeStatus" required aria-readonly="true">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="createdOn">Agent Created On</label>
                                    <input type="text" class="form-control" id="createdOn" name="createdOn" placeholder="Agent Created On">
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
                                    <label for="email">Email <strong><code>*</code></strong></label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="contact1">Primary Contact <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="contact1" name="contact1" placeholder="Primary contact" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="contact2">Secondary Contact </label>
                                    <input type="text" class="form-control" id="contact2" name="contact2" placeholder="Primary contact" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="address">Address </label>
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Address">
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group view-form-group">
                                    <label for="password">Password <strong><code>*</code></strong></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control password" placeholder="Password" id="password" name="password" aria-label="Agent pass phrase....">
                                        <div class="input-group-append">
                                            <span class="input-group-text make-text-visible"> <i class="fa fa-eye"></i> </span>
                                            <span class="input-group-text make-text-invisible"> <i class="fa fa-eye-slash"></i> </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="city">City <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="city" name="city" placeholder="City" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="pincode">Pincode <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="area">Area</label>
                                    <input type="text" class="form-control" id="area" name="area" placeholder="Area">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="activeStatus">Agent Activity Status</label>
                                    <select class="form-control" id="activeStatus" name="activeStatus" required aria-readonly="true">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
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