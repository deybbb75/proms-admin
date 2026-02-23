<?php
include '../../includes/init.php';
$db = DB::getInstance();

if (isset($_POST['id'])) {
    $id = decrypt_data($_POST['id']);
    $program = $db->queryUniqueObject('SELECT * FROM tbl_ms_prog WHERE sub_prog_id = :sub_prog_id', ['sub_prog_id' => $id]);
    if ($program) {
        $sub_prog_id              = encrypt_data($program->sub_prog_id);
        $title              = e($program->title);
        $description        = e($program->description);
        $cert_image         = $program->cert_img;
        $cert_image_data    = base64_encode($cert_image);
        $cert_image_type    = $program->cert_img_type;
        $cert_image_src     = "data:{$cert_image_type};base64,{$cert_image_data}";
        $yt_link            = e($program->yt_link);
        $certification      = json_decode($program->certification, true);
        $associate_cert     = json_decode($program->associate_cert, true);
        $expert_cert        = json_decode($program->expert_cert, true);
        $status             = e($program->status);
        $image              = $program->img;
        $image_data         = base64_encode($image);
        $image_type         = $program->img_type;
        $image_src          = "data:{$image_type};base64,{$image_data}";
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
    <div class="col-md-12">
        <label for="cert" class="form-label required">Certificate</label>
        <div id="cert-preview" style="display: <?= !empty($cert_image) ? 'block' : 'none' ?>;">
            <img 
                src="<?= $cert_image_src ?? '' ?>" 
                class="img-fluid mx-auto d-block mt-2"
                style="max-width: 100%; height: auto; border-radius: 20px;"
                alt=""
            >
        <?php
            if(!empty($cert_image)) {
        ?>
            <button type="button" class="btn btn-sm btn-info w-100 mt-2" onclick="updateCert('update')">Update Certificate</button>
        <?php
            }
        ?>
        </div>
        <div id="cert-upload" style="display: <?= !empty($image) ? 'none' : 'block' ?>;">
            <input type="file" id="cert_input" class="filepond" name="cert_input" data-max-file-size="10MB" data-max-files="3" />
            <span class="font-10 text-muted"><b>Note: </b>Please upload an image of the certificate in <b>JPG</b> format. The file size must not exceed <b>10 MB</b>.</span>
            <?php
                if(!empty($cert_image)){
            ?>
                <button type="button" class="btn btn-sm btn-danger w-100 mt-2" onclick="updateCert('cancel')">Cancel</button>
            <?php
                }
            ?>
        </div>
        
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="yt_link" class="form-label required">Youtube Link</label>
        <input type="text" id="yt_link" name="yt_link" class="form-control" placeholder="Youtube Link" value="<?= $yt_link ?? '' ?>">
    </div>
</div>
<div class="row">
    <label class="form-label required" style="font-weight: 600;">Microsoft 365 Apps Certifications</label>
    <div class="col-sm-12" style="padding-bottom: 0px;">
        <?php
            if(!isset($certification)){
        ?>
        <div class="row main-item">
            <div class="col-12 mb-2">
                <textarea class="form-control auto-grow-textarea cert" id="cert" name="cert[0][title]" rows="3" placeholder="Certification"></textarea>
                <div class="row mt-2">
                    <label class="form-label required" style="font-weight: 600;">Categories</label>
                    <div class="col-sm-12" style="padding-bottom: 0px;">
                        <div class="row sub-item">
                            <div class="col-lg-12">
                                <textarea class="form-control auto-grow-textarea title" id="title" name="cert[0][ctg][0][title]" rows="3" placeholder="Category Title"></textarea>
                            </div>
                            <div class="col-lg-12">
                                <textarea class="form-control auto-grow-textarea desc" id="desc" name="cert[0][ctg][0][desc]" rows="3" placeholder="Category Description"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-sm btn-info w-100" onclick="addNewItem(this, ctgObj)"><i class="mdi mdi-plus"></i> Add Category</button>
                    </div>
                </div>
            </div>
        </div>

        <?php
            }else{
                for ($i = 0; $i < count($certification); $i++) {
        ?>
        <div class="row main-item">
            <div class="col-10 mb-2">
                <textarea class="form-control auto-grow-textarea cert" id="cert" name="cert[<?= $i ?>][title]" rows="3" placeholder="Certification"><?= e($certification[$i]['title']) ?? '' ?></textarea>
                <div class="row mt-2">
                    <label class="form-label required" style="font-weight: 600;">Categories</label>
                    <div class="col-sm-12" style="padding-bottom: 0px;">
                        <?php
                            for ($j = 0; $j < count($certification[$i]['ctg']); $j++) {
                        ?>
                        <div class="row sub-item">
                            <div class="col-10 mb-2">
                                <textarea class="form-control auto-grow-textarea title" id="title" name="cert[<?= $i ?>][ctg][<?= $j ?>][title]" rows="3" placeholder="Category Title"><?= e($certification[$i]['ctg'][$j]['title']) ?? '' ?></textarea>
                            </div>
                            <div class="col-2 mb-2">
                                <button type="button" class="btn btn-danger w-100" onclick="removeOldItem(this, ctgObj)"><i class="mdi mdi-close"></i></button>
                            </div>
                            <div class="col-lg-12">
                                <textarea class="form-control auto-grow-textarea desc" id="desc" name="cert[<?= $i ?>][ctg][<?= $j ?>][desc]" rows="3" placeholder="Category Description"><?= e($certification[$i]['ctg'][$j]['desc']) ?? '' ?></textarea>
                            </div>
                        </div>
                        <?php
                            }
                        ?>
                    </div>
                    
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-sm btn-info w-100" onclick="addNewItem(this, ctgObj)"><i class="mdi mdi-plus"></i> Add Category</button>
                    </div>
                </div>
            </div>
            <div class="col-2 mb-2">
                <button type="button" class="btn btn-danger w-100" onclick="removeOldItem(this, certObj)"><i class="mdi mdi-close"></i></button>
            </div>
        </div>
        <?php
                }
            }
        ?>
    </div>
    
    <div class="col-sm-12">
        <button type="button" class="btn btn-sm btn-info w-100" onclick="addNewItem(this, certObj)"><i class="mdi mdi-plus"></i> Add Certification</button>
    </div>
</div>
<div class="row">
    <label class="form-label required" style="font-weight: 600;">Associate Certification/s</label>
    <div class="col-sm-12" style="padding-bottom: 0px;">
        <?php
            if(!isset($associate_cert)){
        ?>
        <div class="row">
            <div class="col-lg-12">
                <textarea class="form-control auto-grow-textarea associate_cert" id="associate_cert" name="associate_cert[0]" rows="3" placeholder="Associate Certification"></textarea>
            </div>
        </div>

        <?php
            }else{
                for ($i = 0; $i < count($associate_cert); $i++) {
        ?>
        <div class="row">
            <div class="col-lg-11 mb-2">
                <textarea class="form-control auto-grow-textarea associate_cert" id="associate_cert" name="associate_cert[<?= $i ?>]" rows="3" placeholder="Associate Certification"><?= e($associate_cert[$i]) ?? '' ?></textarea>
            </div>
            <div class="col-lg-1 mb-2">
                <button type="button" class="btn btn-danger w-100" onclick="removeOldItem(this, associateCertObj)"><i class="mdi mdi-close"></i></button>
            </div>
        </div>
        <?php
                }
            }
        ?>
    </div>
    
    <div class="col-sm-12">
        <button type="button" class="btn btn-sm btn-info w-100" onclick="addNewItem(this, associateCertObj)"><i class="mdi mdi-plus"></i> Add Associate Certification</button>
    </div>
</div>
<div class="row">
    <label class="form-label required" style="font-weight: 600;">Expert Certification/s</label>
    <div class="col-sm-12" style="padding-bottom: 0px;">
        <?php
            if(!isset($expert_cert)){
        ?>
        <div class="row">
            <div class="col-lg-12">
                <textarea class="form-control auto-grow-textarea expert_cert" id="expert_cert" name="expert_cert[0]" rows="3" placeholder="Expert Certification"></textarea>
            </div>
        </div>

        <?php
            }else{
                for ($i = 0; $i < count($expert_cert); $i++) {
        ?>
        <div class="row">
            <div class="col-lg-11 mb-2">
                <textarea class="form-control auto-grow-textarea expert_cert" id="expert_cert" name="expert_cert[<?= $i ?>]" rows="3" placeholder="Expert Certification"><?= e($expert_cert[$i]) ?? '' ?></textarea>
            </div>
            <div class="col-lg-1 mb-2">
                <button type="button" class="btn btn-danger w-100" onclick="removeOldItem(this, expertCertObj)"><i class="mdi mdi-close"></i></button>
            </div>
        </div>
        <?php
                }
            }
        ?>
    </div>
    
    <div class="col-sm-12">
        <button type="button" class="btn btn-sm btn-info w-100" onclick="addNewItem(this, expertCertObj)"><i class="mdi mdi-plus"></i> Add Expert Certification</button>
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
