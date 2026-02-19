<?php
include '../../includes/init.php';
$db = DB::getInstance();

$redirect_path = '../short-term.php';

if (isset($_POST['Save'])) {
    try {
        // Check if the required fields are set
        $requiredFields = [
            'prog_title'         => 'Program Title',
            'training_title'     => 'Training/Course Title',
            'description'       => 'Description',
            'venue'             => 'Venue',
            'status'            => 'Status'
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
        $message = $db->hasDuplicate('SELECT prog_title, training_title FROM tbl_short_term WHERE prog_title = :prog_title AND training_title = :training_title', [
            'prog_title' => $_POST['prog_title'],
            'training_title' => $_POST['training_title']
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
            'prog_title'        => $_POST['prog_title'],
            'training_title'    => $_POST['training_title'],
            'description'       => $_POST['description'],
            'venue'             => $_POST['venue'],
            'objective'         => json_encode($_POST['objective'] ?? []),
            'outline'           => json_encode($_POST['outline'] ?? []),
            'status'            => $_POST['status'],
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
        $db->executeInsert($sqlArray, 'tbl_short_term');
        
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
            'prog_title'         => 'Program Title',
            'training_title'     => 'Training/Course Title',
            'description'       => 'Description',
            'venue'             => 'Venue',
            'status'            => 'Status'
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
        $st_id  = decrypt_data($_POST['st_id']);
        // Check for duplicate sub_prog_name
        $message = $db->hasDuplicate('SELECT prog_title, training_title FROM tbl_short_term WHERE prog_title = :prog_title AND training_title = :training_title AND st_id != :st_id', [
            'prog_title' => $_POST['prog_title'],
            'training_title' => $_POST['training_title'],
            'st_id' => $st_id
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
            'prog_title'        => $_POST['prog_title'],
            'training_title'    => $_POST['training_title'],
            'description'       => $_POST['description'],
            'venue'             => $_POST['venue'],
            'objective'         => json_encode($_POST['objective'] ?? []),
            'outline'           => json_encode($_POST['outline'] ?? []),
            'status'            => $_POST['status'],
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
        $db->executeUpdate($sqlArray, 'tbl_short_term', 'st_id = :st_id', ['st_id' => $st_id]);

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
        $db->executeDelete('tbl_short_term', 'st_id = :st_id', ['st_id' => $id]);
        
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
