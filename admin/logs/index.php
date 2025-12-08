<?php
require_once('../config.php');
require_once('inc/sess_auth.php');

// Only admin can access
if($_settings->userdata('type') != 1){
    redirect('admin/?page=error/404');
    exit;
}
?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Activity Logs</h3>
        <div class="card-tools">
            <button class="btn btn-flat btn-primary btn-sm" type="button" id="filter_btn">
                <i class="fa fa-filter"></i> Filters
            </button>
            <button class="btn btn-flat btn-success btn-sm" type="button" id="export_btn">
                <i class="fa fa-download"></i> Export
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter Panel -->
        <div id="filter_panel" style="display:none;" class="mb-3">
            <div class="card">
                <div class="card-body">
                    <form id="filter_form">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>User</label>
                                    <select name="user_id" class="form-control form-control-sm select2">
                                        <option value="">All Users</option>
                                        <?php
                                        $users = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM users ORDER BY firstname ASC");
                                        while($row = $users->fetch_assoc()):
                                        ?>
                                        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Action</label>
                                    <select name="action" class="form-control form-control-sm">
                                        <option value="">All Actions</option>
                                        <option value="login">Login</option>
                                        <option value="logout">Logout</option>
                                        <option value="create">Create</option>
                                        <option value="update">Update</option>
                                        <option value="delete">Delete</option>
                                        <option value="view">View</option>
                                        <option value="export">Export</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Module</label>
                                    <select name="module" class="form-control form-control-sm">
                                        <option value="">All Modules</option>
                                        <option value="users">Users</option>
                                        <option value="orders">Orders</option>
                                        <option value="menus">Menus</option>
                                        <option value="categories">Categories</option>
                                        <option value="sales">Sales</option>
                                        <option value="discounts">Discounts</option>
                                        <option value="promo_discount">Promo Discount</option>
                                        <option value="reports">Reports</option>
                                        <option value="tables">Tables</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" name="date_from" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" name="date_to" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Search</label>
                                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search description, action, or user...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary btn-sm">Apply Filter</button>
                                        <button type="button" class="btn btn-secondary btn-sm" id="reset_filter">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="stat_today">0</h3>
                        <p>Today's Activities</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="stat_week">0</h3>
                        <p>This Week</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="stat_month">0</h3>
                        <p>This Month</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="stat_total">0</h3>
                        <p>Total Logs</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-database"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="logs_table">
                <colgroup>
                    <col width="5%">
                    <col width="15%">
                    <col width="15%">
                    <col width="10%">
                    <col width="10%">
                    <col width="30%">
                    <col width="10%">
                    <col width="5%">
                </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date/Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th>Date Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(function(){
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Load statistics
    function load_statistics(){
        $.ajax({
            url: _base_url_ + 'classes/ActivityLog.php?action=get_statistics',
            method: 'GET',
            dataType: 'json',
            error: err => {
                console.log(err);
            },
            success: function(resp){
                if(resp){
                    $('#stat_today').text(resp.today_logs || 0);
                    $('#stat_week').text(resp.week_logs || 0);
                    $('#stat_month').text(resp.month_logs || 0);
                    $('#stat_total').text(resp.total_logs || 0);
                }
            }
        });
    }

    // Load logs table
    var table = $('#logs_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: _base_url_ + 'admin/logs/get_logs.php',
            type: 'POST',
            data: function(d){
                d.user_id = $('select[name="user_id"]').val();
                d.action = $('select[name="action"]').val();
                d.module = $('select[name="module"]').val();
                d.date_from = $('input[name="date_from"]').val();
                d.date_to = $('input[name="date_to"]').val();
                d.search = $('input[name="search"]').val();
            },
            error: function(xhr, error, thrown) {
                console.error('Ajax error:', error, xhr.responseText);
            }
        },
        columns: [
            { data: null, render: function(data, type, row, meta){
                return meta.row + meta.settings._iDisplayStart + 1;
            }},
            { data: 'date_created', render: function(data){
                return moment(data).format('MMM DD, YYYY hh:mm A');
            }},
            { data: 'user_name', render: function(data, type, row){
                var badge_class = '';
                if(row.user_type == 1) badge_class = 'badge-danger';
                else if(row.user_type == 2) badge_class = 'badge-primary';
                else if(row.user_type == 3) badge_class = 'badge-info';
                
                var type_text = row.user_type == 1 ? 'Admin' : (row.user_type == 2 ? 'Cashier' : 'Kitchen');
                
                return data + ' <span class="badge ' + badge_class + '">' + type_text + '</span>';
            }},
            { data: 'action', render: function(data){
                var badge_class = 'badge-secondary';
                if(data == 'login') badge_class = 'badge-success';
                else if(data == 'logout') badge_class = 'badge-warning';
                else if(data == 'create') badge_class = 'badge-primary';
                else if(data == 'update') badge_class = 'badge-info';
                else if(data == 'delete') badge_class = 'badge-danger';
                
                return '<span class="badge ' + badge_class + '">' + data.toUpperCase() + '</span>';
            }},
            { data: 'module', render: function(data){
                return data ? '<span class="badge badge-light">' + data + '</span>' : '-';
            }},
            { data: 'description' },
            { data: 'date_created', render: function(data){
                return data ? moment(data).format('MMM DD, YYYY HH:mm:ss') : '-';
            }},
            { data: null, render: function(data, type, row){
                return '<button class="btn btn-sm btn-info view_log" data-id="' + row.id + '"><i class="fas fa-eye"></i></button>';
            }}
        ],
        order: [[1, 'desc']],
        pageLength: 25
    });

    // Toggle filter panel
    $('#filter_btn').click(function(){
        $('#filter_panel').slideToggle();
    });

    // Apply filter
    $('#filter_form').submit(function(e){
        e.preventDefault();
        table.ajax.reload();
    });

    // Reset filter
    $('#reset_filter').click(function(){
        $('#filter_form')[0].reset();
        $('.select2').val('').trigger('change');
        table.ajax.reload();
    });

    // View log details
    $(document).on('click', '.view_log', function(){
        var id = $(this).data('id');
        
        start_loader();
        $.ajax({
            url: _base_url_ + 'admin/logs/view_log.php?id=' + id,
            method: 'GET',
            success: function(resp){
                $('#uni_modal .modal-title').html('Activity Log Details');
                $('#uni_modal .modal-body').html(resp);
                $('#uni_modal .modal-footer').hide();
                $('#uni_modal .modal-dialog').removeClass().addClass('modal-dialog modal-lg modal-dialog-centered');
                $('#uni_modal').modal('show');
                end_loader();
            },
            error: function(){
                alert('Error loading log details');
                end_loader();
            }
        });
    });;

    // Export logs
    $('#export_btn').click(function(){
        var params = {
            user_id: $('select[name="user_id"]').val(),
            action: $('select[name="action"]').val(),
            module: $('select[name="module"]').val(),
            date_from: $('input[name="date_from"]').val(),
            date_to: $('input[name="date_to"]').val(),
            search: $('input[name="search"]').val()
        };
        
        var query = $.param(params);
        window.open(_base_url_ + 'admin/logs/export_logs.php?' + query, '_blank');
    });

    // Load initial data
    load_statistics();
});
</script>
