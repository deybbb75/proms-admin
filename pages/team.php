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
                        onclick="addItem({fetch_name: 'fetch-team', custom_function: fetchCustom})">
                        <i class="mdi mdi-plus"></i>
                        <span class="add-btn-name">Add Member</span>
                    </button>
                </div>
                <h4 class="page-title">OUR TEAM</h4>
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
                                <th>Member Name</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $team_query = $db->query("SELECT * FROM tbl_member");

                                while ($line = $db->fetchNextObject($team_query)) {
                            ?>
                            <tr>
                                <td><?= e($line->member_name) ?></td>
                                <td><?= e(truncateText($line->position)) ?></td>
                                <td>
                                    <span class="status-<?= strtolower($line->status) ?>"><?= e($line->status) ?></span>
                                </td>
                                <td>
                                    <center>
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                                            onclick="editItem({fetch_name: 'fetch-team', item_id: '<?= encrypt_data($line->member_id) ?>', custom_function: fetchCustom})"><i class="mdi mdi-square-edit-outline"></i>
                                        </button>
                                        <?php
                                            if($line->member_id != 1){ // Prevent deletion of default admin
                                        ?>
                                        <button type="button" class="btn btn-danger ms-1" onclick="deleteItem('<?= encrypt_data($line->member_id) ?>')">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                        <?php
                                            }
                                        ?>
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
        </div><!-- end col-->
    </div> <!-- end row-->

</div>
<!-- container -->

<div id="primary-header-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="primary-header-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="controller/ctr-team.php" method="POST" id="form_validation">
                <div class="modal-header modal-colored-header bg-primary">
                    <h4 class="modal-title" id="primary-header-modalLabel">Member Details</h4>
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
}
</script>
<?php
include '../footer.php';
?>