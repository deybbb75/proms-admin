<?php
include '../../includes/init.php';
$db = DB::getInstance();

$sched_count = 0;

if (isset($_POST['id'])) {
    $id = decrypt_data($_POST['id']);
    $program = $db->queryUniqueObject('SELECT * FROM tbl_program WHERE prog_id = :prog_id', ['prog_id' => $id]);
    if ($program) {
        $prog_id        = encrypt_data($program->prog_id);
        $prog_name      = e($program->prog_name);
        $enroll_status  = e($program->enroll_status);
        $status         = e($program->status);
        $image          = $program->img;
        $image_data     = base64_encode($image);
        $image_type     = $program->img_type;
        $image_src      = "data:{$image_type};base64,{$image_data}";
    }
}
?>
<input type="hidden" name="prog_id" value="<?= $prog_id ?? '' ?>">

<div class="row">
    <div class="col-md-12">
        <label for="prog_name" class="form-label required">Program Name</label>
        <input type="text" id="prog_name" name="prog_name" class="form-control" placeholder="Program Title" value="<?= $prog_name ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <label for="enroll_status" class="form-label required">Enrollment Status</label>
        <select class="form-control select2" data-toggle="select2" name="enroll_status" data-placeholder="Select Enrollment Status">
            <option value="<?= $enroll_status ?? '' ?>" selected>
                <?= !empty($enroll_status) ? $enroll_status : '' ?>
            </option>
            <?php
                if($enroll_status != "Active") {
            ?>
                <option value="Active">Active</option>
            <?php
                }
                if($enroll_status != "Inactive") {
            ?>
                <option value="Inactive">Inactive</option>
            <?php
                }
            ?>
        </select>
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
<div class="row">
    <div class="col-md-12">
        <label for="img" class="form-label required">Image</label>
        <input type="file" id="img_input" class="filepond" name="img_input" data-max-file-size="10MB"/>
        <span class="font-10 text-muted"><b>Note: </b>Please upload an image in <b>JPG</b> format. The file size must not exceed <b>10 MB</b>.</span>        
    </div>
</div>
