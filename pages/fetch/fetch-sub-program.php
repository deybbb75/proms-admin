<?php
include '../../includes/init.php';
$db = DB::getInstance();

$sched_count = 0;

if (isset($_POST['id'])) {
    $id = decrypt_data($_POST['id']);
    $program = $db->queryUniqueObject('SELECT * FROM tbl_sub_program WHERE sub_prog_id = :sub_prog_id', ['sub_prog_id' => $id]);
    if ($program) {
        $sub_prog_id = encrypt_data($program->sub_prog_id);
        $sub_prog_name  = $program->sub_prog_name;
        $prog_id =encrypt_data($program->prog_id);
        $start_date_1  = $program->start_date_1;
        $start_date_2  = $program->start_date_2;
        $venue = $program->venue;
        $main_fee  = number_format($program->main_fee, 2, '.', ',');
        $sub_fee  = number_format($program->sub_fee, 2, '.', ',');
        $status = $program->status;
        $image = $program->sub_prog_img;
        $image_data = base64_encode($image);
        $image_type = $program->sub_prog_img_type;
        $image_src  = "data:{$image_type};base64,{$image_data}";
    }

    $day = [];
    $start_time = [];
    $end_time = [];
    $counter = 0;

    $sub_prog_sched_query = $db->query('SELECT * FROM tbl_schedule WHERE sub_prog_id = :sub_prog_id', ['sub_prog_id' => $id]);
    $sched_count = ($db->countOf("tbl_schedule", "sub_prog_id = :sub_prog_id", ['sub_prog_id' => $id])) - 1;

    while ($line = $db->fetchNextObject($sub_prog_sched_query)) {
        $day[$counter] = $line->day;
        $start_time[$counter] = $line->start_time;
        $end_time[$counter] = $line->end_time;
        $counter++;
    }
}
?>
<input type="hidden" name="prog_id" value="<?= $prog_id ?? encrypt_data($_SESSION['prog_id']) ?>">
<input type="hidden" name="sub_prog_id" value="<?= $sub_prog_id ?? '' ?>">

<div class="row">
    <div class="col-md-12">
        <label for="sub_prog_name" class="form-label required">Sub-program Name</label>
        <input type="text" id="sub_prog_name" name="sub_prog_name" class="form-control" placeholder="Sub-program Name" value="<?= $sub_prog_name ?? '' ?>">
    </div>
