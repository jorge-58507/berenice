<?php
require '../../bh_conexion.php';
$link = conexion();

$product_id=$_GET['a'];
$limit=$_GET['b'];
$date_i=date('Y-m-d',strtotime($_GET['c']));
$date_f=date('Y-m-d',strtotime($_GET['d']));

$array_ps=array();
if($limit == ""){	$line_limit="";	}else{	$line_limit= " LIMIT ".$limit;	}
if (!empty($date_i) && !empty($date_f)) {
	$line_date_facturacompra = " AND TX_facturacompra_fecha >=	'$date_i' AND TX_facturacompra_fecha <= '$date_f'";
	$line_date_facturaf = " AND TX_facturaf_fecha >=	'$date_i' AND TX_facturaf_fecha <= '$date_f'";
}
//  ###########################   PURCHASED     ###################
$txt_facturacompra="SELECT bh_facturacompra.AI_facturacompra_id, bh_facturacompra.TX_facturacompra_fecha, bh_facturacompra.TX_facturacompra_numero, bh_almacen.TX_almacen_value, bh_facturacompra.TX_facturacompra_ordendecompra, bh_proveedor.TX_proveedor_nombre, bh_datocompra.TX_datocompra_cantidad, bh_datocompra.TX_datocompra_existencia
FROM (((bh_facturacompra
INNER JOIN bh_datocompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id)
      INNER JOIN bh_proveedor ON bh_facturacompra.facturacompra_AI_proveedor_id = bh_proveedor.AI_proveedor_id)
	  INNER JOIN bh_almacen ON bh_facturacompra.TX_facturacompra_almacen = bh_almacen.AI_almacen_id)
WHERE bh_datocompra.datocompra_AI_producto_id = '$product_id' AND bh_facturacompra.TX_facturacompra_preguardado < 1 ".$line_date_facturacompra." ORDER BY TX_facturacompra_fecha DESC, AI_facturacompra_id DESC".$line_limit;
$qry_facturacompra=$link->query($txt_facturacompra)or die($link->error);
$raw_facturacompra=array();
while($rs_facturacompra=$qry_facturacompra->fetch_array(MYSQLI_ASSOC)){
	$raw_facturacompra[]=$rs_facturacompra;
}
//   ##########################      SOLD       #######################
$txt_facturaf="SELECT bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento,  bh_datoventa.TX_datoventa_stock,
bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_numero, bh_cliente.TX_cliente_nombre, bh_user.TX_user_seudonimo
FROM ((((bh_datoventa
INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_facturaf.facturaf_AI_cliente_id)
INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaf.facturaf_AI_user_id)
WHERE bh_datoventa.datoventa_AI_producto_id = '$product_id'".$line_date_facturaf."
ORDER BY TX_facturaf_fecha DESC, TX_facturaf_numero DESC".$line_limit;
$qry_facturaf=$link->query($txt_facturaf)or die($link->error);
$raw_facturaf=array();
while ($rs_facturaf=$qry_facturaf->fetch_array(MYSQLI_ASSOC)) {
	$raw_facturaf[]=$rs_facturaf;
}
//   ########################   NOTAS DE CREDITO    ###########################
$txt_nc="SELECT bh_datodevolucion.TX_datodevolucion_cantidad, bh_notadecredito.TX_notadecredito_fecha, bh_notadecredito.TX_notadecredito_numero, bh_cliente.TX_cliente_nombre, bh_notadecredito.TX_notadecredito_anulado
FROM ((bh_datodevolucion
INNER JOIN bh_notadecredito ON bh_notadecredito.AI_notadecredito_id = bh_datodevolucion.datodevolucion_AI_notadecredito_id)
INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_notadecredito.notadecredito_AI_cliente_id)
WHERE bh_datodevolucion.datodevolucion_AI_producto_id = '$product_id' AND TX_notadecredito_fecha >=	'$date_i' AND TX_notadecredito_fecha <= '$date_f'
ORDER BY TX_notadecredito_fecha DESC, TX_notadecredito_numero ASC".$line_limit;
$qry_nc=$link->query($txt_nc)or die($link->error);
$raw_nc=array();
while ($rs_nc = $qry_nc->fetch_array(MYSQLI_ASSOC)) {
	$raw_nc[]=$rs_nc;
}


array_push($array_ps, $raw_facturacompra, $raw_facturaf, $raw_nc);

echo json_encode($array_ps);
?>
