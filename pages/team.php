<?php
include '../includes/init.php';
include '../header.php';
$db = DB::getInstance();

$_SESSION['proms-admin']['max_schedule'] = 3;
?>
<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                        onclick="addItem({fetch_file: 'fetch/fetch-team.php', custom_function: () => fetchCustom()})">
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
                    <table id="team-datatable" class="table table-striped dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>Member Name</th>
                                <th>Position</th>
                                <th>Order</th>
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
                                <td><?= e($line->name) ?></td>
                                <td><?= e(truncateText($line->position)) ?></td>
                                <td><?= e($line->member_order) ?></td>
                                <td>
                                    <span class="status-<?= strtolower($line->status) ?>"><?= e($line->status) ?></span>
                                </td>
                                <td>
                                    <center>
                                        <?php
                                            $image_data     = base64_encode($line->img);
                                            $image_src      = "data:{$line->img_type};base64,{$image_data}";
                                        ?>
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#primary-header-modal" id="editBtn"
                                            onclick="editItem({
                                                fetch_file: 'fetch/fetch-team.php', 
                                                item_id: '<?= encrypt_data($line->member_id) ?>', 
                                                custom_function: () => fetchCustom('<?= $image_src ?>')
                                            })"
                                            ><i class="mdi mdi-square-edit-outline"></i>
                                        </button>
                                        <?php
                                            if($line->member_order != 1){ // Prevent deletion of default admin
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

<script>
    $("#team-datatable").DataTable({
        keys: true,
        order: [[2, "asc"]], // 👈 add this
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            }
        },
        drawCallback: function () {
            $(".dataTables_paginate > .pagination")
                .addClass("pagination-rounded");
        }
    });
</script>