<?php
include '../../includes/init.php';
$db = DB::getInstance();

if (isset($_POST['id'])) {
    $id = decrypt_data($_POST['id']);
    $academic_year = $db->queryUniqueObject('SELECT * FROM tbl_academic_year WHERE ay_id = :ay_id', ['ay_id' => $id]);
    if ($academic_year) {
        $ay_id = encrypt_data($academic_year->ay_id);
        $year  = $academic_year->year;
        $semester  = $academic_year->semester;
        $status = $academic_year->status;
    }
}
?>
<input type="hidden" name="ay_id" value="<?= $ay_id ?? '' ?>">

<div class="row">
    <label class="form-label required" style="font-weight: 600;">Year</label>
    <div class="col-sm-12" id="schedule-container">
        <div class="row">
            <div class="col-lg-5">
                <input type="text" id="year1" name="year1" class="form-control" maxlength="4" placeholder="Academic Year Start" value="<?= $year1 ?? '' ?>">
            </div>
            <div class="col-lg-2">
                <p style="text-align: center; margin-bottom: 0px; margin-top: 5px;">TO</p>
            </div>
            <div class="col-lg-5">
                <input type="text" id="year2" name="year2" class="form-control" maxlength="4" placeholder="Academic Year End" value="<?= $year2 ?? '' ?>">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <label for="semester" class="form-label required">Semester</label>
        <select class="form-select select2" data-toggle="select2" name="semester" id="semester" data-placeholder="Select Semester">
            <option value="<?= $semester ?? '' ?>" <?php if(!$status) echo 'disabled'; ?> selected>
                <?= !empty($semester) ? $semester : '' ?>
            </option>
            <?php
                if($semester != "1st Semester") {
            ?>
                <option value="1st Semester">1st Semester</option>
            <?php
                }
                if($semester != "2nd Semester") {
            ?>
                <option value="2nd Semester">2nd Semester</option>
            <?php
                }
            ?>
        </select>
    </div>
    <div class="col-sm-6">
        <label for="status" class="form-label required">Status</label>
        <select class="form-control select2" data-toggle="select2" name ="status" data-placeholder="Select Status">
            <option value="<?= $status ?? '' ?>" <?php if(empty($status)) echo 'disabled'; ?> selected>
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
