<?php 
require_once('./../../config.php');

$cash = $change = $grand_total = $total_amount = $discount_percent = $emoney_reference = $card_number = '';
$vat_amount = $net_total = $discount_amount = 0;
$payment_method = '';

if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM `order_list` WHERE id = '{$_GET['id']}'");
    if ($qry->num_rows > 0) {
        $order = $qry->fetch_assoc();
        // Assign values from DB
        $cash = isset($order['tendered_amount']) ? floatval($order['tendered_amount']) : 0;
        $change = isset($order['change_amount']) ? floatval($order['change_amount']) : 0;
        $grand_total = isset($order['discounted_amount']) ? floatval($order['discounted_amount']) : 0;
        $total_amount = isset($order['total_amount']) ? floatval($order['total_amount']) : 0;
        $discount_type = isset($order['discount_type']) ? $order['discount_type'] : '';
        $discount_percent = isset($order['discount_percent']) ? floatval($order['discount_percent']) : 0;
        $emoney_reference = isset($order['reference_number']) ? $order['reference_number'] : '';
        $card_number = isset($order['card_number']) ? $order['card_number'] : '';
        $vat_amount = isset($order['vat']) ? floatval($order['vat']) : 0;
        $date_created = isset($order['date_created']) ? $order['date_created'] : '';
        $code = isset($order['code']) ? $order['code'] : '';
        $queue = isset($order['queue']) ? $order['queue'] : '';
        $user_id = isset($order['user_id']) ? $order['user_id'] : '';
        $discount_amount = $total_amount - $grand_total;
        $vat_amount = isset($order['vat_amount']) ? floatval($order['vat_amount']) : 0;
        $discount  = $discount_percent == 0? 0: format_num($discount_amount, 2);
       
    }

     $dcqry = $conn->query("SELECT * FROM `discount_code` WHERE order_list_id = '{$_GET['id']}'");
     if ($dcqry->num_rows > 0) {
         $dc = $dcqry->fetch_assoc();
 
         $discount_code = isset($dc['discount_code']) ? $dc['discount_code'] : '';
     }

    if (isset($user_id)) {
        $user = $conn->query("SELECT username FROM `users` WHERE id = '{$user_id}'");
        if ($user->num_rows > 0) {
            $processed_by = $user->fetch_array()[0];
        }
    }
}

// Determine payment method
if (!empty($card_number)) {
    $payment_method = 'credit_card';
} elseif (!empty($emoney_reference)) {
    $payment_method = 'emoney';
} elseif ($cash > 0) {
    $payment_method = 'cash';
} else {
    $payment_method = 'unknown';
}

// Exclude VAT for PWD or Senior Citizen
$vat_rate = 0.12;
$is_vat_exempt = false;
if (!empty($discount_type)) {
    $discount_type_lower = strtolower($discount_type);
    if (strpos($discount_type_lower, 'senior') !== false || strpos($discount_type_lower, 'pwd') !== false) {
        $vat_amount = 0;
        $is_vat_exempt = true;
    }
}
if (!$is_vat_exempt) {
    // If VAT is not stored, calculate it
    if (!$vat_amount && $total_amount) {
        $vat_amount = $total_amount * $vat_rate;
    }
}
$net_total = $total_amount - $vat_amount;

// Compute refunded vs non-refunded items to reflect accurate totals on receipt
$display_subtotal = 0; // Sum of non-refunded items only
$refunded_subtotal = 0; // Sum of refunded items (for info/badges)
$all_items = [];
if (isset($_GET['id'])) {
  $oid = $_GET['id'];
  $itQry = $conn->query("SELECT oi.*, m.name FROM `order_items` oi INNER JOIN `menu_list` m ON oi.menu_id = m.id WHERE oi.order_id = '{$oid}'");
  while($r = $itQry->fetch_assoc()){
    $r['line_total'] = (float)$r['price'] * (int)$r['quantity'];
    if ((int)$r['refunded'] === 1) {
      $refunded_subtotal += $r['line_total'];
    } else {
      $display_subtotal += $r['line_total'];
    }
    $all_items[] = $r;
  }
}

