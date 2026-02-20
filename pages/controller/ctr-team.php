<?php
include '../../includes/init.php';
$db = DB::getInstance();

$redirect_path = '../team.php';

if (isset($_POST['Save'])) {
    try {
        // Check if the required fields are set
        $requiredFields = [
            'name'          => 'Member Name', 
            'position'      => 'Position',
            'member_order'  => 'Order',
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

        // Check for duplicate name
        $message = $db->hasDuplicate('SELECT name FROM tbl_member WHERE name = :name', [
            'name' => $_POST['name']
        ]);
        if ($message) {
            Alert::error(array(
                'title' => 'Duplicate Entry',
                'html'  => $message,
                'path'  => $redirect_path
            ));
        }

        $duplicate_order = $db->hasDuplicate('SELECT member_order FROM tbl_member WHERE status ="Active"');

        if($duplicate_order){
            $member_query = $db->query('SELECT * FROM tbl_member WHERE status = "Active" ORDER BY member_order');
            $new_order = $_POST['member_order'];
            while ($line = $db->fetchNextObject($member_query)) {
                if($line->member_order >= $_POST['member_order']){
                    $db->executeUpdate(['member_order'  => $new_order + 1], 'tbl_member', 'member_id = :member_id', ['member_id' => $line->member_id]);
                }else{
                    continue;
                }

                $new_order++;
            }
        }

        // Prepare the SQL array for insertion
        $sqlArray = array(
            'name'          => $_POST['name'],
            'position'      => $_POST['position'],
            'member_order'  => $_POST['member_order'],
            'status'        => $_POST['status'],
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
            'name'          => 'Member Name', 
            'position'      => 'Position',
            'member_order'  => 'Order',
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
        $member_id  = decrypt_data($_POST['member_id']);
        // Check for duplicate name
        $message = $db->hasDuplicate('SELECT name FROM tbl_member WHERE name = :name AND member_id != :member_id', [
            'name' => $_POST['name'],
            'member_id' => $member_id
        ]);
        if ($message) {
            Alert::error(array(
                'title' => 'Duplicate Entry',
                'html'  => $message,
                'path'  => $redirect_path
            ));
        }

        $duplicate_order = $db->hasDuplicate('SELECT member_order FROM tbl_member WHERE status = "Active"');

        if($duplicate_order){
            $member_query = $db->query('SELECT * FROM tbl_member WHERE status = "Active" ORDER BY member_order');
            $new_order = $_POST['member_order'];
            while ($line = $db->fetchNextObject($member_query)) {
                if($line->member_order >= $_POST['member_order']){
                    $db->executeUpdate(['member_order'  => $new_order + 1], 'tbl_member', 'member_id = :member_id', ['member_id' => $line->member_id]);
                }else{
                    continue;
                }

                $new_order++;
            }
        }

        // Prepare the SQL array for insertion
        $sqlArray = array(
            'name'          => $_POST['name'],
            'position'      => $_POST['position'],
            'member_order'  => $_POST['member_order'],
            'status'        => $_POST['status'],
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

        $member_query = $db->query('SELECT * FROM tbl_member WHERE status = "Active" ORDER BY member_order');
        $new_order = 1;
        while ($line = $db->fetchNextObject($member_query)) {
            $db->executeUpdate(['member_order'  => $new_order], 'tbl_member', 'member_id = :member_id', ['member_id' => $line->member_id]);

            $new_order++;
        }
        
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
