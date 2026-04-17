<?php
include '../../includes/init.php';
$db = DB::getInstance();

if (isset($_POST['id'])) {
    $id = decrypt_data($_POST['id']);
    $banner = $db->queryUniqueObject('SELECT * FROM tbl_banner WHERE banner_id = :banner_id', ['banner_id' => $id]);
    if ($banner) {
        $banner_id          = encrypt_data($banner->banner_id);
        $image          = $banner->img;
        $image_data     = base64_encode($image);
        $image_type     = $banner->img_type;
        $image_src      = "data:{$image_type};base64,{$image_data}";
        $status         = $banner->status;
    }
}
?>
<input type="hidden" name="banner_id" value="<?= $banner_id ?? '' ?>">

<div class="row">
    <div class="col-md-12">
        <label for="img" class="form-label required">Image</label>
        <input type="file" id="img_input" class="filepond" name="img_input" data-max-file-size="10MB"/>
        <span class="font-10 text-muted"><b>Note: </b>Please upload an image in <b>JPG</b> format. The file size must not exceed <b>10 MB</b>.</span>
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