// Recalculate VAT and discounts based on non-refunded subtotal
$receipt_vat_amount = 0;
if (!$is_vat_exempt) {
  $receipt_vat_amount = $display_subtotal ? ($display_subtotal * $vat_rate) : 0;
}
$receipt_discount_amount = 0;
if (!empty($discount_percent) && $discount_percent > 0) {
  $receipt_discount_amount = $display_subtotal * ($discount_percent / 100);
}
$receipt_grand_total = ($display_subtotal - $receipt_discount_amount) + $receipt_vat_amount;
$is_full_refund = ($display_subtotal <= 0 && count($all_items) > 0);
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
  <?php if ($is_full_refund): ?>
    <div class="text-center" style="color:#dc3545; font-weight:bold; margin:4px 0;">FULL ORDER REFUND</div>
  <?php endif; ?>
  <div style="display:flex; font-weight: bold;">
    <div style="width: 40%;">Item</div>
    <div style="width: 20%; text-align: right;">Price</div>
    <div style="width: 10%; text-align: center;">Qty</div>
    <div style="width: 30%; text-align: right;">Amount</div>
  </div>
  <?php if (!empty($all_items)): ?>
    <?php foreach($all_items as $row): ?>
      <?php 
        $isRefunded = isset($row['refunded']) && (int)$row['refunded'] === 1; 
        $lineTotal = isset($row['line_total']) ? $row['line_total'] : ((float)$row['price'] * (int)$row['quantity']);
      ?>
      <div style="display:flex; <?= $isRefunded ? 'color:#dc3545; text-decoration: line-through;' : '' ?>">
        <div style="width: 40%;">
          <?= htmlspecialchars($row['name']) ?>
          <?php if ($isRefunded): ?>
            <span style="font-size:10px; font-weight:bold; margin-left:4px;">[REFUNDED]</span>
          <?php endif; ?>
        </div>
        <div style="width: 20%; text-align: right;"><?= format_num($row['price'], 2) ?></div>
        <div style="width: 10%; text-align: center;"><?= (int)$row['quantity'] ?></div>
        <div style="width: 30%; text-align: right;"><?= format_num($lineTotal, 2) ?></div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <hr>
  <div style="display:flex;"><div style="width: 70%">SUBTOTAL:</div><div style="width:30%; text-align:right;"><?= format_num($display_subtotal, 2) ?></div></div>
  <div style="display:flex;"><div style="width: 70%">12% VAT:</div><div style="width:30%; text-align:right;"><?= format_num($receipt_vat_amount, 2) ?></div></div>
  <?php if (!empty($discount_code)): ?>
  <div style="display:flex;"><div style="width: 70%;">Discount Code (<?= $discount_type ?>):</div><div style="width:30%; text-align:right;"><?= $discount_code ?></div></div>
  <?php endif; ?>
  <div style="display:flex;"><div style="width: 70%">Discount (<?= $discount_percent ?>%):</div><div style="width:30%; text-align:right; "><?= $discount_percent == 0? 0: format_num($receipt_discount_amount, 2) ?></div></div>
  <div style="display:flex;"><div style="width: 70%; font-weight:bold;">TOTAL:</div><div style="width:30%; text-align:right; font-weight:bold; "><?= format_num($receipt_grand_total, 2) ?></div></div>
  <?php if($payment_method == 'cash'): ?>
    <?php $display_change = max(0, $cash - $receipt_grand_total); ?>
    <div style="display:flex;"><div style="width: 70%">CASH:</div><div style="width:30%; text-align:right; "><?= format_num($cash, 2) ?></div></div>
    <div style="display:flex;"><div style="width: 70%">CHANGE:</div><div style="width:30%; text-align:right; "><?= format_num($display_change, 2) ?></div></div>
  <?php elseif($payment_method == 'emoney'): ?>
    <div style="display:flex;"><div style="width: 70%;">E-Money Ref:</div><div style="width:30%; text-align:right;"><?= htmlspecialchars($emoney_reference) ?></div></div>
    <div style="display:flex;"><div style="width: 70%">AMOUNT PAID:</div><div style="width:30%; text-align:right; "><?= format_num($receipt_grand_total, 2) ?></div></div>
  <?php elseif($payment_method == 'credit_card'): ?>
    <div style="display:flex;"><div style="width: 70%;">Card Number:</div><div style="width:30%; text-align:right;"><?= $card_number ? '**** **** **** ' . substr($card_number, -4) : '' ?></div></div>
    <div style="display:flex;"><div style="width: 70%">AMOUNT PAID:</div><div style="width:30%; text-align:right; "><?= format_num($receipt_grand_total, 2) ?></div></div>
  <?php endif; ?>
  <hr>

  <?php $receipt_net_total = $display_subtotal - $receipt_vat_amount; ?>
  <div>
    VATable Sales: <?= format_num($receipt_net_total, 2) ?><br>
    VAT Amount: <?= format_num($receipt_vat_amount, 2) ?><br>
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
