<?php
include '../../includes/init.php';
$db = DB::getInstance();

$redirect_path = '../../login.php';

try {
    $sys_id = decrypt_data($_POST['sys_id']);
    $employee = $db->queryUniqueObject("SELECT * FROM tbl_system_user WHERE sys_id = :sys_id", ['sys_id' => $sys_id]);
    if ($employee) {
        $new_password = $_POST['create_password'];

        $hash = password_hash(
            $new_password,
            PASSWORD_ARGON2ID,
            [
                'memory_cost' => 65536, // 64 MB
                'time_cost'   => 3,     // iterations
                'threads'     => 2
            ]
        );

        if ($hash === false) {
            throw new Exception('Password hashing failed');
        }

        $db->executeUpdate(['password' => $hash], 'tbl_system_user', 'sys_id = :sys_id', ['sys_id' => $sys_id]);

        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Password Reset Successful',
                'text'  => 'Your password has been reset successfully. You can now log in with your new password.',
                'path'  => '../../login.php'
            ));
        }
    } else {
        Alert::error(array(
            'title' => 'Account Not Found',
            'text'  => 'We couldn’t find an account with the provided information.',
            'path'  => '../../login.php'
        ));
    }
} catch (Exception $e) {
    console_error('An error occurred during password reset: ' . $e->getMessage());
}
?>