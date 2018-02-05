<?php
require '../../bh_conexion.php';
$link = conexion();

$raw_value=$_GET['a'];

$qry_order=$link->prepare("UPDATE bh_datopedido SET TX_datopedido_revisado= ? WHERE AI_datopedido_id = ?")or die($link->error);

foreach($raw_value as $index => $value){
	$qry_order->bind_param("di",$value,$index);
	if($qry_order->execute()){
	}
echo "All Right";
$last_datopedido_id=$index;
}
	$qry_datopedido = $link->query("SELECT datopedido_AI_pedido_id FROM bh_datopedido WHERE AI_datopedido_id = '$last_datopedido_id'");
	$rs_datopedido= $qry_datopedido->fetch_array();
	$pedido_id = $rs_datopedido['datopedido_AI_pedido_id'];
	$link->query("UPDATE bh_pedido SET TX_pedido_status = 'RECIBIDO' WHERE AI_pedido_id = '$pedido_id'");
?>
