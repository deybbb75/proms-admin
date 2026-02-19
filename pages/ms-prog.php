<?php
include '../includes/init.php';
include '../header.php';
$db = DB::getInstance();

setActiveLink('program.php');

$_SESSION['max_cert'] = 5;
$_SESSION['max_ctg'] = 5;
$_SESSION['max_associate_cert'] = 10;
$_SESSION['max_expert_cert'] = 10;
?>
<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                        onclick="addItem({fetch_file: 'fetch/fetch-ms-prog.php', custom_function: fetchCustom})">
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
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sub_program_query = $db->query("SELECT * FROM tbl_ms_prog");

                                while ($line = $db->fetchNextObject($sub_program_query)) {
                            ?>
                            <tr>
                                <td><?= e($line->title) ?></td>
                                <td style="white-space: pre-line;"><?= e(truncateText($line->description)) ?></td>
                                <td>
                                    <span class="status-<?= strtolower($line->status) ?>"><?= $line->status ?></span>
                                </td>
                                <td>
                                    <center>
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                                            onclick="editItem({fetch_file: 'fetch/fetch-ms-prog.php', item_id: '<?= encrypt_data($line->mp_id) ?>', custom_function: fetchCustom})">
                                            <i class="mdi mdi-square-edit-outline"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger ms-1" onclick="deleteItem('<?= encrypt_data($line->mp_id) ?>')">
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
            <form action="controller/ctr-ms-prog.php" method="POST" id="form_validation">
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


<template id="cert-template">
    <div class="row main-item">
        <div class="col-10 mb-2">
            <textarea class="form-control auto-grow-textarea cert" id="cert" name="cert[0][title]" rows="3" placeholder="Certification"></textarea>
            <div class="row mt-2">
                <label class="form-label required" style="font-weight: 600;">Categories</label>
                <div class="col-sm-12" style="padding-bottom: 0px;">
                    <div class="row sub-item">
                        <div class="col-lg-12">
                            <textarea class="form-control auto-grow-textarea title" id="title" name="cert[0][ctg][0][title]" rows="3" placeholder="Category Title"></textarea>
                        </div>
                        <div class="col-lg-12">
                            <textarea class="form-control auto-grow-textarea desc" id="desc" name="cert[0][ctg][0][desc]" rows="3" placeholder="Description"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-12">
                    <button type="button" class="btn btn-sm btn-info w-100" onclick="addNewItem(this, ctgObj)"><i class="mdi mdi-plus"></i> Add Category</button>
                </div>
            </div>
        </div>
        <div class="col-2 mb-2">
            <button type="button" class="btn btn-danger w-100" onclick="removeOldItem(this, certObj)"><i class="mdi mdi-close"></i></button>
        </div>
    </div>
</template>

<template id="ctg-template">
    <div class="row sub-item">
        <div class="col-10 mb-2">
            <textarea class="form-control auto-grow-textarea title" id="title" name="cert[0][ctg][0][title]" rows="3" placeholder="Category Title"></textarea>
        </div>
        <div class="col-2 mb-2">
            <button type="button" class="btn btn-danger w-100" onclick="removeOldItem(this, ctgObj)"><i class="mdi mdi-close"></i></button>
        </div>
        <div class="col-12 mb-2">
            <textarea class="form-control auto-grow-textarea desc" id="desc" name="cert[0][ctg][0][desc]" rows="3" placeholder="Category Description"></textarea>
        </div>
    </div>
</template>

<template id="associate-cert-template">
    <div class="row">
        <div class="col-lg-11 col-md-10 col-12 mb-2">
            <textarea class="form-control auto-grow-textarea associate_cert" id="associate_cert" name="associate_cert[0]" rows="3" placeholder="Associate Certification"></textarea>
        </div>
        <div class="col-lg-1 col-md-2 col-12 mb-2">
            <button type="button" class="btn btn-danger w-100" onclick="removeOldItem(this, associateCertObj)"><i class="mdi mdi-close"></i></button>
        </div>
    </div>
</template>

<template id="expert-cert-template">
    <div class="row">
        <div class="col-lg-11 col-md-10 col-12 mb-2">
            <textarea class="form-control auto-grow-textarea expert_cert" id="expert_cert" name="expert_cert[0]" rows="3" placeholder="Expert Certification"></textarea>
        </div>
        <div class="col-lg-1 col-md-2 col-12 mb-2">
            <button type="button" class="btn btn-danger w-100" onclick="removeOldItem(this, expertCertObj)"><i class="mdi mdi-close"></i></button>
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

function updateCert(action){
    const cert_input_section = document.getElementById('cert-upload');
    const preview = document.getElementById('cert-preview');

    if(action === 'update'){
        cert_input_section.style.display = 'block';
        preview.style.display = 'none';
    }else{
        cert_input_section.style.display = 'none';
        preview.style.display = 'block';
    }
}

const certObj = {
    fieldMap: {
        "textarea.cert": "cert[0][title]",
        "textarea.title": "cert[0][ctg][0][title]",
        "textarea.desc": "cert[0][ctg][0][desc]"
    },
    maxItems: <?= $_SESSION['max_cert'] ?>,
    templateId: "cert-template",
    confirmTitle: "Are you sure you want to delete this certification?",
    itemSelector: '.main-item'
};

const ctgObj = {
    fieldMap: {
        "textarea.title": "[ctg][0][title]",
        "textarea.desc": "[ctg][0][desc]"
    },
    maxItems: <?= $_SESSION['max_ctg'] ?>,
    templateId: "ctg-template",
    confirmTitle: "Are you sure you want to delete this category?",
    itemSelector: '.sub-item'
};

const associateCertObj = {
    fieldMap: {
        "textarea.associate_cert": "associate_cert"
    },
    maxItems: <?= $_SESSION['max_associate_cert'] ?>,
    templateId: "associate-cert-template",
    confirmTitle: "Are you sure you want to delete this associate certification?"
};

const expertCertObj = {
    fieldMap: {
        "textarea.expert_cert": "expert_cert"
    },
    maxItems: <?= $_SESSION['max_expert_cert'] ?>,
    templateId: "expert-cert-template",
    confirmTitle: "Are you sure you want to delete this expert certification?"
};

function fetchCustom(){
    initFilePond(
        'img_input',
        ['image/jpeg'],
        'Only JPG files are allowed',
        ['#save_changes']
    );

    initFilePond(
        'cert_input',
        ['image/jpeg'],
        'Only JPG files are allowed',
        ['#save_changes']
    );
}



</script>