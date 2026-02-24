<?php
include '../includes/init.php';
include '../header.php';
$db = DB::getInstance();

if(isset($_POST['ay_id'])){
    $_SESSION['proms-admin']['pending_ay_id'] = $_POST['ay_id'];
    safe_redirect('pending.php');
}

console($_SESSION['proms-admin']['pending_ay_id']);
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
                                <th>Student Name</th>
                                <th>Student Number</th>
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
                                    $student_name = $db->queryUniqueValue("SELECT CONCAT(fname, ' ', mname, ' ', lname) FROM tbl_student WHERE student_id = :student_id", ["student_id" => $line->student_id]);
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
                                <td class="text-wrap"><?= e($student_name) ?></td>
                                <td>
                                    <div class="w-100 text-wrap">
                                        <?php 
                                        if(!empty($student_no)){
                                            echo e($student_no);
                                        }else{
                                        ?>
                                        <p style="color: red; font-style: italic;">Not Yet Assigned</p>
                                        <?php
                                        } 
                                        ?>
                                    </div>
                                </td>
                                <td class="text-wrap"><?= e($program) ?></td>
                                <td class="text-wrap"><?= e($sub_program) ?></td>
                                <td><?= e($line->date_scheduled) ?></td>
                                <td><?= e($line->datetime_reserved) ?></td>
                                <td style="white-space: unset;">
                                    <?php
                                    if(!$student_no){
                                    ?>
                                    <button type="button" class="btn btn-success w-100" style="padding-block: 3px;" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                                        onclick="addStudentNumber({fetch_file: 'fetch/fetch-student-no.php', item_id: '<?= encrypt_data($line->reserve_id) ?>'})">
                                        Enrolled
                                    </button>
                                    <?php
                                    }else{
                                    ?>
                                    <button type="button" class="btn btn-success w-100 mt-1" style="padding-block: 3px;" onclick="enrollStudent('<?= encrypt_data($line->reserve_id) ?>')">
                                        Enrolled
                                    </button>
                                    <?php
                                    }
                                    ?>
                                    <button type="button" class="btn btn-warning w-100 mt-1" style="padding-block: 3px;" onclick="reserveStudent('<?= encrypt_data($line->reserve_id) ?>')">
                                        Reserved
                                    </button>
                                    <button type="button" class="btn btn-danger w-100 mt-1" style="padding-block: 3px;" onclick="expireStudent('<?= encrypt_data($line->reserve_id) ?>')">
                                        Expired
                                    </button>
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
                <input type="hidden" id="action" value="">
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
}

function enrollStudent(item_id) {
    $("#action").attr("name", "Enroll");
    $("#action").val(item_id);
    $("#form_validation").submit();
}

function reserveStudent(item_id) {
    $("#action").attr("name", "Reserve");
    $("#action").val(item_id);
    $("#form_validation").submit();
}

function expireStudent(item_id) {
    $("#action").attr("name", "Expire");
    $("#action").val(item_id);
    $("#form_validation").submit();
}
</script>