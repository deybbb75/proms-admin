<?php
include '../../includes/init.php';
$db = DB::getInstance();

$redirect_path = '../system-user.php';

if (isset($_POST['Save'])) {
    try {
        // Check if the required fields are set
        $requiredFields = [
            'emp_no' => 'LPU Number', 
            'fname' => 'First Name', 
            'lname' => 'Last Name', 
            'email' => 'Email', 'role' => 
            'Role', 'status' => 'Status'
        ];
        $missing        = validateRequiredFields($requiredFields, $_POST);
        if ($missing) {
            Alert::error(array(
                'title' => 'Validation Error',
                'html'  => 'Missing required fields: ' . implode(', ', $missing),
                'path'  => $redirect_path
            ));
        }

        // Check for duplicate emp_no or email
        $message = $db->hasDuplicate('SELECT emp_no, email FROM tbl_system_user WHERE emp_no = :emp_no OR LOWER(email) = LOWER(:email)', [
            'emp_no' => $_POST['emp_no'],
            'email'  => $_POST['email']
        ]);
        if ($message) {
            Alert::error(array(
                'title' => 'Duplicate Entry',
                'html'  => $message,
                'path'  => $redirect_path
            ));
        }

        // Prepare the SQL array for insertion
        $sqlArray = array(
            'emp_no'  => $_POST['emp_no'],
            'fname'   => ucwords($_POST['fname']),
            'mname'   => ucwords($_POST['mname']),
            'lname'   => ucwords($_POST['lname']),
            'email'   => $_POST['email'],
            'role'    => $_POST['role'],
            'status'  => $_POST['status'],
        );
        // Execute the insert operation
        $db->executeInsert($sqlArray, 'tbl_system_user');
        
        // Check if the insert was successful
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Save Successful',
                'html'  => 'User successfully saved.',
                'path'  => $redirect_path
            ));
        }
    } catch (DBException $e) {
        // Handle the database error
        Alert::error(array(
            'title' => 'Server Error',
            'html'  => 'Something went wrong on our end.',
            'path'  => $redirect_path
        ));

        // echo $e->getMessage();
    } catch (Exception $e) {
        // Handle other exceptions
        Alert::error(array(
            'title' => 'Error',
            'html'  => 'Something went wrong with your request.',
            'path'  => $redirect_path
        ));
    }
}

if (isset($_POST['Edit'])) {
    try {
        // Check if the required fields are set
        $requiredFields = [
            'emp_no' => 'LPU Number', 
            'fname' => 'First Name', 
            'lname' => 'Last Name', 
            'email' => 'Email', 
            'role' => 'Role', 
            'status' => 'Status'
        ];
        $missing        = validateRequiredFields($requiredFields, $_POST);
        if ($missing) {
            Alert::error(array(
                'title' => 'Validation Error',
                'html'  => 'Missing required fields: ' . implode(', ', $missing),
                'path'  => $redirect_path
            ));
        }

        // Decrypt the id
        $sys_id  = decrypt_data($_POST['sys_id']);
        // Check for duplicate emp_no or email excluding the current record
        $message = $db->hasDuplicate('SELECT emp_no, email FROM tbl_system_user WHERE (emp_no = :emp_no OR LOWER(email) = LOWER(:email)) AND sys_id != :sys_id', [
            'emp_no' => $_POST['emp_no'],
            'email'  => $_POST['email'],
            'sys_id' => $sys_id
        ]);

        if ($message) {
            Alert::error(array(
                'title' => 'Duplicate Entry',
                'html'  => $message,
                'path'  => $redirect_path
            ));
        }

        // Prepare the SQL array for update
        $sqlArray = array(
            'fname'   => ucwords($_POST['fname']),
            'mname'   => ucwords($_POST['mname']),
            'lname'   => ucwords($_POST['lname']),
            'email'   => $_POST['email'],
            'role'    => $_POST['role'],
            'status'  => $_POST['status'],
        );
        // Execute the update operation
        $db->executeUpdate($sqlArray, 'tbl_system_user', 'sys_id = :sys_id', ['sys_id' => $sys_id]);

        // Check if the update was successful
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Update Successful',
                'html'  => 'User successfully updated.',
                'path'  => $redirect_path
            ));
        } else {
            Alert::warning(array(
                'title' => 'No Update Performed',
                'html'  => 'Submitted data is identical to existing record.',
                'path'  => $redirect_path
            ));
        }
    } catch (DBException $e) {
        // Handle the database error
        Alert::error(array(
            'title' => 'Server Error',
            'html'  => 'Something went wrong on our end.',
            'path'  => $redirect_path
        ));
    } catch (Exception $e) {
        // Handle other exceptions
        Alert::error(array(
            'title' => 'Error',
            'html'  => 'Something went wrong with your request.',
            'path'  => $redirect_path
        ));
    }
}

if (isset($_POST['Delete'])) {
    try {
        $id = decrypt_data($_POST['Delete']);
        $db->executeDelete('tbl_system_user', 'sys_id = :sys_id', ['sys_id' => $id]);
        
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Delete Successful',
                'html'  => 'User successfully deleted.',
                'path'  => $redirect_path
            ));
        } else {
            Alert::error(array(
                'title' => 'Delete Failed',
                'html'  => 'No user found with the provided ID.',
                'path'  => $redirect_path
            ));
        }
    } catch (DBException $e) {
        // Handle the database error
        Alert::error(array(
            'title' => 'Server Error',
            'html'  => 'Something went wrong on our end.',
            'path'  => $redirect_path
        ));
    } catch (Exception $e) {
        // Handle other exceptions
        Alert::error(array(
            'title' => 'Error',
            'html'  => 'Something went wrong with your request.',
            'path'  => $redirect_path
        ));
    }
}
