<?php
require '../../bh_conexion.php';
$link = conexion();

$client_id=$_GET['a'];
$limite_credito=$_GET['b'];
$plazo_credito=$_GET['c'];
$porcobrar = $_GET['d'];
$client_name = $_GET['e'];
$client_restringido = $_GET['f'];
$client_exempt = $_GET['g'];

	$txt_upd="UPDATE bh_cliente SET TX_cliente_limitecredito='$limite_credito', TX_cliente_plazocredito='$plazo_credito', TX_cliente_porcobrar = '$porcobrar', TX_cliente_nombre = '$client_name', TX_cliente_restringido = '$client_restringido', TX_cliente_exento = '$client_exempt' WHERE AI_cliente_id = '$client_id'";
	$link->query($txt_upd)or die($link->error);

	echo $txt_upd;

?>
