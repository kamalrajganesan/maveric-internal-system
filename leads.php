<?php require_once("./shared/components/pre-header.php");

if (isset($_GET['page'])) {
    $page = htmlspecialchars($_GET['page']);

    echo "<script>
        var lead_page = '" . $page . "'
    </script>";
} else {
    echo "No data received.";
}
?>

<title> My Leads - Tejas </title>

<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">

<?php require_once("./shared/components/post-header.php");  ?>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="row">
            <div class="col-sm-12 short-l">
                <div class="d-sm-flex align-items-center justify-content-end border-bottom">
                    <div>
                        <div class="btn-wrapper">
                            <button class="btn btn-otline-dark align-items-center" data-bs-toggle="modal" data-bs-target="#addLeadModal" id="addLeadModalBtn">
                                <i class="fa fa-plus"></i> Add New Lead
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
                                <table id="leadMasterTbl" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Lead Name</th>
                                            <th>Contact</th>
                                            <th>Company Name</th>
                                            <th>Requirement</th>
                                            <th>Description</th>
                                            <th>City</th>
                                            <th>Status</th>
                                            <th>Follow up</th>
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

    <!-- add lead modal -->
    <div class="modal fade" id="addLeadModal" tabindex="-1" role="dialog" aria-labelledby="addLeadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addLeadModalLabel">Add New Lead</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="addLeadForm" class="form-sample">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">

                                    <label for="leadNm">Lead Name</label>
                                    <input type="text" class="form-control" id="leadNm" name="leadNm" placeholder="Lead Name" required>


                                    <label for="contact">Contact</label>
                                    <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact" required>

                                    <label for="companyNm">Company Name</label>
                                    <input type="text" class="form-control" id="companyNm" name="companyNm" placeholder="Company Name" required>

                                    <label for="requirement">Requirement</label>
                                    <input type="text" class="form-control" id="requirement" name="requirement" placeholder="Requirement" required>

                                    <label for="notes">Notes</label>
                                    <input type="text" class="form-control" id="notes" name="notes" placeholder="Notes" required>

                                    <label for="description">Description</label>
                                    <input type="text" class="form-control" id="description" name="description" placeholder="Description" required>


                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">

                                    <label for="addressLn">Address Line</label>
                                    <input type="text" class="form-control" id="addressLn" name="addressLn" placeholder="Address Line" required>

                                    <label for="pincode">Pincode</label>
                                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode" required>

                                    <label for="city">City</label>
                                    <input type="text" class="form-control" id="city" name="city" placeholder="City" required>

                                    <label for="area">Area</label>
                                    <input type="text" class="form-control" id="area" name="area" placeholder="Area" required>

                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>

                                    <label for="followUpDt">Follow-up Date</label>
                                    <input type="date" class="form-control" id="followUpDt" name="followUpDt" required>

                                    <label for="leadStatus">Lead Status</label>
                                    <input type="text" class="form-control" id="leadStatus" name="leadStatus" placeholder="Lead Status" required>


                                </div>
                            </div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal"> Close</button>
                    <button type="submit" class="btn btn-primary" id="addLeadDataBtn" data-loading-text="Loading..." autocomplete="off"> Create Lead</button>
                </div> <!-- /modal-footer -->
            </div>
        </div>
    </div>
    <!-- end add lead modal -->

    <!-- end modals -->

    <?php require_once("./shared/components/pre-footer.php");  ?>


    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>

    <script src="assets/custom-js/leadHelper.js"></script>
    <script src="assets/default-js/jquery.cookie.js" type="text/javascript"></script>

    <script src="assets/vendors/chart.js/chart.umd.js"></script>
    <script src="assets/vendors/progressbar.js/progressbar.min.js"></script>

    <?php require_once("./shared/components/post-footer.php");  ?>