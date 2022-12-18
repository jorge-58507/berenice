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
	<?php include 'attached/php/req_required.php'; ?>
	<link href="attached/css/start_css.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript">
		$(document).ready(function() {
			$("#btn_start").click(function(){	window.location="start.php";	});
			$("#btn_exit").click(function(){	location.href="index.php";		});
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
			$("#btn_adminapp").on("click",function(){
				window.location='admin_application.php';
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
    		<div id="container_username" class="col-xs-4 col-sm-4 col-md-4 col-lg-4 visible-lg">
	        Bienvenido: <label class="bg-primary"><?php echo $rs_checklogin['TX_user_seudonimo']; ?></label>
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
				<div id="container_btn_option" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 btn_reveal">
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
						<button type="button" id="btn_configuration" class="btn btn-default btn-lg"><strong>Opc. Sistema</strong></button>
						&nbsp;&nbsp;
						<button type="button" id="btn_adminaccount" class="btn btn-info btn-lg"><strong>Usuarios</strong></button>
						&nbsp;&nbsp;
						<button type="button" id="btn_adminapp" class="btn btn-default btn-lg"><strong>Herramientas</strong></button>
					</p>
				</div>
			</form>
		</div>
		<div id="footer">
			<?php require 'attached/php/req_footer.php'; ?>
		</div>
	</div>
	<script type="text/javascript">
		<?php require 'attached/php/req_footer_js.php'; ?>
	</script>
</body>
</html>
