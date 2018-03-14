<?php
require 'bh_conexion.php';
$link=conexion();
date_default_timezone_set('America/Panama');
require 'attached/php/req_login_sale.php';

function checkfacturaventa($numero){
	$link=conexion();
	$qry_checkfacturaventa=$link->query("SELECT AI_facturaventa_id FROM bh_facturaventa WHERE TX_facturaventa_numero = '$numero'")or die($link->error());
	$nr_checkfacturaventa=$qry_checkfacturaventa->num_rows;
	$link->close();
	if($nr_checkfacturaventa > 0){
		return sumarfacturaventa($numero);
	}else{
		return $numero;
	}
}
function sumarfacturaventa($numero){
		return checkfacturaventa($numero+1);
}

$qry_precio = $link->prepare("SELECT TX_precio_cuatro FROM bh_precio WHERE precio_AI_producto_id = ? AND TX_precio_inactivo = '0'")or die($link->error);
$qry_letra = $link->prepare("SELECT bh_letra.TX_letra_value FROM (bh_letra INNER JOIN bh_producto ON bh_letra.AI_letra_id = bh_producto.producto_AI_letra_id) WHERE bh_producto.AI_producto_id = ? ")or die($link->error);

$qry_facturaventa_numero=$link->query("SELECT AI_facturaventa_id, TX_facturaventa_numero FROM bh_facturaventa ORDER BY AI_facturaventa_id DESC LIMIT 1")or die($link->error);
$rs_facturaventa_numero=$qry_facturaventa_numero->fetch_array();
$number = $rs_facturaventa_numero['TX_facturaventa_numero'];
$number=checkfacturaventa($number);

$qry_product=$link->query("SELECT AI_producto_id, TX_producto_codigo, TX_producto_value, TX_producto_cantidad FROM bh_producto WHERE TX_producto_activo = '0' ORDER BY TX_producto_value ASC LIMIT 10");
$raw_producto=array(); $i=0;
while ($rs_product=$qry_product->fetch_array(MYSQLI_ASSOC)) {
	$qry_precio->bind_param("i", $rs_product['AI_producto_id']); $qry_precio->execute(); $result = $qry_precio->get_result();
	$rs_precio=$result->fetch_array(MYSQLI_ASSOC);
	$qry_letra->bind_param("i", $rs_product['AI_producto_id']); $qry_letra->execute(); $result = $qry_letra->get_result();
	$rs_letra=$result->fetch_array(MYSQLI_ASSOC);

	$raw_producto[$i]=$rs_product;
	$raw_producto[$i]['precio']=$rs_precio['TX_precio_cuatro'];
	$raw_producto[$i]['letra']=(!empty($rs_letra['TX_letra_value'])) ? $rs_letra['TX_letra_value'] :  '';
	$i++;
};

$qry_vendor=$link->query("SELECT AI_user_id, TX_user_seudonimo FROM bh_user WHERE AI_user_id = '{$_COOKIE['coo_iuser']}'");
$rs_vendor=$qry_vendor->fetch_array(MYSQLI_ASSOC);

$file = fopen("nva_venta.txt", "r");
$contenido = fgets($file);
fclose($file);
$raw_nuevaventa = json_decode($contenido, true);

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
<link href="attached/css/sell_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/jquery.cookie.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/sell_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>


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

	$(window).on('beforeunload', function(){
		close_popup();
	});

	$('#txt_filterproduct').focus();

	$("#btn_sale").click(function(){
		window.location="sale.php";
	});
	$("#btn_stock").click(function(){
		window.location="stock.php";
	});

	$('#btn_salir').click(function(){
		setTimeout("history.back(1)",250);
	});

	$("#form_sell").keyup(function(e){
		if(e.which == 120) {
			$("#btn_guardar").click();
		}
	});
	$("#txt_filterproduct").keyup(function(e){
		if(e.which == 13){
			setTimeout( function(){ $("#tbl_product tbody tr:first").click(); },2000);
		}
	});
	$("input[name=r_limit]").on("change",function(){
		$("#txt_filterproduct").keyup();
	})
	var observation_val = '';
	$("#txt_observation").on("keyup", function(){
		this.value = this.value.toUpperCase();
		if(this.value.length >= '71'){
			this.value = observation_val;
		}else{
			observation_val = this.value;
		}
	});
	$("#txt_observation").validCampoFranz('abcdefghijklmnopqrstuvwxyz .0123456789-/');
	$("#txt_filterclient").validCampoFranz('abcdefghijklmnopqrstuvwxyzñ .0123456789-/&');
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

	var rep_user = $("#lbl_user").text().replace("Coticemos: ","");
	$("#lbl_user").addClass("zoomed")
	setTimeout(function(){
		$("#lbl_user").text(rep_user);
		$("#lbl_user").removeClass("zoomed");
	}, 1000);

});

