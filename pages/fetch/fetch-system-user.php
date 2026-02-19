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
        $role   = e($sys_user->role);
        $status = e($sys_user->status);
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
<div class="row">
    <div class="col-sm-6">
        <label for="role" class="form-label required">Role</label>
        <select class="form-select select2" data-toggle="select2" name ="role" id="role" data-placeholder="Select Role">
            <option value="<?= $role ?? '' ?>" <?php if(!$status) echo 'disabled'; ?> selected>
                <?= !empty($role) ? $role : '' ?>
            </option>
            <?php
                if($role != "Admin") {
            ?>
                <option value="Admin">Admin</option>
            <?php
                }
                if($role != "User") {
            ?>
                <option value="User">User</option>
            <?php
                }
            ?>
        </select>
    </div>
    <div class="col-sm-6">
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
