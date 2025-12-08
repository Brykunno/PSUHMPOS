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
    <button class="print-btn" onclick="window.print()"><i class="fas fa-print mr-1"></i>Print</button>
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

<style media="print">
  .dashboard-header,
  .print-btn,
  .card-header {
    page-break-inside: avoid;
  }
  
  body {
    background: white;
  }
  
  .card {
    box-shadow: none;
    border: 1px solid #ddd;
  }
</style>
