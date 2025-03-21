<?php 

require_once("./shared/components/pre-header.php");

?>

<title> Email Leads - Tejas </title>

<link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/3.0.0/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.2/css/buttons.dataTables.css">

<!-- DataTables Select extension CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.3/css/select.dataTables.min.css">

<?php require_once("./shared/components/post-header.php");  ?>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="row">

            <div class="col-sm-12">
                <div id="events" class="box">
                    Row selected count - new information added at the top
                </div>
            </div>

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
                                <table id="leadMasterTbl" class="display nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th></th>
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
                    <button type="button" class="btn btn-inverse-light btn-fw" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="addLeadForm" class="form-sample">
                        <div class="row justify-content-center">
                            <div class="col-sm-5">
                                <div class="form-group view-form-group">
                                    <label for="leadNm">Lead Name <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="leadNm" name="leadNm" placeholder="Lead Name" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="companyNm">Company Name</label>
                                    <input type="text" class="form-control" id="companyNm" name="companyNm" placeholder="Company Name">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="contact">Contact <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="requirement">Requirement <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="requirement" name="requirement" placeholder="Requirement" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="description">Description</label>
                                    <input type="text" class="form-control" id="description" name="description" placeholder="Description">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="notes">Notes</label>
                                    <input type="text" class="form-control" id="notes" name="notes" placeholder="Notes">

                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group view-form-group">

                                    <label for="addressLn">Address Line</label>
                                    <input type="text" class="form-control" id="addressLn" name="addressLn" placeholder="Address Line">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="area">Area</label>
                                    <input type="text" class="form-control" id="area" name="area" placeholder="Area">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="city">City</label>
                                    <input type="text" class="form-control" id="city" name="city" placeholder="City">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="pincode">Pincode</label>
                                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="followUpDt">Follow-up Date <strong><code>*</code></strong></label>
                                    <input type="date" class="form-control" id="followUpDt" name="followUpDt" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="leadStatus">Lead Status <strong><code>*</code></strong></label>
                                    <select class="form-control required" id="leadStatus" name="leadStatus" aria-readonly="true" required>
                                        <option value="New">Newly Added</option>
                                        <option value="Emailed and Waiting for reply">Emailed and Waiting for reply</option>
                                        <option value="Following">Following up</option>
                                        <option value="Converted"> Converted </option>
                                        <option value="Lost">Lost</option>
                                    </select>
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

    <!-- view lead modal -->
    <div class="modal fade" id="viewLeadModal" tabindex="-1" role="dialog" aria-labelledby="viewLeadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="viewLeadModalLabel">Lead Details of <strong id="currentLeadCode"></strong> </h4>
                    <button type="button" class="btn btn-inverse-light btn-fw" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="viewLeadForm" class="form-sample">
                        <div class="row justify-content-center">
                            <div class="col-sm-5">
                                <div class="form-group view-form-group">
                                    <label for="leadNm">Lead Name <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="leadNm" name="leadNm" placeholder="Lead Name" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="companyNm">Company Name</label>
                                    <input type="text" class="form-control" id="companyNm" name="companyNm" placeholder="Company Name" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="contact">Contact <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="requirement">Requirement <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="requirement" name="requirement" placeholder="Requirement" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="description">Description</label>
                                    <input type="text" class="form-control" id="description" name="description" placeholder="Description" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="notes">Notes</label>
                                    <input type="text" class="form-control" id="notes" name="notes" placeholder="Notes" required>

                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group view-form-group">

                                    <label for="addressLn">Address Line</label>
                                    <input type="text" class="form-control" id="addressLn" name="addressLn" placeholder="Address Line" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="area">Area</label>
                                    <input type="text" class="form-control" id="area" name="area" placeholder="Area" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="city">City</label>
                                    <input type="text" class="form-control" id="city" name="city" placeholder="City" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="pincode">Pincode</label>
                                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="followUpDt">Follow-up Date <strong><code>*</code></strong></label>
                                    <input type="date" class="form-control" id="followUpDt" name="followUpDt" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="leadStatus">Lead Status <strong><code>*</code></strong></label>
                                    <select class="form-control required" id="leadStatus" name="leadStatus" aria-readonly="true" required>
                                        <option value="New">Newly Added</option>
                                        <option value="Emailed and Waiting for reply">Emailed and Waiting for reply</option>
                                        <option value="Following">Following up</option>
                                        <option value="Converted"> Converted </option>
                                        <option value="Lost">Lost</option>
                                    </select>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="createdBy">Created By</label>
                                    <input type="text" class="form-control" id="createdBy" name="createdBy" required>
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
    <!-- end view lead modal -->

    <!-- edit lead modal -->
    <div class="modal fade" id="editLeadModal" tabindex="-1" role="dialog" aria-labelledby="editLeadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editLeadModalLabel">Edit Lead Details of <strong id="currentEditLeadCode"></strong> </h4>
                    <button type="button" class="btn btn-inverse-light btn-fw" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="editLeadForm" class="form-sample">
                        <div class="row justify-content-center">
                        <div class="row justify-content-center">
                            <div class="col-sm-5">
                                <div class="form-group view-form-group">
                                    <label for="leadNm">Lead Name <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="leadNm" name="leadNm" placeholder="Lead Name" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="companyNm">Company Name</label>
                                    <input type="text" class="form-control" id="companyNm" name="companyNm" placeholder="Company Name" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="contact">Contact <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="requirement">Requirement <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="requirement" name="requirement" placeholder="Requirement" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="description">Description</label>
                                    <input type="text" class="form-control" id="description" name="description" placeholder="Description" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="notes">Notes</label>
                                    <input type="text" class="form-control" id="notes" name="notes" placeholder="Notes" required>

                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group view-form-group">

                                    <label for="addressLn">Address Line</label>
                                    <input type="text" class="form-control" id="addressLn" name="addressLn" placeholder="Address Line" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="area">Area</label>
                                    <input type="text" class="form-control" id="area" name="area" placeholder="Area" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="city">City</label>
                                    <input type="text" class="form-control" id="city" name="city" placeholder="City" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="pincode">Pincode</label>
                                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="followUpDt">Follow-up Date <strong><code>*</code></strong></label>
                                    <input type="date" class="form-control" id="followUpDt" name="followUpDt" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="leadStatus">Lead Status <strong><code>*</code></strong></label>
                                    <select class="form-control required" id="leadStatus" name="leadStatus" aria-readonly="true" required>
                                        <option value="New">Newly Added</option>
                                        <option value="Emailed and Waiting for reply">Emailed and Waiting for reply</option>
                                        <option value="Following">Following up</option>
                                        <option value="Converted"> Converted </option>
                                        <option value="Lost">Lost</option>
                                    </select>
                                </div>

                            
                            </div>
                        </div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal"> Close</button>
                    <button type="submit" class="btn btn-primary" id="editLeadDataBtn" data-loading-text="Loading..." autocomplete="off"> Update Lead </button>
                </div> <!-- /modal-footer -->
            </div>
        </div>
    </div>
    <!-- end edit lead modal -->

    <!-- edit lead modal -->
    <div class="modal fade" id="multiActionLeadModal" tabindex="-1" role="dialog" aria-labelledby="multiActionLeadModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="multiActionLeadModalLabel">Action on Multiple Leads </h4>
                    <button type="button" class="btn btn-inverse-light btn-fw" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="multiActionLeadForm" class="form-sample">
                        <div class="row justify-content-center">
                            <div class="col-sm-7">

                                <div class="form-group view-form-group selectedLeadCount"> </div>

                                <div class="form-group view-form-group">
                                    <label for="followUpDt">Follow-up Date <strong><code>*</code></strong></label>
                                    <input type="date" class="form-control" id="followUpDt" name="followUpDt" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="leadStatus">Lead Status <strong><code>*</code></strong></label>
                                    <select class="form-control required" id="leadStatus" name="leadStatus" aria-readonly="true" required>
                                        <option value="New">Newly Added</option>
                                        <option value="Emailed and Waiting for reply">Emailed and Waiting for reply</option>
                                        <option value="Following">Following up</option>
                                        <option value="Converted"> Converted </option>
                                        <option value="Lost">Lost</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal"> Close</button>
                    <button type="submit" class="btn btn-primary" id="multiActionDataBtn" data-loading-text="Loading..." autocomplete="off"> Update </button>
                </div> <!-- /modal-footer -->
            </div>
        </div>
    </div>
    <!-- end edit lead modal -->



    <!-- end modals -->

    <?php require_once("./shared/components/pre-footer.php");  ?>

    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/select/3.0.0/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/select/3.0.0/js/select.dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.js"></script>

    <script src="assets/custom-js/leadEmailHelper.js"></script>
    <script src="assets/default-js/jquery.cookie.js" type="text/javascript"></script>

    <script src="assets/vendors/chart.js/chart.umd.js"></script>
    <script src="assets/vendors/progressbar.js/progressbar.min.js"></script>

    <?php require_once("./shared/components/post-footer.php");  ?>