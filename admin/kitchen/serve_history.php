<?php
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date("Y-m-d");
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date("Y-m-d");

// Fetch staff list for filter (users table doesn't have delete_flag in this schema)
$staff_qry = $conn->query("SELECT id, username FROM users ORDER BY username ASC");

function format_datetime($dt){
    return $dt ? date('Y-m-d H:i', strtotime($dt)) : '-';
}
?>

<div class="content py-5 px-3" style="background: linear-gradient(45deg, #5b9bd5, #1c6cab); color: white;">
  <h2>Serve History</h2>
</div>

<div class="row mt-4 justify-content-center">
  <div class="col-lg-11">
    <div class="card shadow-sm border-0 mb-2">
      <div class="card-header">
        <h3 class="card-title mb-0"><i class="fa fa-filter mr-2"></i>Filter</h3>
      </div>
      <div class="card-body">
        <form id="filter-form">
          <div class="row align-items-end">
            <div class="col-md-4">
              <label>Start Date</label>
              <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>" required>
            </div>
            <div class="col-md-4">
              <label>End Date</label>
              <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>" required>
            </div>
            <div class="col-md-3">
              <label>Staff</label>
              <select name="staff_id" class="form-control">
                <option value="">All</option>
                <?php while($s = $staff_qry->fetch_assoc()): ?>
                  <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['username']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="col-md-1">
              <button class="btn btn-primary mt-2 btn-block" id="filter-btn"><i class="fa fa-filter"></i></button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-header d-flex justify-content-between align-items-center py-2">
        <h5 class="mb-0">Serve History</h5>
        <div>
          <button class="btn btn-sm btn-light" id="print"><i class="fa fa-print"></i> Print</button>
        </div>
      </div>
      <div class="card-body" id="printout">
        <table class="table table-bordered table-striped" id="serve-history-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Date/Time</th>
              <th>Order Code</th>
              <th>Item</th>
              <th class="text-center">Qty</th>
              <th>Served By</th>
            </tr>
          </thead>
          <tbody>
            <tr><td colspan="6" class="text-center">Loading...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<noscript id="print-header">
  <div class="text-center mb-3">
    <h4><?= $_settings->info('name') ?></h4>
    <h5>Serve History</h5>
    <h6>From <?= date("F d, Y", strtotime($start_date)) ?> to <?= date("F d, Y", strtotime($end_date)) ?></h6>
    <hr>
  </div>
</noscript>

<script>
function fetch_history(data){
    data = data || {};
    data.start_date = data.start_date || $('input[name="start_date"]').val();
    data.end_date = data.end_date || $('input[name="end_date"]').val();
    data.staff_id = data.staff_id || $('select[name="staff_id"]').val();
    start_loader();
    $.ajax({
        url: _base_url_ + 'classes/Master.php?f=get_kitchen_serve_history',
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
                var tbody = $('#serve-history-table tbody');
                tbody.empty();
                if(resp.data.length > 0){
                    resp.data.map(function(r, idx){
                        var row = $('<tr>');
                        row.append($('<td>').text(idx+1));
                        row.append($('<td>').text(r.served_at ? r.served_at : '-'));
                        row.append($('<td>').text(r.order_code));
                        row.append($('<td>').text(r.item_name));
                        row.append($('<td class="text-center">').text(r.quantity));
                        row.append($('<td>').text(r.served_by_name ? r.served_by_name : r.served_by));
                        tbody.append(row);
                    })
                }else{
                    tbody.append('<tr><td colspan="6" class="text-center">No records found</td></tr>');
                }
            }else{
                alert_toast('Failed to fetch history', 'error');
            }
            end_loader();
        }
    })
}

$(function(){
    fetch_history();
    $('#filter-form').submit(function(e){
        e.preventDefault();
        fetch_history();
    });

    $('#print').click(function(){
        var h = $('head').clone();
        var el = $('#printout').clone();
        var ph = $($('noscript#print-header').html()).clone();
        h.find('title').text('Serve History - Print View');
        var nw = window.open('', '_blank', 'width=800,height=600,left=100,top=100');
        nw.document.querySelector('head').innerHTML = h.html();
        nw.document.querySelector('body').innerHTML = ph[0].outerHTML + el[0].outerHTML;
        nw.document.close();
        start_loader();
        setTimeout(() => { nw.print(); setTimeout(() => { nw.close(); end_loader(); }, 200); }, 300);
    });
});
</script>
