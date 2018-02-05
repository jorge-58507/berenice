<?php
require 'bh_conexion.php';
$link=conexion();

$proveedor_id = $_GET['a'];
$qry_proveedor = $link->query("SELECT TX_proveedor_nombre FROM bh_proveedor WHERE AI_proveedor_id = '$proveedor_id'")or die($link->error);
$rs_proveedor = $qry_proveedor->fetch_array();

$qry_pedido=$link->query("SELECT AI_pedido_id, TX_pedido_numero FROM bh_pedido WHERE pedido_AI_proveedor_id = '$proveedor_id'")or die($link->error);

$qry_datopedido=$link->prepare("SELECT TX_datopedido_cantidad, TX_datopedido_precio, datopedido_AI_producto_id FROM bh_datopedido WHERE datopedido_AI_pedido_id = ?");
$qry_producto=$link->prepare("SELECT AI_producto_id, TX_producto_exento FROM bh_producto WHERE AI_producto_id = ?")or die($link->error);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>

<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap-theme.css" rel="stylesheet" type="text/css" />
<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_layout.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_blocks.css" rel="stylesheet" type="text/css" />
<link href="attached/css/popup_css.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript" src="attached/js/addprovider_funct.js"></script>
<script type="text/javascript">

$(document).ready(function() {

$("#txt_monto").on("blur",function(){
	this.value = val_intw2dec(this.value);
})

$("#sel_pedido").on("change", function(){
	$("#txt_monto").val($("#sel_pedido option:selected").attr("alt"));
})

$('#btn_acept').click(function(){
	if ($("#txt_fecha").val() === "" || $("#txt_monto").val() === "") {
		return false;
	}
	$.ajax({	data: {"a" : <?php echo $proveedor_id; ?>, "b" : $("#txt_fecha").val(), "c" : $("#txt_monto").val() , "d" : $("#sel_pedido").val() },	type: "GET",	dataType: "text",	url: "attached/get/plus_cpp.php", })
	 .done(function( data, textStatus, jqXHR ) { console.log("GOOD"+textStatus);
	 	window.opener.$("#tbl_cpp tbody").html(data);
		setTimeout("self.close()",250);
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
})
$('#btn_cancel').click(function(){
	self.close();
})
$( function() {
	$("#txt_fecha").datepicker({
		changeMonth: true,
		changeYear: true
	});
});

$('#txt_telephone, #txt_cif').validCampoFranz('0123456789 -');
$('#txt_direction, #txt_providername').validCampoFranz('0123456789 .,- abcdefghijklmnopqrstuvwxyz');


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

<div id="container_proveedor" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<label for="">Proveedor:</label>
  <span class="form-control bg-disabled"><?php echo $rs_proveedor['TX_proveedor_nombre']; ?></span>
</div>
<div id="container_pedido" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<label for="">Orden de Compra:</label>
	<select class="form-control" id="sel_pedido">
		<option value="0" alt="0">Seleccione</option>
<?php while ($rs_pedido = $qry_pedido->fetch_array(MYSQLI_ASSOC)) {
	$qry_datopedido->bind_param("i",$rs_pedido['AI_pedido_id']);
	$qry_datopedido->execute();
	$result = $qry_datopedido->get_result();
	$total_pedido = 0;
	while($rs_datopedido = $result->fetch_array(MYSQLI_ASSOC)){
		$qry_producto->bind_param("i", $rs_datopedido['datopedido_AI_producto_id']); $qry_producto->execute();
		$result_producto = $qry_producto->get_result(); $rs_producto = $result_producto->fetch_array();
		$total_datopedido=$rs_datopedido['TX_datopedido_precio']*$rs_datopedido['TX_datopedido_cantidad'];
		$impuesto_datopedido=($rs_producto['TX_producto_exento']*$total_datopedido)/100;
		$total_pedido+=$total_datopedido+$impuesto_datopedido;
	};
?>
		<option value="<?php echo $rs_pedido['AI_pedido_id'] ?>" alt="<?php echo $total_pedido; ?>"><?php echo "(".$rs_pedido['TX_pedido_numero'].") - B/ ".$total_pedido; ?></option>
<?php } ?>
	</select>
</div>
<div id="container_fecha" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
	<label for="txt_fecha">Fecha:</label>
  <input type="text" name="txt_fecha" id="txt_fecha" class="form-control" readonly="readonly" value="<?php echo date('d-m-Y') ?>"/>
</div>
<div id="container_monto" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
	<label for="txt_monto">Monto:</label>
  <input type="text" name="txt_monto" id="txt_monto" class="form-control" onkeyup="chk_providername(this)" />
</div>

<div id="container_button" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<button type="button" id="btn_acept" class="btn btn-success">Aceptar</button>
&nbsp;
<button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
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
