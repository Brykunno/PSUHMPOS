<?php
require_once(__DIR__ . '/../../config.php');
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date("Y-m-d");
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date("Y-m-d");

// Fetch staff list for filter
$staff_qry = $conn->query("SELECT id, username FROM users ORDER BY username ASC");
?>

<div class="content py-4 px-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="fa fa-archive mr-2"></i>Void History (Orders)</h4>
    <div>
      <button class="btn btn-sm btn-light" id="print-void-orders"><i class="fa fa-print"></i> Print</button>
    </div>
  </div>

  <!-- Tab Navigation -->
  <ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="items-tab" data-toggle="tab" href="#items-content" role="tab">Voided Items</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="orders-tab" data-toggle="tab" href="#orders-content" role="tab">Voided Orders</a>
    </li>
  </ul>

  <div class="tab-content">
    <!-- Voided Items Tab -->
    <div class="tab-pane fade show active" id="items-content" role="tabpanel">
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
          <form id="filter-form-void-orders" class="form-inline">
            <div class="form-group mr-2">
              <label class="mr-2">Start Date</label>
              <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>" required>
            </div>
            <div class="form-group mr-2">
              <label class="mr-2">End Date</label>
              <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>" required>
            </div>
            <div class="form-group mr-2">
              <label class="mr-2">Staff</label>
              <select name="staff_id" class="form-control">
                <option value="">All</option>
                <?php while($s = $staff_qry->fetch_assoc()): ?>
                  <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['username']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" id="filter-btn-void-orders">Filter</button>
            </div>
          </form>
        </div>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-body" id="printout-void-orders">
          <table class="table table-bordered table-striped" id="void-history-table-orders">
            <thead>
              <tr>
                <th>#</th>
                <th>Date/Time</th>
                <th>Order Code</th>
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Amount</th>
                <th>Voided By</th>
                <th>Reason</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr><td colspan="9" class="text-center">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Voided Orders Tab -->
    <div class="tab-pane fade" id="orders-content" role="tabpanel">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <table class="table table-bordered table-striped" id="voided-orders-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Order Code</th>
                <th>Date Created</th>
                <th>Items Count</th>
                <th class="text-right">Total Amount</th>
                <th>Voided By</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr><td colspan="7" class="text-center">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<noscript id="print-header-void-orders">
  <div class="text-center mb-3">
    <h4><?= $_settings->info('name') ?></h4>
    <h5>Void History (Orders)</h5>
    <h6>From <?= date("F d, Y", strtotime($start_date)) ?> to <?= date("F d, Y", strtotime($end_date)) ?></h6>
    <hr>
  </div>
</noscript>

<!-- Restore Confirmation Modal -->
<div class="modal fade" id="restoreConfirmModal" tabindex="-1" role="dialog" aria-labelledby="restoreConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="restoreConfirmModalLabel">
          <i class="fa fa-undo mr-2"></i>Restore Item
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p class="mb-3">Are you sure you want to restore this item back to the order?</p>
        <div class="alert alert-info">
          <strong>Item Details:</strong>
          <div id="restore-item-details"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="confirm-restore-btn">
          <i class="fa fa-check mr-1"></i>Yes, Restore
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Restore Order Confirmation Modal -->
<div class="modal fade" id="restoreOrderConfirmModal" tabindex="-1" role="dialog" aria-labelledby="restoreOrderConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="restoreOrderConfirmModalLabel">
          <i class="fa fa-recycle mr-2"></i>Restore Entire Order
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p class="mb-3">Are you sure you want to restore this entire order? All voided items will be restored.</p>
        <div class="alert alert-info">
          <strong>Order Details:</strong>
          <div id="restore-order-details"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-info" id="confirm-restore-order-btn">
          <i class="fa fa-check mr-1"></i>Yes, Restore Order
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Select Table Modal (shown when original table is occupied) -->
<div class="modal fade" id="restoreSelectTableModal" tabindex="-1" role="dialog" aria-labelledby="restoreSelectTableModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="restoreSelectTableModalLabel">
          <i class="fa fa-chair mr-2"></i>Select a Table
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p class="mb-3">The original table is occupied. Please pick another available table for this order.</p>
        <div class="form-group mb-0">
          <label for="restore-available-table" class="font-weight-bold">Available Tables</label>
          <select class="form-control" id="restore-available-table"></select>
        </div>
        <small class="text-muted d-block mt-2">Only tables marked Available and without active queued/serving orders are listed.</small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirm-restore-select-table">
          <i class="fa fa-check mr-1"></i>Use This Table
        </button>
      </div>
    </div>
  </div>
</div>

