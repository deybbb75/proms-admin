<?php
include '../../includes/init.php';
$db = DB::getInstance();

$redirect_path = '../pending.php';

if (isset($_POST['Add'])) {
    try {
        // Check if the required fields are set
        $requiredFields = [
            'student_no' => 'Student Number'
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
        $reserve_id  = decrypt_data($_POST['reserve_id']);
        $student_id = $db->queryUniqueValue("SELECT student_id FROM tbl_reservation WHERE reserve_id = :reserve_id", ["reserve_id" => $reserve_id]);
        // Check for duplicate emp_no or email excluding the current record
        $message = $db->hasDuplicate('SELECT student_no FROM tbl_student WHERE student_no = :student_no', [
            'student_no' => $_POST['student_no'],
        ]);

        if ($message) {
            Alert::error(array(
                'title' => 'Duplicate Entry',
                'html'  => $message,
                'path'  => $redirect_path
            ));
        }

        // Prepare the SQL array for update
        $studentArray = array(
            'student_no'  => $_POST['student_no'],
        );
        // Execute the update operation
        $db->executeUpdate($studentArray, 'tbl_student', 'student_id = :student_id', ['student_id' => $student_id]);

        // Prepare the SQL array for update
        $sqlArray = array(
            'status'  => 'Enrolled',
        );
        // Execute the update operation
        $db->executeUpdate($sqlArray, 'tbl_reservation', 'reserve_id = :reserve_id', ['reserve_id' => $reserve_id]);

        // Check if the update was successful
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Update Successful',
                'html'  => 'Reservation successfully updated.',
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

if (isset($_POST['Enroll'])) {
    try {
        // Decrypt the id
        $reserve_id  = decrypt_data($_POST['Enroll']);

        // Prepare the SQL array for update
        $sqlArray = array(
            'status'  => 'Enrolled',
        );
        // Execute the update operation
        $db->executeUpdate($sqlArray, 'tbl_reservation', 'reserve_id = :reserve_id', ['reserve_id' => $reserve_id]);

        // Check if the update was successful
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Update Successful',
                'html'  => 'Reservation successfully updated.',
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

if (isset($_POST['Reserve'])) {
    try {
        // Decrypt the id
        $reserve_id  = decrypt_data($_POST['Reserve']);

        // Prepare the SQL array for update
        $sqlArray = array(
            'status'  => 'Reserved',
        );
        // Execute the update operation
        $db->executeUpdate($sqlArray, 'tbl_reservation', 'reserve_id = :reserve_id', ['reserve_id' => $reserve_id]);

        // Check if the update was successful
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Update Successful',
                'html'  => 'Reservation successfully updated.',
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

if (isset($_POST['Expire'])) {
    try {
        // Decrypt the id
        $reserve_id  = decrypt_data($_POST['Expire']);

        // Prepare the SQL array for update
        $sqlArray = array(
            'status'  => 'Expired',
        );
        // Execute the update operation
        $db->executeUpdate($sqlArray, 'tbl_reservation', 'reserve_id = :reserve_id', ['reserve_id' => $reserve_id]);

        // Check if the update was successful
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Update Successful',
                'html'  => 'Reservation successfully updated.',
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

if (isset($_POST['Revert'])) {
    try {
        // Decrypt the id
        $reserve_id  = decrypt_data($_POST['Revert']);

        // Prepare the SQL array for update
        $sqlArray = array(
            'status'  => 'Pending',
        );
        // Execute the update operation
        $db->executeUpdate($sqlArray, 'tbl_reservation', 'reserve_id = :reserve_id', ['reserve_id' => $reserve_id]);

        // Check if the update was successful
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Update Successful',
                'html'  => 'Reservation successfully updated.',
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
