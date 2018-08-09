<?php
require 'bh_conexion.php';
$link=conexion();

$link->query("UPDATE bh_cliente SET TX_cliente_porcobrar = '1' WHERE AI_cliente_id = '5219'")or die($link->error);

 ?>
