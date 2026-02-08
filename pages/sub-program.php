<?php
include '../includes/init.php';
include '../header.php';
$db = DB::getInstance();

setActiveLink('program.php');

$_SESSION['max_schedule'] = 3;
$_SESSION['unique_prog_id'] = 3;

if($_SESSION['prog_id'] == $_SESSION['unique_prog_id']){
    $_SESSION['main_fee_title'] = "Tuition Fee";
    $_SESSION['sub_fee_title'] = "Down Payment";
}else{
    $_SESSION['main_fee_title'] = "Assessment Fee";
    $_SESSION['sub_fee_title'] = "Processing Fee";
}
?>

<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                        onclick="addItem({fetch_name: 'fetch-sub-program', custom_function: fetchCustom})">
                        <i class="mdi mdi-plus"></i>
                        <span class="add-btn-name">Add Sub-program</span>
                    </button>
                </div>
                <h4 class="page-title">SUB-PROGRAMS (<?= $db->queryUniqueValue("SELECT prog_name FROM tbl_program WHERE prog_id = :prog_id", ["prog_id" => $_SESSION['prog_id'] ]) ?>)</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane show active" id="basic-datatable-preview">
                            <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Sub-program Name</th>
                                    <?php
                                        if($_SESSION['prog_id'] == $_SESSION['unique_prog_id']){
                                    ?>
                                        <th>Start of Classes</th>
                                        <th>Schedule</th>
                                        <th>Venue</th>
                                    <?php
                                        }
                                    ?>
                                        <th>Fees</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sub_program_query = $db->query("SELECT * FROM tbl_sub_program WHERE prog_id = :prog_id", ["prog_id" => $_SESSION['prog_id']]);

                                        while ($line = $db->fetchNextObject($sub_program_query)) {
                                    ?>
                                    <tr>
                                        <td><?= $line->sub_prog_name ?></td>
                                        <?php
                                            if($_SESSION['prog_id'] == $_SESSION['unique_prog_id']){
                                        ?>
                                        <td>
                                            <center>
                                                <p style="margin-bottom: 0;"><b>1st Semester:</b></p>
                                                <p><?= $line->start_date_1 ?></p>
                                                <p style="margin-bottom: 0;"><b>2nd Semester:</b></p>
                                                <p style="margin-bottom: 0;"><?= $line->start_date_2 ?></p>
                                            </center>
                                        </td>
                                        <td>
                                            <?php
                                                $schedule_query = $db->query("SELECT * FROM tbl_schedule WHERE sub_prog_id = :sub_prog_id", ["sub_prog_id" => $line->sub_prog_id]);

                                                if($db->numRows($schedule_query) == 0){
                                            ?>
                                            <p style="color: red; font-style: italic;">No Schedule Available</p>
                                            <?php
                                                } else{
                                            ?>
                                            <ul style="padding-left: 1rem;">
                                            <?php
                                                    while ($sched_line = $db->fetchNextObject($schedule_query)) {
                                            ?>
                                                <li><?= $sched_line->day ?>, <?= date("h:i A", strtotime($sched_line->start_time)) ?> - <?= date("h:i A", strtotime($sched_line->end_time)) ?></li>
                                            <?php
                                                    }
                                            ?>
                                            </ul>
                                            <?php
                                                }
                                            ?>
                                        </td>
                                        <td><?= $line->venue ?></td>
                                        <?php
                                            }
                                        ?>
                                        <td>
                                            <center>
                                                <p style="margin-bottom: 0;"><b><?= $_SESSION['main_fee_title'] ?>:</b></p>
                                                <p><?= number_format($line->main_fee, 2, '.', ',') ?></p>
                                                <p style="margin-bottom: 0;"><b><?= $_SESSION['sub_fee_title'] ?>:</b></p>
                                                <p style="margin-bottom: 0;"><?= number_format($line->sub_fee, 2, '.', ',') ?></p>
                                            </center>
                                        </td>
                                        <td>
                                            <span class="status-<?= strtolower($line->status) ?>"><?= $line->status ?></span>
                                        </td>
                                        <td>
                                            <center>
                                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                                                    onclick="editItem({fetch_name: 'fetch-sub-program', item_id: '<?= encrypt_data($line->prog_id) ?>', custom_function: fetchCustom})">
                                                    <i class="mdi mdi-square-edit-outline"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger ms-1" onclick="deleteItem('<?= encrypt_data($line->prog_id) ?>')">
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
                        </div> <!-- end preview-->
                    </div> <!-- end tab-content-->
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div> <!-- end row-->

</div>
<!-- container -->

<div id="primary-header-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="primary-header-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="controller/ctr-sub-program.php" method="POST" id="form_validation">
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
        <div class="col-lg-1">
            <button type="button" class="btn btn-danger w-100" onclick="removeSchedule(this)"><i class="mdi mdi-close"></i></button>
        </div>
    </div>
</template>

<script>
function addSchedule(schedCounter) {
    const template = document.getElementById("schedule-template");
    const container = document.getElementById("schedule-container");

    schedCounter++;

    if (schedCounter <= <?= $_SESSION['max_schedule'] ?>) {
        // Update the button's onclick with new counter
        document.getElementById('add-sched-btn').setAttribute("onclick", `addSchedule(${schedCounter})`);

        // Clone the template content
        const clone = template.content.cloneNode(true);

        const daySelect = clone.querySelector('select');
        daySelect.name = `day[${schedCounter}]`;

        const startTimeInput = clone.getElementById('start_time');
        startTimeInput.name = `start_time[${schedCounter}]`;

        const endTimeInput = clone.getElementById('end_time');
        endTimeInput.name = `end_time[${schedCounter}]`;
        
        // Append to the DOM
        container.appendChild(clone);
        reInitUI($(container));
    }
    if ((schedCounter + 1) == <?= $_SESSION['max_schedule'] ?>) {
        document.getElementById('add-sched-btn-container').style.display = "none";
    }
    console.log(schedCounter);
    
}

function removeSchedule(element) {
    Swal.fire({
        title: "Are you sure you want to delete this schedule?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#FF2121",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
		if (result.isConfirmed) {
            // Remove the schedule container
            element.closest('.row').remove();
            
            // Re-index remaining schedule inputs
            const schedSelects = document.querySelectorAll('select.day');
            for (let i = 0; i < schedSelects.length; i++) {
                schedSelects[i].name = `day[${i}]`;
            }

            const schedStartDate = document.querySelectorAll('input.start-time');
            for (let i = 0; i < schedStartDate.length; i++) {
                schedStartDate[i].name = `start_time[${i}]`;
            }

            const schedEndDate = document.querySelectorAll('input.end-time');
            for (let i = 0; i < schedEndDate.length; i++) {
                schedEndDate[i].name = `end_time[${i}]`;
            }

            document.getElementById('add-sched-btn').setAttribute("onclick", `addSchedule(${schedSelects.length - 1})`);

            // Show the add button again (if it was hidden)
            document.getElementById('add-sched-btn-container').style.display = "block";
        }
    });
}

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

function fetchCustom(){
    initFilePond(
        'img_input',
        ['image/jpeg'],
        'Only JPG files are allowed',
        ['#save_changes']
    );
}
</script>
<?php
include '../footer.php';
?>