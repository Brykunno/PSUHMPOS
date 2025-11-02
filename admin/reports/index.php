<?php
// Check if 'date' parameter is provided in the URL query string
$date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

// Define start and end date for the range
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : $date;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : $date;

// Function to format numbers as Peso (PHP)
function format_peso($amount) {
    return '₱ ' . number_format($amount, 2);
}
?>

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
		background-color: #5b9bd5; /* Blue background */
		color: white;
	}
	.card-title {
		font-weight: bold;
	}
	.table thead {
		background: #eef4fb;
	}
	.table thead th {
		text-align: center;
		background-color: #5b9bd5; /* Blue background */
		color: white !important;
	}
	.table tbody tr:hover {
		background-color: #eef4fb;
	}
	.badge {
		font-size: 0.9em;
		padding: 0.45em 0.85em;
		border-radius: 1em;
	}
	.badge-primary {
		background: #5b9bd5; /* Updated to blue */
		color: white;
	}
	.badge-success {
		background: #4caf50;
		color: white;
	}
	.badge-light {
		
		color: #000;
	}
	.table td {
		color: black !important;
	}
</style>

<div class="content py-5 px-3" style="background: linear-gradient(45deg, #5b9bd5, #1c6cab); color: white;">
    <h2>Sales Reports</h2>
</div>

<div class="row flex-column mt-4 justify-content-center align-items-center mt-lg-n4 mt-md-3 mt-sm-0">
    <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12">
        <div class="card card-outline rounded-3 shadow-sm border-0 mb-2">
            <div class="card-header py-3">
                <h3 class="card-title mb-0"><i class="fa fa-list-alt mr-2"></i>Filter</h3>
            </div>
            <div class="card-body">
                <form action="" id="filter-form">
                    <div class="row align-items-end">
                        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label for="start_date" class="control-label">Start Date</label>
                                <input type="date" class="form-control form-control-sm rounded-0" name="start_date" id="start_date" value="<?= $start_date ?>" required="required">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label for="end_date" class="control-label">End Date</label>
                                <input type="date" class="form-control form-control-sm rounded-0" name="end_date" id="end_date" value="<?= $end_date ?>" required="required">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <button class="btn btn-sm btn-flat btn-primary bg-gradient-primary"><i class="fa fa-filter"></i> Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12">
        <div class="alert alert-info d-flex justify-content-between align-items-center">
            <div><strong>More Reports:</strong> Explore additional reports and insights.</div>
            <div>
                <a href="./?page=reports/product&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" class="btn btn-sm btn-primary">Product Sales</a>
                <a href="./?page=reports/refunds&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" class="btn btn-sm btn-danger">Refunds</a>
            </div>
        </div>
    </div>

    <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12">
        <div class="card card-outline rounded-3 shadow-sm border-0 mb-2">
            <div class="card-header py-1">
                <div class="card-tools">
                    <button class="btn btn-flat btn-sm btn-light bg-gradient-light border text-dark" type="button" id="print"><i class="fa fa-print"></i> Print</button>
                </div>
            </div>
            <div class="card-body">
                <div class="container-fluid" id="printout">
                    <table class="table table-bordered">
                        <colgroup>
                            <col width="10%">
                            <col width="15%">
                            <col width="20%">
                            <col width="20%">
                            <col width="20%">
                            <col width="15%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="px-1 py-1 text-center">#</th>
                                <th class="px-1 py-1 text-center">Time</th>
                                <th class="px-1 py-1 text-center">Transaction Code</th>
                                <th class="px-1 py-1 text-center">Queue</th>
                                <th class="px-1 py-1 text-center">Processed By</th>
                                <th class="px-1 py-1 text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $g_total = 0;
                            $i = 1;
                            // Only include Paid and Paid (Refunded Items) and compute totals excluding refunded items
                            $stock = $conn->query("SELECT 
                                    ol.*, 
                                    (
                                        SELECT IFNULL(SUM(oi.quantity * oi.price), 0)
                                        FROM order_items oi 
                                        WHERE oi.order_id = ol.id AND oi.refunded = 0
                                    ) AS net_total
                                FROM `order_list` ol
                                WHERE date(ol.date_created) BETWEEN '{$start_date}' AND '{$end_date}'
                                  AND ol.status IN (2,5)
                                ORDER BY abs(unix_timestamp(ol.date_created)) asc");
                            while($row = $stock->fetch_assoc()):
                                $user = $conn->query("SELECT username FROM `users` WHERE id = '{$row['user_id']}'");
                                $row['processed_by'] = "N/A";
                                if($user->num_rows > 0){
                                    $row['processed_by'] = $user->fetch_array()[0];
                                }
                                $g_total += (float)$row['net_total']; 
                            ?>
                            <tr>
                                <td class="px-1 py-1 align-middle text-center"><?= $i++ ?></td>
                                <td class="px-1 py-1 align-middle"><?= date("h:i A", strtotime($row['date_created'])) ?></td>
                                <td class="px-1 py-1 align-middle"><?= $row['code'] ?></td>
                                <td class="px-1 py-1 align-middle"><?= $row['queue'] ?></td>
                                <td class="px-1 py-1 align-middle"><?= $row['processed_by'] ?></td>
                                <td class="px-1 py-1 align-middle text-right"><?= format_peso($row['net_total']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if($stock->num_rows <= 0): ?>
                                <tr>
                                    <td class="py-1 text-center" colspan="6">No records found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-center">Total Sales</th>
                                <th class="text-right"><?= format_peso($g_total) ?></th> <!-- Convert to Peso -->
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<noscript id="print-header">
    <div>
        <style>
            html{
                min-height:unset !important;
            }
        </style>
        <div class="d-flex w-100 align-items-center">
            <div class="col-2 text-center">
                <img src="<?= validate_image($_settings->info('logo')) ?>" alt="" class="rounded-circle border order-logo">
            </div>
            <div class="col-8">
                <div style="line-height:1em">
                    <div class="text-center font-weight-bold h5 mb-0"><large><?= $_settings->info('name') ?></large></div>
                    <div class="text-center font-weight-bold h5 mb-0"><large>Daily Sales Report</large></div>
                    <div class="text-center font-weight-bold h5 mb-0">from <?= date("F d, Y", strtotime($start_date)) ?> to <?= date("F d, Y", strtotime($end_date)) ?></div>
                </div>
            </div>
        </div>
        <hr>
    </div>
</noscript>

<script>
    function print_r(){
        var h = $('head').clone()
        var el = $('#printout').clone()
        var ph = $($('noscript#print-header').html()).clone()
        h.find('title').text("Sales Report - Print View")
        var nw = window.open("", "_blank", "width="+($(window).width() * .8)+",left="+($(window).width() * .1)+",height="+($(window).height() * .8)+",top="+($(window).height() * .1))
            nw.document.querySelector('head').innerHTML = h.html()
            nw.document.querySelector('body').innerHTML = ph[0].outerHTML
            nw.document.querySelector('body').innerHTML += el[0].outerHTML
            nw.document.close()
            start_loader()
            setTimeout(() => {
                nw.print()
                setTimeout(() => {
                    nw.close()
                    end_loader()
                }, 200);
            }, 300);
    }
    $(function(){
        $('#filter-form').submit(function(e){
            e.preventDefault()
            location.href = './?page=reports&'+$(this).serialize()
        })
        $('#print').click(function(){
            print_r()
        })
    })
</script>
