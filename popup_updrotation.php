<?php
require 'bh_conexion.php';
$link=conexion();
date_default_timezone_set('America/Panama');

$fecha_actual = date('Y-m-d');
$fecha_ciclo=date('Y',strtotime($fecha_actual));
$fecha_subciclo = date ('Y-m',strtotime($fecha_actual));
$qry_chkrotate=$link->query("SELECT AI_rotacion_id FROM bh_rotacion WHERE TX_rotacion_ciclo = '$fecha_ciclo' AND TX_rotacion_json LIKE '%$fecha_subciclo%'")or die($link->error);
$nr_chkrotate=$qry_chkrotate->num_rows;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>

<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap-theme.css" rel="stylesheet" type="text/css" />
<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_layout.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_blocks.css" rel="stylesheet" type="text/css" />
<link href="attached/css/popup_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>

<script type="text/javascript">

$(document).ready(function() {
	var nr_chkrotate = <?php  echo $nr_chkrotate; ?>;
	if(nr_chkrotate < 1){
	$.ajax({	data: {"a": '<?php echo $fecha_actual; ?>'},	type: "GET",	dataType: "text",	url: "attached/get/plus_rotation.php", })
	.done(function( data, textStatus, jqXHR ) {
		console.log("Succesfully");
		if(data==='All Right'){	self.close();	}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD " + textStatus );	});
	}

	$('#btn_cancel').click(function(){
		self.close();
	});


});


</script>

</head>

<body>
<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
		<div id="logo" ></div>
	</div>
</div>

<div id="content-sidebar_popup" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

    <div id="container_spiner" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
	<i id="cog_1" class="fa fa-cog fa-spin fa-5x" aria-hidden="true"></i>
	<i id="cog_2" class="fa fa-cog fa-spin fa-5x" aria-hidden="true"></i>
    </div>

    <div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
    <button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
    </div>


</div>


<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
&copy; Derechos Reservados a: Trilli, S.A. 2017
	</div>
</div>
</div>

</body>
</html>
