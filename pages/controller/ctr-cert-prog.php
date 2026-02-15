<?php
include '../../includes/init.php';
$db = DB::getInstance();

$redirect_path = '../cert-prog.php';

if (isset($_POST['Save'])) {
    try {
        // Check if the required fields are set
        $requiredFields = [
            'title'         => 'Title',
            'description'   => 'Description',
            'class_details' => 'Class Details',
            'start_date_1'  => 'Start Date (1st Semester)', 
            'start_date_2'  => 'Start Date (2nd Semester)', 
            'venue'         => 'Venue',
            'main_fee'      => 'Tuition Fee',
            'sub_fee'       => 'Processing Fee',
            'note'          => 'Note',
            'status'        => 'Status'
        ];

        $missing        = validateRequiredFields($requiredFields, $_POST);
        if ($missing) {
            Alert::error(array(
                'title' => 'Validation Error',
                'html'  => 'Missing required fields: ' . implode(', ', $missing),
                'path'  => $redirect_path
            ));
        }

        // Check for duplicate title
        $message = $db->hasDuplicate('SELECT title FROM tbl_cert_prog WHERE title = :title', [
            'title' => $_POST['title']
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
            'title'         => $_POST['title'],
            'description'   => $_POST['description'],
            'class_details' => $_POST['class_details'],
            'start_date_1'  => $_POST['start_date_1'],
            'start_date_2'  => $_POST['start_date_2'],
            'schedule'      => json_encode($_POST['schedule'] ?? []),
            'venue'         => $_POST['venue'],
            'main_fee'      => str_replace(',', '', $_POST['main_fee']),
            'sub_fee'       => str_replace(',', '', $_POST['sub_fee']),
            'note'          => $_POST['note'],
            'requirement'   => json_encode($_POST['requirement'] ?? []),
            'status'        => $_POST['status'],
        );

        if(isset($_SESSION['img_input'])){
            if($_SESSION['img_input']['status'] == 'Success') {
                $sqlArray['img'] = $_SESSION['img_input']['content'];
                $sqlArray['img_type'] = $_SESSION['img_input']['type'];
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
        $db->executeInsert($sqlArray, 'tbl_cert_prog');
        
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
            'title'         => 'Title',
            'description'   => 'Description',
            'class_details' => 'Class Details',
            'start_date_1'  => 'Start Date (1st Semester)', 
            'start_date_2'  => 'Start Date (2nd Semester)', 
            'venue'         => 'Venue',
            'main_fee'      => 'Tuition Fee',
            'sub_fee'       => 'Processing Fee',
            'note'          => 'Note',
            'status'        => 'Status'
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
        $cp_id  = decrypt_data($_POST['cp_id']);
        // Check for duplicate sub_prog_name
        $message = $db->hasDuplicate('SELECT title FROM tbl_cert_prog WHERE title = :title AND cp_id != :cp_id', [
            'title' => $_POST['title'],
            'cp_id' => $cp_id
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
            'title'         => $_POST['title'],
            'description'   => $_POST['description'],
            'class_details' => $_POST['class_details'],
            'start_date_1'  => $_POST['start_date_1'],
            'start_date_2'  => $_POST['start_date_2'],
            'schedule'      => json_encode($_POST['schedule'] ?? []),
            'venue'         => $_POST['venue'],
            'main_fee'      => str_replace(',', '', $_POST['main_fee']),
            'sub_fee'       => str_replace(',', '', $_POST['sub_fee']),
            'note'          => $_POST['note'],
            'requirement'   => json_encode($_POST['requirement'] ?? []),
            'status'        => $_POST['status'],
        );

        if(isset($_SESSION['img_input'])){
            if($_SESSION['img_input']['status'] == 'Success') {
                $sqlArray['img'] = $_SESSION['img_input']['content'];
                $sqlArray['img_type'] = $_SESSION['img_input']['type'];
            }else{
                Alert::error([
                    'title' => 'Image Upload Error',
                    'html'  => $_SESSION['img_input']['content'],
                    'path'  => $redirect_path
                ]);
            }
        }

        // Execute the insert operation
        $db->executeUpdate($sqlArray, 'tbl_cert_prog', 'cp_id = :cp_id', ['cp_id' => $cp_id]);
        
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Update Successful',
                'html'  => 'Sub-program successfully updated.',
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
        $db->executeDelete('tbl_cert_prog', 'cp_id = :cp_id', ['cp_id' => $id]);
        
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
