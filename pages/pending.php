<?php
include '../includes/init.php';
include '../header.php';
$db = DB::getInstance();

if(!isset($_SESSION['proms-admin']['pending_ay_id'])){
    $_SESSION['proms-admin']['pending_ay_id'] = $db->queryUniqueValue("SELECT ay_id FROM tbl_academic_year WHERE status = 'Active'");
}

if(isset($_POST['ay_id'])){
    $_SESSION['proms-admin']['pending_ay_id'] = $_POST['ay_id'];
    safe_redirect('pending.php');
}
?>
<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">PENDING RESERVATIONS</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12" style="margin-bottom: 30px;">
                            <label for="ay_id" class="form-label">Academic Year Filter:</label>
                            <form action="" method="POST" id="acad_year" style="display: flex; gap: 10px;">
                                <select class="form-control select2" data-toggle="select2" name="ay_id" data-placeholder="Select Academic Year">
                                <?php
                                $ay_query = $db->query("SELECT * FROM tbl_academic_year");

                                while ($line = $db->fetchNextObject($ay_query)) {
                                ?>
                                    <option value="<?= $line->ay_id ?>" <?php if($_SESSION['proms-admin']['pending_ay_id'] == $line->ay_id) echo 'selected'; ?>>
                                        <?= $line->year ?> ( <?= $line->semester ?> )
                                    </option>
                                <?php
                                }
                                ?>
                                </select>
                                <button class="btn btn-success" style="width: 200px;" onclick="addItem({fetch_file: 'fetch-system-user'})">
                                    <span class="add-btn-name">Apply Filter</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>Student Number</th>
                                <th>Student Name</th>
                                <th>Program</th>
                                <th>Sub-program</th>
                                <th>Scheduled Date</th>
                                <th>Date/Time Reserved</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $reservation_query = $db->query("SELECT * FROM tbl_reservation WHERE ay_id = :ay_id AND status ='Pending'", ['ay_id' => $_SESSION['proms-admin']['pending_ay_id']]);

                                while ($line = $db->fetchNextObject($reservation_query)) {
                                    $student_no = $db->queryUniqueValue("SELECT student_no FROM tbl_student WHERE student_id = :student_id", ["student_id" => $line->student_id]);
                                    $student_name = $db->queryUniqueValue("SELECT CONCAT(fname, ' ', IFNULL(mname, ''), ' ', lname) FROM tbl_student WHERE student_id = :student_id", ["student_id" => $line->student_id]);
                                    $program = $db->queryUniqueValue("SELECT prog_name FROM tbl_program WHERE prog_id = :prog_id", ["prog_id" => $line->prog_id]);

                                    if($line->prog_id == 1){
                                        $table_name = "tbl_assess_cert";
                                    }else if($line->prog_id == 2){
                                        $table_name = "tbl_foreign_lang";
                                    }else if($line->prog_id == 3){
                                        $table_name = "tbl_cert_prog"; 
                                    }else if($line->prog_id == 4){
                                        $table_name = "tbl_short_term";
                                    }else if($line->prog_id == 5){
                                        $table_name = "tbl_micro_course";
                                    }else if($line->prog_id == 6){
                                        $table_name = "tbl_ms_prog"; 
                                    }

                                    $sub_program = $db->queryUniqueValue("SELECT title FROM " . $table_name . " WHERE sub_prog_id = :sub_prog_id", ["sub_prog_id" => $line->sub_prog_id]);      
                            ?>
                            <tr>
                                <td>
                                    <div class="w-100 text-wrap">
                                        <?php 
                                        if(!empty($student_no)){
                                            echo e($student_no);
                                        }else{
                                        ?>
                                        <p style="color: red; font-style: italic;">No student number assigned yet</p>
                                        <?php
                                        } 
                                        ?>
                                    </div>
                                </td>
                                <td class="text-wrap"><?= e($student_name) ?></td>
                                <td class="text-wrap"><?= e($program) ?></td>
                                <td class="text-wrap"><?= e($sub_program) ?></td>
                                <td><?= e($line->date_scheduled) ?></td>
                                <td><?= e($line->datetime_reserved) ?></td>
                                <td style="white-space: unset;">
                                    <select class="customSelect" id="action_pending" data-placeholder="" onchange="actionPending(this.value, '<?= encrypt_data($line->reserve_id) ?>')">
                                        <option value="" selected disabled>Select Action</option>    
                                        <option value="<?php if(!$student_no){ echo "First Enroll"; }else{ echo "Enroll"; } ?>">Mark Enrolled</option>
                                        <option value="Reserve">Reserve Slot</option>
                                        <option value="Expire">Expire Reservation</option>
                                    </select>
                                </td>
                            </tr>
                            <?php
                                }
                            ?>
                        </tbody>
                    </table>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div> <!-- end row-->

