<?php
include '../../includes/init.php';
$db = DB::getInstance();

if (isset($_POST['id'])) {
    $id = decrypt_data($_POST['id']);
    $program = $db->queryUniqueObject('SELECT * FROM tbl_assess_cert WHERE sub_prog_id = :sub_prog_id', ['sub_prog_id' => $id]);
    if ($program) {
        $sub_prog_id          = encrypt_data($program->sub_prog_id);
        $title          = e($program->title);
        $description    = e($program->description);
        $main_fee       = e(number_format($program->main_fee, 2, '.', ','));
        $sub_fee        = e(number_format($program->sub_fee, 2, '.', ','));
        $requirement    = json_decode($program->requirement, true);
        $status         = e($program->status);
        $image          = $program->img;
        $image_data     = base64_encode($image);
        $image_type     = $program->img_type;
        $image_src      = "data:{$image_type};base64,{$image_data}";
    }
}
?>
<input type="hidden" name="sub_prog_id" value="<?= $sub_prog_id ?? '' ?>">

<div class="row">
    <div class="col-md-12">
        <label for="title" class="form-label required">Title</label>
        <input type="text" id="title" name="title" class="form-control" placeholder="Title" value="<?= $title ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="description" class="form-label required">Description</label>
        <textarea class="form-control auto-grow-textarea" id="description" name="description" rows="5" placeholder="Description"><?= $description ?? '' ?></textarea>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <label for="main_fee" class="form-label required">Assessment Fee</label>
        <input type="text" id="main_fee" name="main_fee" class="form-control" data-toggle="input-mask" placeholder="Assessment Fee" value="<?= $main_fee ?? '' ?>"
            data-mask-format="000,000,000,000,000.00" data-reverse="true">
    </div>
    <div class="col-sm-6">
        <label for="sub_fee" class="form-label required">Processing Fee</label>
        <input type="text" id="sub_fee" name="sub_fee" class="form-control" data-toggle="input-mask" placeholder="Processing Fee" value="<?= $sub_fee ?? '' ?>"
            data-mask-format="000,000,000,000,000.00" data-reverse="true">
    </div>
</div>
<div class="row">
    <label class="form-label required" style="font-weight: 600;">Requirement/s</label>
    <div class="col-sm-12" style="padding-bottom: 0px;">
        <?php
            if(!isset($requirement)){
        ?>
        <div class="row">
            <div class="col-lg-12">
                <textarea class="form-control auto-grow-textarea requirement" id="requirement" name="requirement[0]" rows="3" placeholder="Requirement"></textarea>
            </div>
        </div>

        <?php
            }else{
                for ($i = 0; $i < count($requirement); $i++) {
        ?>
        <div class="row">
            <div class="col-lg-11 mb-2">
                <textarea class="form-control auto-grow-textarea requirement" id="requirement" name="requirement[<?= $i ?>]" rows="3" placeholder="Requirement"><?= e($requirement[$i]) ?? '' ?></textarea>
            </div>
            <div class="col-lg-1 mb-2">
                <button type="button" class="btn btn-danger w-100" onclick="removeOldItem(this, reqObj)"><i class="mdi mdi-close"></i></button>
            </div>
        </div>
        <?php
                }
            }
        ?>
    </div>
    
    <div class="col-sm-12">
        <button type="button" class="btn btn-sm btn-info w-100" onclick="addNewItem(this, reqObj)"><i class="mdi mdi-plus"></i> Add Requirement</button>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <label for="status" class="form-label required">Status</label>
        <select class="form-control select2" data-toggle="select2" name ="status" data-placeholder="Select Status">
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
