<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif; ?>

<style>
	.menu-img {
		width: 3em;
		height: 3em;
		object-fit: cover;
		object-position: center center;
	}

	.card-header {
		background-color: #5b9bd5;
		color: white !important;
	}

	.card-title {
		font-weight: bold;
	}

	.table thead {
		background-color: #5b9bd5;
	}

	.table thead th {
		text-align: center !important;
		background-color: #5b9bd5;
		color: white !important;
	}

	.table tbody td {
		color: black !important;
	}

	.table tbody tr:hover {
		background-color: #eef4fb;
	}

	.badge {
		font-size: 0.9em;
		padding: 0.45em 0.85em;
		border-radius: 1em;
	}

	.badge-success {
		background: #4caf50;
		color: white;
	}

	.badge-danger {
		background: #e53935;
		color: white;
	}

	.btn-group .btn,
	.dropdown-item {
		transition: 0.2s ease;
	}


	/* Updated button styling */
	#create_new {
		border-radius: 25px !important;
		font-weight: bold !important;
		padding: 0.5em 1.5em !important;
		background-color: #5b9bd5;
		color: white !important;
	}

	#create_new:hover {
		transform: scale(1.05) !important;
	}

	#create_new:focus {
		box-shadow: 0 0 10px rgba(0, 0, 0, 0.2) !important;
	}
</style>


<div class="card card-outline rounded-3 shadow-sm border-0">
	<div class="card-header py-3 d-flex justify-content-between align-items-center">
		<h3 class="card-title mb-0"><i class="fa fa-utensils mr-2"></i>List of Menus</h3>
		<a href="javascript:void(0)" id="create_new" class="btn btn-warning text-white bg-gradient border-0 ml-auto"><i class="fas fa-plus"></i> Create New</a>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-hover table-striped table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="20%">
					<col width="30%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Menu</th>
						<th>Description</th>
						<th>Status</th>
						<th>Price</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
					$qry = $conn->query("SELECT m.*, c.name as `category` from `menu_list` m inner join category_list c on m.category_id = c.id where m.delete_flag = 0 order by m.`code` asc, m.`name` asc ");
					while($row = $qry->fetch_assoc()):
					?>
					<tr>
						<td class="text-center"><?php echo $i++; ?></td>
						<td><?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
						<td>
							<div style="line-height: 1em">
								<div><?= $row['code'] ?> - <?= $row['name'] ?></div>
								<div><small class="text-muted"><?= $row['category'] ?></small></div>
							</div>
						</td>
						<td><p class="mb-0 truncate-1"><?= strip_tags(htmlspecialchars_decode($row['description'])) ?></p></td>
						<td class="text-center">
							<?php if($row['status'] == 1): ?>
								<span class="badge badge-success">Available</span>
							<?php else: ?>
								<span class="badge badge-danger">Unavailable</span>
							<?php endif; ?>
						</td>
						<td class="text-right">₱ <?= number_format($row['price'], 2) ?></td>
						<td class="text-center">
							<div class="btn-group btn-group-sm">
								<a class="btn btn-light bg-gradient-light border view-data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" title="View Details"><i class="fa fa-eye text-dark"></i></a>
								<a class="btn btn-primary bg-gradient-primary border edit-data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" title="Edit"><i class="fa fa-edit"></i></a>
								<a class="btn btn-danger bg-gradient-danger border delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" title="Delete"><i class="fa fa-trash"></i></a>
							</div>
						</td>
					</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this Menu permanently?", "delete_menu", [$(this).attr('data-id')])
		});
		$('#create_new').click(function(){
			uni_modal("<i class='far fa-plus-square'></i> Add New Menu", "menus/manage_menu.php")
		});
		$('.edit-data').click(function(){
			uni_modal("<i class='fa fa-edit'></i> Edit Menu", "menus/manage_menu.php?id=" + $(this).attr('data-id'))
		});
		$('.view-data').click(function(){
			uni_modal("<i class='fa fa-th-list'></i> Menu Details", "menus/view_menu.php?id=" + $(this).attr('data-id'))
		});
		$('.table').dataTable({
			columnDefs: [
				{ orderable: false, targets: [6] }
			],
			order: [0, 'asc']
		});
		$('.dataTable td, .dataTable th').addClass('py-2 px-3 align-middle');
	});

	function delete_menu($id){
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=delete_menu",
			method: "POST",
			data: { id: $id },
			dataType: "json",
			error: err => {
				console.log(err);
				alert_toast("An error occurred.", 'error');
				end_loader();
			},
			success: function(resp){
				if (typeof resp == 'object' && resp.status == 'success') {
					location.reload();
				} else {
					alert_toast("An error occurred.", 'error');
					end_loader();
				}
			}
		})
	}
</script>