</div>
<!-- container -->

<div id="primary-header-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="primary-header-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="controller/ctr-reservation.php" method="POST" id="form_validation">
                <div class="modal-header modal-colored-header bg-primary">
                    <h4 class="modal-title" id="primary-header-modalLabel">Student Details</h4>
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
                <input type="hidden" id="action" name="action" value="">
                <input type="hidden" id="item_id" name="item_id" value="">
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
include '../footer.php';
?>

<script>
let container = document.getElementById('acad_year');
reInitUI($(container));

function addStudentNumber({
    fetch_file: fetch_file,
    item_id: item_id,
}) {
    Swal.fire({
        title: "Confirm student enrollment status?",
        text: "This will set the student's status to Enrolled.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "var(--success-color)",
        confirmButtonText: "Yes, mark as enrolled",
        cancelButtonText: "Cancel"
    }).then((result) => {
		if (result.isConfirmed) {
            let modal = new bootstrap.Modal(document.getElementById("primary-header-modal"));
            modal.show();

            // Reset the form and set the button to "Save"
            $(".fetched-data").html("");
            $("#save_changes").attr("name", "Add");
            $.ajax({
                type: "post",
                data: {
                    id: item_id,
                },
                url: fetch_file,
                success: function (data) {
                    let $fetch = $(".fetched-data").html(data);
                    reInitUI($fetch);
                },
            });
        } else if (result.isDismissed) {
            $('#action_pending').val(null).trigger('change');
        }
    });
}

function enrollStudent(item_id) {
    Swal.fire({
        title: "Confirm student enrollment status?",
        text: "This will set the student's status to Enrolled.",
        icon: "warning",
        showCancelButton: true,
        allowOutsideClick: false,
        confirmButtonColor: "var(--success-color)",
        confirmButtonText: "Yes, mark as enrolled",
        cancelButtonText: "Cancel"
    }).then((result) => {
		if (result.isConfirmed) {
            $("#action").val("Enrolled");
            $("#item_id").val(item_id);
            $("#form_validation").submit();
        } else if (result.isDismissed) {
            $('#action_pending').val(null).trigger('change');
        }
    });
}

function reserveStudent(item_id) {
    Swal.fire({
        title: "Reserve this student's slot?",
        text: "This will reserve the student's enrollment slot.",
        icon: "warning",
        showCancelButton: true,
        allowOutsideClick: false,
        confirmButtonColor: "var(--primary-color)",
        confirmButtonText: "Yes, reserve slot",
        cancelButtonText: "Cancel"
    }).then((result) => {
		if (result.isConfirmed) {
            $("#action").val("Reserved");
            $("#item_id").val(item_id);
            $("#form_validation").submit();
        } else if (result.isDismissed) {
            $('#action_pending').val(null).trigger('change');
        }
    });
}

function expireStudent(item_id) {
    Swal.fire({
        title: "Mark reservation as expired?",
        text: "Mark this reservation as expired? This action will notify the client.",
        icon: "warning",
        showCancelButton: true,
        allowOutsideClick: false,
        confirmButtonColor: "var(--danger-color)",
        confirmButtonText: "Yes, mark as expired",
        cancelButtonText: "Cancel",
    }).then((result) => {
		if (result.isConfirmed) {
            $("#action").val("Expired");
            $("#item_id").val(item_id);
            $("#form_validation").submit();
        } else if (result.isDismissed) {
            $('#action_pending').val(null).trigger('change');
        }
    });
}

function actionPending(value, item_id){
    if(value == "First Enroll"){
        addStudentNumber({fetch_file: 'fetch/fetch-pending.php', item_id: item_id});
    }else if(value == "Enroll"){
        enrollStudent(item_id);
    }else if(value == "Reserve"){
        reserveStudent(item_id);
    }else if(value == "Expire"){
        expireStudent(item_id);
    }
}
</script>