function generate_tbl_nuevaventa(data,activo){
	var nuevaventa = data[<?php echo $_COOKIE['coo_iuser']; ?>][activo];
	var total_itbm=0; var total_descuento=0; var total=0;
	if(Object.keys(nuevaventa).length > 0){
		var content = '';
		for (var x in nuevaventa) {
			var descuento = (nuevaventa[x]['precio']*nuevaventa[x]['descuento'])/100;
			var precio_descuento = nuevaventa[x]['precio']-descuento;
			var impuesto = (precio_descuento*nuevaventa[x]['impuesto'])/100;
			var precio_unitario = precio_descuento+impuesto;
			var subtotal = nuevaventa[x]['cantidad']*precio_unitario;

			total_itbm += impuesto*nuevaventa[x]['cantidad'];
			total_descuento += descuento*nuevaventa[x]['cantidad'];
			total += subtotal;

			content=content+'<tr><td>'+nuevaventa[x]['codigo']+'</td><td onclick="upd_descripcion_nuevaventa('+x+')">'+nuevaventa[x]['descripcion']+'</td><td>'+nuevaventa[x]['medida']+'</td><td onclick="upd_unidades_nuevaventa('+x+');">'+nuevaventa[x]['cantidad']+'</td><td  onclick="upd_precio_nuevaventa('+x+');">'+nuevaventa[x]['precio']+'</td><td>'+descuento.toFixed(2)+'</td><td>'+impuesto.toFixed(2)+'</td><td>'+precio_unitario.toFixed(2)+'</td><td>'+subtotal.toFixed(2)+'</td><td><button type="button" id="btn_delproduct" class="btn btn-danger btn-sm" onclick="del_nuevaventa('+x+');"><strong>X</strong></button></td></tr>';
		}
		activo = activo.replace("_sale","");

		$("#tbl_product2sell_"+activo+" tbody").html(content);
		$("#span_discount_"+activo).html(total_descuento.toFixed(2));
		$("#span_itbm_"+activo).html(total_itbm.toFixed(2));
		$("#span_total_"+activo).html(total.toFixed(2));
	}else{
		content=content+'<tr><td colspan="9"> </td></tr>';
		activo = activo.replace("_sale","");
		$("#tbl_product2sell_"+activo+" tbody").html(content);
		$("#span_discount_"+activo).html(total_descuento.toFixed(2));
		$("#span_itbm_"+activo).html(total_itbm.toFixed(2));
		$("#span_total_"+activo).html(total.toFixed(2));
	}
}


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
        Bienvenido: <label id="lbl_user" class="bg-primary">
         <?php echo "Coticemos: ".$rs_checklogin['TX_user_seudonimo']; ?>
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
<form action="sale.php" method="post" name="form_sell"  id="form_sell">

<div class="container-fluid" >
	<div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 bg-success" id="div_title"><h2>Nueva Cotizaci&oacute;n</h2></div>
</div>
<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#first_sale">1&deg;</a></li>
  <li><a data-toggle="tab" href="#second_sale">2&deg;</a></li>
</ul>

<div class="tab-content">

