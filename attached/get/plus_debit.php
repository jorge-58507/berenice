<?php
require '../../bh_conexion.php';
$link = conexion();
require '../php/req_login_admin.php';
date_default_timezone_set('America/Panama');
$fecha_actual=date('Y-m-d');
// ############################## FUNCIONES #####################
function ObtenerIP(){
	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"),"unknown"))
	$ip = getenv("HTTP_CLIENT_IP");
	else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
	$ip = getenv("HTTP_X_FORWARDED_FOR");
	else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
	$ip = getenv("REMOTE_ADDR");
	else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
	$ip = $_SERVER['REMOTE_ADDR'];
	else
	$ip = "IP desconocida";
	return($ip);
}

function insert_notadebito($client_id,$user_id,$numero_nd,$motivo,$fecha,$hora){
	$link = conexion();

	$host_ip=ObtenerIP();
	$host_name=gethostbyaddr($host_ip);
	$qry_impresora = $link->query("SELECT AI_impresora_id FROM bh_impresora WHERE TX_impresora_cliente = '$host_name'") or die ($link->error);
	$rs_impresora=$qry_impresora->fetch_array();

	$bh_insert_nd="INSERT INTO bh_notadebito (notadebito_AI_cliente_id, notadebito_AI_user_id, notadebito_AI_impresora_id, TX_notadebito_numero, TX_notadebito_motivo, TX_notadebito_fecha, TX_notadebito_hora) VALUES ('$client_id', '$user_id', '{$rs_impresora['AI_impresora_id']}', '$numero_nd', '$motivo', '$fecha', '$hora')";
	$link->query($bh_insert_nd) or die ($link->error);

	$qry_lastid=$link->query("SELECT LAST_INSERT_ID();");
	$rs_lastid = $qry_lastid->fetch_array();
	return $last_id = trim($rs_lastid[0]);
	$link->close();

}

function insert_datodebito($debito_id,$user_id,$metododepago_id,$debito_monto,$debito_numero,$fecha){
	$link = conexion();
	$bh_insert_datodebito="INSERT INTO bh_datodebito (datodebito_AI_notadebito_id, datodebito_AI_user_id,  datodebito_AI_metododepago_id, TX_datodebito_monto, TX_datodebito_numero, TX_datodebito_fecha) VALUES ('$debito_id','$user_id','$metododepago_id','$debito_monto','$debito_numero','$fecha')";
	$link->query($bh_insert_datodebito)or die($link->error);
	$link->close();
}

function checknumerond($numero_nd){
	$link = conexion();
	$qry=$link->query("SELECT * FROM bh_notadebito WHERE TX_notadebito_numero = '$numero_nd'")or die($link->error);
	$nr=$qry->num_rows;
	$link->close();
	if($nr > 0){
		return sumarnumerond($numero_nd);
	}else{
		return $numero_nd;
	}
}
function sumarnumerond($numero_nd){
	$pre_numero_nd = "00000000".($numero_nd +1);
		$numero_nd = substr($pre_numero_nd,-8);
		return checknumerond($numero_nd);
}

// ############################## FUNCIONES #########################
$host_ip=ObtenerIP();
$host_name=gethostbyaddr($host_ip);
$qry_impresora = $link->query("SELECT AI_impresora_id FROM bh_impresora WHERE TX_impresora_cliente = '$host_name'") or die ($link->error);
$rs_impresora=$qry_impresora->fetch_array();
$impresora_id = $rs_impresora['AI_impresora_id'];
//   ############################  CHK FACTURAF  ###############################
$str_factid=$_GET['b'];

$motivo = $_GET['a'];

$qry_lastnd=$link->query("SELECT AI_notadebito_id, TX_notadebito_numero FROM bh_notadebito ORDER BY AI_notadebito_id DESC LIMIT 1")or die($link->error);
$rs_lastnd=$qry_lastnd->fetch_array();
$numero_nd = $rs_lastnd['TX_notadebito_numero'];
$numero_nd = checknumerond($numero_nd);
$fecha = date('Y-m-d');
$hora = date('h:i a');

$arr_factid = explode(",",$str_factid);

$txt_clientid="SELECT facturaf_AI_cliente_id FROM bh_facturaf WHERE";
foreach ($arr_factid as $key => $value) {
	if ($value === end($arr_factid)) {
		$txt_clientid=$txt_clientid." AI_facturaf_id = '$value'";
	}else{
		$txt_clientid=$txt_clientid." AI_facturaf_id = '$value' OR";
	}
}
$qry_clientid=$link->query($txt_clientid)or die($link->error);
$row_clientid=$qry_clientid->fetch_array();
$client_id=$row_clientid['facturaf_AI_cliente_id'];

$txt_chk_facturaf="SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_deficit
FROM (bh_facturaf
INNER JOIN bh_cliente ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id)
WHERE";
foreach ($arr_factid as $key => $value) {
	if ($value === end($arr_factid)) {
		$txt_chk_facturaf .= " bh_facturaf.TX_facturaf_deficit > '0' AND bh_facturaf.facturaf_AI_cliente_id = '$client_id' AND bh_facturaf.AI_facturaf_id = '$value' ORDER BY bh_facturaf.AI_facturaf_id ASC, bh_facturaf.TX_facturaf_deficit ASC";
	}else{
		$txt_chk_facturaf .= " bh_facturaf.TX_facturaf_deficit > '0' AND bh_facturaf.facturaf_AI_cliente_id = '$client_id' AND bh_facturaf.AI_facturaf_id = '$value' OR";
	}
}

