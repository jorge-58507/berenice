<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_sale.php';

$fecha_actual = date('Y-m-d');
$user_id = $_COOKIE['coo_iuser'];

function ObtenerIP(){
	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"),"unknown"))
	$ip = getenv("HTTP_CLIENT_IP");
	else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
	$ip = getenv("HTTP_X_FORWARDED_FOR");
	else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
	$ip = getenv("REMOTE_ADDR");
	else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
	$ip = $_SERVER['REMOTE_ADDR'];
	else
	$ip = "IP desconocida";
	return($ip);
}
$ip   = ObtenerIP();
$cliente = gethostbyaddr($ip);

$txt_facturaventa="SELECT bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.AI_facturaventa_id, bh_cliente.TX_cliente_nombre, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status, bh_user.TX_user_seudonimo
FROM ((bh_facturaventa
INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaventa.facturaventa_AI_user_id)
WHERE ";
switch ($_COOKIE['coo_tuser']) {
	case "1":
		$txt_facturaventa=$txt_facturaventa." 1 ORDER BY AI_facturaventa_id DESC LIMIT 10";
	break;
	case "2":
		$txt_facturaventa=$txt_facturaventa." TX_facturaventa_status != 'CANCELADA' ORDER BY AI_facturaventa_id DESC LIMIT 10";
	break;
	case "4":
		$txt_facturaventa=$txt_facturaventa." TX_facturaventa_status != 'CANCELADA' ORDER BY AI_facturaventa_id DESC LIMIT 10";
	break;
	default:
		$txt_facturaventa=$txt_facturaventa." TX_facturaventa_status != 'CANCELADA' AND TX_facturaventa_status != 'INACTIVA' ORDER BY AI_facturaventa_id DESC LIMIT 10";
	break;
}
$qry_facturaventa=$link->query($txt_facturaventa)or die($link->error);
$rs_facturaventa=$qry_facturaventa->fetch_array();

$prep_payment = $link->prepare("SELECT AI_datopago_id, datopago_AI_metododepago_id, TX_datopago_monto FROM bh_datopago WHERE datopago_AI_facturaf_id = ?")or die($link->error);

$qry_lastsale = $link->query("SELECT bh_facturaventa.TX_facturaventa_numero, bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_total, bh_cliente.TX_cliente_nombre FROM ((bh_facturaf INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_facturaf.facturaf_AI_cliente_id) INNER JOIN bh_facturaventa ON bh_facturaventa.facturaventa_AI_facturaf_id = bh_facturaf.AI_facturaf_id) WHERE facturaventa_AI_user_id = '$user_id' AND bh_facturaf.TX_facturaf_fecha = '$fecha_actual' ORDER BY AI_facturaf_id DESC")or die($link->error);
$raw_lastsale=array();
$raw_payment = ["1"=>0,"2"=>0,"3"=>0,"4"=>0,"5"=>0,"7"=>0,"8"=>0];
while ($rs_lastsale = $qry_lastsale->fetch_array()) {
  $raw_lastsale[] = $rs_lastsale;
	$prep_payment->bind_param("i",$rs_lastsale['AI_facturaf_id']); $prep_payment->execute(); $qry_payment = $prep_payment->get_result();
  while ($rs_payment = $qry_payment->fetch_array(MYSQLI_ASSOC)) {
    $raw_payment[$rs_payment['datopago_AI_metododepago_id']] += $rs_payment['TX_datopago_monto'];
  }
}
$sumatoria = $raw_payment[1]+$raw_payment[2]+$raw_payment[3]+$raw_payment[4]+$raw_payment[7]+$raw_payment[8];
$qry_user = $link->query("SELECT TX_user_meta FROM bh_user WHERE AI_user_id = '{$_COOKIE['coo_iuser']}'")or die($link->error);
$rs_user = $qry_user->fetch_array();
$porcentaje = round(($sumatoria*100)/$rs_user['TX_user_meta']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Trilli, S.A. - Todo en Materiales</title>
	<?php include 'attached/php/req_required.php'; ?>
	<link href="attached/css/sell_css.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="attached/js/sell_funct.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#btn_newsale").click(function(){	window.location.href="new_sale.php";	});
			$("#btn_inspectsale").click(function(){
				open_popup_w_scroll('popup_inspect_sale.php','_popup','1040','420');
			});
			$("#txt_filterfacturaventa").on("keyup",function(){
				filter_sale(this.value);
			});
			$("#sel_status").on("change",function(){
				$("#txt_filterfacturaventa").keyup();
			});
			$("#txt_date").on("change",function(){
				$("#txt_filterfacturaventa").keyup();
			});
			$( function() {
				$("#txt_date").datepicker({
					changeMonth: true,
					changeYear: true
				});
			});
			$(window).on('beforeunload',function(){	close_popup();	});
			var json_lastsale = '<?php echo json_encode($raw_lastsale); ?>';
			raw_lastsale = JSON.parse(json_lastsale);
			setTimeout(function(){	get_lastsale();	},60000);
		});

		function upd_loginclient (){
			var ans = confirm("¿Desea ocultar este aviso?");
			if (!ans) { return false; }
			$.ajax({	data: {"a" : '<?php echo $_COOKIE['coo_iuser']; ?>', "b" : '<?php echo $cliente; ?>'}, type: "GET", dataType: "text", url: "attached/get/upd_loginclient.php",	})
			.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
				setTimeout(function(){ window.location.href="index.php";},500);
			})
			.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
		}
		function get_lastsale(){
			$.ajax({	data: {"a" : '<?php echo $_COOKIE['coo_iuser']; ?>'}, type: "GET", dataType: "text", url: "attached/get/get_lastsale.php",	})
			.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
				var data = JSON.parse(data);
				raw_data = data['last_sale'];
				var content = '';
				if (raw_data.length != raw_lastsale.length) {
					for (var x in raw_data) {
						if (x == 0) {
							content += `<tr id="last_sale" class="display_none">
								<td>${raw_data[x]['TX_facturaventa_numero']}</td>
								<td>${raw_data[x]['TX_cliente_nombre']}</td>
								<td>${raw_data[x]['TX_facturaf_total']}</td>
								</tr>`;
						}else{
							var total = parseFloat(raw_data[x]['TX_facturaf_total']);
							content += `<tr>
								<td>${raw_data[x]['TX_facturaventa_numero']}</td>
								<td>${raw_data[x]['TX_cliente_nombre']}</td>
								<td>${total.toFixed(2)}</td>
								</tr>`;
						}
					}
					raw_lastsale = raw_data;
				}else{
					content += $("#tbl_lastsale tbody").html();
				}
				const porcentaje = Math.round10(data['datopago']);
				$("#bar_goal").html(porcentaje+'%');
				$("#container_goal").attr("title",porcentaje+'%');
				$("#bar_goal").css("width",porcentaje+'%');
				$("#tbl_lastsale tbody").html(content);
				$("#last_sale").show(5000);

			})
			.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});

			setTimeout(function(){	get_lastsale();	},60000);
		}
		function clear_sales(){
			$.ajax({	data: {"a" : '<?php echo $_COOKIE['coo_iuser']; ?>'}, type: "GET", dataType: "text", url: "attached/get/clean_sale.php",	})
			.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus); })
			.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
		}
	</script>