<div id="first_sale" class="container-fluid no_padding tab-pane fade in active" >
	<div id="container_complementary" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_txtdate" class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
	    	<label for="txt_date_first">Fecha:</label>
		    <input type="text" class="form-control" alt="" id="txt_date_first" name="txt_date_first" readonly="readonly" value="<?php echo date('d-m-Y'); ?>" />
	  </div>
		<div id="container_txtnumero" class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
	  	<label for="txt_numero">Cotización N&deg;:</label>
	    <span class="form-control bg-disabled"><?php echo $number; ?></span>
	    <input type="hidden" class="form-control" alt="" id="txt_numero" name="txt_numero" readonly="readonly" value="" />
	  </div>
		<div id="container_txtvendedor" class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
	  	<label for="txt_vendedor">Vendedor:</label>
	    <input type="text" class="form-control" alt="<?php echo $rs_vendor['AI_user_id']; ?>" id="txt_vendedor" name="txt_vendedor" readonly="readonly"  value="<?php echo $rs_vendor['TX_user_seudonimo']; ?>" />
	  </div>
	</div>
	<div id="container_client" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_txtfilterclient_first" class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
    	<label for="txt_filterclient_first">Cliente:</label>
	    <input type="text" class="form-control" alt="1" id="txt_filterclient_first" placeholder="CONTADO" name="txt_filterclient_first" onkeyup="unset_filterclient(event)" />
    </div>
		<div id="container_btnaddclient" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
			<button type="button" id="btn_addclient_first" onclick="add_client()" class="btn btn-success btn_md"><strong><i class="fa fa-wrench" aria-hidden="true"></i></strong></button>
		</div>
		<div id="container_client_recall_first" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

		</div>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_txtobservation" class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
	  	<label for="txt_observation_first">Observaciones:</label>
			<input type="text" class="form-control" id="txt_observation_first" name="txt_observation_first" />
		</div>
		<div id="container_btnrefreshtblproduct2sale_first" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
			<button type="button" id="btn_refresh_tblproduct2sale" onclick="refresh_tblproduct2sale()" class="btn btn-info btn_md" title="Refrescar Tabla"><strong><i class="fa fa-refresh fa-spin fa-1x fa-fw"></i><span class="sr-only"></span></strong></button>
		</div>
	</div>
	<div id="container_product2sell" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_tblproduct2sale" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <table id="tbl_product2sell_first" class="table table-bordered table-hover ">
	        <caption>Lista de Productos para la Venta</caption>
	        <thead class="bg_green">
	            <tr>
	                <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Codigo</th>
	                <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Producto</th>
	                <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Medida</th>
	                <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
	                <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Precio</th>
									<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Desc</th>
	                <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Imp.</th>
									<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">P. Uni.</th>
	                <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">SubTotal</th>
	                <th></th>
	            </tr>
	        </thead>
					<tbody>
