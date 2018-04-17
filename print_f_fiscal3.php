<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_paydesk.php';

$facturaf_id=$_SESSION["facturaf_id"];

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

$ip   = ObtenerIP();
$cliente = gethostbyaddr($ip);
$qry_impresora=$link->query("SELECT AI_impresora_id, TX_impresora_recipiente, TX_impresora_retorno, TX_impresora_cliente, TX_impresora_serial FROM bh_impresora WHERE TX_impresora_cliente = '$cliente'");
$rs_impresora=$qry_impresora->fetch_array();
$impresora_id = $rs_impresora['AI_impresora_id'];

$txt_facturaf="SELECT bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_direccion,
bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_descuento, bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_cambio
FROM (bh_facturaf
INNER JOIN bh_cliente ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id)
WHERE bh_facturaf.AI_facturaf_id = '$facturaf_id'";
$qry_facturaf=$link->query($txt_facturaf)or die($link->error);
$rs_facturaf=$qry_facturaf->fetch_array();
$minuendo=$rs_facturaf['TX_facturaf_deficit'];
if (!empty($rs_facturaf['TX_facturaf_cambio'])) { $cambio=round($rs_facturaf['TX_facturaf_cambio'],2); }else{  $cambio = 0 ;}

$txt_datopago="SELECT bh_datopago.TX_datopago_monto, bh_datopago.TX_datopago_numero, bh_datopago.datopago_AI_metododepago_id
 FROM bh_datopago
