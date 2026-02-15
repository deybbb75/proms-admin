<?php
include '../../includes/init.php';
$db = DB::getInstance();

if (isset($_POST['id'])) {
    $id = decrypt_data($_POST['id']);
    $program = $db->queryUniqueObject('SELECT * FROM tbl_foreign_lang WHERE fl_id = :fl_id', ['fl_id' => $id]);
    if ($program) {
        $fl_id          = encrypt_data($program->fl_id);
        $title          = $program->title;
        $offering       = json_decode($program->offering, true);
        $level          = json_decode($program->level, true);
        $duration       = json_decode($program->duration, true);
        $mode           = json_decode($program->mode, true);
        $note           = json_decode($program->note, true);
        $status         = $program->status;
        $image          = $program->img;
        $image_data     = base64_encode($image);
        $image_type     = $program->img_type;
        $image_src      = "data:{$image_type};base64,{$image_data}";
    }
}
?>
<input type="hidden" name="fl_id" value="<?= $fl_id ?? '' ?>">

<div class="row">
    <div class="col-md-12">
        <label for="title" class="form-label required">Title</label>
        <input type="text" id="title" name="title" class="form-control" placeholder="Program Title" value="<?= $title ?? '' ?>">
    </div>
</div>
<div class="row">
    <label class="form-label required" style="font-weight: 600;">Program Offering/s</label>
    <div class="col-sm-12" id="offering-container" style="padding-bottom: 0px;">
        <?php
            if(!isset($offering)){
        ?>
        <div class="row">
            <div class="col-lg-12">
                <textarea class="form-control auto-grow-textarea offering" id="offering" name="offering[0]" rows="3" placeholder="Offering"><?= $offering[0] ?? '' ?></textarea>
            </div>
        </div>

        <?php
            }else{
                for ($i = 0; $i < count($offering); $i++) {
        ?>
        <div class="row">
            <div class="col-lg-11 mb-2">
                <textarea class="form-control auto-grow-textarea offering" id="offering" name="offering[<?= $i ?>]" rows="3" placeholder="Offering"><?= $offering[$i] ?? '' ?></textarea>
            </div>
            <div class="col-lg-1 mb-2">
                <button type="button" class="btn btn-danger w-100" data-remove-item><i class="mdi mdi-close"></i></button>
            </div>
        </div>
        <?php
                }
            }
        ?>
    </div>
    
    <div class="col-sm-12" id="add-offering-btn-container">
        <button type="button" class="btn btn-sm btn-info w-100" id="add-offering-btn"><i class="mdi mdi-plus"></i> Add Offering</button>
    </div>
</div>
<div class="row">
    <label class="form-label required" style="font-weight: 600;">Competency Level/s</label>
    <div class="col-sm-12" id="level-container" style="padding-bottom: 0px;">
        <?php
            if(!isset($level)){
        ?>
        <div class="row">
            <div class="col-lg-12">
                <textarea class="form-control auto-grow-textarea level" id="level" name="level[0]" rows="3" placeholder="Level"><?= $level[0] ?? '' ?></textarea>
            </div>
        </div>

        <?php
            }else{
                for ($i = 0; $i < count($level); $i++) {
        ?>
        <div class="row">
            <div class="col-lg-11 mb-2">
                <textarea class="form-control auto-grow-textarea level" id="level" name="level[<?= $i ?>]" rows="3" placeholder="Level"><?= $level[$i] ?? '' ?></textarea>
            </div>
            <div class="col-lg-1 mb-2">
                <button type="button" class="btn btn-danger w-100" data-remove-item><i class="mdi mdi-close"></i></button>
            </div>
        </div>
        <?php
                }
            }
        ?>
    </div>
    
    <div class="col-sm-12" id="add-level-btn-container">
        <button type="button" class="btn btn-sm btn-info w-100" id="add-level-btn"><i class="mdi mdi-plus"></i> Add Level</button>
    </div>
