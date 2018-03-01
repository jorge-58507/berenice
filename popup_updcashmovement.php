<?php
require 'bh_conexion.php';
$link=conexion();
$efectivo_id = $_GET['a'];
$qry_cashmovement = $link->query("SELECT bh_efectivo.TX_efectivo_fecha, bh_efectivo.TX_efectivo_tipo, bh_efectivo.TX_efectivo_motivo, bh_efectivo.TX_efectivo_monto, bh_user.TX_user_seudonimo, bh_efectivo.AI_efectivo_id
FROM (bh_efectivo
INNER JOIN bh_user ON bh_efectivo.efectivo_AI_user_id = bh_user.AI_user_id)
WHERE AI_efectivo_id = '$efectivo_id'")or die($link->error);
$rs_cashmovement=$qry_cashmovement->fetch_array(MYSQLI_ASSOC);
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

<script type="text/javascript">

$(document).ready(function() {
	$('#btn_cancel').click(function(){
		self.close();
	});

	$('#btn_save').click(function(){
		$.ajax({ data: {"a" : $("#txt_motivo").val(), "b" : $("#sel_type").val(), "c" : '<?php echo $efectivo_id; ?>' }, type: "GET", dataType: "text", url: "attached/get/upd_cashmovement.php",	})
		.done(function( data, textStatus, jqXHR ) {	console.log("GOOD " + textStatus );
			if(data){ window.opener.location.reload(); setTimeout(function(){	self.close(); },200) }
		})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
	});

	$('#txt_motivo').on("blur", function(){
		this.value = this.value.toUpperCase();
	})

	$('#txt_motivo').validCampoFranz('0123456789 abcdefghijklmnopqrstuvwxyz-/*+,.()');


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
<form id="form_updefectivo" class="" action="" method="post">

	<div id="container_cashmovement" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_txtmotivo" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label for="txt_motivo">Motivo</label>
			<input type="text" id="txt_motivo" class="form-control" value="<?php echo $rs_cashmovement['TX_efectivo_motivo']; ?>"	/>
		</div>
		<div id="container_seltype" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label for="sel_type">Tipo de Movimiento</label>
			<select id="sel_type" class="form-control" name="sel_type">
<?php 	if ($rs_cashmovement['TX_efectivo_tipo'] === 'SALIDA'): ?>
				<option value="SALIDA" selected="selected">Salida</option>
				<option value="ENTRADA">Entrada</option>
<?php 	else: ?>
				<option value="SALIDA">Salida</option>
				<option value="ENTRADA" selected="selected">Entrada</option>
<?php 	endif; ?>
			</select>
		</div>
		<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<button type="button" id="btn_save" class="btn btn-success">Guardar</button>
			&nbsp;&nbsp;
			<button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
		</div>
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
