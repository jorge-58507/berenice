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
	<script type="text/javascript" src="attached/js/sell_funct.js"></script>
	<!-- <script type="text/javascript" src="attached/js/addprovider_funct.js"></script> -->
	<script type="text/javascript">

		$(document).ready(function() {
			if (<?php echo $client_id ?> === 1) {
				self.close();
			}
			$('#btn_acept').click(function(){
				$(this).attr("disabled", true);
				setTimeout(() => { $(this).attr("disabled", false); }, 3000);
				cls_client.edit_client();
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
						<input type="text" name="<?php echo $client_id; ?>" id="txt_clientname" class="form-control" onkeyup="limitText(this,80,0)" value="<?php echo $r_function->replace_special_character($rs_client['TX_cliente_nombre']); ?>" readonly onclick="mod_clientname()"/>
					</div>
				</div>
				<div class="row px_15 pt_7">
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<label for="sel_client" class="label label_blue_sky">Tipo de Cliente</label>
						<select name="sel_client" id="sel_client" class="form-control" onchange="cls_client.chk_client_taxpayer(this)">
							<option value="" disabled selected>Seleccione</option>
							<option value="0">Consumidor Final</option>
							<option value="1">Persona Natural</option>
							<option value="2">Empresa</option>
							<option value="3">Gobierno</option>
							<option value="4">Empresa Extranjera</option>
							<option value="5">Persona Extranjera</option>
						</select>
						<input type="hidden" name="sel_client_taxpayer" id="sel_client_taxpayer" value="<?php echo $rs_client['TX_cliente_contribuyente']; ?>">
						<input type="hidden" name="sel_client_type" id="sel_client_type" value="<?php echo $rs_client['TX_cliente_tipo']; ?>">
					</div>
				</div>
				<div class="row px_15 pt_7">
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
						<label  class="label label_blue_sky"for="txt_cif">RUC:</label>
						<input type="text" name="txt_cif" id="txt_cif" class="form-control" onkeyup="limitText(this, 22, 1)"  value="<?php echo $rs_client['TX_cliente_cif']; ?>" />
					</div>
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
						<label class="label label_blue_sky" for="txt_dv">DV:</label>
						<input type="text" name="txt_dv" id="txt_dv" class="form-control" onkeyup="limitText(this, 2, 1)"  value="<?php echo $rs_client['TX_cliente_dv']; ?>" />
					</div>
				</div>
				<div class="row px_15 pt_7">
					<div class="col-xs-8 col-sm-8 col-md-8 col-lg-3">
						<label  class="label label_blue_sky"for="txt_client_email">Correo E:</label>
						<input type="text" name="txt_client_email" id="txt_client_email" class="form-control" onkeyup="limitText(this, 60, 1)" value="<?php echo $rs_client['TX_cliente_correo']; ?>" />
					</div>			
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<label  class="label label_blue_sky"for="txt_telephone">Tel&eacute;fono:</label>
						<input type="text" name="txt_telephone" id="txt_telephone" class="form-control" onkeyup="limitText(this, 30, 1)" value="<?php echo $rs_client['TX_cliente_telefono']; ?>" />
					</div>
				</div>
				<div class="row px_15 pt_7">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<label  class="label label_blue_sky"for="txt_direction">Direcci&oacute;n:</label>
						<textarea name="txt_direction" id="txt_direction" class="form-control"  onkeyup="limitText(this, 140, 1)" ><?php echo $rs_client['TX_cliente_direccion']; ?></textarea>
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
		const cls_client = new client();
			var taxpayer = document.getElementById('sel_client_taxpayer').value;
			var type = document.getElementById('sel_client_type').value

			if (taxpayer === "1") {
				switch (type) {
					case "2":
						$('#sel_client').val(0)
					break;
					case "1":
						$('#sel_client').val(1)
					break;
					case "4":
						$('#sel_client').val(5)
					break;
				}				
			}else{
				switch (type) {
					case "1":
						$('#sel_client').val(2)
					break;
					case "3":
						$('#sel_client').val(3)
					break;
					case "4":
						$('#sel_client').val(4)
					break;
				}				
			}
					// natural cobnsumidor final			1	2		0	
					// natural contribuyente					1	1		1
					// natural extanjero							1	4		5

					// empresa contribuyente					2	1		2
					// empresa Gobierno								2	3		3
					// empresa extranjero							2	4		4

		// document.getElementById('sel_client_taxpayer').value = "<?php echo $rs_client['TX_cliente_contribuyente']; ?>";
		// cls_client.chk_client_taxpayer(document.getElementById('sel_client_taxpayer'));
		// document.getElementById('sel_client_type').value = "<?php echo $rs_client['TX_cliente_tipo']; ?>";
	</script>
</body>
</html>
