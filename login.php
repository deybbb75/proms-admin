<?php
include 'includes/init.php';
include 'head.php';
?>
<body class="loading authentication-bg login" data-layout-config='{"darkMode":false}'>
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">
                    <div class="card">

                        <div class="card-body p-4">
                            
                            <div class="text-center w-75 m-auto">
                                <h2 class="text-dark-50 text-center pb-0 fw-bold">Log In</h2>
                                <p class="text-muted mb-4">Enter your employee number to access admin panel.</p>
                            </div>

                            <form action="pages/controller/ctr-login.php" id="form_validation" method="POST">

                                <div class="mb-3">
                                    <label for="emp_no" class="form-label">Employee Number</label>
                                    <input type="text" class="form-control" name="emp_no" id="emp_no" placeholder="Enter your employee number">
                                </div>

                                <div class="mb-3">
                                    <a href="forget-pass.php" class="text-muted float-end"><small>Forgot your password?</small></a>
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" class="form-control password" id="password" name="password" placeholder="Enter your password">
                                        <div class="input-group-text" data-password="false">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 text-center">
                                    <button type="submit" class="btn btn-primary btn-login">
                                        <i class="mdi mdi-login-variant me-1"></i>
                                        LOG IN
                                    </button>
                                </div>

                            </form>
                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->

                    <!-- end row -->

                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->
    
</body>

<?php
include 'scripts.php';
?>
