<?php
// Cashier Dashboard - Shows today's sales and performance
// Only accessible by cashiers (type 2)
if($_settings->userdata('type') != 2) {
  redirect('./?page=home');
}

$cashier_id = $_settings->userdata('id');
$cashier_name = $_settings->userdata('firstname') . ' ' . $_settings->userdata('lastname');
$today = date('Y-m-d');

// Function to format peso
function format_peso($amount) {
  return '₱ ' . number_format((float)$amount, 2);
}

// Get today's sales summary
$sales_summary = $conn->query("SELECT 
    COUNT(DISTINCT ol.id) as total_transactions,
    IFNULL(SUM(oi.quantity), 0) as total_items,
    IFNULL(SUM(oi.quantity * oi.price), 0) as total_sales
  FROM order_list ol
  LEFT JOIN order_items oi ON ol.id = oi.order_id AND oi.refunded = 0
  WHERE ol.user_id = '{$cashier_id}'
    AND DATE(ol.date_created) = '{$today}'
    AND ol.status IN (2,5)")->fetch_assoc();

// Get refunds today
$refunds_summary = $conn->query("SELECT 
    COUNT(*) as refund_count,
    IFNULL(SUM(amount), 0) as total_refunds
  FROM refunds
  WHERE user_id = '{$cashier_id}'
    AND DATE(date_created) = '{$today}'")->fetch_assoc();

// Get top items sold today
$top_items = $conn->query("SELECT 
    m.name as item_name,
    SUM(oi.quantity) as qty_sold,
    oi.price as unit_price,
    SUM(oi.quantity * oi.price) as total_amount
  FROM order_items oi
  INNER JOIN order_list ol ON oi.order_id = ol.id
  INNER JOIN menu_list m ON oi.menu_id = m.id
  WHERE ol.user_id = '{$cashier_id}'
    AND DATE(ol.date_created) = '{$today}'
    AND ol.status IN (2,5)
    AND oi.refunded = 0
  GROUP BY m.id, oi.price
  ORDER BY qty_sold DESC
  LIMIT 10");

// Calculate average sale
$avg_sale = $sales_summary['total_transactions'] > 0 ? $sales_summary['total_sales'] / $sales_summary['total_transactions'] : 0;

// Net sales (sales - refunds)
$net_sales = $sales_summary['total_sales'] - $refunds_summary['total_refunds'];
?>

<style>
  .dashboard-header {
    background: linear-gradient(45deg, #5b9bd5, #1c6cab);
    color: white;
    padding: 30px;
    border-radius: 8px;
    margin-bottom: 30px;
  }
  
  .dashboard-header h2 {
    margin: 0 0 10px 0;
    font-size: 28px;
  }
  
  .dashboard-header p {
    margin: 0;
    font-size: 16px;
    opacity: 0.9;
  }
  
  .summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
  }
  
  .summary-card {
    background: white;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-left: 5px solid #5b9bd5;
  }
  
  .summary-card.refund {
    border-left-color: #dc3545;
  }
  
  .summary-card.net {
    border-left-color: #28a745;
  }
  
  .summary-label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
  }
  
  .summary-value {
    font-size: 28px;
    font-weight: bold;
    color: #5b9bd5;
  }
  
  .summary-card.refund .summary-value {
    color: #dc3545;
  }
  
  .summary-card.net .summary-value {
    color: #28a745;
  }
  
  .sub-value {
    font-size: 14px;
    color: #999;
    margin-top: 8px;
  }
  
  .card {
    border-radius: 8px;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 25px;
  }
  
  .card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #5b9bd5;
    padding: 15px 20px;
  }
  
  .card-header h5 {
    margin: 0;
    color: #5b9bd5;
    font-weight: 600;
  }
  
  .card-body {
    padding: 20px;
  }
  
  .table {
    margin-bottom: 0;
  }
  
  .table th {
    background: #f8f9fa;
    border-top: none;
    border-bottom: 2px solid #dee2e6;
    color: #5b9bd5;
    font-weight: 600;
    padding: 12px 8px;
  }
  
  .table td {
    padding: 12px 8px;
    vertical-align: middle;
  }
  
  .table tbody tr:hover {
    background: #f9f9f9;
  }
  
  .empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #999;
  }
  
  .empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
  }
  
  .print-btn {
    background: #5b9bd5;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
  }
  
  .print-btn:hover {
    background: #1c6cab;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
  }

  /* Screen display styles - hide print-only elements */
  @media screen {
    .print-only {
      display: none !important;
    }
  }

  /* Print-only styles */
  @media print {
    /* Hide screen elements */
    .content-header,
    .btn,
    .main-sidebar,
    .main-header,
    .main-footer,
    nav,
    .no-print,
    .dashboard-header,
    .summary-grid,
    .print-btn {
      display: none !important;
    }

    body {
      margin: 0;
      padding: 20px;
      font-family: 'Arial', sans-serif;
      font-size: 11pt;
    }

    .content-wrapper {
      margin: 0 !important;
      padding: 0 !important;
    }

    /* Show print elements */
    .print-only {
      display: block !important;
    }

    /* Print Header Styles */
    .print-header {
      margin-bottom: 20px;
      page-break-inside: avoid;
    }

    .print-title-box {
      text-align: center;
      margin-bottom: 15px;
    }

    .print-divider {
      font-size: 10pt;
      color: #333;
      margin: 5px 0;
      letter-spacing: -1px;
    }

    .print-title-box h1 {
      font-size: 18pt;
      font-weight: bold;
      margin: 10px 0;
      color: #000;
      text-transform: uppercase;
    }

    .print-info {
      font-size: 10pt;
      margin-top: 10px;
    }

    .print-info p {
      margin: 3px 0;
    }

    /* Print Summary Box */
    .print-summary-box {
      border: 2px solid #000;
      padding: 15px;
      margin: 15px 0;
      background: #fff;
      page-break-inside: avoid;
    }

    .print-section-title {
      text-align: center;
      font-size: 12pt;
      font-weight: bold;
      margin-bottom: 10px;
      padding-bottom: 5px;
      border-bottom: 2px solid #000;
      text-transform: uppercase;
    }

    .print-summary-section {
      margin: 10px 0;
      padding: 10px 0;
    }

    .print-summary-section:not(:last-child) {
      border-bottom: 1px dashed #999;
    }

    .print-summary-section h4 {
      font-size: 10pt;
      font-weight: bold;
      margin: 0 0 8px 0;
      color: #333;
      text-transform: uppercase;
    }

    .print-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 5px 0;
      font-size: 10pt;
      line-height: 1.6;
    }

    .print-dots {
      flex: 1;
      border-bottom: 1px dotted #666;
      margin: 0 8px;
      height: 0.8em;
    }

    .print-value {
      white-space: nowrap;
      font-weight: bold;
      text-align: right;
      min-width: 100px;
    }

    .print-summary-section.net-sales {
      background: #f5f5f5;
      padding: 12px;
      margin-top: 10px;
      border: 2px solid #000;
      border-bottom: none;
    }

    .print-summary-section.net-sales .print-row {
      font-size: 11pt;
    }

    .print-summary-section.net-sales .print-value {
      font-size: 12pt;
    }

    /* Print Section Divider */
    .print-section-divider {
      display: flex;
      align-items: center;
      margin: 20px 0;
      font-size: 10pt;
      font-weight: bold;
      text-transform: uppercase;
    }

    .print-divider-line {
      flex: 1;
      height: 2px;
      background: #000;
    }

    .print-section-divider span {
      padding: 0 15px;
      white-space: nowrap;
    }

    /* Print Footer */
    .print-footer {
      margin-top: 30px;
      page-break-inside: avoid;
    }

    .print-footer-divider {
      border-top: 2px solid #000;
      margin: 10px 0;
    }

    .print-footer-content {
      text-align: center;
      font-size: 9pt;
    }

    .print-footer-content p {
      margin: 4px 0;
    }

    .print-footer-note {
      font-size: 8pt;
      color: #666;
      font-style: italic;
    }

    /* Cards in print */
    .card {
      border: 1px solid #000 !important;
      box-shadow: none !important;
      page-break-inside: avoid;
      margin-bottom: 15px !important;
    }

    .card-header {
      background: #f5f5f5 !important;
      color: #000 !important;
      border-bottom: 2px solid #000 !important;
      padding: 10px !important;
    }

    .card-title {
      font-size: 12pt !important;
      font-weight: bold !important;
    }

    /* Table styles */
    table {
      width: 100% !important;
      border-collapse: collapse !important;
      font-size: 9pt !important;
    }

    table thead {
      background: #f0f0f0 !important;
    }

    table th {
      border: 1px solid #000 !important;
      padding: 8px 4px !important;
      font-weight: bold !important;
      text-align: center !important;
    }

    table td {
      border: 1px solid #000 !important;
      padding: 6px 4px !important;
    }

    table tr:nth-child(even) {
      background: #f9f9f9 !important;
    }
  }
