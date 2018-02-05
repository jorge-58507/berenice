<?php
require '../../bh_con.php';
$link = conexion();
session_start();
$str = $_GET['a'];

unset($_SESSION[$str]); 

echo "respuesta";
	
?>
  