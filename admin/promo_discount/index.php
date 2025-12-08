<?php
// Config is loaded by parent admin/index.php
?>
<div class="content py-4">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0"><i class="fas fa-tag mr-2"></i>Promotional Discounts</h5>
        </div>
        <div class="card-body">
          <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#promo_modal" onclick="manage_promo('')">
            <i class="fas fa-plus mr-2"></i>Add New Promo
          </button>
          <div class="table-responsive">
            <table class="table table-hover table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Promo Name</th>
                  <th>Type</th>
                  <th>Discount Value</th>
                  <th>Valid Period</th>
                  <th>Min Amount</th>
                  <th>Usage</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $query = $conn->query("SELECT * FROM `promo_discount` WHERE `delete_flag` = 0 ORDER BY id DESC");
                $idx = 1;
                while($row = $query->fetch_assoc()):
                  $status_badge = $row['is_active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>';
                  $usage_text = $row['usage_limit'] ? $row['usage_count'] . '/' . $row['usage_limit'] : 'Unlimited';
                  $discount_display = $row['discount_type'] === 'percentage' ? $row['discount_value'] . '%' : '₱' . number_format($row['discount_value'], 2);
                  $start = date('M d, Y', strtotime($row['start_date']));
                  $end = date('M d, Y', strtotime($row['end_date']));
                  
                  // Check if promo has reached usage limit
                  $is_exhausted = ($row['usage_limit'] && $row['usage_count'] >= $row['usage_limit']);
                  $exhausted_badge = $is_exhausted ? '<span class="badge badge-warning ml-2">Exhausted</span>' : '';
                ?>
                <tr>
                  <td><?= $idx++ ?></td>
                  <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                  <td><?= ucfirst($row['discount_type']) ?></td>
                  <td><?= $discount_display ?></td>
                  <td><small><?= $start ?> - <?= $end ?></small></td>
                  <td><?= $row['minimum_amount'] > 0 ? '₱' . number_format($row['minimum_amount'], 2) : '-' ?></td>
                  <td><?= $usage_text ?></td>
                  <td><?= $status_badge ?><?= $exhausted_badge ?></td>
                  <td>
                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#promo_modal" onclick="manage_promo(<?= $row['id'] ?>)">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="delete_promo(<?= $row['id'] ?>)">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Promo Modal -->
<div class="modal fade" id="promo_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Manage Promotional Discount</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="promo-form">
          <input type="hidden" name="id" value="">
          
          <div class="form-group">
            <label>Promo Name *</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          
          <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="2"></textarea>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Discount Type *</label>
                <select name="discount_type" class="form-control" required onchange="update_discount_label()">
                  <option value="percentage">Percentage (%)</option>
                  <option value="fixed">Fixed Amount (₱)</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label id="discount_label">Discount Percentage *</label>
                <div class="input-group">
                  <input type="number" name="discount_value" class="form-control" step="0.01" min="0" required>
                  <div class="input-group-append">
                    <span class="input-group-text" id="discount_symbol">%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Minimum Order Amount</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">₱</span>
                  </div>
                  <input type="number" name="minimum_amount" class="form-control" step="0.01" min="0" value="0">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Maximum Discount (Optional)</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">₱</span>
                  </div>
                  <input type="number" name="maximum_discount" class="form-control" step="0.01" min="0">
                </div>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Start Date *</label>
                <input type="datetime-local" name="start_date" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>End Date *</label>
                <input type="datetime-local" name="end_date" class="form-control" required>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Usage Limit (Optional)</label>
                <input type="number" name="usage_limit" class="form-control" min="1">
                <small class="form-text text-muted">Leave empty for unlimited usage</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Apply To</label>
                <select name="applicable_to" class="form-control">
                  <option value="all_items">All Items</option>
                  <option value="category">Specific Category</option>
                  <option value="menu_item">Specific Item</option>
                </select>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
              <label class="custom-control-label" for="is_active">Active</label>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="save_promo()">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Alert Modal -->
<div class="modal fade" id="alert_modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="alert_title">Alert</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="alert_message">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="delete_confirm_modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this promo discount?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" onclick="confirm_delete_promo()" id="confirm_delete_btn">Delete</button>
      </div>
    </div>
  </div>
</div>

<script>
function update_discount_label() {
  var type = $('select[name="discount_type"]').val();
  if(type === 'percentage') {
    $('#discount_label').text('Discount Percentage *');
    $('#discount_symbol').text('%');
  } else {
    $('#discount_label').text('Discount Amount *');
    $('#discount_symbol').text('₱');
  }
}

function manage_promo(id) {
  if(id === '') {
    // New promo
    $('#promo-form')[0].reset();
    $('input[name="id"]').val('');
    update_discount_label();
  } else {
    // Edit promo
    start_loader();
    $.ajax({
      url: _base_url_ + 'admin/promo_discount/get_promo.php',
      method: 'GET',
      data: { id: id },
      dataType: 'json',
      success: function(resp) {
        if(resp) {
          $('input[name="id"]').val(resp.id);
          $('input[name="name"]').val(resp.name);
          $('textarea[name="description"]').val(resp.description);
          $('select[name="discount_type"]').val(resp.discount_type);
          $('input[name="discount_value"]').val(resp.discount_value);
          $('input[name="minimum_amount"]').val(resp.minimum_amount);
          $('input[name="maximum_discount"]').val(resp.maximum_discount);
          $('input[name="start_date"]').val(resp.start_date.replace(' ', 'T'));
          $('input[name="end_date"]').val(resp.end_date.replace(' ', 'T'));
          $('input[name="usage_limit"]').val(resp.usage_limit);
          $('select[name="applicable_to"]').val(resp.applicable_to);
          
          if(resp.is_active == 1) {
            $('#is_active').prop('checked', true);
          } else {
            $('#is_active').prop('checked', false);
          }
          
          update_discount_label();
        }
        end_loader();
      },
      error: function() {
        alert_toast('Error loading promo details', 'error');
        end_loader();
      }
    });
  }
}

function save_promo() {
  var form = $('#promo-form');
  start_loader();
  
  $.ajax({
    url: _base_url_ + 'classes/Master.php?f=save_promo_discount',
    method: 'POST',
    data: form.serialize(),
    dataType: 'json',
    success: function(resp) {
      if(resp.status === 'success') {
        alert_toast('Promo discount saved successfully', 'success');
        setTimeout(() => location.reload(), 1000);
      } else {
        show_alert_modal('Error', resp.msg || 'Failed to save promo discount', 'error');
      }
      end_loader();
    },
    error: function() {
      show_alert_modal('Error', 'An error occurred', 'error');
      end_loader();
    }
  });
}

var promo_id_to_delete = null;

function delete_promo(id) {
  promo_id_to_delete = id;
  $('#delete_confirm_modal').modal('show');
}

function confirm_delete_promo() {
  if(promo_id_to_delete === null) return;
  
  start_loader();
  $.ajax({
    url: _base_url_ + 'classes/Master.php?f=delete_promo_discount',
    method: 'POST',
    data: { id: promo_id_to_delete },
    dataType: 'json',
    success: function(resp) {
      if(resp.status === 'success') {
        show_alert_modal('Success', 'Promo discount deleted successfully', 'success', function(){
          location.reload();
        });
      } else {
        show_alert_modal('Error', resp.msg || 'Failed to delete promo discount', 'error');
      }
      end_loader();
    },
    error: function() {
      show_alert_modal('Error', 'An error occurred', 'error');
      end_loader();
    }
  });
  $('#delete_confirm_modal').modal('hide');
}

function show_alert_modal(title, message, type, callback) {
  $('#alert_title').text(title);
  $('#alert_message').html(message);
  
  // Add alert styling based on type
  $('#alert_message').removeClass('alert alert-success alert-error alert-warning alert-info');
  
  if(type === 'success') {
    $('#alert_message').addClass('alert alert-success');
  } else if(type === 'error') {
    $('#alert_message').addClass('alert alert-danger');
  } else if(type === 'warning') {
    $('#alert_message').addClass('alert alert-warning');
  } else {
    $('#alert_message').addClass('alert alert-info');
  }
  
  $('#alert_modal').modal('show');
  
  // Execute callback when modal is closed
  if(callback) {
    $('#alert_modal').one('hidden.bs.modal', callback);
  }
}
</script>
