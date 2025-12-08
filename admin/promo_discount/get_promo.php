<?php
require_once('../../config.php');

if(isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $query = $conn->query("SELECT * FROM `promo_discount` WHERE id = {$id} AND delete_flag = 0");
  
  if($query->num_rows > 0) {
    $promo = $query->fetch_assoc();
    echo json_encode($promo);
  } else {
    echo json_encode(null);
  }
} else {
  echo json_encode(null);
}
?>
