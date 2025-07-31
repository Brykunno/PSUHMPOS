<?php
require_once('./../../config.php');

if (isset($_GET['id']) && $_GET['id'] > 0) {
    // Parameterized query to prevent SQL injection
    $id = (int) $_GET['id'];
    $qry = $conn->query(
        "SELECT m.*, c.name as `category` 
         FROM `menu_list` m 
         INNER JOIN category_list c ON m.category_id = c.id 
         WHERE m.id = $id AND m.delete_flag = 0"
    );
    
    if ($qry->num_rows > 0) {
        $row = $qry->fetch_assoc();
        foreach ($row as $k => $v) {
            $$k = $v;
        }
    } else {
        echo '<script>alert("Menu ID is not valid."); location.replace("./?page=menus")</script>';
        exit;
    }
} else {
    echo '<script>alert("Menu ID is required."); location.replace("./?page=menus")</script>';
    exit;
}
?>

<style>
    #uni_modal .modal-footer {
        display: none;
    }
</style>

<div class="container-fluid">
    <dl>
        <dt class="text-muted">Category</dt>
        <dd class="pl-4"><?= isset($category) ? htmlspecialchars($category) : "" ?></dd>

        <dt class="text-muted">Code</dt>
        <dd class="pl-4"><?= isset($code) ? htmlspecialchars($code) : "" ?></dd>

        <dt class="text-muted">Name</dt>
        <dd class="pl-4"><?= isset($name) ? htmlspecialchars($name) : "" ?></dd>

        <dt class="text-muted">Price</dt>
        <dd class="pl-4">
            <?php if (isset($price)): ?>
                ₱ <?= format_num($price, 2) ?>
            <?php else: ?>
                N/A
            <?php endif; ?>
        </dd>

        <dt class="text-muted">Description</dt>
        <dd class="pl-4"><?= isset($description) ? nl2br(htmlspecialchars_decode($description)) : '' ?></dd>

        <dt class="text-muted">Status</dt>
        <dd class="pl-4">
            <?php if (isset($status) && $status == 1): ?>
                <span class="badge badge-success px-3 rounded-pill">Available</span>
            <?php else: ?>
                <span class="badge badge-danger px-3 rounded-pill">Unavailable</span>
            <?php endif; ?>
        </dd>

        <?php if (isset($image_path) && !empty($image_path)): ?>
        <dt class="text-muted">Image</dt>
        <dd class="pl-4">
            <img src="<?= base_url . 'uploads/image/' . $image_path ?>" alt="Menu Image" style="max-height: 200px; border: 1px solid #ccc; border-radius: 6px;">
        </dd>
        <?php else: ?>
        <dt class="text-muted">Image</dt>
        <dd class="pl-4">No image uploaded</dd>
        <?php endif; ?>
    </dl>
</div>

<hr class="mx-n3">
<div class="text-right pt-1">
    <button class="btn btn-sm btn-flat btn-light bg-gradient-light border" type="button" data-dismiss="modal">
        <i class="fa fa-times"></i> Close
    </button>
</div>
