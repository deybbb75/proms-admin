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
                                <h2 class="text-dark-50 text-center pb-0 fw-bold">Forget Password?</h2>
                                <p class="text-muted mb-4">Enter your email address and we'll send you an email with instructions to reset your password.</p>
                            </div>

                            <form action="pages/controller/ctr-forget-pass.php" id="form_validation" method="POST">

                                <div class="mb-3">
                                    <label for="emp_no" class="form-label">Email</label>
                                    <input class="form-control" type="email" name="email" id="emailaddress" placeholder="Enter your email">
                                </div>

                                <div class="mt-4 text-center">
                                    <button type="submit" class="btn btn-primary btn-login">
                                        <i class="mdi mdi-check-bold me-1"></i>
                                        DONE
                                    </button>
                                </div>

                            </form>
                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-muted">Remember your password? <a href="login.php" class="text-muted ms-1"><b>Log in</b></a></p>
                        </div> <!-- end col -->
                    </div>

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