</style>

<div class="dashboard-header">
  <h2><i class="fas fa-user-tie mr-2"></i>Your Sales Dashboard</h2>
  <p>Today's Performance • <?php echo date('F d, Y'); ?></p>
</div>

<div class="summary-grid">
  <div class="summary-card">
    <div class="summary-label"><i class="fas fa-shopping-cart mr-2"></i>Total Sales</div>
    <div class="summary-value"><?php echo format_peso($sales_summary['total_sales']); ?></div>
    <div class="sub-value"><?php echo (int)$sales_summary['total_transactions']; ?> transactions</div>
  </div>
  
  <div class="summary-card">
    <div class="summary-label"><i class="fas fa-box mr-2"></i>Items Sold</div>
    <div class="summary-value"><?php echo (int)$sales_summary['total_items']; ?></div>
    <div class="sub-value">Avg per transaction: <?php echo (int)($sales_summary['total_transactions'] > 0 ? $sales_summary['total_items'] / $sales_summary['total_transactions'] : 0); ?></div>
  </div>
  
  <div class="summary-card">
    <div class="summary-label"><i class="fas fa-divide mr-2"></i>Average Sale</div>
    <div class="summary-value"><?php echo format_peso($avg_sale); ?></div>
    <div class="sub-value">Per transaction</div>
  </div>
  
  <div class="summary-card refund">
    <div class="summary-label"><i class="fas fa-undo mr-2"></i>Refunds</div>
    <div class="summary-value"><?php echo format_peso($refunds_summary['total_refunds']); ?></div>
    <div class="sub-value"><?php echo (int)$refunds_summary['refund_count']; ?> refund(s)</div>
  </div>
  
  <div class="summary-card net">
    <div class="summary-label"><i class="fas fa-coins mr-2"></i>Net Sales</div>
    <div class="summary-value"><?php echo format_peso($net_sales); ?></div>
    <div class="sub-value">Sales minus refunds</div>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><i class="fas fa-fire mr-2"></i>Top 10 Items Sold Today</h5>
    <button class="print-btn" onclick="requestAdminPrint()"><i class="fas fa-print mr-1"></i>Print</button>
  </div>
  <div class="card-body">
    <?php if($top_items && $top_items->num_rows > 0): ?>
    <table class="table table-hover">
      <thead>
        <tr>
          <th style="width: 5%">#</th>
          <th style="width: 50%">Item Name</th>
          <th style="width: 15%" class="text-center">Qty Sold</th>
          <th style="width: 15%" class="text-right">Unit Price</th>
          <th style="width: 15%" class="text-right">Total</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $rank = 1;
        while($item = $top_items->fetch_assoc()): 
        ?>
        <tr>
          <td><?php echo $rank++; ?></td>
          <td><?php echo htmlspecialchars($item['item_name']); ?></td>
          <td class="text-center"><strong><?php echo (int)$item['qty_sold']; ?></strong></td>
          <td class="text-right"><?php echo format_peso($item['unit_price']); ?></td>
          <td class="text-right"><strong><?php echo format_peso($item['total_amount']); ?></strong></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="empty-state">
      <i class="fas fa-inbox"></i>
      <p>No sales recorded yet today. Start taking orders!</p>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Print-only Header and Summary -->
