<?php
include '../../includes/init.php';
$db = DB::getInstance();

if (isset($_POST['id'])) {
    $id = decrypt_data($_POST['id']);
    $program = $db->queryUniqueObject('SELECT * FROM tbl_cert_prog WHERE sub_prog_id = :sub_prog_id', ['sub_prog_id' => $id]);
    if ($program) {
        $sub_prog_id          = encrypt_data($program->sub_prog_id);
        $title          = e($program->title);
        $description    = e($program->description);
        $class_details  = e($program->class_details);
        $start_date_1   = e($program->start_date_1);
        $start_date_2   = e($program->start_date_2);
        $schedule       = json_decode($program->schedule, true);
        $venue          = e($program->venue);
        $main_fee       = number_format($program->main_fee, 2, '.', ',');
        $sub_fee        = number_format($program->sub_fee, 2, '.', ',');
        $note           = e($program->note);
        $requirement    = json_decode($program->requirement, true);
        $status         = e($program->status);
        $image          = $program->img;
        $image_data     = base64_encode($image);
        $image_type     = $program->img_type;
        $image_src      = "data:{$image_type};base64,{$image_data}";
    }
}
?>
<input type="hidden" name="sub_prog_id" value="<?= $sub_prog_id ?? '' ?>">

<div class="row">
    <div class="col-md-12">
        <label for="title" class="form-label required">Title</label>
        <input type="text" id="title" name="title" class="form-control" placeholder="Title" value="<?= $title ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="description" class="form-label required">Description</label>
        <textarea class="form-control auto-grow-textarea" id="description" name="description" rows="5" placeholder="Description"><?= $description ?? '' ?></textarea>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="class_details" class="form-label required">Class Details</label>
        <textarea class="form-control auto-grow-textarea" id="class_details" name="class_details" rows="5" placeholder="Class Details"><?= $class_details ?? '' ?></textarea>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <label for="emp_no" class="form-label required">Start of Classes (1st Semester)</label>
        <input class="form-control" id="start_date_1" type="date" name="start_date_1" value="<?= $start_date_1 ?? '' ?>">
    </div>
    <div class="col-sm-6">
        <label for="emp_no" class="form-label required">Start of Classes (2nd Semester)</label>
        <input class="form-control" id="start_date_2" type="date" name="start_date_2" value="<?= $start_date_2 ?? '' ?>">
    </div>
