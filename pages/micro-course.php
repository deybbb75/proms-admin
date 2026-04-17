<?php
include '../includes/init.php';
include '../header.php';
$db = DB::getInstance();

setActiveLink('program.php');

$_SESSION['proms-admin']['max_policy'] = 10;
?>

<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                        onclick="addItem({fetch_file: 'fetch/fetch-micro-course.php', custom_function: () => fetchCustom()})">
                        <i class="mdi mdi-plus"></i>
                        <span class="add-btn-name">Add Sub-program</span>
                    </button>
                </div>
                <h4 class="page-title">SUB-PROGRAMS (<?= $db->queryUniqueValue("SELECT prog_name FROM tbl_program WHERE prog_id = 5") ?>)</h4>
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
                                <th>Program Title</th>
                                <th>Course Title</th>
                                <th>Courses</th>
                                <th>Duration</th>
                                <th>Credits Units</th>
                                <th>Developer</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sub_program_query = $db->query("SELECT * FROM tbl_micro_course");

                                while ($line = $db->fetchNextObject($sub_program_query)) {
                            ?>
                            <tr>
                                <td>
                                    <div class="w-100 text-wrap">
                                        <?= e($line->title) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="w-100 text-wrap">
                                        <?= e($line->course_title) ?>
                                    </div>
                                </td>
                                <td>
                                    <center>
                                        <?php
                                            if (!empty($line->course_1) || !empty($line->course_2) || !empty($line->course_3)) {
                                        ?>
                                        <p style="margin-bottom: 0;"><b>Course 1:</b></p>
                                        <p class="w-100 text-wrap"><?= e($line->course_1) ?></p>
                                        <p style="margin-bottom: 0;"><b>Course 2:</b></p>
                                        <p class="w-100 text-wrap"><?= e($line->course_2) ?></p>
                                        <p style="margin-bottom: 0;"><b>Course 3:</b></p>
                                        <p class="w-100 text-wrap" style="margin-bottom: 0;"><?= e($line->course_3) ?></p>
                                        <?php
                                            } else {
                                        ?>
                                        <p style="color: red; font-style: italic;">No courses available</p>
                                        <?php
                                            }
                                        ?>
                                    </center>
                                </td>
                                <td>
                                    <div class="w-100 text-wrap">
                                        <?= e($line->duration) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="w-100 text-wrap">
                                        <?php 
                                        if(!empty($line->credit_unit)){
                                            echo e($line->credit_unit);
                                        }else{
                                        ?>
                                        <p style="color: red; font-style: italic;">Not Applicable</p>
                                        <?php
                                        } 
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="w-100 text-wrap">
                                        <?= e($line->developer) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-<?= strtolower($line->status) ?>"><?= $line->status ?></span>
                                </td>
                                <td>
                                    <center>
                                        <?php
                                            $image_data     = base64_encode($line->img);
                                            $image_src      = "data:{$line->img_type};base64,{$image_data}";
                                        ?>
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                                            onclick="editItem({
                                                fetch_file: 'fetch/fetch-micro-course.php', 
                                                item_id: '<?= encrypt_data($line->sub_prog_id) ?>', 
                                                custom_function: () => fetchCustom('<?= $image_src ?>')
                                            })">
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
            <form action="controller/ctr-micro-course.php" method="POST" id="form_validation">
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

<template id="policy-template">
    <div class="row">
        <div class="col-lg-11 col-md-10 col-12 mb-2">
            <textarea class="form-control auto-grow-textarea policy" id="policy" name="policy" rows="3" placeholder="Policy"></textarea>
        </div>
        <div class="col-lg-1 col-md-2 col-12 mb-2">
            <button type="button" class="btn btn-danger w-100" onclick="removeOldItem(this, policyObj)"><i class="mdi mdi-close"></i></button>
        </div>
    </div>
</template>

<script>
const policyObj = {
    fieldMap: {
        "textarea.policy": "policy"
    },
    maxItems: <?= $_SESSION['proms-admin']['max_policy'] ?>,
    templateId: "policy-template",
    confirmTitle: "Are you sure you want to delete this policy?"
};

function fetchCustom(img_src){
    initFilePond(
        'img_input',
        ['image/jpeg'],
        ['#save_changes'],
        img_src
    );
}
</script>

<?php
include '../footer.php';
?>