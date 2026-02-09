<?php
include '../../includes/init.php';
$db = DB::getInstance();

$redirect_path = '../academic-year.php';

if (isset($_POST['Save'])) {
    try {
        // Check if the required fields are set
        $requiredFields = [
            'year1' => 'Academic Year Start', 
            'year2' => 'Academic Year End', 
            'semester' => 'Semester', 
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

        $year = $_POST['year1'] . '-' . $_POST['year2'];

        // Check for duplicate academic year
        $message = $db->hasDuplicate('SELECT year FROM tbl_academic_year WHERE year = :year AND semester = :semester', [
            'year' => $year,
            'semester' => $_POST['semester']
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
            'year'  => $year,
            'semester' => $_POST['semester'],
            'status'  => $_POST['status'],
        );
        // Execute the insert operation
        $db->executeInsert($sqlArray, 'tbl_academic_year');

        if($_POST['status'] == 'Active') {
            // Deactivate other academic years if the new one is set to Active
            $db->executeUpdate(['status' => 'Inactive'],'tbl_academic_year', 'ay_id != :ay_id', ['ay_id' => $db->lastInsertedId()]);
        }
        
        // Check if the insert was successful
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Save Successful',
                'html'  => 'Academic Year successfully saved.',
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
            'year1' => 'Academic Year Start', 
            'year2' => 'Academic Year End', 
            'semester' => 'Semester', 
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

        $year = $_POST['year1'] . '-' . $_POST['year2'];

        // Decrypt the id
        $ay_id  = decrypt_data($_POST['ay_id']);
        // Check for duplicate year and semester excluding the current record
        $message = $db->hasDuplicate('SELECT year, semester FROM tbl_academic_year WHERE (year = :year AND semester = :semester) AND ay_id != :ay_id', [
            'year' => $year,
            'semester' => $_POST['semester'],
            'ay_id' => $ay_id
        ]);

        if ($message) {
            Alert::error(array(
                'title' => 'Duplicate Entry',
                'html'  => $message,
                'path'  => $redirect_path
            ));
        }

        // Prepare the SQL array for update
        $sqlArray = array(
            'year'  => $year,
            'semester' => $_POST['semester'],
            'status'  => $_POST['status'],
        );
        // Execute the update operation
        $db->executeUpdate($sqlArray, 'tbl_academic_year', 'ay_id = :ay_id', ['ay_id' => $ay_id]);

        if($_POST['status'] == 'Active') {
            // Deactivate other academic years if the new one is set to Active
            $db->executeUpdate(['status' => 'Inactive'],'tbl_academic_year', 'ay_id != :ay_id', ['ay_id' => $ay_id]);
        }

        // Check if the update was successful
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Update Successful',
                'html'  => 'Academic Year successfully updated.',
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
        $db->executeDelete('tbl_academic_year', 'ay_id = :ay_id', ['ay_id' => $id]);
        
        if ($db->affectedRows > 0) {
            Alert::success(array(
                'title' => 'Delete Successful',
                'html'  => 'Academic Year successfully deleted.',
                'path'  => $redirect_path
            ));
        } else {
            Alert::error(array(
                'title' => 'Delete Failed',
                'html'  => 'No academic year found with the provided ID.',
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
