<?php
include '../../includes/init.php';
$db = DB::getInstance();

if (isset($_POST['id'])) {
    $id = decrypt_data($_POST['id']);
    $program = $db->queryUniqueObject('SELECT * FROM tbl_micro_course WHERE mc_id = :mc_id', ['mc_id' => $id]);
    if ($program) {
        $mc_id              = encrypt_data($program->mc_id);
        $prog_title         = $program->prog_title;
        $description        = $program->description;
        $course_title       = $program->course_title;
        $course_1           = $program->course_1;
        $course_2           = $program->course_2;
        $course_3           = $program->course_3;
        $duration           = $program->duration;
        $credit_unit        = $program->credit_unit;
        $developer          = $program->developer;
        $developer_email    = $program->developer_email;
        $objective          = $program->objective;
        $policy             = json_decode($program->policy, true);
        $status             = $program->status;
        $image              = $program->img;
        $image_data         = base64_encode($image);
        $image_type         = $program->img_type;
        $image_src          = "data:{$image_type};base64,{$image_data}";
    }
}
?>
<input type="hidden" name="mc_id" value="<?= $mc_id ?? '' ?>">

<div class="row">
    <div class="col-md-12">
        <label for="prog_title" class="form-label required">Program Title</label>
        <input type="text" id="prog_title" name="prog_title" class="form-control" placeholder="Program Title" value="<?= $prog_title ?? '' ?>">
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
        <label for="title" class="form-label required">Course Title</label>
        <input type="text" id="course_title" name="course_title" class="form-control" placeholder="Course Title" value="<?= $course_title ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="title" class="form-label">Course 1</label>
        <input type="text" id="course_1" name="course_1" class="form-control" placeholder="Course 1 Title" value="<?= $course_1 ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="title" class="form-label">Course 2</label>
        <input type="text" id="course_2" name="course_2" class="form-control" placeholder="Course 2 Title" value="<?= $course_2 ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="title" class="form-label">Course 3</label>
        <input type="text" id="course_3" name="course_3" class="form-control" placeholder="Course 3 Title" value="<?= $course_3 ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="duration" class="form-label required">Duration</label>
        <input type="text" id="duration" name="duration" class="form-control" placeholder="Duration" value="<?= $duration ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="credit_unit" class="form-label">Credit Unit</label>
        <input type="number" id="credit_unit" name="credit_unit" class="form-control" placeholder="Credit Unit" value="<?= $credit_unit ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="developer" class="form-label required">Developer</label>
        <input type="text" id="developer" name="developer" class="form-control" placeholder="Developer Name" value="<?= $developer ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="developer_email" class="form-label required">Developer Email</label>
        <input type="email" id="developer_email" name="developer_email" class="form-control" placeholder="Developer Email" value="<?= $developer_email ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="objective" class="form-label required">Objective</label>
        <textarea class="form-control auto-grow-textarea" id="objective" name="objective" rows="5" placeholder="Objective"><?= $objective ?? '' ?></textarea>
    </div>
</div>
<div class="row">
    <label class="form-label required" style="font-weight: 600;">Policies</label>
    <div class="col-sm-12" id="policy-container" style="padding-bottom: 0px;">
        <?php
            if(!isset($policy)){
        ?>
        <div class="row">
            <div class="col-lg-12">
                <textarea class="form-control auto-grow-textarea policy" id="policy" name="policy[0]" rows="3" placeholder="Policy"><?= $policy[0] ?? '' ?></textarea>
            </div>
        </div>

        <?php
            }else{
                for ($i = 0; $i < count($policy); $i++) {
        ?>
        <div class="row">
            <div class="col-lg-11 mb-2">
                <textarea class="form-control auto-grow-textarea policy" id="policy" name="policy[<?= $i ?>]" rows="3" placeholder="Policy"><?= $policy[$i] ?? '' ?></textarea>
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
    
    <div class="col-sm-12" id="add-policy-btn-container">
        <button type="button" class="btn btn-sm btn-info w-100" id="add-policy-btn"><i class="mdi mdi-plus"></i> Add Policy</button>
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
