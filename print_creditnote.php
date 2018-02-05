<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login_admin.php';
$creditnote_id = $_SESSION['creditnote_id'];
//$creditnote_id = 19;
?>
<?php
mysql_query("UPDATE bh_notadecredito SET TX_notadecredito_status = 'IMPRESA' WHERE AI_notadecredito_id = '$creditnote_id'");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv='Refresh' content='3;url=start.php' />
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

print_html("print_client_nc.php?a=<?php echo $creditnote_id; ?>");

});

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
<?php
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
?>
<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<form action="" method="post" name="print_f_fiscal"  id="print_f_fiscal">

<?php
$ip   = ObtenerIP();
//echo  $ip;
$cliente = gethostbyaddr($ip);
//echo "<br>".$cliente;
?>
<?php

$qry_impresora=mysql_query("SELECT AI_impresora_id, TX_impresora_recipiente, TX_impresora_cliente, TX_impresora_serial FROM bh_impresora WHERE TX_impresora_cliente = '$cliente'");
$row_impresora=mysql_fetch_array($qry_impresora);

$recipiente = $row_impresora['TX_impresora_recipiente'];
if (!file_exists($recipiente)) {
    mkdir($recipiente, 0777, true);
}

$qry_notadecredito=mysql_query("SELECT bh_notadecredito.TX_notadecredito_numero, bh_notadecredito.TX_notadecredito_tipo, bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_direccion, bh_notadecredito.TX_notadecredito_monto, round(((bh_notadecredito.TX_notadecredito_impuesto*100)/bh_notadecredito.TX_notadecredito_monto)) AS alicuota, bh_notadecredito.TX_notadecredito_motivo, bh_notadecredito.TX_notadecredito_fecha, bh_notadecredito.TX_notadecredito_hora, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_ticket
FROM ((bh_notadecredito 
INNER JOIN bh_cliente ON bh_notadecredito.notadecredito_AI_cliente_id = bh_cliente.AI_cliente_id)
INNER JOIN bh_facturaf ON bh_notadecredito.notadecredito_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
WHERE AI_notadecredito_id = '$creditnote_id'", $link);
$row_notadecredito=mysql_fetch_array($qry_notadecredito);

$str=substr($row_notadecredito[0],-7);
//echo $str."<br>";

$predate=strtotime($row_notadecredito['TX_notadecredito_fecha']);
$fecha=date('d-m-Y',$predate);
$fecha=str_replace("-","/",$fecha);

$prehora=strtotime($row_notadecredito['TX_notadecredito_hora']);
$hora=date('H:i',$prehora);
?>
 <?php 
/* ##### ENCABEZADO  ##### */
$file = fopen($recipiente."NCTI".$str.".txt", "w");

fwrite($file, $row_notadecredito['TX_notadecredito_tipo'].chr(9)."NCTI".substr($row_notadecredito['TX_notadecredito_numero'],-7).chr(9).$row_notadecredito['TX_cliente_nombre'].chr(9).$row_notadecredito['TX_cliente_cif'].chr(9).$row_notadecredito['TX_cliente_direccion'].chr(9).$row_notadecredito['TX_notadecredito_monto'].chr(9).$row_notadecredito['alicuota'].chr(9).$row_notadecredito['TX_notadecredito_motivo'].chr(9).$fecha.chr(9).$hora.chr(9).$row_impresora['TX_impresora_serial'].chr(9).substr($row_notadecredito['TX_facturaf_numero'],-7).chr(9).substr($row_notadecredito['TX_facturaf_ticket'],-7).chr(9) );

fclose($file);
/* ##### ENCABEZADO  ##### */
?>
<?php
$qry_datodevolucion=mysql_query("SELECT bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_datodevolucion.TX_datodevolucion_cantidad, round(((bh_datoventa.TX_datoventa_precio-((bh_datoventa.TX_datoventa_descuento*bh_datoventa.TX_datoventa_precio)/100))*((100-bh_notadecredito.TX_notadecredito_retencion)/100)),2) AS precio, bh_datoventa.TX_datoventa_impuesto 
FROM (((bh_datodevolucion 
INNER JOIN bh_producto ON bh_datodevolucion.datodevolucion_AI_producto_id = bh_producto.AI_producto_id)
INNER JOIN bh_notadecredito ON bh_datodevolucion.datodevolucion_AI_notadecredito_id = bh_notadecredito.AI_notadecredito_id)
INNER JOIN bh_datoventa ON bh_datodevolucion.datodevolucion_AI_datoventa_id = bh_datoventa.AI_datoventa_id)
WHERE bh_notadecredito.AI_notadecredito_id = '$creditnote_id'" );
$row_datodevolucion=mysql_fetch_array($qry_datodevolucion);
?>
 <?php 
/* ##### MOVIMIENTO  ##### */
$file = fopen($recipiente."NCMV".$str.".txt", "w");
do{
fwrite($file, "NCTI".$str.chr(9).$row_datodevolucion['TX_producto_codigo'].chr(9).$row_datodevolucion['TX_producto_value'].chr(9).$row_datodevolucion['TX_producto_medida'].chr(9).$row_datodevolucion['TX_datodevolucion_cantidad'].chr(9).$row_datodevolucion['precio'].chr(9).$row_datodevolucion['TX_datoventa_impuesto'].chr(9). PHP_EOL );
}while($row_datodevolucion=mysql_fetch_array($qry_datodevolucion));
fclose($file);
/* ##### MOVIMIENTO  ##### */
?>
<?php
sleep(5);
?>
<?php
/*
$file = fopen("//COTIZADOR/print/RETURN/"."NCTI".$str.".txt", "r");
$content = fgets($file);
fclose($file);

$raw_content = explode("\t",$content);
mysql_query("UPDATE bh_notadecredito SET TX_notadecredito_serial = '$raw_content[6]', TX_notadecredito_ticket = '$raw_content[7]' WHERE AI_notadecredito_id = '$creditnote_id'");
*/
?>
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
<?php unset($_SESSION['creditnote_id']);  ?>
</body>
</html>
