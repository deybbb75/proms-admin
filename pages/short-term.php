<?php
include '../includes/init.php';
include '../header.php';
$db = DB::getInstance();

setActiveLink('program.php');

$_SESSION['max_objective'] = 10;
$_SESSION['max_outline'] = 10;
?>

<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                        onclick="addItem({fetch_name: 'fetch-short-term', custom_function: fetchCustom})">
                        <i class="mdi mdi-plus"></i>
                        <span class="add-btn-name">Add Sub-program</span>
                    </button>
                </div>
                <h4 class="page-title">SUB-PROGRAMS (<?= $db->queryUniqueValue("SELECT prog_name FROM tbl_program WHERE prog_id = 4") ?>)</h4>
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
                                <th>Training/Course Title</th>
                                <th>Description</th>
                                <th>Venue</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sub_program_query = $db->query("SELECT * FROM tbl_short_term");

                                while ($line = $db->fetchNextObject($sub_program_query)) {
                            ?>
                            <tr>
                                <td>
                                    <div class="w-100 text-wrap">
                                        <?= e($line->prog_title) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="w-100 text-wrap">
                                        <?= e($line->training_title) ?>
                                    </div>
                                </td>
                                <td style="white-space: pre-line;"><?= e(truncateText($line->description)) ?></td>
                                <td>
                                    <div class="w-100 text-wrap">
                                        <?= e($line->venue) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-<?= strtolower($line->status) ?>"><?= $line->status ?></span>
                                </td>
                                <td>
                                    <center>
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                                            onclick="editItem({fetch_name: 'fetch-short-term', item_id: '<?= encrypt_data($line->st_id) ?>', custom_function: fetchCustom})">
                                            <i class="mdi mdi-square-edit-outline"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger ms-1" onclick="deleteItem('<?= encrypt_data($line->st_id) ?>')">
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
            <form action="controller/ctr-short-term.php" method="POST" id="form_validation">
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

<template id="objective-template">
    <div class="row">
        <div class="col-lg-11 col-md-10 col-12 mb-2">
            <textarea class="form-control auto-grow-textarea objective" id="objective" name="objective" rows="3" placeholder="Objective"></textarea>
        </div>
        <div class="col-lg-1 col-md-2 col-12 mb-2">
            <button type="button" class="btn btn-danger w-100" onclick="removeObjective(this)"><i class="mdi mdi-close"></i></button>
        </div>
    </div>
</template>

<template id="outline-template">
    <div class="row">
        <div class="col-lg-11 col-md-10 col-12 mb-2">
            <textarea class="form-control auto-grow-textarea outline" id="outline" name="outline" rows="3" placeholder="Outline"></textarea>
        </div>
        <div class="col-lg-1 col-md-2 col-12 mb-2">
            <button type="button" class="btn btn-danger w-100" data-remove-item><i class="mdi mdi-close"></i></button>
        </div>
    </div>
</template>

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

function fetchCustom(){
    initFilePond(
        'img_input',
        ['image/jpeg'],
        'Only JPG files are allowed',
        ['#save_changes']
    );

    createDynamicList({

        templateId: "objective-template",
        containerId: "objective-container",
        addBtnId: "add-objective-btn",
        addBtnContainerId: "add-objective-btn-container",
        itemSelector: ".row",
        maxItems: <?= $_SESSION['max_objective'] ?>,
        confirmTitle: "Are you sure you want to delete this objective?",

        fieldMap: {
            "textarea.objective": "objective"
        },

        afterAdd: container => reInitUI($(container))

    });

    createDynamicList({

        templateId: "outline-template",
        containerId: "outline-container",
        addBtnId: "add-outline-btn",
        addBtnContainerId: "add-outline-btn-container",
        itemSelector: ".row",
        maxItems: <?= $_SESSION['max_outline'] ?>,
        confirmTitle: "Are you sure you want to delete this outline?",

        fieldMap: {
            "textarea.outline": "outline"
        },

        afterAdd: container => reInitUI($(container))

    });
}
</script>
<?php
include '../footer.php';
?>