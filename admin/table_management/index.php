<?php
// filepath: c:\xampp\htdocs\PSUHMPOS\admin\table_management\index.php
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * from `table_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Table Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo base_url ?>admin">Home</a></li>
                    <li class="breadcrumb-item active">Table Management</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<style>
    .table-card {
        border: 2px solid #ddd;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        height: 150px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
    }
    
    .table-card.available {
        background: linear-gradient(145deg, #d4edda, #c3e6cb);
        border-color: #28a745;
    }
    
    .table-card.occupied {
        background: linear-gradient(145deg, #f8d7da, #f1b0b7);
        border-color: #dc3545;
    }
    
    .table-card.reserved {
        background: linear-gradient(145deg, #fff3cd, #ffeaa7);
        border-color: #ffc107;
    }
    
    .table-card:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .table-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 10px;
    }
    
    .table-status {
        font-size: 1rem;
        text-transform: uppercase;
        font-weight: 600;
    }
    
    .table-capacity {
        font-size: 0.9rem;
        color: #666;
        margin-top: 5px;
    }
    
    .order-info {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.7rem;
    }
    
    .legend {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 3px;
        border: 2px solid;
    }
    
    .available-legend { background: #d4edda; border-color: #28a745; }
    .occupied-legend { background: #f8d7da; border-color: #dc3545; }
    .reserved-legend { background: #fff3cd; border-color: #ffc107; }
    .out-of-service-legend { background: #f5f5f5; border-color: #6c757d; }
</style>

<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Tables Overview</h3>
                <div class="card-tools">
                 <?php if($_settings->userdata('type') == 1): ?>
        <a href="javascript:void(0)" id="create_new" class="btn btn-flat btn-primary">
            <span class="fas fa-plus"></span> Create New Table
        </a>
        <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <!-- Legend -->
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color available-legend"></div>
                        <span>Available</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color occupied-legend"></div>
                        <span>Occupied</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color reserved-legend"></div>
                        <span>Reserved</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color out-of-service-legend"></div>
                        <span>Out of Service</span>
                    </div>
                </div>
                
                <!-- Tables Grid -->
                <div class="row" id="tables-container">
                    <?php 
                    $tables = $conn->query("SELECT t.*, o.id as order_id, o.code as order_code, o.total_amount
                                          FROM table_list t 
                                          LEFT JOIN order_list o ON t.id = o.table_id AND o.status IN (0,1) 
                                          WHERE t.delete_flag = 0 
                                          ORDER BY CAST(SUBSTRING(t.table_number, 6) AS UNSIGNED)");
                    while($row = $tables->fetch_assoc()):
                        $status = 'available';
                        if($row['order_id']) {
                            $status = 'occupied';
                        } elseif($row['status'] == 2) {
                            $status = 'reserved';
                        }elseif($row['status'] == 0) {
                            $status = 'out of service';
                        }
                    ?>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                        <div class="table-card <?= $status ?>" data-id="<?= $row['id'] ?>" data-status="<?= $status ?>">
                            <?php if($row['order_id']): ?>
                            <div class="order-info">#<?= $row['order_code'] ?></div>
                            <?php endif; ?>
                            
                            <div class="table-number"><?= $row['table_number'] ?></div>
                            <div class="table-status"><?= ucfirst($status) ?></div>
                            <div class="table-capacity">Capacity: <?= $row['capacity'] ?> people</div>
                            
                            <?php if($row['order_id']): ?>
                            <div style="font-size: 0.8rem; margin-top: 5px;">
                                <!-- <div><?= $row['customer_name'] ?></div> -->
                                <div>₱<?= number_format($row['total_amount'], 2) ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Table Management Modal -->
<div class="modal fade" id="tableModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" id="table-form">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="msg"></div>
                    <input type="hidden" name="id">
                    
                    <div class="form-group">
                        <label for="table_number">Table Number</label>
                        <input type="text" name="table_number" id="table_number" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="capacity">Capacity (People)</label>
                        <input type="number" name="capacity" id="capacity" class="form-control" min="1" max="20" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Location/Area</label>
                        <input type="text" name="location" id="location" class="form-control" placeholder="e.g., Main Hall, Terrace, VIP">
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="1">Available</option>
                            <option value="2">Reserved</option>
                            <option value="0">Out of Service</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Table Action Modal -->
<div class="modal fade" id="tableActionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Table Actions</h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="table-info"></div>
                <div class="btn-group-vertical w-100">
                    <button type="button" class="btn btn-info mb-2" id="view-orders">View Orders</button>
                    <button type="button" class="btn btn-warning mb-2" id="assign-order">Assign New Order</button>
                    <button type="button" class="btn btn-success mb-2" id="mark-available">Mark as Available</button>
                    <button type="button" class="btn btn-secondary mb-2" id="mark-reserved">Mark as Reserved</button>
                    <button type="button" class="btn btn-primary mb-2" id="edit-table">Edit Table</button>
                    <button type="button" class="btn btn-danger" id="delete-table">Delete Table</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    var selectedTableId = null;
    
    // Create new table
    $('#create_new').click(function(){
        $('#table-form')[0].reset();
        $('#table-form input[name="id"]').val('');
        $('.modal-title').text('Add New Table');
        $('#tableModal').modal('show');
    });
    
    // Table click handler
    $(document).on('click', '.table-card', function(){
        selectedTableId = $(this).data('id');
        var status = $(this).data('status');
        var tableNumber = $(this).find('.table-number').text();
        
        $('#table-info').html('<h5>Table: ' + tableNumber + '</h5><p>Status: ' + ucfirst(status) + '</p>');
        
        // Show/hide buttons based on status
        if(status === 'occupied') {
            $('#mark-available').show();
            $('#assign-order').hide();
        } else if(status === 'available') {
            $('#mark-available').hide();
            $('#assign-order').show();
        } else {
            $('#mark-available').show();
            $('#assign-order').show();
        }
        
        $('#tableActionModal').modal('show');
    });
    
    // View orders
    $('#view-orders').click(function(){
        if(selectedTableId) {
            window.open(_base_url_ + 'admin/?page=orders&table_id=' + selectedTableId, '_blank');
        }
    });
    
    // Assign new order
    $('#assign-order').click(function(){
        if(selectedTableId) {
            window.open(_base_url_ + 'admin/?page=sales/manage_sale&table_id=' + selectedTableId, '_blank');
        }
    });
    
    // Mark as available
    $('#mark-available').click(function(){
        if(selectedTableId) {
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=update_table_status",
                method: 'POST',
                data: {id: selectedTableId, status: 1},
                dataType: 'json',
                success: function(resp){
                    if(resp.status == 'success'){
                        alert_toast(resp.msg, 'success');
                        setTimeout(function(){
                            location.reload();
                        }, 1000);
                    } else {
                        alert_toast(resp.msg, 'error');
                    }
                }
            });
        }
    });
    
    // Mark as reserved
    $('#mark-reserved').click(function(){
        if(selectedTableId) {
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=update_table_status",
                method: 'POST',
                data: {id: selectedTableId, status: 2},
                dataType: 'json',
                success: function(resp){
                    if(resp.status == 'success'){
                        alert_toast(resp.msg, 'success');
                        setTimeout(function(){
                            location.reload();
                        }, 1000);
                    } else {
                        alert_toast(resp.msg, 'error');
                    }
                }
            });
        }
    });
    
    // Edit table
    $('#edit-table').click(function(){
        if(selectedTableId) {
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=get_table",
                method: 'POST',
                data: {id: selectedTableId},
                dataType: 'json',
                success: function(resp){
                    if(resp.status == 'success'){
                        var data = resp.data;
                        $('#table-form input[name="id"]').val(data.id);
                        $('#table-form input[name="table_number"]').val(data.table_number);
                        $('#table-form input[name="capacity"]').val(data.capacity);
                        $('#table-form input[name="location"]').val(data.location);
                        $('#table-form select[name="status"]').val(data.status);
                        $('.modal-title').text('Edit Table');
                        $('#tableActionModal').modal('hide');
                        $('#tableModal').modal('show');
                    }
                }
            });
        }
    });
    
    // Delete table
    $('#delete-table').click(function(){
        if(selectedTableId) {
            _conf("Are you sure to delete this table?", "delete_table", [selectedTableId]);
        }
    });
    
    // Save table
    $('#table-form').submit(function(e){
        e.preventDefault();
        var _this = $(this);
        $('.err-msg').remove();
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=save_table",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            success: function(resp){
                if(typeof resp == 'object' && resp.status == 'success'){
                    location.reload();
                } else if(resp.status == 'failed' && !!resp.msg){
                    var el = $('<div>');
                    el.addClass("alert alert-danger err-msg").text(resp.msg);
                    _this.prepend(el);
                    el.show('slow');
                    $("html, body").scrollTop(0);
                } else {
                    alert_toast("An error occurred", 'error');
                    end_loader();
                }
            }
        });
    });
});

function delete_table($id){
    start_loader();
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=delete_table",
        method: 'POST',
        data: {id: $id},
        dataType: 'json',
        success: function(resp){
            if(typeof resp == 'object' && resp.status == 'success'){
                location.reload();
            } else {
                alert_toast("An error occurred.", 'error');
                end_loader();
            }
        }
    });
}

function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}
</script>