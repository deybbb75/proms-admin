<?php
include '../../includes/init.php';
$db = DB::getInstance();

$redirect_path = '../ms-prog.php';

if (isset($_POST['Save'])) {
    try {
        // Check if the required fields are set
        $requiredFields = [
            'title'         => 'Title',
            'description'   => 'Description',
            'yt_link'       => 'Youtube Link',
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
        $message = $db->hasDuplicate('SELECT title FROM tbl_ms_prog WHERE title = :title', [
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
            'yt_link'       => $_POST['yt_link'],
            'certification' => json_encode($_POST['cert'] ?? []),
            'associate_cert' => json_encode($_POST['associate_cert'] ?? []),
            'expert_cert'   => json_encode($_POST['expert_cert'] ?? []),
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

        if(isset($_SESSION['cert_input'])){
            if($_SESSION['cert_input']['status'] == 'Success') {
                $sqlArray['cert_img'] = $_SESSION['cert_input']['content'];
                $sqlArray['cert_img_type'] = $_SESSION['cert_input']['type'];
            }else{
                Alert::error([
                    'title' => 'Image Upload Error',
                    'html'  => $_SESSION['cert_input']['content'],
                    'path'  => $redirect_path
                ]);
            }
        }else{
            Alert::error([
                'title' => 'Certficate Upload Error',
                'html'  => 'No certificate uploaded.',
                'path'  => $redirect_path
            ]);
        }

        // Execute the insert operation
        $db->executeInsert($sqlArray, 'tbl_ms_prog');
        
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
            'yt_link'       => 'Youtube Link',
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
        $mp_id  = decrypt_data($_POST['mp_id']);
        // Check for duplicate sub_prog_name
        $message = $db->hasDuplicate('SELECT title FROM tbl_ms_prog WHERE title = :title AND mp_id != :mp_id', [
            'title' => $_POST['title'],
            'mp_id' => $mp_id
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
            'yt_link'       => $_POST['yt_link'],
            'certification' => json_encode($_POST['cert'] ?? []),
            'associate_cert' => json_encode($_POST['associate_cert'] ?? []),
            'expert_cert'   => json_encode($_POST['expert_cert'] ?? []),
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

        if(isset($_SESSION['cert_input'])){
            if($_SESSION['cert_input']['status'] == 'Success') {
                $sqlArray['cert_img'] = $_SESSION['cert_input']['content'];
                $sqlArray['cert_img_type'] = $_SESSION['cert_input']['type'];
            }else{
                Alert::error([
                    'title' => 'Image Upload Error',
                    'html'  => $_SESSION['cert_input']['content'],
                    'path'  => $redirect_path
                ]);
            }
        }

        // Execute the insert operation
        $db->executeUpdate($sqlArray, 'tbl_ms_prog', 'mp_id = :mp_id', ['mp_id' => $mp_id]);
        
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
        $db->executeDelete('tbl_ms_prog', 'mp_id = :mp_id', ['mp_id' => $id]);
        
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
