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

	<?php include 'attached/php/req_required.php'; ?>
	<link href="attached/css/popup_css.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="attached/js/sell_funct.js"></script>
	<!-- <script type="text/javascript" src="attached/js/addprovider_funct.js"></script> -->
	<script type="text/javascript">

		$(document).ready(function() {
			$("#txt_clientname").focus();
					
			$('#btn_acept').click(function(){
				$(this).attr("disabled", true);
				setTimeout(() => { $(this).attr("disabled", false); }, 3000);
				cls_client.save_client();
			})

			$('#btn_cancel').click(function(){
				self.close();
			})

			$('#txt_telephone').validCampoFranz('0123456789 -');
			$('#txt_cif, #txt_dv').validCampoFranz('0123456789-abcdefghijklmnopqrstuvwxyz');
			$('#txt_client_email').validCampoFranz('0123456789-_.,º?¿¡!@abcdefghijklmnopqrstuvwxyz');
		});
	</script>

</head>

<body>
<div id="snackbar"></div>
<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="logo_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-2" >
			<div id="logo" ></div>
		</div>
	</div>

	<div id="content-sidebar_popup" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<form method="post" name="form_addprovider">
			<div class="row px_15 pt_7">
				<div id="container_name" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
					<label class="label label_blue_sky" for="txt_clientname">Nombre:</label>
					<input type="text" name="txt_clientname" id="txt_clientname" class="form-control" onkeyup="limitText(this,80,0)" value="<?php echo $name; ?>" onblur="this.value = this.value.toUpperCase()" />
				</div>
			</div>
			<div class="row px_15 pt_7">
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
					<label for="sel_client" class="label label_blue_sky">Tipo de Cliente</label>
					<select name="sel_client" id="sel_client" class="form-control" onchange="cls_client.chk_client_taxpayer(this)">
						<option value="" disabled selected>Seleccione</option>
						<option value="0">Consumidor Final (No requiere correo)</option>
						<option value="1">Persona Natural</option>
						<option value="2">Empresa</option>
						<option value="3">Gobierno</option>
						<option value="4">Empresa Extranjera</option>
						<option value="5">Persona Extranjera</option>
					</select>


					<!-- 
					natural cobnsumidor final			1	2		0	
					natural contribuyente					1	1		1
					natural extanjero							1	4		5

					empresa contribuyente					2	1		2
					empresa Gobierno							2	3		3
					empresa extranjero						2	4		4

					  -->

					<input type="hidden" name="sel_client_taxpayer" id="sel_client_taxpayer" value="">
					<input type="hidden" name="sel_client_type" id="sel_client_type" value="">
				</div>
			</div>
			<div class="row px_15 pt_7">
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
					<label class="label label_blue_sky" for="txt_cif">RUC:</label>
					<input type="text" name="txt_cif" id="txt_cif" class="form-control" onkeyup="limitText(this, 22, 1)" />
				</div>
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
					<label class="label label_blue_sky" for="txt_dv">DV:</label>
					<input type="text" name="txt_dv" id="txt_dv" class="form-control" onkeyup="limitText(this, 2, 1)" />
				</div>
			</div>
			<div class="row px_15 pt_7">
				<div class="col-xs-8 col-sm-8 col-md-8 col-lg-3">
					<label class="label label_blue_sky" for="txt_client_email">Correo E:</label>
					<input type="text" name="txt_client_email" id="txt_client_email" class="form-control" onkeyup="limitText(this, 60, 1)" />
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
					<label class="label label_blue_sky" for="txt_telephone">Tel&eacute;fono:</label>
					<input type="text" name="txt_telephone" id="txt_telephone" class="form-control" onkeyup="limitText(this,30,1)" />
				</div>
			</div>
			<div class="row px_15 pt_7">
				<div id="container_direction" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<label class="label label_blue_sky" for="txt_direction">Direcci&oacute;n:</label>
					<textarea name="txt_direction" id="txt_direction" class="form-control" onkeyup="limitText(this,240,1)"></textarea>
				</div>
			</div>
			<div class="row pt_21 pb_7 ">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text_center">
					<button type="button" id="btn_acept" class="btn btn-success">Aceptar</button>
					&nbsp;
					<button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
				</div>
			</div>
		</form>
	</div>

	<div id="footer">
		<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
			&copy; Derechos Reservados a: Jorge Salda&nacute;a <?php echo date('Y'); ?>
		</div>
	</div>

</div>
<script type="text/javascript">
	// $(document).ready(function() {
		const cls_client = new client();
		$('#sel_client').val('');
		$('#sel_client_taxpayer').val('');
		$('#sel_client_type').val('');
	// });
</script>
</body>
</html>
