<?php
include '../includes/init.php';
include '../header.php';
$db = DB::getInstance();

setActiveLink('program.php');

$_SESSION['max_offering'] = 10;
$_SESSION['max_level'] = 10;
$_SESSION['max_duration'] = 10;
$_SESSION['max_mode'] = 10;
$_SESSION['max_note'] = 10;
?>

<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                        onclick="addItem({fetch_name: 'fetch-foreign-lang', custom_function: fetchCustom})">
                        <i class="mdi mdi-plus"></i>
                        <span class="add-btn-name">Add Sub-program</span>
                    </button>
                </div>
                <h4 class="page-title">SUB-PROGRAMS (<?= $db->queryUniqueValue("SELECT prog_name FROM tbl_program WHERE prog_id = 2") ?>)</h4>
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
                                <th>Offerings</th>
                                <th>Levels</th>
                                <th>Duration</th>
                                <th>Mode</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sub_program_query = $db->query("SELECT * FROM tbl_foreign_lang");

                                while ($line = $db->fetchNextObject($sub_program_query)) {
                            ?>
                            <tr>
                                <td>
                                    <div class="w-100 text-wrap">
                                        <?= e($line->title) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                        $offerings = json_decode($line->offering, true) ?? [];

                                        if(!empty($offerings)){
                                    ?>
                                        <ul>
                                    <?php
                                            foreach ($offerings as $offer) {
                                    ?>
                                        <li class="w-100 text-wrap"><?= e($offer) ?></li>
                                    <?php
                                            }
                                    ?>                                        
                                        </ul>
                                    <?php
                                        }else{
                                    ?>
                                        <p style="color: red; font-style: italic;">No Offering Available</p>
                                    <?php
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        $levels = json_decode($line->level, true) ?? [];

                                        if(!empty($levels)){
                                    ?>
                                        <ul>
                                    <?php
                                            foreach ($levels as $level) {
                                    ?>
                                        <li class="w-100 text-wrap"><?= e($level) ?></li>
                                    <?php
                                            }
                                    ?>                                        
                                        </ul>
                                    <?php
                                        }else{
                                    ?>
                                        <p style="color: red; font-style: italic;">No Levels Available</p>
                                    <?php
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        $durations = json_decode($line->duration, true) ?? [];

                                        if(!empty($durations)){
                                    ?>
                                        <ul>
                                    <?php
                                            foreach ($durations as $duration) {
                                    ?>
                                        <li class="w-100 text-wrap"><?= e($duration) ?></li>
                                    <?php
                                            }
                                    ?>                                        
                                        </ul>
                                    <?php
                                        }else{
                                    ?>
                                        <p style="color: red; font-style: italic;">No Duration Available</p>
                                    <?php
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        $modes = json_decode($line->mode, true) ?? [];

                                        if(!empty($modes)){
                                    ?>
                                        <ul>
                                    <?php
                                            foreach ($modes as $mode) {
                                    ?>
                                        <li class="w-100 text-wrap"><?= e($mode) ?></li>
                                    <?php
                                            }
                                    ?>                                        
                                        </ul>
                                    <?php
                                        }else{
                                    ?>
                                        <p style="color: red; font-style: italic;">No Mode Available</p>
                                    <?php
                                        }
                                    ?>
                                </td>
                                <td>
                                    <span class="status-<?= strtolower($line->status) ?>"><?= $line->status ?></span>
                                </td>
                                <td>
                                    <center>
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                                            onclick="editItem({fetch_name: 'fetch-foreign-lang', item_id: '<?= encrypt_data($line->fl_id) ?>', custom_function: fetchCustom})">
                                            <i class="mdi mdi-square-edit-outline"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger ms-1" onclick="deleteItem('<?= encrypt_data($line->fl_id) ?>')">
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
            <form action="controller/ctr-foreign-lang.php" method="POST" id="form_validation">
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

<template id="offering-template">
    <div class="row">
        <div class="col-lg-11 col-md-10 col-12 mb-2">
            <input class="form-control offering" id="offering" name="offering" placeholder="Offering">
        </div>
        <div class="col-lg-1 col-md-2 col-12 mb-2">
            <button type="button" class="btn btn-danger w-100" data-remove-item><i class="mdi mdi-close"></i></button>
        </div>
    </div>
</template>

<template id="level-template">
    <div class="row">
        <div class="col-lg-11 col-md-10 col-12 mb-2">
            <input class="form-control level" id="level" name="level" placeholder="Level">
        </div>
        <div class="col-lg-1 col-md-2 col-12 mb-2">
            <button type="button" class="btn btn-danger w-100" data-remove-item><i class="mdi mdi-close"></i></button>
        </div>
    </div>
</template>

<template id="duration-template">
    <div class="row">
        <div class="col-lg-11 col-md-10 col-12 mb-2">
            <input class="form-control duration" id="duration" name="duration" placeholder="Duration">
        </div>
        <div class="col-lg-1 col-md-2 col-12 mb-2">
            <button type="button" class="btn btn-danger w-100" data-remove-item><i class="mdi mdi-close"></i></button>
        </div>
    </div>
</template>

<template id="mode-template">
    <div class="row">
        <div class="col-lg-11 col-md-10 col-12 mb-2">
            <input class="form-control mode" id="mode" name="mode" placeholder="Mode">
        </div>
        <div class="col-lg-1 col-md-2 col-12 mb-2">
            <button type="button" class="btn btn-danger w-100" data-remove-item><i class="mdi mdi-close"></i></button>
        </div>
    </div>
</template>

<template id="note-template">
    <div class="row">
        <div class="col-lg-11 col-md-10 col-12 mb-2">
            <input class="form-control note" id="note" name="note" placeholder="Note">
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

        templateId: "offering-template",
        containerId: "offering-container",
        addBtnId: "add-offering-btn",
        addBtnContainerId: "add-offering-btn-container",
        itemSelector: ".row",
        maxItems: <?= $_SESSION['max_offering'] ?>,
        confirmTitle: "Are you sure you want to delete this offering?",

        fieldMap: {
            "input.offering": "offering"
        },

        afterAdd: container => reInitUI($(container))

    });

    createDynamicList({

        templateId: "level-template",
        containerId: "level-container",
        addBtnId: "add-level-btn",
        addBtnContainerId: "add-level-btn-container",
        itemSelector: ".row",
        maxItems: <?= $_SESSION['max_level'] ?>,
        confirmTitle: "Are you sure you want to delete this level?",

        fieldMap: {
            "input.level": "level"
        },

        afterAdd: container => reInitUI($(container))

    });

    createDynamicList({

        templateId: "duration-template",
        containerId: "duration-container",
        addBtnId: "add-duration-btn",
        addBtnContainerId: "add-duration-btn-container",
        itemSelector: ".row",
        maxItems: <?= $_SESSION['max_duration'] ?>,
        confirmTitle: "Are you sure you want to delete this duration?",

        fieldMap: {
            "input.duration": "duration"
        },

        afterAdd: container => reInitUI($(container))

    });

    createDynamicList({

        templateId: "mode-template",
        containerId: "mode-container",
        addBtnId: "add-mode-btn",
        addBtnContainerId: "add-mode-btn-container",
        itemSelector: ".row",
        maxItems: <?= $_SESSION['max_mode'] ?>,
        confirmTitle: "Are you sure you want to delete this mode?",

        fieldMap: {
            "input.mode": "mode"
        },

        afterAdd: container => reInitUI($(container))

    });

    createDynamicList({

        templateId: "note-template",
        containerId: "note-container",
        addBtnId: "add-note-btn",
        addBtnContainerId: "add-note-btn-container",
        itemSelector: ".row",
        maxItems: <?= $_SESSION['max_note'] ?>,
        confirmTitle: "Are you sure you want to delete this note?",

        fieldMap: {
            "input.note": "note"
        },

        afterAdd: container => reInitUI($(container))

    });
    
}
</script>
<?php
include '../footer.php';
?>