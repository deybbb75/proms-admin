<?php
include '../../includes/init.php';
$db = DB::getInstance();

$redirect_path = '../student.php';

if (isset($_POST['Edit'])) {
    try {
        // Check if the required fields are set
        $requiredFields = [
            'fname' => 'First Name', 
            'lname' => 'Last Name', 
            'email' => 'Email', 
            'mobile_no' => 'Mobile Number', 
            'birthday' => 'Birthday',
            'fb_link' => 'Facebook Link',  
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
        $student_id  = decrypt_data($_POST['student_id']);
        // Check for duplicate emp_no or email excluding the current record
        $message = $db->hasDuplicate('SELECT fname, mname, lname, email FROM tbl_student WHERE ((fname = :fname AND mname = :mname AND lname = :lname) OR LOWER(email) = LOWER(:email)) AND student_id != :student_id', [
            'fname' => $_POST['fname'],
            'mname' => $_POST['mname'],
            'lname' => $_POST['lname'],
            'email'  => $_POST['email'],
            'student_id' => $student_id
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
            'fname'         => ucwords($_POST['fname']),
            'mname'         => ucwords($_POST['mname']),
            'lname'         => ucwords($_POST['lname']),
            'student_no'    => !empty($_POST['student_no']) ? $_POST['student_no'] : null,
            'email'         => $_POST['email'],
            'mobile_no'     => $_POST['mobile_no'],
            'birthday'      => $_POST['birthday'],
            'fb_link'       => $_POST['fb_link'],
            'status'        => $_POST['status'],
        );

        // Execute the update operation
        $db->executeUpdate($sqlArray, 'tbl_student', 'student_id = :student_id', ['student_id' => $student_id]);

        // Check if the update was successful
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Update Successful',
                'html'  => 'Student successfully updated.',
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
        $db->executeDelete('tbl_student', 'student_id = :student_id', ['student_id' => $id]);
        
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Delete Successful',
                'html'  => 'Student successfully deleted.',
                'path'  => $redirect_path
            ));
        } else {
            Alert::error(array(
                'title' => 'Delete Failed',
                'html'  => 'No student found with the provided ID.',
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
