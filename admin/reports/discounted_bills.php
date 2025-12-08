<?php
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date("Y-m-d");
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date("Y-m-d");
function format_peso($amount){ return '₱ ' . number_format((float)$amount, 2); }
?>

<div class="content py-5 px-3" style="background: linear-gradient(45deg, #5b9bd5, #1c6cab); color: white;">
  <h2>Discounted Bills Report</h2>
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
        <h5 class="mb-0">Discounted Bills</h5>
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
              <th>Cashier</th>
              <th class="text-center">Discount Type</th>
              <th class="text-center">Discount %</th>
              <th class="text-right">Subtotal</th>
              <th class="text-right">Discount Amount</th>
              <th class="text-right">Net Total</th>
              <th class="text-center">Table/Type</th>
              <th>Code</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sql = $conn->query("SELECT 
                ol.*, 
                u.username AS cashier,
                tl.table_number,
                dc.discount_code as applied_code,
                (ol.total_amount - ol.discounted_amount) AS discount_amount
              FROM order_list ol
              INNER JOIN users u ON ol.user_id = u.id
              LEFT JOIN table_list tl ON ol.table_id = tl.id
              LEFT JOIN discount_code dc ON dc.order_list_id = ol.id
              WHERE DATE(ol.date_created) BETWEEN '{$start_date}' AND '{$end_date}'
                AND ol.status IN (2,5)
                AND (ol.discount_percent > 0 OR (ol.discount_type IS NOT NULL AND ol.discount_type != '' AND ol.discount_type != 'None'))
              ORDER BY ol.date_created DESC");
            $i = 1; $total_sub = 0; $total_disc = 0; $total_net = 0;
            if($sql){
              while($row = $sql->fetch_assoc()):
                $sub = (float)$row['total_amount'];
                $net = isset($row['discounted_amount']) ? (float)$row['discounted_amount'] : $sub;
                $disc = isset($row['discount_amount']) ? (float)$row['discount_amount'] : ($sub - $net);
                $total_sub += $sub;
                $total_disc += $disc;
                $total_net += $net;
                $table_label = !empty($row['table_number']) ? $row['table_number'] : ($row['order_type'] ?? '');
            ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= date('Y-m-d H:i', strtotime($row['date_created'])) ?></td>
              <td><?= htmlspecialchars($row['code']) ?></td>
              <td><?= htmlspecialchars($row['cashier']) ?></td>
              <td class="text-center"><?= htmlspecialchars($row['discount_type']) ?></td>
              <td class="text-center"><?= number_format((float)$row['discount_percent'], 2) ?>%</td>
              <td class="text-right"><?= format_peso($sub) ?></td>
              <td class="text-right"><?= format_peso($disc) ?></td>
              <td class="text-right"><?= format_peso($net) ?></td>
              <td class="text-center"><?= htmlspecialchars($table_label) ?></td>
              <td><?= htmlspecialchars($row['applied_code']) ?></td>
            </tr>
            <?php endwhile; }
            if(!$sql || $sql->num_rows == 0): ?>
              <tr><td colspan="11" class="text-center">No discounted bills found</td></tr>
            <?php endif; ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="6" class="text-right">Totals:</th>
              <th class="text-right"><?= format_peso($total_sub) ?></th>
              <th class="text-right"><?= format_peso($total_disc) ?></th>
              <th class="text-right"><?= format_peso($total_net) ?></th>
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
    <h5>Discounted Bills Report</h5>
    <h6>From <?= date("F d, Y", strtotime($start_date)) ?> to <?= date("F d, Y", strtotime($end_date)) ?></h6>
    <hr>
  </div>
</noscript>

<script>
function print_r() {
  var h = $('head').clone();
  var el = $('#printout').clone();
  var ph = $($('noscript#print-header').html()).clone();
  h.find('title').text("Discounted Bills Report - Print View");
  var nw = window.open("", "_blank", "width=800,height=600,left=100,top=100");
  nw.document.querySelector('head').innerHTML = h.html();
  nw.document.querySelector('body').innerHTML = ph[0].outerHTML + el[0].outerHTML;
  nw.document.close();
  start_loader();
  setTimeout(() => { nw.print(); setTimeout(() => { nw.close(); end_loader(); }, 200); }, 300);
}
$(function(){
  $('#filter-form').submit(function(e){ e.preventDefault(); location.href = './?page=reports/discounted_bills&' + $(this).serialize(); });
  $('#print').click(print_r);
});
</script>
