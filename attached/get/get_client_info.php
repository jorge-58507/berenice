<?php
require '../../bh_conexion.php';
$link = conexion();

$client_id=$_GET['a'];

$qry_client = $link->query("SELECT AI_cliente_id, TX_cliente_nombre, TX_cliente_telefono, TX_cliente_direccion, TX_cliente_cif FROM bh_cliente WHERE AI_cliente_id = '$client_id'")or die($link->error);
$raw_client=array();

while ($rs_client = $qry_client -> fetch_array() ) {
	$raw_client[$rs_client['AI_cliente_id']]['nombre'] = $rs_client['TX_cliente_nombre'];
	$raw_client[$rs_client['AI_cliente_id']]['telefono'] = $rs_client['TX_cliente_telefono'];
	$raw_client[$rs_client['AI_cliente_id']]['direccion'] = $rs_client['TX_cliente_direccion'];
	$raw_client[$rs_client['AI_cliente_id']]['cif'] = $rs_client['TX_cliente_cif'];
}

echo json_encode($raw_client);

?>
