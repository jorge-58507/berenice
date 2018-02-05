<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login.php';
?>
<?php
session_start();
session_destroy();
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
<link href="attached/css/start_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/login_funct.js"></script>


<script type="text/javascript">

$(document).ready(function() {

$("#btn_sell").click(function(){
	window.location="sale.php";
});
$("#btn_stock").click(function(){
	window.location="stock.php";
});
$("#btn_paydesk").click(function(){
	window.location="paydesk.php";
})
$("#btn_config").click(function(){
	window.location="start_admin.php";
});






$("#btn_start").click(function(){
	window.location="start.php";
});
$("#btn_exit").click(function(){
	location.href="index.php";
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
	
	<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-10">
		<div id="navigation" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		</div>
	</div>

</div>

<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<form action="login.php" method="post" name="form_login"  id="form_login">

<div id="container_btn_option" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<br />
<br />
<button type="button" id="btn_sell" name="btn_sell" class="btn btn-primary"><strong>Venta</strong></button>
&nbsp;&nbsp;
<?php
if($tuser == 3 || $tuser == 4){
	echo "";
}else{
?>
<button type="button" id="btn_stock" name="btn_stock" class="btn btn-success"><strong>Inventario</strong></button>
<?php
}
?>
&nbsp;&nbsp;
<?php
if($tuser == 3 || $tuser == 5){
	echo "";
}else{
?>
<button type="button" id="btn_paydesk" name="btn_paydesk" class="btn btn-warning"><strong>Caja</strong></button>
<?php
}
?>
&nbsp;&nbsp;
<?php
if($tuser == 3 || $tuser == 5 || $tuser == 4){
	echo "";
}else{
?>
<button type="button" id="btn_config" name="btn_config" class="btn btn-info"><strong>Administraci&oacute;n</strong></button>
<?php } ?>
</div>

</form>
</div>


<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
        <div id="container_btnadminicon" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
        </div>
        <div id="container_txtcopyright" class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
    &copy; Derechos Reservados a: Trilli, S.A. 2017
        </div>
        <div id="container_btnstart" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                    		<i id="btn_start" class="fa fa-home" title="Ir al Inicio"></i>
        </div>
        <div id="container_btnexit" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <button type="button" class="btn btn-danger" id="btn_exit">Salir</button></div>
        </div>
	</div>
</div>
</div>

</body>
</html>
