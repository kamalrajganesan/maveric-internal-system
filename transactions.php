<?php require_once("./shared/components/pre-header.php");

// if (isset($_GET['page'])) {
//     $page = htmlspecialchars($_GET['page']);

//     echo "<script>
//         var transaction_page = '" . $page . "'
//     </script>";
// } else {
//     echo "No data received.";
// }
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
                            <button class="btn btn-otline-dark align-items-center" data-bs-toggle="modal" data-bs-target="#addTransactionModal" id="addTransactionModalBtn">
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
                                            <th>Customer Name</th>
                                            <th>issue</th>
                                            <th>service type</th>
                                            <th>Assigned Agent</th>
                                            <th>created date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

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
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="addTransactionForm" class="form-sample">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    
                                    <label for="isActive">Is Active</label>
                                    <input type="checkbox" class="form-control" id="isActive" name="isActive">

                                    <label for="createdBy">Created By</label>
                                    <input type="text" class="form-control" id="createdBy" name="createdBy" placeholder="Created By" required>

                                    <label for="assignedAgentId">Assigned Agent ID</label>
                                    <input type="number" class="form-control" id="assignedAgentId" name="assignedAgentId" placeholder="Assigned Agent ID" required>

                                    <label for="customerId">Customer ID</label>
                                    <input type="number" class="form-control" id="customerId" name="customerId" placeholder="Customer ID" required>

                                    <label for="comments">Comments</label>
                                    <textarea class="form-control" id="comments" name="comments" placeholder="Comments" required></textarea>

                                    <label for="problemDesc">Problem Description</label>
                                    <textarea class="form-control" id="problemDesc" name="problemDesc" placeholder="Problem Description" required></textarea>

                                    <label for="newRequirement">New Requirement</label>
                                    <input type="text" class="form-control" id="newRequirement" name="newRequirement" placeholder="New Requirement" required>

                                    <label for="status">Status</label>
                                    <input type="text" class="form-control" id="status" name="status" placeholder="Status" required>

                                    <label for="problemStmt">Problem Statement</label>
                                    <input type="text" class="form-control" id="problemStmt" name="problemStmt" placeholder="Problem Statement" required>

                                    <label for="serviceType">Service Type</label>
                                    <input type="text" class="form-control" id="serviceType" name="serviceType" placeholder="Service Type" required>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="isUnderAMC">Is Under AMC</label>
                                    <input type="checkbox" class="form-control" id="isUnderAMC" name="isUnderAMC">

                                    <label for="solvedBy">Solved By</label>
                                    <input type="number" class="form-control" id="solvedBy" name="solvedBy" placeholder="Solved By" required>

                                    <label for="notes">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" placeholder="Notes" required></textarea>

                                    <label for="closedDate">Closed Date</label>
                                    <input type="date" class="form-control" id="closedDate" name="closedDate">

                                    <label for="isDeleted">Is Deleted</label>
                                    <input type="checkbox" class="form-control" id="isDeleted" name="isDeleted">
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

    <!-- end modals -->

    <?php require_once("./shared/components/pre-footer.php");  ?>


    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>

    <script src="assets/custom-js/transactionsHelper.js"></script>
    <script src="assets/default-js/jquery.cookie.js" type="text/javascript"></script>

    <script src="assets/vendors/chart.js/chart.umd.js"></script>
    <script src="assets/vendors/progressbar.js/progressbar.min.js"></script>

    <?php require_once("./shared/components/post-footer.php");  ?>