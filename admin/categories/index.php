<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif; ?>

<style>
	.category-img {
		width: 3em;
		height: 3em;
		object-fit: cover;
		object-position: center center
	}

	.card-header {
		background-color: #5b9bd5;
		color: white;
	}

	.card-title {
		font-weight: bold;
		color: white;
	}

	.table thead {
		background-color: #5b9bd5;
	}

	.table thead th {
		text-align: center;
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
		background: linear-gradient(45deg, #4caf50, #66bb6a);
		color: white;
	}

	.badge-danger {
		background: linear-gradient(45deg, #e53935, #ef5350);
		color: white;
	}

	.btn-group .btn,
	.dropdown-item {
		transition: 0.2s ease;
	}


	#create_new {
		border-radius: 25px;
		font-weight: bold;
		padding: 0.5em 1.5em;
		background-color: #5b9bd5;
		color: white;
	}

	#create_new:hover {
		transform: scale(1.05);
	}

	#create_new:focus {
		box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
	}
</style>


<div class="card card-outline rounded-3 shadow-sm border-0">
	<div class="card-header py-3 d-flex justify-content-between align-items-center">
		<h3 class="card-title mb-0"><i class="fa fa-layer-group mr-2"></i>List of Categories</h3>
		<a href="javascript:void(0)" id="create_new" class="btn btn-warning text-white bg-gradient border-0 ml-auto"><i class="fas fa-plus"></i> Create New</a>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-hover table-striped table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="25%">
					<col width="35%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Name</th>
						<th>Description</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
					$qry = $conn->query("SELECT * FROM `category_list` WHERE delete_flag = 0 ORDER BY `name` ASC ");
					while($row = $qry->fetch_assoc()):
					?>
					<tr>
						<td class="text-center"><?php echo $i++; ?></td>
						<td><?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
						<td><?= $row['name'] ?></td>
						<td><p class="mb-0 truncate-1"><?= strip_tags(htmlspecialchars_decode($row['description'])) ?></p></td>
						<td class="text-center">
							<?php if($row['status'] == 1): ?>
								<span class="badge badge-success">Active</span>
							<?php else: ?>
								<span class="badge badge-danger">Inactive</span>
							<?php endif; ?>
						</td>
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
			_conf("Are you sure to delete this category permanently?", "delete_category", [$(this).attr('data-id')])
		});
		$('#create_new').click(function(){
			uni_modal("<i class='far fa-plus-square'></i> Add New Category", "categories/manage_category.php")
		});
		$('.edit-data').click(function(){
			uni_modal("<i class='fa fa-edit'></i> Edit Category", "categories/manage_category.php?id=" + $(this).attr('data-id'))
		});
		$('.view-data').click(function(){
			uni_modal("<i class='fa fa-th-list'></i> Category Details", "categories/view_category.php?id=" + $(this).attr('data-id'))
		});
		$('.table').dataTable({
			columnDefs: [
				{ orderable: false, targets: [5] }
			],
			order: [0, 'asc']
		});
		$('.dataTable td, .dataTable th').addClass('py-2 px-3 align-middle');
	});

	function delete_category($id){
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=delete_category",
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
