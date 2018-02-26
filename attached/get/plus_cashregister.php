<?php
require '../../bh_conexion.php';
$link = conexion();

date_default_timezone_set('America/Panama');

require '../php/req_login_paydesk.php';

$fecha_actual=date('Y-m-d');
//$fecha_actual='2017-09-11';
$hora_actual=date('h:i a');

$qry_datopago=$link->prepare("SELECT bh_datopago.AI_datopago_id, bh_datopago.datopago_AI_metododepago_id, bh_datopago.TX_datopago_monto
FROM ((bh_notadecredito
INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_notadecredito.notadecredito_AI_facturaf_id)
INNER JOIN bh_datopago ON bh_facturaf.AI_facturaf_id = bh_datopago.datopago_AI_facturaf_id)
WHERE bh_notadecredito.AI_notadecredito_id = ?")or die($link->error);

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

// ##################  IMPRESORA ############################################
$host_ip=ObtenerIP();
$host_name=gethostbyaddr($host_ip);
$qry_impresora=$link->query("SELECT AI_impresora_id FROM bh_impresora WHERE TX_impresora_cliente = '$host_name'");
$rs_impresoraid=$qry_impresora->fetch_array();

/* V#################  CALCULAR TOTALES DE LAS VENTAS  ###################V */

//  #########################    METODOS DE PAGO   ############################

$qry_metododepago=$link->query("SELECT AI_metododepago_id, TX_metododepago_value FROM bh_metododepago")or die($link->error);

$raw_metododepago=array();	$raw_pago=array();	$raw_debito=array();	$raw_nc_anulated=array();
while ($rs_metododepago = $qry_metododepago->fetch_array()) {
	$raw_metododepago[$rs_metododepago['AI_metododepago_id']] = $rs_metododepago['TX_metododepago_value'];
	$raw_pago[$rs_metododepago['AI_metododepago_id']] = 0;
	$raw_debito[$rs_metododepago['AI_metododepago_id']] = 0;
	$raw_nc_anulated[$rs_metododepago['AI_metododepago_id']] = 0;
}

//  #########################    FACTURAS FISCALES    ############################
$txt_facturaf="SELECT bh_facturaf.AI_facturaf_id, bh_datopago.TX_datopago_monto, bh_datopago.datopago_AI_metododepago_id, bh_facturaf.TX_facturaf_descuento as descuento
FROM (bh_facturaf
INNER JOIN bh_datopago ON bh_facturaf.AI_facturaf_id = bh_datopago.datopago_AI_facturaf_id)
WHERE bh_facturaf.facturaf_AI_impresora_id = '{$rs_impresoraid['0']}'
AND bh_facturaf.facturaf_AI_arqueo_id = '0'
AND bh_facturaf.facturaf_AI_user_id = '{$_COOKIE['coo_iuser']}'";

$qry_facturaf=$link->query($txt_facturaf)or die($link->error);
$raw_ffid=array();
$ttl_descuento=0; $i=0;
while($rs_facturaf=$qry_facturaf->fetch_array()){
	$raw_pago[$rs_facturaf['datopago_AI_metododepago_id']] += $rs_facturaf['TX_datopago_monto'];
	$ttl_descuento += $rs_facturaf['descuento'];
	$raw_ffid[$i] = $rs_facturaf['AI_facturaf_id'];
	$i++;
}
 echo "<br /> PAGOS: ".json_encode($raw_pago);

 $chk_raw_ffid = array_count_values($raw_ffid);
 unset($raw_ffid); $raw_ffid = array();
 $i=0;
 foreach ($chk_raw_ffid as $key => $value) {
 	$raw_ffid[$i] = $key;
 	$i++;
 }
 $cantidad_ff = $i;

 //  #########################    DEBITOS   ############################

$txt_notadebito="SELECT bh_notadebito.AI_notadebito_id, bh_datodebito.TX_datodebito_monto, bh_datodebito.datodebito_AI_metododepago_id
FROM (bh_notadebito
INNER JOIN bh_datodebito ON bh_notadebito.AI_notadebito_id = bh_datodebito.datodebito_AI_notadebito_id)
WHERE bh_notadebito.notadebito_AI_impresora_id = '{$rs_impresoraid['0']}'
AND bh_notadebito.notadebito_AI_arqueo_id = '0'";

$qry_notadebito=$link->query($txt_notadebito);
$raw_debitoid=array();
$i=0;
while($rs_notadebito=$qry_notadebito->fetch_array()){
	$raw_debito[$rs_notadebito['datodebito_AI_metododepago_id']] += $rs_notadebito['TX_datodebito_monto'];
	$raw_debitoid[$i]=$rs_notadebito['AI_notadebito_id'];
	$i++;
}
echo "<br >DEBITO: ".json_encode($raw_debito);


// $cantidad_pago = count($raw_ffid);

//  #########################    NOTAS DE CREDITO    ############################

$txt_devolucion="SELECT bh_notadecredito.AI_notadecredito_id,  bh_notadecredito.TX_notadecredito_destino, ROUND(bh_notadecredito.TX_notadecredito_monto+bh_notadecredito.TX_notadecredito_impuesto,2) AS NC, bh_notadecredito.TX_notadecredito_monto, bh_notadecredito.TX_notadecredito_impuesto, bh_notadecredito.TX_notadecredito_anulado
FROM bh_notadecredito
WHERE bh_notadecredito.notadecredito_AI_impresora_id = '{$rs_impresoraid['0']}'
AND bh_notadecredito.notadecredito_AI_arqueo_id = '0'";
$qry_devolucion=$link->query($txt_devolucion);
$nc_base=0;	$nc_impuesto=0;	$devolucion=0; $anulado=0;
$raw_ncid=array();
$ite=0;
while($rs_devolucion = $qry_devolucion->fetch_array()){
	$raw_ncid[$ite]=$rs_devolucion['AI_notadecredito_id'];
	$ite++;
	if($rs_devolucion['TX_notadecredito_anulado'] != 1){
		if($rs_devolucion['TX_notadecredito_destino'] == 'EFECTIVO'){
			$devolucion+=$rs_devolucion['NC'];
		}else {
			$nc_base += $rs_devolucion['TX_notadecredito_monto'];
			$nc_impuesto+=$rs_devolucion['TX_notadecredito_impuesto'];
		}
	}else{
		$qry_datopago->bind_param("i", $rs_devolucion['AI_notadecredito_id']); $qry_datopago->execute(); $result=$qry_datopago->get_result();
		$rs_datopago=$result->fetch_array();
		$raw_nc_anulated[$rs_datopago['datopago_AI_metododepago_id']]=$rs_datopago['TX_datopago_monto'];
		$anulado+=$rs_datopago['TX_datopago_monto'];
	}
}
$json_nc_anulated=json_encode($raw_nc_anulated);

echo "<br /> ANULADAS: ".json_encode($raw_nc_anulated);

echo "<br>Descuento: ".$ttl_descuento;

$venta_neta=0;
foreach($raw_pago as $pago){
	$venta_neta += $pago;
}
$venta_neta=$venta_neta-$devolucion-$anulado;
$venta_bruta=$venta_neta+$ttl_descuento;

echo "<br> Venta brut: ".$venta_bruta;

echo "<br> Venta neta: ".$venta_neta;


echo "<br> Devolucion: ".$devolucion;

//#######CALCULAR BASE IMPONIBLE

if ($cantidad_ff > 0) {

	$txt_base="SELECT bh_facturaventa.facturaventa_AI_facturaf_id, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento
	FROM ((bh_facturaf
	INNER JOIN bh_facturaventa ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
	INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
	WHERE";
	$line_ff="";
	for($it=0;$it<$cantidad_ff;$it++){
		if($it == $cantidad_ff-1){
			$line_ff.=" bh_facturaf.AI_facturaf_id = '$raw_ffid[$it]'";
		}else{
			$line_ff.=" bh_facturaf.AI_facturaf_id = '$raw_ffid[$it]' OR";
		}
	}
	$txt_base.$line_ff;
	$qry_base=$link->query($txt_base.$line_ff);
	$base_ni=0;
	$base_ci=0;
	$ttl_impuesto=0;
	while($rs_base=$qry_base->fetch_array()){
		$precio=$rs_base['TX_datoventa_cantidad']*$rs_base['TX_datoventa_precio'];
		$descuento=($rs_base['TX_datoventa_descuento']*$precio)/100;
		$precio_descuento=$precio-$descuento;
		if($rs_base['TX_datoventa_impuesto'] > 0){
			$impuesto = ($rs_base['TX_datoventa_impuesto']*$precio_descuento)/100;
			$base_descuento_impuesto = $precio_descuento+$impuesto;
			echo "<br/> SUMATORIA BASES: ".$base_ci += $precio_descuento;
			$ttl_impuesto += $impuesto;
		}else{
			echo $base_ni += $precio_descuento;
		}
	}

}else{
	$base_ni=0;
	$base_ci=0;
	$ttl_impuesto=0;
}

$txt_cajamenuda="SELECT bh_efectivo.AI_efectivo_id, bh_efectivo.TX_efectivo_monto, bh_efectivo.TX_efectivo_tipo
FROM bh_efectivo
WHERE bh_efectivo.efectivo_AI_impresora_id = '{$rs_impresoraid['0']}'
AND bh_efectivo.efectivo_AI_arqueo_id = '0'";
$qry_cajamenuda=$link->query($txt_cajamenuda);
$ttl_entrada=0;
$ttl_salida=0;
$raw_efectivoid=array();
$iter=0;
while($rs_cajamenuda=$qry_cajamenuda->fetch_array()){
	if($rs_cajamenuda['TX_efectivo_tipo']=='ENTRADA'){
		$ttl_entrada+=$rs_cajamenuda['TX_efectivo_monto'];
	}else{
		$ttl_salida+=$rs_cajamenuda['TX_efectivo_monto'];
	}
	$raw_efectivoid[$iter]=$rs_cajamenuda['AI_efectivo_id'];
	$iter++;
};

$line_pago="";
$json_datopago = json_encode($raw_pago);
$json_datodebito = json_encode($raw_debito);

	$txt_insert	=	"INSERT INTO bh_arqueo	(arqueo_AI_impresora_id, arqueo_AI_user_id, TX_arqueo_fecha, TX_arqueo_hora, TX_arqueo_pago, TX_arqueo_ventabruta, TX_arqueo_ventaneta, TX_arqueo_totalni, TX_arqueo_totalci, TX_arqueo_totalci_nc, TX_arqueo_impuesto, TX_arqueo_impuesto_nc, TX_arqueo_descuento, TX_arqueo_cantidadff, TX_arqueo_entrada, TX_arqueo_salida, TX_arqueo_devolucion, TX_arqueo_debito, TX_arqueo_anulado)	VALUES ('{$rs_impresoraid['AI_impresora_id']}', '{$_COOKIE['coo_iuser']}', '$fecha_actual', '$hora_actual', '$json_datopago', '$venta_bruta', '$venta_neta', '$base_ni', '$base_ci', '$nc_base', '$ttl_impuesto', '$nc_impuesto', '$ttl_descuento', '$cantidad_ff', '$ttl_entrada', '$ttl_salida', '$devolucion', '$json_datodebito', '$json_nc_anulated')";
	$link->query($txt_insert) or die($link->error);
	$qry_lastid=$link->query("SELECT LAST_INSERT_ID();");
	$rs_lastid = $qry_lastid->fetch_array();
	$last_arqueo = trim($rs_lastid[0]);

$_SESSION['arqueo_id'] = $last_arqueo;

//######################   UPDATES   ##################

foreach($raw_ffid as $ffid){
	$link->query("UPDATE bh_facturaf SET facturaf_AI_arqueo_id='$last_arqueo' WHERE AI_facturaf_id = '$ffid'")or die($link->error);
}

foreach ($raw_debitoid as $key => $debito_id) {
	$link->query("UPDATE bh_notadebito SET notadebito_AI_arqueo_id = '$last_arqueo' WHERE AI_notadebito_id = '$debito_id'")or die($link->error);
}

foreach($raw_ncid as $nc){
	$link->query("UPDATE bh_notadecredito SET notadecredito_AI_arqueo_id='$last_arqueo' WHERE AI_notadecredito_id = '$nc'")or die($link->error);
};

foreach($raw_efectivoid as $efectivo){
	$link->query("UPDATE bh_efectivo SET efectivo_AI_arqueo_id='$last_arqueo' WHERE AI_efectivo_id = '$efectivo'")or die($link->error);
};
