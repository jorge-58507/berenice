<?php
require 'bh_conexion.php';
$link=conexion();

$proveedor_id=$_GET['a'];

$qry_proveedor=$link->query("SELECT AI_proveedor_id, TX_proveedor_nombre, TX_proveedor_cif, TX_proveedor_dv, TX_proveedor_tipo, TX_proveedor_telefono, TX_proveedor_direccion FROM bh_proveedor WHERE AI_proveedor_id = '$proveedor_id'")or die($link->error);
$rs_proveedor=$qry_proveedor->fetch_array(MYSQLI_ASSOC);

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
		if($("#txt_providername").val() === ''){ return false; }
		if ($("#txt_cif").val() === '') { $("#txt_cif").val('0000-0-000000');	}
		if ($("#txt_dv").val() === '') { $("#txt_dv").val('00');	}
		if ($("#txt_telephone").val() === '') { $("#txt_telephone").val('0000-0000');	 }
		if ($("#txt_direction").val() === '') { $("#txt_direction").val('PTY');	}
		 upd_provider(<?php echo $proveedor_id; ?>);
	})
	$('#btn_cancel').click(function(){
		self.close();
	})

	$('#txt_telephone').validCampoFranz('0123456789 -');
	$('#txt_cif').validCampoFranz('abcdefghijklmnopqrstuvwxyz0123456789 -');
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
	<form method="post" name="form_updprovider">
	<div id="container_name" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
		<label for="txt_providername">Nombre:</label>
    <input type="text" name="txt_providername" id="txt_providername" class="form-control" onkeyup="chk_providername(this)" value="<?php echo $rs_proveedor['TX_proveedor_nombre']; ?>" />
	</div>
	<div id="container_cif" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
		<label for="txt_cif">RUC:</label>
    <input type="text" name="txt_cif" id="txt_cif" class="form-control" onkeyup="chk_cif(this)" value="<?php echo $rs_proveedor['TX_proveedor_cif']; ?>"/>
	</div>
	<div id="container_dv" class="col-xs-3 col-sm-3 col-md-3 col-lg-1">
		<label for="txt_dv">DV:</label>
    <input type="text" name="txt_dv" id="txt_dv" class="form-control" onkeyup="chk_dv(this)" value="<?php echo $rs_proveedor['TX_proveedor_dv']; ?>" />
	</div>
	<div id="container_telephone" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
	    <label for="txt_telephone">Tel&eacute;fono:</label>
	    <input type="text" name="txt_telephone" id="txt_telephone" class="form-control" onkeyup="chk_telephone(this)" value="<?php echo $rs_proveedor['TX_proveedor_telefono']; ?>" />
	</div>
	<div id="container_type" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
		<label for="sel_type">Tipo:</label>
	  <select class="form-control" id="sel_type">
			<?php if ($rs_proveedor['TX_proveedor_tipo'] === 'DISTRIBUIDOR'): ?>
		    <option value="DISTRIBUIDOR" selected>DISTRIBUIDOR</option>
		    <option value="OTRO">OTRO</option>
			<?php endif; ?>
			<?php if ($rs_proveedor['TX_proveedor_tipo'] === 'OTRO'): ?>
		    <option value="DISTRIBUIDOR">DISTRIBUIDOR</option>
		    <option value="OTRO" selected>OTRO</option>
			<?php endif; ?>
	  </select>
	</div>
	<div id="container_direction" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<label for="txt_direction">Direcci&oacute;n:</label>
	   <textarea name="txt_direction" id="txt_direction" class="form-control" onkeyup="chk_direction(this)"><?php echo $rs_proveedor['TX_proveedor_direccion']; ?></textarea>
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
&copy; Derechos Reservados a: Jorge Salda&nacute;a <?php echo date('Y'); ?>
	</div>
</div>
</div>

</body>
</html>
