<?php
require '../../bh_con.php';
$link=conexion();
$date_i = date('Y-m-d',strtotime($_GET['a']));
$date_f = date('Y-m-d',strtotime($_GET['b']));
$product_id = $_GET['c'];

$txt_purchase="SELECT TX_datocompra_cantidad, TX_datocompra_precio, TX_datocompra_impuesto, TX_datocompra_descuento FROM (bh_datocompra INNER JOIN bh_facturacompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id) 
WHERE bh_facturacompra.TX_facturacompra_fecha >= '$date_i' AND  bh_facturacompra.TX_facturacompra_fecha <= '$date_f' AND bh_datocompra.datocompra_AI_producto_id = '$product_id'";
$qry_purchase=mysql_query($txt_purchase);
$cantidad_comprada=0;
while($rs_purchase=mysql_fetch_array($qry_purchase)){
$cantidad_comprada+=$rs_purchase[0];
}

$txt_sold="SELECT TX_datoventa_cantidad 
FROM ((bh_datoventa 
INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
WHERE bh_facturaf.TX_facturaf_fecha >= '$date_i' AND bh_facturaf.TX_facturaf_fecha <= '$date_f' AND bh_datoventa.datoventa_AI_producto_id = '$product_id'";
//echo $txt_sold;

$qry_sold=mysql_query($txt_sold);
$cantidad_vendida=0;
$raw_sold=array();
$i=0;
while($rs_sold=mysql_fetch_array($qry_sold)){
	$raw_sold[$i]=$rs_sold;
	$i++;
	$cantidad_vendida+=$rs_sold[0];
}
//echo json_encode($raw_sold);
if($cantidad_comprada == 0){
	$relation=0;
}else{
$relation = ($cantidad_vendida*100)/$cantidad_comprada;
}

$relation = round($relation,2);
// echo "comprado: ".$cantidad_comprada." vendido: ".$cantidad_vendida." relacion: ".$relation;
$raw_relation=array($cantidad_comprada, $cantidad_vendida, $relation);
echo '{"0":'.$cantidad_comprada.', "1":'.$cantidad_vendida.', "2":'.$relation.'}';

//mysql_query("UPDATE bh_producto SET TX_producto_relacion = '$relation' WHERE AI_producto_id = '$product_id'");
?>