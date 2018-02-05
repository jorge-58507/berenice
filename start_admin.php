<?php
require 'bh_conexion.php';
$link=conexion();
date_default_timezone_set('America/Panama');

require 'attached/php/req_login_admin.php';

if(isset($_GET['a'])){
	$facturaventa_ids=$_GET['a'];
}else{
	$facturaventa_ids=0;
}
if(isset($_GET['b'])){
	$client_id=$_GET['b'];
}else{
	$client_id=0;
}
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


<script type="text/javascript">

$(document).ready(function() {

$("#btn_navsale").click(function(){
	window.location="sale.php";
});
$("#btn_navstock").click(function(){
	window.location="stock.php";
});
$("#btn_navpaydesk").click(function(){
	window.location="paydesk.php";
})
$("#btn_navadmin").click(function(){
	window.location="start_admin.php";
});
$("#btn_start").click(function(){
	window.location="start.php";
});
$("#btn_exit").click(function(){
	location.href="index.php";
})


$("#btn_adminfacturaf").click(function(){
	window.location="admin_facturaf.php";
});
$("#btn_admindatoventa").click(function(){
	if(this.name != "0"){
		window.location="admin_datoventa.php?a="+this.name+"&b=<?php echo $client_id; ?>";
	}else{
		window.location="admin_facturaventa.php";
	};
});
$("#btn_adminvendedor").click(function(){
	window.location='admin_vendedor.php';
});
$("#btn_admincuentaxcobrar").click(function(){
	window.location='admin_account_receivable.php';
});
$("#btn_adminproducto").click(function(){
	window.location='stock.php';
});
$("#btn_admincuentaxpagar").on("click",function(){
	window.location='admin_provider.php';
});
$("#btn_configuration").on("click",function(){
	window.location='admin_option.php';
});
$("#btn_adminaccount").on("click",function(){
	window.location='admin_account.php';
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
    	<div id="container_username" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        Bienvenido: <label class="bg-primary">
         <?php echo $rs_checklogin['TX_user_seudonimo']; ?>
        </label>
        </div>
		<div id="navigation" class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
<?php
switch ($_COOKIE['coo_tuser']){
	case '1':
		include 'attached/php/nav_master.php';
	break;
	case '2':
		include 'attached/php/nav_admin.php';
	break;
	case '3':
		include 'attached/php/nav_sale.php';
	break;
	case '4':
		include 'attached/php/nav_paydesk.php';
	break;
	case '5':
		include 'attached/php/nav_stock.php';
	break;
}
?>
		</div>
	</div>

</div>

<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<form action="login.php" method="post" name="form_login"  id="form_login">

<div id="container_btn_option" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

<br />
<p>
<button type="button" id="btn_adminfacturaf" class="btn btn-info btn-lg"><strong>Facturas Fisc.</strong></button>
&nbsp;&nbsp;
<button type="button" id="btn_admindatoventa" name="<?php echo $facturaventa_ids ?>" class="btn btn-default btn-lg"><strong>Cotizaciones</strong></button>
&nbsp;&nbsp;
<button type="button" id="btn_adminvendedor" class="btn btn-info btn-lg"><strong>Vendedores</strong></button>
&nbsp;&nbsp;
<button type="button" id="btn_admincuentaxcobrar" class="btn btn-default btn-lg"><strong>Clientes</strong></button>
&nbsp;&nbsp;
<button type="button" id="btn_adminproducto" class="btn btn-info btn-lg"><strong>Producto</strong></button>
</p>
<p>
<button type="button" id="btn_admincuentaxpagar" class="btn btn-info btn-lg"><strong>Proveedores</strong></button>
&nbsp;&nbsp;
<button type="button" id="btn_configuration" class="btn btn-default btn-lg"><strong>Configuraci&oacute;n</strong></button>
&nbsp;&nbsp;
<button type="button" id="btn_adminaccount" class="btn btn-info btn-lg"><strong>Usuarios</strong></button>
</p>
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
