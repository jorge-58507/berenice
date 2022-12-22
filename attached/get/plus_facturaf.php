<?php
set_time_limit(180);

require '../../bh_conexion.php';
$link = conexion();
require '../php/req_login_paydesk.php';

if (!empty($_COOKIE['coo_iuser'])) {	$uid=$_COOKIE['coo_iuser'];	}else{ return false;	}
$fecha_actual=date('Y-m-d');
$hora_actual=date('h:i a');

$str_factid = $_GET['a'];
$arr_factid = explode(",",$str_factid);

$client_id = $_GET['b'];

/* V ########################### FUNCIONES ################### V */
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

function ins_facturaf($cliente, $uid, $fecha_actual, $hora_actual, $numero_ff, $subtotal_ni, $descuento_ni, $subtotal_ci, $impuesto, $descuento_ci, $total_ff, $impresora_id){ //SE INSERTA CON DEUDA TOTAL
	$link = conexion();
	$txt_insert="INSERT INTO bh_facturaf (facturaf_AI_cliente_id, facturaf_AI_user_id, facturaf_AI_impresora_id, TX_facturaf_fecha, TX_facturaf_hora, TX_facturaf_numero, TX_facturaf_subtotalni, TX_facturaf_subtotalci, TX_facturaf_impuesto, TX_facturaf_descuento, TX_facturaf_total, TX_facturaf_deficit, TX_facturaf_descuentoni) VALUES ('$cliente', '{$_COOKIE['coo_iuser']}', '$impresora_id', '$fecha_actual', '$hora_actual', '$numero_ff', '$subtotal_ni', '$subtotal_ci', '$impuesto', '$descuento_ci', '$total_ff', '$total_ff', '$descuento_ni')";
	$link->query($txt_insert)or die($link->error);
	$qry_lastid=$link->query("SELECT LAST_INSERT_ID();")or die($link->error);
	$rs_lastid = $qry_lastid->fetch_array();
	return $last_id = trim($rs_lastid[0]);
	$link->close();
}

function ins_payment($facturaf_id,$uid,$payment_method,$payment_amount,$payment_number,$fecha_actual){
	$link = conexion();
	$link->query("INSERT INTO bh_datopago (datopago_AI_facturaf_id, datopago_AI_user_id, datopago_AI_metododepago_id, TX_datopago_monto, TX_datopago_numero,TX_datopago_fecha) VALUES ('$facturaf_id','$uid','$payment_method','$payment_amount','$payment_number','$fecha_actual')")or die ($link->error);
	$link->close();
}
function upd_facturaventa($facturaf_id,$facturaventa_id,$client_id){
	$link = conexion();
	$link->query("UPDATE bh_facturaventa SET facturaventa_AI_facturaf_id = '$facturaf_id', TX_facturaventa_status = 'CANCELADA', facturaventa_AI_cliente_id = '$client_id' WHERE AI_facturaventa_id = '$facturaventa_id'")or die ($link->error);
	$link->close();
}
function checkfacturaf($numero_ff){
	$link = conexion();
	$pre_numero_ff = substr("00000000".$numero_ff, -8);
	$qry_checkfacturaf=$link->query("SELECT AI_facturaf_id FROM bh_facturaf WHERE TX_facturaf_numero = '$pre_numero_ff'")or die($link->error);
	$nr_checkfacturaf = $qry_checkfacturaf->num_rows;
	$link -> close();
	if($nr_checkfacturaf > 0){
		return sumarfacturaf($numero_ff);
	}else{
		return $numero_ff;
	}
}
function sumarfacturaf($numero_ff){
	$pre_numero_ff = "00000000".($numero_ff +1);
		$numero_ff = substr($pre_numero_ff,-8);
		return checkfacturaf($numero_ff);
}
/* ^########################### FUNCIONES ##################### ^ */

$host_ip=ObtenerIP();
$host_name=gethostbyaddr($host_ip);
$qry_impresora = $link->query("SELECT AI_impresora_id, TX_impresora_retorno, TX_impresora_recipiente FROM bh_impresora WHERE TX_impresora_cliente = '$host_name'")or die($link->error);
$nr_impresora = $qry_impresora->num_rows;
if ($nr_impresora < 1) {
	echo "Impresora no encontrada";
	return false;
}
$rs_impresora=$qry_impresora->fetch_array();
$impresora_id = $rs_impresora['AI_impresora_id'];
$recipiente = $rs_impresora['TX_impresora_recipiente'];
// ############################# 								VERIFICAR SI HAY ACCESO A LA RED								########################
$retorno = $rs_impresora['TX_impresora_retorno'];
if (!file_exists($recipiente)) {
	if(!mkdir($recipiente, 0777, true)){
		echo "No se puede acceder a recipiente";
		return false;
	};
}

