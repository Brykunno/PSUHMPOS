<?php
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date("Y-m-01");
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date("Y-m-d");
function format_peso($amount){ return '₱ ' . number_format((float)$amount, 2); }
?>

<div class="content py-5 px-3" style="background: linear-gradient(45deg, #5b9bd5, #1c6cab); color: white;">
  <h2>Refund Trends</h2>
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

    <div class="card shadow-sm border-0 mb-4">
      <div class="card-header d-flex justify-content-between align-items-center py-2">
        <h5 class="mb-0">By Item</h5>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Item</th>
              <th class="text-center">Refund Count</th>
              <th class="text-right">Total Refunded</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $q1 = $conn->query("SELECT 
                COALESCE(m.name, 'Full Order') AS item_name,
                COUNT(*) AS refund_count,
                SUM(r.amount) AS total_amount
              FROM refunds r
              LEFT JOIN order_items oi ON r.item_id = oi.id
              LEFT JOIN menu_list m ON oi.menu_id = m.id
              WHERE DATE(r.date_created) BETWEEN '{$start_date}' AND '{$end_date}'
              GROUP BY COALESCE(m.name, 'Full Order')
              ORDER BY total_amount DESC");
            $i=1; if($q1){ while($row=$q1->fetch_assoc()): ?>
              <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['item_name']) ?></td>
                <td class="text-center"><?= (int)$row['refund_count'] ?></td>
                <td class="text-right"><?= format_peso($row['total_amount']) ?></td>
              </tr>
            <?php endwhile; } if(!$q1 || $q1->num_rows==0): ?>
              <tr><td colspan="4" class="text-center">No data</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
      <div class="card-header d-flex justify-content-between align-items-center py-2">
        <h5 class="mb-0">By Reason</h5>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Reason</th>
              <th class="text-center">Refund Count</th>
              <th class="text-right">Total Refunded</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $q2 = $conn->query("SELECT 
                COALESCE(NULLIF(TRIM(r.reason),''),'Unspecified') AS reason_label,
                COUNT(*) AS refund_count,
                SUM(r.amount) AS total_amount
              FROM refunds r
              WHERE DATE(r.date_created) BETWEEN '{$start_date}' AND '{$end_date}'
              GROUP BY COALESCE(NULLIF(TRIM(r.reason),''),'Unspecified')
              ORDER BY total_amount DESC");
            $i=1; if($q2){ while($row=$q2->fetch_assoc()): ?>
              <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['reason_label']) ?></td>
                <td class="text-center"><?= (int)$row['refund_count'] ?></td>
                <td class="text-right"><?= format_peso($row['total_amount']) ?></td>
              </tr>
            <?php endwhile; } if(!$q2 || $q2->num_rows==0): ?>
              <tr><td colspan="4" class="text-center">No data</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
      <div class="card-header d-flex justify-content-between align-items-center py-2">
        <h5 class="mb-0">By Day</h5>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Date</th>
              <th class="text-center">Refund Count</th>
              <th class="text-right">Total Refunded</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $q3 = $conn->query("SELECT 
                DATE(r.date_created) as rdate,
                COUNT(*) AS refund_count,
                SUM(r.amount) AS total_amount
              FROM refunds r
              WHERE DATE(r.date_created) BETWEEN '{$start_date}' AND '{$end_date}'
              GROUP BY DATE(r.date_created)
              ORDER BY rdate ASC");
            $i=1; if($q3){ while($row=$q3->fetch_assoc()): ?>
              <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['rdate']) ?></td>
                <td class="text-center"><?= (int)$row['refund_count'] ?></td>
                <td class="text-right"><?= format_peso($row['total_amount']) ?></td>
              </tr>
            <?php endwhile; } if(!$q3 || $q3->num_rows==0): ?>
              <tr><td colspan="4" class="text-center">No data</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<noscript id="print-header">
  <div class="text-center mb-3">
    <h4><?= $_settings->info('name') ?></h4>
    <h5>Refund Trends</h5>
    <h6>From <?= date("F d, Y", strtotime($start_date)) ?> to <?= date("F d, Y", strtotime($end_date)) ?></h6>
    <hr>
  </div>
</noscript>

<script>
function print_r() {
  var h = $('head').clone();
  var el = $('#printout').length ? $('#printout').clone() : $('.row.mt-4').clone();
  var ph = $($('noscript#print-header').html()).clone();
  h.find('title').text("Refund Trends - Print View");
  var nw = window.open("", "_blank", "width=800,height=600,left=100,top=100");
  nw.document.querySelector('head').innerHTML = h.html();
  nw.document.querySelector('body').innerHTML = ph[0].outerHTML + el[0].outerHTML;
  nw.document.close();
  start_loader();
  setTimeout(() => { nw.print(); setTimeout(() => { nw.close(); end_loader(); }, 200); }, 300);
}
$(function(){
  $('#filter-form').submit(function(e){ e.preventDefault(); location.href = './?page=reports/refund_trends&' + $(this).serialize(); });
});
</script>
