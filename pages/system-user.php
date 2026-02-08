<?php
include '../includes/init.php';
include '../header.php';
$db = DB::getInstance();
?>
<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                        onclick="addItem({fetch_name: 'fetch-system-user'})">
                        <i class="mdi mdi-plus"></i>
                        <span class="add-btn-name">Add System User</span>
                    </button>
                </div>
                <h4 class="page-title">SYSTEM USERS</h4>
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
                                        <th>Employee Number</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $system_users_query = $db->query("SELECT * FROM tbl_system_user");

                                        while ($line = $db->fetchNextObject($system_users_query)) {
                                    ?>
                                    <tr>
                                        <td><?= $line->sys_id ?></td>
                                        <td><?= $line->emp_no ?></td>
                                        <td><?= $line->fname ?> <?= $line->mname ?> <?= $line->lname ?></td>
                                        <td><?= $line->email ?></td>
                                        <td><?= $line->role ?></td>
                                        <td>
                                            <span class="status-<?= strtolower($line->status) ?>"><?= $line->status ?></span>
                                        </td>
                                        <td>
                                            <center>
                                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                                                    onclick="editItem({fetch_name: 'fetch-system-user', item_id: '<?= encrypt_data($line->sys_id) ?>'})"><i class="mdi mdi-square-edit-outline"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger ms-1" onclick="deleteItem('<?= encrypt_data($line->sys_id) ?>')">
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
                <input type="hidden" id="action" value="">
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
include '../footer.php';
?>