</head>
<body>
	<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div id="logo_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-2" >
		  	<div id="logo" ></div>
		  </div>
			<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-10">
			 	<div id="container_username" class="col-lg-4  visible-lg">
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
				}
		?>
			</div>
		</div>
	</div>
	<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<form action="sale.php" method="post" name="form_sell"  id="form_sell">
<?php
			if (!empty($_COOKIE['coo_usercliente'])) {
				if ($cliente != $_COOKIE['coo_usercliente']) {?>
					<div id="span_useronline" class="visible-lg">
						<div onclick="upd_loginclient()" class="col-xs-2 col-sm-2 col-md-2 col-lg-2 no_padding"><i title="Omitir" class="glyphicon glyphicon-exclamation-sign"></i></div>
							Te haz conectado en:<br />
						<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 no_padding"><span><?php echo strtoupper(substr($_COOKIE['coo_usercliente'],0,8)); ?></span></div>
					</div>
	<?php
				}
			} ?>
			<div id="container_btn_sale" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 btn_reveal">
				<button type="button" id="btn_newsale" class="btn btn-info btn-lg" autofocus="autofocus"><strong>Nueva Venta</strong></button>
			   &nbsp;
				<button type="button" id="btn_inspectsale" class="btn btn-default btn-lg"><strong>Listado de Venta</strong></button>
			</div>
			<div id="container_facturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div id="container_filterfacturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		      <div id="container_txtfilterfacturaventa" class="col-xs-12 col-sm-8 col-md-5 col-lg-5">
		        <label for="txt_filterfacturaventa" class="label label_blue_sky">Buscar</label>
		      	<input type="text" id="txt_filterfacturaventa" class="form-control" />
		      </div>
					<div id="container_selfilterfacturaventa" class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
		      	<label for="sel_status" class="label label_blue_sky">Estado</label>
		        <select id="sel_status" class="form-control">
		        	<option value="">TODAS</option>
		        	<option value="ACTIVA">ACTIVA</option>
		        	<option value="INACTIVA">INACTIVA</option>
		        	<option value="FACTURADA">FACTURADA</option>
		        </select>
		      </div>
		      <div id="container_txtfilterfacturaventa" class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
		        <label for="txt_date" class="label label_blue_sky">Fecha</label>
		        <input type="text" id="txt_date" class="form-control" readonly="readonly" />
		      </div>
					<div id="container_txtfilterfacturaventa" class="col-xs-2 col-sm-2 col-md-2 col-lg-2 side-btn-md-label">
						<button type="button" id="" class="btn btn-danger btn-xs clear_date" onclick="setEmpty('txt_date')"><span class="glyphicon glyphicon-exclamation-sign"></span></button>
						<button type="button" id="" class="btn btn-warning btn-xs" onclick="clear_sales()" title="Clear Sales"><span class="fa fa-cab"></span></button>
					</div>
		    </div>
		    <div id="container_tblfacturaventa" class="col-xs-12 col-sm-12 col-md-9 col-lg-9">

					<table id="tbl_facturaventa" class="table table-bordered table-striped">
						<caption class="caption">Cotizaciones Guardadas</caption>
						<thead class="bg-primary">
					  	<tr>
					    	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
			          <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Cliente</th>
			          <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Factura</th>
			          <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Total</th>
			          <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Status</th>
			        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Vendedor</th>
			          <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
			          <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
			        </tr>
				    </thead>
			    	<tfoot class="bg-primary">
			    		<tr>
				        <td colspan="8"></td>
							</tr>
			    	</tfoot>
		    		<tbody>
		<?php 		if($qry_facturaventa->num_rows > 0){
								do{ ?>
		    					<tr>
			        			<td><?php	echo $date=date('d-m-Y',strtotime($rs_facturaventa['TX_facturaventa_fecha']));	?></td>
						        <td><?php echo $rs_facturaventa['TX_cliente_nombre']; ?></td>
						        <td><?php echo $rs_facturaventa['TX_facturaventa_numero']; ?></td>
						        <td><?php echo number_format($rs_facturaventa['TX_facturaventa_total'],2); ?></td>
		<?php						switch($rs_facturaventa['TX_facturaventa_status']){
											case "ACTIVA":	$font='#00CC00';	break;
											case "FACTURADA":	$font='#0033FF';	break;
											case "INACTIVA":	$font='#337ab7';	break;
											default:	$font='#990000';
										}	?>
										<td style="font-weight:bold; color:<?php echo $font ?>;"><?php	echo $rs_facturaventa['TX_facturaventa_status']; ?></td>
										<td><?php echo $rs_facturaventa['TX_user_seudonimo']; ?></td>
			        			<td><?php
											if($rs_facturaventa['TX_facturaventa_status'] == "ACTIVA"){ ?>
							        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" onclick="jacascript:window.location='old_sale.php?a='+this.name">Modificar</button>
			<?php 					}else if($rs_facturaventa['TX_facturaventa_status'] == "FACTURADA" && $_COOKIE['coo_iuser'] > '2'){ ?>
							        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" disabled="disabled">Modificar</button>
			<?php 					}else if($rs_facturaventa['TX_facturaventa_status'] == "INACTIVA" && $_COOKIE['coo_iuser'] > '2'){ ?>
							        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" disabled="disabled">Modificar</button>
			<?php 					}else{ ?>
							        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" onclick="javascript:window.location='old_sale.php?a='+this.name">Modificar</button>
			<?php 					} ?>
						        </td>
						        <td><button type="button" id="btn_print" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-info btn-md-fa" onclick="print_html('print_sale_html.php?a='+this.name+'')"><i class="fa fa-print"></i></button></td>
			    				</tr>
		<?php				}while($rs_facturaventa=$qry_facturaventa->fetch_array());
		 					}else{ ?>
						    <tr>
					        <td colspan="8"> </td>
						    </tr>
		<?php 		} ?>
				    </tbody>
					</table>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 no_padding">
					<div id="container_goal" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 px_0 py_14" title="<?php echo $porcentaje."%"; ?>">
						<label for="div_goal" class="label label_blue_sky">Meta</label>
						<div class="progress">
							<div id="bar_goal" class="progress-bar progress-bar-success progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="70"	aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $porcentaje; ?>%"><?php echo $porcentaje."%"; ?></div>
						</div>
					</div>
					<div id="container_tbl_lastsale" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 px_0 py_14">
						<table id="tbl_lastsale" class="table table-condensed table-bordered table-striped">
							<caption>Ultimas Ventas</caption>
							<thead class="bg_green">
								<tr onclick="get_lastsale()">
									<th>No.</th>
									<th>Cliente</th>
									<th>Monto</th>
								</tr>
							</thead>
							<tbody>
	<?php 				if (!empty($raw_lastsale)) {
									foreach ($raw_lastsale as $key => $lastsale) {
										echo "<tr><td>{$lastsale['TX_facturaventa_numero']}</td><td>{$lastsale['TX_cliente_nombre']}</td><td class='al_right'>".number_format($lastsale['TX_facturaf_total'],2)."</td></tr>";
									}
								} else {
									echo "<tr><td colspan='3'></td></tr>";
								}	?>
							</tbody>
							<tfoot class="bg_green">
								<tr>
									<td colspan="3"></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div id="footer">
		<?php require 'attached/php/req_footer.php'; ?>
	</div>
</div>
<script type="text/javascript">
	<?php include 'attached/php/req_footer_js.php'; ?>
</script>

</body>
</html>
