<?php
require '../../bh_con.php';
$link = conexion();
require '../php/req_login_sale.php';

function edit_quote($str){
$pat = array("\"", "'", "º", "laremun");
$rep = array("''", "\'", "&deg;", "#");
return $n_str = str_replace($pat, $rep, $str);
}

$report = edit_quote($_GET['a']);
$fecha_actual = date('Y-m-d');
$nr_report=mysql_num_rows(mysql_query("SELECT AI_reporte_id FROM bh_reporte WHERE TX_reporte_value = '$report' AND TX_reporte_status = 'ACTIVA'"));

if($nr_report < 1){
	mysql_query("INSERT INTO bh_reporte (reporte_AI_user_id, TX_reporte_fecha, TX_reporte_tipo, TX_reporte_value, TX_reporte_status) VALUES ('{$_COOKIE['coo_iuser']}','$fecha_actual','INVENTARIO','$report','ACTIVA')", $link) or die(mysql_error());
	echo "Reporte Guardado de manera exitosa.";
}else{
	echo "Este reporte ya fue realizado, comuniquese con el administrador.";
}

?>