<?php
require 'bh_conexion.php';
$link=conexion();

$name=$r_function->url_replace_special_character($_GET['a']);
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
	$("#txt_clientname").focus();

$('#btn_acept').click(function(){
	$(this).attr("disabled", true);
	$("#txt_clientname").val($("#txt_clientname").val().toUpperCase());
	if ($("#txt_clientname").val() === ""){ $("#txt_clientname").focus(); return false; }
	if($("#txt_cif").val() != "" && $("#txt_cif").val().length < '7'){
		$("#txt_cif").focus();
		return false;
	}
	 plus_newclient();
})
$('#btn_cancel').click(function(){
	self.close();
})

$('#txt_telephone').validCampoFranz('0123456789 -');
$('#txt_cif').validCampoFranz('0123456789 -abcdefghijklmnopqrstuvwxyz:');


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
<div id="container_name" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
	<label class="label label_blue_sky" for="txt_providername">Nombre:</label>
  <input type="text" name="txt_clientname" id="txt_clientname" class="form-control" onkeyup="chk_clientname(this)" value="<?php echo $name; ?>" />
</div>
<div id="container_cif" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
	<label class="label label_blue_sky" for="txt_cif">RUC:</label>
    <input type="text" name="txt_cif" id="txt_cif" class="form-control" onkeyup="chk_cif(this)" />
</div>
<div id="container_telephone" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
    <label class="label label_blue_sky" for="txt_telephone">Tel&eacute;fono:</label>
    <input type="text" name="txt_telephone" id="txt_telephone" class="form-control" onkeyup="chk_telephone(this)" />
</div>
<div id="container_direction" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<label class="label label_blue_sky" for="txt_direction">Direcci&oacute;n:</label>
    <textarea name="txt_direction" id="txt_direction" class="form-control" onkeyup="chk_direction(this)"></textarea>
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
