<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_sale.php';

$fecha_actual = date('Y-m-d');
$user_id = $_COOKIE['coo_iuser'];
$fecha_i = date('d-m-Y',strtotime('2017-10-01'));
$fecha_f = date('d-m-Y',strtotime($fecha_actual));

$qry_facturaf = $link->query("SELECT bh_producto.AI_producto_id, bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_cliente.TX_cliente_nombre, bh_cliente.AI_cliente_id, bh_datoventa.TX_datoventa_descripcion, bh_datoventa.TX_datoventa_cantidad
FROM ((((bh_facturaf
INNER JOIN bh_cliente ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id)
INNER JOIN bh_facturaventa ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datoventa.datoventa_AI_producto_id)
WHERE bh_datoventa.TX_datoventa_entrega = 0 ORDER BY AI_facturaf_id DESC");
$raw_porentregar = array();
while ($rs_facturaf = $qry_facturaf->fetch_array(MYSQLI_ASSOC)) {
	$raw_porentregar[] = $rs_facturaf;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Trilli, S.A. - Todo en Materiales</title>
	<?php include 'attached/php/req_required.php'; ?>
	<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />
	<link href="attached/css/warehouse_css.css" rel="stylesheet" type="text/css" />
	<link href="attached/css/sell_css.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="attached/js/warehouse_funct.js"></script>
	<script type="text/javascript">
			var json_todeliver = '<?php echo json_encode($raw_porentregar); ?>';
			var raw_todeliver = JSON.parse(json_todeliver);
		$(document).ready(function() {
			var raw_todeliver_ff = new Object();
			for (var x in raw_todeliver) {
				var flete = 0;	var n_array = [];
				if (raw_todeliver[x]['AI_facturaf_id'] in raw_todeliver_ff) {
				}else{
					raw_todeliver_ff[raw_todeliver[x]['AI_facturaf_id']] = [];
					n_array = {"facturaf" : raw_todeliver[x]['TX_facturaf_numero'],"fecha" : raw_todeliver[x]['TX_facturaf_fecha'], "hora" : raw_todeliver[x]['TX_facturaf_hora'], "cliente" : raw_todeliver[x]['TX_cliente_nombre'], "flete" : 0};
					raw_todeliver_ff[raw_todeliver[x]['AI_facturaf_id']] = n_array;
				}
				if(raw_todeliver_ff[raw_todeliver[x]['AI_facturaf_id']]['flete'] !== 1){
					if (raw_todeliver[x]['AI_producto_id'] === '14415') { raw_todeliver_ff[raw_todeliver[x]['AI_facturaf_id']]['flete'] = 1 };
				}
			}
			generate_todeliver_facturaf(raw_todeliver_ff);



			setInterval(
				async function () {
					var url_data = data_fetch({cls : "class_todeliver", mtd : 'get_todeliver'});
					var miInit = {method: 'GET',headers: {'Content-Type': 'application/json'},mode: 'cors',cache: 'default' };
					var myRequest = new Request(`attached/get/class_todeliver.php${url_data}`);
					let response = await fetch(myRequest, miInit)
					let ans_json = await response.json();	
					if (window.raw_todeliver['0']['AI_facturaf_id'] < ans_json['array_obj']['0']['AI_facturaf_id']) {
						var raw_todeliver = ans_json['array_obj'];
						var raw_todeliver_ff = new Object();
						for (var x in raw_todeliver) {
							var flete = 0;	var n_array = [];
							if (raw_todeliver[x]['AI_facturaf_id'] in raw_todeliver_ff) {
							}else{
								raw_todeliver_ff[raw_todeliver[x]['AI_facturaf_id']] = [];
								n_array = {"facturaf" : raw_todeliver[x]['TX_facturaf_numero'],"fecha" : raw_todeliver[x]['TX_facturaf_fecha'], "hora" : raw_todeliver[x]['TX_facturaf_hora'], "cliente" : raw_todeliver[x]['TX_cliente_nombre'], "flete" : 0};
								raw_todeliver_ff[raw_todeliver[x]['AI_facturaf_id']] = n_array;
							}
							if(raw_todeliver_ff[raw_todeliver[x]['AI_facturaf_id']]['flete'] !== 1){
								if (raw_todeliver[x]['AI_producto_id'] === '14415') { raw_todeliver_ff[raw_todeliver[x]['AI_facturaf_id']]['flete'] = 1 };
							}
						}
						generate_todeliver_facturaf(raw_todeliver_ff);
						
					}
				}
			,60000);





			$("#txt_todeliver_quantity").validCampoFranz(".0123456789");
			$("#txt_todeliver_quantity").on("keyup", function(e){
				if (e.which === 13) {
					$("#btn_updtodeliver").click();
				}
			})
			$("#txt_filter_todeliverff").on("keyup", function(e){
				if (e.which === 13) {
					$("#btn_filter_todeliverff").focus();
					$("#btn_filter_todeliverff").click();
				}
			})
			$("#txt_filter_todeliverff").on("blur", function(){
				this.value = (this.value).toUpperCase();
			});
			$("#btn_filter_todeliverff").on("click", function(){
				filter_todeliverff(raw_todeliver_ff,$("#txt_filter_todeliverff").val());
			});
			$("#txt_filterproduct").on("keyup", function(e){
				if(e.which === 13){ $("#btn_filter_product").click(); }
			})
			$("#txt_filter_deliveredff").on("keyup", function(e){
				if(e.which === 13){ $("#btn_filter_deliveredff").click(); }
			})
			$("#txt_filterproduct_delivered").on("keyup", function(e){
				if(e.which === 13){ $("#btn_filter_product_delivered").click(); }
			})







		});


		$( function() {
			$("#txt_filter_todeliverclient").autocomplete({
				source: "attached/get/filter_client_sell.php",
				minLength: 2,
				select: function( event, ui ) {
					var n_val = ui.item.value;
					raw_n_val = n_val.split(" | Dir:");
					ui.item.value = raw_n_val[0];
					content = '<strong>Nombre:</strong> '+ui.item.value+' <strong>RUC:</strong> '+ui.item.ruc+' <strong>Tlf.</strong> '+ui.item.telefono+' <strong>Dir.</strong> '+ui.item.direccion.substr(0,20);
					fire_recall('container_todeliver_client_recall', content)
					$.ajax({	data: {"a" : ui.item.id, "z" : 'client'},	type: "GET",	dataType: "text",	url: "attached/get/get_porentregar.php", })
					.done(function( data, textStatus, jqXHR ) { console.log("GOOD "+textStatus);
							data = JSON.parse(data);
							generate_todeliver_client(data);
						})
					.fail(function( jqXHR, textStatus, errorThrown ) {		});
				}
			});
		});
		$( function() {
			$("#txt_filter_deliveredclient").autocomplete({
				source: "attached/get/filter_client_sell.php",
				minLength: 2,
				select: function( event, ui ) {
					var n_val = ui.item.value;
					raw_n_val = n_val.split(" | Dir:");
					ui.item.value = raw_n_val[0];
					content = '<strong>Nombre:</strong> '+ui.item.value+' <strong>RUC:</strong> '+ui.item.ruc+' <strong>Tlf.</strong> '+ui.item.telefono+' <strong>Dir.</strong> '+ui.item.direccion.substr(0,20);
					fire_recall('container_delivered_client_recall', content)
					$.ajax({	data: {"a" : ui.item.id, "z" : 'client'},	type: "GET",	dataType: "text",	url: "attached/get/get_entregado.php", })
					.done(function( data, textStatus, jqXHR ) { console.log("GOOD "+textStatus);
							raw_data = JSON.parse(data);
							generate_delivered_client(raw_data);
						})
					.fail(function( jqXHR, textStatus, errorThrown ) {		});
				}
			});
		});
		$( function() {
			var dateFormat = "dd-mm-yy",
			from = $( "#txt_date_initial_deliveredff" )
			.datepicker({
				defaultDate: "+1w",
				changeMonth: true,
				numberOfMonths: 2
			})
			.on( "change", function() {
				to.datepicker( "option", "minDate", getDate( this ) );
			}),
			to = $( "#txt_date_final_deliveredff" ).datepicker({
				defaultDate: "+1w",
				changeMonth: true,
				numberOfMonths: 2
			})
			.on( "change", function() {
				from.datepicker( "option", "maxDate", getDate( this ) );
			});

			function getDate( element ) {
				var date;
				try {
					date = $.datepicker.parseDate( dateFormat, element.value );
				} catch( error ) {
					date = null;
				}
				return date;
			}
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
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" id="carousel_container">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" id="carousel_arrow">
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding"><i class="fa fa-arrow-circle-left fa-4x" id="go_left"> </i></div>
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no_padding" id="carousel_title"></div>
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding al_right"><i class="fa fa-arrow-circle-right fa-4x" id="go_right"> </i></div>
			</div>
			<div id="container_promotion" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding carousel active">
				<!-- #####################     MODAL WINDOWS add_promotion_product   ######################### -->
				<input type="hidden" id="hd_tab" name="" value="facturaf" />
				<div id="mod_set_todeliver" class="col-xs-5 col-sm-5 col-md-5 col-lg-5 display_none">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 border_1 py_7">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center">
							<h4 style="color: #d55044"><strong>Indique la Cantidad a Entregar</strong></h4>
						</div>
						<div id="div_codigo" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
							<label for="span_todeliver_codigo" class="label label_blue_sky">Codigo</label>
							<span id="span_todeliver_codigo" class="form-control bg-disabled"></span>
						</div>
						<div id="div_descripcion" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
							<label for="span_todeliver_descripcion" class="label label_blue_sky">Descripci&oacute;n</label>
							<span id="span_todeliver_descripcion" class="form-control bg-disabled"></span>
						</div>
						<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding">
							<label for="txt_todeliver_quantity" class="label label_blue_sky">Pendiente</label>
							<span id="span_todeliver_pending" class="form-control bg-disabled">0</span>
						</div>
						<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding">
							<label for="txt_todeliver_quantity" class="label label_blue_sky">Cantidad</label>
							<input type="text" id="txt_todeliver_quantity" class="form-control" name="" value="" placeholder="Cantidad">
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 pt_14">
							<button type="button" id="btn_updtodeliver" alt="" name="" class="btn btn-success" onclick="upd_todeliver(this)">Aceptar</button>
							&nbsp;
							<button type="button" name="button" class="btn btn-warning" onclick="close_modal();">Cancelar</button>
						</div>
					</div>
				</div>
				<!-- #####################     MODAL WINDOWS add_promotion_product   ######################### -->
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding bg_green" id="carousel_title">
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding">&nbsp;</div>
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no_padding al_center">
						<strong><h2>Por Entregar</h2></strong>
					</div>
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding al_right">&nbsp;</div>
				</div>
				<div id="container_tab_todeliver" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
					<ul class="nav nav-tabs">
					  <li class="active"><a data-toggle="tab" href="#todeliver_ff" onclick="set_hd_tab('facturaf')">Factura</a></li>
						<li><a data-toggle="tab" href="#todeliver_client" onclick="set_hd_tab('client')">Cliente</a></li>
						<li><a data-toggle="tab" href="#todeliver_product" onclick="set_hd_tab('product')">Producto</a></li>
					</ul>
					<div class="tab-content">
						<!-- ##############################      facturaf   ############################ -->
						<div id="todeliver_ff" class="container-fluid no_padding tab-pane fade in active " >
							<div id="container_leftside" class="col-xs-12 col-sm-12 col-md-4 col-lg-4 no_padding br_1 px_7 pt_14">
								<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no_padding">
									<input type="text" id="txt_filter_todeliverff" class="form-control" value="" placeholder="No. de Factura o Cliente">
								</div>
								<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no_padding side-btn-md px_7">
									<button type="button" id="btn_filter_todeliverff" class="btn btn-success" ><i class="fa fa-search"></i></button>
								</div>
								<div id="container_tbl_todeliverff" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
									&nbsp;
								</div>
							</div>
							<div id="container_rightside" class="col-xs-12 col-sm-12 col-md-8 col-lg-8 no_padding br_1 px_7 pt_14">
								<div id="todeliver_caption" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
									&nbsp;
								</div>
								<div id="tbl_todeliver" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_table">
									<div id="content_dhead" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_header no_padding bg-primary">
						 					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell br_1">CODIGO</div>
											<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 cell no_padding br_1">DESCRIPCION</div>
											<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding br_1">PENDIENTE</div>
											<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell no_padding">
												<button type="button" id="btn_todeliverall" name="" class="btn btn-danger display_none" onclick="set_todeliverall();">Todos</button>
											</div>
										</div>
									</div>
									<div id="content_dbody" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding al_center">
									</div>
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-primary">&nbsp;</div>
					 		 </div>
							 	<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
									<button type="button" id="btn_todeliver_save" name="" class="btn btn-success btn-lg" onclick="save_todeliverff(this.name)">Procesar</button>
								</div>
								<div id="tbl_delivered" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_table mt_7">
									<div id="content_dhead" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_header no_padding bg_red">
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 cell al_left">Entregas Previas</div>
										</div>
									</div>
									<div id="content_dbody" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
									</div>
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg_red">&nbsp;</div>
								</div>
							</div>
						</div>
						<!-- ##############################      CLIENTE   ############################ -->
						<div id="todeliver_client" class="container-fluid no_padding tab-pane fade  " >
							<div id="container_leftside" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding br_1 px_7 pt_14">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
									<label for="txt_filter_todeliverclient" class="label label_blue_sky">Buscar</label>
									<input type="text" id="txt_filter_todeliverclient" class="form-control" placeholder="Nombre del Cliente" />
								</div>
								<div id="container_todeliver_client_recall" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"></div>
								<div id="tbl_todeliver_client" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_table mt_7 no_padding">
									<div id="content_dhead" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_header no_padding bg-primary">
											<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 cell br_1">FACTURA</div>
											<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell br_1">FECHA</div>
											<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell br_1">C&Oacute;DIGO</div>
											<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 cell br_1">DESCRIPCI&Oacute;N</div>
											<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 cell br_1 px_0">PENDIENTE</div>
											<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">&nbsp;</div>
										</div>
									</div>
									<div id="todeliver_client_dbody" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 border_1 no_padding">&nbsp;</div>
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-primary">&nbsp;</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding al_center py_7">
									<button type="button" id="btn_process" name="" class="btn btn-success btn-lg" onclick="save_multiple_deliver();">Procesar</button>
								</div>
								<div id="tbl_delivered_client" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_table mt_7 px_0">
									<div id="content_dhead" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_header no_padding bg_red">
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 cell al_left">Entregas Previas</div>
										</div>
									</div>
									<div id="content_dbody" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
									</div>
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg_red">&nbsp;</div>
								</div>
							</div>
						</div>
						<!-- ########################            PRODUCTO           ########################### -->
						<div id="todeliver_product" class="container-fluid no_padding tab-pane fade  " >
							<div id="container_leftside" class="col-xs-12 col-sm-12 col-md-4 col-lg-4 no_padding br_1 px_7 pt_14">
								<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 no_padding">
									<label for="txt_filterproduct" class="label label_blue_sky">Buscar Producto</label>
									<input type="text" class="form-control" id="txt_filterproduct" value="" placeholder="Descripci&oacute;n o C&oacute;digo del Producto">
								</div>
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding side-btn-md px_7 pt_14">
									<button type="button" id="btn_filter_product" class="btn btn-success" onclick="filter_product()"><i class="fa fa-search"></i></button>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
									<div id="tbl_product" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_table mt_7 px_0">
										<div id="content_caption" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding al_left">&nbsp;</div>
										<div id="content_dhead" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_header no_padding bg-info">
												<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell al_center">CODIGO</div>
												<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 cell al_center">DESCRIPCION</div>
											</div>
										</div>
										<div id="content_dbody" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">&nbsp;</div>
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-info">&nbsp;</div>
									</div>
								</div>
							</div>
							<div id="container_rightside" class="col-xs-12 col-sm-12 col-md-8 col-lg-8 no_padding br_1 px_7 pt_14">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
									<div id="tbl_todeliver_product" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_table mt_7 px_0">
										<div id="content_dhead" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_header no_padding bg-primary">
												<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 cell al_center">CLIENTE</div>
												<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell al_center">FACTURA</div>
												<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell al_center">FECHA/HORA</div>
												<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell al_center">PENDIENTE</div>
												<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 cell al_center">&nbsp;</div>
											</div>
										</div>
										<div id="content_dbody" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">&nbsp;</div>
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-primary">&nbsp;</div>
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding al_center py_7">
									<button type="button" id="btn_process_product" name="" class="btn btn-success btn-lg" onclick="save_multiple_deliver();">Procesar</button>
								</div>
								<div id="tbl_todeliver_previus_product" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div_table mt_7 px_0">
									<div id="content_dhead" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_body no_padding">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 line_header no_padding bg_red">
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 cell al_left">Entregas Previas</div>
										</div>
									</div>
									<div id="content_dbody" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
									</div>
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg_red">&nbsp;</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="container_delivered" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding carousel">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding bg-primary" id="carousel_title">
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding">&nbsp;</div>
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no_padding al_center">
						<h2>Entregado</h2>
					</div>
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding al_right">&nbsp;</div>
				</div>
				<!-- ######################   TABS PARA ENTREGADO   ################# -->
				<div id="container_tab_delivered" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">

					<?php include 'attached/php/inc_warehouse_entregado.php'; ?>

				</div>
			</div>
		</div>
	</div>
	<!-- The actual snackbar -->
	<div id="snackbar">Some text some message..</div>

	<div id="footer">
		<?php require 'attached/php/req_footer.php'; ?>
	</div>
</div>
<script type="text/javascript">
	<?php include 'attached/php/req_footer_js.php'; ?>
</script>

</body>
</html>