<div class="print-only print-header">
  <div class="print-title-box">
    <div class="print-divider">═══════════════════════════════════════════════</div>
    <h1>DAILY SALES REPORT</h1>
    <div class="print-divider">═══════════════════════════════════════════════</div>
    <div class="print-info">
      <p><strong>Cashier:</strong> <?php echo strtoupper($cashier_name); ?></p>
      <p><strong>Date:</strong> <?php echo date('F d, Y (l)'); ?></p>
      <p><strong>Time Generated:</strong> <?php echo date('h:i A'); ?></p>
    </div>
  </div>
  
  <div class="print-summary-box">
    <div class="print-section-title">SALES SUMMARY</div>
    
    <div class="print-summary-section">
      <h4>GROSS SALES</h4>
      <div class="print-row">
        <span>Total Sales</span>
        <span class="print-dots"></span>
        <span class="print-value"><?php echo format_peso($sales_summary['total_sales']); ?></span>
      </div>
      <div class="print-row">
        <span>Total Transactions</span>
        <span class="print-dots"></span>
        <span class="print-value"><?php echo (int)$sales_summary['total_transactions']; ?> orders</span>
      </div>
      <div class="print-row">
        <span>Items Sold</span>
        <span class="print-dots"></span>
        <span class="print-value"><?php echo (int)$sales_summary['total_items']; ?> items</span>
      </div>
      <div class="print-row">
        <span>Average per Transaction</span>
        <span class="print-dots"></span>
        <span class="print-value"><?php echo format_peso($avg_sale); ?></span>
      </div>
    </div>
    
    <div class="print-summary-section">
      <h4>DEDUCTIONS</h4>
      <div class="print-row">
        <span>Refunds</span>
        <span class="print-dots"></span>
        <span class="print-value"><?php echo format_peso($refunds_summary['total_refunds']); ?></span>
      </div>
      <div class="print-row">
        <span>Number of Refunds</span>
        <span class="print-dots"></span>
        <span class="print-value"><?php echo (int)$refunds_summary['refund_count']; ?> refund(s)</span>
      </div>
    </div>
    
    <div class="print-summary-section net-sales">
      <h4>NET SALES</h4>
      <div class="print-row">
        <span>Total Net Sales</span>
        <span class="print-dots"></span>
        <span class="print-value"><strong><?php echo format_peso($net_sales); ?></strong></span>
      </div>
    </div>
  </div>
  
  <div class="print-section-divider">
    <div class="print-divider-line"></div>
    <span>TOP 10 BEST SELLING ITEMS TODAY</span>
    <div class="print-divider-line"></div>
  </div>
