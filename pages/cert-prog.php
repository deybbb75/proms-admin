<?php
include '../includes/init.php';
include '../header.php';
$db = DB::getInstance();

setActiveLink('program.php');

$_SESSION['proms-admin']['max_schedule'] = 3;
$_SESSION['proms-admin']['max_requirement'] = 10;
?>
<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                        onclick="addItem({fetch_file: 'fetch/fetch-cert-prog.php', custom_function: fetchCustom})">
                        <i class="mdi mdi-plus"></i>
                        <span class="add-btn-name">Add Sub-program</span>
                    </button>
                </div>
                <h4 class="page-title">SUB-PROGRAMS (<?= $db->queryUniqueValue("SELECT prog_name FROM tbl_program WHERE prog_id = 3") ?>)</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Start of Classes</th>
                                <th>Schedule</th>
                                <th>Venue</th>
                                <th>Fees</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sub_program_query = $db->query("SELECT * FROM tbl_cert_prog");

                                while ($line = $db->fetchNextObject($sub_program_query)) {
                            ?>
                            <tr>
                                <td><?= e($line->title) ?></td>
                                <td>
                                    <center>
                                        <p style="margin-bottom: 0;"><b>1st Semester:</b></p>
                                        <p><?= e($line->start_date_1) ?></p>
                                        <p style="margin-bottom: 0;"><b>2nd Semester:</b></p>
                                        <p style="margin-bottom: 0;"><?= e($line->start_date_2) ?></p>
                                    </center>
                                </td>
                                <td>
                                    <?php
                                        $schedules = json_decode($line->schedule, true);

                                        if(empty($schedules)){
                                    ?>
                                    <p style="color: red; font-style: italic;">No Schedule Available</p>
                                    <?php
                                        } else{
                                    ?>
                                    <ul style="padding-left: 1rem;">
                                    <?php
                                            foreach ($schedules as $schedule) {
                                    ?>
                                        <li><?= e($schedule['day']) ?>, <?= e(date("h:i A", strtotime($schedule['start_time']))) ?> - <?= e(date("h:i A", strtotime($schedule['end_time']))) ?></li>
                                    <?php
                                            }
                                    ?>
                                    </ul>
                                    <?php
                                        }
                                    ?>
                                </td>
                                <td>
                                    <div class="w-100 text-wrap">
                                        <?= e($line->venue) ?>
                                    </div>
                                </td>
                                <td>
                                    <center>
                                        <p style="margin-bottom: 0;"><b>Tuition Fee:</b></p>
                                        <p><?= e(number_format($line->main_fee, 2, '.', ',')) ?></p>
                                        <p style="margin-bottom: 0;"><b>Down Payment:</b></p>
                                        <p style="margin-bottom: 0;"><?= e(number_format($line->sub_fee, 2, '.', ',')) ?></p>
                                    </center>
                                </td>
                                <td>
                                    <span class="status-<?= strtolower($line->status) ?>"><?= $line->status ?></span>
                                </td>
                                <td>
                                    <center>
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                                            onclick="editItem({fetch_file: 'fetch/fetch-cert-prog.php', item_id: '<?= encrypt_data($line->sub_prog_id) ?>', custom_function: fetchCustom})">
                                            <i class="mdi mdi-square-edit-outline"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger ms-1" onclick="deleteItem('<?= encrypt_data($line->sub_prog_id) ?>')">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </center>
                                </td>
                            </tr>
                            <?php
                                }
                            ?>
                        </tbody>
                    </table>               
                </div> <!-- end card body-->
            </div> <!-- end card -->

            <button type="button" class="btn btn-warning mt-1 mb-3 w-100" onclick="window.location.href='program.php'">
                <i class="mdi mdi-keyboard-backspace"></i>
                <span class="add-btn-name">Go Back</span>
            </button>
        </div><!-- end col-->
    </div> <!-- end row-->

</div>
<!-- container -->

<div id="primary-header-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="primary-header-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="controller/ctr-cert-prog.php" method="POST" id="form_validation">
                <div class="modal-header modal-colored-header bg-primary">
                    <h4 class="modal-title" id="primary-header-modalLabel">Sub-program Details</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="fetched-data">
                        <!-- Content will be loaded here from "remote.php" file -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="save_changes">Save changes</button>
                </div>
                <input type="hidden" id="action" value="">
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<template id="schedule-template">
    <div class="row">
        <div class="col-lg-4">
            <select class="form-control select2 day" data-toggle="select2" name ="day" data-placeholder="Select Day">
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
            <input class="form-control start-time" id="start_time" type="time" name="start_time">
            <span class="font-13 text-muted">Start Time</span>
        </div>
        <div class="col-lg-3">
            <input class="form-control end-time" id="end_time" type="time" name="end_time">
            <span class="font-13 text-muted">End Time</span>
        </div>
        <div class="col-lg-1 mb-3">
            <button type="button" class="btn btn-danger w-100" onclick="removeOldItem(this, schedObj)"><i class="mdi mdi-close"></i></button>
        </div>
    </div>
</template>

<template id="requirement-template">
    <div class="row">
        <div class="col-lg-11 col-md-10 col-12 mb-2">
            <textarea class="form-control auto-grow-textarea requirement" id="requirement" name="requirement" rows="3" placeholder="Requirement"></textarea>
        </div>
        <div class="col-lg-1 col-md-2 col-12 mb-2">
            <button type="button" class="btn btn-danger w-100" onclick="removeOldItem(this, reqObj)"><i class="mdi mdi-close"></i></button>
        </div>
    </div>
</template>

<?php
include '../footer.php';
?>

<script>
function updateImage(action){
    const image_input_section = document.getElementById('image-upload');
    const preview = document.getElementById('image-preview');

    if(action === 'update'){
        image_input_section.style.display = 'block';
        preview.style.display = 'none';
    }else{
        image_input_section.style.display = 'none';
        preview.style.display = 'block';
    }
}

const schedObj = {
    fieldMap: {
        "select.day": "schedule[0][day]",
        "input.start-time": "schedule[0][start_time]",
        "input.end-time": "schedule[0][end_time]"
    },
    maxItems: <?= $_SESSION['proms-admin']['max_schedule'] ?>,
    templateId: "schedule-template",
    confirmTitle: "Are you sure you want to delete this schedule?"
};

const reqObj = {
    fieldMap: {
        "textarea.requirement": "requirement"
    },
    maxItems: <?= $_SESSION['proms-admin']['max_requirement'] ?>,
    templateId: "requirement-template",
    confirmTitle: "Are you sure you want to delete this requirement?"
};

function fetchCustom(){
    initFilePond(
        'img_input',
        ['image/jpeg'],
        'Only JPG files are allowed',
        ['#save_changes']
    );
}



</script>