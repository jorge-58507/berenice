<?php
require '../../bh_conexion.php';
$link = conexion();

$provider=$_GET['a'];
$billnumber=$_GET['b'];


	$qry_checkinvoice=$link->query("SELECT AI_facturacompra_id FROM bh_facturacompra 
	WHERE	facturacompra_AI_proveedor_id = '$provider' AND
	TX_facturacompra_numero = '$billnumber' AND TX_facturacompra_preguardado = '0'")or die ($link->error);
	if($qry_checkinvoice->num_rows < 1){
		echo 0; 
	}else{
		echo 1;
	};
?>
