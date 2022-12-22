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
<script type="text/javascript" src="attached/js/addprovider_funct.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	$("#txt_clientname").focus();

	$('#btn_acept').click(function(){
		$(this).attr("disabled", true);
		setTimeout(() => {		$(this).attr("disabled", false);	}, 3000);
		
		$("#txt_clientname").val($("#txt_clientname").val().toUpperCase());
		if (is_empty_var($("#txt_clientname").val()) === 0) {
			shot_snackbar('Debe ingresar un nombre','bg-warning');
			$("#txt_clientname").focus();	return false;
		}
		if ($('#sel_client_type').val() != "2") {

			if (is_empty_var($("#txt_cif").val()) === 0 || $("#txt_cif").val().length < 10) {
				shot_snackbar('Debe ingresar un RUC de 10 d&iacute;gitos v&aacute;lido.','bg-warning');
				$("#txt_cif").focus();	return false;
			}
			if (is_empty_var($("#txt_dv").val()) === 0) {
				shot_snackbar('Falta el D&iacute;gito Verificador.','bg-warning');
				$("#txt_dv").focus();	return false;
			}
			if (is_empty_var($("#txt_client_email").val()) === 0) {
				shot_snackbar('Falta el correo electrónico.','bg-warning');
				$("#txt_client_email").focus();	return false;
			}
			var patron=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;
			var valueForm = $("#txt_client_email").val();
			if(valueForm.search(patron)!=0){
				shot_snackbar('Verifique el correo electrónico.','bg-warning');
				$("#txt_client_email").focus();	return false;
			}

		}else{

			var ruc = $("#txt_cif").val();
			if (is_empty_var($("#txt_cif").val()) === 0 || $("#txt_cif").val().length < 5	|| ruc.charAt(0) == 0 && ruc.charAt(1) == '-' || ruc.charAt(0) == 0 && ruc.charAt(1) == 0) {
				shot_snackbar('Debe ingresar documento valido de 5 d&iacute;gitos.');
				$("#txt_cif").focus();	return false;
			}

			if ($('#sel_client_taxpayer').val() === "2") {
				self.close();
			}

			if (is_empty_var($("#txt_client_email").val()) === 1) {
				var patron=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;
				var valueForm = $("#txt_client_email").val();
				if(valueForm.search(patron)!=0){
					shot_snackbar('Verifique el correo electrónico.','bg-warning');
					$("#txt_client_email").focus();	return false;
				}
			}

		}
		plus_newclient();
	})
	$('#btn_cancel').click(function(){
		self.close();
	})

	$('#txt_telephone').validCampoFranz('0123456789 -');
	$('#txt_cif, #txt_dv').validCampoFranz('0123456789 -abcdefghijklmnopqrstuvwxyz:');

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
					<input type="text" name="txt_clientname" id="txt_clientname" class="form-control" onkeyup="chk_clientname(this)" value="<?php echo $name; ?>" onblur="this.value = this.value.toUpperCase()" />
				</div>
			</div>
			<div class="row px_15 pt_7">
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-3">
					<label for="sel_client_taxpayer" class="label label_blue_sky">Contribuyente</label>
					<select name="sel_client_taxpayer" id="sel_client_taxpayer" class="form-control" onchange="chk_client_taxpayer(this)">
						<option value="1" selected>Natural</option>
						<option value="2">Jur&iacute;dico</option>
					</select>
				</div>
				<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
					<label for="sel_client_type" class="label label_blue_sky">Tipo de Cliente</label>
					<select name="sel_client_type" id="sel_client_type" class="form-control">
						<option value="2" selected>Consumidor Final</option>
					</select>
				</div>
			</div>
			<div class="row px_15 pt_7">
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
					<label class="label label_blue_sky" for="txt_cif">RUC:</label>
					<input type="text" name="txt_cif" id="txt_cif" class="form-control" onkeyup="chk_cif(this)" />
				</div>
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
					<label class="label label_blue_sky" for="txt_dv">DV:</label>
					<input type="text" name="txt_dv" id="txt_dv" class="form-control" onkeyup="limitText(this, 2, 1)" />
				</div>
			</div>
			<div class="row px_15 pt_7">
				<div class="col-xs-8 col-sm-8 col-md-8 col-lg-3">
					<label class="label label_blue_sky" for="txt_client_email">Correo E:</label>
					<input type="text" name="txt_client_email" id="txt_client_email" class="form-control" />
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
					<label class="label label_blue_sky" for="txt_telephone">Tel&eacute;fono:</label>
					<input type="text" name="txt_telephone" id="txt_telephone" class="form-control" onkeyup="chk_telephone(this)" />
				</div>
			</div>
			<div class="row px_15 pt_7">
				<div id="container_direction" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<label class="label label_blue_sky" for="txt_direction">Direcci&oacute;n:</label>
					<textarea name="txt_direction" id="txt_direction" class="form-control" onkeyup="chk_direction(this)"></textarea>
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

</body>
</html>
