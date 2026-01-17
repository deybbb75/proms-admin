<div class="row">
    <div class="col-md-4">
        <label for="fname" class="form-label">First Name</label>
        <input type="text" id="fname" name="fname" class="form-control" placeholder="First Name">
    </div>
    <div class="col-md-4">
        <label for="mname" class="form-label">Middle Name</label>
        <input type="text" id="mname" name="mname" class="form-control" placeholder="Middle Name (Optional)">
    </div>
    <div class="col-md-4">
        <label for="lname" class="form-label">Last Name</label>
        <input type="text" id="lname" name="lname" class="form-control" placeholder="Last Name">
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <label for="emp_no" class="form-label">Employee Number</label>
        <input type="text" id="emp_no" name="emp_no" class="form-control" placeholder="Employee Number">
    </div>
    <div class="col-sm-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="Email">
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <label for="role" class="form-label">Role</label>
        <select class="form-select select2" data-toggle="select2" name ="role" id="role" data-placeholder="Select Role">
            <option value="" disabled selected>Select Role</option>
            <option value="Admin">Admin</option>
            <option value="User">User</option>
        </select>
    </div>
    <div class="col-sm-6">
        <label for="status" class="form-label">Status</label>
        <select class="form-control select2" data-toggle="select2" name ="status" data-placeholder="Select Status">
            <option value="" disabled selected>Select Status</option>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select>
    </div>
</div>
