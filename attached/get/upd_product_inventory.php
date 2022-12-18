<?php
require '../../bh_conexion.php';
$link = conexion();

$conteo=$_GET['a']; 
$producto_id=$_GET['b'];
$fecha_actual = date('Y-m-d');

$qry_inventario = $link->query("SELECT AI_inventario_id,TX_inventario_json FROM bh_inventario WHERE inventario_AI_producto_id = '$producto_id'")or die($link->error);
$qry_producto = $link->query("SELECT AI_producto_id, TX_producto_cantidad,TX_producto_value,TX_producto_codigo FROM bh_producto WHERE AI_producto_id = '$producto_id'")or die($link->error);
$rs_producto = $qry_producto->fetch_array(MYSQLI_ASSOC);
$existencia = $rs_producto['TX_producto_cantidad'];
$new_inventario = [$fecha_actual => $conteo, "user" => $_COOKIE['coo_iuser'], "existencia" => $existencia];
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

// ##################   CREAR NOTIFICACION
$raw_user = $r_function->read_user();
$qry_user = $link->query("SELECT AI_user_id, TX_user_seudonimo FROM bh_user WHERE TX_user_type = '2' AND TX_user_activo = '1' or TX_user_type = '5'  AND TX_user_activo = '1'")or die($link->error);
$value = $rs_producto['TX_producto_value']; $codigo = $rs_producto['TX_producto_codigo'];
while($rs_user=$qry_user->fetch_array(MYSQLI_ASSOC)) {
	$content = $raw_user[$_COOKIE['coo_iuser']].' cambi&oacute; el conteo a '.$value.' ('.$codigo.') de '.$existencia.' a '.$conteo;
	$r_function->method_message('create', $_COOKIE['coo_iuser'], $rs_user['AI_user_id'], 'Producto Inventariado', $content, 'notification', date('H:i:s'), date('d-m-Y'));
}
echo $conteo;
