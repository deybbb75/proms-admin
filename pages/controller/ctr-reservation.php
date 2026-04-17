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

if (isset($_POST['action'])) {
    try {
        // Decrypt the id
        $reserve_id  = decrypt_data($_POST['item_id']);

        // Prepare the SQL array for update
        $sqlArray = array(
            'status'  => $_POST['action'],
        );
        // Execute the update operation
        $db->executeUpdate($sqlArray, 'tbl_reservation', 'reserve_id = :reserve_id', ['reserve_id' => $reserve_id]);

        // Check if the update was successful
        if ($db->affectedRows > 0) {
            $reservation = $db->queryUniqueObject("SELECT * FROM tbl_reservation WHERE reserve_id = :reserve_id", ['reserve_id' => $reserve_id]);
            $student_id = $reservation->student_id;
            $prog_id = $reservation->prog_id;
            $sub_prog_id = $reservation->sub_prog_id;

            $student = $db->queryUniqueObject("SELECT * FROM tbl_student WHERE student_id = :student_id", ['student_id' => $student_id]);
            $_SESSION['proms']['confirm_email']['email'] = $student->email;
            $_SESSION['proms']['confirm_email']['name'] = $student->fname . " " . $student->mname . " " . $student->lname;

            if($prog_id == 1){
                $_SESSION['proms']['confirm_email']['program'] = $db->queryUniqueValue("SELECT title FROM tbl_assess_cert WHERE sub_prog_id = :sub_prog_id", ['sub_prog_id' => $sub_prog_id]);
            }else if($prog_id == 2){
                $_SESSION['proms']['confirm_email']['program'] = $db->queryUniqueValue("SELECT title FROM tbl_foreign_lang WHERE sub_prog_id = :sub_prog_id", ['sub_prog_id' => $sub_prog_id]);
            }else if($prog_id == 3){
                $_SESSION['proms']['confirm_email']['program'] = $db->queryUniqueValue("SELECT title FROM tbl_cert_prog WHERE sub_prog_id = :sub_prog_id", ['sub_prog_id' => $sub_prog_id]);
            }else if($prog_id == 4){
                $_SESSION['proms']['confirm_email']['program'] = $db->queryUniqueValue("SELECT title FROM tbl_short_term WHERE sub_prog_id = :sub_prog_id", ['sub_prog_id' => $sub_prog_id]);
            }else if($prog_id == 5){
                $_SESSION['proms']['confirm_email']['program'] = $db->queryUniqueValue("SELECT title FROM tbl_micro_course WHERE sub_prog_id = :sub_prog_id", ['sub_prog_id' => $sub_prog_id]);
            }else if($prog_id == 6){
                $_SESSION['proms']['confirm_email']['program'] = $db->queryUniqueValue("SELECT title FROM tbl_ms_prog WHERE sub_prog_id = :sub_prog_id", ['sub_prog_id' => $sub_prog_id]);
            }

            if($_POST['action'] == "Enrolled"){
                safe_redirect('../../email/confirm-enroll.php');
            }else if($_POST['action'] == "Reserved"){
                safe_redirect('../../email/confirm-reserve.php');
            }else if($_POST['action'] == "Expired"){
                safe_redirect('../../email/confirm-expire.php');
            }
            
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
