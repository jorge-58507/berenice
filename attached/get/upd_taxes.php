<?php
//ini_set('max_execution_time', 300);
//require '../../bh_con.php';
//$link = conexion();
require '../../bh_conexion.php';
$link=conexion();
$raw_value=$_GET['a'];

$raw_taxmodified=array();
// echo count($raw_taxmodified);
foreach($raw_value as $index =>	$value){
	$txt_impuesto="SELECT AI_impuesto_id, TX_impuesto_value FROM bh_impuesto WHERE AI_impuesto_id = '$index' AND TX_impuesto_categoria = 'ESPECIAL'";
	$qry_impuesto = $link->query($txt_impuesto)or die($link->error);
	if($qry_impuesto->num_rows > 0){
		$rs_impuesto = $qry_impuesto->fetch_array();
		if($rs_impuesto[1] != $value){
			$raw_taxmodified[$rs_impuesto[0]]=$rs_impuesto[1];
		}
	}
	$link->query("UPDATE bh_impuesto SET TX_impuesto_value = '$value' WHERE AI_impuesto_id = '$index'")or die(mysql_error());
}

$total_taxes=0;
$txt_taxes="SELECT TX_impuesto_value FROM bh_impuesto WHERE TX_impuesto_categoria =	'GENERAL'";
$qry_taxes =	$link->query($txt_taxes);
while ($rs_taxes = $qry_taxes->fetch_array()) {
	$total_taxes+=$rs_taxes[0];
}
//echo count($raw_taxmodified);
foreach ($raw_taxmodified as $index => $value) {
	$txt_product="SELECT bh_producto.AI_producto_id
			FROM ((bh_producto
				INNER JOIN rel_producto_impuesto ON rel_producto_impuesto.rel_AI_producto_id = bh_producto.AI_producto_id)
				INNER JOIN bh_impuesto ON rel_producto_impuesto.rel_AI_impuesto_id = bh_impuesto.AI_impuesto_id)
				WHERE rel_producto_impuesto.rel_AI_impuesto_id = '$index'
				GROUP BY AI_producto_id";
	$qry_product=$link->query($txt_product);
	$rs_product=$qry_product->fetch_array();

	$taxbyproduct="SELECT bh_producto.AI_producto_id, SUM(bh_impuesto.TX_impuesto_value) AS impuesto
			FROM ((bh_producto
				INNER JOIN rel_producto_impuesto ON rel_producto_impuesto.rel_AI_producto_id = bh_producto.AI_producto_id)
				INNER JOIN bh_impuesto ON rel_producto_impuesto.rel_AI_impuesto_id = bh_impuesto.AI_impuesto_id)
				WHERE rel_producto_impuesto.rel_AI_producto_id = '{$rs_product['AI_producto_id']}'
				GROUP BY AI_producto_id";
	$qry_taxbyproduct=$link->query($taxbyproduct)or die($link->error);
	$rs_taxbyproduct=$qry_taxbyproduct->fetch_array();
	$product_tax=$total_taxes+$rs_taxbyproduct['impuesto'];

	$link->query("UPDATE bh_producto SET TX_producto_exento='$product_tax' WHERE AI_producto_id = '{$rs_product['AI_producto_id']}'");
}

$qry_opcion = $link->query("SELECT TX_opcion_value FROM bh_opcion WHERE TX_opcion_titulo='IMPUESTO'");
$impuesto = $qry_opcion->fetch_array();
$link->query("UPDATE bh_opcion SET TX_opcion_value = '$total_taxes' WHERE TX_opcion_titulo = 'IMPUESTO'")OR die($link->error);

$qry_product_special = $link->query("SELECT AI_producto_id, TX_producto_exento FROM bh_producto WHERE TX_producto_exento != '$impuesto[0]'");
$raw_product_special=array();
$i=0;
while ($rs_product_special =	$qry_product_special->fetch_array()) {
	$raw_product_special[$rs_product_special[0]]= $rs_product_special[1];
}
$link->query("UPDATE bh_producto SET TX_producto_exento = '$total_taxes'");

foreach($raw_product_special as $index => $product_special){
	$exento = $product_special;
	$sustraccion = $exento-$impuesto[0];
	$new_exento = $sustraccion+$total_taxes;
	$txt_update="UPDATE bh_producto SET TX_producto_exento = '$new_exento' WHERE AI_producto_id = '$index'";
	$link->query($txt_update)or die($link->error);
}

echo $total_taxes;
$qry_taxes->free();
$link->close();
?>
