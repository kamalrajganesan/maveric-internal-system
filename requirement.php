<?php require_once("./shared/components/pre-header.php");

if (isset($_GET['page'])) {
    $page = htmlspecialchars($_GET['page']);

    echo "<script>
        var requirement_page = '" . $page . "'
    </script>";
} else {
    echo "No data received.";
}
?>

<title> My Requirement</title>

<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">

<?php require_once("./shared/components/post-header.php");  ?>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="row">
            <div class="col-sm-12 short-l">
                <div class="d-sm-flex align-items-center justify-content-end border-bottom">
                    <div>
                        <div class="btn-wrapper">
                            <button class="btn btn-otline-dark align-items-center" data-bs-toggle="modal" data-bs-target="#addRequirementModal" id="addRequirementModalBtn">
                                <i class="fa fa-plus"></i> Add New Requirement
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
                                <table id="requirementTbl" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>S. No.</th>
                                            <th>Requirement</th>
                                            <th>Brief</th>
                                            <th>Detail</th>
                                            <th>Customer</th>
                                            <th>Phone</th>
                                            <th>Date</th>
                                            <th>Status</th>
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

    <!-- add requirement modal -->
    <div class="modal fade" id="addRequirementModal" tabindex="-1" role="dialog" aria-labelledby="addRequirementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addRequirementModalLabel">Add New Requirement</h4>
                    <button type="button" class="btn btn-inverse-light btn-fw" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="addRequirementForm" class="form-sample">
                        <div class="row justify-content-center">
                            <div class="col-sm-5">
                                <div class="form-group view-form-group">
                                    <label for="nm">Requirement Name <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="nm" name="nm" placeholder="Requirement Name" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="brief">Brief <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="brief" name="brief" placeholder="Brief" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="detailed">Detailed <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="detailed" name="detailed" placeholder="Detailed" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="cust_id">Customer ID</label>
                                    <input type="text" class="form-control" id="cust_id" name="cust_id" placeholder="Customer ID">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="requirementStatus">Requirement Status <strong><code>*</code></strong></label>
                                    <select class="form-control required" id="requirementStatus" name="requirementStatus" aria-readonly="true" required>
                                        <option value="New">Newly Added</option>
                                        <option value="Contacted">Contacted</option>
                                        <option value="Converted">Converted</option>
                                        <option value="Following">following up</option>
                                        <option value="Lost">Lost</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal"> Close</button>
                    <button type="submit" class="btn btn-primary" id="addRequirementDataBtn" data-loading-text="Loading..." autocomplete="off"> Create Requirement</button>
                </div> <!-- /modal-footer -->
            </div>
        </div>
    </div>
    <!-- end add requirement modal -->

    <!-- view requirement modal -->
    <div class="modal fade" id="viewRequirementModal" tabindex="-1" role="dialog" aria-labelledby="viewRequirementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="viewRequirementModalLabel">Requirement Details of <strong id="currentRequirementCode"></strong> </h4>
                    <button type="button" class="btn btn-inverse-light btn-fw" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="viewRequirementForm" class="form-sample">
                        <div class="row justify-content-center">
                            <div class="col-sm-5">
                                <div class="form-group view-form-group">
                                    <label for="nm">Requirement Name <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="nm" name="nm" placeholder="Requirement Name" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="brief">Brief <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="brief" name="brief" placeholder="Brief" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="detailed">Detailed <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="detailed" name="detailed" placeholder="Detailed" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="cust_id">Customer ID</label>
                                    <input type="text" class="form-control" id="cust_id" name="cust_id" placeholder="Customer ID">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="requirementStatus">Lead Status <strong><code>*</code></strong></label>
                                    <select class="form-control" id="requirementStatus" name="requirementStatus" required aria-readonly="true">
                                    <option value="New">Newly Added</option>
                                        <option value="Contacted">Contacted</option>
                                        <option value="Converted">Converted</option>
                                        <option value="Following">following up</option>
                                        <option value="Lost">Lost</option>

                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal"> Close</button>
                </div> <!-- /modal-footer -->
            </div>
        </div>
    </div>
    <!-- end view requirement modal -->


    <!-- edit requirement modal -->
    <div class="modal fade" id="editRequirementModal" tabindex="-1" role="dialog" aria-labelledby="editRequirementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editRequirementModalLabel">Edit Requirement Details of <strong id="currentEditRequirementCode"></strong> </h4>
                    <button type="button" class="btn btn-inverse-light btn-fw" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="editRequirementForm" class="form-sample">
                        <div class="row justify-content-center">
                            <div class="col-sm-5">
                                <div class="form-group view-form-group">
                                    <label for="nm">Requirement Name <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="nm" name="nm" placeholder="Requirement Name" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="brief">Brief <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="brief" name="brief" placeholder="Brief" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="detailed">Detailed <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="detailed" name="detailed" placeholder="Detailed" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="cust_id">Customer ID</label>
                                    <input type="text" class="form-control" id="cust_id" name="cust_id" placeholder="Customer ID">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="requirementStatus">Lead Status <strong><code>*</code></strong></label>
                                    <select class="form-control required" id="requirementStatus" name="requirementStatus" required aria-readonly="true">
                                        <option value="New">Newly Added</option>
                                        <option value="Contacted">Contacted</option>
                                        <option value="Converted">Converted</option>
                                        <option value="Following">following up</option>
                                        <option value="Lost">Lost</option>

                                    </select>
                                </div>

                            </div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal"> Close</button>
                    <button type="submit" class="btn btn-primary" id="editRequirementDataBtn" data-loading-text="Loading..." autocomplete="off"> Update Requirement </button>
                </div> <!-- /modal-footer -->
            </div>
        </div>
    </div>
    <!-- end edit requirement modal -->

    <!-- end modals -->

    <?php require_once("./shared/components/pre-footer.php");  ?>


    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>

    <script src="assets/custom-js/requirementHelper.js"></script>
    <script src="assets/default-js/jquery.cookie.js" type="text/javascript"></script>

    <script src="assets/vendors/chart.js/chart.umd.js"></script>
    <script src="assets/vendors/progressbar.js/progressbar.min.js"></script>

    <?php require_once("./shared/components/post-footer.php");  ?>