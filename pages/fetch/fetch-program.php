<?php
include '../../includes/init.php';
$db = DB::getInstance();

if (isset($_POST['id'])) {
    $sys_id = decrypt_data($_POST['id']);
    $sys_user = $db->queryUniqueObject('SELECT * FROM tbl_system_user WHERE sys_id = :sys_id', ['sys_id' => $sys_id]);
    if ($sys_user) {
        $sys_id = encrypt_data($sys_user->sys_id);
        $fname  = $sys_user->fname;
        $mname  = $sys_user->mname;
        $lname  = $sys_user->lname;
        $emp_no = $sys_user->emp_no;
        $email  = $sys_user->email;
        $role   = $sys_user->role;
        $status = $sys_user->status;
    }
}
?>
<input type="hidden" name="sys_id" value="<?= $sys_id ?? '' ?>">

<div class="row">
    <div class="col-md-12">
        <label for="prog_title" class="form-label">Program Title</label>
        <input type="text" id="prog_title" name="prog_title" class="form-control" placeholder="Program Title" value="<?= $prog_title ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <label for="emp_no" class="form-label">Program Description</label>
        <textarea class="form-control" id="prog_desc" name="prog_desc" rows="5" placeholder="Program Description"><?= $prog_desc ?? '' ?></textarea>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <label for="emp_no" class="form-label">Start of Classes (1st Semester)</label>
        <input class="form-control" id="start_date1" type="date" name="start_date1" value="<?= $start_date1 ?? '' ?>">
    </div>
    <div class="col-sm-6">
        <label for="emp_no" class="form-label">Start of Classes (2nd Semester)</label>
        <input class="form-control" id="start_date2" type="date" name="start_date2" value="<?= $start_date2 ?? '' ?>">
    </div>
</div>
<div class="row">
    <p class="form-label" style="font-weight: 600;">Schedule</p>
    <div class="col-sm-12" id="schedule-container" style="padding-bottom: 0px;">
        <div class="row">
            <div class="col-lg-4">
                <select class="form-control select2 day" id="day" data-toggle="select2" name="day[0]" data-placeholder="Select Day">
                    <option value="<?= $day ?? '' ?>" <?php if(!empty($day)) echo 'disabled'; ?> selected>
                        <?= !empty($day) ? $day : '' ?>
                    </option>
                    <?php
                        if($day != "Sunday") {
                    ?>
                        <option value="Sunday">Sunday</option>
                    <?php
                        }
                        if($day != "Monday") {
                    ?>
                        <option value="Monday">Monday</option>
                    <?php
                        }
                        if($day != "Tuesday") {
                    ?>
                        <option value="Tuesday">Tuesday</option>
                    <?php
                        }
                        if($day != "Wednesday") {
                    ?>
                        <option value="Wednesday">Wednesday</option>
                    <?php
                        }
                        if($day != "Thursday") {
                    ?>
                        <option value="Thursday">Thursday</option>
                    <?php
                        }
                        if($day != "Friday") {
                    ?>
                        <option value="Friday">Friday</option>
                    <?php
                        }
                        if($day != "Saturday") {
                    ?>
                        <option value="Saturday">Saturday</option>
                    <?php
                        }
                    ?>
                </select>
                <span class="font-13 text-muted">Day of the Week</span>
            </div>
            <div class="col-lg-4">
                <input class="form-control start-time" id="start_time" type="time" name="start_time[0]">
                <span class="font-13 text-muted">Start Time</span>
            </div>
            <div class="col-lg-4">
                <input class="form-control end-time" id="end_time" type="time" name="end_time[0]">
                <span class="font-13 text-muted">End Time</span>
            </div>
        </div>
    </div>
    
    <div class="col-sm-12" id="add-sched-btn-container">
        <button type="button" class="btn btn-sm btn-info w-100" id="add-sched-btn" onclick="addSchedule(0)"><i class="mdi mdi-plus"></i> Add Schedule</button>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="venue" class="form-label">Venue</label>
        <input type="text" id="venue" name="venue" class="form-control" placeholder="Venue" value="<?= $venue ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <label for="tuition" class="form-label">Tuition Fees</label>
        <input type="text" id="tuition" name="tuition" class="form-control" data-toggle="input-mask" placeholder="Tuition Fees" value="<?= $tuition ?? '' ?>"
            data-mask-format="000,000,000,000,000.00" data-reverse="true">
    </div>
    <div class="col-sm-6">
        <label for="status" class="form-label">Status</label>
        <select class="form-control select2" data-toggle="select2" name ="status" data-placeholder="Select Status">
            <option value="<?= $status ?? '' ?>" <?php if(!empty($status)) echo 'disabled'; ?> selected>
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
