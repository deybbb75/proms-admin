<?php
include '../../includes/init.php';
$db = DB::getInstance();

$redirect_path = '../banner.php';

if (isset($_POST['Save'])) {
    try {
        // Check if the required fields are set
        $requiredFields = [
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

        // Prepare the SQL array for insertion
        $sqlArray = array(
            'status'        => $_POST['status'],
        );

        if(isset($_SESSION['proms-admin']['img_input'])){
            if($_SESSION['proms-admin']['img_input']['result'] == 'success') {
                $sqlArray['img'] = $_SESSION['proms-admin']['img_input']['content'];
                $sqlArray['img_type'] = $_SESSION['proms-admin']['img_input']['type'];
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
        $db->executeInsert($sqlArray, 'tbl_banner');
        
        // Check if the insert was successful
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Save Successful',
                'html'  => 'Banner successfully saved.',
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
        $banner_id  = decrypt_data($_POST['banner_id']);

        // Prepare the SQL array for insertion
        $sqlArray = array(
            'status'        => $_POST['status'],
        );

        if(isset($_SESSION['proms-admin']['img_input'])){
            if($_SESSION['proms-admin']['img_input']['result'] == 'success') {
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
        $db->executeUpdate($sqlArray, 'tbl_banner', 'banner_id = :banner_id', ['banner_id' => $banner_id]);
        
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Update Successful',
                'html'  => 'Banner successfully updated.',
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
        $db->executeDelete('tbl_banner', 'banner_id = :banner_id', ['banner_id' => $id]);
        
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Delete Successful',
                'html'  => 'Banner successfully deleted.',
                'path'  => $redirect_path
            ));
        } else {
            Alert::error(array(
                'title' => 'Delete Failed',
                'html'  => 'No banner found with the provided ID.',
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
