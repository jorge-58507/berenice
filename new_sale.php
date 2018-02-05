<?php
require 'bh_con.php';
$link=conexion();
date_default_timezone_set('America/Panama');
require 'attached/php/req_login_sale.php';

function checkfacturaventa($numero){
	$qry_checkfacturaventa=mysql_query("SELECT * FROM bh_facturaventa WHERE TX_facturaventa_numero = '$numero'");
	$nr_checkfacturaventa=mysql_num_rows($qry_checkfacturaventa);
	if($nr_checkfacturaventa > 0){
		return sumarfacturaventa($numero);
	}else{
		return $numero;
	}
	mysql_close();
}
function sumarfacturaventa($numero){
		return checkfacturaventa($numero+1);
}

$next_num2sale=$_GET['a'];

//mysql_query("DELETE FROM bh_nuevaventa WHERE nuevaventa_AI_user_id = '{$_COOKIE['coo_iuser']}'",$link);

$qry_checknextnum2sale=mysql_query("SELECT * FROM bh_facturaventa WHERE TX_facturaventa_numero = '$next_num2sale'", $link);
$nr_checknextnum2sale=mysql_num_rows($qry_checknextnum2sale);

if($nr_checknextnum2sale > 0){
	echo "<meta http-equiv='Refresh' content='1;url=index.php'>";
}


$qry_product=mysql_query("SELECT * FROM bh_producto WHERE TX_producto_activo = '0' ORDER BY TX_producto_value ASC LIMIT 10");
$rs_product=mysql_fetch_assoc($qry_product);

$qry_client=mysql_query("SELECT * FROM bh_cliente ORDER BY TX_cliente_nombre ASC");
$rs_client=mysql_fetch_assoc($qry_client);

$rs = mysql_query("SELECT MAX(AI_facturaventa_id) AS id FROM bh_facturaventa");
if ($row = mysql_fetch_row($rs)) {
	$last_id = trim($row[0]);
	$next_id = $last_id+'1';
}

$qry_lastclientid = mysql_query("SELECT MAX(AI_cliente_id) AS id FROM bh_cliente");
if ($row = mysql_fetch_row($qry_lastclientid)) {
	$last_clientid = trim($row[0]);
	$next_clientid = $last_clientid+'1';
}


$qry_vendor=mysql_query("SELECT * FROM bh_user WHERE AI_user_id = '{$_COOKIE['coo_iuser']}'");
$rs_vendor=mysql_fetch_assoc($qry_vendor);

