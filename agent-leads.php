<?php
require_once("./shared/components/pre-header.php");

$type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : ''; // Default to empty if not set

echo "<script>
    var report_type = '" . $type . "';
</script>";
?>


<title> My Reports - Tejas </title>

<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="./assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="./assets/css/flatpickr.min.css">
<link rel="stylesheet" href="./assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<?php require_once("./shared/components/post-header.php");  ?>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="row">
            <div class="col-sm-12 short-l">
                <div class="d-sm-flex align-items-center justify-content-end border-bottom">
                </div>
            </div>
        </div>
        <!-- Date Filter Section -->
       <div class="row py-3">
    <div class="col-sm-12">
        <div class="card card-rounded">
            <div class="card-body">

                <!-- ✅ One row: 3 filters -->
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="transactionDateRange">Transaction Date Range</label>
                            <input type="text" class="form-control" id="transactionDateRange" placeholder="DD/MM/YYYY">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="transactionDate">Transaction Date</label>
                            <input type="text" class="form-control" id="transactionDate" placeholder="DD/MM/YYYY">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="serviceTypeFilter">Lead Type</label>
                            <select class="form-control" id="serviceTypeFilter" name="serviceTypeFilter">
                                <option value="">All Lead Types</option>
                                <option value="Phone Call">Call Lead</option>
                                <option value="Email">Email Lead</option>
                            </select>
                        </div>
                    </div>

                </div> <!-- end row -->

                <!-- ✅ Buttons row -->
                <div class="row mt-3">
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary me-2" id="filterBtn">Filter</button>
                        <button type="button" class="btn btn-secondary" id="resetBtn">Reset</button>
                    </div>
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
                                <table id="transactionMasterTbl" class="display nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>S. No.</th>
                                            <th>Agent Name</th>
                                            <th>New</th>
                                            <th>Following Up</th>
                                            <th>Converted</th>
                                            <th>Contacted / Emailed</th>
                                            <th>Lost</th>
                                            <th>Leads Handled</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <!-- Data will be populated by DataTables -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->

        <!-- modals -->
        <!-- view transaction modal -->

        <!-- end view transaction modal -->
        <!-- end modals -->

        <?php require_once("./shared/components/pre-footer.php");  ?>


        <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>

        <script src="assets/default-js/jquery.cookie.js" type="text/javascript"></script>

        <script src="assets/vendors/select2/select2.min.js"></script>
        <script src="assets/default-js/flatpickr.js"></script>
        <script src="assets/custom-js/agent-leads.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

        <?php require_once("./shared/components/post-footer.php");  ?>