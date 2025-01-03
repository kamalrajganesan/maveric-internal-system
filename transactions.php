<?php require_once("./shared/components/pre-header.php");

if (isset($_GET['type']) && isset($_GET['value'])) {
    $type = htmlspecialchars($_GET['type']);    // serviceType (or) serviceThrough
    $value = htmlspecialchars($_GET['value']);  // One Time, Tally, AMC (or) Phone Call, Remote, Physical Visit

    echo "<script>
        var transaction_type = '" . $type . "'
        var transaction_value = '" . $value . "'
    </script>";
} else {
    echo "No data received.";
}
?>

<title> My Transactions - Tejas </title>

<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">

<?php require_once("./shared/components/post-header.php");  ?>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="row">
            <div class="col-sm-12 short-l">
                <div class="d-sm-flex align-items-center justify-content-end border-bottom">
                    <div>
                        <div class="btn-wrapper">
                            <button class="btn btn-otline-dark align-items-center" onclick="onClickAddTransaction()">
                                <i class="fa fa-plus"></i> Add New Transaction
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
                                <table id="transactionMasterTbl" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Created date</th>
                                            <th>Issue</th>
                                            <th>Customer Name</th>
                                            <th>Service type</th>
                                            <th>Created By</th>
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

    <!-- add transaction modal -->
    <div class="modal fade" id="addTransactionModal" tabindex="-1" role="dialog" aria-labelledby="addTransactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addTransactionModalLabel">Add New Transaction</h4>
                    <button type="button" class="btn btn-inverse-light btn-fw" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="addTransactionForm" class="form-sample">
                        <div class="row justify-content-center">
                            <div class="col-sm-5">

                                <div class="form-group view-form-group">
                                    <label for="customerId">Customer <strong><code>*</code></strong></label>
                                    <select class="form-control required" id="customerId" name="customerId" required>
                                        <option value="" disabled selected>Select Customer</option>
                                    </select>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="problemStmt">Issue(in brief) <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="problemStmt" name="problemStmt" placeholder="Problem Statement" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="problemDesc">Issue Description</label>
                                    <textarea class="form-control" id="problemDesc" name="problemDesc" placeholder="Problem Description"></textarea>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="status">Transaction Status <strong><code>*</code></strong></label>
                                    <select class="form-control" id="status" name="status" disabled>
                                        <option value="">Select Status</option>
                                        <option value="New" selected>New</option>
                                        <option value="Contacted/Pending">Contacted/ Pending</option>
                                        <option value="Following Up">Following Up</option>
                                        <option value="Closed">Closed</option>
                                    </select>
                                </div>

                            </div>

                            <div class="col-sm-5">

                                <div class="form-group view-form-group">
                                    <label for="serviceType">Service Type <strong><code>*</code></strong></label>
                                    <select class="form-control required" id="serviceType" name="serviceType" aria-readonly="true" required>
                                        <option value="" hidden selected>Select Service</option>
                                        <option value="AMC">AMC</option>
                                        <option value="Tally">Tally</option>
                                        <option value="One Time">One Time</option>
                                    </select>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="serviceThrough">Service Offered Through <strong><code>*</code></strong></label>
                                    <select class="form-control required" id="serviceThrough" name="serviceThrough" aria-readonly="true">
                                        <option value="" hidden selected>Service Offered Through</option>
                                        <option value="Phone Call">Phone Call</option>
                                        <option value="Remote">Remote</option>
                                        <option value="Physical Visit">Physical Visit</option>
                                    </select>
                                </div>
                                   
                                <div class="form-group view-form-group">
                                    <label for="isUnderAMC">Is client under AMC</label>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="isUnderAMC" id="isUnderAMCYES" value="1"> Yes </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="isUnderAMC" id="isUnderAMCNO" value="0" checked> No </label>
                                    </div>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="comments">Comments <strong><code>*</code></strong></label>
                                    <textarea class="form-control" id="comments" name="comments" placeholder="Comments" required></textarea>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" placeholder="Notes"></textarea>
                                </div>

                            </div>
                        </div>
                    </form>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal"> Close</button>
                    <button type="submit" class="btn btn-primary" id="addTransactionDataBtn" data-loading-text="Loading..." autocomplete="off"> Create Transaction</button>
                </div> <!-- /modal-footer -->
            </div>
        </div>
    </div>
    <!-- end add transaction modal -->

    <!-- view transaction modal -->
    <div class="modal fade" id="viewTransactionModal" tabindex="-1" role="dialog" aria-labelledby="viewTransactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="viewTransactionModalLabel">Transaction Details of <strong id="currentViewTransactionCode"></strong> </h4>
                    <button type="button" class="btn btn-inverse-light btn-fw" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="viewTransactionForm" class="form-sample">
                        <div class="row justify-content-center">
                            <div class="col-sm-5">
                                
                                <div class="form-group view-form-group">
                                    <label for="customerId">Customer <strong><code>*</code></strong></label>
                                    <select class="form-control" id="customerId" name="customerId">
                                        <option value="" hidden selected>Select Customer</option>
                                        <!-- Add all customers options here -->
                                    </select>
                                </div>
                                    
                                <div class="form-group view-form-group">
                                    <label for="problemStmt">Issue(in brief)  <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="problemStmt" name="problemStmt" placeholder="Problem Statement" readonly>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="problemDesc">Issue Description</label>
                                    <textarea class="form-control" id="problemDesc" name="problemDesc" placeholder="Problem Description" readonly></textarea>
                                </div>

                                <div class="form-group view-form-group">
                                    <strong> Past Comments </strong>
                                    <div id="pastCommentsOfThisTransaction"></div>
                                </div>

                                <div class="form-group view-form-group">
                                    <strong>  Past Notes </strong>
                                    <div id="pastNotesOfThisTransaction"></div>
                                </div>
                            </div>

                            <div class="col-sm-5">       

                                <div class="form-group view-form-group">
                                    <label for="serviceType">Service Type</label>
                                    <select class="form-control" id="serviceType" name="serviceType" aria-readonly="true" disabled>
                                        <option value="" hidden selected>Select Service <strong><code>*</code></strong></option>
                                        <option value="AMC">AMC</option>
                                        <option value="Tally">Tally</option>
                                        <option value="One Time">One Time</option>
                                    </select>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="serviceThrough">Service Offered Through <strong><code>*</code></strong></label>
                                    <select class="form-control" id="serviceThrough" name="serviceThrough" aria-readonly="true" disabled>
                                        <option value="" hidden selected>Service Offered Through</option>
                                        <option value="Phone Call">Phone Call</option>
                                        <option value="Remote">Remote</option>
                                        <option value="Physical Visit">Physical Visit</option>
                                    </select>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="createdDate">Transaction Created Date <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="createdDate" name="createdDate" placeholder="Created Date" readonly>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="createdDate">Transaction Created By</label>
                                    <select class="form-control" id="aAgentId" name="aAgentId">
                                        <option value="" hidden>Select Agent</option>
                                    </select>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="isUnderAMC">Is Client Under AMC</label>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="isUnderAMC" id="isUnderAMCYES" value="1"> Yes </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="isUnderAMC" id="isUnderAMCNO" value="0"> No </label>
                                    </div>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="status">Transaction Status <strong><code>*</code></strong></label>
                                    <select class="form-control" id="status" name="status" disabled>
                                        <option value="" hidden>Select Status</option>
                                        <option value="New">New</option>
                                        <option value="Contacted/Pending">Contacted/ Pending</option>
                                        <option value="Following Up">Following Up</option>
                                        <option value="Closed">Closed</option>
                                    </select>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="comments">Last Status Updated On</label>
                                    <textarea class="form-control" id="lastUpdatedOn" name="lastUpdatedOn" placeholder="lastUpdatedOn" readonly></textarea>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="agentId">Last Status Updated By</label>
                                    <select class="form-control" name="agentId" id="agentId" disabled="disabled">
                                        <option value="" hidden> Select Agent</option>
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
    <!-- end view transaction modal -->

    <!-- edit transaction modal -->
    <div class="modal fade" id="editTransactionModal" tabindex="-1" role="dialog" aria-labelledby="editTransactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editTransactionModalLabel">Edit Transaction Details of <strong id="currentTransactionCode"></strong> </h4>
                    <button type="button" class="btn btn-inverse-light btn-fw" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="editTransactionForm" class="form-sample">
                        <div class="row justify-content-center">
                            <div class="col-sm-5">
                                
                                <div class="form-group view-form-group">
                                    <label for="customerId">Customer <strong><code>*</code></strong></label>
                                    <select class="form-control" id="customerId" name="customerId">
                                        <option value="" hidden selected>Select Customer</option>
                                        <!-- Add all customers options here -->
                                    </select>
                                </div>
                                    
                                <div class="form-group view-form-group">
                                    <label for="problemStmt">Issue(in brief)  <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="problemStmt" name="problemStmt" placeholder="Problem Statement" readonly>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="problemDesc">Issue Description</label>
                                    <textarea class="form-control" id="problemDesc" name="problemDesc" placeholder="Problem Description" readonly></textarea>
                                </div>

                                <div class="form-group view-form-group">
                                    <strong> Past Comments </strong>
                                    <div id="pastCommentsOfThisTransaction"></div>
                                </div>

                                <div class="form-group view-form-group">
                                    <strong> Past Notes </strong>
                                    <div id="pastNotesOfThisTransaction"></div>
                                </div>

                            </div>
                            
                            <div class="col-sm-5">       
                                
                                <div class="form-group view-form-group">
                                    <label for="serviceType">Service Type <strong><code>*</code></strong></label>
                                    <select class="form-control" id="serviceType" name="serviceType" aria-readonly="true" disabled>
                                        <option value="" hidden selected>Select Service</option>
                                        <option value="AMC">AMC</option>
                                        <option value="Tally">Tally</option>
                                        <option value="One Time">One Time</option>
                                    </select>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="serviceThrough">Service Offered Through <strong><code>*</code></strong></label>
                                    <select class="form-control" id="serviceThrough" name="serviceThrough" aria-readonly="true">
                                        <option value="" hidden selected>Service Offered Through</option>
                                        <option value="Phone Call">Phone Call</option>
                                        <option value="Remote">Remote</option>
                                        <option value="Physical Visit">Physical Visit</option>
                                    </select>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="createdDate">Transaction Created Date</label>
                                    <input type="text" class="form-control" id="createdDate" name="createdDate" placeholder="Created Date" readonly>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="isUnderAMC">Is Client Under AMC</label>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="isUnderAMC" id="isUnderAMCYES" value="1"> Yes </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="isUnderAMC" id="isUnderAMCNO" value="0"> No </label>
                                    </div>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="status">Transaction Status <strong><code>*</code></strong></label>
                                    <select class="form-control required" id="status" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="New">New</option>
                                        <option value="Contacted/Pending">Contacted/ Pending</option>
                                        <option value="Following Up">Following Up</option>
                                        <option value="Closed">Closed</option>
                                    </select>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="comments">New Comment <strong><code>*</code></strong></label>
                                    <textarea class="form-control" id="comments" name="comments" placeholder="Comments" required></textarea>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="notes">New Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" placeholder="Notes"></textarea>
                                </div>

                            </div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal"> Close</button>
                    <button type="submit" class="btn btn-primary" id="editTransactionDataBtn" data-loading-text="Loading..." autocomplete="off"> Update Transaction </button>
                </div> <!-- /modal-footer -->
            </div>
        </div>
    </div>
    <!-- end edit transaction modal -->

    <!-- end modals -->

    <?php require_once("./shared/components/pre-footer.php");  ?>


    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>

    <script src="assets/custom-js/transactionsHelper.js"></script>
    <script src="assets/default-js/jquery.cookie.js" type="text/javascript"></script>

    <script src="assets/vendors/chart.js/chart.umd.js"></script>
    <script src="assets/vendors/progressbar.js/progressbar.min.js"></script>

    <?php require_once("./shared/components/post-footer.php");  ?>