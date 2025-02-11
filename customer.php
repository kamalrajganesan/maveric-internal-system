<?php require_once("./shared/components/pre-header.php");

if (isset($_GET['page'])) {
    $page = htmlspecialchars($_GET['page']);

    echo "<script>
        var customer_page = '" . $page . "'
    </script>";
} else {
    echo "Check the URL param";
}

?>
<title> My Customers - Tejas </title>

<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">

<?php require_once("./shared/components/post-header.php");  ?>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="row">
            <div class="col-sm-12 short-l">
                <div class="d-sm-flex align-items-center justify-content-end border-bottom">
                    <div>
                        <div class="btn-wrapper">
                            <button class="btn btn-otline-dark align-items-center" data-bs-toggle="modal" data-bs-target="#addCustomerModal" id="addCustomerModalBtn">
                                <i class="fa fa-plus"></i> Add New Customer
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
                                <table id="customerMasterTbl" class="display nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>S. No.</th>
                                            <th>Serial Number</th>
                                            <th>Company</th>
                                            <th>Service(s) Offered</th>
                                            <th>Mobile Number</th>
                                            <th>City/ Pincode</th>
                                            <th>Communication Email</th>
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
    <!-- add customer modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addCustomerModalLabel">Add New Customer</h4>
                    <button type="button" class="btn btn-inverse-light btn-fw" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="addCustomerForm" class="form-sample">
                        <div class="row justify-content-center">
                            <div class="col-sm-5">

                                <div class="form-group view-form-group">
                                    <label for="customerUniqCode">Customer Serial Number <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="customerUniqCode" name="customerUniqCode" placeholder="Customer Unique Code" required>
                                </div>
                                
                                <div class="form-group view-form-group">
                                    <label for="companyName">Company Name <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="companyName" name="companyName" placeholder="Company Name" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="customerName">Customer Name </label>
                                    <input type="text" class="form-control" id="customerName" name="customerName" placeholder="Customer Name">
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
                                    <label for="address">Address </label>
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
                                            <input type="checkbox" class="form-check-input" name="serviceType[]" value="Cloud"> Cloud </label>
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

                                <div class="form-group view-form-group AMCService">
                                    <label for="amcStartDate">AMC Service Start Date </label>
                                    <input type="date" class="form-control" id="amcStartDate" name="amcStartDate" >
                                </div>

                                <div class="form-group view-form-group AMCService">
                                    <label for="amcEndDate">AMC Service End Date  </label>
                                    <input type="date" class="form-control" id="amcEndDate" name="amcEndDate" >
                                </div>

                                <div class="form-group view-form-group tallyService">
                                    <label for="tallyStartDate">Tally Subscription Start Date </label>
                                    <input type="date" class="form-control" id="tallyStartDate" name="tallyStartDate" >
                                </div>

                                <div class="form-group view-form-group tallyService">
                                    <label for="tallyEndDate">Tally Subscription End Date </label>
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
                                    <label for="licenseType"> License Type  </label>
                                    <select class="form-control " id="licenseType" name="licenseType"  aria-readonly="true" >
                                        <option value="" selected hidden>Select the License Type</option>
                                        <option value="Single User">Single User</option>
                                        <option value="Multi-user">Multi-user</option>
                                        <option value="Auditor Pack">Auditor Pack</option>
                                        <option value="Rental">Rental</option>
                                    </select>
                                </div>
                                
                                <div class="form-group view-form-group tallyService">
                                    <label for="tallyEmail">Tally Mail Id </label>
                                    <input type="email" class="form-control" id="tallyEmail" name="tallyEmail" placeholder="System Email" >
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="specialNote">Special Note</label>
                                    <input type="text" class="form-control" id="specialNote" name="specialNote" placeholder="Special Note">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="referredBy">Referred by </label>
                                    <input type="text" class="form-control" id="referredBy" name="referredBy" placeholder="Referred by who ?">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="auditor">Customer's Auditor </label>
                                    <input type="text" class="form-control" id="auditor" name="auditor" placeholder="Customer's Auditor">
                                </div>
                                
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal"> Close</button>
                    <button type="submit" class="btn btn-primary" id="addCustomerDataBtn" data-loading-text="Loading..." autocomplete="off"> Create Customer</button>
                </div> <!-- /modal-footer -->
            </div>
        </div>
    </div>
    <!-- end add customer modal -->

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

                            <div class="col-9">    
                                <div id="customerDetails" class="customerDetailsPanel"></div>
                            </div>

                            <div class="col-sm-5">

                                <div class="form-group view-form-group">
                                    <label for="companyName">Customer <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="companyName" name="companyName">
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
                                    <select class="form-control" id="status" name="status" >
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
                                    <input type="text" class="form-control" id="serviceType" name="serviceType" required>
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

    <!-- edit customer modal -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editCustomerModalLabel">Edit Customer Details</h4>
                    <button type="button" class="btn btn-inverse-light btn-fw" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="editCustomerForm" class="form-sample">
                        <div class="row justify-content-center">
                            <div class="col-sm-5">

                                <div class="form-group view-form-group">
                                    <label for="customerUniqCode">Customer Serial Number <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="customerUniqCode" name="customerUniqCode" placeholder="Customer Unique Code" required>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="customerName">Customer Name <strong><code>*</code></strong></label>
                                    <input type="text" class="form-control" id="customerName" name="customerName" placeholder="Customer Name" required>
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
                                    <label for="Active Status">Customer Status <strong><code>*</code></strong></label>
                                    <select class="form-control" id="customerStatus" name="customerStatus" required >
                                        <option value="" selected hidden>Select the Customer Status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="createdBy">Customer Created By</label>
                                    <input type="text" class="form-control" id="createdBy" name="createdBy" placeholder="Created By" required readonly>
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="createdOn">Customer Created On</label>
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
                                            <input type="checkbox" class="form-check-input" name="serviceType[]" value="Cloud"> Cloud </label>
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
                                    <select class="form-control " id="licenseType" name="licenseType"  aria-readonly="true">
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
                                    <label for="referredBy">Referred by </label>
                                    <input type="text" class="form-control" id="referredBy" name="referredBy" placeholder="Referred by who ?">
                                </div>

                                <div class="form-group view-form-group">
                                    <label for="auditor">Customer's Auditor </label>
                                    <input type="text" class="form-control" id="auditor" name="auditor" placeholder="Customer's Auditor">
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer editCustomerDataFooter">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"> Close</button>
                    <button type="submit" class="btn btn-primary" id="editCustomerDataBtn" data-loading-text="Loading..." autocomplete="off"> Update Customer</button>
                </div> <!-- /modal-footer -->
            </div>
        </div>
    </div>
    <!-- end edit customer modal -->

    <!-- end modals -->

    <?php require_once("./shared/components/pre-footer.php");  ?>


    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>

    <script src="assets/custom-js/customerHelper.js"></script>
    <script src="assets/default-js/jquery.cookie.js" type="text/javascript"></script>

    <script src="assets/vendors/chart.js/chart.umd.js"></script>
    <script src="assets/vendors/progressbar.js/progressbar.min.js"></script>

    <?php require_once("./shared/components/post-footer.php");  ?>