<?php
require '../../bh_conexion.php';
$link = conexion();

$cheque_id=$_GET['a'];

$qry_cpp =	$link->query("SELECT AI_cpp_id FROM (bh_cpp INNER JOIN bh_cheque ON bh_cheque.cheque_AI_cpp_id = bh_cpp.AI_cpp_id) WHERE AI_cheque_id = '$cheque_id'")or die($link->error);
$rs_cpp = $qry_cpp->fetch_array();
$cpp_id = $rs_cpp['AI_cpp_id'];

$link->query("UPDATE bh_cheque SET cheque_AI_cpp_id = '0', TX_cheque_status = 'ALMACENADO' WHERE AI_cheque_id = '$cheque_id'")or die($link->error);


// #####################  ANSWER  #########################

$qry_cheque = $link->query("SELECT AI_cheque_id, TX_cheque_numero, TX_cheque_monto, TX_cheque_status
	FROM (bh_cheque
		INNER JOIN bh_cpp ON bh_cpp.AI_cpp_id = bh_cheque.cheque_AI_cpp_id)
		WHERE bh_cpp.AI_cpp_id = '$cpp_id'")or die($link->error);

  $raw_cheque = array();
	$raw_cheque[0]=array();
	$raw_cheque[1]=array();

 	while($rs_cheque = $qry_cheque->fetch_array(MYSQLI_ASSOC)){
    $raw_cheque[0][]=$rs_cheque;
  }

  $qry_proveedor = $link->query("SELECT AI_proveedor_id FROM (bh_proveedor INNER JOIN bh_cpp ON bh_cpp.cpp_AI_proveedor_id = bh_proveedor.AI_proveedor_id) WHERE AI_cpp_id = '$cpp_id'")or die($link->error);
  $rs_proveedor=$qry_proveedor->fetch_array();
  $qry_cheque_proveedor = $link->query("SELECT AI_cheque_id, TX_cheque_numero, TX_cheque_monto, TX_cheque_status FROM bh_cheque WHERE bh_cheque.cheque_AI_proveedor_id = '{$rs_proveedor['AI_proveedor_id']}'")or die($link->error);
  while ($rs_cheque_proveedor = $qry_cheque_proveedor->fetch_array(MYSQLI_ASSOC)) {
    $raw_cheque[1][]=$rs_cheque_proveedor;
  }

	$raw_cheque[3]=$cpp_id;

  echo json_encode($raw_cheque);

?>