</div>
<div class="row">
    <label class="form-label required" style="font-weight: 600;">Schedule</label>
    <div class="col-sm-12" style="padding-bottom: 0px;">
        <?php
            if(!isset($schedule)){
        ?>
        <div class="row">
            <div class="col-lg-4">
                <select class="form-control select2 day" data-toggle="select2" name="schedule[0][day]" data-placeholder="Select Day">
                    <option value="" disabled selected></option>
                    <option value="Sunday">Sunday</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                </select>
                <span class="font-13 text-muted">Day of the Week</span>
            </div>
            <div class="col-lg-4">
                <input class="form-control start-time" id="start_time" type="time" name="schedule[0][start_time]">
                <span class="font-13 text-muted">Start Time</span>
            </div>
            <div class="col-lg-4">
                <input class="form-control end-time" id="end_time" type="time" name="schedule[0][end_time]">
                <span class="font-13 text-muted">End Time</span>
            </div>
        </div>

        <?php
            }else{
                for ($i = 0; $i < count($schedule); $i++) {
        ?>
        <div class="row">
            <div class="col-lg-4">
                <select class="form-control select2 day" id="day" data-toggle="select2" name="schedule[<?= $i ?>][day]" data-placeholder="Select Day">
                    <option value="<?= $schedule[$i]['day'] ?? '' ?>" <?php if(empty($schedule[$i]['day'])) echo 'disabled'; ?> selected>
                        <?= !empty($schedule[$i]['day']) ? $schedule[$i]['day'] : '' ?>
                    </option>
                    <?php
                        if($schedule[$i]['day'] != "Sunday") {
                    ?>
                        <option value="Sunday">Sunday</option>
                    <?php
                        }
                        if($schedule[$i]['day'] != "Monday") {
                    ?>
                        <option value="Monday">Monday</option>
                    <?php
                        }
                        if($schedule[$i]['day'] != "Tuesday") {
                    ?>
                        <option value="Tuesday">Tuesday</option>
                    <?php
                        }
                        if($schedule[$i]['day'] != "Wednesday") {
                    ?>
                        <option value="Wednesday">Wednesday</option>
                    <?php
                        }
                        if($schedule[$i]['day'] != "Thursday") {
                    ?>
                        <option value="Thursday">Thursday</option>
                    <?php
                        }
                        if($schedule[$i]['day'] != "Friday") {
                    ?>
                        <option value="Friday">Friday</option>
                    <?php
                        }
                        if($schedule[$i]['day'] != "Saturday") {
                    ?>
                        <option value="Saturday">Saturday</option>
                    <?php
                        }
                    ?>
                </select>
                <span class="font-13 text-muted">Day of the Week</span>
            </div>
            <div class="col-lg-4">
                <input class="form-control start-time" id="start_time" type="time" name="schedule[<?= $i ?>][start_time]" value="<?= e($schedule[$i]['start_time']) ?? '' ?>">
                <span class="font-13 text-muted">Start Time</span>
            </div>
            <div class="col-lg-3">
                <input class="form-control end-time" id="end_time" type="time" name="schedule[<?= $i ?>][end_time]" value="<?= e($schedule[$i]['end_time']) ?? '' ?>">
                <span class="font-13 text-muted">End Time</span>
            </div>
            <div class="col-lg-1 mb-3">
                <button type="button" class="btn btn-danger w-100" onclick="removeOldItem(this, schedObj)"><i class="mdi mdi-close"></i></button>
            </div>
        </div>
        <?php
                }
            }
        ?>
    </div>
    
    <div class="col-sm-12">
        <button type="button" class="btn btn-sm btn-info w-100" id="add-sched-btn" onclick="addNewItem(this, schedObj)"><i class="mdi mdi-plus"></i> Add Schedule</button>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="venue" class="form-label required">Venue</label>
        <input type="text" id="venue" name="venue" class="form-control" placeholder="Venue" value="<?= $venue ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <label for="main_fee" class="form-label required">Tution Fee</label>
        <input type="text" id="main_fee" name="main_fee" class="form-control" data-toggle="input-mask" placeholder="Tution Fee" value="<?= $main_fee ?? '' ?>"
            data-mask-format="000,000,000,000,000.00" data-reverse="true">
    </div>
    <div class="col-sm-6">
        <label for="sub_fee" class="form-label required">Down Payment</label>
        <input type="text" id="sub_fee" name="sub_fee" class="form-control" data-toggle="input-mask" placeholder="Down Payment" value="<?= $sub_fee ?? '' ?>"
            data-mask-format="000,000,000,000,000.00" data-reverse="true">
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="note" class="form-label required">Note</label>
        <textarea class="form-control auto-grow-textarea" id="note" name="note" rows="5" placeholder="Note"><?= $note ?? '' ?></textarea>
    </div>
</div>
<div class="row">
    <label class="form-label required" style="font-weight: 600;">Requirement/s</label>
    <div class="col-sm-12" style="padding-bottom: 0px;">
        <?php
            if(!isset($requirement)){
        ?>
        <div class="row">
            <div class="col-lg-12">
                <textarea class="form-control auto-grow-textarea requirement" id="requirement" name="requirement[0]" rows="3" placeholder="Requirement"></textarea>
            </div>
        </div>

        <?php
            }else{
                for ($i = 0; $i < count($requirement); $i++) {
        ?>
        <div class="row">
            <div class="col-lg-11 mb-2">
                <textarea class="form-control auto-grow-textarea requirement" id="requirement" name="requirement[<?= $i ?>]" rows="3" placeholder="Requirement"><?= e($requirement[$i]) ?? '' ?></textarea>
            </div>
            <div class="col-lg-1 mb-2">
                <button type="button" class="btn btn-danger w-100" onclick="removeOldItem(this, reqObj)"><i class="mdi mdi-close"></i></button>
            </div>
        </div>
        <?php
                }
            }
        ?>
    </div>
    
    <div class="col-sm-12">
        <button type="button" class="btn btn-sm btn-info w-100" onclick="addNewItem(this, reqObj)"><i class="mdi mdi-plus"></i> Add Requirement</button>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
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
