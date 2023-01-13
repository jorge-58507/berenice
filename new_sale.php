<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_sale.php';

$qry_precio = $link->prepare("SELECT TX_precio_cuatro FROM bh_precio WHERE precio_AI_producto_id = ? AND TX_precio_inactivo = '0'")or die($link->error);
$qry_product=$link->query("SELECT AI_producto_id, TX_producto_codigo, TX_producto_value, TX_producto_cantidad, TX_producto_inventariado FROM bh_producto WHERE TX_producto_activo = '0' ORDER BY TX_producto_value ASC LIMIT 10");
$raw_producto=array(); $i=0;
while ($rs_product=$qry_product->fetch_array(MYSQLI_ASSOC)) {
	$qry_precio->bind_param("i", $rs_product['AI_producto_id']); $qry_precio->execute(); $result = $qry_precio->get_result();
	$rs_precio=$result->fetch_array(MYSQLI_ASSOC);
	$raw_producto[$i]=$rs_product;
	$raw_producto[$i]['precio']=$rs_precio['TX_precio_cuatro'];
	$i++;
};
$qry_vendor=$link->query("SELECT AI_user_id, TX_user_seudonimo FROM bh_user WHERE AI_user_id = '{$_COOKIE['coo_iuser']}'");
$rs_vendor=$qry_vendor->fetch_array(MYSQLI_ASSOC);

$qry_nuevaventa = $link->query("SELECT TX_rel_nuevaventa_compuesto FROM rel_nuevaventa WHERE AI_rel_nuevaventa_id = 1")or die($link->error);
$rs_nuevaventa = $qry_nuevaventa->fetch_array();
$contenido = $rs_nuevaventa['TX_rel_nuevaventa_compuesto'];

$raw_nuevaventa = json_decode($contenido, true);
$first_sale = (!empty($raw_nuevaventa[$_COOKIE['coo_iuser']]['first_sale'])) ? $raw_nuevaventa[$_COOKIE['coo_iuser']]['first_sale'] : [];
$second_sale = (!empty($raw_nuevaventa[$_COOKIE['coo_iuser']]['second_sale'])) ? $raw_nuevaventa[$_COOKIE['coo_iuser']]['second_sale'] : [];

$qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida")or die($link->error);
$raw_medida = array();
while($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)){
	$raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
}

$qry_promocion = $link->query("SELECT AI_promocion_id, TX_promocion_descripcion, TX_promocion_componente, TX_promocion_tipo, TX_promocion_titulo FROM bh_promocion")or die($link->error);
	$raw_promocion=array();	$i=0;
