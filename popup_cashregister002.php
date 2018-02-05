<?php
require 'bh_conexion.php';
$link=conexion();

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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>

<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap-theme.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_layout.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_blocks.css" rel="stylesheet" type="text/css" />
<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="attached/css/popup_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript" src="attached/js/addprovider_funct.js"></script>
<script type="text/javascript">

$(document).ready(function() {

$('#btn_cancel').click(function(){
	self.close();
})
$("#txt_filterarqueo").on("keyup",function(){
  $.ajax({	data: {"a" : this.value, "b" : $("#txt_filterfecha").val() },	type: "GET",	dataType: "text",	url: "attached/get/filter_arqueo.php", })
   .done(function( data, textStatus, jqXHR ) {	$("#tbl_cashregister tbody").html( data );	})
   .fail(function( jqXHR, textStatus, errorThrown ) {		});
})
$( function() {
	$("#txt_filterfecha").datepicker({
		changeMonth: true,
		changeYear: true
	});
});
$("#btn_pluscashregister").on("click",function(){
  ans = confirm("Se procedera a cerrar este usuario, ¿Desea Continuar?");
  if(!ans){ return false; };
  plus_cashregister();
  setTimeout(function(){ print_html('print_cashregister.php') },150);
  setTimeout(function(){ window.opener.location = 'index.php'; },500);
})


});


</script>

</head>

<body>

<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-2" >
		<div id="logo" ></div>
	</div>

</div>

<div id="content-sidebar_popup" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<form method="post" name="form_addprovider">
<div id="container_btnpluscashregister" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <button type="button" id="btn_pluscashregister" class="btn btn-danger">Arquear Caja</button>
</div>
<div id="container_txtfilterarqueo" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	<label for="txt_filterarqueo">Buscar:</label>
  <input type="text" name="txt_filterarqueo" id="txt_filterarqueo" class="form-control" />
</div>
<div id="container_txtfilterarqueo" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	<label for="txt_filterfecha">Fecha:</label>
  <input type="text" name="txt_filterfecha" id="txt_filterfecha" class="form-control" value="<?php echo date('d-m-Y'); ?>" readonly="readonly" />
</div>
<div id="container_tblcashregister" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <table id="tbl_cashregister" class="table table-bordered table-condensed table-hover">
  <thead>
  <tr class="bg-primary">
    <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Fecha</th>
    <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Hora</th>
    <th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Operador</th>
    <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
  </tr>
  </thead>
  <tbody>
  <tr>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  </tbody>
  <tfoot class="bg-primary">
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  </tfoot>
  </table>
</div>
<div id="container_button" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
</div>
<div id="container_preview" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

<?php
$host_ip=ObtenerIP();
$host_name=gethostbyaddr($host_ip);
$qry_impresora=$link->query("SELECT AI_impresora_id FROM bh_impresora WHERE TX_impresora_cliente = '$host_name'");
$rs_impresoraid=$qry_impresora->fetch_array();

$qry_metododepago=$link->query("SELECT AI_metododepago_id, TX_metododepago_value FROM bh_metododepago")or die($link->error);
$raw_pago=array();	$raw_debito=array();
while ($rs_metododepago = $qry_metododepago->fetch_array()) {
	$raw_pago[$rs_metododepago['AI_metododepago_id']] = 0;
	$raw_debito[$rs_metododepago['AI_metododepago_id']] = 0;
}
$txt_facturaf = "SELECT bh_facturaf.AI_facturaf_id FROM bh_facturaf WHERE bh_facturaf.facturaf_AI_impresora_id = '{$rs_impresoraid['0']}' AND bh_facturaf.facturaf_AI_arqueo_id = '0' AND bh_facturaf.facturaf_AI_user_id = '{$_COOKIE['coo_iuser']}'";
$qry_facturaf = $link->query($txt_facturaf)or die($link->error);
$raw_ffid = array();
$i=0; $ttl_descuento=0;
while($rs_facturaf=$qry_facturaf->fetch_array()){
	$raw_ffid[$i] = $rs_facturaf['AI_facturaf_id'];
	$ttl_descuento += $rs_facturaf['descuento'];
	$i++;
}
$cantidad_ff = $i;
if ($cantidad_ff > 0) {
	$txt_datopago = "SELECT bh_datopago.TX_datopago_monto, bh_datopago.datopago_AI_metododepago_id
	FROM bh_datopago
	WHERE ";
	foreach ($raw_ffid as $key => $value) {
		($value === end($raw_ffid)) ?	$txt_datopago .= "datopago_AI_facturaf_id = '$value'"	: $txt_datopago .= "datopago_AI_facturaf_id = '$value' OR ";
	}
	$qry_datopago = $link->query($txt_datopago)or die($link->error);
}

