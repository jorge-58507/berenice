<?php
require '../../bh_conexion.php';
$link = conexion();
$qry_nuevacompra=$link->query("SELECT * FROM bh_nuevacompra WHERE nuevacompra_AI_user_id = '{$_COOKIE['coo_iuser']}'")or die($link->error);
$nr_nuevacompra=$qry_nuevacompra->num_rows;


if($nr_nuevacompra > 0){
	$link->query("DELETE FROM bh_nuevacompra WHERE nuevacompra_AI_user_id = '{$_COOKIE['coo_iuser']}'");
}