/* V#################CALCULAR TOTALES DE LOS PRODUCTOS EN LA FACTURA  ###################V */
$txt_facturaventa="SELECT bh_facturaventa.AI_facturaventa_id, bh_datoventa.datoventa_AI_producto_id, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento, bh_producto.TX_producto_exento, bh_facturaventa.facturaventa_AI_cliente_id, bh_datoventa.TX_datoventa_medida
FROM ((bh_facturaventa
INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
WHERE";
foreach ($arr_factid as $key => $value) {
	if ($value === end($arr_factid)) {
		$txt_facturaventa=$txt_facturaventa." AI_facturaventa_id = '$value' ORDER BY AI_facturaventa_id ASC, AI_datoventa_id ASC ";
	}else {
		$txt_facturaventa=$txt_facturaventa." AI_facturaventa_id = '$value' OR";
	}
}
$qry_facturaventa=$link->query($txt_facturaventa)or die($link->error);
$raw_facturaventa = array();

$subtotal_ni=0;
$subtotal_ci=0;
$impuesto=0;
$descuento_ni=0;
$descuento_ci=0;
$prep_producto = $link->prepare("SELECT TX_producto_cantidad FROM bh_producto WHERE AI_producto_id = ?")or die($link->error);
$prep_producto_medida = $link->prepare("SELECT AI_rel_productomedida_id, TX_rel_productomedida_cantidad FROM rel_producto_medida WHERE productomedida_AI_producto_id = ? AND productomedida_AI_medida_id = ?")or die($link->error);

while($rs_facturaventa=$qry_facturaventa->fetch_array(MYSQLI_ASSOC)){
	$prep_producto->bind_param("i",$rs_facturaventa['datoventa_AI_producto_id']); $prep_producto->execute(); $qry_producto = $prep_producto->get_result();
	$rs_producto = $qry_producto->fetch_array(MYSQLI_ASSOC);

	$prep_producto_medida->bind_param("ii",$rs_facturaventa['datoventa_AI_producto_id'],$rs_facturaventa['TX_datoventa_medida']); $prep_producto_medida->execute(); $qry_producto_medida = $prep_producto_medida->get_result();
	$rs_producto_medida = $qry_producto_medida->fetch_array(MYSQLI_ASSOC);

	$resta=$rs_producto['TX_producto_cantidad']-($rs_facturaventa['TX_datoventa_cantidad']*$rs_producto_medida['TX_rel_productomedida_cantidad']);
	$link->query("UPDATE bh_producto SET TX_producto_cantidad = '$resta' WHERE AI_producto_id = '{$rs_facturaventa['datoventa_AI_producto_id']}' AND TX_producto_descontable = '1'")or die($link->error);
	$link->query("UPDATE bh_datoventa SET TX_datoventa_entrega = 1 WHERE datoventa_AI_facturaventa_id = '{$rs_facturaventa['AI_facturaventa_id']}'")or die($link->error);

	$desc=($rs_facturaventa['TX_datoventa_descuento']*$rs_facturaventa['TX_datoventa_precio'])/100;
	$precio_descuento=$rs_facturaventa['TX_datoventa_precio']-$desc;
	$imp=($rs_facturaventa['TX_datoventa_impuesto']*$precio_descuento)/100;
	if($rs_facturaventa['TX_datoventa_impuesto'] === 0){
		$subtotal_ni+=$precio_descuento*$rs_facturaventa['TX_datoventa_cantidad'];
		$descuento_ni+=$desc*$rs_facturaventa['TX_datoventa_cantidad'];
	}else{
		$subtotal_ci+=$precio_descuento*$rs_facturaventa['TX_datoventa_cantidad'];
		$descuento_ci+=$desc;
		$impuesto+=$imp*$rs_facturaventa['TX_datoventa_cantidad'];
	}
}
$total_ff = $subtotal_ni+$subtotal_ci+$impuesto;
$total_ff=round($total_ff,2);
/* ^#################CALCULAR TOTALES DE LOS PRODUCTOS EN LA FACTURA  ###################^ */
/* V#################INSERCION DE  LA FACTURA  ###################V */
$qry_facturaf_numero=$link->query("SELECT AI_facturaf_id, TX_facturaf_numero FROM bh_facturaf ORDER BY AI_facturaf_id DESC LIMIT 1")or die($link->error);
$rs_facturaf_numero=$qry_facturaf_numero->fetch_array();
$numero_ff = $rs_facturaf_numero['TX_facturaf_numero']; //Calcular el numero de factura.
$numero_ff = checkfacturaf($numero_ff);
$last_ff = ins_facturaf($client_id,$uid,$fecha_actual,$hora_actual,$numero_ff,$subtotal_ni,$descuento_ni,$subtotal_ci,$impuesto,$descuento_ci,$total_ff,$impresora_id);
$_SESSION['facturaf_id']=$last_ff; //DECLARACION DEL SESSION PARA LA IMPRESION
/* ^ ################# INSERCION DE  LA FACTURA  ################### ^ */
/* V ################# INSERCION DE  LOS PAGOS  ###################V */
$total_pagado=0;
$qry_payment = $link->query("SELECT pago_AI_user_id,pago_AI_metododepago_id,TX_pago_monto,TX_pago_numero FROM bh_pago WHERE pago_AI_user_id = '$uid'")or die($link->error);
$efectivo['monto'] = 0;
$cheque['monto'] = 0;
while ($rs_payment = $qry_payment->fetch_array()) {
	$total_pagado+=$rs_payment['TX_pago_monto'];
	if ($rs_payment['pago_AI_metododepago_id'] != 1) { //Si el metodo no es en efectivo insertar el pago
	 	ins_payment($last_ff,$uid,$rs_payment['pago_AI_metododepago_id'],$rs_payment['TX_pago_monto'],$rs_payment['TX_pago_numero'],$fecha_actual);
		if ($rs_payment['pago_AI_metododepago_id'] == 7) { //Si fue pagado con saldo hacer la disminucion del saldoo disponible
			$qry_cliente = $link->query("SELECT TX_cliente_saldo FROM bh_cliente WHERE AI_cliente_id = '$client_id'")or die($link->error);
			$rs_cliente = $qry_cliente->fetch_array();
			$new_saldo = $rs_cliente['TX_cliente_saldo'] - $rs_payment['TX_pago_monto'];
			$link->query("UPDATE bh_cliente SET TX_cliente_saldo = '$new_saldo' WHERE AI_cliente_id = '$client_id'")or die($link->error);
		}
		if ($rs_payment['pago_AI_metododepago_id'] == 2) { //Si el metodo es cheque llenar esta variable
			$cheque['monto'] = $rs_payment['TX_pago_monto'];
			$cheque['numero'] = $rs_payment['TX_pago_numero'];
		}

	}else{
		$efectivo['monto'] = $rs_payment['TX_pago_monto'];
		$efectivo['numero'] = $rs_payment['TX_pago_numero'];
	}
}
$total_pagado=round($total_pagado,2);
if ($total_pagado > $total_ff) { //VERIFICAR EL CAMBIO
	$cambio = $total_pagado - $total_ff;
	$cambio_ff = $cambio;
	$link->query("UPDATE bh_facturaf SET TX_facturaf_cambio = '$cambio_ff' WHERE AI_facturaf_id = '$last_ff'"); //AGREGAR EL CAMBIO A LA FF
	if ($efectivo['monto'] > 0) { //SI HAY PAGO EN EFECTIVO RESTAR EL CAMBIO Y CAMBIAR EL CAMBIO A 0
	 	ins_payment($last_ff,$uid,1,$efectivo['monto'],$efectivo['numero'],$fecha_actual);
		$cambio = 0;
	}
	if ($cheque['monto'] > 0) { //SI HAY PAGO EN CHEQUE Y EL CAMBIO ES MAYOR A 0, INSERTAR UNA SALIDA POR EL CHEQUE
		if ($cambio > 0) {
			$motivo= 'CAMBIO CHEQUE '.$cheque['numero'];
			$link->query("INSERT INTO bh_efectivo (efectivo_AI_user_id, efectivo_AI_impresora_id, TX_efectivo_tipo, TX_efectivo_motivo, TX_efectivo_monto, TX_efectivo_fecha, TX_efectivo_status)	VALUES ('$uid', '$impresora_id','SALIDA','$motivo','$cambio','$fecha_actual','ACTIVA')")or die($link->error);
		}
	}
}
foreach ($arr_factid as $key => $value) {
	upd_facturaventa($last_ff,$value,$client_id);
}
echo "acepted"
?>
