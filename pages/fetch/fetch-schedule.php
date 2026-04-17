<?php
include '../../includes/init.php';
$db = DB::getInstance();

if (isset($_POST['id'])) {
    $id = decrypt_data($_POST['id']);
    $schedule = $db->queryUniqueObject('SELECT * FROM tbl_schedule WHERE sched_id = :sched_id', ['sched_id' => $id]);
    if ($schedule) {
        $sched_id       = encrypt_data($schedule->sched_id);
        $sched_date     = e($schedule->sched_date);
        $slot_count     = e($schedule->slot_count);
        $status         = e($schedule->status);
    }
}
?>
<input type="hidden" name="sched_id" value="<?= $sched_id ?? '' ?>">

<div class="row">
    <div class="col-md-12">
        <label for="sched_date" class="form-label required">Date</label>
        <input type="date" class="form-control" id="sched_date" name="sched_date" value="<?= $sched_date ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <label for="slot_count" class="form-label required">Total Slots Available</label>
        <input type="number" id="slot_count" name="slot_count" class="form-control" placeholder="Total Slots Available" value="<?= $slot_count ?? '' ?>">
    </div>
    <div class="col-sm-6">
        <label for="status" class="form-label required">Status</label>
        <select class="form-control select2" data-toggle="select2" name="status" data-placeholder="Select Status">
            <option value="<?= $status ?? '' ?>" selected>
                <?= !empty($status) ? $status : '' ?>
            </option>
            <?php
                if($status != "Active") {
            ?>
                <option value="Active">Active</option>
            <?php
                }
                if($status != "Inactive") {
            ?>
                <option value="Inactive">Inactive</option>
            <?php
                }
            ?>
        </select>
    </div>
</div>
