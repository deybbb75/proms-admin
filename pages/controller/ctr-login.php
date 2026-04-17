<?php
include '../../includes/init.php';
$db = DB::getInstance();

$redirect_path = '../../login.php';

$emp_no = $_POST['emp_no'];
$enteredPassword = $_POST['password'];

try {
    $employee = $db->queryUniqueObject("SELECT * FROM tbl_system_user WHERE emp_no = :emp_no AND status = 'Active'", ['emp_no' => $emp_no]);
    if ($employee) {
        $_SESSION['proms-admin']['sys_id']      = $employee->sys_id;
        $_SESSION['proms-admin']['emp_no']      = $employee->emp_no;
        $_SESSION['proms-admin']['fullname']    = $employee->fname . ' ' . $employee->mname . ' ' . $employee->lname;
        $_SESSION['proms-admin']['email']       = $employee->email;
        $_SESSION['proms-admin']['password']    = $employee->password;
        $image                                  = $employee->img;

        if($image){
            $image_data                             = base64_encode($image);
            $image_type                             = $employee->img_type;
            $_SESSION['proms-admin']['img_src']     = "data:{$image_type};base64,{$image_data}";
        }

        if (password_verify($enteredPassword, $_SESSION['proms-admin']['password'])) {
            $sqlArray = array(
                'account_id'    => $_SESSION['proms-admin']['sys_id'],
                'account_type'  => 'Employee',
                'email'        => $_SESSION['proms-admin']['email'],
                'fullname'      => $_SESSION['proms-admin']['fullname'],
                'log_datetime'  => date('Y-m-d H:i:s'),
            );
            $db->executeInsert($sqlArray, 'tbl_login_logs');

            if ($db->affectedRows > 0) {
                Alert::success(array(
                    'title' => 'Welcome!',
                    'text'  => 'You have successfully logged in.',
                    'path'  => '../../index.php'
                ));;
            }
        } else {
            Alert::error(array(
                'title' => 'Invalid Password',
                'text'  => 'The password you entered is incorrect. Please try again.',
                'path'  => $redirect_path
            ));
        }
    } else {
        Alert::error(array(
            'title' => 'Login Failed',
            'text'  => 'Account does not exist.',
            'path'  => $redirect_path
        ));
    }
} catch (Exception $e) {
    console_error('An error occurred during login: ' . $e->getMessage());
}

?>