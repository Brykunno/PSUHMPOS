<?php if($_settings->chk_flashdata('success')): ?>
<script>
    alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif; ?>

<style>
    .user-avatar {
        width: 3em;
        height: 3em;
        object-fit: cover;
        object-position: center center;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background:  #5b9bd5; /* Blue gradient */
        color: white;
    }

    .card-title {
        font-weight: bold;
    }

    .table thead {
        background: #e7f0fa;
    }

    .table thead th {
        text-align: center;
        background: linear-gradient(45deg, #5b9bd5, #3c78d8); /* Blue gradient */
        color: white !important;
    }

    .table tbody tr:hover {
        background-color: #f0f6fc;
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

    .btn-group .btn, .dropdown-item {
        transition: 0.2s ease;
    }

    .table td {
        color: black !important;
    }

    .table th {
        color: white !important;
    }

    /* New button styling */
    #create_new {
        border-radius: 25px;
        font-weight: bold;
        background:#5b9bd5; /* Blue button */
        padding: 0.5em 1.5em;
        color: white;
        border: none;
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
        <h3 class="card-title mb-0"><i class="fa fa-users mr-2"></i>List of Users</h3>
        <a href="./?page=user/manage_user" id="create_new" class="btn btn-warning text-white bg-gradient border-0 ml-auto"><i class="fas fa-plus"></i> Create New</a>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <table class="table table-hover table-striped table-bordered" id="list">
                <colgroup>
                    <col width="5%">
                    <col width="15%">
                    <col width="15%">
                    <col width="25%">
                    <col width="15%">
                    <col width="10%">
                    <col width="15%">
                </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date Updated</th>
                        <th>Avatar</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    $qry = $conn->query("SELECT *, CONCAT(firstname, ' ', COALESCE(CONCAT(middlename, ' '), ''), lastname) as `name` 
                                         FROM `users` 
                                         WHERE id != '{$_settings->userdata('id')}' 
                                         ORDER BY CONCAT(firstname, ' ', lastname) ASC");
                    while($row = $qry->fetch_assoc()):
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $i++; ?></td>
                        <td><?php echo date("Y-m-d H:i", strtotime($row['date_updated'])) ?></td>
                        <td class="text-center">
                            <img src="<?= validate_image($row['avatar']) ?>" alt="Avatar" class="img-thumbnail rounded-circle user-avatar">
                        </td>
                        <td><?php echo $row['name'] ?></td>
                        <td><?php echo $row['username'] ?></td>
                        <td class="text-center">
                            <?php if($row['type'] == 1): ?>
                                <span class="badge badge-success">Administrator</span>
                            <?php elseif($row['type'] == 2): ?>
                                <span class="badge badge-warning">Cashier</span>
                            <?php elseif($row['type'] == 3): ?>
                                <span class="badge badge-info">Kitchen Side</span>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a class="btn btn-light bg-gradient-light border view-data" href="./?page=user/manage_user&id=<?= $row['id'] ?>" title="View Details"><i class="fa fa-eye text-dark"></i></a>
                                <a class="btn btn-primary bg-gradient-primary border edit-data" href="./?page=user/manage_user&id=<?= $row['id'] ?>" title="Edit"><i class="fa fa-edit"></i></a>
                                <a class="btn btn-danger bg-gradient-danger border delete_data" href="javascript:void(0)" data-id="<?= $row['id'] ?>" title="Delete"><i class="fa fa-trash"></i></a>
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
    $(document).ready(function() {
        $('.delete_data').click(function() {
            _conf("Are you sure to delete this User permanently?", "delete_user", [$(this).attr('data-id')])
        })
        $('.table').dataTable({
            columnDefs: [{
                orderable: false,
                targets: [6]
            }],
            order: [0, 'asc']
        });
        $('.dataTable td, .dataTable th').addClass('py-1 px-2 align-middle')
    })

    function delete_user($id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Users.php?f=delete",
            method: "POST",
            data: {
                id: $id
            },
            error: err => {
                console.log(err)
                alert_toast("An error occurred.", 'error');
                end_loader();
            },
            success: function(resp) {
                if (resp == 1) {
                    location.reload();
                } else {
                    alert_toast("An error occurred.", 'error');
                    end_loader();
                }
            }
        })
    }
</script>
