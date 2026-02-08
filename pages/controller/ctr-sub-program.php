<?php
include '../../includes/init.php';
$db = DB::getInstance();

$redirect_path = '../sub-program.php';

if (isset($_POST['Save'])) {
    try {
        // Check if the required fields are set
        $requiredFields = [
            'sub_prog_name' => 'Sub-Program Name',
            'main_fee'      => 'Tuition Fee',
            'sub_fee'       => 'Processing Fee',
            'status'        => 'Status'
        ];

        if(decrypt_data($_POST['prog_id']) == $_SESSION['unique_prog_id']){
            $requiredFields[] = [
                'start_date_1'  => 'Start Date (1st Semester)', 
                'start_date_2'  => 'Start Date (2nd Semester)', 
                'venue'         => 'Venue',
            ];
        }

        $missing        = validateRequiredFields($requiredFields, $_POST);
        if ($missing) {
            Alert::error(array(
                'title' => 'Validation Error',
                'html'  => 'Missing required fields: ' . implode(', ', $missing),
                'path'  => $redirect_path
            ));
        }

        // Check for duplicate sub_prog_name
        $message = $db->hasDuplicate('SELECT sub_prog_name FROM tbl_sub_program WHERE sub_prog_name = :sub_prog_name', [
            'sub_prog_name' => $_POST['sub_prog_name']
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
            'sub_prog_name' => $_POST['sub_prog_name'],
            'prog_id'       => decrypt_data($_POST['prog_id']),
            'start_date_1'  => !empty($_POST['start_date_1']) ? $_POST['start_date_1'] : null,
            'start_date_2'  => !empty($_POST['start_date_2']) ? $_POST['start_date_2'] : null,
            'venue'         => !empty($_POST['venue']) ? $_POST['venue'] : null,
            'main_fee'      => str_replace(',', '', $_POST['main_fee']),
            'sub_fee'       => str_replace(',', '', $_POST['sub_fee']),
            'status'        => $_POST['status'],
        );

        if(isset($_SESSION['img_input'])){
            if($_SESSION['img_input']['status'] == 'Success') {
                $sqlArray['sub_prog_img'] = $_SESSION['img_input']['content'];
                $sqlArray['sub_prog_img_type'] = $_SESSION['img_input']['type'];
            }else{
                Alert::error([
                    'title' => 'Image Upload Error',
                    'html'  => $_SESSION['img_input']['content'],
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
        $db->executeInsert($sqlArray, 'tbl_sub_program');

        $lastInsertId = $db->lastInsertedId();

        for ($i = 0; $i < $_SESSION['max_schedule']; $i++) {
            if (empty($_POST['day'][$i]) || empty($_POST['start_time'][$i]) || empty($_POST['end_time'][$i])) {
                continue;
            }
            $sqlArray = array(
                'sub_prog_id'  => $lastInsertId,
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
                'html'  => 'Sub-program successfully saved.',
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
            'sub_prog_name' => 'Sub-program Name',
            'main_fee'      => 'Tuition Fee',
            'sub_fee'       => 'Processing Fee',
            'status'        => 'Status'
        ];

        if(decrypt_data($_POST['prog_id']) == $_SESSION['unique_prog_id']){
            $requiredFields[] = [
                'start_date_1'  => 'Start Date (1st Semester)', 
                'start_date_2'  => 'Start Date (2nd Semester)', 
                'venue'         => 'Venue',
            ];
        }
        $missing        = validateRequiredFields($requiredFields, $_POST);
        if ($missing) {
            Alert::error(array(
                'title' => 'Validation Error',
                'html'  => 'Missing required fields: ' . implode(', ', $missing),
                'path'  => $redirect_path
            ));
        }

       // Decrypt the id
        $sub_prog_id  = decrypt_data($_POST['sub_prog_id']);
        // Check for duplicate sub_prog_name
        $message = $db->hasDuplicate('SELECT sub_prog_name FROM tbl_sub_program WHERE sub_prog_name = :sub_prog_name AND sub_prog_id != :sub_prog_id', [
            'sub_prog_name' => $_POST['sub_prog_name'],
            'sub_prog_id' => $sub_prog_id
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
            'sub_prog_name' => $_POST['sub_prog_name'],
            'prog_id'       => decrypt_data($_POST['prog_id']),
            'start_date_1'  => !empty($_POST['start_date_1']) ? $_POST['start_date_1'] : null,
            'start_date_2'  => !empty($_POST['start_date_2']) ? $_POST['start_date_2'] : null,
            'venue'         => !empty($_POST['venue']) ? $_POST['venue'] : null,
            'main_fee'      => str_replace(',', '', $_POST['main_fee']),
            'sub_fee'       => str_replace(',', '', $_POST['sub_fee']),
            'status'        => $_POST['status'],
        );

        if(isset($_SESSION['img_input'])){
            if($_SESSION['img_input']['status'] == 'Success') {
                $sqlArray['sub_prog_img'] = $_SESSION['img_input']['content'];
                $sqlArray['sub_prog_img_type'] = $_SESSION['img_input']['type'];
            }else{
                Alert::error([
                    'title' => 'Image Upload Error',
                    'html'  => $_SESSION['img_input']['content'],
                    'path'  => $redirect_path
                ]);
            }
        }

        // Execute the insert operation
        $db->executeUpdate($sqlArray, 'tbl_sub_program', 'sub_prog_id = :sub_prog_id', ['sub_prog_id' => $sub_prog_id]);
        $updated = false;

        if ($db->affectedRows > 0) {
            $updated = true;
        }

        $db->executeDelete('tbl_schedule', 'sub_prog_id = :sub_prog_id', ['sub_prog_id' => $sub_prog_id]);

        for ($i = 0; $i < $_SESSION['max_schedule']; $i++) {
            if (empty($_POST['day'][$i]) || empty($_POST['start_time'][$i]) || empty($_POST['end_time'][$i])) {
                continue;
            }

            $sqlArray = array(
                'sub_prog_id'  => $sub_prog_id,
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
                'html'  => 'Sub-program successfully updated.',
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
        $db->executeDelete('tbl_schedule', 'sub_prog_id = :sub_prog_id', ['sub_prog_id' => $id]);
        $db->executeDelete('tbl_sub_program', 'sub_prog_id = :sub_prog_id', ['sub_prog_id' => $id]);
        
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Delete Successful',
                'html'  => 'Sub-program successfully deleted.',
                'path'  => $redirect_path
            ));
        } else {
            Alert::error(array(
                'title' => 'Delete Failed',
                'html'  => 'No sub-program found with the provided ID.',
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
