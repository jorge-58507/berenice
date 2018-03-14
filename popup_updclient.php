<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_sale.php';

$client_id=$_GET['a'];

$qry_client = $link->query("SELECT AI_cliente_id, TX_cliente_nombre, TX_cliente_cif, TX_cliente_telefono, TX_cliente_direccion FROM bh_cliente WHERE AI_cliente_id = '$client_id'")or die($link->error);
$rs_client = $qry_client->fetch_array();

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
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript" src="attached/js/addprovider_funct.js"></script>
<script type="text/javascript">

$(document).ready(function() {
$('#btn_acept').click(function(){
	if ($("#txt_clientname").val() === ""){
		$("#txt_clientname").focus();
		return false;
	}
	if($("#txt_cif").val() != "" && $("#txt_cif").val().length < '7'){
		$("#txt_cif").focus();
		return false;
	}
	$.ajax({	data: { "a" : $("#txt_clientname").val().replace("&","ampersand"), "b" : $("#txt_cif").val(), "c" : $("#txt_telephone").val(), "d" : $("#txt_direction").val(), "e" : '<?php echo $client_id; ?>' },	type: "GET",	dataType: "text",	url: "attached/get/upd_client_info.php", })
	 .done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
		 window.opener.$("#container_txtfilterclient").html(data);
		 self.close(); })
	 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
})
$('#btn_cancel').click(function(){
	self.close();
})


$('#txt_telephone').validCampoFranz('0123456789 -');
$('#txt_cif').validCampoFranz('0123456789 -abcdefghijklmnopqrstuvwxyz:');


});
function mod_clientname(){
	$.ajax({	data: "", type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
	.done(function( data, textStatus, jqXHR ) {
		if (data[0][0] === '') {
			return false;
		}else{
			$("#txt_clientname").removeAttr("readonly");
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}


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
<div id="container_name" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
	<label for="txt_providername">Nombre:</label>
  <input type="text" name="txt_clientname" id="txt_clientname" class="form-control" onkeyup="chk_clientname(this)" value="<?php echo $rs_client['TX_cliente_nombre']; ?>" readonly onclick="mod_clientname()"/>
</div>
<div id="container_cif" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
	<label for="txt_cif">RUC:</label>
    <input type="text" name="txt_cif" id="txt_cif" class="form-control" onkeyup="chk_cif(this)"  value="<?php echo $rs_client['TX_cliente_cif']; ?>" />
</div>
<div id="container_telephone" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
    <label for="txt_telephone">Tel&eacute;fono:</label>
    <input type="text" name="txt_telephone" id="txt_telephone" class="form-control" onkeyup="chk_telephone(this)"  value="<?php echo $rs_client['TX_cliente_telefono']; ?>" />
</div>
<div id="container_direction" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<label for="txt_direction">Direcci&oacute;n:</label>
    <textarea name="txt_direction" id="txt_direction" class="form-control" onkeyup="chk_direction(this)" ><?php echo $rs_client['TX_cliente_direccion']; ?></textarea>
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
