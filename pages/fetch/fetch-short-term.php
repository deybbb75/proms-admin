<?php
include '../../includes/init.php';
$db = DB::getInstance();

if (isset($_POST['id'])) {
    $id = decrypt_data($_POST['id']);
    $program = $db->queryUniqueObject('SELECT * FROM tbl_short_term WHERE st_id = :st_id', ['st_id' => $id]);
    if ($program) {
        $st_id          = encrypt_data($program->st_id);
        $prog_title     = $program->prog_title;
        $training_title = $program->training_title;
        $description    = $program->description;
        $venue          = $program->venue;
        $objective       = json_decode($program->objective, true);
        $outline       = json_decode($program->outline, true);
        $status         = $program->status;
        $image          = $program->img;
        $image_data     = base64_encode($image);
        $image_type     = $program->img_type;
        $image_src      = "data:{$image_type};base64,{$image_data}";
    }
}
?>
<input type="hidden" name="st_id" value="<?= $st_id ?? '' ?>">

<div class="row">
    <div class="col-md-12">
        <label for="prog_title" class="form-label required">Program Title</label>
        <input type="text" id="prog_title" name="prog_title" class="form-control" placeholder="Program Title" value="<?= $prog_title ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="title" class="form-label required">Training/Course Title</label>
        <input type="text" id="training_title" name="training_title" class="form-control" placeholder="Training/Course Title" value="<?= $training_title ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="description" class="form-label required">Description</label>
        <textarea class="form-control auto-grow-textarea" id="description" name="description" rows="5" placeholder="Description"><?= $description ?? '' ?></textarea>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="venue" class="form-label required">Venue</label>
        <input type="text" id="venue" name="venue" class="form-control" placeholder="Venue" value="<?= $venue ?? '' ?>">
    </div>
</div>
<div class="row">
    <label class="form-label required" style="font-weight: 600;">Objective/s</label>
    <div class="col-sm-12" style="padding-bottom: 0px;">
        <?php
            if(!isset($objective)){
        ?>
        <div class="row">
            <div class="col-lg-12">
                <textarea class="form-control auto-grow-textarea objective" id="objective" name="objective[0]" rows="3" placeholder="Objective"><?= $objective[0] ?? '' ?></textarea>
            </div>
        </div>

        <?php
            }else{
                for ($i = 0; $i < count($objective); $i++) {
        ?>
        <div class="row">
            <div class="col-lg-11 mb-2">
                <textarea class="form-control auto-grow-textarea objective" id="objective" name="objective[<?= $i ?>]" rows="3" placeholder="Objective"><?= $objective[$i] ?? '' ?></textarea>
            </div>
            <div class="col-lg-1 mb-2">
                <button type="button" class="btn btn-danger w-100" onclick="removeOldItem(this, ObjectiveObj)"><i class="mdi mdi-close"></i></button>
            </div>
        </div>
        <?php
                }
            }
        ?>
    </div>
    
    <div class="col-sm-12">
        <button type="button" class="btn btn-sm btn-info w-100" onclick="addNewItem(this, ObjectiveObj)"><i class="mdi mdi-plus"></i> Add Objective</button>
    </div>
</div>
<div class="row">
    <label class="form-label required" style="font-weight: 600;">Outline/s</label>
    <div class="col-sm-12" style="padding-bottom: 0px;">
        <?php
            if(!isset($outline)){
        ?>
        <div class="row">
            <div class="col-lg-12">
                <textarea class="form-control auto-grow-textarea outline" id="outline" name="outline[0]" rows="3" placeholder="Outline"></textarea>
            </div>
        </div>

        <?php
            }else{
                for ($i = 0; $i < count($outline); $i++) {
        ?>
        <div class="row">
            <div class="col-lg-11 mb-2">
                <textarea class="form-control auto-grow-textarea outline" id="outline" name="outline[<?= $i ?>]" rows="3" placeholder="Outline"><?= $outline[$i] ?? '' ?></textarea>
            </div>
            <div class="col-lg-1 mb-2">
                <button type="button" class="btn btn-danger w-100" onclick="removeOldItem(this, OutlineObj)"><i class="mdi mdi-close"></i></button>
            </div>
        </div>
        <?php
                }
            }
        ?>
    </div>
    
    <div class="col-sm-12">
        <button type="button" class="btn btn-sm btn-info w-100" onclick="addNewItem(this, OutlineObj)"><i class="mdi mdi-plus"></i> Add Outline</button>
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