</div>

<!-- Print-only Footer -->
<div class="print-only print-footer">
  <div class="print-footer-divider"></div>
  <div class="print-footer-content">
    <p><strong>Document Details:</strong></p>
    <p>Printed on: <?php echo date('F d, Y'); ?> at <?php echo date('h:i:s A'); ?></p>
    <p>Prepared by: <strong><?php echo $cashier_name; ?></strong></p>
    <p>Verified by: Admin</p>
    <p class="print-footer-note">This is a computer-generated document and requires no signature.</p>
    <p class="print-footer-note">For inquiries, please contact the management.</p>
  </div>
  <div class="print-footer-divider"></div>
</div>

<!-- Admin Verification Modal for Print -->
<div class="modal fade" id="adminVerificationModal" tabindex="-1" role="dialog" aria-labelledby="adminVerificationLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title" id="adminVerificationLabel">
          <i class="fas fa-lock mr-2"></i>Admin Verification Required
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p class="text-muted mb-3">Please enter admin credentials to print this sales report.</p>
        <form id="adminVerifyForm">
          <div class="form-group">
            <label for="admin_username">Admin Username</label>
            <input type="text" class="form-control" id="admin_username" name="username" required autocomplete="off">
          </div>
          <div class="form-group">
            <label for="admin_password">Admin Password</label>
            <input type="password" class="form-control" id="admin_password" name="password" required autocomplete="off">
          </div>
          <div id="verify_error" class="alert alert-danger d-none" role="alert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-warning" onclick="verifyAndPrint()">
          <i class="fas fa-check mr-1"></i>Verify & Print
        </button>
      </div>
    </div>
  </div>
