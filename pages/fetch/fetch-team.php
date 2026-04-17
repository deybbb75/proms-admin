<?php
include '../../includes/init.php';
$db = DB::getInstance();

$sched_count = 0;

if (isset($_POST['id'])) {
    $id = decrypt_data($_POST['id']);
    $member = $db->queryUniqueObject('SELECT * FROM tbl_member WHERE member_id = :member_id', ['member_id' => $id]);
    if ($member) {
        $member_id      = encrypt_data($member->member_id);
        $name           = e($member->name);
        $position       = e($member->position);
        $member_order   = e($member->member_order);
        $status         = e($member->status);
        $image          = $member->img;
        $image_data     = base64_encode($image);
        $image_type     = $member->img_type;
        $image_src      = "data:{$image_type};base64,{$image_data}";
    }
}
?>
<input type="hidden" name="member_id" value="<?= $member_id ?? '' ?>">

<div class="row">
    <div class="col-md-12">
        <label for="name" class="form-label required">Member Name</label>
        <input type="text" id="name" name="name" class="form-control" placeholder="Member Name" value="<?= $name ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <label for="position" class="form-label required">Position</label>
        <input type="text" id="position" name="position" class="form-control" placeholder="Position" value="<?= $position ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="member_order" class="form-label required">Order</label>
        <input type="number" id="member_order" name="member_order" class="form-control" placeholder="Order" min="1" step="1" value="<?= $member_order ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
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
<div class="row">
    <div class="col-md-12">
        <label for="img" class="form-label required">Image</label>
        <input type="file" id="img_input" class="filepond" name="img_input" data-max-file-size="10MB"/>
        <span class="font-10 text-muted"><b>Note: </b>Please upload an image in <b>JPG</b> format. The file size must not exceed <b>10 MB</b>.</span>
    </div>
</div>