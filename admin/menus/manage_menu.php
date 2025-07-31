<?php
require_once('./../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `menu_list` where id = '{$_GET['id']}' and `delete_flag` = 0 ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>

<div class="container-fluid">
    <form action="" id="menu-form" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
        <div class="form-group">
            <label for="category_id" class="control-label">Category</label>
            <select name="category_id" id="category_id" class="form-control form-control-sm rounded-0" required>
                <option value="">-- Select Category --</option>
                <?php 
                $categories = $conn->query("SELECT * FROM `category_list` where delete_flag = 0 and `status` = 1 ");
                while($row= $categories->fetch_assoc()):
                ?>
                <option value="<?= $row['id'] ?>" <?= isset($category_id) && $category_id == $row['id'] ? 'selected' : '' ?>><?= $row['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="code" class="control-label">Code</label>
            <input type="text" name="code" id="code" class="form-control form-control-sm rounded-0" value="<?= isset($code) ? $code : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="name" class="control-label">Name</label>
            <input type="text" name="name" id="name" class="form-control form-control-sm rounded-0" value="<?= isset($name) ? $name : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="price" class="control-label">Price</label>
            <input type="number" step="any" name="price" id="price" class="form-control form-control-sm rounded-0 text-right" value="<?= isset($price) ? $price : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="description" class="control-label">Description</label>
            <textarea rows="3" name="description" id="description" class="form-control form-control-sm rounded-0" required><?= isset($description) ? $description : '' ?></textarea>
        </div>
        <div class="form-group">
            <label for="status" class="control-label">Status</label>
            <select name="status" id="status" class="form-control form-control-sm rounded-0" required>
                <option value="1" <?= isset($status) && $status == 1 ? 'selected' : '' ?>>Available</option>
                <option value="0" <?= isset($status) && $status == 0 ? 'selected' : '' ?>>Unavailable</option>
            </select>
        </div>
        <div class="form-group">
            <label for="image" class="control-label">Image</label>
            <input type="file" name="image" id="image" class="form-control form-control-sm rounded-0" accept="image/*">
            <?php if (isset($image_path) && $image_path): ?>
                <p>Current Image: <img src="../uploads/image/<?= $image_path ?>" width="100" height="100" alt="Image"></p>
                <input type="hidden" name="existing_image" value="<?= $image_path ?>">
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
$(document).ready(function(){
    $('#uni_modal').on('shown.bs.modal', function(){
        $('#category_id').select2({
            placeholder: "Select Category Here",
            width: '100%',
            dropdownParent: $('#uni_modal')
        });
    });

    $('#menu-form').submit(function(e){
        e.preventDefault();
        var _this = $(this);
        $('.err-msg').remove();
        start_loader();

        $.ajax({
            url: _base_url_ + "classes/Master.php?f=save_menu",
            data: new FormData(this),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            dataType: 'json',
			error: function(xhr, status, error) {
			    console.log("XHR:", xhr);
			    console.log("Status:", status);
			    console.log("Error:", error);
			    alert_toast("An error occurred: " + xhr.responseText, 'error');
			    end_loader();
			},
            success: function(resp) {
                if (resp.status == 'success') {
                    alert_toast(resp.msg, 'success');
                    uni_modal("<i class='fa fa-th-list'></i> Menu Details", "menus/view_menu.php?id=" + resp.iid);
                    $('#uni_modal').on('hide.bs.modal', function(){
                        location.reload();
                    });
                } else if (resp.status == 'failed' && !!resp.msg) {
                    let el = $('<div>').addClass("alert alert-danger err-msg").text(resp.msg);
                    _this.prepend(el);
                    el.show('slow');
                    $("html, body").scrollTop(0);
                    end_loader();
                } else {
                    alert_toast("An unexpected error occurred", 'error');
                    end_loader();
                    console.log(resp);
                }
            }
        });
    });
});
</script>