<script>
function fetch_void_history_orders(data){
    data = data || {};
    data.start_date = data.start_date || $('input[name="start_date"]').val();
    data.end_date = data.end_date || $('input[name="end_date"]').val();
    data.staff_id = data.staff_id || $('select[name="staff_id"]').val();
    start_loader();
    $.ajax({
        url: _base_url_ + 'classes/Master.php?f=get_void_history',
        method: 'POST',
        data: data,
        dataType: 'json',
        error: function(jqXHR, textStatus, errorThrown) {
          console.error('AJAX Error:', textStatus, errorThrown, jqXHR);
          var respText = jqXHR && jqXHR.responseText ? jqXHR.responseText : errorThrown;
          alert_toast('An error occurred: ' + respText, 'error');
          end_loader();
        },
        success: function(resp){
            if(resp.status == 'success'){
                var tbody = $('#void-history-table-orders tbody');
                tbody.empty();
                if(resp.data.length > 0){
                    resp.data.map(function(r, idx){
                        var row = $('<tr>');
                        row.append($('<td>').text(idx+1));
                        row.append($('<td>').text(r.voided_at ? r.voided_at : '-'));
                        row.append($('<td>').text(r.order_code));
                        row.append($('<td>').text(r.item_name ? r.item_name : '-'));
                        row.append($('<td class="text-center">').text(r.quantity));
                        row.append($('<td class="text-right">').text(r.amount ? parseFloat(r.amount).toFixed(2) : '-'));
                        row.append($('<td>').text(r.voided_by_name ? r.voided_by_name : r.voided_by));
                        row.append($('<td>').text(r.reason ? r.reason : '-'));
                        var actions = $('<td class="text-center">');
                        actions.append($('<button class="btn btn-sm btn-success restore-void-btn" data-void-id="' + r.vid + '" data-order-id="' + r.order_id + '"><i class="fa fa-undo"></i> Restore</button>'));
                        row.append(actions);
                        tbody.append(row);
                    })
                }else{
                    tbody.append('<tr><td colspan="9" class="text-center">No records found</td></tr>');
                }
            }else{
                alert_toast('Failed to fetch history', 'error');
            }
            end_loader();
        }
    })
}

function fetch_voided_orders(){
    start_loader();
    $.ajax({
        url: _base_url_ + 'classes/Master.php?f=get_voided_orders',
        method: 'POST',
        dataType: 'json',
        error: function(jqXHR, textStatus, errorThrown) {
          console.error('AJAX Error:', textStatus, errorThrown, jqXHR);
          alert_toast('An error occurred while fetching voided orders', 'error');
          end_loader();
        },
        success: function(resp){
            if(resp.status == 'success'){
                var tbody = $('#voided-orders-table tbody');
                tbody.empty();
                if(resp.data.length > 0){
                    resp.data.map(function(r, idx){
                        var row = $('<tr>');
                        row.append($('<td>').text(idx+1));
                        row.append($('<td>').text(r.code));
                        row.append($('<td>').text(r.date_created ? r.date_created : '-'));
                        row.append($('<td class="text-center">').text(r.item_count || 0));
                        // Format total amount properly as currency
                        var totalAmount = r.total_amount ? parseFloat(r.total_amount).toFixed(2) : '0.00';
                        row.append($('<td class="text-right">').text('₱' + totalAmount));
                        row.append($('<td>').text(r.voided_by_name ? r.voided_by_name : '-'));
                        var actions = $('<td class="text-center">');
                        actions.append($('<button class="btn btn-sm btn-info restore-order-btn" data-order-id="' + r.id + '"><i class="fa fa-recycle"></i> Restore</button>'));
                        row.append(actions);
                        tbody.append(row);
                    })
                }else{
                    tbody.append('<tr><td colspan="7" class="text-center">No voided orders found</td></tr>');
                }
            }else{
                alert_toast('Failed to fetch voided orders', 'error');
            }
            end_loader();
        }
    })
}

