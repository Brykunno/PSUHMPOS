<?php 
if (isset($_GET['id'])) {
    $user = $conn->query("SELECT * FROM users where id ='{$_GET['id']}' ");
    foreach ($user->fetch_array() as $k => $v) {
        $meta[$k] = $v;
    }
}
?>
<?php if ($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif; ?>

<div class="card card-outline card-warning rounded shadow-sm">
    <div class="card-header bg-warning text-white">
        <h4 class="mb-0"><i class="fas fa-user-circle"></i> Manage User Profile</h4>
    </div>
    <div class="card-body">
        <form action="" id="manage-user">
            <input type="hidden" name="id" value="<?= isset($meta['id']) ? $meta['id'] : '' ?>">
            <div class="row">
                <!-- Avatar + Upload -->
                <div class="col-md-4 text-center mb-4">
                    <img src="<?php echo validate_image(isset($meta['avatar']) ? $meta['avatar'] : '') ?>" alt="User Avatar" id="cimg" class="img-thumbnail mb-2 rounded-circle shadow-sm">
                    <div class="custom-file text-left">
                        <input type="file" class="custom-file-input" id="customFile" name="img" onchange="displayImg(this, $(this))" accept="image/png, image/jpeg">
                        <label class="custom-file-label" for="customFile">Choose avatar</label>
                    </div>
                </div>

                <!-- User Info -->
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="firstname">First Name</label>
                        <input type="text" name="firstname" id="firstname" class="form-control" value="<?= isset($meta['firstname']) ? $meta['firstname'] : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="middlename">Middle Name</label>
                        <input type="text" name="middlename" id="middlename" class="form-control" value="<?= isset($meta['middlename']) ? $meta['middlename'] : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="lastname">Last Name</label>
                        <input type="text" name="lastname" id="lastname" class="form-control" value="<?= isset($meta['lastname']) ? $meta['lastname'] : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" class="form-control" value="<?= isset($meta['username']) ? $meta['username'] : '' ?>" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="password"><?= isset($meta['id']) ? "New" : "" ?> Password</label>
                        <input type="password" name="password" id="password" class="form-control" autocomplete="off">
                        <?php if (isset($meta['id'])): ?>
                            <small class="form-text text-muted"><i>Leave blank if you don't want to change the password.</i></small>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="type">User Role</label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="1" <?= isset($meta['type']) && $meta['type'] == 1 ? 'selected' : '' ?>>Administrator</option>
                            <option value="2" <?= isset($meta['type']) && $meta['type'] == 2 ? 'selected' : '' ?>>Cashier</option>
                            <option value="3" <?= isset($meta['type']) && $meta['type'] == 3 ? 'selected' : '' ?>>Kitchen Side</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer bg-light">
        <div class="d-flex justify-content-end">
            <button class="btn btn-primary mr-2" form="manage-user"><i class="fas fa-save"></i> Save</button>
            <a href="./?page=user/list" class="btn btn-secondary border"><i class="fas fa-arrow-left"></i> Cancel</a>
        </div>
    </div>
</div>

<!-- STYLES -->
<style>
    #cimg {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #ffc107;
    }
    .custom-file-label::after {
        content: "Browse";
    }
</style>

<!-- SCRIPT -->
<script>
	function displayImg(input, _this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	            $('#cimg').attr('src', e.target.result);
	        }
	        reader.readAsDataURL(input.files[0]);
	    } else {
	        $('#cimg').attr('src', "<?php echo validate_image(isset($meta['avatar']) ? $meta['avatar'] : '') ?>");
	    }
	}

	$('#manage-user').submit(function(e){
		e.preventDefault();
		start_loader();
		$.ajax({
			url: _base_url_ + 'classes/Users.php?f=save',
			data: new FormData($(this)[0]),
			cache: false,
			contentType: false,
			processData: false,
			method: 'POST',
			type: 'POST',
			success: function(resp){
				if(resp == 1){
					location.href = './?page=user/list';
				} else {
					$('#msg').html('<div class="alert alert-danger">Username already exists</div>');
					end_loader();
				}
			}
		});
	});
</script>
