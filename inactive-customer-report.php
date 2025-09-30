<?php 

require_once("./shared/components/pre-header.php");

?>

<title> My Reports - Tejas </title>

<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="./assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="./assets/css/flatpickr.min.css">
<link rel="stylesheet" href="./assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">

<?php require_once("./shared/components/post-header.php");  ?>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="row">
            <div class="col-sm-12 short-l">
                <div class="d-sm-flex align-items-center justify-content-end border-bottom">
                </div>
            </div>
        </div>

       <!-- Filter Section -->
<div class="row py-3">
    <div class="col-sm-12">
        <div class="card card-rounded">
            <div class="card-body fs-14">
                <!-- First Row: Transaction Date Range + Transaction Date + Service Type + Service Through -->
<div class="row mb-3">
    <div class="col-md-3">
        <div class="form-group">
            <label class="mb-1">Transaction Date Range</label>
            <input type="text" class="form-control" id="dateRange" placeholder="DD/MM/YYYY">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="mb-1">Transaction Date</label>
            <input type="text" class="form-control date-picker" id="singleDate" placeholder="DD/MM/YYYY">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="mb-1">Service Type</label>
            <select class="form-control" id="serviceType">
                <option value="">All Service Types</option>
                <option value="AMC">AMC</option>
                <option value="Tally Subscription">Tally Subscription</option>
                <option value="Cloud">Cloud</option>
                <option value="One Time">One Time</option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="mb-1">Service Through</label>
            <select class="form-control" id="serviceThrough">
                <option value="">All</option>
                <option value="Phone Call">Phone Call</option>
                <option value="Remote">Remote</option>
                <option value="Physical Visit">Physical Visit</option>
            </select>
        </div>
    </div>
</div>

                <!-- Second Row: Filter and Reset Buttons -->
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary" id="filterBtn">Filter</button>
                        <button type="button" class="btn btn-info ml-2" id="resetBtn">Reset</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


        <!-- Table Section -->
        <div class="row py-3">
            <div class="col-sm-12">
                <div class="card card-rounded">
                    <div class="card-body fs-14">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="inactiveCustomerMasterTbl" class="display nowrap ">
    <thead>
        <tr>
            <th>S. No.</th>
            <th>Company Name</th>
            <th>Service Consumed</th>
            <th>Total Services Consumed</th>
            <th>Last Service Date</th>
            <th>Days Since Last Service</th>
        </tr>
    </thead>
    <tbody></tbody>
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

    <!-- view customer modal -->
    <div class="modal fade" id="viewCustomerModal" tabindex="-1" role="dialog" aria-labelledby="viewCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="viewCustomerModalLabel">Customer Details</h4>
                    <button type="button" class="btn btn-inverse-light btn-fw" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="viewCustomerForm" class="form-sample">
                        <div class="row justify-content-center">
                            <div class="col-sm-5">
                                
                                <div class="form-group view-form-group">
                                    <label for="customerUniqCode">Customer Serial Number <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="customerUniqCode" name="customerUniqCode" placeholder="Customer Unique Code" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="customerName">Customer Name </label>
                                    <input type="text" class="form-control" id="customerName" name="customerName" placeholder="Customer Name">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="companyName">Company Name <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="companyName" name="companyName" placeholder="Company Name" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="contact">Mobile Number <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="telephone">Telephone </label>
                                    <input type="text" class="form-control" id="telephone" name="telephone" placeholder="Telephone">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="email">Communication Email <strong><code>*</code></strong></label>
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
                                    <label for="Active Status">Customer Status</label>
                                    <select class="form-control" id="customerStatus" name="customerStatus" required aria-readonly="true">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="createdBy">Customer Created By</label>
                                    <input type="text" class="form-control" id="createdBy" name="createdBy" placeholder="Created By" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="createdOn">Customer Created On</label>
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
                                            <input type="checkbox" class="form-check-input" name="serviceType" value="Cloud"> Cloud </label>
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

                                <div class="form-group view-form-group AMCService">
                                    <label for="amcStartDate">AMC Service Start Date <strong><code>*</code></strong></label>
                                    <input type="date" class="form-control" id="amcStartDate" name="amcStartDate" >
                                </div>

                                <div class="form-group view-form-group AMCService">
                                    <label for="amcEndDate">AMC Service End Date <strong><code>*</code></strong></label>
                                    <input type="date" class="form-control" id="amcEndDate" name="amcEndDate" >
                                </div>

                                <div class="form-group view-form-group tallyService">
                                    <label for="tallyStartDate">Tally Subscription Start Date <strong><code>*</code></strong></label>
                                    <input type="date" class="form-control" id="tallyStartDate" name="tallyStartDate" >
                                </div>

                                <div class="form-group view-form-group tallyService">
                                    <label for="tallyEndDate">Tally Subscription End Date <strong><code>*</code></strong></label>
                                    <input type="date" class="form-control" id="tallyEndDate" name="tallyEndDate" >
                                </div>

                                <div class="form-group view-form-group cloudService">
                                    <label for="cloudStartDate">Cloud Start Date </label>
                                    <input type="date" class="form-control" id="cloudStartDate" name="cloudStartDate" >
                                </div>

                                <div class="form-group view-form-group cloudService">
                                    <label for="cloudEndDate">Cloud End Date </label>
                                    <input type="date" class="form-control" id="cloudEndDate" name="cloudEndDate" >
                                </div>

                                <div class="form-group view-form-group tallyService">
                                    <label for="licenseType"> License Type  <strong><code>*</code></strong></label>
                                    <select class="form-control" id="licenseType" name="licenseType" disabled aria-readonly="true">
                                        <option value="" selected hidden>Select the License Type</option>
                                        <option value="Single User">Single User</option>
                                        <option value="Multi-user">Multi-user</option>
                                        <option value="Auditor Pack">Auditor Pack</option>
                                        <option value="Rental">Rental</option>
                                    </select>
                                </div>

                                <div class="form-group view-form-group tallyService">
                                    <label for="tallyEmail">Tally Mail Id <strong><code>*</code></strong></label>
                                    <input type="email" class="form-control" id="tallyEmail" name="tallyEmail" placeholder="System Email" >
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="specialNote">Special Note</label>
                                    <input type="text" class="form-control" id="specialNote" name="specialNote" placeholder="Special Note" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="referredBy">Referred by <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="referredBy" name="referredBy" placeholder="Referred by who ?">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="auditor">Customer's Auditor </label>
                                    <input type="text" class="form-control" id="auditor" name="auditor" placeholder="Customer's Auditor">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="updatedBy">Customer Updated By</label>
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
    <!-- end view customer modal -->

    <!-- end modals -->

    <?php require_once("./shared/components/pre-footer.php");  ?>

    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="assets/default-js/jquery.cookie.js" type="text/javascript"></script>
    <script src="assets/vendors/select2/select2.min.js"></script>
    <script src="assets/default-js/flatpickr.js"></script>
    <script src="assets/custom-js/inactiveCustomerReportHelper.js"></script>

   

    <?php require_once("./shared/components/post-footer.php");  ?>