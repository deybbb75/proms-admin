<?php
include 'includes/init.php';
include 'head.php';
$db = DB::getInstance();

$token = $_GET['token'] ?? null;

if (!$token) {
    safe_redirect('error.php');
}else{
    $active_resets = $db->query("SELECT * FROM tbl_pass_reset WHERE status = 'Active'");

    $validTokenId = null;

    while ($line = $db->fetchNextObject($active_resets)) {
        if (password_verify($token, $line->token)) {
            $validTokenId = $line->reset_id;
            break; // stop at the first valid match
        }
    }

    if($validTokenId) {
        $active_reset = $db->queryUniqueObject("SELECT * FROM tbl_pass_reset WHERE reset_id = :reset_id", ['reset_id' => $validTokenId]);
        
        if($active_reset->expire_datetime <= date('Y-m-d H:i:s')) {
            $db->executeUpdate(['status' => 'Expired'], 'tbl_pass_reset', 'reset_id = :reset_id', ['reset_id' => $active_reset->reset_id]);

            Alert::error(array(
                'title' => 'Link Expired',
                'text'  => 'Your password reset link has expired. Please request a new one.',
                'path'  => 'login.php'
            ));
        }else{
            $sys_id = encrypt_data($active_reset->account_id);
        }
    }else{
        Alert::error(array(
            'title' => 'Link Expired',
            'text'  => 'Your password reset link has expired. Please request a new one.',
            'path'  => 'login.php'
        ));
    }
}
?>
<body class="loading authentication-bg login" data-layout-config='{"darkMode":false}'>
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-7">
                    <div class="card">

                        <div class="card-body p-4">
                            
                            <div class="text-center w-75 m-auto login-header">
                                <h2 class="text-dark-50 text-center pb-0 fw-bold">Reset Password</h2>
                                <p class="text-muted mb-4">Enter your new password</p>
                            </div>

                            <form action="pages/controller/ctr-reset-pass.php" method="POST" id="form_validation">
                                <input type="hidden" name="sys_id" value="<?= $sys_id ?? '' ?>">

                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" class="form-control password" id="create_password" name="create_password" placeholder="Enter your password">
                                        <div class="input-group-text" data-password="false">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Confirm Password</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" class="form-control password" id="password" name="confirm_password" placeholder="Enter your password">
                                        <div class="input-group-text" data-password="false">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 text-center">
                                    <button class="btn btn-primary btn-login" type="submit">
                                        <i class="mdi mdi-reload me-2"></i>
                                        RESET
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
