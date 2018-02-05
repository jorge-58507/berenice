<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_admin.php';

$proveedor_id = $_GET['a'];

$qry_proveedor = $link->query("SELECT AI_proveedor_id, TX_proveedor_nombre, TX_proveedor_cif, TX_proveedor_dv, TX_proveedor_direccion, TX_proveedor_telefono FROM bh_proveedor WHERE AI_proveedor_id = '$proveedor_id'")or die($link->error);
$rs_proveedor = $qry_proveedor->fetch_array();

$qry_bank = $link->query("SELECT AI_banco_id, TX_banco_value FROM bh_banco ORDER BY TX_banco_value ASC") or die($link->error);

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

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/product2sell_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript">

$(document).ready(function() {

$("#btn_acept").on("click", function(){
	$.ajax({	data: {"a" : $("#txt_number").val(), "b" : $("#sel_bank").val(), "c" : '<?php echo $_GET['a']; ?>', "d" : $("#txt_name").val() },	type: "GET",	dataType: "text",	url: "attached/get/plus_bank_accountnumber.php", })
	 .done(function( data, textStatus, jqXHR ) { console.log("GOOD"+textStatus);
		 if (data) {
			 	window.opener.$("#tbl_bankaccount tbody").html(data);
 		 		setTimeout(function(){ self.close(); },500);
		 }
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
})
$('#btn_cancel').click(function(){
	self.close();
})

$('#txt_cif, #txt_telephone, #txt_dv').validCampoFranz('-0123456789');
$('#txt_proveedor, #txt_direction').validCampoFranz('.0123456789 abcdefghijklmnopqrstuvwxyz-,');

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
<form action="" method="post" name="form_proveedor" id="form_proveedor">

<div id="container_proveedor" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
	<label for="txt_proveedor">Nombre: </label>
  <span class="form-control bg-disabled"><?php echo $rs_proveedor['TX_proveedor_nombre'] ?></span>
</div>
<div id="container_cif" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	<label for="txt_cif">RUC:</label>
  <span class="form-control bg-disabled"><?php echo $rs_proveedor['TX_proveedor_cif']; ?></span>
</div>
<div id="container_dv" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	<label for="txt_dv">DV:</label>
  <span class="form-control bg-disabled"><?php echo $rs_proveedor['TX_proveedor_dv'] ?></span>
</div>
<div id="container_telephone" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	<label for="txt_telephone">Tel&eacute;fono:</label>
  <span class="form-control bg-disabled"><?php echo $rs_proveedor['TX_proveedor_telefono'] ?></span>
</div>
<div id="container_newbankaccount" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<label for="sel_bank">Banco</label>
		<select id="sel_bank" class="form-control">
<?php while ($rs_bank = $qry_bank->fetch_array()) { ?>
			<option value="<?php echo $rs_bank['AI_banco_id']; ?>"><?php echo $rs_bank['TX_banco_value']; ?></option>
<?php } ?>
		</select>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<label for="txt_name">Nombre</label>
		<input type="text" id="txt_name" class="form-control" placeholder="Nombre de Cuenta" value="<?php echo $rs_proveedor['TX_proveedor_nombre']; ?>" autofocus>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<label for="txt_number">Nº. de Cuenta</label>
		<input type="text" id="txt_number" class="form-control" placeholder="Numero de Cuenta">
	</div>
</div>

<div id="container_button" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<button type="button" id="btn_acept" class="btn btn-success">Agregar</button>
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
