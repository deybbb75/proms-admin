<?php
include '../../includes/init.php';
$db = DB::getInstance();

if (isset($_POST['id'])) {
    $id = decrypt_data($_POST['id']);
    $banner = $db->queryUniqueObject('SELECT * FROM tbl_banner WHERE banner_id = :banner_id', ['banner_id' => $id]);
    if ($program) {
        $banner_id          = encrypt_data($program->banner_id);
        $image          = $program->img;
        $image_data     = base64_encode($image);
        $image_type     = $program->img_type;
        $image_src      = "data:{$image_type};base64,{$image_data}";
        $status         = $program->status;
    }
}
?>
<input type="hidden" name="banner_id" value="<?= $banner_id ?? '' ?>">

<div class="row">
    <div class="col-md-12">
        <label for="img" class="form-label required">Image</label>

        <?php
            if(!empty($image)){
        ?>
        <div id="image-preview">
            <img 
                src="<?= $image_src ?? '' ?>" 
                class="img-fluid mx-auto d-block mt-2"
                style="max-width: 100%; height: auto; border-radius: 20px;"
                alt=""
            >
        </div>
        <?php
            }else{
        ?>
        <div id="image-upload">
            <input type="file" id="img_input" class="filepond" name="img_input" data-max-file-size="10MB" data-max-files="3" />
        </div>
        <?php
            }
        ?>
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
