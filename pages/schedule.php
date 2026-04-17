<?php
include '../includes/init.php';
include '../header.php';
$db = DB::getInstance();

$sub_prog_name = e($db->queryUniqueValue("SELECT title FROM tbl_assess_cert WHERE sub_prog_id = :sub_prog_id", ["sub_prog_id" => $_SESSION['proms-admin']['sub_prog_id']]));
?>
<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                        onclick="addItem({fetch_file: 'fetch/fetch-schedule.php'})">
                        <i class="mdi mdi-plus"></i>
                        <span class="add-btn-name">Add Schedule</span>
                    </button>
                </div>
                <h4 class="page-title"><?= strtoupper($sub_prog_name) ?> SCHEDULES</h4>
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
                                <th>Date</th>
                                <th>Total Slots Available</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sched_query = $db->query("SELECT * FROM tbl_schedule");

                                while ($line = $db->fetchNextObject($sched_query)) {
                            ?>
                            <tr>
                                <td><?= date("F j, Y", strtotime(e($line->sched_date))); ?></td>
                                <td><?= e($line->slot_count) ?></td>
                                <td>
                                    <span class="status-<?= strtolower($line->status) ?>"><?= e($line->status) ?></span>
                                </td>
                                <td>
                                    <center>
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#primary-header-modal"
                                            onclick="editItem({
                                                fetch_file: 'fetch/fetch-schedule.php', 
                                                item_id: '<?= encrypt_data($line->sched_id) ?>',
                                            })">
                                            <i class="mdi mdi-square-edit-outline"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger ms-1" onclick="deleteItem('<?= encrypt_data($line->sched_id) ?>')">
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
            <button type="button" class="btn btn-warning mt-1 mb-3 w-100" onclick="window.location.href='assess-cert.php'">
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
            <form action="controller/ctr-schedule.php" method="POST" id="form_validation">
                <div class="modal-header modal-colored-header bg-primary">
                    <h4 class="modal-title" id="primary-header-modalLabel">Schedule Details</h4>
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