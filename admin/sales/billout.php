<?php 
require_once('./../../config.php');

if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM `order_list` where id = '{$_GET['id']}'");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_array() as $k => $v) {
            if (!is_numeric($k)) $$k = htmlspecialchars_decode($v);
        }
    }
    if (isset($user_id)) {
        $user = $conn->query("SELECT username FROM `users` where id = '{$user_id}'");
        if ($user->num_rows > 0) {
            $processed_by = $user->fetch_array()[0];
        }
    }
}

// VAT Calculation
$vat_rate = 0.12;
$vat_amount = isset($total_amount) ? $total_amount * $vat_rate : 0;
$net_total = isset($total_amount) ? $total_amount - $vat_amount : 0;
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once('./../inc/header.php'); ?>
<body>
<style>
@media print {
  @page {
    size: 80mm auto;
    margin: 5mm;
  }
  body {
    width: 80mm;
    font-family: 'Courier New', monospace;
    font-size: 11px;
  }
  .d-print-none {
    display: none;
  }
}

body {
  font-family: 'Courier New', monospace;
  font-size: 11px;
  margin: 0;
  padding: 0;
}

.receipt {
  width: 80mm;
  margin: auto;
  padding: 5px;
}

.text-center {
  text-align: center;
}

hr {
  border-top: 1px dashed #000;
  margin: 4px 0;
}

.bold {
  font-weight: bold;
}

.item-row {
  display: flex;
  justify-content: space-between;
  border-bottom: 1px dashed #000;
  padding: 2px 0;
}
</style>

<div class="receipt">
  <div class="text-center bold">
    <img src="<?= base_url ?>uploads/lioncasa.png" alt="logo" style="height:120px;"><br>
    LION CASA 1979<br>
    Roxas Blvd, San Carlos City, Pangasinan 2420<br>
    0777228119<br>
    PSU LION CASA 1979<br>
    VAT REG TIN: 624-252-592-000<br>
  </div>

  <hr>
  <div>Receipt Date: <?= isset($date_created) ? date("Y.m.d H:i", strtotime($date_created)) : '' ?></div>
  <div>Transaction #: <?= isset($code) ? $code : '' ?></div>
  <div>Cashier: <?= isset($processed_by) ? $processed_by : '' ?></div>
  <div>Register: #<?= isset($queue) ? $queue : '1' ?></div>
  <hr>

  <div class="bold text-center">SALES INVOICE</div>
  <div style="display:flex; font-weight: bold;">
    <div style="width: 40%;">Item</div>
    <div style="width: 20%; text-align: right;">Price</div>
    <div style="width: 10%; text-align: center;">Qty</div>
    <div style="width: 30%; text-align: right;">Amount</div>
  </div>

  <?php 
    if (isset($id)):
      $items = $conn->query("SELECT oi.*, m.name FROM `order_items` oi INNER JOIN `menu_list` m ON oi.menu_id = m.id WHERE oi.order_id = '{$id}'");
      while ($row = $items->fetch_assoc()):
  ?>
  <div style="display:flex;">
    <div style="width: 40%;"><?= $row['name'] ?></div>
    <div style="width: 20%; text-align: right;"><?= format_num($row['price'], 0) ?></div>
    <div style="width: 10%; text-align: center;"><?= $row['quantity'] ?></div>
    <div style="width: 30%; text-align: right;"><?= format_num($row['price'] * $row['quantity'], 0) ?></div>
  </div>
  <?php endwhile; endif; ?>

  <hr>
  <div style="display:flex;"><div style="width: 70%;">SUBTOTAL:</div><div style="width:30%; text-align:right;"><?= format_num($net_total, 2) ?></div></div>
  <div style="display:flex;"><div style="width: 70%;">12% VAT:</div><div style="width:30%; text-align:right;"><?= format_num($vat_amount, 2) ?></div></div>
  <div style="display:flex;"><div style="width: 70%; font-weight:bold;">TOTAL:</div><div style="width:30%; text-align:right; font-weight:bold;"><?= format_num($total_amount, 2) ?></div></div>

  <hr>

  <div>
    VATable Sales: <?= format_num($net_total, 2) ?><br>
    VAT Amount: <?= format_num($vat_amount, 2) ?><br>
    VAT-Exempt Sales: 0.00<br>
    Zero-Rated Sales: 0.00
  </div>

  <br><br>
  <div class="text-center">
    Thank you for dining at LION CASA 1979!<br><br>
    -------------------------------<br>
    POS Provider: BSIT PSU SANCARLOS.<br>
    Pangasinan State University San Carlos Campus Roxas Blvd.<br>
    Roxas Blvd, San Carlos City, Pangasinan 2420.<br>
    VAT REG TIN: 010-036-922-000<br>
    Date Issued: 02/24/2025<br>
    Acc #: 043-0100-36922-201905-10900<br>
    PTU #: FP122023-062-04212023-001<br>
    Date Issued: 05/10/2025<br>
    -------------------------------<br>
    This serves as your sales invoice.<br>
    POWERED BY BSIT POS
  </div>
</div>

<script>
  document.title = "Print Receipt";
  window.print();
</script>

</body>
</html>