<?php 			$total_itbm = 0;	$total_descuento = 0;	$sub_total = 0;
						if(!empty($raw_nuevaventa[$_COOKIE['coo_iuser']]['first_sale'])){
							$nuevaventa_first=$raw_nuevaventa[$_COOKIE['coo_iuser']]['first_sale'];
							foreach ($nuevaventa_first as $key => $rs_nuevaventa) {
								$descuento = (($rs_nuevaventa['descuento']*$rs_nuevaventa['precio'])/100);
								$precio_descuento = ($rs_nuevaventa['precio']-$descuento);
								$impuesto = (($rs_nuevaventa['impuesto']*$precio_descuento)/100);
								$precio_unitario = round($precio_descuento+$impuesto,2);
								$precio_total = ($rs_nuevaventa['cantidad']*($precio_unitario));

								$total_itbm += $rs_nuevaventa['cantidad']*$impuesto;
								$total_descuento += $rs_nuevaventa['cantidad']*$descuento;
								$sub_total += $rs_nuevaventa['cantidad']*$rs_nuevaventa['precio'];
?>
									<tr>
				            <td><?php echo $rs_nuevaventa['codigo']; ?></td>
				            <td onclick="upd_descripcion_nuevaventa(<?php echo $key; ?>)"><?php echo $rs_nuevaventa['descripcion']; ?></td>
				            <td><?php echo $rs_nuevaventa['medida']; ?></td>
				            <td onclick="upd_unidades_nuevaventa(<?php echo $key ?>);"><?php echo $rs_nuevaventa['cantidad']; ?></td>
				            <td onclick="upd_precio_nuevaventa(<?php echo $key; ?>);"><?php echo number_format($rs_nuevaventa['precio'],2); ?></td>
										<td><?php echo number_format($descuento,2); ?></td>
				            <td><?php echo number_format($impuesto,2); ?></td>
										<td><?php echo number_format($precio_unitario,2); ?></td>
				            <td><?php echo number_format($precio_total,2); ?></td>
				            <td>
				            <center>
				            <button type="button" id="btn_delproduct" class="btn btn-danger btn-sm" onclick="javascript: del_nuevaventa(<?php echo $key; ?>);"><strong>X</strong></button>
				            </center>
				            </td>
									</tr>
<?php 							}
						 			}else{ ?>
									<tr>
							            <td colspan="10"> </td>
									</tr>
<?php 						}
							$total=($sub_total-$total_descuento)+$total_itbm;
							?>
		        </tbody>
		        <tfoot class="bg_green">
		            <tr>
		                <td></td>
		                <td></td>
		                <td></td>
		                <td></td>
		                <td></td>
										<td class=" al_center">
		                <strong>T. Desc: </strong> <br /><span id="span_discount_first"><?php echo number_format($total_descuento,2); ?></span>
		                </td>
		                <td class=" al_center">
		                <strong>T. Imp: </strong> <br /><span id="span_itbm_first"><?php echo number_format($total_itbm,2); ?></span>
		                </td>
										<td></td>
		                <td class=" al_center">
		                <strong>Total: </strong> <br /><span id="span_total_first"><?php echo number_format($total,2); ?></span>
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
    	<label for="txt_date_second">Fecha:</label>
	    <input type="text" class="form-control" alt="" id="txt_date_second" name="txt_date_second" readonly="readonly" value="<?php echo date('d-m-Y'); ?>" />
		</div>
		<div id="container_txtnumero" class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
	  	<label for="txt_numero">Cotización N&deg;:</label>
	    <span class="form-control bg-disabled"><?php echo $number; ?></span>
	    <input type="hidden" class="form-control" alt="" id="txt_numero" name="txt_numero" readonly="readonly" value="" />
	  </div>
		<div id="container_txtvendedor" class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
	  	<label for="txt_vendedor">Vendedor:</label>
	    <input type="text" class="form-control" alt="<?php echo $rs_vendor['AI_user_id']; ?>" id="txt_vendedor" name="txt_vendedor" readonly="readonly"  value="<?php echo $rs_vendor['TX_user_seudonimo']; ?>" />
	  </div>
	</div>
	<div id="container_client" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_txtfilterclient_second" class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
    	<label for="txt_filterclient_second">Cliente:</label>
	    <input type="text" class="form-control" alt="1" id="txt_filterclient_second" placeholder="CONTADO" name="txt_filterclient_second" onkeyup="unset_filterclient(event)" />
    </div>
		<div id="container_btnaddclientsecond" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
			<button type="button" id="btn_addclient_second" onclick="add_client()" class="btn btn-success btn_md"><strong><i class="fa fa-wrench" aria-hidden="true"></i></strong></button>
		</div>
		<div id="container_client_recall_second" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

		</div>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_txtobservation_second" class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
	  	<label for="txt_observation_second">Observaciones:</label>
			<input type="text" class="form-control" id="txt_observation_second" name="txt_observation_second" />
		</div>
		<div id="container_btnrefreshtblproduct2sale_second" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
			<button type="button" id="btn_refresh_tblproduct2sale_second" onclick="refresh_tblproduct2sale()" class="btn btn-info btn_md" title="Refrescar Tabla"><strong><i class="fa fa-refresh fa-spin fa-1x fa-fw"></i><span class="sr-only"></span></strong></button>
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
<?php 			$total_itbm = 0;	$total_descuento = 0;	$sub_total = 0;
						if(!empty($raw_nuevaventa[$_COOKIE['coo_iuser']]['second_sale'])){
							$nuevaventa_second=$raw_nuevaventa[$_COOKIE['coo_iuser']]['second_sale'];
							foreach ($nuevaventa_second as $key => $rs_nuevaventa) {
								$descuento = (($rs_nuevaventa['descuento']*$rs_nuevaventa['precio'])/100);
								$precio_descuento = ($rs_nuevaventa['precio']-$descuento);
								$impuesto = (($rs_nuevaventa['impuesto']*$precio_descuento)/100);
								$precio_unitario = round($precio_descuento+$impuesto,2);
								$precio_total = ($rs_nuevaventa['cantidad']*($precio_unitario));

								$total_itbm += $rs_nuevaventa['cantidad']*$impuesto;
								$total_descuento += $rs_nuevaventa['cantidad']*$descuento;
								$sub_total += $rs_nuevaventa['cantidad']*$rs_nuevaventa['precio'];
?>
									<tr>
				            <td><?php echo $rs_nuevaventa['codigo']; ?></td>
				            <td onclick="upd_descripcion_nuevaventa(<?php echo $key; ?>)"><?php echo $rs_nuevaventa['descripcion']; ?></td>
				            <td><?php echo $rs_nuevaventa['medida']; ?></td>
				            <td onclick="upd_unidades_nuevaventa(<?php echo $key ?>);"><?php echo $rs_nuevaventa['cantidad']; ?></td>
				            <td onclick="upd_precio_nuevaventa(<?php echo $key; ?>);"><?php echo number_format($rs_nuevaventa['precio'],2); ?></td>
										<td><?php echo number_format($descuento,2); ?></td>
				            <td><?php echo number_format($impuesto,2); ?></td>
										<td><?php echo number_format($precio_unitario,2); ?></td>
				            <td><?php echo number_format($precio_total,2); ?></td>
				            <td>
				            <center>
				            <button type="button" id="btn_delproduct" class="btn btn-danger btn-sm" onclick="javascript: del_nuevaventa(<?php echo $key; ?>);"><strong>X</strong></button>
				            </center>
				            </td>
									</tr>
<?php 							}
						 			}else{ ?>
									<tr>
							            <td colspan="10"> </td>
									</tr>
<?php 						}
							$total=($sub_total-$total_descuento)+$total_itbm;
							?>
		        </tbody>
		        <tfoot class="bg_red">
		            <tr>
		                <td></td>
		                <td></td>
		                <td></td>
		                <td></td>
		                <td></td>
										<td class=" al_center">
		                <strong>T. Desc: </strong> <br /><span id="span_discount_second"><?php echo number_format($total_descuento,2); ?></span>
		                </td>
		                <td class=" al_center">
		                <strong>T. Imp: </strong> <br /><span id="span_itbm_second"><?php echo number_format($total_itbm,2); ?></span>
		                </td>
										<td></td>
		                <td class=" al_center">
		                <strong>Total: </strong> <br /><span id="span_total_second"><?php echo number_format($total,2); ?></span>
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
			<label for="txt_filterproduct">Buscar:</label>
	    <input type="text" class="form-control" id="txt_filterproduct" name="txt_filterproduct" autocomplete="off" onkeyup="filter_product_sell(this);" />
		</div>
		<div id="container_limit" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
			<label for="txt_rlimit">Mostrar:</label><br />
			<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="20"  checked="checked" /> 20</label>
			<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="50" /> 50</label>
			<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="100" /> 100</label>
		</div>
		<div id="container_report" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
			<button type="button" id="btn_report" class="btn btn-warning btn-sm">Reportar</button>
	  </div>
	  <div id="container_selproduct" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	    <table id="tbl_product" class="table table-bordered table-hover table-striped">
	    <caption>Lista de Productos:</caption>
	    <thead class="bg-primary">
	    	<tr>
        	<th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Codigo</th>
          <th class="col-xs-6 col-sm-6 col-md-8 col-lg-8">Nombre</th>
        	<th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Cantidad</th>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Precio</th>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Letra</th>
        </tr>
	    </thead>
	    <tfoot class="bg-primary">
		    <tr>	<td colspan="5"> </td>	</tr>
	    </tfoot>
			<tbody>

