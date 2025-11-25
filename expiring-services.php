<?php 
require_once("./shared/components/pre-header.php");
?>

<title>Expiring Services - Tejas</title>

<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="./assets/css/flatpickr.min.css">

<?php require_once("./shared/components/post-header.php"); ?>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="row">
            <div class="col-sm-12 short-l">
                <div class="d-sm-flex align-items-center justify-content-between border-bottom">
                    <h4 class="font-weight-bold mb-3">Expiring Services</h4>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="row py-3">
            <div class="col-sm-12">
                <div class="card card-rounded">
                    <div class="card-body fs-14">
                       <div class="row mb-3">
    <!-- Expiry Date Range -->
    <div class="col-md-4">
        <div class="form-group">
            <label class="mb-1">Expiry Date Range</label>
            <input type="text" class="form-control" id="dateRange" placeholder="DD/MM/YYYY to DD/MM/YYYY">
        </div>
    </div>
<!-- Service Type dropdown - Removed "All Services" option, AMC is now default -->
<div class="col-md-4">
    <div class="form-group">
        <label class="mb-1">Service Type</label>
        <select id="serviceFilter" class="form-control">
            <option value="AMC">AMC</option>
            <option value="Tally Subscription">Tally Subscription</option>
            <option value="Cloud">Cloud</option>
        </select>
    </div>
</div>


                        

                        <!-- Buttons -->
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
                               <!-- Update your table header to match the new column order -->
<table id="expiringServicesTable" class="display nowrap" style="width:100%">
    <thead>
        <tr>
            <th>S. No.</th>
            <th>Company Name</th>
            <th>Contact Number</th>
            <th>Services Offered</th>
            <th>Expiry Date</th>
            <th>Agent Name</th>
            <th>Action</th>
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

    <!-- View Customer Modal -->
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
                                    <label for="customerStatus">Customer Status</label>
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
                                    <label for="serviceType">Service Type <strong><code>*</code></strong></label>
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
                                    <input type="date" class="form-control" id="amcStartDate" name="amcStartDate">
                                </div>

                                <div class="form-group view-form-group AMCService">
                                    <label for="amcEndDate">AMC Service End Date <strong><code>*</code></strong></label>
                                    <input type="date" class="form-control" id="amcEndDate" name="amcEndDate">
                                </div>

                                <div class="form-group view-form-group tallyService">
                                    <label for="tallyStartDate">Tally Subscription Start Date <strong><code>*</code></strong></label>
                                    <input type="date" class="form-control" id="tallyStartDate" name="tallyStartDate">
                                </div>

                                <div class="form-group view-form-group tallyService">
                                    <label for="tallyEndDate">Tally Subscription End Date <strong><code>*</code></strong></label>
                                    <input type="date" class="form-control" id="tallyEndDate" name="tallyEndDate">
                                </div>

                                <div class="form-group view-form-group cloudService">
                                    <label for="cloudStartDate">Cloud Start Date</label>
                                    <input type="date" class="form-control" id="cloudStartDate" name="cloudStartDate">
                                </div>

                                <div class="form-group view-form-group cloudService">
                                    <label for="cloudEndDate">Cloud End Date</label>
                                    <input type="date" class="form-control" id="cloudEndDate" name="cloudEndDate">
                                </div>

                                <div class="form-group view-form-group tallyService">
                                    <label for="licenseType">License Type <strong><code>*</code></strong></label>
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
                                    <input type="email" class="form-control" id="tallyEmail" name="tallyEmail" placeholder="System Email">
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
                                    <label for="auditor">Customer's Auditor</label>
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
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End View Customer Modal -->

    <?php require_once("./shared/components/pre-footer.php"); ?>

    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="assets/default-js/jquery.cookie.js" type="text/javascript"></script>
    <script src="assets/default-js/flatpickr.js"></script>
    <script src="assets/custom-js/expiringservices.js"></script>

    <?php require_once("./shared/components/post-footer.php"); ?>