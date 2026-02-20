<?php
include '../../includes/init.php';
$db = DB::getInstance();

$redirect_path = '../program.php';

if (isset($_POST['Save'])) {
    try {
        // Check if the required fields are set
        $requiredFields = [
            'prog_name' => 'Program Name', 
            'prog_desc' => 'Program Description',
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

        // Check for duplicate prog_name
        $message = $db->hasDuplicate('SELECT prog_name FROM tbl_program WHERE prog_name = :prog_name', [
            'prog_name' => $_POST['prog_name']
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
            'prog_name'  => $_POST['prog_name'],
            'prog_desc'   => $_POST['prog_desc'],
            'status'  => $_POST['status'],
        );

        if(isset($_SESSION['proms-admin']['img_input'])){
            if($_SESSION['proms-admin']['img_input']['status'] == 'Success') {
                $sqlArray['prog_img'] = $_SESSION['proms-admin']['img_input']['content'];
                $sqlArray['prog_img_type'] = $_SESSION['proms-admin']['img_input']['type'];
            }else{
                Alert::error([
                    'title' => 'Image Upload Error',
                    'html'  => $_SESSION['proms-admin']['img_input']['content'],
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
            'prog_name' => 'Program Name',
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
        // Check for duplicate prog_name
        $message = $db->hasDuplicate('SELECT prog_name FROM tbl_program WHERE prog_name = :prog_name AND prog_id != :prog_id', [
            'prog_name' => $_POST['prog_name'],
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
            'prog_name'  => $_POST['prog_name'],
            'status'  => $_POST['status'],
        );

        if(isset($_SESSION['proms-admin']['img_input'])){
            if($_SESSION['proms-admin']['img_input']['status'] == 'Success') {
                $sqlArray['img'] = $_SESSION['proms-admin']['img_input']['content'];
                $sqlArray['img_type'] = $_SESSION['proms-admin']['img_input']['type'];
            }else{
                Alert::error([
                    'title' => 'Image Upload Error',
                    'html'  => $_SESSION['proms-admin']['img_input']['content'],
                    'path'  => $redirect_path
                ]);
            }
        }

        // Execute the insert operation
        $db->executeUpdate($sqlArray, 'tbl_program', 'prog_id = :prog_id', ['prog_id' => $prog_id]);
        
        // Check if the insert was successful
        if ($db->affectedRows > 0) {
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

if (isset($_POST['View'])) {
    try {
        $_SESSION['proms-admin']['prog_id'] = decrypt_data($_POST['View']);
        if($_SESSION['proms-admin']['prog_id'] == 1){
            safe_redirect("../assess-cert.php");
        }else if($_SESSION['proms-admin']['prog_id'] == 2){
            safe_redirect("../foreign-lang.php");
        }else if($_SESSION['proms-admin']['prog_id'] == 3){
            safe_redirect("../cert-prog.php");
        }else if($_SESSION['proms-admin']['prog_id'] == 4){
            safe_redirect("../short-term.php");
        }else if($_SESSION['proms-admin']['prog_id'] == 5){
            safe_redirect("../micro-course.php");
        }else if($_SESSION['proms-admin']['prog_id'] == 6){
            safe_redirect("../ms-prog.php");
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
