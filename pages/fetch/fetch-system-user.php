<?php
include '../../includes/init.php';
$db = DB::getInstance();

if (isset($_POST['id'])) {
    $id = decrypt_data($_POST['id']);
    $sys_user = $db->queryUniqueObject('SELECT * FROM tbl_system_user WHERE sys_id = :sys_id', ['sys_id' => $id]);
    if ($sys_user) {
        $sys_id = encrypt_data($sys_user->sys_id);
        $fname  = e($sys_user->fname);
        $mname  = e($sys_user->mname);
        $lname  = e($sys_user->lname);
        $emp_no = e($sys_user->emp_no);
        $email  = e($sys_user->email);
        $status = e($sys_user->status);
        $image          = $sys_user->img;
        $image_data     = base64_encode($image);
        $image_type     = $sys_user->img_type;
        $image_src      = "data:{$image_type};base64,{$image_data}";
    }
}
?>
<input type="hidden" name="sys_id" value="<?= $sys_id ?? '' ?>">

<div class="row">
    <div class="col-md-4">
        <label for="fname" class="form-label required">First Name</label>
        <input type="text" id="fname" name="fname" class="form-control" placeholder="First Name" value="<?= $fname ?? '' ?>">
    </div>
    <div class="col-md-4">
        <label for="mname" class="form-label">Middle Name</label>
        <input type="text" id="mname" name="mname" class="form-control" placeholder="Middle Name (Optional)" value="<?= $mname ?? '' ?>">
    </div>
    <div class="col-md-4">
        <label for="lname" class="form-label required">Last Name</label>
        <input type="text" id="lname" name="lname" class="form-control" placeholder="Last Name" value="<?= $lname ?? '' ?>">
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <label for="emp_no" class="form-label required">Employee Number</label>
        <input type="text" id="emp_no" name="emp_no" class="form-control" placeholder="Employee Number" value="<?= $emp_no ?? '' ?>" data-toggle="input-mask" data-mask-format="000000">
    </div>
    <div class="col-sm-6">
        <label for="email" class="form-label required">Email</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="Email" value="<?= $email ?? '' ?>">
    </div>
</div>
<?php
if (!isset($_POST['id'])) {
?>
<div class="row">
    <div class="col-sm-12">
        <label for="create_password" class="form-label required">Password</label>
        <input type="password" id="create_password" name="create_password" class="form-control" placeholder="Password">
    </div>
</div>
<?php
}
?>
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
        <label for="img" class="form-label">Image</label>
        <input type="file" id="profile_input" class="filepond" name="profile_input" data-max-file-size="10MB"/>
        <span class="font-10 text-muted"><b>Note: </b>Please upload an image in <b>JPG</b> format. The file size must not exceed <b>10 MB</b>.</span>
    </div>
</div>
