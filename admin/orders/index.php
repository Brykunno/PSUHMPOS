<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<style>
	.order-logo {
		width: 3em;
		height: 3em;
		object-fit: cover;
		object-position: center center;
	}

	.card-header {
		background: #5b9bd5;
		color: white;
	}

	.card-title,
	.card-header h3,
	.card-header h4,
	.card-header .table-title,
	.table-title {
		font-weight: bold;
		color: white !important;
	}

	.table thead th {
		text-align: center;
		background: #5b9bd5;
		color: white; /* Only header text white */
	}

	.table tbody tr:hover {
		background-color: #eef4fb;
	}

	.table tbody td,
	.table tbody th {
		color: black !important; /* Table body text black */
	}

	.badge {
		font-size: 0.9em;
		padding: 0.45em 0.85em;
		border-radius: 1em;
		color: white;
	}

	.badge-primary {
		background: #5b9bd5;
		color: white;
	}

	.badge-success {
		background: #4caf50;
		color: white;
	}

	.badge-light {
		background: #e0e0e0;
		color: #555;
	}

	.btn-group .btn {
		transition: 0.2s ease;
		color: black;
		background-color:rgb(255, 255, 255);
		border: none;
	}

	/* Individual item delete button styling */
	.delete_order_item {
		transition: all 0.2s ease;
		padding: 0.25rem 0.5rem;
		font-size: 0.75rem;
		margin-left: 0.5rem;
	}

	.delete_order_item:hover {
		background-color: #dc3545 !important;
		border-color: #dc3545 !important;
		color: white !important;
		transform: scale(1.05);
	}

	.list-group-item > div {
		display: flex;
		align-items: center;
		gap: 0.5rem;
	}

	/* Ensure confirmation modal Continue button is always clickable */
	#confirm_modal #confirm {
		pointer-events: auto !important;
		z-index: 9999 !important;
		position: relative;
	}
	
	#confirm_modal .modal-footer button {
		pointer-events: auto !important;
		z-index: 9999 !important;
	}

</style>





<div class="card card-outline rounded-3 shadow-sm border-0">
	<div class="card-header py-3">
		<h3 class="card-title mb-0"><i class="fa fa-list-alt mr-2"></i>List of Orders</h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-hover table-striped table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="20%">
					<col width="15%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Transaction Code</th>
            	<th>Table</th>
						<th>Queue</th>
            	<th>Cashier</th>
						<th>Total Amount</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
				<?php 
$i = 1;
$uwhere = "";
if ($_settings->userdata('type') != '1') {
    $uwhere = " WHERE order_list.user_id = '{$_settings->userdata('id')}' ";
}

