<?php
include '../../includes/init.php';
$db = DB::getInstance();

if (isset($_POST['id'])) {
    $id = decrypt_data($_POST['id']);
    $student_id = $db->queryUniqueValue("SELECT student_id FROM tbl_reservation WHERE reserve_id = :reserve_id", ["reserve_id" => $id]);
    $student_name = $db->queryUniqueValue("SELECT CONCAT(fname, ' ', IFNULL(mname, ''), ' ', lname) FROM tbl_student WHERE student_id = :student_id", ["student_id" => $student_id]);
}
?>
<input type="hidden" name="reserve_id" value="<?= $_POST['id'] ?? '' ?>">

<div class="row">
    <div class="col-sm-12">
        <label for="student_name" class="form-label required">Student Name</label>
        <input type="text" id="student_name" class="form-control" placeholder="Student Name" value="<?= $student_name ?? '' ?>" disabled>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <label for="student_no" class="form-label required">Student Number</label>
        <input type="text" id="student_no" name="student_no" class="form-control" placeholder="Student Number" value="<?= $student_no ?? '' ?>" data-toggle="input-mask" data-mask-format="00000000">
    </div>
</div>