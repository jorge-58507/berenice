<?php
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');

$verb = 'create';
$content = $_GET['a'];
$title = $_GET['d'];
$type = $_GET['e'];
$hour = date('H:i:s');
$date = date('d-m-Y');
$slug = time();

$link->query("INSERT INTO bh_mensaje (emisor_AI_user_id,receptor_AI_user_id,TX_mensaje_titulo,TX_mensaje_value,TX_mensaje_tipo,TX_mensaje_activo,TX_mensaje_hora,TX_mensaje_fecha,TX_mensaje_slug) VALUES ('2','1','$title','$content','$type','1','$hour','$date','$slug')")or die($link->error);

return ['success'];
?>