$qry_nuevaventa=mysql_query("SELECT bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_producto.TX_producto_cantidad, bh_nuevaventa.TX_nuevaventa_unidades, bh_nuevaventa.TX_nuevaventa_precio, bh_nuevaventa.TX_nuevaventa_itbm, bh_nuevaventa.TX_nuevaventa_descuento, bh_nuevaventa.nuevaventa_AI_producto_id FROM bh_producto, bh_nuevaventa WHERE bh_producto.AI_producto_id = bh_nuevaventa.nuevaventa_AI_producto_id AND bh_nuevaventa.nuevaventa_AI_user_id = '{$_COOKIE['coo_iuser']}' ORDER BY AI_nuevaventa_id ASC");
$nr_nuevaventa=mysql_num_rows($qry_nuevaventa);
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
	//	clean_nuevaventa();
		close_popup();
	});

	if (<?php echo $nr_nuevaventa; ?> < 1) {
		$("#btn_guardar").css('display','none');
		$("#btn_facturar").css('display','none');
	}

	$('#txt_filterproduct').focus();

	$( function() {
		$( "#txt_filterclient").autocomplete({
			source: "attached/get/filter_client_sell.php",
			minLength: 2,
			select: function( event, ui ) {
				$("#txt_filterclient").prop('alt', ui.item.id);
				content = '<strong>Nombre:</strong> '+ui.item.value+' <strong>RUC:</strong> '+ui.item.ruc+' <strong>Tlf.</strong> '+ui.item.telefono+' <strong>Dir.</strong> '+ui.item.direccion.substr(0,20);
				fire_recall('container_client_recall', content)
			}
		});
	});


	$("#btn_sale").click(function(){
		window.location="sale.php";
	});
	$("#btn_stock").click(function(){
		window.location="stock.php";
	});
	$("#btn_addclient").click(function(){
		var name = $("#txt_filterclient").val();
		name = name.replace('&','ampersand');
		if($("#txt_filterclient").prop('alt') != ""){
			if($("#txt_filterclient").prop('alt') === "1"){
				open_popup('popup_addclient.php?a='+name,'popup_addclient','425','420')
			}else{
				open_popup('popup_updclient.php?a='+$("#txt_filterclient").prop('alt'),'popup_updclient','425','420')
			}
		}else{
			open_popup('popup_addclient.php?a='+name,'popup_addclient','425','420')
		}

	});

	$("#btn_guardar").click(function(){
	var status = 'ACTIVA';
	var str = $("#txt_filterclient").prop("alt");
		if(str === ''){
			$("#txt_filterclient").css("border","solid 2px #f50000");
			$("#txt_filterclient").val('');
			$("#txt_filterclient").focus();
			return false;
		}
		save_sale(status);
	//		alert("procesa guardar");
	})

	$('#btn_facturar').click(function(){
		var status = 'FACTURADA';
		var str = $("#txt_filterclient").prop("alt");
		if(str === ''){
			$("#txt_filterclient").css("border","solid 2px #f50000");
			$("#txt_filterclient").val('');
			$("#txt_filterclient").focus();
			return false;
		}
		// alert("procesa facturar");
		save_sale(status);
	});

	$('#btn_salir').click(function(){
		setTimeout("history.back(1)",250);
	});


	$("#txt_filterproduct").keyup(function(e){
		if(e.which == 13){
			setTimeout( function(){ $("#tbl_product tbody tr:first").click(); },2000);
		}
		if(e.which == 120) {
			$("#btn_guardar").click();
		}

	});
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
	$("#txt_filterclient").validCampoFranz('abcdefghijklmnopqrstuvwxyz .0123456789-/&');
	$("#btn_report").click(function(){
		var str = prompt("Ingrese los datos",$("#txt_filterproduct").val());
		str = str.replace("#","laremun");
		$.ajax({
		data: {"a" : str}, type: "GET", dataType: "text", url: "attached/get/plus_stock_report.php",
		})
		.done(function( data, textStatus, jqXHR ) {
		var alert_bootstrap = "<div class='alert alert-info alert-dismissable fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Atenci&oacute;n</strong> "+data+"</div>";
			$("#container_filterproduct").html($("#container_filterproduct").html()+alert_bootstrap);
			setTimeout("$('.close').click()", 3000);

		})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});

	});
	$("#btn_refresh_tblproduct2sale").on("click",function(){
		$.ajax({	data: "", type: "GET", dataType: "text", url: "attached/get/get_tblproduct2sale.php",	})
		.done(function( data, textStatus, jqXHR ) {
			$("#container_tblproduct2sale").html(data);
		})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
	});

	$("#container_client_recall").css("display","none");

	$("#container_username label").on("click", function(){
		popup = window.open("popup_loginadmin.php?z=start_admin.php", "popup_loginadmin", 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=no,width=425,height=420');
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
<form action="sale.php" method="post" name="form_sell"  id="form_sell">

<div id="container_complementary" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_txtdate" class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
    	<label for="txt_date">Fecha:</label>
	    <input type="text" class="form-control" alt="" id="txt_date" name="txt_date" readonly="readonly"
        value="<?php echo date('d-m-Y'); ?>" />
    </div>
	<div id="container_txtnumero" class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
    	<label for="txt_numero">Cotización N&deg;:</label>
        <span class="form-control bg-disabled"><?php echo $numero=checkfacturaventa('1'); ?></span>
	    <input type="hidden" class="form-control" alt="" id="txt_numero" name="txt_numero" readonly="readonly"
        value="" />
    </div>
	<div id="container_txtvendedor" class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
    	<label for="txt_vendedor">Vendedor:</label>
	    <input type="text" class="form-control" alt="<?php echo $rs_vendor['AI_user_id']; ?>" id="txt_vendedor" name="txt_vendedor" readonly="readonly"
        value="<?php echo $rs_vendor['TX_user_seudonimo']; ?>" />
    </div>
</div>
<div id="container_client" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_txtfilterclient" class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
    	<label for="txt_filterclient">Cliente:</label>
	    <input type="text" class="form-control" alt="1" id="txt_filterclient" placeholder="CONTADO" name="txt_filterclient" onkeyup="unset_filterclient(event)" />
    </div>
	<div id="container_btnaddclient" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
		<button type="button" id="btn_addclient" class="btn btn-success"><strong><i class="fa fa-wrench" aria-hidden="true"></i></strong></button>
	</div>
	<div id="container_client_recall" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

	</div>
</div>
<div id="container_txtobservation" class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
    	<label for="txt_observation">Observaciones:</label>
		<input type="text" class="form-control" id="txt_observation" name="txt_observation" />
</div>
<div id="container_btnrefreshtblproduct2sale" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
		<button type="button" id="btn_refresh_tblproduct2sale" class="btn btn-info btn-md" title="Refrescar Tabla">
        <strong><i class="fa fa-refresh fa-spin fa-1x fa-fw"></i><span class="sr-only"></span></strong>
        </button>
</div>
<div id="container_product2sell" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_tblproduct2sale" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <table id="tbl_product2sell" class="table table-bordered table-hover ">
        <caption>Lista de Productos para la Venta</caption>
        <thead class="bg_green">
            <tr>
                <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Codigo</th>
                <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Producto</th>
                <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Medida</th>
                <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
                <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Precio</th>
                <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Imp.</th>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Desc</th>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">P. Uni.</th>
                <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">SubTotal</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
					<?php
					if($nr_nuevaventa > 0){
					$rs_nuevaventa=mysql_fetch_assoc($qry_nuevaventa);

					$total_itbm = 0;
					$total_descuento = 0;
					$sub_total = 0;
					do{
						$descuento = (($rs_nuevaventa['TX_nuevaventa_descuento']*$rs_nuevaventa['TX_nuevaventa_precio'])/100);
						$precio_descuento = ($rs_nuevaventa['TX_nuevaventa_precio']-$descuento);
						$impuesto = (($rs_nuevaventa['TX_nuevaventa_itbm']*$precio_descuento)/100);
						$precio_unitario = round($precio_descuento+$impuesto,2);
						$precio_total = ($rs_nuevaventa['TX_nuevaventa_unidades']*($precio_unitario));

						$total_itbm += $rs_nuevaventa['TX_nuevaventa_unidades']*$impuesto;
						$total_descuento += $rs_nuevaventa['TX_nuevaventa_unidades']*$descuento;
						$sub_total += $rs_nuevaventa['TX_nuevaventa_unidades']*$rs_nuevaventa['TX_nuevaventa_precio'];
					?>

							<tr>
		            <td><?php echo $rs_nuevaventa['TX_producto_codigo']; ?></td>
		            <td><?php echo $rs_nuevaventa['TX_producto_value']; ?></td>
		            <td><?php echo $rs_nuevaventa['TX_producto_medida']; ?></td>
		            <td onclick="upd_unidadesnuevaventa(<?php echo $rs_nuevaventa['nuevaventa_AI_producto_id']; ?>);">
								<?php echo $rs_nuevaventa['TX_nuevaventa_unidades']; ?>
		            <span id="stock_quantity"><?php echo $rs_nuevaventa['TX_producto_cantidad']; ?></span>
		            </td>
		            <td onclick="upd_precionuevaventa(<?php echo $rs_nuevaventa['nuevaventa_AI_producto_id']; ?>);">
									<?php echo number_format($rs_nuevaventa['TX_nuevaventa_precio'],2); ?>
								</td>
		            <td><?php echo number_format($impuesto,2); ?></td>
		            <td><?php echo number_format($descuento,2); ?></td>
								<td><?php echo number_format($precio_unitario,2); ?></td>
		            <td><?php echo number_format($precio_total,2); ?></td>
		            <td>
		            <center>
		            <button type="button" name="<?php echo $rs_nuevaventa['nuevaventa_AI_producto_id']; ?>" id="btn_delproduct" class="btn btn-danger btn-sm" onclick="javascript: del_product2sell(this);"><strong>X</strong></button>
		            </center>
		            </td>
							</tr>
					<?php }while($rs_nuevaventa=mysql_fetch_assoc($qry_nuevaventa)); ?>
					<?php }else{ ?>
					<?php
					$total_itbm = 0;
					$total_descuento = 0;
					$sub_total = 0;
					?>
							<tr>
					            <td></td>
					            <td></td>
					            <td></td>
											<td></td>
											<td></td>
					            <td></td>
					            <td></td>
					            <td></td>
					            <td></td>
					            <td></td>
							</tr>
					<?php }

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
                <td>
                <strong>T. Imp: </strong> <br /><span id="span_itbm"><?php echo number_format($total_itbm,2); ?></span>
                </td>
                <td>
                <strong>T. Desc: </strong> <br /><span id="span_discount"><?php echo number_format($total_descuento,2); ?></span>
                </td>
								<td></td>
                <td>
                <strong>Total: </strong> <br /><span id="span_total"><?php echo number_format($total,2); ?></span>
                </td>
                <td>  </td>
            </tr>
        </tfoot>
        </table>
    </div>
</div>

<div id="container_product_list" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_filterproduct" class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
		<label for="txt_filterproduct">Buscar:</label>
    <input type="text" class="form-control" id="txt_filterproduct" name="txt_filterproduct" autocomplete="off" onkeyup="filter_product_sell(this);" />
	</div>
	<div id="container_limit" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
		<label for="txt_rlimit">Mostrar:</label><br />
		<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="10" checked="checked"/> 10</label>
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
        	<th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">
            	Codigo
            </th>
            <th class="col-xs-6 col-sm-6 col-md-8 col-lg-8">
            	Nombre
            </th>
        	<th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">
            	Cantidad
            </th>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            	Precio
            </th>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            	Letra
            </th>
        </tr>
    </thead>
    <tfoot class="bg-primary">
	    <tr>
    		<td>  </td>
    		<td>  </td>
    		<td>  </td>
    		<td>  </td>
    		<td>  </td>
    	</tr>
    </tfoot>
<tbody>
    <?php
	if($nr_product=mysql_num_rows($qry_product) > 0){
	do{
	?>
    	<tr onclick="javascript:open_product2sell(<?php echo $rs_product['AI_producto_id']; ?>);">
        	<td title="<?php echo $rs_product['AI_producto_id']; ?>">
            <?php echo $rs_product['TX_producto_codigo']; ?>
            </td>
        	<td>
            <?php echo $rs_product['TX_producto_value']; ?>
            </td>
        	<td>
            <?php echo $rs_product['TX_producto_cantidad']; ?>
            </td>
        	<td>
            <?php
			$rs_precio=mysql_fetch_array(mysql_query("SELECT TX_precio_cuatro FROM bh_precio WHERE precio_AI_producto_id = '{$rs_product['AI_producto_id']}' AND TX_precio_inactivo = '0'"));
			echo $rs_precio['TX_precio_cuatro']; ?>
            </td>
        	<td>
            <?php
			$rs_letra=mysql_fetch_array(mysql_query("SELECT bh_letra.TX_letra_value FROM bh_letra, bh_producto WHERE bh_letra.AI_letra_id = '{$rs_product['producto_AI_letra_id']}'"));
			echo $rs_letra['TX_letra_value']; ?>
            </td>
        </tr>
    <?php }while($rs_product=mysql_fetch_assoc($qry_product)); ?>
<?php
}else{
?>
	    <tr>
    		<td>  </td>
    		<td>  </td>
    		<td>  </td>
    		<td>  </td>
    		<td>  </td>
    	</tr>
<?php
}
?>
    </tbody>
    </table>



	</div>
</div>

<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <button type="button" id="btn_facturar" name="FACTURADA" class="btn btn-success">Bloquear</button>
    &nbsp;&nbsp;&nbsp;
    <button type="button" id="btn_guardar" name="ACTIVA" class="btn btn-primary">Guardar</button>
    &nbsp;&nbsp;&nbsp;
    <button type="button" id="btn_salir" class="btn btn-warning">Volver</button>
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
<?php mysql_close(); ?>
