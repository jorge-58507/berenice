<?php
require '../../bh_con.php';
$link = conexion();

$producto_id = $_GET['a'];
$cantidad = $_GET['b'];
$precio = $_GET['c'];
$impuesto = $_GET['d'];
$descuento = $_GET['e'];
$datoventa_id = $_GET['f'];

$qry_datoventa=mysql_query("SELECT datoventa_AI_facturaventa_id FROM bh_datoventa WHERE  AI_datoventa_id = '$datoventa_id'", $link);
$row_datoventa=mysql_fetch_row($qry_datoventa);
$facturaventa_id=$row_datoventa[0];

function cal_datoventa($datoventa_id){
$link = conexion();
$qry_datoventa=mysql_query("SELECT AI_datoventa_id, datoventa_AI_facturaventa_id, datoventa_AI_user_id, datoventa_AI_producto_id, TX_datoventa_cantidad, TX_datoventa_precio, TX_datoventa_impuesto, TX_datoventa_descuento, TX_datoventa_modificado FROM bh_datoventa WHERE  AI_datoventa_id = '$datoventa_id'", $link);
$rs_datoventa=mysql_fetch_assoc($qry_datoventa);

	$descuento = ($rs_datoventa['TX_datoventa_descuento']*$rs_datoventa['TX_datoventa_precio'])/100;
	$precio_descuento = $rs_datoventa['TX_datoventa_precio']-$descuento;
	$impuesto = ($rs_datoventa['TX_datoventa_impuesto']*$precio_descuento)/100;
	$precio_total = $rs_datoventa['TX_datoventa_cantidad']*($precio_descuento+$impuesto);

return $precio_total;
}
function upd_facturaventatotal($funct,$facturaventa_id,$resto){
	$qry_facturaventatotal=mysql_query("SELECT TX_facturaventa_total FROM bh_facturaventa WHERE AI_facturaventa_id = '$facturaventa_id'");
	$row_facturaventatotal=mysql_fetch_row($qry_facturaventatotal);
	if($funct == 'resta'){
		$total = $row_facturaventatotal[0]-$resto;
	}else if($funct == 'suma'){
		$total = $row_facturaventatotal[0]+$resto;
	}
	mysql_query("UPDATE bh_facturaventa SET TX_facturaventa_total = '$total' WHERE AI_facturaventa_id = '$facturaventa_id'");
}

/*    ######################### FIN DE FUNCIONES   ############################  */

$total_datoventa = cal_datoventa($datoventa_id);

mysql_query("UPDATE bh_datoventa SET datoventa_AI_producto_id = '$producto_id', TX_datoventa_cantidad = '$cantidad', TX_datoventa_precio = '$precio', TX_datoventa_impuesto = '$impuesto', TX_datoventa_descuento = '$descuento', TX_datoventa_modificado = '1' WHERE AI_datoventa_id = '$datoventa_id'")or die(mysql_error());

$total_datoventa_nuevo = cal_datoventa($datoventa_id);

if($total_datoventa > $total_datoventa_nuevo){
	$resto=$total_datoventa-$total_datoventa_nuevo;
	upd_facturaventatotal('resta',$facturaventa_id,$resto);
	echo "restara a fv: ".$resto."<br>";
}else{
	$resto=$total_datoventa_nuevo-$total_datoventa;
	upd_facturaventatotal('suma',$facturaventa_id,$resto);
	echo "sumara a fv: ".$resto."<br>";
}
 echo $total_datoventa."<br>";
 echo $total_datoventa_nuevo."<br>";

?>
