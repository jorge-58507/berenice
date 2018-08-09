<?php
require '../../../bh_conexion.php';
$link = conexion();
$raw_str = $_GET['a'];
$fecha_actual = date('Y-m-d');
echo json_encode($raw_str);
$prep_precio = $link->prepare ("INSERT INTO bh_precio (precio_AI_producto_id, precio_AI_medida_id, TX_precio_uno, TX_precio_dos, TX_precio_tres, TX_precio_cuatro, TX_precio_cinco, TX_precio_fecha ) VALUES (?,?,?,?,?,?,?,'$fecha_actual')");
foreach ($raw_str as $product_id => $raw_price) {

	$link->query("UPDATE bh_precio SET TX_precio_inactivo='1' WHERE precio_AI_producto_id = '$product_id' AND precio_AI_medida_id = '{$raw_price['medida']}'")or die($link->error);
	$prep_precio->bind_param("iisssss", $product_id,$raw_price['medida'],$raw_price['p1'],$raw_price['p2'],$raw_price['p3'],$raw_price['p4'],$raw_price['p5']);
	$prep_precio->execute();
}
