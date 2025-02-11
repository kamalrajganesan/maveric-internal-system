</head>

<?php
  if(!session_id()) {
    session_start();
  }
  date_default_timezone_set('Asia/Kolkata');
  // Check if user is logged in / Redirect to login page if not
  if (!isset($_SESSION['user'])) {
    header("Location: ./index.php");
    exit();
  }
?>

  <body class="with-welcome-text">
    <div class="container-scroller">
      <!-- partial:../../partials/_navbar.html -->
      <nav class="navbar default-layout col-lg-12 col-12 p-0 fixked-top d-flex align-items-top flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
          <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
              <span class="icon-menu"></span>
            </button>
          </div>
          <div>
            <a class="navbar-brand brand-logo" href="#">
              <img src="./assets/images/logoipsum.svg" alt="logo" width="100%"  />
            </a>
            <a class="navbar-brand brand-logo-mini" href="#">
              <img src="./assets/images/logoipsum.svg" alt="logo" width="100%" />
            </a>
          </div>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-top">
          <ul class="navbar-nav">
            <li class="nav-item fw-semibold d-none d-lg-block ms-0">
              <h1 class="welcome-text">Hello, <span class="text-black fw-bold"> <?php  echo $_SESSION['user']['nm'] ?> </span></h1>
            </li>
          </ul>
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <form class="search-form" action="#">
                <i class="icon-search"></i>
                <input type="search" class="form-control" placeholder="Search Here" title="Search here">
              </form>
            </li>
            <li class="nav-item dropdown d-none d-lg-block user-dropdown">
              <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <img class="img-xs rounded-circle" src="./assets/images/faces/face8.jpg" alt="Profile image"> </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                <div class="dropdown-header text-center">
                  <img class="img-md rounded-circle" src="./assets/images/faces/face8.jpg" alt="Profile image">
                  <p class="mb-1 mt-3 fw-semibold"> <?php  echo $_SESSION['user']['nm'] ?> </p>
                  <p class="fw-light text-muted mb-0"> <?php  echo $_SESSION['user']['email'] ?> </p>
                </div>
                <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> My Profile <span class="badge badge-pill badge-danger">1</span></a>
                <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-message-text-outline text-primary me-2"></i> Messages</a>
                <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-calendar-check-outline text-primary me-2"></i> Activity</a>
                <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-help-circle-outline text-primary me-2"></i> FAQ</a>
                <a class="dropdown-item" href="./logout.php"><i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Sign Out</a>
              </div>
            </li>
          </ul>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
          </button>
        </div>
      </nav>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:./partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item active">
              <a class="nav-link" href="./dashboard.php">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
              </a>
            </li>
            
            
            <?php 

              if ($_SESSION['userType'] == 'admin') {
                echo '
                
            <li class="nav-item nav-category">Admin Area</li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#agents-submenus" aria-expanded="false" aria-controls="agents-submenus">
                <i class="menu-icon mdi mdi-floor-plan"></i>
                <span class="menu-title">Agents</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="agents-submenus">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="./agents.php">All Agents</a></li>
                  <li class="nav-item"> <a class="nav-link" href="./agent-stats-admnvw.php">Agent Statistics</a></li>
                  <li class="nav-item"> <a class="nav-link" href="./agent-reviews-admnvw.php">Agent Reviews</a></li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="all-customers-admnv.php">
                <i class="menu-icon mdi mdi-floor-plan"></i>
                <span class="menu-title">Customers</span>
              </a>
            </li>

                ';
              }
            
            ?>
            
            <li class="nav-item nav-category"> Menus </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#customer-submenus" aria-expanded="false" aria-controls="customer-submenus">
                <i class="menu-icon mdi mdi-floor-plan"></i>
                <span class="menu-title">Customers</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="customer-submenus">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="./customer.php?page=All">All Customers</a></li>
                  <li class="nav-item"> <a class="nav-link" href="./customer.php?page=AMC">AMC</a></li>
                  <li class="nav-item"> <a class="nav-link" href="./customer.php?page=Tally">Tally Subscription</a></li>
                  <li class="nav-item"> <a class="nav-link" href="./customer.php?page=Cloud">Cloud</a></li>
                  <li class="nav-item"> <a class="nav-link" href="./customer.php?page=OneTime">One Time</a></li>
                  <li class="nav-item"> <a class="nav-link" href="./customer.php?page=Active">Active</a></li>
                  <li class="nav-item"> <a class="nav-link" href="./customer.php?page=InActive">In Active</a></li>
                  <!-- <li class="nav-item"> <a class="nav-link" href="./customer.php?page=Endangered">Endangered</a></li> -->
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#transac-submeuns" aria-expanded="false" aria-controls="transac-submeuns">
                <i class="menu-icon mdi mdi-card-text-outline"></i>
                <span class="menu-title">Transactions</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="transac-submeuns">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"><a class="nav-link" href="./transactions.php?type=All&value=All">All Services</a></li>
                  <li class="nav-item"><a class="nav-link" href="./transactions.php?type=serviceType&value=AMC">AMC</a></li>
                  <li class="nav-item"><a class="nav-link" href="./transactions.php?type=serviceType&value=Tally">Tally Subscription</a></li>
                  <li class="nav-item"><a class="nav-link" href="./transactions.php?type=serviceType&value=Cloud">Cloud</a></li>
                  <li class="nav-item"><a class="nav-link" href="./transactions.php?type=serviceType&value=OneTime">One Time</a></li>
                  <li class="nav-item"><a class="nav-link" href="./transactions.php?type=serviceThrough&value=Remote">Remote</a></li>
                  <li class="nav-item"><a class="nav-link" href="./transactions.php?type=serviceThrough&value=OnCall">Phone Call</a></li>
                  <li class="nav-item"><a class="nav-link" href="./transactions.php?type=serviceThrough&value=PhysicalVisits">Physical Visit</a></li>
                </ul>
              </div>
            </li>
            <!-- <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#requirements-menus" aria-expanded="false" aria-controls="requirements-menus">
                <i class="menu-icon mdi mdi-chart-line">  </i>
                <span class="menu-title">Requirements</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="requirements-menus">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="./newy-added-requirements.php"> Newly Added </a></li>
                  <li class="nav-item"> <a class="nav-link" href="./pending-requirements.php"> Pending </a></li>
                  <li class="nav-item"> <a class="nav-link" href="./follwing-up-requirements.php"> On Follow Ups </a></li>
                  <li class="nav-item"> <a class="nav-link" href="./converted-requirements.php"> Converted </a></li>
                  <li class="nav-item"> <a class="nav-link" href="./deprecated-requirements.php"> Deprecated </a></li>
                  <li class="nav-item"> <a class="nav-link" href="./lost-requirements.php"> Lost </a></li>
                </ul>
              </div>
            </li> -->
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#lead-menus" aria-expanded="false" aria-controls="lead-menus">
                <i class="menu-icon mdi mdi-chart-line">  </i>
                <span class="menu-title">Leads</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="lead-menus">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="./leads.php?page=New"> Newly Added </a></li>
                  <li class="nav-item"> <a class="nav-link" href="./leads.php?page=Contacted"> Contacted </a></li>
                  <li class="nav-item"> <a class="nav-link" href="./leads.php?page=Converted"> Converted </a></li>
                  <li class="nav-item"> <a class="nav-link" href="./leads.php?page=Following"> Follow Ups </a></li>
                  <li class="nav-item"> <a class="nav-link" href="./leads.php?page=Lost"> Lost </a></li>
                </ul>
              </div>
            </li>
            <!-- requirement list -->
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#requirement-menus" aria-expanded="false" aria-controls="requirement-menus">
                <i class="menu-icon mdi mdi-chart-timeline-variant">  </i>
                <span class="menu-title">Requirement</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="requirement-menus">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="./requirement.php?page=New"> Newly Added </a></li>
                  <li class="nav-item"> <a class="nav-link" href="./requirement.php?page=Contacted"> Contacted </a></li>
                  <li class="nav-item"> <a class="nav-link" href="./requirement.php?page=Converted"> Converted </a></li>
                  <li class="nav-item"> <a class="nav-link" href="./requirement.php?page=Following"> Follow Ups </a></li>
                  <li class="nav-item"> <a class="nav-link" href="./requirement.php?page=Lost"> Lost </a></li>
                </ul>
              </div>
            </li>
            <!-- requirement list -->
            <li class="nav-item">
              <a class="nav-link" href="#">
                <i class="menu-icon mdi mdi-file-document"></i>
                <span class="menu-title">Reference Docs</span>
              </a>
            </li>
          </ul>
        </nav>
        <!-- partial -->