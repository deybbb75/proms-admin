<?php
include '../../includes/init.php';
$db = DB::getInstance();

$redirect_path = '../team.php';

if (isset($_POST['Save'])) {
    try {
        // Check if the required fields are set
        $requiredFields = [
            'member_name' => 'Member Name', 
            'position' => 'Position',
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

        // Check for duplicate member_name
        $message = $db->hasDuplicate('SELECT member_name FROM tbl_member WHERE member_name = :member_name', [
            'member_name' => $_POST['member_name']
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
            'member_name'  => $_POST['member_name'],
            'position'   => $_POST['position'],
            'status'  => $_POST['status'],
        );

        if(isset($_SESSION['img_input'])){
            if($_SESSION['img_input']['status'] == 'Success') {
                $sqlArray['member_img'] = $_SESSION['img_input']['content'];
                $sqlArray['member_img_type'] = $_SESSION['img_input']['type'];
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
        $db->executeInsert($sqlArray, 'tbl_member');

        $lastInsertId = $db->lastInsertedId();
        
        // Check if the insert was successful
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Save Successful',
                'html'  => 'Team member successfully saved.',
                'path'  => $redirect_path
            ));
        }
    } catch (DBException $e) {
        // Handle the database error
        // Alert::error(array(
        //     'title' => 'Server Error',
        //     'html'  => 'Something went wrong on our end.',
        //     'path'  => $redirect_path
        // ));

        echo $e->getMessage();
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
            'member_name' => 'Member Name', 
            'position' => 'Position',
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
        $member_id  = decrypt_data($_POST['member_id']);
        // Check for duplicate member_name
        $message = $db->hasDuplicate('SELECT member_name FROM tbl_member WHERE member_name = :member_name AND member_id != :member_id', [
            'member_name' => $_POST['member_name'],
            'member_id' => $member_id
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
            'member_name'  => $_POST['member_name'],
            'position'   => $_POST['position'],
            'status'  => $_POST['status'],
        );

        if(isset($_SESSION['img_input'])){
            if($_SESSION['img_input']['status'] == 'Success') {
                $sqlArray['member_img'] = $_SESSION['img_input']['content'];
                $sqlArray['member_img_type'] = $_SESSION['img_input']['type'];
            }else{
                Alert::error([
                    'title' => 'Image Upload Error',
                    'html'  => $_SESSION['img_input']['content'],
                    'path'  => $redirect_path
                ]);
            }
        }

        // Execute the insert operation
        $db->executeUpdate($sqlArray, 'tbl_member', 'member_id = :member_id', ['member_id' => $member_id]);
        
        // Check if the insert was successful
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Update Successful',
                'html'  => 'Team member successfully updated.',
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
        if($id == 1){ // Prevent deletion of default admin
            Alert::error(array(
                'title' => 'Delete Failed',
                'html'  => 'Cannot delete the default admin member.',
                'path'  => $redirect_path
            ));
        }
        $db->executeDelete('tbl_member', 'member_id = :member_id', ['member_id' => $id]);
        
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Delete Successful',
                'html'  => 'Team member successfully deleted.',
                'path'  => $redirect_path
            ));
        } else {
            Alert::error(array(
                'title' => 'Delete Failed',
                'html'  => 'No team member found with the provided ID.',
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
