<?php
include 'head.php';
$db = DB::getInstance();

if(!isset($_SESSION['proms-admin']['emp_no'])){
    safe_redirect($GLOBALS['INF_CONFIG']['sitehost'] . "/login.php");
}

if(!isset($_SESSION['proms-admin']['pending_ay_id'])){
    $_SESSION['proms-admin']['pending_ay_id'] = $db->queryUniqueValue("SELECT ay_id FROM tbl_academic_year WHERE status = 'Active'");
}

$pending_count = $db->countOf("tbl_reservation","ay_id = :ay_id AND status = 'Pending'", ['ay_id' => $_SESSION['proms-admin']['pending_ay_id']]);
?>
<body class="loading" data-layout-color="light" data-leftbar-theme="dark" data-layout-mode="fluid" data-rightbar-onstart="true">
    
    <div class="preloader" id="main-preloader">
        <span class="loader"></span>
    </div>

    <!-- Begin page -->
    <div class="wrapper" id="wrapper">
        <!-- ========== Left Sidebar Start ========== -->
        <div class="leftside-menu" id="leftside-menu">

            <!-- LOGO -->
            <a href="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/index.php" class="logo text-center logo-light">
                <span class="logo-lg">
                    <img src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/images/logo/ctel-logo-name.png" alt="" height="50">
                </span>
                <span class="logo-sm">
                    <img src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/images/logo/ctel-logo.png" alt="" height="50">
                </span>
            </a>

            <!-- LOGO -->
            <a href="index.html" class="logo text-center logo-dark">
                <span class="logo-lg">
                    <img src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/images/logo/ctel-logo-name.png" alt="" height="50">
                </span>
                <span class="logo-sm">
                    <img src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/images/logo/ctel-logo.png" alt="" height="50">
                </span>
            </a>

            <div class="h-100" id="leftside-menu-container" data-simplebar>

                <!--- Sidemenu -->
                <ul class="side-nav" id="side-nav">

                    <li class="side-nav-title side-nav-item">Main Navigation</li>

                    <li class="side-nav-item">
                        <a href="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/index.php" class="side-nav-link">
                            <i class="mdi mdi-home"></i>
                            <span> Dashboard </span>
                        </a>
                    </li>

                    <li class="side-nav-title side-nav-item">Reservation Utilities</li>

                    <li class="side-nav-item">
                        <a href="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/pages/pending.php" class="side-nav-link">
                            <i class="mdi mdi-file-clock"></i>
                            <?php
                                if($pending_count){
                            ?>
                            <span class="badge bg-success float-end"><?= $pending_count ?></span>
                            <?php
                                }
                            ?>
                            <span>Pending List</span>
                        </a>
                    </li>

                    <li class="side-nav-item">
                        <a href="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/pages/enrolled.php" class="side-nav-link">
                            <i class="mdi mdi-file-check"></i>
                            <span>Enrolled List</span>
                        </a>
                    </li>

                    <li class="side-nav-item">
                        <a href="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/pages/reserved.php" class="side-nav-link">
                            <i class="mdi mdi-file-alert"></i>
                            <span>Reserved List</span>
                        </a>
                    </li>

                    <li class="side-nav-item">
                        <a href="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/pages/expired.php" class="side-nav-link">
                            <i class="mdi mdi-file-cancel"></i>
                            <span>Expired List</span>
                        </a>
                    </li>

                    <li class="side-nav-title side-nav-item">System Utilities</li>

                    <li class="side-nav-item">
                        <a href="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/pages/academic-year.php" class="side-nav-link">
                            <i class="mdi mdi-calendar-month"></i>
                            <span> Academic Years </span>
                        </a>
                    </li>

                    <li class="side-nav-item">
                        <a href="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/pages/banner.php" class="side-nav-link">
                            <i class="mdi mdi-image-multiple"></i>
                            <span> Banner </span>
                        </a>
                    </li>

                    <li class="side-nav-item">
                        <a href="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/pages/about.php" class="side-nav-link">
                            <i class="mdi mdi-account-question"></i>
                            <span> About Us </span>
                        </a>
                    </li>

                    <li class="side-nav-item">
                        <a href="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/pages/team.php" class="side-nav-link">
                            <i class="mdi mdi-account-group"></i>
                            <span> Our Team </span>
                        </a>
                    </li>

                    <li class="side-nav-item">
                        <a href="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/pages/program.php" class="side-nav-link">
                            <i class="mdi mdi-certificate"></i>
                            <span> Programs </span>
                        </a>
                    </li>

                    <li class="side-nav-item">
                        <a href="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/pages/news.php" class="side-nav-link">
                            <i class="mdi mdi-newspaper"></i>
                            <span> News & Events </span>
                        </a>
                    </li>

                    <li class="side-nav-item">
                        <a href="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/pages/student.php" class="side-nav-link">
                            <i class="mdi mdi-school"></i>
                            <span> Student Accounts </span>
                        </a>
                    </li>

                    <li class="side-nav-item">
                        <a href="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/pages/system-user.php" class="side-nav-link">
                            <i class="mdi mdi-account-multiple"></i>
                            <span> System Users </span>
                        </a>
                    </li>
                </ul>
                <!-- End Sidebar -->

                <div class="clearfix"></div>

            </div>
            <!-- Sidebar -left -->

        </div>
        <!-- Left Sidebar End -->


        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">
                <!-- Topbar Start -->
                <div class="navbar-custom">
                    <ul class="list-unstyled topbar-menu float-end mb-0">
                        <li class="dropdown notification-list">
                            <a class="nav-link dropdown-toggle nav-user arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                                aria-expanded="false">
                                <span class="account-user-avatar"> 
                                    <img src="<?= $_SESSION['proms-admin']['img_src'] ?? $GLOBALS['INF_CONFIG']['sitehost'] . '/assets/images/profile.jpg' ?>" alt="user-image" class="rounded-circle">
                                </span>
                                <span>
                                    <span class="account-user-name" style="margin-top: 2px;"><?= $_SESSION['proms-admin']['fullname'] ?></span>
                                    <span class="account-position">Admin</span>
                                </span>
                            </a>
                        </li>
                        <li>
                            <div style="padding-top: 20px; padding-left: 10px; padding-right: 20px;">
                                <a href="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/clear.php">
                                    <i class="mdi mdi-login-variant" style="font-size: 1.5em; color: #7b7b7b;"></i>
                                </a>
                            </div>
                        </li>
                    </ul>
                    <button class="button-menu-mobile open-left">
                        <i class="mdi mdi-menu"></i>
                    </button>
                    <div class="system-title-container">
                        <h3 class="system-title">Program Reservation, Organization, and Management System</h3>
                        <h3 class="system-title-short">PROMS</h3>
                    </div>
                </div>
                <!-- end Topbar -->