</div>

<script>
function requestAdminPrint() {
  // Clear previous input
  $('#adminVerifyForm')[0].reset();
  $('#verify_error').addClass('d-none');
  // Show modal
  $('#adminVerificationModal').modal('show');
}

function verifyAndPrint() {
  var username = $('#admin_username').val().trim();
  var password = $('#admin_password').val();
  
  if(!username || !password) {
    $('#verify_error').text('Please enter both username and password').removeClass('d-none');
    return;
  }
  
  // Show loading
  $('#verify_error').addClass('d-none');
  $('button[onclick="verifyAndPrint()"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Verifying...');
  
  $.ajax({
    url: _base_url_ + 'admin/orders/verify_admin.php',
    method: 'POST',
    data: {
      username: username,
      password: password
    },
    dataType: 'json',
    success: function(resp) {
      if(resp.status === 'success') {
        // Close modal
        $('#adminVerificationModal').modal('hide');
        // Trigger print
        setTimeout(function() {
          window.print();
        }, 300);
      } else {
        $('#verify_error').text(resp.msg || 'Invalid admin credentials').removeClass('d-none');
      }
    },
    error: function() {
      $('#verify_error').text('An error occurred. Please try again.').removeClass('d-none');
    },
    complete: function() {
      $('button[onclick="verifyAndPrint()"]').prop('disabled', false).html('<i class="fas fa-check mr-1"></i>Verify & Print');
    }
  });
}

// Allow Enter key to submit
$('#adminVerificationModal').on('shown.bs.modal', function() {
  $('#admin_username').focus();
});

$('#adminVerifyForm input').on('keypress', function(e) {
  if(e.which === 13) {
    e.preventDefault();
    verifyAndPrint();
  }
});
</script>

