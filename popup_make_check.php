<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_admin.php';

$raw_payment_i=json_decode($_GET['a'], true);
$i=$_GET['b'];
$cpp_id=$_GET['c'];

$method=$raw_payment_i['metodo'];
$number=$raw_payment_i['numero'];
$amount=$raw_payment_i['monto'];

$qry_cpp = $link->query("SELECT bh_proveedor.AI_proveedor_id, bh_proveedor.TX_proveedor_nombre FROM (bh_cpp INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_cpp.cpp_AI_proveedor_id) WHERE AI_cpp_id = '$cpp_id'")or die($link->error);
$rs_cpp = $qry_cpp->fetch_array();

$qry_pedido = $link->query("SELECT bh_pedido.TX_pedido_numero FROM (bh_cpp INNER JOIN bh_pedido ON bh_pedido.AI_pedido_id = bh_cpp.cpp_AI_pedido_id) WHERE AI_cpp_id = '$cpp_id'")or die($link->error);
$rs_pedido = $qry_pedido->fetch_array();

$qry_facturacompra = $link->query("SELECT bh_facturacompra.TX_facturacompra_numero FROM (bh_cpp INNER JOIN bh_facturacompra ON bh_facturacompra.AI_facturacompra_id = bh_cpp.cpp_AI_facturacompra_id) WHERE AI_cpp_id = '$cpp_id'")or die($link->error);
$rs_facturacompra =	$qry_facturacompra->fetch_array();
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
<link href="attached/css/popup_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/admin_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript">

$(document).ready(function() {

$('#txt_motivo').validCampoFranz('.0123456789- abcdefghijklmnopqrstuvwxyz');

$("#btn_print").click(function(){
	window.opener.raw_payment[<?php echo $i; ?>]['metodo'] = '<?php echo $method; ?>';
	window.opener.raw_payment[<?php echo $i; ?>]['numero'] = $("#txt_number").val();
	window.opener.raw_payment[<?php echo $i; ?>]['monto'] = $("#txt_monto").val();
	window.opener.print_payment_cpp(window.opener.raw_payment);

	$.ajax({	data: {"a" : '<?php echo $cpp_id; ?>', "b" : $("#txt_number").val(), "c" : $("#txt_monto").val(), "d" : $("#txt_montoletras").val(), "e" : $("#txt_observation").val(), "f" : <?php echo $rs_cpp['AI_proveedor_id']; ?> },	type: "GET",	dataType: "text",	url: "attached/get/plus_check.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 if (data) {
		 	console.log('GOOD '+textStatus);
			setTimeout(function(){ print_html('print_check.php?a='+data); },100);
			setTimeout(function(){ self.close(); },250);
		 }
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
});
$("#btn_cancel").click(function(){
	self.close()
});

$("#txt_monto").on("blur", function(){
	this.value = val_intw2dec(this.value);
})

$("#txt_montoletras").val(nn($("#txt_monto").val()));
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
	<div id="container_number" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
  	<label class="label label_blue_sky" for="span_cpp">CxP N&deg;: </label>
		<span class="form-control bg-disabled" id="span_cpp"><?php echo substr('000000000'.$cpp_id,-9); ?></span>
  </div>
	<div id="container_provider" class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
  	<label class="label label_blue_sky" for="span_provider">Beneficiario: </label>
		<span class="form-control bg-disabled" id="span_provider"><?php echo $rs_cpp['TX_proveedor_nombre']; ?></span>
  </div>
	<div id="container_number" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
  	<label class="label label_blue_sky" for="txt_number">Numero: </label>
		<input type="text" id="txt_number" class="form-control" value="<?php echo $number; ?>" />
  </div>
	<div id="container_monto" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
  	<label class="label label_blue_sky" for="txt_monto">Monto:</label>
		<input type="text" id="txt_monto" class="form-control" value="<?php echo $amount; ?>" />
  </div>
	<div id="container_montoletras" class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
  	<label class="label label_blue_sky" for="txt_montoletras">Monto en Letras:</label>
		<input type="text" id="txt_montoletras" class="form-control" value="" />
  </div>
	<div id="container_montoletras" class="col-xs-2 col-sm-2 col-md-2 col-lg-2 side-btn-md-label">
		<button type="button" class="btn btn-primary" onclick="$('#txt_montoletras').val(nn($('#txt_monto').val()))"><i class="fa fa-refresh"></i></button>
  </div>
	<div id="container_observation" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  	<label class="label label_blue_sky" for="txt_observation">Observaci&oacute;n: </label>
		<input type="text" id="txt_observation" class="form-control" value="<?php if(!empty($rs_facturacompra[0])){ echo "Factura: ".$rs_facturacompra[0]; }; if(!empty($rs_pedido[0])){ echo "Orden de Compra: ".$rs_pedido[0]; }; ?>" />
  </div>
  <div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  	<button type="button" id="btn_print" class="btn btn-info"><span class="glyphicon glyphicon-print"></span> Imprimir</button>
    <button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
  </div>
</div>

<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
&copy; Derechos Reservados a: Jorge Salda&nacute;a <?php echo date('Y'); ?>
	</div>
</div>
</div>

</body>
</html>
