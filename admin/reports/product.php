<?php
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date("Y-m-d");
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date("Y-m-d");
?>

<<style>
    .card-header {
        background: linear-gradient(45deg, #5b9bd5, #3c78d8); /* Updated to blue gradient */
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between; /* Ensures elements inside spread apart */
        padding: 0.75rem 1rem;
    }

    .card-title {
        margin: 0;
        font-weight: bold;
    }

    .btn-print-container {
        margin-left: auto;
    }

    .btn-print {
        background-color: white;
        color: #5b9bd5;
        border: none;
        font-weight: bold;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        transition: 0.2s ease;
    }

    .btn-print:hover {
        background-color: #f0f0f0;
        transform: scale(1.05);
    }

    .table thead th {
        text-align: center;
        background: linear-gradient(45deg, #5b9bd5, #3c78d8); /* Match blue gradient */
        color: white;
    }

    .table tbody td {
        color: black;
    }

    .table tbody tr:hover {
        background-color: #e8f0fa;
    }
</style>

<div class="content py-5 px-3" style="background: linear-gradient(45deg, #5b9bd5, #1c6cab); color: white;">
    <h2>Product Sales Report</h2>
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
                <h5 class="mb-0">Product Sales Summary</h5>
                <div class="btn-print-container">
                    <button class="btn btn-sm btn-light" id="print"><i class="fa fa-print"></i> Print</button>
                </div>
            </div>
            <div class="card-body" id="printout">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Quantity Sold</th>
                            <th>Sales Amount</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = $conn->query("SELECT m.name, SUM(oi.quantity) as total_qty, SUM(oi.quantity * oi.price) as total_sales
                            FROM order_items oi
                            INNER JOIN order_list ol ON oi.order_id = ol.id
                            INNER JOIN menu_list m ON oi.menu_id = m.id
                            WHERE date(ol.date_created) BETWEEN '{$start_date}' AND '{$end_date}'
                            GROUP BY m.id
                            ORDER BY total_sales DESC");

                        $i = 1;
                        $grand_total = 0;
                        $data = [];

                        while ($row = $query->fetch_assoc()) {
                            $grand_total += $row['total_sales'];
                            $data[] = $row;
                        }

                        foreach ($data as $row) {
                            $percentage = ($grand_total > 0) ? ($row['total_sales'] / $grand_total) * 100 : 0;
                        ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $row['name'] ?></td>
                                <td class="text-center"><?= $row['total_qty'] ?></td>
                                <td class="text-right">₱<?= number_format($row['total_sales'], 2) ?></td>
                                <td class="text-center"><?= number_format($percentage, 2) ?>%</td>
                            </tr>
                        <?php } ?>
                        <?php if (empty($data)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No records found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-center">Total Sales</th>
                            <th class="text-right">₱<?= number_format($grand_total, 2) ?></th>
                            <th></th>
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
        <h5>Product Sales Report</h5>
        <h6>From <?= date("F d, Y", strtotime($start_date)) ?> to <?= date("F d, Y", strtotime($end_date)) ?></h6>
        <hr>
    </div>
</noscript>

<script>
function print_r() {
    var h = $('head').clone();
    var el = $('#printout').clone();
    var ph = $($('noscript#print-header').html()).clone();
    h.find('title').text("Product Sales Report - Print View");
    var nw = window.open("", "_blank", "width=800,height=600,left=100,top=100");
    nw.document.querySelector('head').innerHTML = h.html();
    nw.document.querySelector('body').innerHTML = ph[0].outerHTML + el[0].outerHTML;
    nw.document.close();
    start_loader();
    setTimeout(() => {
        nw.print();
        setTimeout(() => {
            nw.close();
            end_loader();
        }, 200);
    }, 300);
}

$(function(){
    $('#filter-form').submit(function(e){
        e.preventDefault();
        location.href = './?page=reports/product&' + $(this).serialize();
    });
    $('#print').click(print_r);
});
</script>