</div>
<div class="row">
    <label class="form-label required" style="font-weight: 600;">Program Duration/s</label>
    <div class="col-sm-12" id="duration-container" style="padding-bottom: 0px;">
        <?php
            if(!isset($duration)){
        ?>
        <div class="row">
            <div class="col-lg-12">
                <textarea class="form-control auto-grow-textarea duration" id="duration" name="duration[0]" rows="3" placeholder="Duration"><?= $duration[0] ?? '' ?></textarea>
            </div>
        </div>

        <?php
            }else{
                for ($i = 0; $i < count($duration); $i++) {
        ?>
        <div class="row">
            <div class="col-lg-11 mb-2">
                <textarea class="form-control auto-grow-textarea duration" id="duration" name="duration[<?= $i ?>]" rows="3" placeholder="Duration"><?= $duration[$i] ?? '' ?></textarea>
            </div>
            <div class="col-lg-1 mb-2">
                <button type="button" class="btn btn-danger w-100" data-remove-item><i class="mdi mdi-close"></i></button>
            </div>
        </div>
        <?php
                }
            }
        ?>
    </div>
    
    <div class="col-sm-12" id="add-duration-btn-container">
        <button type="button" class="btn btn-sm btn-info w-100" id="add-duration-btn"><i class="mdi mdi-plus"></i> Add Duration</button>
    </div>
</div>
<div class="row">
    <label class="form-label required" style="font-weight: 600;">Mode/s of Study</label>
    <div class="col-sm-12" id="mode-container" style="padding-bottom: 0px;">
        <?php
            if(!isset($mode)){
        ?>
        <div class="row">
            <div class="col-lg-12">
                <textarea class="form-control auto-grow-textarea mode" id="mode" name="mode[0]" rows="3" placeholder="Mode"><?= $mode[0] ?? '' ?></textarea>
            </div>
        </div>

        <?php
            }else{
                for ($i = 0; $i < count($mode); $i++) {
        ?>
        <div class="row">
            <div class="col-lg-11 mb-2">
                <textarea class="form-control auto-grow-textarea mode" id="mode" name="mode[<?= $i ?>]" rows="3" placeholder="Mode"><?= $mode[$i] ?? '' ?></textarea>
            </div>
            <div class="col-lg-1 mb-2">
                <button type="button" class="btn btn-danger w-100" data-remove-item><i class="mdi mdi-close"></i></button>
            </div>
        </div>
        <?php
                }
            }
        ?>
    </div>
    
    <div class="col-sm-12" id="add-mode-btn-container">
        <button type="button" class="btn btn-sm btn-info w-100" id="add-mode-btn"><i class="mdi mdi-plus"></i> Add Mode</button>
    </div>
</div>
<div class="row">
    <label class="form-label required" style="font-weight: 600;">Note/s</label>
    <div class="col-sm-12" id="note-container" style="padding-bottom: 0px;">
        <?php
            if(!isset($note)){
        ?>
        <div class="row">
            <div class="col-lg-12">
                <textarea class="form-control auto-grow-textarea note" id="note" name="note[0]" rows="3" placeholder="Note"><?= $note[0] ?? '' ?></textarea>
            </div>
        </div>

        <?php
            }else{
                for ($i = 0; $i < count($note); $i++) {
        ?>
        <div class="row">
            <div class="col-lg-11 mb-2">
                <textarea class="form-control auto-grow-textarea note" id="note" name="note[<?= $i ?>]" rows="3" placeholder="Note"><?= $note[$i] ?? '' ?></textarea>
            </div>
            <div class="col-lg-1 mb-2">
                <button type="button" class="btn btn-danger w-100" data-remove-item><i class="mdi mdi-close"></i></button>
            </div>
        </div>
        <?php
                }
            }
        ?>
    </div>
    
    <div class="col-sm-12" id="add-note-btn-container">
        <button type="button" class="btn btn-sm btn-info w-100" id="add-note-btn"><i class="mdi mdi-plus"></i> Add Note</button>
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