while($rs_promocion = $qry_promocion->fetch_array(MYSQLI_ASSOC)){
	$raw_componente = json_decode($rs_promocion['TX_promocion_componente'], true);
	$raw_producto_id = array();	$raw_medida_id = array();	$raw_cantidad = array();
	$raw_precio = array();	$raw_impuesto = array();	$raw_descuento = array();
	foreach ($raw_componente as $key => $componente) {
		$raw_producto_id[]=$key;
		$raw_medida_id[] = $componente['medida']*1;
		$raw_cantidad[] = $componente['cantidad'];
		$raw_precio[] = $componente['precio'];
		$raw_impuesto[] = $componente['impuesto'];
		$raw_descuento[] = $componente['descuento'];
	}
	$raw_promocion[$i]['promo_titulo'] = $rs_promocion['TX_promocion_titulo'];
	$raw_promocion[$i]['promo_contenido'] = $rs_promocion['TX_promocion_descripcion'];
	$raw_promocion[$i]['promo_producto'] = json_encode($raw_producto_id);
	$raw_promocion[$i]['promo_medida'] = json_encode($raw_medida_id);
	$raw_promocion[$i]['promo_cantidad'] = json_encode($raw_cantidad);
	$raw_promocion[$i]['promo_precio'] = json_encode($raw_precio);
	$raw_promocion[$i]['promo_impuesto'] = json_encode($raw_impuesto);
	$raw_promocion[$i]['promo_descuento'] = json_encode($raw_descuento);
	$raw_promocion[$i]['promo_tipo'] = $rs_promocion['TX_promocion_tipo'];
	$i++;
}
$qry_selecto = $link->query("SELECT AI_selecto_id, selecto_AI_producto_id, TX_selecto_value, selecto_AI_medida_id, TX_selecto_cantidad, TX_selecto_status FROM bh_selecto WHERE TX_selecto_status != 0 ORDER BY TX_selecto_value ASC")or die($link->error);
$raw_selecto=array();
$raw_selecto_group = array();
while ($rs_selecto=$qry_selecto->fetch_array(MYSQLI_ASSOC)) {
	$raw_selecto[$rs_selecto['AI_selecto_id']] = $rs_selecto;
	if (!in_array($rs_selecto['TX_selecto_value'], $raw_selecto_group)) {
		$raw_selecto_group[$rs_selecto['selecto_AI_producto_id']]=$rs_selecto['TX_selecto_value'];
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>
<?php include 'attached/php/req_required.php'; ?>
<link href="attached/css/sell_css.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="attached/js/jquery.cookie.js"></script>
<script type="text/javascript" src="attached/js/sell_funct.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$(window).on('beforeunload', function(){	close_popup();	});
	$('#btn_salir').click(function(){
		setTimeout("history.back(1)",250);
	});
	$("#form_sell").keyup(function(e){
		if(e.which == 120) {	$("#btn_guardar").click();	}
	});
	$("#txt_filterproduct").keyup(function(e){
		if(e.which == 13){
			$.ajax({data: {"a" : $("#txt_filterproduct").val() }, type: "GET", dataType: "text", url: "attached/get/get_sale_product.php",})
			.done(function( data, textStatus, jqXHR ) {
				data = JSON.parse(data);
				open_product2sell(data['producto_id']);
			})
			.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
		}
	});
	// ############## CARRUSEL ################
	var raw_finded = $("#carousel_container").find("div.carousel");
	var raw_carousel = [];
	var position = 0;
	for (var x in raw_finded) {
		if (raw_finded[x]['id'] != undefined) {
			raw_carousel.push(raw_finded[x]['id']);
		}
	}
	$("#go_left").on("click", function(){
		position = (position-- <= 0) ? raw_carousel.length-1 : position--;
		str_carousel = '';
		for (var y in raw_carousel) {
			if (y != position && $('#'+raw_carousel[y]).is(":visible")) {
				$('#'+raw_carousel[y]).hide(500);
			}
		}
		$("#"+raw_carousel[position]).show(1000);
	})
	$("#go_right").on("click", function(){
		position = (position++ >= raw_carousel.length-1) ? 0 : position++;
		str_carousel = '';
		for (var y in raw_carousel) {
			if (y != position && $('#'+raw_carousel[y]).is(":visible")) {
				$('#'+raw_carousel[y]).hide(500);
			}
		}
		$("#"+raw_carousel[position]).show(1000);
	})

	// ######################    FIN CARRUSEL ####################

	generate_tbl_selecto('');

	$("#btn_promotion").on("click", function(){
		$("#container_tbl_product_favorite").toggleClass('in');
	})
	$("#btn_favorite").on("click", function(){
		$("#container_tbl_product_promotion").toggleClass('in');
	})

	$("input[name=r_limit]").on("change",function(){
		$("#txt_filterproduct").keyup();
	})
	var observation_val = '';
	$("#txt_observation").on("keyup", function(){
		if(this.value.length >= '5'){
			this.value = observation_val;
		}else{
			observation_val = this.value;
		}
	});
	$("#txt_observation").validCampoFranz('abcdefghijklmnopqrstuvwxyz .0123456789-/');
	$("#txt_filterclient_first").validCampoFranz('P0123456789-');
	$("#txt_filterclient_second").validCampoFranz('P0123456789-');
	$("#btn_report").click(function(){
		var str = prompt("Ingrese los datos",$("#txt_filterproduct").val());
		str = str.replace("#","laremun");
		$.ajax({data: {"a" : str}, type: "GET", dataType: "text", url: "attached/get/plus_stock_report.php",})
		.done(function( data, textStatus, jqXHR ) {
		var alert_bootstrap = "<div class='alert alert-info alert-dismissable fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Atenci&oacute;n</strong> "+data+"</div>";
			$("#container_filterproduct").html($("#container_filterproduct").html()+alert_bootstrap);
			setTimeout("$('.close').click()", 3000);
		})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
	});

	$("#container_client_recall").css("display","none");

	$("#container_username label").on("click", function(){
		popup = window.open("popup_loginadmin.php?z=start_admin.php", "popup_loginadmin", 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=no,width=425,height=420');
	})

});

		function generate_tbl_selecto(filter){
			var json_selecto = '<?php echo json_encode($raw_selecto); ?>';
			var array_selecto = JSON.parse(json_selecto);
			content = '';
			if (filter != '') {
				for (var x in array_selecto) {
					if(array_selecto[x]['selecto_AI_producto_id'] === filter){
						html_btn = (array_selecto[x]['TX_selecto_status'] == 1) ? `<button type="button" class="btn btn-danger btn-xs" onclick="set_selecto_status(${x},2)">X</button>` : `<button type="button" class="btn btn-info btn-xs" onclick="set_selecto_status(${x},1)">Ver</button>`;
						content += `<tr>
													<td>${array_selecto[x]['TX_selecto_value']}</td>
													<td>${array_selecto[x]['TX_selecto_cantidad']}</td>
													<td>${html_btn}</td>
												</tr>`;
					}
				}
			} else {
				for (var x in array_selecto) {
					html_btn = (array_selecto[x]['TX_selecto_status'] == 1) ? `<button type="button" class="btn btn-danger btn-xs side_btn_xs" onclick="set_selecto_status(${x},2)">X</button>` : `<button type="button" class="btn btn-info btn-xs" onclick="set_selecto_status(${x},1)">Ver</button>`;
					content += `<tr>
												<td>${array_selecto[x]['TX_selecto_value']}</td>
												<td>${array_selecto[x]['TX_selecto_cantidad']}</td>
												<td>${html_btn}</td>
											</tr>`;
				}
			}
			$("#tbl_product_selected tbody").html(content);
		}

		function generate_tbl_nuevaventa(data,activo){
			var json_medida = '<?php echo json_encode($raw_medida); ?>';
			var array_medida =	JSON.parse(json_medida);
			var nuevaventa = (data[<?php echo $_COOKIE['coo_iuser']; ?>][activo] != undefined) ? data[<?php echo $_COOKIE['coo_iuser']; ?>][activo] : {};
			if(Object.keys(nuevaventa).length > 0){
				var content = '';
				var raw_datoventa = [];
				for (var x in nuevaventa) {
					line = {
						"cantidad": nuevaventa[x]['cantidad'],
						"precio": nuevaventa[x]['precio'],
						"descuento": nuevaventa[x]['descuento'],
						"alicuota": nuevaventa[x]['impuesto']
					};
					raw_datoventa.push(line);

					var descuento = (nuevaventa[x]['precio']*nuevaventa[x]['descuento'])/100;
					descuento = descuento.toFixed(2); descuento = parseFloat(descuento);
					var precio_descuento = nuevaventa[x]['precio']-descuento;
					var impuesto = (precio_descuento*nuevaventa[x]['impuesto'])/100;
					var precio_unitario = precio_descuento+impuesto;
							precio_unitario = Math.round10(precio_unitario, -4);
					var subtotal = nuevaventa[x]['cantidad']*precio_unitario;

					style_promotion = (nuevaventa[x]['promocion'] > 0 ) ? 'style="color: #f86e6e; background-color: #f2ffef; text-shadow: 0.5px 0.5px #f37e7e80;"' : '';
					fire_promotion = (nuevaventa[x]['promocion'] > 0 ) ? '' : '';
					content += '<tr '+style_promotion+'><td>'+nuevaventa[x]['codigo']+'</td><td onclick="upd_descripcion_nuevaventa('+x+',\''+replace_regular_character(nuevaventa[x]['descripcion'])+'\')">'+fire_promotion+replace_special_character(nuevaventa[x]['descripcion'])+'</td><td>'+array_medida[nuevaventa[x]['medida']]+'</td><td onclick="upd_unidades_nuevaventa('+x+');">'+nuevaventa[x]['cantidad']+'</td><td  onclick="upd_precio_nuevaventa('+x+');">'+nuevaventa[x]['precio']+'</td><td>'+descuento.toFixed(3)+'</td><td>'+impuesto.toFixed(3)+'</td><td>'+precio_unitario.toFixed(4)+'</td><td>'+subtotal.toFixed(3)+'</td><td><button type="button" id="btn_delproduct" class="btn btn-danger btn-sm" onclick="del_nuevaventa('+x+');"><strong>X</strong></button></td></tr>';
				}
  			var raw_total = calcular_factura(raw_datoventa);


				activo = activo.replace("_sale","");
				$("#tbl_product2sell_"+activo+" tbody").html(content);
				$("#span_discount_"+activo).html(raw_total['ttl_descuento']);
				$("#span_taxeable_"+activo).html(raw_total['base_impo']);
				$("#span_untaxeable_"+activo).html(raw_total['base_noimpo']);
				$("#span_itbm_"+activo).html(raw_total['ttl_impuesto']);
				$("#span_total_"+activo).html(raw_total['total']);
			}else{
				content=content+'<tr><td colspan="10">&nbsp;</td></tr>';
				activo = activo.replace("_sale","");
				$("#tbl_product2sell_"+activo+" tbody").html(content);
				$("#span_discount_"+activo).html(0.00);
				$("#span_taxeable_"+activo).html(0.00);
				$("#span_untaxeable_"+activo).html(0.00);
				$("#span_itbm_"+activo).html(0.00);
				$("#span_total_"+activo).html(0.00);
			}
		}

		function generate_tbl_favorito(data,activo){
			var array_data = JSON.parse(data);
			var content = '';
			if(Object.keys(array_data).length > 0){
				for (var x in array_data) {
					content +=	`<tr onclick="open_product2sell(${array_data[x]['datoventa_AI_producto_id']});"><td>${array_data[x]['TX_datoventa_descripcion']}</td><td>${array_data[x]['TX_datoventa_precio']}</td></tr>`;
				}
			}else{
		  	content = `<tr><td colspan='2'> </td></tr>`;
			}
			$("#tbl_product_favorite tbody").html(content);
		}

		function insert_multiple_product2sell(raw_producto,raw_medida,raw_cantidad,raw_precio,raw_impuesto,raw_descuento,promotion_type){
			var activo = $(".tab-pane.active").attr("id");
			var multiplo = prompt("Introduzca la cantidad");
			multiplo = val_intw2dec(multiplo);
			if(!val_intwdec(multiplo)){ return false; }
			if(raw_precio.indexOf(0.00)){
				$.ajax({	data: {"a" : raw_producto, "b" : raw_precio, "c" : raw_descuento, "d" : raw_impuesto, "e" : activo, "f" : raw_cantidad, "g" : raw_medida, "h" : promotion_type, "i" : multiplo, "z" : 'plus_multiple' }, type: "GET", dataType: "text", url: "attached/php/method_nuevaventa.php",	})
				.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
					if(data){
						data = JSON.parse(data);
						generate_tbl_nuevaventa(data,activo);
						activo=activo.replace("_sale","");
						$("#btn_guardar, #btn_facturar").css("display","initial");
						$("#txt_filterproduct").focus();
					}
				})
				.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
		 	}
		}

		function set_selecto_status (selecto_id,status){
			$.ajax({	data: {"a" : selecto_id, "b" : status}, type: "GET", dataType: "text", url: "attached/get/upd_selecto_status.php",	})
			.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
				if(data){	generate_tbl_selecto('');	}
			})
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

	<div class="container-fluid" >
		<div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 bg-success" id="div_title"><h2>Nueva Cotizaci&oacute;n</h2></div>
	</div>
	<ul class="nav nav-tabs">
	  <li class="active"><a data-toggle="tab" href="#first_sale">1<sup>ero</sup></a></li>
	  <li><a data-toggle="tab" href="#second_sale">2<sup>do</sup></a></li>
	</ul>

