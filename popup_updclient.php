<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_sale.php';
$client_id=$_GET['a'];
$qry_client = $link->query("SELECT AI_cliente_id, TX_cliente_nombre, TX_cliente_cif, TX_cliente_telefono, TX_cliente_direccion, TX_cliente_porcobrar, TX_cliente_dv, TX_cliente_contribuyente, TX_cliente_tipo, TX_cliente_correo FROM bh_cliente WHERE AI_cliente_id = '$client_id'")or die($link->error);
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
				var opener_url = window.opener.location;
				patt = RegExp(/old_sale|new_collect/);
				activo = (patt.test(opener_url)) ?	'' :	window.opener.$(".tab-pane.active").attr("id");	activo = activo.replace("_sale","");
				var name = url_replace_regular_character($("#txt_clientname").val());
				var pending = ( $('#cb_pending').prop('checked') )  ? '1' : '0';
				var direction = ($("#txt_direction").val() === "" || $("#txt_direction").val().length < 6) ? 'NO INDICA' : $("#txt_direction").val();
				$.ajax({
					data: { "a" : name, "b" : $("#txt_cif").val(), "c" : $("#txt_telephone").val(), "d" : direction, "e" : '<?php echo $client_id; ?>', "f" : activo, "g" : $("#txt_dv").val(), "h" : pending, "i" : $('#sel_client_taxpayer').val(), "j" :  $('#sel_client_type').val(), "k" :  $('#txt_client_email').val() },
					type: "GET",	
					dataType: "text",	
					url: "attached/get/upd_client_info.php", 
				})
				.done(function( data, textStatus, jqXHR ) {
					var data_obj = JSON.parse(data);
					if (data_obj['status'] === 'failed') {
						shot_snackbar('Esa cédula ya existe','bg-warning'); 
						return false;			
					}
					var content = `
						<label class="label label_blue_sky" for="txt_filterclient">Cliente:</label>
						<input type="text" class="form-control" alt="<?php echo $client_id; ?>" id="txt_filterclient${data_obj['activo']}" name="txt_filterclient" value="${data_obj['name']}" onkeyup="unset_filterclient${data_obj['function']}(event)" />
					`;
					if (activo != '') {
						window.opener.$("#container_txtfilterclient"+"_"+activo).html(content);
					} else {
						window.opener.$("#container_txtfilterclient").html(content);
					}
					setTimeout("self.close()",250);
				})
				.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
			})
			$('#btn_cancel').click(function(){
				self.close();
			})
			$("#txt_clientname").on("blur", function () {
				this.value = this.value.toUpperCase();
			})
			$("#cb_pending").attr('disabled',true);
			$("#lbl_cb_pending").on("click", function(){		
				$.ajax({ data: "", type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
				.done(function( data, textStatus, jqXHR ) {	console.log("GOOD " + textStatus);
					if (data[0][0] != "") {
						$("#cb_pending").attr('disabled',false);
					}
				})
				.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
			})

			$('#txt_telephone').validCampoFranz('0123456789 -');
			$('#txt_cif, #txt_dv').validCampoFranz('0123456789 -abcdefghijklmnopqrstuvwxyz:');
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
			<div id="logo_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
				<div id="logo"></div>
			</div>
		</div>
		<div id="content-sidebar_popup" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div id="snackbar"></div>
			<form method="post" name="form_addprovider">
				<div class="row px_15 pt_7">
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<label  class="label label_blue_sky"for="txt_providername">Nombre:</label>
						<input type="text" name="txt_clientname" id="txt_clientname" class="form-control" onkeyup="chk_clientname(this)" value="<?php echo $r_function->replace_special_character($rs_client['TX_cliente_nombre']); ?>" readonly onclick="mod_clientname()"/>
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
						<label  class="label label_blue_sky"for="txt_cif">RUC:</label>
						<input type="text" name="txt_cif" id="txt_cif" class="form-control" onkeyup="chk_cif(this)"  value="<?php echo $rs_client['TX_cliente_cif']; ?>" />
					</div>
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
						<label class="label label_blue_sky" for="txt_dv">DV:</label>
						<input type="text" name="txt_dv" id="txt_dv" class="form-control" onkeyup="limitText(this, 2, 1)"  value="<?php echo $rs_client['TX_cliente_dv']; ?>" />
					</div>
				</div>
				<div class="row px_15 pt_7">
					<div class="col-xs-8 col-sm-8 col-md-8 col-lg-3">
						<label  class="label label_blue_sky"for="txt_client_email">Correo E:</label>
						<input type="text" name="txt_client_email" id="txt_client_email" class="form-control" value="<?php echo $rs_client['TX_cliente_correo']; ?>" />
					</div>			
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<label  class="label label_blue_sky"for="txt_telephone">Tel&eacute;fono:</label>
						<input type="text" name="txt_telephone" id="txt_telephone" class="form-control" onkeyup="chk_telephone(this)" value="<?php echo $rs_client['TX_cliente_telefono']; ?>" />
					</div>
				</div>
				<div class="row px_15 pt_7">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<label  class="label label_blue_sky"for="txt_direction">Direcci&oacute;n:</label>
						<textarea name="txt_direction" id="txt_direction" class="form-control" onkeyup="chk_direction(this)" ><?php echo $rs_client['TX_cliente_direccion']; ?></textarea>
					</div>
				</div>
				<div class="row px_15 pt_7">
					<div class="col-xs-4 col-sm-2 col-md-2 col-lg-1">
						<label for="cb_pending" class="label label_blue_sky">Por Cobrar</label>
						<label id="lbl_cb_pending" class="switch">
							<?php $pending = ($rs_client['TX_cliente_porcobrar'] === "1") ? 'checked' : '' ?>
							<input id="cb_pending" type="checkbox" <?php echo $pending; ?> >
							<span class="slider_switch round"></span>
						</label>
					</div>
				</div>
				<div class="row px_21 pt_7">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text_center">
						<button type="button" id="btn_acept" class="btn btn-success">Aceptar</button>
						&nbsp;
						<button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
					</div>
				</div>
			</form>
		</div>
		<div id="footer">
			<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				&copy; Derechos Reservados a: Jorge Salda&nacute;a <?php echo date('Y'); ?>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		document.getElementById('sel_client_taxpayer').value = "<?php echo $rs_client['TX_cliente_contribuyente']; ?>";
		chk_client_taxpayer(document.getElementById('sel_client_taxpayer'));
		document.getElementById('sel_client_type').value = "<?php echo $rs_client['TX_cliente_tipo']; ?>";
	</script>
</body>
</html>