$qry_chk_facturaf=$link->query($txt_chk_facturaf)or die($link->error);
$nr_chk_facturaf=$qry_chk_facturaf->num_rows;
if($nr_chk_facturaf < 1){	print_r("las ff estan mal");	return false;	}
$raw_ffid=array();
while($rs_chk_facturaf=$qry_chk_facturaf->fetch_array()){
	$raw_ffid[$rs_chk_facturaf['AI_facturaf_id']]['deficit']=$rs_chk_facturaf['TX_facturaf_deficit'];
	$raw_ffid[$rs_chk_facturaf['AI_facturaf_id']]['pago']="";
}
//   ############################  CHK FACTURAF  ###############################

// ################################## INSERCION NOTA DE DEBITO  #######################################

$debito_id = insert_notadebito($client_id,$user_id,$numero_nd,$motivo,$fecha,$hora);

// ################################## INSERCIONES  #######################################
// ########################## INSERTAR PAGOS Y CALCULAR MONTO TOTAL PAGADO  ############################

$txt_nuevodebito="SELECT bh_nuevodebito.nuevodebito_AI_metododepago_id, bh_nuevodebito.TX_nuevodebito_monto, bh_nuevodebito.TX_nuevodebito_numero, bh_nuevodebito.TX_nuevodebito_fecha
FROM bh_nuevodebito
WHERE bh_nuevodebito.nuevodebito_AI_user_id = '$user_id' ORDER BY nuevodebito_AI_metododepago_id DESC";
$qry_nuevodebito=$link->query($txt_nuevodebito);
$total_pagado=0;
$raw_nuevodebito=array();
while($rs_nuevodebito=$qry_nuevodebito->fetch_array()){
	$total_pagado+=$rs_nuevodebito['TX_nuevodebito_monto'];
	$raw_nuevodebito[$rs_nuevodebito['nuevodebito_AI_metododepago_id']]['monto'] = $rs_nuevodebito['TX_nuevodebito_monto'];
	$raw_nuevodebito[$rs_nuevodebito['nuevodebito_AI_metododepago_id']]['numero'] = $rs_nuevodebito['TX_nuevodebito_numero'];
	$raw_nuevodebito[$rs_nuevodebito['nuevodebito_AI_metododepago_id']]['fecha'] = $rs_nuevodebito['TX_nuevodebito_fecha'];
};
$pagado = $total_pagado;
foreach ($raw_ffid as $key => $value) {
	$resta = $value['deficit'] - $pagado;
	$resta = round($resta,2);
	if ($resta <= 0) {
		$raw_ffid[$key]['pago']=$value['deficit']*1;
		$pagado -= $value['deficit'];
		$pagado = round($pagado,2);
	}else{
		$raw_ffid[$key]['pago']=$value['deficit']-$resta;
		$pagado = 0;
	}
}

// echo json_encode($raw_ffid);
$cambio = $pagado;
$total_notadebito = 0;
// echo "<br /> cambio: ".$cambio;
foreach ($raw_ffid as $key => $value) {
	$new_deficit = $value['deficit'] - $value['pago'];
	$new_deficit = round($new_deficit,2);
	$link->query("UPDATE bh_facturaf SET TX_facturaf_deficit = '$new_deficit' WHERE AI_facturaf_id = '$key'");
	$link->query("INSERT INTO rel_facturaf_notadebito (rel_AI_facturaf_id, rel_AI_notadebito_id, TX_rel_facturafnotadebito_importe) VALUES ('$key','$debito_id','{$value['pago']}')");
	$total_notadebito += $value['pago'];
}
// @@@@@@@@@@@@@@@@@@@@@ ARREGLO DE PAGOS

if ($cambio > 0.01) {
	if (array_key_exists(1,$raw_nuevodebito)) {
		$raw_nuevodebito[1]['monto'] = $raw_nuevodebito[1]['monto']-$cambio;
	}elseif (array_key_exists(2,$raw_nuevodebito)) {
		$motivo_cambio_cheque="CAMBIO A CHEQUE ".$raw_nuevodebito[2]['numero'];
		$link->query("INSERT INTO bh_efectivo (efectivo_AI_user_id, efectivo_AI_impresora_id, TX_efectivo_tipo, TX_efectivo_motivo, TX_efectivo_monto, TX_efectivo_fecha, TX_efectivo_status)	VALUES ('$user_id', '$impresora_id','SALIDA','$motivo_cambio_cheque','$cambio','$fecha_actual','ACTIVA')")or die($link->error);
	}
	$link->query("UPDATE bh_notadebito SET TX_notadebito_cambio = '$cambio'  WHERE AI_notadebito_id = '$debito_id'")or die($link->error);
}
// echo "<br /> nuevodebito: ".json_encode($raw_nuevodebito);

foreach ($raw_nuevodebito as $key => $value) {
	insert_datodebito($debito_id, $user_id, $key, $value['monto'],$value['numero'],$value['fecha']);
	if ($key === 7) {
		$qry_cliente = $link->query("SELECT TX_cliente_saldo FROM bh_cliente WHERE AI_cliente_id = '$client_id'")or die($link->error);
		$rs_cliente = $qry_cliente->fetch_array();
		$new_saldo = $rs_cliente['TX_cliente_saldo'] - $value['monto'];
		$link->query("UPDATE bh_cliente SET TX_cliente_saldo = '$new_saldo' WHERE AI_cliente_id = '$client_id'")or die($link->error);
	}
}

$link->query("UPDATE bh_notadebito SET TX_notadebito_total = '$total_notadebito'  WHERE AI_notadebito_id = '$debito_id'")or die($link->error);

$_SESSION['debito_id']=$debito_id;
echo $debito_id;
