<?php
require '../../bh_conexion.php';
$link=conexion();
$date_i = date('Y-m-d',strtotime($_GET['a']));
$date_f = date('Y-m-d',strtotime($_GET['b']));
$product_id = $_GET['c'];
$conteo=$_GET['d'];

$txt_purchase="SELECT TX_datocompra_cantidad, TX_datocompra_precio, TX_datocompra_impuesto, TX_datocompra_descuento FROM (bh_datocompra INNER JOIN bh_facturacompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id)
WHERE bh_facturacompra.TX_facturacompra_fecha >= '$date_i' AND  bh_facturacompra.TX_facturacompra_fecha <= '$date_f' AND bh_datocompra.datocompra_AI_producto_id = '$product_id' AND bh_facturacompra.TX_facturacompra_preguardado != 1";
$qry_purchase=$link->query($txt_purchase);
$cantidad_comprada=0;
while($rs_purchase=$qry_purchase->fetch_array()){
	echo "<br />comprados ".$rs_purchase[0];
	$cantidad_comprada+=$rs_purchase[0];
}
$cantidad_ingresada = $cantidad_comprada+$conteo;
$txt_sold="SELECT TX_datoventa_cantidad
FROM ((bh_datoventa
INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
WHERE bh_facturaf.TX_facturaf_fecha >= '$date_i' AND bh_facturaf.TX_facturaf_fecha <= '$date_f' AND bh_datoventa.datoventa_AI_producto_id = '$product_id'";

$qry_sold=$link->query($txt_sold);
$cantidad_vendida=0;
$raw_sold=array();
$i=0;
while($rs_sold=$qry_sold->fetch_array()){
	$raw_sold[$i]=$rs_sold;
	$i++;
	$cantidad_vendida+=$rs_sold[0];
}
//echo json_encode($raw_sold);
if($cantidad_ingresada === 0){
	$relation=0;
}else{
	$relation = ($cantidad_vendida*100)/$cantidad_ingresada;
}
$relation = round($relation,2);

// ###################   R y R

$content = file_get_contents("../tool/reduce_recompose/reduce_recompose.json");
// echo $content;
$raw_contenido = json_decode($content, true);
$reducido=0; $sumado=0;
foreach ($raw_contenido['saved'] as $index => $saved) {
	foreach ($saved['minus'] as $key => $minus) {
		if ($minus['producto_id'] === $product_id) {
			$reducido += $minus['cantidad'];
		}
	}
	foreach ($saved['plus'] as $key => $plus) {
		if ($plus['producto_id'] === $product_id) {
			$sumado += $plus['cantidad'];
		}
	}
}


$raw_relation=array(0 => $cantidad_ingresada, 1 => $cantidad_vendida, 2 => $relation, 3 => $sumado, 4 => $reducido);

echo json_encode($raw_relation);
// echo '{"0":'.$cantidad_comprada.', "1":'.$cantidad_vendida.', "2":'.$relation.'}';

//mysql_query("UPDATE bh_producto SET TX_producto_relacion = '$relation' WHERE AI_producto_id = '$product_id'");
?>