WHERE bh_datopago.datopago_AI_facturaf_id = '$facturaf_id'";
$qry_datopago=$link->query($txt_datopago)or die($link->error);
$raw_datopago=array();
$total_pagado=0;
while($rs_datopago=$qry_datopago->fetch_array()){
	$total_pagado += $rs_datopago['TX_datopago_monto'];
	$raw_datopago[$rs_datopago['datopago_AI_metododepago_id']]['monto'] = $rs_datopago['TX_datopago_monto'];
	$raw_datopago[$rs_datopago['datopago_AI_metododepago_id']]['numero'] = $rs_datopago['TX_datopago_numero'];
};
if(array_key_exists(5,$raw_datopago)){
	$link->query("UPDATE bh_facturaf SET TX_facturaf_deficit = '{$raw_datopago[5]['monto']}', TX_facturaf_status = 'IMPRESA' WHERE AI_facturaf_id = '$facturaf_id'");
}else{
	$link->query("UPDATE bh_facturaf SET TX_facturaf_deficit = '0', TX_facturaf_status = 'IMPRESA' WHERE AI_facturaf_id = '$facturaf_id'");
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv='Refresh' content='3;url=paydesk.php?a=<?php echo $facturaf_id; ?>' />
<title>Trilli, S.A. - Todo en Materiales</title>

<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap-theme.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_layout.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_blocks.css" rel="stylesheet" type="text/css" />
<link href="attached/css/sell_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>

<script type="text/javascript">

$(document).ready(function() {

$("#btn_navsale").click(function(){
	window.location="sale.php";
});
$("#btn_navstock").click(function(){
	window.location="stock.php";
});
$("#btn_navpaydesk").click(function(){
	window.location="paydesk.php";
})
$("#btn_navadmin").click(function(){
	window.location="start_admin.php";
});

$("#btn_start").click(function(){
	window.location="start.php";
});
$("#btn_exit").click(function(){
	location.href="index.php";
})

setTimeout(function(){ print_html("print_client_facturaf.php?a=<?php echo $facturaf_id; ?>"); },2500);

setTimeout(function(){ upd_return('<?php echo $facturaf_id; ?>'); },2500);

});

function upd_return(facturaf_id){
	$.ajax({	data: {"b" : facturaf_id},	type: "GET",	dataType: "text",	url: "attached/get/upd_return.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 console.log("GOOD " + textStatus);
	 })
	 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD " + textStatus );	});
}
</script>

</head>

<body>

<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-2" >
  	<div id="logo" ></div>
   	</div>

	<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-10">
		<div id="navigation" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

		</div>
	</div>

</div>

<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<form action="" method="post" name="print_f_fiscal"  id="print_f_fiscal">

<?php

$raw_facti=["documento" => "", "c_nombre" => "", "c_ruc" => "", "c_direccion" => "", "total_descuento" => "", "total_pagado" => "", "total_final" => "", "recargo" => "", "porcentaje_recargo" => "",
"p_efectivo" => "", "p_tdc" => "", "p_cheque" => "", "p_tdd" => "", "p_nc" => "", "p_otro" => "", "dv" => ""];

$raw_facti['documento']="FACTI".substr($rs_facturaf['TX_facturaf_numero'],-7);
$raw_facti['c_nombre']=$rs_facturaf['TX_cliente_nombre'];
$raw_facti['c_ruc']=$rs_facturaf['TX_cliente_cif'];
$raw_facti['c_direccion']=$rs_facturaf['TX_cliente_direccion'];
if (!empty($rs_facturaf['TX_facturaf_descuento'])) { $raw_facti['total_descuento']=round($rs_facturaf['TX_facturaf_descuento'],2);  }else{ $raw_facti['total_descuento'] = '0.00'; }
$raw_facti['total_pagado']=round($total_pagado,2);
$raw_facti['total_final']=round($rs_facturaf['TX_facturaf_total'],2);
if (!empty($raw_datopago[1]['monto'])) {  $raw_facti['p_efectivo']=round($raw_datopago[1]['monto']+$cambio,2); }else{ $raw_facti['p_efectivo'] = '0.00'; }
if (!empty($raw_datopago[2]['monto'])) {  $raw_facti['p_cheque']=round($raw_datopago[2]['monto'],2); }else{ $raw_facti['p_cheque'] = '0.00'; }
if (!empty($raw_datopago[3]['monto'])) {  $raw_facti['p_tdc']=round($raw_datopago[3]['monto'],2); }else{ $raw_facti['p_tdc'] = '0.00'; }
if (!empty($raw_datopago[4]['monto'])) {  $raw_facti['p_tdd']=round($raw_datopago[4]['monto'],2); }else{ $raw_facti['p_tdd'] = '0.00'; }
if (!empty($raw_datopago[5]['monto'])) {  $raw_facti['p_otro']=round($raw_datopago[5]['monto'],2); }else{ $raw_facti['p_otro'] = '0.00'; }
if (!empty($raw_datopago[7]['monto'])) {  $raw_facti['p_nc']=round($raw_datopago[7]['monto'],2); }else{ $raw_facti['p_nc'] = '0.00'; }

$recipiente = $rs_impresora['TX_impresora_recipiente'];
$retorno = $rs_impresora['TX_impresora_retorno'];
if (!file_exists($recipiente)) {
    mkdir($recipiente, 0777, true);
}
?>

<?php
/* ###################### ENCABEZADO  ######################## */
$total_pagado=$total_pagado+$cambio;
$file = fopen($recipiente."FACTI".substr($rs_facturaf['TX_facturaf_numero'],-7).".txt", "w");
$str_factid="";
foreach ($raw_facti as $key => $value) {
  if ($value === reset($raw_facti)) {
    $str_factid .= $value;
  }else{
    $str_factid .= chr(9).$value;
  }
}

//fwrite($file, "FACTI".substr($str,-7).chr(9).$rs_facturaf['cliente_nombre'].chr(9).$rs_facturaf['ruc'].chr(9).$rs_facturaf['direccion'].chr(9).round($rs_facturaf['descuento'],2).chr(9).round($total_pagado,2).chr(9).round($rs_facturaf['total'],2).chr(9).chr(9).chr(9).round($total_efectivo,2).chr(9).round($total_cheque,2).chr(9).round($total_tarjeta_debito,2).chr(9).round($total_credito,2).chr(9)   );
fwrite($file, $str_factid  );

fclose($file);


/* ####################### ENCABEZADO  ###################### */
/* ####################### ARTICULOS  ###################### */

// $txt_datoventa="SELECT bh_producto.AI_producto_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_descripcion,
// (bh_datoventa.TX_datoventa_precio-((bh_datoventa.TX_datoventa_precio*bh_datoventa.TX_datoventa_descuento)/100)) AS precio, bh_datoventa.TX_datoventa_impuesto
// FROM (((bh_facturaf
// INNER JOIN bh_facturaventa ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
// INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
// INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
// WHERE bh_facturaf.AI_facturaf_id = '$facturaf_id'";

$txt_datoventa="SELECT bh_producto.AI_producto_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_descripcion,
bh_datoventa.TX_datoventa_precio AS precio, bh_datoventa.TX_datoventa_impuesto
FROM (((bh_facturaf
INNER JOIN bh_facturaventa ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
WHERE bh_facturaf.AI_facturaf_id = '$facturaf_id'";
$qry_datoventa=$link->query($txt_datoventa)or die($link->error);
$rs_datoventa=$qry_datoventa->fetch_array();

$file = fopen($recipiente."FACMV".substr($rs_facturaf['TX_facturaf_numero'],-7).".txt", "w");

if ($qry_datoventa->num_rows > 3) {
  do{
  fwrite($file, "FACTI".substr($rs_facturaf['TX_facturaf_numero'],-7).chr(9).substr($rs_datoventa['TX_producto_codigo'],-6).chr(9).substr($r_function->replace_special_character($rs_datoventa['TX_datoventa_descripcion']),0,35).chr(9).$rs_datoventa['TX_producto_medida'].chr(9).$rs_datoventa['TX_datoventa_cantidad'].chr(9).$rs_datoventa['precio'].chr(9).$rs_datoventa['TX_datoventa_impuesto'].chr(9). PHP_EOL);
  }while($rs_datoventa=$qry_datoventa->fetch_array());
}else{
  do{
  fwrite($file, "FACTI".substr($rs_facturaf['TX_facturaf_numero'],-7).chr(9).substr($rs_datoventa['TX_producto_codigo'],-6).chr(9).substr($r_function->replace_special_character($rs_datoventa['TX_datoventa_descripcion']),0,65).chr(9).$rs_datoventa['TX_producto_medida'].chr(9).$rs_datoventa['TX_datoventa_cantidad'].chr(9).$rs_datoventa['precio'].chr(9).$rs_datoventa['TX_datoventa_impuesto'].chr(9). PHP_EOL);
  }while($rs_datoventa=$qry_datoventa->fetch_array());
}

fclose($file);

 ?>
 <div id="container_background" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="height:500px;">
 </div>
 <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	 &nbsp;
 </div>
 <div id="container_cambio" class="col-xs-6 col-sm-6 col-md-6 col-lg-6 bg-primary">
	 <div id="div_cambio" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		 <p>Su Cambio: </p>
		 <span id="span_cambio">B/ <?php echo number_format($cambio,2); ?></span>
	 </div>
 </div>

</form>
</div>

<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
        <div id="container_btnadminicon" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
        </div>
        <div id="container_txtcopyright" class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
    &copy; Derechos Reservados a: Trilli, S.A. 2017
        </div>
        <div id="container_btnstart" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                    		<i id="btn_start" class="fa fa-home" title="Ir al Inicio"></i>
        </div>
        <div id="container_btnexit" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <button type="button" class="btn btn-danger" id="btn_exit">Salir</button></div>
        </div>
	</div>
</div>
</div>
<?php  unset($_SESSION["facturaf_id"]);  ?>
</body>
</html>
