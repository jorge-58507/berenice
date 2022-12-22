<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login.php';
session_start();
session_destroy();

$raw_notification = $r_function->method_message('read');

$salutation = (date('a') != 'am') ? 'Buenas Tardes' : 'Buenos Dias';
$content = file_get_contents('attached/tool/admin_welcome/admin_welcome.json');
$raw_msgs = json_decode($content, true);
$msg_saved = $raw_msgs['saved'];
$d=mt_rand(0,count($msg_saved[1]['phrase'])-1);
$message = $msg_saved[1]['phrase'][$d]['message'];

$raw_motd = array_reverse($msg_saved[0]['motd']);
array_splice($raw_motd,4);
//   ####################    Resumen
if(date('w',strtotime('-1 day')) === '0') {
	$date_abstract = date('Y-m-d',strtotime('-2 day'));
}else{
	$date_abstract = date('Y-m-d',strtotime('-1 day'));	
}
$line_user = '';
if ($_COOKIE['coo_tuser'] > 2) {
	$line_user = " AND bh_facturaventa.facturaventa_AI_user_id = '{$_COOKIE['coo_iuser']}'";
}
$txt_facturaf="SELECT sum(TX_facturaf_total) as total
FROM bh_facturaf
INNER JOIN bh_facturaventa ON bh_facturaventa.facturaventa_AI_facturaf_id = bh_facturaf.AI_facturaf_id
WHERE  TX_facturaf_fecha = '$date_abstract'".$line_user;
$qry_facturaf=$link->query($txt_facturaf)or die($link->error);
$rs_facturaf = $qry_facturaf->fetch_array(MYSQLI_ASSOC);

$txt_nc = "SELECT TX_notadecredito_monto, TX_notadecredito_impuesto FROM bh_notadecredito WHERE TX_notadecredito_fecha = '$date_abstract'";
$line_user_nc = '';
if ($_COOKIE['coo_tuser'] > 2) {
	$line_user_nc = " AND bh_notadecredito.notadecredito_AI_user_id = '{$_COOKIE['coo_iuser']}'";
}
$qry_nc = $link->query($txt_nc.$line_user_nc)or die($link->error);
$ttl_nc_impuesto = 0; $ttl_nc = 0;
while ($rs_nc = $qry_nc->fetch_array()) {
	$ttl_nc_impuesto += $rs_nc['TX_notadecredito_impuesto'];
	$ttl_nc += $rs_nc['TX_notadecredito_monto']+$rs_nc['TX_notadecredito_impuesto'];
}

$facturaf_total = $rs_facturaf['total'];
$nc_total = $ttl_nc+$ttl_nc_impuesto;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Trilli, S.A. - Todo en Materiales</title>
	<?php include 'attached/php/req_required.php'; ?>
	<link href="attached/css/start_css.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="attached/js/login_funct.js"></script>
</head>
<body>
	<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div id="logo_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-2" >
				<div id="logo" ></div>
		 	</div>
			<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-10">
				<div id="navigation" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"></div>
			</div>
		</div>
		<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div id="container_btn_option" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<?php
				switch ($tuser) {
					case '1':
						include 'attached/php/nav_master.php';
						break;
					case '2':
						include 'attached/php/nav_admin.php';
						break;
					case '3': // VENDEDOR
						include 'attached/php/nav_sale.php';
						break;
					case '4':
						include 'attached/php/nav_paydesk.php';
						break;
					case '5': //INVENTARIO
						include 'attached/php/nav_stock.php';
						break;
					case '6': //ASISTENTE
						include 'attached/php/nav_assistant.php';
						break;
				}
				?>
			</div>
			<div class="col-md-1 col-lg-1 visible-md visible-lg"> &nbsp; </div>
			<div id="container_wailing_wall" class="col-xs-12 col-sm-12 col-md-10 col-lg-10 no_padding">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div id="wailing_wall_salutation" class="col-xs-12 col-sm-3 col-md-3 col-lg-3 well al_center px_7">
						<h3>Bienvenida(o)</h3>
						<h2><?php echo $rs_checklogin['TX_user_seudonimo']; ?></h2>
						<h5><?php echo $salutation,', espero que estes bien.<br />Y recuerda...'; ?></h5>

						<span style="font-size: 30px">"</span><span style="font-size: 20px"><?php echo $message ?></span><span style="font-size: 30px">"</span>
					</div>
					<div id="wailing_wall_motd" class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 px_0 al_center">
							<h3>¡ATENCION!</h3>
							<h4>Mensaje del Dia.</h4>
						</div>
						<div id="container_motd" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"></div>
					</div>
					<div id="wailing_wall_abstract" class="col-xs-12 col-sm-3 col-md-3 col-lg-3 well al_center px_7">
						<h4>Resumen del <a href="#" style="text-decoration: none;" onclick="open_popup('popup_surplus_report.php?date=<?php echo date('d-m-Y',strtotime($date_abstract)); ?>','_popup','900','300')" ><?php echo date('d-m-Y',strtotime($date_abstract)) ?></a></h4>
						<?php echo "<strong>Ventas:</strong> B/.".number_format($facturaf_total,2)."<br />"; ?>
						<?php echo "<strong>Devoluciones:</strong> B/.".number_format($nc_total,2)."<br />"; ?>							
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center px_7">
							<img src="attached/image/dec_div.png" alt="" width="100%">
						</div>
						<a href="#" style="text-decoration: none;" onclick="open_popup('popup_surplus_report.php?date=<?php echo date('d-m-Y',strtotime($date_abstract)); ?>','_popup','900','300')" ><h4>Ver detalle</h4></a>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-primary" id="nav_wailing_wall" ></div>
			</div>
			<div class="col-md-1 col-lg-1 visible-md visible-lg"> &nbsp; </div>
		</div>
		<div id="footer">
			<?php include 'attached/php/req_footer.php'; ?>
		</div>
	</div>
	<script>
		$(document).ready(function() {
			var raw_motd = JSON.parse('<?php echo json_encode($raw_motd); ?>');
			var content = '';
			for (const a in raw_motd) {
				var date_obj = new Date();
				var current_date = `${date_obj.getFullYear()}-${date_obj.getMonth() + 1}-${date_obj.getDate()}`;

				var bg_color = (current_date === raw_motd[a]['fecha']) ? 'bg_red' : 'bg-primary';
				content += `
						<div class="row no_padding bl_1">
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ${bg_color} motd_fecha">
								${raw_motd[a]['fecha']}
							</div>
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-info h_40">
								${raw_motd[a]['message']}
							</div>
						</div>
				`;
				console.log(content)
			}
			$('#container_motd').html(content)
		})
		ScrollReveal().reveal('.btn')
		ScrollReveal().reveal('#container_motd', { delay: 500 })

	</script>
</body>
</html>