</div>
<?php
    if($_SESSION['prog_id'] == $_SESSION['unique_prog_id']){
?>
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
    <div class="col-sm-12" id="schedule-container" style="padding-bottom: 0px;">
        <div class="row">
            <div class="col-lg-4">
                <select class="form-control select2 day" data-toggle="select2" name="day[0]" data-placeholder="Select Day">
                    <option value="<?= $day[0] ?? '' ?>" <?php if(empty($day[0])) echo 'disabled'; ?> selected>
                        <?= !empty($day[0]) ? $day[0] : '' ?>
                    </option>
                    <?php
                        if($day[0] != "Sunday") {
                    ?>
                        <option value="Sunday">Sunday</option>
                    <?php
                        }
                        if($day[0] != "Monday") {
                    ?>
                        <option value="Monday">Monday</option>
                    <?php
                        }
                        if($day[0] != "Tuesday") {
                    ?>
                        <option value="Tuesday">Tuesday</option>
                    <?php
                        }
                        if($day[0] != "Wednesday") {
                    ?>
                        <option value="Wednesday">Wednesday</option>
                    <?php
                        }
                        if($day[0] != "Thursday") {
                    ?>
                        <option value="Thursday">Thursday</option>
                    <?php
                        }
                        if($day[0] != "Friday") {
                    ?>
                        <option value="Friday">Friday</option>
                    <?php
                        }
                        if($day[0] != "Saturday") {
                    ?>
                        <option value="Saturday">Saturday</option>
                    <?php
                        }
                    ?>
                </select>
                <span class="font-13 text-muted">Day of the Week</span>
            </div>
            <div class="col-lg-4">
                <input class="form-control start-time" id="start_time" type="time" name="start_time[0]" value="<?= $start_time[0] ?? '' ?>">
                <span class="font-13 text-muted">Start Time</span>
            </div>
            <div class="col-lg-4">
                <input class="form-control end-time" id="end_time" type="time" name="end_time[0]" value="<?= $end_time[0] ?? '' ?>">
                <span class="font-13 text-muted">End Time</span>
            </div>
        </div>

        <?php
            for ($i = 1; $i < $_SESSION['max_schedule']; $i++) {
                if (empty($day[$i]) || empty($start_time[$i]) || empty($end_time[$i])) {
                    continue;
                }
        ?>
            <div class="row">
                <div class="col-lg-4">
                    <select class="form-control select2 day" id="day" data-toggle="select2" name="day[<?= $i ?>]" data-placeholder="Select Day">
                        <option value="<?= $day[$i] ?? '' ?>" <?php if(empty($day[$i])) echo 'disabled'; ?> selected>
                            <?= !empty($day[$i]) ? $day[$i] : '' ?>
                        </option>
                        <?php
                            if($day[$i] != "Sunday") {
                        ?>
                            <option value="Sunday">Sunday</option>
                        <?php
                            }
                            if($day[$i] != "Monday") {
                        ?>
                            <option value="Monday">Monday</option>
                        <?php
                            }
                            if($day[$i] != "Tuesday") {
                        ?>
                            <option value="Tuesday">Tuesday</option>
                        <?php
                            }
                            if($day[$i] != "Wednesday") {
                        ?>
                            <option value="Wednesday">Wednesday</option>
                        <?php
                            }
                            if($day[$i] != "Thursday") {
                        ?>
                            <option value="Thursday">Thursday</option>
                        <?php
                            }
                            if($day[$i] != "Friday") {
                        ?>
                            <option value="Friday">Friday</option>
                        <?php
                            }
                            if($day[$i] != "Saturday") {
                        ?>
                            <option value="Saturday">Saturday</option>
                        <?php
                            }
                        ?>
                    </select>
                    <span class="font-13 text-muted">Day of the Week</span>
                </div>
                <div class="col-lg-4">
                    <input class="form-control start-time" id="start_time" type="time" name="start_time[<?= $i ?>]" value="<?= $start_time[$i] ?? '' ?>">
                    <span class="font-13 text-muted">Start Time</span>
                </div>
                <div class="col-lg-3">
                    <input class="form-control end-time" id="end_time" type="time" name="end_time[<?= $i ?>]" value="<?= $end_time[$i] ?? '' ?>">
                    <span class="font-13 text-muted">End Time</span>
                </div>
                <div class="col-lg-1">
                    <button type="button" class="btn btn-danger w-100" onclick="removeSchedule(this)"><i class="mdi mdi-close"></i></button>
                </div>
            </div>
        <?php
            }
        ?>
    </div>
    
    <div class="col-sm-12" id="add-sched-btn-container">
    <button type="button" class="btn btn-sm btn-info w-100" id="add-sched-btn" onclick="addSchedule(<?= $sched_count ?>)"><i class="mdi mdi-plus"></i> Add Schedule</button>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="venue" class="form-label required">Venue</label>
        <input type="text" id="venue" name="venue" class="form-control" placeholder="Venue" value="<?= $venue ?? '' ?>">
    </div>
</div>
<?php
    }
?>
<div class="row">
    <div class="col-sm-6">
        <label for="main_fee" class="form-label required"><?= $_SESSION['main_fee_title'] ?></label>
        <input type="text" id="main_fee" name="main_fee" class="form-control" data-toggle="input-mask" placeholder="<?= $_SESSION['main_fee_title'] ?>" value="<?= $main_fee ?? '' ?>"
            data-mask-format="000,000,000,000,000.00" data-reverse="true">
    </div>
    <div class="col-sm-6">
        <label for="sub_fee" class="form-label required"><?= $_SESSION['sub_fee_title'] ?></label>
        <input type="text" id="sub_fee" name="sub_fee" class="form-control" data-toggle="input-mask" placeholder="<?= $_SESSION['sub_fee_title'] ?>" value="<?= $sub_fee ?? '' ?>"
            data-mask-format="000,000,000,000,000.00" data-reverse="true">
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
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
<div class="row">
    <div class="col-md-12">
        <label for="img" class="form-label required">Image</label>
        <div id="image-preview" style="display: <?= !empty($image) ? 'block' : 'none' ?>;">
            <img 
                src="<?= $image_src ?? '' ?>" 
                class="img-fluid mx-auto d-block mt-2"
                style="max-width: 100%; height: auto; border-radius: 20px;"
                alt=""
            >
        <?php
            if(!empty($image)) {
        ?>
            <button type="button" class="btn btn-sm btn-info w-100 mt-2" onclick="updateImage('update')">Update Image</button>
        <?php
            }
        ?>
        </div>
        <div id="image-upload" style="display: <?= !empty($image) ? 'none' : 'block' ?>;">
            <input type="file" id="img_input" class="filepond" name="img_input" data-max-file-size="10MB" data-max-files="3" />
            <span class="font-10 text-muted"><b>Note: </b>Please upload an image in <b>JPG</b> format. The file size must not exceed <b>10 MB</b>.</span>
            <?php
                if(!empty($image)){
            ?>
                <button type="button" class="btn btn-sm btn-danger w-100 mt-2" onclick="updateImage('cancel')">Cancel</button>
            <?php
                }
            ?>
        </div>
        
    </div>
</div>
