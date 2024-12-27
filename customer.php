<?php  require_once("./shared/components/pre-header.php");  ?>
<title> My Customers - Tejas </title>

<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">

<?php  require_once("./shared/components/post-header.php");  ?>

    <div class="main-panel">
        <div class="content-wrapper">

            <div class="row">
                <div class="col-sm-12 short-l">
                    <div class="d-sm-flex align-items-center justify-content-end border-bottom">
                        <div>
                            <div class="btn-wrapper">
                                <a href="#" class="btn btn-otline-dark align-items-center" data-toggle="modal" data-target="#addCustomerModal" id="addCustomerModalBtn">
                                    <i class="fa fa-plus"></i> Add New Customer 
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row py-3">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="customerMasterTbl" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Customer Code</th>
                                        <th>Name</th>
                                        <th>Company</th>
                                        <th>City/ Pincode</th>
                                        <th>Phone</th>
                                        <th>Service(s) Offered</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                            </table>
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
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="addCustomerForm" class="form-sample">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        
                                        <label for="customerName">Customer Name</label>
                                        <input type="text" class="form-control" id="customerName" name="customerName" placeholder="Customer Name" required>

                                        <label for="companyName">Customer Name</label>
                                        <input type="text" class="form-control" id="companyName" name="companyName" placeholder="Company Name" required>
                                        
                                        <label for="contact">Contact</label>
                                        <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact" required>
                                        
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                        
                                        <label for="serviceType">Service Type</label>
                                        <input type="text" class="form-control" id="serviceType" name="serviceType" placeholder="Service Type" required>

                                        <label for="address">Address</label>
                                        <input type="text" class="form-control" id="address" name="address" placeholder="Address" required>

                                        <label for="area">Area</label>
                                        <input type="text" class="form-control" id="area" name="area" placeholder="Area" required>
                                        
                                        <label for="pincode">Pincode</label>
                                        <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode" required>

                                        <label for="city">City</label>
                                        <input type="text" class="form-control" id="city" name="city" placeholder="City" required>

                                        <label for="specialNote">Special Note</label>
                                        <input type="text" class="form-control" id="specialNote" name="specialNote" placeholder="Special Note" required>

                                        <label for="licenseType">License Type</label>
                                        <input type="text" class="form-control" id="licenseType" name="licenseType" placeholder="License Type" required>

                                        <label for="systemEmail">System Email</label>
                                        <input type="email" class="form-control" id="systemEmail" name="systemEmail" placeholder="System Email" required>

                                        <label for="customerUniqCode">Customer Unique Code</label>
                                        <input type="text" class="form-control" id="customerUniqCode" name="customerUniqCode" placeholder="Customer Unique Code" required>

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- end add customer modal -->

        <!-- end modals -->

<?php  require_once("./shared/components/pre-footer.php");  ?>


<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>

<script src="assets/custom-js/customerHelper.js"></script>
<script src="assets/default-js/jquery.cookie.js" type="text/javascript"></script>

<script src="assets/vendors/chart.js/chart.umd.js"></script>
<script src="assets/vendors/progressbar.js/progressbar.min.js"></script>

<?php  require_once("./shared/components/post-footer.php");  ?>