<?php
include '../includes/init.php';
include '../header.php';
$db = DB::getInstance();

$_SESSION['max_schedule'] = 3;
?>
<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                        onclick="addItem('fetch-program')">
                        <i class="mdi mdi-plus"></i>
                        <span class="add-btn-name">Add Program</span>
                    </button>
                </div>
                <h4 class="page-title">PROGRAMS</h4>
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
                                        <th>ID</th>
                                        <th>Program Title</th>
                                        <th>Description</th>
                                        <th>Start of Classes</th>
                                        <th>Schedule</th>
                                        <th>Tuition Fees</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
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
            <form action="controller/ctr-system-user.php" method="POST" id="form_validation">
                <div class="modal-header modal-colored-header bg-primary">
                    <h4 class="modal-title" id="primary-header-modalLabel">Modal Heading</h4>
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
                <input type="hidden" id="delete_id" value="">
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
</script>
<?php
include '../footer.php';
?>