echo $txt_datopago;
return false;
$txt_facturaf="SELECT bh_facturaf.AI_facturaf_id, bh_datopago.TX_datopago_monto, bh_datopago.datopago_AI_metododepago_id, bh_facturaf.TX_facturaf_descuento as descuento
FROM (bh_facturaf
INNER JOIN bh_datopago ON bh_facturaf.AI_facturaf_id = bh_datopago.datopago_AI_facturaf_id)
WHERE bh_facturaf.facturaf_AI_impresora_id = '{$rs_impresoraid['0']}'
AND bh_facturaf.facturaf_AI_arqueo_id = '0'
AND bh_facturaf.facturaf_AI_user_id = '{$_COOKIE['coo_iuser']}'";
$qry_facturaf=$link->query($txt_facturaf)or die($link->error);
if($qry_facturaf->num_rows < 1){
	//return false;
}
$ttl_descuento=0;
$i=0;
while($rs_facturaf=$qry_facturaf->fetch_array()){
	$raw_pago[$rs_facturaf['datopago_AI_metododepago_id']] += $rs_facturaf['TX_datopago_monto'];
	$ttl_descuento += $rs_facturaf['descuento'];
	$raw_ffid[$i] = $rs_facturaf['AI_facturaf_id'];
	$i++;
}
 // echo "<br /> PAGOS: ".json_encode($raw_pago);

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
 // echo "<br >DEBITO: ".json_encode($raw_debito);
 //
 // echo "<br>DESCUENTO: ".$ttl_descuento;

 $txt_devolucion="SELECT bh_notadecredito.AI_notadecredito_id,  bh_notadecredito.TX_notadecredito_destino, bh_notadecredito.TX_notadecredito_monto, bh_notadecredito.TX_notadecredito_impuesto
 FROM bh_notadecredito
 WHERE bh_notadecredito.notadecredito_AI_impresora_id = '{$rs_impresoraid['0']}'
 AND bh_notadecredito.notadecredito_AI_arqueo_id = '0'";
 $qry_devolucion=$link->query($txt_devolucion);
 $nc_base=0;
 $nc_impuesto=0;
 $devolucion=0;
 $raw_nc=array();
 $ite=0;
 while($rs_devolucion = $qry_devolucion->fetch_array()){
 	$raw_nc[$ite]=$rs_devolucion['AI_notadecredito_id'];
 	$ite++;
 	if($rs_devolucion['TX_notadecredito_destino'] == 'EFECTIVO'){
 		$devolucion+=($rs_devolucion['TX_notadecredito_monto']+$rs_devolucion['TX_notadecredito_impuesto']);
 	}else {
 		$nc_base += $rs_devolucion['TX_notadecredito_monto'];
 		$nc_impuesto+=$rs_devolucion['TX_notadecredito_impuesto'];
 	}
 }
 $venta_neta=0;
 foreach($raw_pago as $pago){
 	$venta_neta += $pago;
 }
 $venta_neta-=$devolucion;
 $venta_bruta=$venta_neta+$ttl_descuento;
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
 		$base_ci += $precio_descuento;
 		$ttl_impuesto += $impuesto;
 	}else{
 		$base_ni += $precio_descuento;
 	}
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
 //echo $ttl_entrada;
 // echo $json_datopago = json_encode($raw_pago);
 // echo $json_datodebito = json_encode($raw_debito);