// Fix the SQL query - add WHERE clause properly
$qry = $conn->query("SELECT order_list.*, users.username, table_list.table_number
                     FROM order_list 
                     INNER JOIN users ON order_list.user_id = users.id 
                     LEFT JOIN table_list ON order_list.table_id = table_list.id 
                     {$uwhere}
                     ORDER BY abs(unix_timestamp(order_list.date_created)) DESC");
while ($row = $qry->fetch_assoc()):
    $order_id = $row['id'];

    // Fetch all items for this order
    $items = $conn->query("SELECT oi.*, ml.name AS menu_name, ml.price 
                           FROM order_items oi 
                           INNER JOIN menu_list ml ON oi.menu_id = ml.id 
                           WHERE oi.order_id = '$order_id'");
?>
<tr>
    <td class="text-center"><?php echo $i++; ?></td>
    <td><?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
    <td class="text-center"><?= $row['code'] ?></td>
    <td class="text-center"><?= !empty($row['table_number']) ? $row['table_number'] : $row['order_type'] ?></td>
    <td class="text-center"><?= $row['queue'] ?></td>
     <td class="text-center"><?= $row['username'] ?></td>
    <td class="text-right">₱<?php echo number_format($row['total_amount'], 2); ?></td>
    <td class="text-center">
        <?php 
        switch($row['status']){
            case 0:
                echo '<span class="badge badge-warning">Queued</span>';
                break;
            case 1:
                echo '<span class="badge badge-primary">Served</span>';
                break;
            case 2:
                echo '<span class="badge badge-success">Paid</span>';
                break;
            case 3:
                echo '<span class="badge badge-info">Billed Out</span>';
                break;
            default:
                echo '<span class="badge badge-light">N/A</span>';
                break;
        }
        ?>
    </td>
    <td class="text-center">
        <div class="btn-group btn-group-sm">
            <?php if($row['status'] == 2): // Paid ?>
                <!-- Print Receipt Button -->
                <a class="btn btn-light bg-gradient-light border view_receipt" href="javascript:void(0)" data-id="<?php echo $order_id ?>" title="Print Receipt">
                    <i class="fa fa-print text-success"></i>
                </a>
                <!-- Delete Button -->
                <!-- <a class="btn btn-danger bg-gradient-danger delete_data" href="javascript:void(0)" data-id="<?php echo $order_id ?>" title="Delete Order">
                    <i class="fa fa-trash"></i>
                </a> -->
            <?php else: ?>
                <!-- Edit Order Button -->
       
                <a class="btn btn-light bg-gradient-light border" data-toggle="modal" data-target="#orderConfirmationModal<?php echo $order_id ?>" title="Edit Order">
                    <i class="fa fa-edit text-warning"></i>
                </a>
              
                <?php if($row['status'] != 0): // Queued ?>
                <!-- Bill Out Button -->
                <a class="btn btn-light bg-gradient-light border" data-toggle="modal" data-target="#billoutConfirmationModal<?php echo $order_id ?>" title="Bill Out">
                    <i class="fa fa-receipt text-warning"></i>
                </a>
                <?php endif?>
                  <?php if($row['status'] == 0): // Queued ?>
                <!-- Delete Button -->
                <a class="btn btn-danger bg-gradient-danger delete_data" href="javascript:void(0)" data-id="<?php echo $order_id ?>" title="Delete Order">
                    <i class="fa fa-trash"></i>
                </a>
                <?php endif?>
            <?php endif; ?>
        </div>
    </td>
</tr>

<!-- Modal for Editing Order -->
<div class="modal fade" id="orderConfirmationModal<?= $order_id ?>" tabindex="-1" role="dialog"
  aria-labelledby="orderConfirmationModalLabel" aria-hidden="true"
  data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="orderConfirmationModalLabel">Edit Order</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h6 class="font-weight-bold">Order Summary (Order #<?php echo $order_id ?>):</h6>
        <ul class="list-group mb-3">
          <?php while($item = $items->fetch_assoc()): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              
              <?= $item['menu_name'] ?> 
              <div class="">
              <span class="badge badge-primary badge-pill">
                <?= $item['quantity'] ?>x</span>
                <?php if($row['status'] == 0): // Queued ?>
              <button class="btn btn-sm btn-outline-danger delete_order_item" 
                      data-order-id="<?= $order_id ?>" 
                      data-item-id="<?= $item['id'] ?>"
                      data-item-name="<?= $item['menu_name'] ?>"
                      title="Delete Item">
                <i class="fa fa-trash"></i>
              </button>
              <?php endif ?>
              </div>

            </li>
            
          <?php endwhile; ?>
        </ul>

		<!-- Add Order Button -->
<div class="mb-2 text-center">
	<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addOrderModal<?= $order_id ?>">
		Add Order
	</button>
</div>

<!-- Modal -->
<div class="modal fade" id="addOrderModal<?= $order_id ?>" tabindex="-1" role="dialog" aria-labelledby="addOrderModalLabel<?= $order_id ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="addOrderModalLabel<?= $order_id ?>">Add Items to Order #<?= $order_id ?></h5>
        <button type="button" class="close text-white" onclick="$('#addOrderModal<?= $order_id ?>').modal('hide')" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="add_order_item.php" method="POST" id="addOrderForm<?= $order_id ?>"> <!-- You can change this to your actual processing file -->
        <div class="modal-body">
          <!-- Hidden input to pass order ID -->
          <input type="hidden" name="order_id" value="<?= $order_id ?>">

          <div class="form-group">
            <label for="menu_item">Select Menu Item:</label>
            <select class="form-control" name="menu_id" id="menu_item" required>
              <option value="">-- Select Item --</option>
              <?php
              $menus = $conn->query("SELECT * FROM menu_list ORDER BY name ASC");
              while($menu = $menus->fetch_assoc()):
              ?>
                <option value="<?= $menu['id'] ?>"><?= $menu['name'] ?> - ₱<?= number_format($menu['price'], 2) ?></option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" class="form-control" name="quantity" min="1" value="1" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Add to Order</button>
         <button type="button" class="btn btn-secondary" onclick="$('#addOrderModal<?= $order_id ?>').modal('hide')">Cancel</button>

        </div>
      </form>
    </div>
  </div>
</div>
        <div class="mb-2">
          <strong>Order Type:</strong> 
          <span class="badge badge-info"><?= ucwords($row['order_type']) ?></span>
        </div>
        <!-- <div class="mb-2">
          <strong>Discount:</strong> 
          <span class="badge badge-success"><?= $row['discount'] ?? 'None' ?></span>
        </div> -->
        <div class="mb-2">
          <strong>Total Amount:</strong> 
          <span class="font-weight-bold">₱<?= number_format($row['total_amount'], 2) ?></span>
        </div>
	
        <!-- <div class="mb-2">
          <strong>Tendered Amount:</strong> 
          <span class="font-weight-bold">₱<?= number_format($row['tendered_amount'], 2) ?></span>
        </div>
        <div class="mb-2">
          <strong>Change:</strong> 
          <span class="font-weight-bold text-danger">₱<?= number_format($row['tendered_amount'] - $row['total_amount'], 2) ?></span>
        </div> -->
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button> -->
        <!-- <button type="button" class="btn btn-primary" id="confirmOrderButton<?= $order_id ?>">Confirm Order</button> -->
      </div>
    </div>
  </div>
</div>


          <!-- Bill Out Modal -->
<div class="modal fade" id="billoutConfirmationModal<?= $order_id ?>" tabindex="-1" role="dialog" aria-labelledby="billoutConfirmationModalLabel<?= $order_id ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="billoutConfirmationModalLabel<?= $order_id ?>">Bill Out for Order #<?= $order_id ?></h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="process_billout.php" method="POST" id="billOutForm<?= $order_id ?>">
        <div class="modal-body">
          <input type="hidden" name="order_id" value="<?= $order_id ?>">
            <a class="btn btn-light bg-gradient-light border billout_receipt" href="javascript:void(0)" data-id="<?php echo $order_id ?>" title="Print Receipt">
                <i class="fa fa-receipt text-warning"></i>
            </a>
          <div class="form-group">
            <label><strong>Payment Method:</strong></label>
            <div class="form-check">
              <input class="form-check-input payment-method" type="radio" name="payment_method" value="cash" id="cash<?= $order_id ?>" checked>
              <label class="form-check-label" for="cash<?= $order_id ?>">Cash</label>
            </div>
            <div class="form-check">
              <input class="form-check-input payment-method" type="radio" name="payment_method" value="emoney" id="emoney<?= $order_id ?>">
              <label class="form-check-label" for="emoney<?= $order_id ?>">E-Money</label>
            </div>
            <div class="form-check">
              <input class="form-check-input payment-method" type="radio" name="payment_method" value="credit_card" id="creditCard<?= $order_id ?>">
              <label class="form-check-label" for="creditCard<?= $order_id ?>">Credit Card</label>
            </div>
          </div>

          
<!-- Discount Type Dropdown -->
<div class="form-group">
  <label for="discount_type<?= $order_id ?>">Discount</label>
  <select id="discount_type<?= $order_id ?>" name="discount_type" class="form-control form-control-sm rounded-0 bg-light font-weight-bold" style="font-size: 12px;" required>
    <option value="N/A" data-percent="0" selected>N/A</option>
    <?php
    $discounts = $conn->query("SELECT * FROM discount_list");
    while($discount = $discounts->fetch_assoc()):
    ?>
      <option value="<?= $discount['name'] ?>" data-percent="<?= $discount['percentage'] ?>">
        <?= $discount['name'] ?>
      </option>
    <?php endwhile; ?>
  </select>
</div>

<!-- Discount Code (shown when a discount is selected) -->
<div class="form-group d-none" id="discount_code_group<?= $order_id ?>">
  <label for="discount_code<?= $order_id ?>">Discount Code</label>
  <input type="text" class="form-control" id="discount_code<?= $order_id ?>" name="discount_code" placeholder="Enter discount code">
  <small class="form-text text-muted">Required when a discount is selected.</small>
  
</div>

          <div class="form-group payment-input cash-input">
            <label for="cash_amount<?= $order_id ?>">Cash Amount</label>
            <input type="number" step="0.01"    class="form-control" name="cash_amount" id="cash_amount<?= $order_id ?>">
          </div>


          <div class="form-group payment-input emoney-input d-none">
            <label for="emoney_ref<?= $order_id ?>">E-Money Reference No.</label>
            <input type="text" class="form-control" name="emoney_reference" id="emoney_ref<?= $order_id ?>">
          </div>

          <div class="form-group payment-input credit-card-input d-none">
            <label for="card_number<?= $order_id ?>">Card Number</label>
            <input type="text" class="form-control" name="card_number" id="card_number<?= $order_id ?>">
          </div>



<!-- Show Discount Percent -->
<div class="form-group">
  <label>Discount Percent:</label>
  <span id="discount_percent<?= $order_id ?>" class="font-weight-bold">0%</span>
</div>


<!-- Show Total Amount -->
<div class="form-group">
  <label>Subtotal Amount:</label>
  <span id="total_amount<?= $order_id ?>" class="font-weight-bold">₱<?= number_format($row['total_amount'], 2) ?></span>
</div>

<!-- Show Discounted Price -->
<div class="form-group">
  <label>Grand Total Amount:</label>
  <span id="discounted_price<?= $order_id ?>" class="font-weight-bold">₱0.00</span>
</div>

<!-- Show VAT Amount -->
<div class="form-group">
  <label>VAT (12%):</label>
  <span id="vat_amount<?= $order_id ?>" class="font-weight-bold">₱0.00</span>
</div>

<!-- Change Display -->
<div class="form-group">
  <label>Change:</label>
  <span id="change_amount<?= $order_id ?>" class="font-weight-bold">₱0.00</span>
</div>



          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary preview_receipt" data-id="<?php echo $order_id ?>" >Preview Receipt</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>


<?php endwhile; ?>


				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- Receipt Preview Modal -->
<div class="modal fade" id="receiptPreviewModal" tabindex="-1" role="dialog" aria-labelledby="receiptPreviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="receiptPreviewModalLabel">
          <i class="fa fa-receipt mr-2"></i>Receipt Preview
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <iframe id="receiptPreviewFrame" style="width: 100%; height: 500px; border: 1px solid #ddd;"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="confirmPaymentFromPreview">
          <i class="fa fa-check mr-1"></i>Confirm Payment
        </button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fa fa-times mr-1"></i>Cancel
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Admin Authentication Modal for Delete Order -->
<div class="modal fade" id="adminAuthModal" tabindex="-1" role="dialog" aria-labelledby="adminAuthModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="adminAuthModalLabel">
          <i class="fa fa-shield-alt mr-2"></i>Admin Authentication Required
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="adminAuthForm">
        <div class="modal-body">
          <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle mr-2"></i>
            <strong>Warning:</strong> You are about to permanently delete this order. Admin credentials are required to proceed.
          </div>
          
          <div class="form-group">
            <label for="admin_username">
              <i class="fa fa-user mr-1"></i>Admin Username:
            </label>
            <input type="text" class="form-control" id="admin_username" name="admin_username" required autocomplete="username">
          </div>
          
          <div class="form-group">
            <label for="admin_password">
              <i class="fa fa-lock mr-1"></i>Admin Password:
            </label>
            <div class="input-group">
              <input type="password" class="form-control" id="admin_password" name="admin_password" required autocomplete="current-password">
              <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                  <i class="fa fa-eye"></i>
                </button>
              </div>
            </div>
          </div>
          
          <div id="authError" class="alert alert-danger d-none">
            <i class="fa fa-times-circle mr-2"></i>
            <span id="authErrorMessage"></span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fa fa-times mr-1"></i>Cancel
          </button>
          <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">
            <i class="fa fa-trash mr-1"></i>Confirm Delete
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>

  function isValidCardNumber(cardNumber) {
    // Remove all non-digit characters
    cardNumber = cardNumber.replace(/\D/g, '');
    if (cardNumber.length < 12 || cardNumber.length > 19) return false; // Typical card length

    let sum = 0;
    let shouldDouble = false;
    // Luhn algorithm
    for (let i = cardNumber.length - 1; i >= 0; i--) {
        let digit = parseInt(cardNumber.charAt(i));
        if (shouldDouble) {
            digit *= 2;
            if (digit > 9) digit -= 9;
        }
        sum += digit;
        shouldDouble = !shouldDouble;
    }
    return (sum % 10) === 0;
}
	$(document).ready(function(){
		// Store the order ID for deletion
		let orderToDelete = null;
		let itemToDelete = null;
		let deleteType = 'order'; // 'order' or 'item'
		
		$('.delete_data').click(function(){
			orderToDelete = $(this).attr('data-id');
			deleteType = 'order';
			$('#adminAuthModal').modal('show');
			$('#adminAuthModalLabel').html('<i class="fa fa-shield-alt mr-2"></i>Admin Authentication Required - Delete Order');
			// Clear previous form data and errors
			$('#adminAuthForm')[0].reset();
			$('#authError').addClass('d-none');
		});
		
		// Handle individual item deletion
		$(document).on('click', '.delete_order_item', function(){
			let itemData = {
				orderId: $(this).attr('data-order-id'),
				itemId: $(this).attr('data-item-id'),
				itemName: $(this).attr('data-item-name')
			};
			
			itemToDelete = itemData;
			deleteType = 'item';
			$('#adminAuthModal').modal('show');
			$('#adminAuthModalLabel').html('<i class="fa fa-shield-alt mr-2"></i>Admin Authentication Required - Delete Item');
			// Clear previous form data and errors
			$('#adminAuthForm')[0].reset();
			$('#authError').addClass('d-none');
		});
		
		// Store payment data globally for preview and confirmation
		var currentPaymentData = {};
		
		$('.preview_receipt').click(function(){
    var order_id = $(this).attr('data-id');
    var modal = $('#billoutConfirmationModal' + order_id);
    var payment_method = modal.find('input[name="payment_method"]:checked').val();

    if(payment_method === 'credit_card') {
        var card_number = modal.find('#card_number' + order_id).val().trim();
        if(!isValidCardNumber(card_number)) {
            alert_toast('Please enter a valid credit card number.', 'error');
            modal.find('#card_number' + order_id).focus();
            return false;
        }
    }

  var cash = parseFloat(modal.find('#cash_amount' + order_id).val()) || 0;
  var grand_total = parseFloat(modal.find('#discounted_price' + order_id).text().replace(/[^\d.]/g, '')) || 0;

    // Cash validation
    if(payment_method === 'cash') {
        if(!cash || cash <= 0) {
            alert_toast('Please enter the cash amount.', 'error');
            modal.find('#cash_amount' + order_id).focus();
            return false;
        }
        if(cash < grand_total) {
            alert_toast('Insufficient cash amount.', 'error');
            modal.find('#cash_amount' + order_id).focus();
            return false;
        }
    }

  // Require discount code if a discount is selected
  var selectedDiscount = modal.find('#discount_type' + order_id).val() || 'N/A';
  var selectedDiscountCode = modal.find('#discount_code' + order_id).val() || '';
  if(selectedDiscount !== 'N/A' && selectedDiscountCode.trim() === '') {
    alert_toast('Please enter a discount code.', 'error');
    modal.find('#discount_code' + order_id).focus();
    return false;
  }

  // Gather payment details
    var cash = modal.find('#cash_amount' + order_id).val() || 0;
    var change = modal.find('#change_amount' + order_id).text().replace(/[^\d.]/g, '') || 0;
    var payment_method = modal.find('input[name="payment_method"]:checked').val();
    var discount_type = modal.find('#discount_type' + order_id).val() || '';
    var discount_percent = modal.find('#discount_type' + order_id + ' option:selected').data('percent') || 0;
    var subtotal = modal.find('#total_amount' + order_id).text().replace(/[^\d.]/g, '') || 0;
    var grand_total = modal.find('#discounted_price' + order_id).text().replace(/[^\d.]/g, '') || 0;
    var emoney_reference = modal.find('#emoney_ref' + order_id).val() || '';
    var card_number = modal.find('#card_number' + order_id).val() || '';
  var vat = modal.find('#vat_amount' + order_id).text().replace(/[^\d.]/g, '') || 0;
  var discount_code = modal.find('#discount_code' + order_id).val() || '';
  
    // Store payment data for later confirmation
    currentPaymentData = {
        order_id: order_id,
        cash: cash,
        change: change,
        payment_method: payment_method,
        discount_type: discount_type,
        discount_percent: discount_percent,
        discount_code: discount_code,
        subtotal: subtotal,
        grand_total: grand_total,
        emoney_reference: emoney_reference,
        card_number: card_number,
        vat: vat
    };
    
    // Build receipt preview URL
  var url = _base_url_ + "admin/sales/receipt.php?id=" + order_id + 
    "&cash=" + encodeURIComponent(cash) +
    "&change=" + encodeURIComponent(change) +
    "&payment_method=" + encodeURIComponent(payment_method) +
    "&discount_percent=" + encodeURIComponent(discount_percent) +
    "&discount_type=" + encodeURIComponent(discount_type) +
  "&discount_code=" + encodeURIComponent(discount_code) +
    "&grand_total=" + encodeURIComponent(grand_total) +
    "&total_amount=" + encodeURIComponent(subtotal) +
    "&emoney_reference=" + encodeURIComponent(emoney_reference) +
    "&card_number=" + encodeURIComponent(card_number) +
    "&vat_amount=" + encodeURIComponent(vat);
    
    // Load receipt in iframe
    $('#receiptPreviewFrame').attr('src', url);
    
    // Hide billout modal and show preview modal
    modal.modal('hide');
    $('#receiptPreviewModal').modal('show');
});

// Confirm payment from preview modal
$('#confirmPaymentFromPreview').click(function(){
    var data = currentPaymentData;
    
    if(!data.order_id) {
        alert_toast('No payment data found.', 'error');
        return;
    }
    
    // Show loading
    $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Processing...');
    
    // Update the database
    $.ajax({
        url: 'orders/process_payment.php',
        method: 'POST',
        data: {
            order_id: data.order_id,
            payment_method: data.payment_method,
            cash_amount: data.cash,
            discount_type: data.discount_type,
            discount_percent: data.discount_percent,
            discount_code: data.discount_code,
            vat: data.vat,
            subtotal: data.subtotal,
            grand_total: data.grand_total,
            change: data.change,
            emoney_reference: data.emoney_reference,
            card_number: data.card_number,
            vat_amount: data.vat
        },
        dataType: 'json',
        success: function(resp) {
            if(resp.status === 'success') {
                alert_toast('Payment confirmed!', 'success');
                
                // Print the receipt
                var url = _base_url_ + "admin/sales/receipt.php?id=" + data.order_id + 
                    "&cash=" + encodeURIComponent(data.cash) +
                    "&change=" + encodeURIComponent(data.change) +
                    "&payment_method=" + encodeURIComponent(data.payment_method) +
                    "&discount_percent=" + encodeURIComponent(data.discount_percent) +
                    "&discount_type=" + encodeURIComponent(data.discount_type) +
                    "&discount_code=" + encodeURIComponent(data.discount_code) +
                    "&grand_total=" + encodeURIComponent(data.grand_total) +
                    "&total_amount=" + encodeURIComponent(data.subtotal) +
                    "&emoney_reference=" + encodeURIComponent(data.emoney_reference) +
                    "&card_number=" + encodeURIComponent(data.card_number) +
                    "&vat_amount=" + encodeURIComponent(data.vat);
                
                var nw = window.open(url, '_blank', 
                    "width=" + ($(window).width() * .8) + ",left=" + ($(window).width() * .1) + 
                    ",height=" + ($(window).height() * .8) + ",top=" + ($(window).height() * .1));
                    
                setTimeout(() => {
                    nw.print();
                    setTimeout(() => {
                        nw.close();
                        location.reload();
                    }, 300);
                }, 200);
            } else {
                alert_toast('Failed to confirm payment: ' + (resp.msg || 'Unknown error'), 'error');
                console.log(resp.msg);
                $('#confirmPaymentFromPreview').prop('disabled', false).html('<i class="fa fa-check mr-1"></i>Confirm Payment');
            }
        },
        error: function(xhr) {
            alert_toast('Server error: ' + xhr.responseText, 'error');
            $('#confirmPaymentFromPreview').prop('disabled', false).html('<i class="fa fa-check mr-1"></i>Confirm Payment');
        }
    });
});

	$('.billout_receipt').click(function(){
    var order_id = $(this).attr('data-id');
    // Try to get the cash amount from the billout modal if open
    var cash = 0;
    var change = 0;
    var modal = $('#billoutConfirmationModal' + order_id);
    if(modal.length && modal.is(':visible')) {
        cash = modal.find('#cash_amount' + order_id).val() || 0;
        change = modal.find('#change_amount' + order_id).text().replace(/[^\d.]/g, '') || 0;
    }
    var url = _base_url_ + "admin/sales/billout.php?id=" + order_id + "&cash=" + encodeURIComponent(cash) + "&change=" + encodeURIComponent(change);
    var nw = window.open(url, '_blank', 
        "width=" + ($(window).width() * .8) + ",left=" + ($(window).width() * .1) + 
        ",height=" + ($(window).height() * .8) + ",top=" + ($(window).height() * .1));
    setTimeout(() => {
        nw.print();
        setTimeout(() => {
            nw.close();
          $.ajax({
    url: 'orders/billout.php',
    method: 'POST',
    data: {
        order_id: order_id,
    },
    dataType: 'json',
    success: function(resp) {
        if(resp.status === 'success') {
            alert_toast('Order has been billed out.', 'success'); 
            setTimeout(function() {
                location.reload();
            }, 800);
        } else {
            alert_toast('Failed to update status.', 'error');
        }
    },
    error: function(xhr) {
        alert_toast('Server error: ' + xhr.responseText, 'error');
    }
});
        }, 300);
    }, 200);
});

// Print receipt for paid orders
$(document).on('click', '.view_receipt', function(){
    var order_id = $(this).attr('data-id');
    // Open the receipt page, which fetches all info from the database using order_id
    var url = _base_url_ + "admin/orders/paid_receipt.php?id=" + order_id;
    var nw = window.open(url, '_blank', 
        "width=" + ($(window).width() * .8) + ",left=" + ($(window).width() * .1) + 
        ",height=" + ($(window).height() * .8) + ",top=" + ($(window).height() * .1));
    setTimeout(() => {
        nw.print();
        setTimeout(() => {
            nw.close();
        }, 300);
    }, 200);
});

    // 		$('.confirm_payment').click(function(){


          
		// 	var nw = window.open(_base_url_ + "admin/sales/receipt.php?id=" + $(this).attr('data-id'), '_blank', 
		// 		"width=" + ($(window).width() * .8) + ",left=" + ($(window).width() * .1) + 
		// 		",height=" + ($(window).height() * .8) + ",top=" + ($(window).height() * .1));
		// 	setTimeout(() => {
		// 		nw.print();
		// 		setTimeout(() => {
		// 			nw.close();
		// 			location.reload();
		// 		}, 300);
		// 	}, 200);
		// });
		
		// Admin authentication form submission
		$('#adminAuthForm').submit(function(e){
			e.preventDefault();
			
			let username = $('#admin_username').val().trim();
			let password = $('#admin_password').val();
			
			if(!username || !password) {
				showAuthError('Please enter both username and password');
				return;
			}
			
			// Disable submit button
			$('#confirmDeleteBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Verifying...');
			
			// Verify admin credentials
			$.ajax({
				url: 'orders/verify_admin.php',
				method: 'POST',
				data: {
					username: username,
					password: password
				},
				dataType: 'json',
				success: function(resp) {
					if(resp.status === 'success') {
						// Hide the modal and proceed with deletion
						$('#adminAuthModal').modal('hide');
						
						if(deleteType === 'order') {
							// Show confirmation dialog for order deletion
							_conf("Are you sure to delete this order permanently?", "delete_order", [orderToDelete]);
						} else if(deleteType === 'item') {
							// Show confirmation dialog for item deletion - pass individual values instead of object
							_conf("Are you sure to delete '" + itemToDelete.itemName + "' from this order?", "delete_order_item", [itemToDelete.orderId, itemToDelete.itemId, "'" + itemToDelete.itemName + "'"]);
						}
						
						// Ensure the Continue button in confirmation modal is clickable
						setTimeout(function() {
							$('#confirm_modal #confirm').removeAttr('aria-hidden').css({
								'pointer-events': 'auto',
								'z-index': '9999',
								'position': 'relative'
							});
						}, 100);
					} else {
						showAuthError(resp.message || 'Invalid admin credentials');
					}
				},
				error: function(xhr) {
					console.error('Admin verification error:', xhr);
					showAuthError('Server error occurred. Please try again.');
				},
				complete: function() {
					// Re-enable submit button
					$('#confirmDeleteBtn').prop('disabled', false).html('<i class="fa fa-trash mr-1"></i>Confirm Delete');
				}
			});
		});
		
		// Toggle password visibility
		$('#togglePassword').click(function() {
			let passwordField = $('#admin_password');
			let icon = $(this).find('i');
			
			if (passwordField.attr('type') === 'password') {
				passwordField.attr('type', 'text');
				icon.removeClass('fa-eye').addClass('fa-eye-slash');
			} else {
				passwordField.attr('type', 'password');
				icon.removeClass('fa-eye-slash').addClass('fa-eye');
			}
		});
		
		// Clear form when modal is hidden
		$('#adminAuthModal').on('hidden.bs.modal', function() {
			$('#adminAuthForm')[0].reset();
			$('#authError').addClass('d-none');
			orderToDelete = null;
			itemToDelete = null;
			deleteType = 'order';
		});
		
		// Ensure proper focus management for admin auth modal
		$('#adminAuthModal').on('shown.bs.modal', function () {
			$(this).removeAttr('aria-hidden');
			$('#admin_username').focus();
		});

		// Global fix for Bootstrap modal accessibility issues
		$(document).on('shown.bs.modal', '.modal', function () {
			$(this).removeAttr('aria-hidden');
			// Ensure all buttons in the modal are clickable
			$(this).find('button').removeAttr('aria-hidden').css('pointer-events', 'auto');
			
			// Focus on the first focusable element in the modal
			var $modal = $(this);
			setTimeout(function() {
				var $focusable = $modal.find('input:not([readonly]):not([disabled]), button:not([disabled]), select:not([disabled]), textarea:not([readonly]):not([disabled])').first();
				if ($focusable.length) {
					$focusable.focus();
				}
			}, 100);
		});
		
		// Specific fix for confirmation modal (from admin/index.php)
		$(document).on('shown.bs.modal', '#confirm_modal', function () {
			$(this).removeAttr('aria-hidden');
			$('#confirm_modal #confirm').removeAttr('aria-hidden').css({
				'pointer-events': 'auto !important',
				'z-index': '9999'
			});
		});
		
		// Restore aria-hidden when modal is hidden - but keep confirm button functional
		$(document).on('hidden.bs.modal', '.modal', function () {
			if ($(this).attr('id') !== 'confirm_modal') {
				$(this).attr('aria-hidden', 'true');
			}
		});

		$('.table').dataTable({
			columnDefs: [
				{ orderable: false, targets: [6] }
			],
			order: [0, 'asc']
		});
		$('.dataTable td, .dataTable th').addClass('py-2 px-3 align-middle');
	});
	
	// Function to show authentication error
	function showAuthError(message) {
		$('#authErrorMessage').text(message);
		$('#authError').removeClass('d-none');
	}
	
	function delete_order($id){
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=delete_order",
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
	
	function delete_order_item(orderId, itemId, itemName){
		start_loader();
		$.ajax({
			url: 'orders/delete_order_item.php',
			method: "POST",
			data: { 
				order_id: orderId,
				item_id: itemId
			},
			dataType: "json",
			error: err => {
				console.log(err);
				alert_toast("An error occurred while deleting the item.", 'error');
				end_loader();
			},
			success: function(resp){
				if (typeof resp == 'object' && resp.status == 'success') {
					alert_toast("Item '" + resp.deleted_item + "' has been removed from the order.", 'success');
					setTimeout(function() {
						location.reload();
					}, 800);
				} else {
					alert_toast(resp.message || "An error occurred while deleting the item.", 'error');
				}
				end_loader();
			}
		})
	}

	$(document).on('submit', 'form[id^="addOrderForm"]', function(e) {
  e.preventDefault();
  var $form = $(this);
  var formData = $form.serialize() + '&ajax=1';
  $.ajax({
    url: 'orders/add_order_item.php',
    method: 'POST',
    data: formData,
    dataType: 'json',
    success: function(resp) {
      if(resp.status === 'success') {
        alert_toast('Order item added!', 'success');
        setTimeout(function() {
          location.reload();
        }, 800);
      } else {
        alert_toast('Failed to add item.', 'error');
      }
    },
    error: function() {
      alert_toast('Server error.', 'error');
    }
  });
});


$(document).on('change', '.payment-method', function() {
  var selectedMethod = $(this).val();
  var modal = $(this).closest('.modal');

  modal.find('.payment-input').addClass('d-none');

  if (selectedMethod === 'cash') {
    modal.find('.cash-input').removeClass('d-none');
  } else if (selectedMethod === 'emoney') {
    modal.find('.emoney-input').removeClass('d-none');
  } else if (selectedMethod === 'credit_card') {
    modal.find('.credit-card-input').removeClass('d-none');
  }
});
$(document).ready(function() {
  // For each Bill Out modal
  $('[id^="billoutConfirmationModal"]').each(function() {
    var modal = $(this);
    var order_id = modal.attr('id').replace('billoutConfirmationModal', '');

    // Discount change
    modal.find('#discount_type' + order_id).on('change', function() {
      recalcBillout();
      var val = $(this).val() || 'N/A';
      if (val !== 'N/A') {
        modal.find('#discount_code_group' + order_id).removeClass('d-none');
      } else {
        modal.find('#discount_code_group' + order_id).addClass('d-none');
        modal.find('#discount_code' + order_id).val('');
      }
    });

    // Cash input change
    modal.find('#cash_amount' + order_id).on('input', function() {
      updateChange();
    });

function recalcBillout() {
  var total = parseFloat(modal.find('#total_amount' + order_id).text().replace(/[^\d.]/g, '')) || 0;
  var discountType = modal.find('#discount_type' + order_id + ' option:selected').val().toLowerCase();
  var percent = parseFloat(modal.find('#discount_type' + order_id).find(':selected').data('percent')) || 0;

  // Check if discount is Senior Citizen or PWD
  var isVatExempt = (discountType.includes('senior') || discountType.includes('pwd'));

  var vat = isVatExempt ? 0 : total * 0.12;
  var grandTotalWithVAT = total + vat;
  modal.find('#vat_amount' + order_id).text('₱' + vat.toFixed(2));

  var discountAmount = grandTotalWithVAT * (percent / 100);

  var discountedPrice = grandTotalWithVAT - discountAmount;

  console.log('Total:', total, 'VAT:', vat, 'Discount Percent:', percent, 'Discount Amount:', discountAmount, 'Discounted Price:', discountedPrice);

  modal.find('#discount_percent' + order_id).text(percent + '%');
  modal.find('#discounted_price' + order_id).text('₱' + discountedPrice.toFixed(2));
  updateChange();
}
    function updateChange() {
      var discounted = parseFloat(modal.find('#discounted_price' + order_id).text().replace(/[^\d.]/g, '')) || 0;
      var cash = parseFloat(modal.find('#cash_amount' + order_id).val()) || 0;
      var change = cash - discounted;
      modal.find('#change_amount' + order_id).text('₱' + (change > 0 ? change.toFixed(2) : '0.00'));
    }

    // Initialize on modal show
    modal.on('shown.bs.modal', function() {
      modal.find('#discount_type' + order_id).trigger('change');
      // Ensure correct visibility of discount code group
      var currentVal = modal.find('#discount_type' + order_id).val() || 'N/A';
      if (currentVal !== 'N/A') {
        modal.find('#discount_code_group' + order_id).removeClass('d-none');
      } else {
        modal.find('#discount_code_group' + order_id).addClass('d-none');
        modal.find('#discount_code' + order_id).val('');
      }
      modal.find('#cash_amount' + order_id).val('');
      modal.find('#change_amount' + order_id).text('₱0.00');
    });
  });
});
</script>