<div class="tab-content">

<div id="first_sale" class="container-fluid no_padding tab-pane fade in active" >
	<div id="container_complementary" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_txtdate" class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
	    	<label class="label label_blue_sky" for="txt_date_first">Fecha:</label>
		    <input type="text" class="form-control" alt="" id="txt_date_first" name="txt_date_first" readonly="readonly" value="<?php echo date('d-m-Y'); ?>" />
	  </div>
		<div id="container_txtvendedor" class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
	  	<label class="label label_blue_sky" for="txt_vendedor">Vendedor:</label>
	    <input type="text" class="form-control" alt="<?php echo $rs_vendor['AI_user_id']; ?>" id="txt_vendedor" name="txt_vendedor" readonly="readonly"  value="<?php echo $rs_vendor['TX_user_seudonimo']; ?>" />
	  </div>
	</div>
	<div id="container_client" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_txtfilterclient_first" class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
			<label class="label label_blue_sky" for="txt_filterclient_first">Cliente:</label>
			<input type="text" class="form-control" alt="1" id="txt_filterclient_first" placeholder="CONTADO" name="txt_filterclient_first" onkeyup="unset_filterclient(event)" />
    	</div>
		<div id="container_btnaddclient" class="col-xs-1 col-sm-1 col-md-1 col-lg-1 side-btn-md-label">
			<button type="button" id="btn_addclient_first" onclick="add_client()" class="btn btn-success"><strong><span class="glyphicon glyphicon-wrench"></span></strong></button>
		</div>
		<div id="container_client_recall_first" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

		</div>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_txtobservation" class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
	  	<label class="label label_blue_sky" for="txt_observation_first">Observaciones:</label>
			<input type="text" class="form-control" id="txt_observation_first" name="txt_observation_first" />
		</div>
		<div id="container_btnrefreshtblproduct2sale_first" class="col-xs-1 col-sm-1 col-md-1 col-lg-1 side-btn-md-label">
			<button type="button" id="btn_refresh_tblproduct2sale" onclick="refresh_tblproduct2sale()" class="btn btn-info" title="Refrescar Tabla"><strong><span class="glyphicon glyphicon-refresh"></span></strong></button>
		</div>
	</div>
	<div id="container_product2sell" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_tblproduct2sale" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <table id="tbl_product2sell_first" class="table table-bordered table-hover ">
	        <caption>Lista de Productos para la Venta</caption>
	        <thead class="bg_green">
						<tr>
							<th>Codigo</th>
							<th>Producto</th>
							<th>Medida</th>
							<th>Cantidad</th>
							<th>Precio</th>
							<th>Desc</th>
							<th>Imp.</th>
							<th>P. Uni.</th>
							<th>SubTotal</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="10"></td>
						</tr>
					</tbody>
					<tfoot class="bg_green">
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td class=" al_center">
								<strong>Ttl No Impo: </strong> <br /><span id="span_untaxeable_first"></span>
							</td>
							<td class=" al_center">
								<strong>Ttl Impo: </strong> <br /><span id="span_taxeable_first"></span>
							</td>
							<td class=" al_center">
								<strong>T. Desc: </strong> <br /><span id="span_discount_first"></span>
							</td>
							<td class=" al_center">
								<strong>T. Imp: </strong> <br /><span id="span_itbm_first"></span>
							</td>
							<td></td>
							<td class=" al_center">
								<strong>Total: </strong> <br /><span id="span_total_first"></span>
							</td>
							<td>  </td>
						</tr>
		    	</tfoot>
	    	</table>
	    </div>
	</div>