<?php
			if($nr_product=$qry_product->num_rows > 0){
				foreach ($raw_producto as $key => $rs_product) {
?>
			    <tr onclick="javascript:open_product2sell(<?php echo $rs_product['AI_producto_id']; ?>);">
	        	<td title="<?php echo $rs_product['AI_producto_id']; ?>"><?php echo $rs_product['TX_producto_codigo']; ?></td>
	        	<td><?php echo $rs_product['TX_producto_value']; ?></td>
	        	<td><?php echo $rs_product['TX_producto_cantidad']; ?></td>
						<td><?php echo $rs_product['precio']; ?></td>
						<td><?php echo $rs_product['letra']; ?></td>
	        </tr>
<?php 	};
			}else{
?>
		    <tr>
	    		<td colspan="5">  </td>
	    	</tr>
<?php
			}
?>
	    </tbody>
	    </table>
		</div>
	</div>

	<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	    <button type="button" id="btn_facturar" name="FACTURADA" onclick="save_sale(FACTURADA)" class="btn btn-success">Bloquear</button>
	    &nbsp;&nbsp;&nbsp;
	    <button type="button" id="btn_guardar" name="ACTIVA" onclick="save_sale('ACTIVA')"class="btn btn-primary">Guardar</button>
	    &nbsp;&nbsp;&nbsp;
	    <button type="button" id="btn_salir" class="btn btn-warning">Volver</button>
	</div>


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
<?php $link->close(); ?>
