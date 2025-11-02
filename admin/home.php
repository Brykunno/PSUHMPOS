<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #fdf6e3;
      color: #1a1a1a;
      margin: 0;
      padding: 20px;
    }
    h1 {
      font-size: 2rem;
      color:#023047;
      margin-bottom: 10px;
    }
    hr {
      border-top: 2px solid#023047;
      margin-bottom: 30px;
    }
    .container {
      max-width: 1200px;
      margin: auto;
    }
    .info-box {
      display: flex;
      align-items: center;
      background-color: #4a90e2;
      border-radius: 15px;
      padding: 15px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      color: #fff;
      cursor: pointer;
      text-decoration: none;
    }
    .info-box:hover {
      transform: translateY(-5px);
      color: #ffcc00; /* A soft, vibrant yellow */

    }
    .info-box-icon {
      background-color: #000;
      border-radius: 50%;
      padding: 18px;
      font-size: 1.8rem;
      margin-right: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      min-width: 60px;
      height: 60px;
    }
    .info-box-content {
      flex: 1;
    }
    .info-box-text {
      font-size: 1.1rem;
      font-weight: 500;
      color: #fff;
    }
    .info-box-number {
      font-size: 1.6rem;
      font-weight: bold;
      color: #fff;
      text-shadow: 0 1px 0 rgba(255, 255, 255, 0.6);
    }
    .card {
      background-color: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      margin-top: 30px;
      overflow: hidden;
    }
    .card-header {
      background:#023047;
      color: #fff;
      font-size: 1.25rem;
      font-weight: bold;
      padding: 15px 20px;
    }
    .card-body {
      padding: 25px;
      background-color: #fff;
    }
    #sales-chart, #top-sold-food {
      width: 100%;
      height: 400px;
    }
    .row {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }
    .col-md-4, .col-md-6 {
      flex: 1;
      min-width: 280px;
    }
    .col-md-6 {
      max-width: 48%;
    }
    .text-right {
      text-align: right;
    }
    a {
      text-decoration: none;
    }
    @media screen and (max-width: 768px) {
      .info-box {
        flex-direction: column;
        align-items: flex-start;
      }
      .info-box-icon {
        margin-bottom: 10px;
      }
      .text-right {
        text-align: left;
      }
    }
  </style>
</head>
<body>
<div class="container">
    <h1>Welcome, <?php echo $_settings->userdata('firstname')." ".$_settings->userdata('lastname'); ?>!</h1>
    <hr>

    <div class="row">
      <div class="col-md-4">
        <a href="./?page=categories" class="info-box">
          <span class="info-box-icon"><i class="fas fa-th-list"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Food Categories</span>
            <span class="info-box-number text-right h5">
              <?php 
                $category = $conn->query("SELECT * FROM category_list where delete_flag = 0 and status = 1")->num_rows;
                echo format_num($category);
              ?>
            </span>
          </div>
        </a>
      </div>

      <div class="col-md-4">
        <a href="./?page=menus" class="info-box">
          <span class="info-box-icon"><i class="fas fa-hamburger"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Food Menu</span>
            <span class="info-box-number text-right h5">
              <?php 
                $menus = $conn->query("SELECT id FROM menu_list where delete_flag = 0 and status = 1")->num_rows;
                echo format_num($menus);
              ?>
            </span>
          </div>
        </a>
      </div>

      <?php if($_settings->userdata('type') != 2): ?>
      <div class="col-md-4">
        <a href="./?page=kitchen" class="info-box">
          <span class="info-box-icon"><i class="fas fa-table"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Queued Order</span>
            <span class="info-box-number text-right h5">
              <?php 
                $orders = $conn->query("SELECT id FROM order_list where status = 0")->num_rows;
                echo format_num($orders);
              ?>
            </span>
          </div>
        </a>
      </div>
      <?php endif; ?>

      <?php if($_settings->userdata('type') == 1 || $_settings->userdata('type') == 2): ?>
<div class="col-md-4">
  <a href="./?page=reports" class="info-box">
    <span class="info-box-icon"><i class="fas fa-coins"></i></span>
    <div class="info-box-content">
      <span class="info-box-text">Total Sales Today</span>
      <span class="info-box-number text-right h5">
        <?php 
          // Calculate total sales today excluding refunded items
          $today = date('Y-m-d');
          $user_filter = ($_settings->userdata('type') == 2) ? " AND ol.user_id = '{$_settings->userdata('id')}'" : "";
          
          $sales_query = "SELECT COALESCE(SUM(
              (SELECT IFNULL(SUM(oi.quantity * oi.price), 0)
               FROM order_items oi 
               WHERE oi.order_id = ol.id AND oi.refunded = 0)
          ), 0) as total_sales
          FROM order_list ol
          WHERE DATE(ol.date_created) = '{$today}'
            AND ol.status IN (2,5)
            {$user_filter}";
          
          $sales_result = $conn->query($sales_query)->fetch_array()[0];
          echo "₱ " . format_num($sales_result, 2);
        ?>
      </span>
    </div>
  </a>