</div>
<!-- ########################          SECOND SALE           ########################## -->

<div id="second_sale" class="container-fluid no_padding tab-pane fade">
	<div id="container_complementary" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_txtdate" class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
    	<label class="label label_blue_sky" for="txt_date_second">Fecha:</label>
	    <input type="text" class="form-control" alt="" id="txt_date_second" name="txt_date_second" readonly="readonly" value="<?php echo date('d-m-Y'); ?>" />
		</div>
		<div id="container_txtvendedor" class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
	  	<label class="label label_blue_sky" for="txt_vendedor">Vendedor:</label>
	    <input type="text" class="form-control" alt="<?php echo $rs_vendor['AI_user_id']; ?>" id="txt_vendedor" name="txt_vendedor" readonly="readonly"  value="<?php echo $rs_vendor['TX_user_seudonimo']; ?>" />
	  </div>
	</div>
	<div id="container_client" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_txtfilterclient_second" class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
    	<label class="label label_blue_sky" for="txt_filterclient_second">Cliente:</label>
	    <input type="text" class="form-control" alt="1" id="txt_filterclient_second" placeholder="CONTADO" name="txt_filterclient_second" onkeyup="unset_filterclient(event)" />
    </div>
		<div id="container_btnaddclientsecond" class="col-xs-1 col-sm-1 col-md-1 col-lg-1 side-btn-md-label">
			<button type="button" id="btn_addclient_second" onclick="add_client()" class="btn btn-success"><strong><span class="glyphicon glyphicon-wrench"></span></strong></button>
		</div>
		<div id="container_client_recall_second" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

		</div>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_txtobservation_second" class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
	  	<label class="label label_blue_sky" for="txt_observation_second">Observaciones:</label>
			<input type="text" class="form-control" id="txt_observation_second" name="txt_observation_second" />
		</div>
		<div id="container_btnrefreshtblproduct2sale_second" class="col-xs-1 col-sm-1 col-md-1 col-lg-1 side-btn-md-label">
			<button type="button" id="btn_refresh_tblproduct2sale_second" onclick="refresh_tblproduct2sale()" class="btn btn-info" title="Refrescar Tabla"><strong><span class="glyphicon glyphicon-refresh"></span></strong></button>
		</div>
	</div>

		<div id="container_product2sell" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div id="container_tblproduct2sale" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <table id="tbl_product2sell_second" class="table table-bordered table-hover ">
	        <caption>Lista de Productos para la Venta</caption>
	        <thead class="bg_red">
            <tr>
                <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center">Codigo</th>
                <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4 al_center">Producto</th>
                <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center">Medida</th>
                <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center">Cantidad</th>
                <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center">Precio</th>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center">Desc</th>
                <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center">Imp.</th>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center">P. Uni.</th>
                <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center">SubTotal</th>
                <th>&nbsp;</th>
            </tr>
	        </thead>
	        <tbody>
					</tbody>
					<tfoot class="bg_red">
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td class=" al_center">
								<strong>Ttl No Impo: </strong> <br /><span id="span_untaxeable_second"></span>
							</td>
							<td class=" al_center">
								<strong>Ttl Impo: </strong> <br /><span id="span_taxeable_second"></span>
							</td>
							<td class=" al_center">
								<strong>T. Desc: </strong> <br /><span id="span_discount_second"></span>
							</td>
							<td class=" al_center">
								<strong>T. Imp: </strong> <br /><span id="span_itbm_second"></span>
							</td>
							<td></td>
							<td class=" al_center">
								<strong>Total: </strong> <br /><span id="span_total_second"></span>
							</td>
							<td>  </td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>



	<div id="container_product_list" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_filterproduct" class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
			<label class="label label_blue_sky" for="txt_filterproduct">Buscar:</label>
	    <input type="text" class="form-control" id="txt_filterproduct" name="txt_filterproduct" autocomplete="off" onkeyup="filter_product_sell(this);" autofocus />
		</div>
		<div id="container_limit" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
			<label class="label label_blue_sky" for="txt_rlimit">Mostrar:</label><br />
			<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="20"  checked="checked" /> 20</label>
			<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="50" /> 50</label>
			<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="100" /> 100</label>
		</div>
		<div id="container_report" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
			<button type="button" id="btn_report" class="btn btn-warning btn-sm">Reportar</button>
	  </div>
	  <div id="container_selproduct" class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
	    <table id="tbl_product" class="table table-bordered table-hover table-striped">
				<caption>Lista de Productos:</caption>
				<thead class="bg-primary">
					<tr>
						<th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Codigo</th>
						<th class="col-xs-6 col-sm-6 col-md-8 col-lg-8">Nombre</th>
						<th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Cantidad</th>
						<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Precio</th>
					</tr>
				</thead>
				<tfoot class="bg-primary">
					<tr>	<td colspan="4"> </td>	</tr>
				</tfoot>
				<tbody>
					<tr>
						<td colspan="4">  </td>
					</tr>
				</tbody>
	    </table>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 no_padding">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" id="carousel_container">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" id="carousel_arrow">
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding">
						<h4 class="" id="go_left"><span class="glyphicon glyphicon-chevron-left"></span></h4>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no_padding" id="carousel_title">
														<!-- TITULO -->
					</div>
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding al_right">
						<h4 class="" id="go_right"><span class="glyphicon glyphicon-chevron-right"></span></h4>
					</div>
				</div>
				<!--    CIELO RASO -->
				<div id="container_ceiling" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding carousel active">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding bg-danger" id="carousel_title">
						<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding">&nbsp;</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no_padding al_center">
							<h4>Cielo raso</h4>
						</div>
						<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding al_right">&nbsp;</div>
					</div>
					<div id="container_tbl_product_favorite" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
						<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 no_padding">
							<label class="label label_blue_sky" for="txt_filterproduct">Metraje:</label>
							<input type="text" class="form-control" id="txt_metraje" name="txt_filterproduct" autocomplete="off" />
						</div>
						<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 px_7 py_0">
							<button type="button" id="btn_ceiling_exe" name="button" class="btn btn-success btn_squared_md mt_14" onclick="calculate_ceiling()">Ver</button>
						</div>
						<table id='tbl_ceiling' class="table table-condensed table-hover table-bordered">
							<thead>
								<tr>
									<th>Descripcion</th>
									<th>2X2</th>
									<th>2X4</th>
								</tr>
							</thead>
							<tbody>
								<tr><td class="al_center">Laminas</td><td colspan="2"></td></tr>
								<tr><td class="al_center">T 12</td><td colspan="2"></td></tr>
								<tr><td class="al_center">T 4</td><td colspan="2"></td></tr>
								<tr><td class="al_center">Ang.12</td><td colspan="2"></td></tr>
								<tr><td class="al_center">Clavo</td><td colspan="2"></td></tr>
							</tbody>
							<tfoot class="bg-danger">
								<tr><td colspan="3"></td></tr>
							</tfoot>
						</table>
					</div>
				</div>
				<!-- PROMOCIONES -->
				<div id="container_promotion" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding carousel ">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding bg_green" id="carousel_title">
						<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding">&nbsp;</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no_padding al_center">
							<strong><h4>PROMOCIONES</h4></strong>
						</div>
						<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding al_right">&nbsp;</div>
					</div>
					<div id="container_tbl_product_promotion" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
						<table id='tbl_product_promotion' class="table table-condensed table-hover table-bordered">
			 				<tbody>
								<?php 
								if(count($raw_promocion) > 0){
									foreach ($raw_promocion as $key => $value) {?>
			 							<tr onclick='insert_multiple_product2sell(<?php echo $value['promo_producto'].",".$value['promo_medida'].",".$value['promo_cantidad'].",".$value['promo_precio'].",".$value['promo_impuesto'].",".$value['promo_descuento'].",".$value['promo_tipo']; ?>)'><td style="font-weight:bolder; cursor:pointer;"><?php echo $value['promo_titulo'];?></td></tr>
										<tr><td>-<?php echo $value['promo_contenido'];?></td></tr>
										<?php 					
									}
								}else{ ?>
									<tr><td></td></tr><?php				
								} ?>
			 				</tbody>
			 				<tfoot class="bg-success">
			 					<tr><td colspan="2"></td></tr>
			 				</tfoot>
			 			</table>
					</div>
				</div>
				<div id="container_favorite" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding carousel">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding bg-info" id="carousel_title">
						<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding">&nbsp;</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no_padding">
							<h4>FAVORITOS</h4>
						</div>
						<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding al_right">&nbsp;</div>
					</div>
					<div id="container_tbl_product_favorite" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
							<label class="label label-info" for="txt_filterproduct">Buscar:</label>
					    <input type="text" class="form-control" id="txt_filterproduct" name="txt_filterproduct" autocomplete="off" onkeyup=""/>
						</div>
						<table id='tbl_product_favorite' class="table table-condensed table-hover table-bordered">
							<tbody>
								<tr><td colspan="2" rowspan="10"> </td></tr>
							</tbody>
							<tfoot class="bg-info">
								<tr><td colspan="2"></td></tr>
							</tfoot>
						</table>
					</div>
				</div>
				<div id="container_selected" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding carousel">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding bg-warning" id="carousel_title">
						<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding">&nbsp;</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no_padding">
							<h4>SELECCIONADOS</h4>
						</div>
						<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding al_right">&nbsp;</div>
					</div>
					<div id="container_tbl_product_selected" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
							<label class="label label-warning" for="txt_filterproduct">Filtrar:</label>
							<select class="form-control" name="" onchange="generate_tbl_selecto(this.value)">
								<option value=''>Seleccione</option><?php 					
								foreach ($raw_selecto_group as $key => $selecto_group) {
									echo "<option value='$key'>{$r_function->replace_special_character($selecto_group)}</option>";
								}	?>
							</select>
						</div>
						<table id='tbl_product_selected' class="table table-condensed table-hover table-bordered">
							<thead class="bg-warning">
								<tr><th colspan="3"></th></tr>
							</thead>
							<tbody>
								<tr><td colspan="2" rowspan="10"> </td></tr>
							</tbody>
							<tfoot class="bg-warning">
								<tr><td colspan="3"></td></tr>
							</tfoot>
						</table>
					</div>
				</div>
				<!-- INGRESO MULTIPLE -->
				<div id="container_grouped" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding carousel">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding bg-primary" id="carousel_title">
						<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding">&nbsp;</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no_padding al_center">
							<h4>Agrupados</h4>
						</div>
						<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding al_right">&nbsp;</div>
					</div>
					<div id="container_tbl_product_favorite" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
						<table id='tbl_grouped' class="table table-condensed table-hover table-bordered table-stripped">
							<thead>
								<tr>
									<th>Descripcion</th>
								</tr>
							</thead>
							<tbody>
								<tr onclick='insert_herraje_tina();'>
									<td class="align_center">HERRAJE TINA</td>
								</tr>
								<tr onclick='insert_herraje_inodoro();'>
									<td class="align_center">HERRAJE inodoro</td>
								</tr>
							</tbody>
							<tfoot class="bg-primary">
								<tr><td colspan="2"></td></tr>
							</tfoot>
						</table>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <button type="button" id="btn_guardar" name="ACTIVA" onclick="save_sale('ACTIVA')"class="btn btn-primary">Guardar</button>
    &nbsp;&nbsp;&nbsp;
    <button type="button" id="btn_salir" class="btn btn-warning">Volver</button>
	</div>
	<div id="snackbar"></div>
</div>

</form>
</div>


<div id="footer">
	<?php require 'attached/php/req_footer.php'; ?>
</div>
</div>
<script type="text/javascript">
	<?php include 'attached/php/req_footer_js.php'; ?>
	var raw_nuevaventa = <?php echo json_encode($raw_nuevaventa); ?>;
	generate_tbl_nuevaventa(raw_nuevaventa,'second_sale');
	generate_tbl_nuevaventa(raw_nuevaventa,'first_sale');
</script>
</body>
</html>
<?php $link->close(); ?>

