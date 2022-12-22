<?php
require '../../bh_con.php';
$link = conexion();
require '../php/req_login_admin.php';
date_default_timezone_set('America/Panama');

$fecha = date('Y-m-d');
$hora = date('h:i a');

$numero_nc = $_GET['b'];
$cliente_id = $_GET['c'];;
$motivo = $_GET['d'];
$monto = $_GET['e'];

// ############################## FUNCIONES #####################
function checknumeronc($numero_nc){
	$qry=mysql_query("SELECT * FROM bh_notadecredito WHERE TX_notadecredito_numero = '$numero_nc'");
	$nr=mysql_num_rows($qry);
	if($nr > 0){
		return sumarnumeronc($numero_nc);
	}else{
		return $numero_nc;
	}
}
function sumarnumeronc($numero_nc){
	$pre_numero_nc = "00000000".($numero_nc +1);
		$numero_nc = substr($pre_numero_nc,-8);
		return checknumeronc($numero_nc);
}
// ############################## FUNCIONES #########################

$numero_nc = checknumeronc($numero_nc);
mysql_query("INSERT INTO bh_notadecredito (notadecredito_AI_cliente_id, notadecredito_AI_user_id, TX_notadecredito_tipo, TX_notadecredito_numero, TX_notadecredito_monto, TX_notadecredito_exedente, TX_notadecredito_motivo, TX_notadecredito_fecha, TX_notadecredito_hora, TX_notadecredito_destino) VALUES ('$cliente_id', '$user_id','2','$numero_nc','$monto','$monto','$motivo','$fecha','$hora','SALDO')", $link)or die(mysql_error());

$qry_lastid = mysql_query("SELECT LAST_INSERT_ID();");
$rs_lastid = mysql_fetch_row($qry_lastid);
$last_id = trim($rs_lastid[0]);

$_SESSION['creditnote_id'] = $last_id;
