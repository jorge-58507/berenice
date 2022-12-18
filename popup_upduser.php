<?php
require 'bh_conexion.php';
$link=conexion();
$user_id = $_GET['a'];
$qry_typeuser = $link->query("SELECT bh_tuser.AI_tuser_id, bh_tuser.TX_tuser_value FROM bh_tuser");
$qry_account =	$link->query("SELECT bh_user.AI_user_id, bh_user.TX_user_seudonimo, bh_user.TX_user_type, bh_tuser.TX_tuser_value FROM (bh_user INNER JOIN bh_tuser ON bh_tuser.AI_tuser_id = bh_user.TX_user_type) WHERE AI_user_id = '$user_id'")or die($link->error);
$rs_account =	$qry_account->fetch_array();

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
<script type="text/javascript" src="attached/js/jshash-2.2/sha1.js"></script>

<script type="text/javascript">

$(document).ready(function() {
	$('#btn_cancel').click(function(){
		self.close();
	});

	$('#btn_save').click(function(){
		if($("#txt_password").val() != $("#txt_password_2").val()){ return false; }
		$('#txt_password').prop("value",
		hex_sha1($('#txt_password').val())
		)
		$.ajax({ data: {"a" : $("#txt_seudonimo").val(), "b" : $("#sel_type").val(), "c" : $("#txt_password").val(), "d" : <?php echo $_GET['a'];  ?> }, type: "GET", dataType: "text", url: "attached/get/upd_user.php",	})
		.done(function( data, textStatus, jqXHR ) {	console.log("GOOD " + textStatus );
			if(data){ window.opener.location.reload(); setTimeout(function(){	self.close(); },200) }
		})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
	});

	$('#txt_seudonimo').validCampoFranz(".0123456789abcdefghijklmnopqrstuvwxyz ");
	$('#txt_password').validCampoFranz('.0123456789abcdefghijklmnopqrstuvwxyz- ');

	$('#txt_seudonimo').on("keyup", function(){
		this.value = this.value.toUpperCase();
	})

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
<form id="form_upduser" class="" action="" method="post">

	<div id="container_accountinfo" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_txtseudonimo" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label for="txt_seudonimo">Nombre</label>
			<input type="text" id="txt_seudonimo" class="form-control" value="<?php echo $rs_account['TX_user_seudonimo']; ?>"	/>
		</div>
		<div id="container_seltype" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label for="sel_type">Tipo de Usuario</label>
			<select id="sel_type" class="form-control" name="sel_type">
<?php while ($rs_typeuser = $qry_typeuser->fetch_array()) {
			if ($rs_account['TX_user_type'] == $rs_typeuser['AI_tuser_id']) {
			?>
				<option value="<?php echo $rs_typeuser['AI_tuser_id']; ?>" selected="selected" ><?php echo $rs_typeuser['TX_tuser_value']; ?></option>
			<?php }else{ ?>
				<option value="<?php echo $rs_typeuser['AI_tuser_id']; ?>" ><?php echo $rs_typeuser['TX_tuser_value']; ?></option>
			<?php }
			} ?>
			</select>
		</div>
		<div id="container_txtpassword" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label for="txt_password">Contrase&ntilde;a</label>
			<input type="password" id="txt_password" class="form-control"	value="Hey Bochinchoso" />
		</div>
		<div id="container_txtpassword" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label for="txt_password_2">Confirmar</label>
			<input type="password" id="txt_password_2" class="form-control"	value="" />
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
&copy; Derechos Reservados a: Jorge Salda&nacute;a <?php echo date('Y'); ?>
	</div>
</div>
</div>

</body>
</html>