?>
<table id="tbl_preview" class="table table-bordered table-striped table-condensed">
<thead class="bg_green">
	<tr>
		<th colspan="3">Movimientos Parciales</th>
	</tr>
</thead>
<tbody>
	<tr> <td colspan="3"> <strong>Caja Menuda</strong> (B/ <?php echo number_format($ttl_entrada-$ttl_salida-$devolucion,2); ?>) </td> </tr>
	<tr>
		<td><?php echo "Entradas: ".number_format($ttl_entrada,2); ?> </td>
		<td><?php echo "Salidas: ".number_format($ttl_salida,2); ?></td>
		<td><?php echo "Devoluciones: ".number_format($devolucion,2); ?></td>
 </tr>
	<tr> <td colspan="3"> <strong>Efectivo:</strong> (B/ <?php echo number_format($raw_pago[1]+$raw_debito[1],2); ?>) </td> </tr>
	<tr>
		<td><?php echo "Ventas: ".number_format($raw_pago[1],2); ?></td>
		<td><?php echo "Cobros: ".number_format($raw_debito[1],2); ?></td>
	</tr>
	<tr> <td colspan="3"> <strong>Cheques</strong> (B/ <?php echo number_format($raw_pago[2]+$raw_debito[2],2); ?>) </td> </tr>
	<tr>
		<td><?php echo "Ventas: ".number_format($raw_pago[2],2); ?></td>
		<td><?php echo "Cobros: ".number_format($raw_debito[2],2); ?></td>
	</tr>
	<tr> <td colspan="3"><strong>Tarjeta de Cr&eacute;dito</strong> (B/ <?php echo number_format($raw_pago[3]+$raw_debito[3],2); ?>) </td> </tr>
	<tr>
		<td><?php echo "Ventas: ".number_format($raw_pago[3],2); ?></td>
		<td><?php echo "Cobros: ".number_format($raw_debito[3],2); ?></td>
	</tr>
	<tr> <td colspan="3"><strong>Tarjeta Clave</strong> (B/ <?php echo number_format($raw_pago[4]+$raw_debito[4],2); ?>) </td> </tr>
	<tr>
		<td><?php echo "Ventas: ".number_format($raw_pago[4],2); ?></td>
		<td><?php echo "Cobros: ".number_format($raw_debito[4],2); ?></td>
	</tr>
	<tr> <td colspan="3"><strong>Cr&eacute;dito</strong> (B/ <?php echo number_format($raw_pago[5]+$raw_debito[5],2); ?>) </td></tr>
	<tr>
		<td><?php echo "Ventas: ".number_format($raw_pago[5],2); ?></td>
		<td><?php echo "Cobros: ".number_format($raw_debito[5],2); ?></td>
	</tr>
	<tr> <td colspan="3"><strong>Nota de Cr&eacute;dito</strong> (B/ <?php echo number_format($raw_pago[7]+$raw_debito[7],2); ?>) </td> </tr>
	<tr>
		<td><?php echo "Ventas: ".number_format($raw_pago[7],2); ?></td>
		<td><?php echo "Cobros: ".number_format($raw_debito[7],2); ?></td>
	</tr>
</tbody>
<tfoot class="bg_green">
<tr>
	<td><strong>Venta Bruta:</strong> <?php echo $venta_bruta; ?></td>
	<td><strong>Venta Neta:</strong> <?php echo $venta_neta; ?></td>
	<td><strong>Venta Real:</strong> <?php echo $venta_neta+$devolucion; ?></td>
</tr>
</tfoot>
</table>

</div>
</form>
</div>


<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
&copy; Derechos Reservados a: Trilli, S.A. 2017
	</div>
</div>
</div>

</body>
</html>
