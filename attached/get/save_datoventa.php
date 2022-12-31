<?php
require '../../bh_conexion.php';
$link = conexion();

$producto_id = $_GET['a'];
$cantidad = $_GET['b'];
$precio = $_GET['c'];
$impuesto = $_GET['d'];
$descuento = $_GET['e'];
$datoventa_id = $_GET['f'];

$qry_datoventa=$link->query("SELECT datoventa_AI_facturaventa_id FROM bh_datoventa WHERE  AI_datoventa_id = '$datoventa_id'");
$row_datoventa=$qry_datoventa->fetch_row();
$facturaventa_id=$row_datoventa[0];

function cal_datoventa($datoventa_id){
	$link = conexion();
	$qry_datoventa=$link->query("SELECT AI_datoventa_id, datoventa_AI_facturaventa_id, datoventa_AI_user_id, datoventa_AI_producto_id, TX_datoventa_cantidad, TX_datoventa_precio, TX_datoventa_impuesto, TX_datoventa_descuento, TX_datoventa_modificado FROM bh_datoventa WHERE  AI_datoventa_id = '$datoventa_id'");
	$rs_datoventa=$qry_datoventa->fetch_assoc();

	$descuento = ($rs_datoventa['TX_datoventa_descuento']*$rs_datoventa['TX_datoventa_precio'])/100;
	$precio_descuento = $rs_datoventa['TX_datoventa_precio']-$descuento;
	$impuesto = ($rs_datoventa['TX_datoventa_impuesto']*$precio_descuento)/100;
	$precio_total = $rs_datoventa['TX_datoventa_cantidad']*($precio_descuento+$impuesto);

	return $precio_total;
}
function upd_facturaventatotal($funct,$facturaventa_id,$resto){
	$link = conexion();
	$qry_facturaventatotal=$link->query("SELECT TX_facturaventa_total FROM bh_facturaventa WHERE AI_facturaventa_id = '$facturaventa_id'");
	$row_facturaventatotal=$qry_facturaventatotal->fetch_row();
	if($funct == 'resta'){
		$total = $row_facturaventatotal[0]-$resto;
	}else if($funct == 'suma'){
		$total = $row_facturaventatotal[0]+$resto;
	}
	$link->query("UPDATE bh_facturaventa SET TX_facturaventa_total = '$total' WHERE AI_facturaventa_id = '$facturaventa_id'");
}

/*    ######################### FIN DE FUNCIONES   ############################  */

$total_datoventa = cal_datoventa($datoventa_id);

$link->query("UPDATE bh_datoventa SET datoventa_AI_producto_id = '$producto_id', TX_datoventa_cantidad = '$cantidad', TX_datoventa_precio = '$precio', TX_datoventa_impuesto = '$impuesto', TX_datoventa_descuento = '$descuento', TX_datoventa_modificado = '1' WHERE AI_datoventa_id = '$datoventa_id'")or die($link->error);

$total_datoventa_nuevo = cal_datoventa($datoventa_id);

if($total_datoventa > $total_datoventa_nuevo){
	$resto=$total_datoventa-$total_datoventa_nuevo;
	upd_facturaventatotal('resta',$facturaventa_id,$resto);
}else{
	$resto=$total_datoventa_nuevo-$total_datoventa;
	upd_facturaventatotal('suma',$facturaventa_id,$resto);
}
	echo json_encode(['status'=>'success'])
?>
