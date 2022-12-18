<?php
require '../../bh_conexion.php';
$link=conexion();

$verb = $_GET['a'];

$respond = $r_function->method_message($verb);

return $respond;

?>
