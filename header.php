<?php
include 'head.php';
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
            <a href="index.html" class="logo text-center logo-light">
                <span class="logo-lg">
                    <img src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/images/logo/logo1-white.png" alt="" height="50">
                </span>
                <span class="logo-sm">
                    <img src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/images/logo/logo-small.png" alt="" height="50">
                </span>
            </a>

            <!-- LOGO -->
            <a href="index.html" class="logo text-center logo-dark">
                <span class="logo-lg">
                    <img src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/images/logo-dark.png" alt="" height="16">
                </span>
                <span class="logo-sm">
                    <img src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/images/logo_sm_dark.png" alt="" height="16">
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
                            <span class="badge bg-success float-end">4</span>
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
                        <li class="notification-list">
                            <a class="nav-link end-bar-toggle" href="javascript: void(0);">
                                <i class="dripicons-gear noti-icon"></i>
                            </a>
                        </li>

                        <li class="dropdown notification-list">
                            <a class="nav-link dropdown-toggle nav-user arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                                aria-expanded="false">
                                <span class="account-user-avatar"> 
                                    <img src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/images/users/avatar-1.jpg" alt="user-image" class="rounded-circle">
                                </span>
                                <span>
                                    <span class="account-user-name">Dominic Keller</span>
                                    <span class="account-position">Founder</span>
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown">
                                <!-- item-->
                                <div class=" dropdown-header noti-title">
                                    <h6 class="text-overflow m-0">Welcome !</h6>
                                </div>

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <i class="mdi mdi-account-circle me-1"></i>
                                    <span>My Account</span>
                                </a>

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <i class="mdi mdi-account-edit me-1"></i>
                                    <span>Settings</span>
                                </a>

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <i class="mdi mdi-lifebuoy me-1"></i>
                                    <span>Support</span>
                                </a>

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <i class="mdi mdi-lock-outline me-1"></i>
                                    <span>Lock Screen</span>
                                </a>

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <i class="mdi mdi-logout me-1"></i>
                                    <span>Logout</span>
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