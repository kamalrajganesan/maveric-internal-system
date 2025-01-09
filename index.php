<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - Tejas </title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="./assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="./assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="./assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="./assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="./assets/vendors/typicons/typicons.css">
    <link rel="stylesheet" href="./assets/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="./assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="./assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="./assets/css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="./assets/images/favicon.png" />
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0 login-bg">
          <div class="row w-100 mx-0">
            <div class="col-lg-4 mx-auto">
              <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                <div class="brand-logo">
                  <img src="./assets/images/logoipsum.svg" alt="logo">
                </div>
                <h4>Hello Agent! let's get started</h4>
                <h6 class="fw-light">Sign in to continue</h6>
                <form class="pt-3" id="loginForm">
                  <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">@</span>
                      </div>
                      <input type="email" class="form-control" id="inputEmail" placeholder="Username" name="username" aria-label="Username" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group">
                      <input type="password" class="form-control" placeholder="Password" id="inputPassword" name="password" aria-label="Your pass phrase....">
                      <div class="input-group-append">
                        <span class="input-group-text make-text-visible"> <i class="fa fa-eye"></i> </span>
                        <span class="input-group-text make-text-invisible d-none"> <i class="fa fa-eye-slash"></i> </span>
                      </div>
                    </div>
                  </div>
                  <div class="mt-3 d-grid gap-2">
                    <button type="submit" class="btn btn-block btn-primary btn-lg fw-medium auth-form-btn">SIGN IN</a>
                  </div>
                  <div class="text-center mt-4 fw-light"> <a href="./admin-login.php" class="text-primary">Go to Admin login</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="./assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="./assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="./assets/custom-js/loginHelper.js"></script>
    <script src="../../assets/js/off-canvas.js"></script>
    <script src="../../assets/js/template.js"></script>
    <script src="../../assets/js/settings.js"></script>
    <script src="../../assets/js/hoverable-collapse.js"></script>
    <script src="../../assets/js/todolist.js"></script>
    <!-- endinject -->
  </body>
</html>