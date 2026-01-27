<?php
include '../../includes/init.php';
$db = DB::getInstance();

$redirect_path = '../program.php';

if (isset($_POST['Save'])) {
    try {
        // Check if the required fields are set
        $requiredFields = [
            'prog_title' => 'Program Title', 
            'prog_desc' => 'Program Description', 
            'start_date1' => 'Start Date (1st Semester)', 
            'start_date2' => 'Start Date (2nd Semester)', 
            'venue' => 'Venue',
            'tuition_fee' => 'Tuition Fees',
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

        // Check for duplicate prog_title
        $message = $db->hasDuplicate('SELECT prog_title FROM tbl_program WHERE prog_title = :prog_title', [
            'prog_title' => $_POST['prog_title']
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
            'prog_title'  => $_POST['prog_title'],
            'prog_desc'   => $_POST['prog_desc'],
            'start_date1' => $_POST['start_date1'],
            'start_date2' => $_POST['start_date2'],
            'venue'       => $_POST['venue'],
            'tuition_fee'=> $_POST['tuition_fee'],
            'status'  => $_POST['status'],
        );

        if(isset($_SESSION['prog_image'])){
            if($_SESSION['prog_image']['status'] == 'Success') {
                $sqlArray['prog_image'] = $_SESSION['prog_image']['content'];
                $sqlArray['image_type'] = $_SESSION['prog_image']['type'];
            }else{
                Alert::error([
                    'title' => 'Image Upload Error',
                    'html'  => $_SESSION['prog_image']['content'],
                    'path'  => $redirect_path
                ]);
            }
        }else{
            Alert::error([
                'title' => 'Image Upload Error',
                'html'  => 'No image uploaded.',
                'path'  => $redirect_path
            ]);
        }

        // Execute the insert operation
        $db->executeInsert($sqlArray, 'tbl_program');

        $lastInsertId = $db->lastInsertedId();

        for ($i = 0; $i < $_SESSION['max_schedule']; $i++) {
            if (empty($_POST['day'][$i]) || empty($_POST['start_time'][$i]) || empty($_POST['end_time'][$i])) {
                continue;
            }
            $sqlArray = array(
                'prog_id'  => $lastInsertId,
                'day'   => $_POST['day'][$i],
                'start_time' => $_POST['start_time'][$i],
                'end_time' => $_POST['end_time'][$i]
            );
            // Execute the insert operation
            $db->executeInsert($sqlArray, 'tbl_schedule');
        }
        
        // Check if the insert was successful
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Save Successful',
                'html'  => 'Program successfully saved.',
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

if (isset($_POST['Edit'])) {
    try {
        // Check if the required fields are set
        $requiredFields = [
            'prog_title' => 'Program Title', 
            'prog_desc' => 'Program Description', 
            'start_date1' => 'Start Date (1st Semester)', 
            'start_date2' => 'Start Date (2nd Semester)', 
            'venue' => 'Venue',
            'tuition_fee' => 'Tuition Fees',
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
        $prog_id  = decrypt_data($_POST['prog_id']);
        // Check for duplicate prog_title
        $message = $db->hasDuplicate('SELECT prog_title FROM tbl_program WHERE prog_title = :prog_title AND prog_id != :prog_id', [
            'prog_title' => $_POST['prog_title'],
            'prog_id' => $prog_id
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
            'prog_title'  => $_POST['prog_title'],
            'prog_desc'   => $_POST['prog_desc'],
            'start_date1' => $_POST['start_date1'],
            'start_date2' => $_POST['start_date2'],
            'venue'       => $_POST['venue'],
            'tuition_fee'=> $_POST['tuition_fee'],
            'status'  => $_POST['status'],
        );

        if(isset($_SESSION['prog_image'])){
            if($_SESSION['prog_image']['status'] == 'Success') {
                $sqlArray['prog_image'] = $_SESSION['prog_image']['content'];
                $sqlArray['image_type'] = $_SESSION['prog_image']['type'];
            }else{
                Alert::error([
                    'title' => 'Image Upload Error',
                    'html'  => $_SESSION['prog_image']['content'],
                    'path'  => $redirect_path
                ]);
            }
        }

        // Execute the insert operation
        $db->executeUpdate($sqlArray, 'tbl_program', 'prog_id = :prog_id', ['prog_id' => $prog_id]);
        $updated = false;

        if ($db->affectedRows > 0) {
            $updated = true;
        }

        $db->executeDelete('tbl_schedule', 'prog_id = :prog_id', ['prog_id' => $prog_id]);

        for ($i = 0; $i < $_SESSION['max_schedule']; $i++) {
            if (empty($_POST['day'][$i]) || empty($_POST['start_time'][$i]) || empty($_POST['end_time'][$i])) {
                continue;
            }

            $sqlArray = array(
                'prog_id'  => $prog_id,
                'day'   => $_POST['day'][$i],
                'start_time' => $_POST['start_time'][$i],
                'end_time' => $_POST['end_time'][$i]
            );
            // Execute the insert operation
            $db->executeInsert($sqlArray, 'tbl_schedule');

            if ($db->affectedRows > 0) {
                $updated = true;
            }
        }
        
        // Check if the insert was successful
        if ($updated) {
            Alert::success(array(
                'title' => 'Update Successful',
                'html'  => 'Program successfully updated.',
                'path'  => $redirect_path
            ));
        }else {
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
        $db->executeDelete('tbl_schedule', 'prog_id = :prog_id', ['prog_id' => $id]);
        $db->executeDelete('tbl_program', 'prog_id = :prog_id', ['prog_id' => $id]);
        
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
