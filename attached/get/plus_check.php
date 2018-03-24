<?php
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');

$cpp_id=$_GET['a'];
$numero=$_GET['b'];
$monto=$_GET['c'];
$montoletra=$r_function->replace_regular_character($_GET['d']);
$observacion=$r_function->replace_regular_character($_GET['e']);
$proveedor_id=$_GET['f'];
$fecha_actual = date('Y-m-d');

$link->query("INSERT INTO bh_cheque (cheque_AI_user_id, cheque_AI_cpp_id, TX_cheque_fecha, TX_cheque_numero,TX_cheque_monto,TX_cheque_montoletra,TX_cheque_observacion, cheque_AI_proveedor_id) VALUES ('{$_COOKIE['coo_iuser']}','$cpp_id','$fecha_actual','$numero','$monto','$montoletra','$observacion','$proveedor_id')")or die($link->error);

$qry_lastid = $link->query("SELECT LAST_INSERT_ID()")or die($link->error);
$rs_lastid = $qry_lastid->fetch_array();
$lastid = trim($rs_lastid[0]);
echo $lastid;
?>
