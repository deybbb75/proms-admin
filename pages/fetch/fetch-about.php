<?php
include '../../includes/init.php';
$db = DB::getInstance();

$sched_count = 0;

if (isset($_POST['id'])) {
    $id = decrypt_data($_POST['id']);
    $about = $db->queryUniqueObject('SELECT * FROM tbl_about WHERE about_id = :about_id', ['about_id' => $id]);
    if ($about) {
        $about_id = encrypt_data($about->about_id);
        $section  = $about->section;
        $content  = $about->content;
    }
}
?>
<input type="hidden" name="about_id" value="<?= $about_id ?? '' ?>">

<div class="row">
    <div class="col-md-12">
        <label for="section" class="form-label">Section</label>
        <input type="text" id="section" class="form-control" placeholder="Section" value="<?= $section ?? '' ?>" disabled>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <label for="content" class="form-label required">Content</label>
        <textarea class="form-control auto-grow-textarea" id="content" name="content" rows="5" placeholder="Content"><?= $content ?? '' ?></textarea>
    </div>
</div>

