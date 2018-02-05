<?php
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');

$cpp_id=$_GET['b'];
$json_payment = $_GET['a'];
$raw_payment = json_decode($json_payment, true);

$qry_cpp = $link->query("SELECT TX_cpp_saldo FROM bh_cpp WHERE AI_cpp_id = '$cpp_id'")or die($link->error);
$rs_cpp = $qry_cpp->fetch_array();
$total_pagado=0;
foreach ($raw_payment as $key => $value) {
	$fecha = date('Y-m-d',strtotime($value['fecha']));
	$link->query("INSERT INTO bh_datocpp (TX_datocpp_monto,TX_datocpp_numero,TX_datocpp_fecha,datocpp_AI_cpp_id,datocpp_AI_user_id,datocpp_AI_metododepago_id) VALUES ('{$value['monto']}','{$value['numero']}','$fecha','$cpp_id','{$_COOKIE['coo_iuser']}','{$value['metodo']}')")or die($link->error);
	$total_pagado+=$value["monto"];
}
$total_pagado = round($total_pagado,2);
$saldo_pendiente = round($rs_cpp['TX_cpp_saldo'],2);

$new_saldo = ($saldo_pendiente>=$total_pagado) ? $rs_cpp['TX_cpp_saldo']-$total_pagado : 0;

$link->query("UPDATE bh_cpp SET TX_cpp_saldo = '$new_saldo' WHERE AI_cpp_id = '$cpp_id'")or die($link->error);
echo $cpp_id;
