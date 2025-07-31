<?php 
require_once('../../config.php');
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM discount_list WHERE id = {$_GET['id']}");
	foreach($qry->fetch_array() as $k => $v){
		$$k = $v;
	}
}
?>
<form action="" id="discount-form">
	<input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
	<div class="form-group">
		<label for="name" class="control-label">Discount Name</label>
		<input type="text" name="name" required class="form-control" value="<?= isset($name) ? $name : '' ?>">
	</div>
	<div class="form-group">
		<label for="percentage" class="control-label">Percentage</label>
		<input type="number" step="0.01" min="0" max="100" name="percentage" required class="form-control" value="<?= isset($percentage) ? $percentage : '' ?>">
	</div>
</form>

<script>
	$(function(){
		$('#discount-form').submit(function(e){
			e.preventDefault();
			start_loader();
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=save_discount",
				data: $(this).serialize(),
				method: 'POST',
				dataType: 'json',
				error: err => {
					console.log(err);
					alert_toast("An error occurred.", 'error');
					end_loader();
				},
				success: function(resp){
					if(resp.status == 'success'){
						location.reload();
					}else{
						alert_toast("Failed to save discount", 'error');
						end_loader();
					}
				}
			});
		});
	});
</script>
