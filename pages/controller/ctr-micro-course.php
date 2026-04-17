<?php
include '../../includes/init.php';
$db = DB::getInstance();

$redirect_path = '../micro-course.php';

if (isset($_POST['Save'])) {
    try {
        // Check if the required fields are set
        $requiredFields = [
            'title'        => 'Program Title',
            'description'       => 'Description',
            'course_title'      => 'Course Title',
            'duration'          => 'Duration',
            'developer'         => 'Developer',
            'developer_email'   => 'Developer Email',
            'objective'         => 'Objective',
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
        $message = $db->hasDuplicate('SELECT title, course_title FROM tbl_micro_course WHERE title = :title AND course_title = :course_title', [
            'title' => $_POST['title'],
            'course_title' => $_POST['course_title']
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
            'title'             => $_POST['title'],
            'description'       => $_POST['description'],
            'course_title'      => $_POST['course_title'],
            'course_1'          => $_POST['course_1'] ?? null,
            'course_2'          => $_POST['course_2'] ?? null,
            'course_3'          => $_POST['course_3'] ?? null,
            'duration'          => $_POST['duration'],
            'credit_unit'       => $_POST['credit_unit'] ?? 0,
            'developer'         => $_POST['developer'],
            'developer_email'   => $_POST['developer_email'],
            'objective'         => $_POST['objective'],
            'policy'            => json_encode($_POST['policy'] ?? []),
            'status'            => $_POST['status'],
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
        $db->executeInsert($sqlArray, 'tbl_micro_course');
        
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
            'title'             => 'Program Title',
            'description'       => 'Description',
            'course_title'      => 'Course Title',
            'duration'          => 'Duration',
            'developer'         => 'Developer',
            'developer_email'   => 'Developer Email',
            'objective'         => 'Objective',
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
        $sub_prog_id  = decrypt_data($_POST['sub_prog_id']);
        // Check for duplicate sub_prog_name
        $message = $db->hasDuplicate('SELECT title, course_title FROM tbl_micro_course WHERE title = :title AND course_title = :course_title AND sub_prog_id != :sub_prog_id', [
            'title' => $_POST['title'],
            'course_title' => $_POST['course_title'],
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
            'title'             => $_POST['title'],
            'description'       => $_POST['description'],
            'course_title'      => $_POST['course_title'],
            'course_1'          => $_POST['course_1'] ?? null,
            'course_2'          => $_POST['course_2'] ?? null,
            'course_3'          => $_POST['course_3'] ?? null,
            'duration'          => $_POST['duration'],
            'credit_unit'       => $_POST['credit_unit'] ?? 0,
            'developer'         => $_POST['developer'],
            'developer_email'   => $_POST['developer_email'],
            'objective'         => $_POST['objective'],
            'policy'            => json_encode($_POST['policy'] ?? []),
            'status'            => $_POST['status'],
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
        $db->executeUpdate($sqlArray, 'tbl_micro_course', 'sub_prog_id = :sub_prog_id', ['sub_prog_id' => $sub_prog_id]);

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
        $db->executeDelete('tbl_micro_course', 'sub_prog_id = :sub_prog_id', ['sub_prog_id' => $id]);
        
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
