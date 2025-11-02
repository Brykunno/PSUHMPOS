<?php
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date("Y-m-d");
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date("Y-m-d");

function format_peso($amount){ return '₱ ' . number_format((float)$amount, 2); }
?>

<div class="content py-5 px-3" style="background: linear-gradient(45deg, #5b9bd5, #1c6cab); color: white;">
  <h2>Refunds Report</h2>
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
            <div class="col-md-5">
              <label>Start Date</label>
              <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>" required>
            </div>
            <div class="col-md-5">
              <label>End Date</label>
              <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>" required>
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
        <h5 class="mb-0">Refunds Summary</h5>
        <div>
          <button class="btn btn-sm btn-light" id="print"><i class="fa fa-print"></i> Print</button>
        </div>
      </div>
      <div class="card-body" id="printout">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Date</th>
              <th>Order Code</th>
              <th>Item</th>
              <th class="text-center">Qty</th>
              <th class="text-right">Refund Amount</th>
              <th>Reason</th>
              <th>Processed By</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sql = $conn->query("SELECT 
                r.*, 
                ol.code AS order_code,
                u.username AS processed_by,
                oi.quantity AS qty,
                m.name AS item_name
              FROM refunds r
              INNER JOIN order_list ol ON r.order_id = ol.id
              LEFT JOIN order_items oi ON r.item_id = oi.id
              LEFT JOIN menu_list m ON oi.menu_id = m.id
              LEFT JOIN users u ON r.user_id = u.id
              WHERE DATE(r.date_created) BETWEEN '{$start_date}' AND '{$end_date}'
              ORDER BY r.date_created DESC");
            $i = 1; $total_refunds = 0; 
            if($sql){
              while($row = $sql->fetch_assoc()):
                $is_full = is_null($row['item_id']);
                $item_label = $is_full ? 'Full Order' : ($row['item_name'] ?? 'Item');
                $qty = $is_full ? '-' : ((int)$row['qty']);
                $total_refunds += (float)$row['amount'];
            ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= date('Y-m-d H:i', strtotime($row['date_created'])) ?></td>
              <td><?= htmlspecialchars($row['order_code']) ?></td>
              <td><?= htmlspecialchars($item_label) ?></td>
              <td class="text-center"><?= $qty ?></td>
              <td class="text-right"><?= format_peso($row['amount']) ?></td>
              <td><?= htmlspecialchars($row['reason']) ?></td>
              <td><?= htmlspecialchars($row['processed_by']) ?></td>
            </tr>
            <?php endwhile; } ?>
            <?php if(!$sql || $sql->num_rows == 0): ?>
              <tr><td colspan="8" class="text-center">No refunds found</td></tr>
            <?php endif; ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="5" class="text-right">Total Refunded:</th>
              <th class="text-right"><?= format_peso($total_refunds) ?></th>
              <th colspan="2"></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>

<noscript id="print-header">
  <div class="text-center mb-3">
    <h4><?= $_settings->info('name') ?></h4>
    <h5>Refunds Report</h5>
    <h6>From <?= date("F d, Y", strtotime($start_date)) ?> to <?= date("F d, Y", strtotime($end_date)) ?></h6>
    <hr>
  </div>
</noscript>

<script>
function print_r() {
  var h = $('head').clone();
  var el = $('#printout').clone();
  var ph = $($('noscript#print-header').html()).clone();
  h.find('title').text("Refunds Report - Print View");
  var nw = window.open("", "_blank", "width=800,height=600,left=100,top=100");
  nw.document.querySelector('head').innerHTML = h.html();
  nw.document.querySelector('body').innerHTML = ph[0].outerHTML + el[0].outerHTML;
  nw.document.close();
  start_loader();
  setTimeout(() => { nw.print(); setTimeout(() => { nw.close(); end_loader(); }, 200); }, 300);
}
$(function(){
  $('#filter-form').submit(function(e){ e.preventDefault(); location.href = './?page=reports/refunds&' + $(this).serialize(); });
  $('#print').click(print_r);
});
</script>