<style media="print">
  /* Hide screen-only elements */
  .dashboard-header,
  .print-btn,
  .card-header,
  .summary-grid,
  .btn,
  .navbar,
  .sidebar,
  .main-sidebar,
  .content-wrapper > .content-header,
  nav,
  footer,
  .modal,
  .modal-backdrop {
    display: none !important;
  }
  
  /* Reset page styles */
  * {
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  
  body {
    background: white !important;
    margin: 0;
    padding: 0;
    font-family: 'Arial', sans-serif;
    font-size: 11pt;
    color: #000;
  }
  
  .content-wrapper {
    margin: 0 !important;
    padding: 0 !important;
  }
  
  /* Print page settings */
  @page {
    size: A4;
    margin: 15mm 15mm 20mm 15mm;
  }
  
  .card {
    box-shadow: none;
    border: none;
    page-break-inside: avoid;
    margin: 0;
    padding: 0;
  }
  
  .card-body {
    padding: 0;
  }
  
  /* Professional Print Header */
  .card:first-of-type::before {
    content: "";
    display: block;
    text-align: center;
    margin-bottom: 25px;
    padding: 20px 0;
    border: 3px double #000;
    background: #f8f8f8;
  }
  
  /* Company/Title Header */
  .card:first-of-type::before {
    content: "═════════════════════════════════════════\A\A DAILY SALES REPORT\A\A═════════════════════════════════════════\A\A Cashier: <?php echo strtoupper($cashier_name); ?>\A Date: <?php echo date('F d, Y (l)'); ?>\A Time Generated: <?php echo date('h:i A'); ?>";
    white-space: pre-line;
    font-weight: bold;
    text-align: center;
    font-size: 13pt;
    line-height: 1.8;
    padding: 20px;
    margin-bottom: 30px;
    background: linear-gradient(to bottom, #f5f5f5 0%, #ffffff 100%);
    border: 2px solid #000;
    border-radius: 8px;
  }
  
  /* Sales Summary Box */
  .card:first-of-type::after {
    content: "┌─────────────────────────── SALES SUMMARY ───────────────────────────┐\A│\A│  GROSS SALES\A│  Total Sales .......................... <?php echo str_pad(format_peso($sales_summary['total_sales']), 25, ' ', STR_PAD_LEFT); ?>\A│  Total Transactions ................... <?php echo str_pad((int)$sales_summary['total_transactions'] . ' orders', 25, ' ', STR_PAD_LEFT); ?>\A│  Items Sold ........................... <?php echo str_pad((int)$sales_summary['total_items'] . ' items', 25, ' ', STR_PAD_LEFT); ?>\A│  Average per Transaction .............. <?php echo str_pad(format_peso($avg_sale), 25, ' ', STR_PAD_LEFT); ?>\A│\A│  DEDUCTIONS\A│  Refunds .............................. <?php echo str_pad(format_peso($refunds_summary['total_refunds']), 25, ' ', STR_PAD_LEFT); ?>\A│  Number of Refunds .................... <?php echo str_pad((int)$refunds_summary['refund_count'] . ' refund(s)', 25, ' ', STR_PAD_LEFT); ?>\A│\A│  NET SALES\A│  Total Net Sales ...................... <?php echo str_pad(format_peso($net_sales), 25, ' ', STR_PAD_LEFT); ?>\A│\A└──────────────────────────────────────────────────────────────────────┘\A\A\A━━━━━━━━━━━━━━━━━ TOP 10 BEST SELLING ITEMS TODAY ━━━━━━━━━━━━━━━━━\A";
    white-space: pre-line;
    display: block;
    margin: 25px 0 20px 0;
    font-size: 10pt;
    line-height: 1.6;
    font-family: 'Courier New', monospace;
    padding: 15px;
    background: #fafafa;
    border: 1px solid #ddd;
    border-radius: 5px;
  }
  
  /* Table styling for print */
  .table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    font-size: 10pt;
    background: white;
  }
  
  .table thead {
    background: #333 !important;
    color: white !important;
  }
  
  .table th {
    background: #333 !important;
    color: white !important;
    padding: 10px 8px;
    text-align: left;
    font-weight: bold;
    border: 1px solid #000;
    font-size: 10pt;
  }
  
  .table td {
    padding: 8px;
    border: 1px solid #ccc;
    background: white;
  }
  
  .table tbody tr:nth-child(even) {
    background: #f9f9f9 !important;
  }
  
  .table tbody tr:hover {
    background: #f0f0f0 !important;
  }
  
  .table tbody tr:last-child {
    border-bottom: 3px double #000;
  }
  
  .text-center {
    text-align: center !important;
  }
  
  .text-right {
    text-align: right !important;
  }
  
  /* Empty state */
  .empty-state {
    padding: 30px;
    text-align: center;
    font-style: italic;
    color: #666;
    border: 2px dashed #ccc;
    border-radius: 5px;
    margin: 20px 0;
    background: #f9f9f9;
  }
  
  .empty-state i {
    display: none;
  }
  
  /* Professional Footer */
  .card:last-of-type::after {
    content: "\A\A━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\A\A Document Details:\A Printed on: <?php echo date('F d, Y'); ?> at <?php echo date('h:i:s A'); ?>\A Prepared by: <?php echo $cashier_name; ?>\A Verified by: Admin\A\A This is a computer-generated document and requires no signature.\A For inquiries, please contact the management.\A\A━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━";
    white-space: pre-line;
    display: block;
    margin-top: 40px;
    text-align: center;
    font-size: 8pt;
    color: #555;
    padding: 20px 0;
    border-top: 2px solid #000;
    line-height: 1.8;
  }
  
  /* Improve readability */
  strong {
    font-weight: 700;
    color: #000;
  }
  
  /* Page breaks */
  .card {
    page-break-after: auto;
  }
  
  tr {
    page-break-inside: avoid;
  }
  
  /* Add subtle watermark effect */
  body::after {
    content: "CONFIDENTIAL";
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-45deg);
    font-size: 80pt;
    color: rgba(0, 0, 0, 0.03);
    z-index: -1;
    font-weight: bold;
    pointer-events: none;
  }
</style>
