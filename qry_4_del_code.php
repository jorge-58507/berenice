<?php
require 'bh_conexion.php';
$link=conexion();

$json_code = $_GET['a'];

print_r(json_decode($json_code));

?>
