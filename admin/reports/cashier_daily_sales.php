<?php
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date("Y-m-d");
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date("Y-m-d");
$selected_cashier = isset($_GET['cashier_id']) ? (int)$_GET['cashier_id'] : 0;
$cashier_filter = $selected_cashier > 0 ? " AND ol.user_id = {$selected_cashier}" : "";
$selected_cashier_label = '';
function format_peso($amount){ return '₱ ' . number_format((float)$amount, 2); }
?>

<style>
  .cashier-header {
    background: linear-gradient(45deg, #5b9bd5, #1c6cab);
    color: white;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 8px;
  }
  .cashier-section {
    margin-bottom: 30px;
    page-break-inside: avoid;
  }
  .cashier-name {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
    color: #5b9bd5;
  }
  .cashier-summary {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-bottom: 15px;
  }
  .summary-card {
    background: #f8f9fa;
    padding: 10px;
    border-left: 4px solid #5b9bd5;
    border-radius: 4px;
  }
  .summary-label {
    font-size: 12px;
    color: #666;
  }
  .summary-value {
    font-size: 18px;
    font-weight: bold;
    color: #5b9bd5;
  }
  .items-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    font-size: 13px;
  }
  .items-table th {
    background: #5b9bd5;
    color: white;
    padding: 8px;
    text-align: left;
  }
  .items-table td {
    padding: 8px;
    border-bottom: 1px solid #ddd;
  }
  .items-table tr:hover {
    background: #f9f9f9;
  }
</style>

