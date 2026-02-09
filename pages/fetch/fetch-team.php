<?php
include '../../includes/init.php';
$db = DB::getInstance();

$sched_count = 0;

if (isset($_POST['id'])) {
    $id = decrypt_data($_POST['id']);
    $member = $db->queryUniqueObject('SELECT * FROM tbl_member WHERE member_id = :member_id', ['member_id' => $id]);
    if ($member) {
        $member_id = encrypt_data($member->member_id);
        $member_name  = $member->member_name;
        $position  = $member->position;
        $status = $member->status;
        $image = $member->member_img;
        $image_data = base64_encode($image);
        $image_type = $member->member_img_type;
        $image_src  = "data:{$image_type};base64,{$image_data}";
    }
}
?>
<input type="hidden" name="member_id" value="<?= $member_id ?? '' ?>">

<div class="row">
    <div class="col-md-12">
        <label for="member_name" class="form-label required">Member Name</label>
        <input type="text" id="member_name" name="member_name" class="form-control" placeholder="Member Name" value="<?= $member_name ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <label for="position" class="form-label required">Position</label>
        <input type="text" id="position" name="position" class="form-control" placeholder="Position" value="<?= $position ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <label for="status" class="form-label required">Status</label>
        <select class="form-control select2" data-toggle="select2" name ="status" data-placeholder="Select Status">
            <option value="<?= $status ?? '' ?>" <?php if(empty($status)) echo 'disabled'; ?> selected>
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
        <div id="image-preview" style="display: <?= !empty($image) ? 'block' : 'none' ?>;">
            <img 
                src="<?= $image_src ?? '' ?>" 
                class="img-fluid mx-auto d-block mt-2"
                style="max-width: 100%; height: auto; border-radius: 20px;"
                alt=""
            >
        <?php
            if(!empty($image)) {
        ?>
            <button type="button" class="btn btn-sm btn-info w-100 mt-2" onclick="updateImage('update')">Update Image</button>
        <?php
            }
        ?>
        </div>
        <div id="image-upload" style="display: <?= !empty($image) ? 'none' : 'block' ?>;">
            <input type="file" id="img_input" class="filepond" name="img_input" data-max-file-size="10MB" data-max-files="3" />
            <span class="font-10 text-muted"><b>Note: </b>Please upload an image in <b>JPG</b> format. The file size must not exceed <b>10 MB</b>.</span>
            <?php
                if(!empty($image)){
            ?>
                <button type="button" class="btn btn-sm btn-danger w-100 mt-2" onclick="updateImage('cancel')">Cancel</button>
            <?php
                }
            ?>
        </div>
        
    </div>
</div>
