<?php
require '../../bh_conexion.php';
$link = conexion();

$cheque_id=$_GET['a'];
$status = $_GET['b'];

$link->query("UPDATE bh_cheque SET TX_cheque_status = '$status' WHERE AI_cheque_id = '$cheque_id'")or die($link->error);

$qry_cpp = $link->query("SELECT cheque_AI_cpp_id FROM bh_cheque WHERE AI_cheque_id = '$cheque_id'")or die($link->error);
$rs_cpp = $qry_cpp->fetch_array(); $cpp_id = $rs_cpp['cheque_AI_cpp_id'];

$qry_cheque = $link->query("SELECT AI_cheque_id, TX_cheque_numero, TX_cheque_monto, TX_cheque_status
	FROM (bh_cheque
		INNER JOIN bh_cpp ON bh_cpp.AI_cpp_id = bh_cheque.cheque_AI_cpp_id)
		WHERE bh_cpp.AI_cpp_id = '$cpp_id'")or die($link->error);

  $raw_cheque = array();
 	while($rs_cheque = $qry_cheque->fetch_array(MYSQLI_ASSOC)){
    $raw_cheque[0][]=$rs_cheque;
  }

  echo json_encode($raw_cheque);

  ?>