</div>

<div class="col-md-4">
  <a href="./?page=reports/refunds" class="info-box" style="background-color: #dc3545;">
    <span class="info-box-icon"><i class="fas fa-undo"></i></span>
    <div class="info-box-content">
      <span class="info-box-text">Total Refunds Today</span>
      <span class="info-box-number text-right h5">
        <?php 
          // Calculate total refunds today
          $today = date('Y-m-d');
          $user_filter_refund = ($_settings->userdata('type') == 2) ? " AND r.user_id = '{$_settings->userdata('id')}'" : "";
          
          $refunds_query = "SELECT COALESCE(SUM(r.amount), 0) as total_refunds
          FROM refunds r
          WHERE DATE(r.date_created) = '{$today}'
            {$user_filter_refund}";
          
          $refunds_result = $conn->query($refunds_query)->fetch_array()[0];
          echo "₱ " . format_num($refunds_result, 2);
        ?>
      </span>
    </div>
  </a>
</div>
<?php endif; ?>


      <div class="col-md-4">
        <a href="./?page=orders" class="info-box">
          <span class="info-box-icon"><i class="fas fa-clock"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Live Orders In Progress</span>
            <span class="info-box-number text-right h5">
              <?php 
                $live_orders = $conn->query("SELECT id FROM order_list WHERE status = 1")->num_rows;
                echo format_num($live_orders);
              ?>
            </span>
          </div>
        </a>
      </div>
    </div>
</div>


    <?php
    if($_settings->userdata('type') == 1):
      $sales_data = [];
      $date_labels = [];
      for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        // Calculate sales excluding refunded items
        $query = "SELECT COALESCE(SUM(
            (SELECT IFNULL(SUM(oi.quantity * oi.price), 0)
             FROM order_items oi 
             WHERE oi.order_id = ol.id AND oi.refunded = 0)
        ), 0) as total_sales
        FROM order_list ol
        WHERE DATE(ol.date_created) = '{$date}'
          AND ol.status IN (2,5)";
        $sales = $conn->query($query)->fetch_array()[0];
        $date_labels[] = date('M d', strtotime($date));
        $sales_data[] = $sales;
      }

      $top_sold_food = [];
      $food_labels = [];
      // Exclude refunded items from top sold food calculation
      $food_query = "SELECT oi.menu_id, SUM(oi.quantity) AS total_quantity 
                    FROM order_items oi
                    JOIN order_list ol ON oi.order_id = ol.id 
                    WHERE ol.date_created > NOW() - INTERVAL 30 DAY 
                      AND oi.refunded = 0
                      AND ol.status IN (2,5)
                    GROUP BY oi.menu_id 
                    ORDER BY total_quantity DESC 
                    LIMIT 5";
      $food_result = $conn->query($food_query);
      while($row = $food_result->fetch_assoc()) {
        $menu_name = $conn->query("SELECT name FROM menu_list WHERE id = ".$row['menu_id'])->fetch_assoc()['name'];
        $food_labels[] = $menu_name;
        $top_sold_food[] = (int)$row['total_quantity'];
      }
    ?>

    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">Sales Report (Last 7 Days)</div>
          <div class="card-body">
            <canvas id="sales-chart"></canvas>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card">
          <div class="card-header">Most Sold Food (Last 30 Days)</div>
          <div class="card-body">
            <canvas id="top-sold-food"></canvas>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
  Chart.register(ChartDataLabels);

  // Sales Bar Chart (leave as is)
  const salesCtx = document.getElementById('sales-chart').getContext('2d');
  new Chart(salesCtx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($date_labels); ?>,
      datasets: [{
        label: 'Sales (₱)',
        data: <?php echo json_encode($sales_data); ?>,
        backgroundColor: 'rgba(0, 123, 255, 0.8)',
        borderColor: 'rgba(0, 123, 255, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) { return '₱' + value; }
          }
        }
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: function(tooltipItem) {
              return '₱' + tooltipItem.raw;
            }
          }
        }
      }
    }
  });

  // Top Sold Food Pie Chart (modified nicely)
  const topSoldCtx = document.getElementById('top-sold-food').getContext('2d');
  new Chart(topSoldCtx, {
    type: 'pie',
    data: {
      labels: <?php echo json_encode($food_labels); ?>,
      datasets: [{
        data: <?php echo json_encode($top_sold_food); ?>,
        backgroundColor: [
          '#ff9800', '#ffc107', '#ff5722', '#ffb74d', '#ffa726'
        ],
        borderColor: '#fff',
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            color: '#333',
            font: {
              size: 14,
              weight: 'bold'
            }
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.label || '';
              const value = context.raw || 0;
              return label + ': ' + value + ' sold';
            }
          }
        },
        datalabels: {
          color: '#fff',
          formatter: (value, context) => {
            const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
            const percentage = (value / total * 100).toFixed(1) + '%';
            return percentage;
          },
          font: {
            weight: 'bold',
            size: 14
          }
        }
      }
    }
  });
</script>

    <?php endif; ?>
  </div>
</body>
</html>
