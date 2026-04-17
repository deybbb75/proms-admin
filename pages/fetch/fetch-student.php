<?php
include '../../includes/init.php';
$db = DB::getInstance();

if (isset($_POST['id'])) {
    $id = decrypt_data($_POST['id']);
    $student = $db->queryUniqueObject('SELECT * FROM tbl_student WHERE student_id = :student_id', ['student_id' => $id]);
    if ($student) {
        $student_id = encrypt_data($student->student_id);
        $fname      = e($student->fname);
        $mname      = e($student->mname);
        $lname      = e($student->lname);
        $student_no = e($student->student_no);
        $email      = e($student->email);
        $mobile_no  = e($student->mobile_no);
        $birthday   = e($student->birthday);
        $fb_link    = e($student->fb_link);
        $status     = e($student->status);
    }
}
?>
<input type="hidden" name="student_id" value="<?= $student_id ?? '' ?>">

<div class="row">
    <div class="col-md-4">
        <label for="fname" class="form-label required">First Name</label>
        <input type="text" id="fname" name="fname" class="form-control" placeholder="First Name" value="<?= $fname ?? '' ?>">
    </div>
    <div class="col-md-4">
        <label for="mname" class="form-label">Middle Name</label>
        <input type="text" id="mname" name="mname" class="form-control" placeholder="Middle Name (Optional)" value="<?= $mname ?? '' ?>">
    </div>
    <div class="col-md-4">
        <label for="lname" class="form-label required">Last Name</label>
        <input type="text" id="lname" name="lname" class="form-control" placeholder="Last Name" value="<?= $lname ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <label for="student_no" class="form-label required">Student Number</label>
        <input type="text" id="student_no" name="student_number" class="form-control" placeholder="Employee Number" value="<?= $student_no ?? '' ?>" data-toggle="input-mask" data-mask-format="00000000">
    </div>
    <div class="col-sm-6">
        <label for="email" class="form-label required">Email</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="Email" value="<?= $email ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <label for="role" class="form-label required">Mobile Number</label>
        <input type="tel" id="mobile_no" name="mobile_no" class="form-control" maxlength="11" value="<?= $mobile_no ?? '' ?>" placeholder="Mobile Number">
    </div>
    <div class="col-sm-6">
        <label for="role" class="form-label required">Birthday</label>
        <input type="date" id="birthday" name="birthday" class="form-control" value="<?= $birthday ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <label for="fb_link" class="form-label required">Facebook Link</label>
        <input type="text" id="fb_link" name="fb_link" class="form-control" value="<?= $fb_link ?? '' ?>" placeholder="Facebook Link">
    </div>
    <div class="col-sm-6">
        <label for="status" class="form-label required">Status</label>
        <select class="form-control select2" data-toggle="select2" name="status" data-placeholder="Select Status">
            <option value="<?= $status ?? '' ?>" selected>
                <?= !empty($status) ? $status : '' ?>
            </option>
            <?php
                if($status != "Active") {
            ?>
                <option value="Active">Active</option>
            <?php
                }
                if($status != "Inactive") {
            ?>
                <option value="Inactive">Inactive</option>
            <?php
                }
            ?>
        </select>
    </div>
</div>
