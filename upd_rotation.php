<?php
require 'bh_conexion.php';
$link=conexion();
date_default_timezone_set('America/Panama');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>

<?php
if(!empty($_COOKIE['coo_iuser'])){
setcookie('coo_iuser','',time()-100);
}
if(!empty($_COOKIE['coo_tuser'])){
setcookie('coo_tuser','',time()-100);
}
if(!empty($_COOKIE['coo_suser'])){
setcookie('coo_suser','',time()-100);
}
if(!empty($_COOKIE['coo_tittle'])){
setcookie('coo_tittle','',time()-100);
}
session_start();
session_destroy();

$fecha_actual = date('Y-m-d');
$fecha_dia=date('d',strtotime($fecha_actual));
$fecha_ciclo=date('Y',strtotime($fecha_actual));
$fecha_subciclo = date ('Y-m',strtotime($fecha_actual));

$qry_chkrotate=$link->query("SELECT AI_rotacion_id FROM bh_rotacion WHERE TX_rotacion_ciclo = '$fecha_ciclo' AND TX_rotacion_json LIKE '%$fecha_subciclo%'")or die($link->error);
$nr_chkrotate=$qry_chkrotate->num_rows;

?>
<link href="attached/image/favicon.ico" rel="shortcut icon" type="icon" />
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap-theme.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_layout.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>

<script type="text/javascript">

$(document).ready(function() {
	var nr_chkrotate = <?php  echo $nr_chkrotate; ?>;
	console.log(nr_chkrotate);
	if(nr_chkrotate < 1){
		console.log("abrir");
		open_popup('popup_updrotation.php','_popup','500','420');
	}
});
</script>
</head>
<body>
<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div id="logo_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-2" >
  		<div id="logo" ></div>
   	</div>
		<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-10">
			<div id="navigation" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"></div>
		</div>
	</div>
	<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<form action="login.php" method="post" name="form_login"  id="form_login">
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