$(function(){
    var pendingRestoreOrderId = null;

    fetch_void_history_orders();
    fetch_voided_orders();
    $('#filter-form-void-orders').submit(function(e){
        e.preventDefault();
        fetch_void_history_orders();
    });

    // Restore void item - show confirmation modal
    $(document).on('click', '.restore-void-btn', function(){
        var void_id = $(this).data('void-id');
        var order_id = $(this).data('order-id');
        var btn = $(this);
        var row = btn.closest('tr');
        
        // Extract item details from the row
        var item_name = row.find('td:eq(3)').text();
        var quantity = row.find('td:eq(4)').text();
        var amount = row.find('td:eq(5)').text();
        var voided_at = row.find('td:eq(1)').text();
        
        // Display item details in modal
        var details = '<p><strong>Item:</strong> ' + item_name + '</p>' +
                      '<p><strong>Quantity:</strong> ' + quantity + '</p>' +
                      '<p><strong>Amount:</strong> ₱' + amount + '</p>' +
                      '<p><strong>Voided At:</strong> ' + voided_at + '</p>';
        
        $('#restore-item-details').html(details);
        
        // Store the restoration data
        $('#confirm-restore-btn').data('void-id', void_id).data('order-id', order_id);
        
        // Show modal
        $('#restoreConfirmModal').modal('show');
    });

    // Handle confirm restore button click (item)
    $('#confirm-restore-btn').on('click', function(){
        var void_id = $(this).data('void-id');
        var order_id = $(this).data('order-id');
        
        $('#restoreConfirmModal').modal('hide');
        start_loader();
        
        $.ajax({
            url: _base_url_ + 'classes/Master.php?f=restore_void_item',
            method: 'POST',
            data: {
                void_id: void_id,
                order_id: order_id
            },
            dataType: 'json',
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Restore Error:', textStatus, errorThrown, jqXHR);
                alert_toast('Error restoring item: ' + textStatus, 'error');
                end_loader();
            },
            success: function(resp){
                if(resp.status == 'success'){
                    alert_toast(resp.msg + ' - Order restored and now visible in the Orders List.', 'success');
                    fetch_void_history_orders();
                }else{
                    alert_toast(resp.msg || 'Failed to restore item', 'error');
                }
                end_loader();
            }
        });
    });

    function openTablePicker(resp){
        var $select = $('#restore-available-table');
        $select.empty();

        if(resp.available_tables && resp.available_tables.length){
            resp.available_tables.forEach(function(t){
                var label = t.table_number + ' (Capacity: ' + (t.capacity || '-') + ')';
                $select.append('<option value="'+ t.id +'">'+ label +'</option>');
            });
            $('#restoreSelectTableModal').modal('show');
        }else{
            alert_toast(resp.msg || 'No available tables right now.', 'error');
        }
    }

    function attemptRestoreOrder(orderId, tableId){
        start_loader();
        $.ajax({
            url: _base_url_ + 'classes/Master.php?f=restore_order',
            method: 'POST',
            data: {
                order_id: orderId,
                table_id: tableId || ''
            },
            dataType: 'json',
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Restore Error:', textStatus, errorThrown, jqXHR);
                alert_toast('Error restoring order: ' + textStatus, 'error');
                end_loader();
            },
            success: function(resp){
                if(resp.status === 'success'){
                    alert_toast(resp.msg + ' - Order is now visible in the Orders List.', 'success');
                    fetch_voided_orders();
                    fetch_void_history_orders();
                }else if(resp.status === 'need_table'){
                    end_loader();
                    openTablePicker(resp);
                    return;
                }else if(resp.status === 'no_table'){
                    alert_toast(resp.msg || 'No available tables right now.', 'error');
                }else{
                    alert_toast(resp.msg || 'Failed to restore order', 'error');
                }
                end_loader();
            }
        });
    }

    // Handle restore order button click - show confirmation modal
    $(document).on('click', '.restore-order-btn', function(){
        var order_id = $(this).data('order-id');
        var row = $(this).closest('tr');
        
        // Extract order details from the row
        var order_code = row.find('td:eq(1)').text();
        var date_created = row.find('td:eq(2)').text();
        var item_count = row.find('td:eq(3)').text();
        var total_amount = row.find('td:eq(4)').text();
        
        // Display order details in modal
        var details = '<p><strong>Order Code:</strong> ' + order_code + '</p>' +
                      '<p><strong>Date Created:</strong> ' + date_created + '</p>' +
                      '<p><strong>Items Count:</strong> ' + item_count + '</p>' +
                      '<p><strong>Total Amount:</strong> ' + total_amount + '</p>';
        
        $('#restore-order-details').html(details);
        
        // Store the restoration data
        $('#confirm-restore-order-btn').data('order-id', order_id);
        pendingRestoreOrderId = order_id;
        
        // Show modal
        $('#restoreOrderConfirmModal').modal('show');
    });

    // Handle confirm restore order button click
    $('#confirm-restore-order-btn').on('click', function(){
        var order_id = $(this).data('order-id');
        pendingRestoreOrderId = order_id;
        $('#restoreOrderConfirmModal').modal('hide');
        attemptRestoreOrder(order_id, null);
    });

    // Handle table selection confirm
    $('#confirm-restore-select-table').on('click', function(){
        var selectedTable = $('#restore-available-table').val();
        if(!selectedTable){
            alert_toast('Please choose a table.', 'warning');
            return;
        }
        $('#restoreSelectTableModal').modal('hide');
        if(pendingRestoreOrderId){
            attemptRestoreOrder(pendingRestoreOrderId, selectedTable);
        }
    });

    $('#print-void-orders').click(function(){
        var h = $('head').clone();
        var el = $('#printout-void-orders').clone();
        var ph = $($('noscript#print-header-void-orders').html()).clone();
        h.find('title').text('Void History (Orders) - Print View');
        var nw = window.open('', '_blank', 'width=800,height=600,left=100,top=100');
        nw.document.querySelector('head').innerHTML = h.html();
        nw.document.querySelector('body').innerHTML = ph[0].outerHTML + el[0].outerHTML;
        nw.document.close();
        start_loader();
        setTimeout(() => { nw.print(); setTimeout(() => { nw.close(); end_loader(); }, 200); }, 300);
    });
});
</script>
