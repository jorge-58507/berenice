<?php
require '../../bh_conexion.php';
$link = conexion();

$conteo=$_GET['a'];
$producto_id=$_GET['b'];
$fecha_actual = date('Y-m-d');

// $link->query("UPDATE bh_producto SET TX_producto_cantidad = '$conteo' WHERE AI_producto_id = '$producto_id'")or die($link->error);

$qry_inventario = $link->query("SELECT AI_inventario_id,TX_inventario_json FROM bh_inventario WHERE inventario_AI_producto_id = '$producto_id'")or die($link->error);
$new_inventario = [$fecha_actual => $conteo];
// echo "new array ".json_encode($new_inventario);
// return false;
if ($qry_inventario->num_rows > 0) {
	$rs_inventario = $qry_inventario->fetch_array(MYSQLI_ASSOC);
	$raw_inventario = json_decode($rs_inventario['TX_inventario_json'],true);
	$raw_inventario[] = $new_inventario;
	$json_inventario = json_encode($raw_inventario);
	$link->query("UPDATE bh_inventario SET TX_inventario_json = '$json_inventario' WHERE AI_inventario_id = '{$rs_inventario['AI_inventario_id']}'")or die($link->error);
}else {
	$raw_inventario[] = $new_inventario;
	$json_inventario = json_encode($raw_inventario);
	$link->query("INSERT INTO bh_inventario (inventario_AI_producto_id, TX_inventario_json) VALUES ('$producto_id','$json_inventario')")or die($link->error);
}

$link->query("UPDATE bh_producto SET TX_producto_cantidad = '$conteo', TX_producto_inventariado = 1 WHERE AI_producto_id = '$producto_id'")or die($link->error);
echo $conteo;