<?php
$cashier_options = $conn->query("SELECT DISTINCT 
    u.id,
    u.username,
    u.firstname,
    u.lastname
  FROM order_list ol
  INNER JOIN users u ON ol.user_id = u.id
  WHERE DATE(ol.date_created) BETWEEN '{$start_date}' AND '{$end_date}'
    AND ol.status IN (2,5)
  ORDER BY u.username ASC");
?>

<div class="content py-5 px-3" style="background: linear-gradient(45deg, #5b9bd5, #1c6cab); color: white;">
  <h2>Cashier Daily Sales Report</h2>
</div>

<div class="row mt-4 justify-content-center">
  <div class="col-lg-11">
    <div class="card shadow-sm border-0 mb-2">
      <div class="card-header">
        <h3 class="card-title mb-0"><i class="fa fa-filter mr-2"></i>Filter</h3>
      </div>
      <div class="card-body">
        <form action="" id="filter-form">
          <div class="row align-items-end">
            <div class="col-md-4">
              <label>Start Date</label>
              <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>" required>
            </div>
            <div class="col-md-3">
              <label>End Date</label>
              <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>" required>
            </div>
            <div class="col-md-3">
              <label>Cashier</label>
              <select name="cashier_id" class="form-control">
                <option value="0">All Cashiers</option>
                <?php if($cashier_options && $cashier_options->num_rows > 0): ?>
                  <?php while($co = $cashier_options->fetch_assoc()):
                    $label = $co['firstname'] . ' ' . $co['lastname'] . ' (' . $co['username'] . ')';
                    if($selected_cashier && (int)$co['id'] === $selected_cashier) $selected_cashier_label = $label;
                  ?>
                    <option value="<?= $co['id'] ?>" <?= $selected_cashier == $co['id'] ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                  <?php endwhile; ?>
                <?php else: ?>
                  <option value="" disabled>No cashiers found</option>
                <?php endif; ?>
              </select>
            </div>
            <div class="col-md-2">
              <button class="btn btn-primary mt-4 btn-block"><i class="fa fa-filter"></i> Filter</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-header d-flex justify-content-between align-items-center py-2">
        <h5 class="mb-0">Cashier Daily Sales</h5>
        <div>
          <button class="btn btn-sm btn-light" id="print"><i class="fa fa-print"></i> Print</button>
        </div>
      </div>
      <div class="card-body" id="printout">
        <?php
        // Get all cashiers who had sales within the date range
        $cashiers_sql = $conn->query("SELECT DISTINCT 
            u.id,
            u.username,
            u.firstname,
            u.lastname
          FROM order_list ol
          INNER JOIN users u ON ol.user_id = u.id
          WHERE DATE(ol.date_created) BETWEEN '{$start_date}' AND '{$end_date}'
            AND ol.status IN (2,5) {$cashier_filter}
          ORDER BY u.username ASC");
        
        if($cashiers_sql && $cashiers_sql->num_rows > 0):
          $grand_total_all = 0;
          $grand_items_all = 0;
          
          while($cashier = $cashiers_sql->fetch_assoc()):
            $user_id = $cashier['id'];
            $cashier_name = $cashier['firstname'] . ' ' . $cashier['lastname'];
            $cashier_username = $cashier['username'];
            $cashier_label = $cashier_name . ' (' . $cashier_username . ')';
            
        ?>
        <div class="cashier-section" id="cashier-section-<?= $user_id ?>">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="cashier-name mb-0">
              <i class="fa fa-user-circle mr-2"></i><?= htmlspecialchars($cashier_name) ?> (<?= htmlspecialchars($cashier_username) ?>)
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary print-cashier" data-target="cashier-section-<?= $user_id ?>" data-label="<?= htmlspecialchars($cashier_label, ENT_QUOTES) ?>">
              <i class="fa fa-print"></i> Print
            </button>
          </div>
          
          <?php 
          $cashier_daily_total = 0;
          $cashier_daily_items = 0;
          $cashier_daily_transactions = 0;
          
          // Get daily breakdown for this cashier
          $daily_details = $conn->query("SELECT 
              DATE(ol.date_created) as sale_date,
              COUNT(DISTINCT ol.id) as daily_transactions,
              IFNULL(SUM(oi.quantity * oi.price), 0) as daily_amount
            FROM order_list ol
            LEFT JOIN order_items oi ON ol.id = oi.order_id AND oi.refunded = 0
            WHERE ol.user_id = '{$user_id}'
              AND DATE(ol.date_created) BETWEEN '{$start_date}' AND '{$end_date}'
              AND ol.status IN (2,5)
            GROUP BY DATE(ol.date_created)
            ORDER BY sale_date ASC");
          
          while($day_row = $daily_details->fetch_assoc()):
            $cashier_daily_total += (float)$day_row['daily_amount'];
            $cashier_daily_transactions += (int)$day_row['daily_transactions'];
          endwhile;
          
          // Get total items sold by cashier
          $items_count = $conn->query("SELECT 
              SUM(oi.quantity) as total_qty
            FROM order_items oi
            INNER JOIN order_list ol ON oi.order_id = ol.id
            WHERE ol.user_id = '{$user_id}'
              AND DATE(ol.date_created) BETWEEN '{$start_date}' AND '{$end_date}'
              AND ol.status IN (2,5)
              AND oi.refunded = 0");
          $cashier_daily_items = 0;
          if($items_count){
            $ic_row = $items_count->fetch_assoc();
            $cashier_daily_items = (int)($ic_row['total_qty'] ?? 0);
          }
          
          $grand_total_all += $cashier_daily_total;
          $grand_items_all += $cashier_daily_items;
          ?>
          
          <div class="cashier-summary">
            <div class="summary-card">
              <div class="summary-label">Total Sales</div>
              <div class="summary-value"><?= format_peso($cashier_daily_total) ?></div>
            </div>
            <div class="summary-card">
              <div class="summary-label">Transactions</div>
              <div class="summary-value"><?= (int)$cashier_daily_transactions ?></div>
            </div>
            <div class="summary-card">
              <div class="summary-label">Total Items</div>
              <div class="summary-value"><?= (int)$cashier_daily_items ?></div>
            </div>
            <div class="summary-card">
              <div class="summary-label">Avg Sale</div>
              <div class="summary-value"><?= format_peso($cashier_daily_transactions > 0 ? $cashier_daily_total / $cashier_daily_transactions : 0) ?></div>
            </div>
          </div>
          
          <table class="items-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Item Name</th>
                <th class="text-center">Qty Sold</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Get all items sold by this cashier within the date range
              $items_sql = $conn->query("SELECT 
                  m.name as item_name,
                  SUM(oi.quantity) as total_qty,
                  oi.price as unit_price,
                  SUM(oi.quantity * oi.price) as total_amount
                FROM order_items oi
                INNER JOIN order_list ol ON oi.order_id = ol.id
                INNER JOIN menu_list m ON oi.menu_id = m.id
                WHERE ol.user_id = '{$user_id}'
                  AND DATE(ol.date_created) BETWEEN '{$start_date}' AND '{$end_date}'
                  AND ol.status IN (2,5)
                  AND oi.refunded = 0
                GROUP BY m.id, oi.price
                ORDER BY total_amount DESC");
              
              $item_idx = 1;
              if($items_sql){
                while($item = $items_sql->fetch_assoc()):
              ?>
              <tr>
                <td><?= $item_idx++ ?></td>
                <td><?= htmlspecialchars($item['item_name']) ?></td>
                <td class="text-center"><?= (int)$item['total_qty'] ?></td>
                <td class="text-right"><?= format_peso($item['unit_price']) ?></td>
                <td class="text-right"><?= format_peso($item['total_amount']) ?></td>
              </tr>
              <?php endwhile; } ?>
            </tbody>
          </table>
        </div>
        <?php 
          endwhile;
        ?>
        
        <!-- Grand Total -->
        <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; border: 2px solid #5b9bd5; margin-top: 20px;">
          <div style="font-size: 16px; font-weight: bold; color: #5b9bd5;">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
              <div>Grand Total Sales: <span style="font-size: 20px;"><?= format_peso($grand_total_all) ?></span></div>
              <div>Total Items: <span style="font-size: 20px;"><?= (int)$grand_items_all ?></span></div>
              <div>Total Cashiers: <span style="font-size: 20px;"><?= $cashiers_sql->num_rows ?></span></div>
            </div>
          </div>
        </div>
        
        <?php 
        else:
        ?>
          <div class="alert alert-info">No sales data found for the selected date range.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<noscript id="print-header">
  <div class="text-center mb-3">
    <h4><?= $_settings->info('name') ?></h4>
    <h5>Cashier Daily Sales Report</h5>
    <h6>From <?= date("F d, Y", strtotime($start_date)) ?> to <?= date("F d, Y", strtotime($end_date)) ?></h6>
    <?php if(!empty($selected_cashier_label)): ?>
      <h6>Cashier: <?= htmlspecialchars($selected_cashier_label) ?></h6>
    <?php endif; ?>
    <hr>
  </div>
</noscript>

<script>
function print_single(sectionId, cashierLabel) {
  var h = $('head').clone();
  var section = $('#' + sectionId).clone();
  var ph = $($('noscript#print-header').html()).clone();
  if (cashierLabel) {
    ph.append('<h6 style="margin-top:5px;">Cashier: ' + cashierLabel + '</h6>');
  }
  h.find('title').text("Cashier Daily Sales Report - Print View");
  var nw = window.open("", "_blank", "width=900,height=700,left=100,top=100");
  nw.document.querySelector('head').innerHTML = h.html();
  nw.document.querySelector('body').innerHTML = ph[0].outerHTML + section[0].outerHTML;
  nw.document.close();
  start_loader();
  setTimeout(() => { nw.print(); setTimeout(() => { nw.close(); end_loader(); }, 200); }, 300);
}
function print_r() {
  var h = $('head').clone();
  var el = $('#printout').clone();
  var ph = $($('noscript#print-header').html()).clone();
  h.find('title').text("Cashier Daily Sales Report - Print View");
  var nw = window.open("", "_blank", "width=900,height=700,left=100,top=100");
  nw.document.querySelector('head').innerHTML = h.html();
  nw.document.querySelector('body').innerHTML = ph[0].outerHTML + el[0].outerHTML;
  nw.document.close();
  start_loader();
  setTimeout(() => { nw.print(); setTimeout(() => { nw.close(); end_loader(); }, 200); }, 300);
}
$(function(){
  $('#filter-form').submit(function(e){ e.preventDefault(); location.href = './?page=reports/cashier_daily_sales&' + $(this).serialize(); });
  $('#print').click(print_r);
  $('.print-cashier').on('click', function(){
    var target = $(this).data('target');
    var label = $(this).data('label');
    print_single(target, label);
  });
});
</script>
