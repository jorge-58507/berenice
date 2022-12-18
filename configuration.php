<?php 
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_sale.php';

$fecha_actual = date('Y-m-d');
$user_id = $_COOKIE['coo_iuser'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>
<?php include 'attached/php/req_required.php'; ?>
<link href="attached/css/configuration_css.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/configuration_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>

<script type="text/javascript">
	$(document).ready(function() {
		$("#btn_start").click(function(){	window.location="start.php";	});
		$("#btn_exit").click(function(){	location.href="index.php";	});
  });
</script>
</head>
<body>
<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="logo_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-2" >
	  	<div id="logo" ></div>
	  </div>
		<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-10">
		 	<div id="container_username" class="col-xs-4 col-sm-4 col-md-4 col-lg-4 hidden-xs hidden-sm hidden-md">
				Bienvenido: <label class="bg-primary"><?php echo $rs_checklogin['TX_user_seudonimo']; ?></label>
		  </div>
			<div id="navigation" class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
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
					case '6':
						include 'attached/php/nav_assistant.php';
					break;
					case '7':
						include 'attached/php/nav_warehouse.php';
					break;
				}
	?>	</div>
		</div>
	</div>
	<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_btn_scroll" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
			<div id="container_scroll" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
				<?php
				switch ($_COOKIE['coo_tuser']){
					case '1':
					include 'attached/php/configuration_master.php';
					break;
					// case '2':
					// 	include 'attached/php/configuration_admin.php';
					// break;
					// case '3':
					// 	include 'attached/php/configuration_sale.php';
					// break;
					// case '4':
					// 	include 'attached/php/configuration_paydesk.php';
					// break;
					// case '5':
					// 	include 'attached/php/configuration_stock.php';
					// break;
					// case '6':
					// 	include 'attached/php/configuration_assistant.php';
					// break;
					// case '7':
					// 	include 'attached/php/configuration_warehouse.php';
					// break;
				}
				?>
			</div>
		</div>
		<div id="container_target" class="col-xs-9 col-sm-9 col-md-9 col-lg-9">

		</div>
	</div>
	<div id="footer">
		<?php require 'attached/php/req_footer.php'; ?>
	</div>
</div>

</body>
</html>
