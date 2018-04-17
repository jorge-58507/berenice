<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_stock.php';

$qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida WHERE TX_medida_value = 'UNIDADES' ORDER BY TX_medida_value ASC");
$rs_medida=$qry_medida->fetch_array(MYSQLI_ASSOC);

$qry_product=$link->query("SELECT AI_producto_id, TX_producto_value, TX_producto_minimo, TX_producto_codigo, TX_producto_medida, TX_producto_alarma, TX_producto_maximo, TX_producto_cantidad, TX_producto_rotacion, TX_producto_referencia FROM bh_producto ORDER BY TX_producto_value ASC LIMIT 20 ");
$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);

$qry_itbm=$link->query("SELECT TX_opcion_value FROM bh_opcion WHERE TX_opcion_titulo = 'IMPUESTO'");
$row_itbm=$qry_itbm->fetch_array();
$itbm = $row_itbm[0];

$qry_checkbeneath=$link->query("SELECT AI_producto_id FROM bh_producto WHERE TX_producto_cantidad < TX_producto_minimo AND TX_producto_alarma = '0'");
$nr_checkbeneath=$qry_checkbeneath->num_rows;

$qry_checkreport=$link->query("SELECT AI_reporte_id FROM bh_reporte WHERE TX_reporte_tipo = 'INVENTARIO' AND TX_reporte_status = 'ACTIVA'");
$nr_checkreport=$qry_checkreport->num_rows;
if($nr_checkreport > 0){ $value_button="Reporte (".$nr_checkreport.")"; }else{ $value_button="Reporte"; }

$qry_letter=$link->query("SELECT AI_letra_id, TX_letra_value, TX_letra_porcentaje FROM bh_letra")or die ($link->error);
$prep_precio=$link->prepare("SELECT TX_precio_cuatro FROM bh_precio WHERE precio_AI_producto_id = ? AND TX_precio_inactivo = '0' ORDER BY AI_precio_id DESC LIMIT 1");
$prep_facturaventa=$link->prepare("SELECT bh_facturaventa.AI_facturaventa_id FROM (bh_datoventa INNER JOIN bh_facturaventa ON bh_datoventa.datoventa_AI_facturaventa_id = bh_facturaventa.AI_facturaventa_id) WHERE bh_datoventa.datoventa_AI_producto_id = ?")or die($link->error);
$prep_facturacompra=$link->prepare("SELECT bh_facturacompra.AI_facturacompra_id FROM (bh_datocompra INNER JOIN bh_facturacompra ON bh_datocompra.datocompra_AI_facturacompra_id = bh_facturacompra.AI_facturacompra_id) WHERE bh_datocompra.datocompra_AI_producto_id = ?")or die($link->error);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>

<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap-theme.css" rel="stylesheet" type="text/css" />
<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_layout.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />
<link href="attached/css/stock_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/stock_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript" src="attached/js/jquery.cookie.js"></script>

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
$("#btn_exit").click(function(){
	location.href="index.php";
})
$("#btn_start").click(function(){
	window.location="start.php";
});
$(window).on('beforeunload',function(){
	close_popup();
});
$("#txt_p_1, #txt_p_2, #txt_p_3, #txt_p_4, #txt_p_5").on("blur",function(){
	this.value = val_intw2dec(this.value);
})

	setTimeout("upd_btn_report()",60000)
	$("#container_create_product").css("display","none");

	$('#btn_save_product').click(function(){
		if($("#txt_codigo, #txt_cantidad, #txt_medida, #txt_cantminima, #txt_cantmaxima") === ""){
			return false;
		}
		if($('#txt_p_4').val() === ""){
			$('#txt_p_4').val('0.00');
		}
		ans = val_intwdec($('#txt_p_4').val());
		if(!ans){	return false;	}
		ans = val_intwdec($('#txt_cantidad').val());
		if(!ans){	return false;	}

		setTimeout("add_product()",250);
	})

	$('#btn_clean_product').click(function(){
		$('#txt_codigo, #txt_nombre, #txt_cantidad, #txt_cantminima, #txt_cantmaxima, #txt_p_1, #txt_p_2, #txt_p_3, #txt_p_4, #txt_p_5').val("");
	})

	$('#txt_cantidad').validCampoFranz('.0123456789');
	$('#txt_cantminima, #txt_cantmaxima, #txt_impuesto').validCampoFranz('0123456789');
	$('#txt_p_1, #txt_p_2, #txt_p_3, #txt_p_4, #txt_p_5').validCampoFranz('.0123456789');


	$("#btn_qry_entry").click(function(){
		window.location='purchased.php';
	});
	$("#btn_reg_entry").click(function(){
		window.location='order.php';
	});
	$("#btn_purchase").on("click",function(){
		window.location='purchase.php';
	})
	$("#btn_qry_sale").click(function(){
		window.location='sold.php';
	});
	$("#btn_admincuentaxpagar").on("click",function(){
		window.location='admin_provider.php';
	});


	$("#div_newproduct").click(function(){
		$("#container_create_product").toggle(200);
		$("#div_newproduct").toggleClass("fa-angle-double-down");
		$("#div_newproduct").toggleClass("fa-angle-double-up");
	});

	$('#txt_nombre').validCampoFranz(".0123456789abcdefghijklmnopqrstuvwxyzº'#/-; ");
	$('#txt_codigo').validCampoFranz("0123456789abcdefghijklmnopqrstuvwxyz");
	$('#txt_referencia').validCampoFranz("0123456789abcdefghijklmnopqrstuvwxyz/- ");

	$("#btn_alarm_off").click(function(){
		$.ajax({	data: "",	type: "GET",	dataType: "text",	url: "attached/get/filter_product_alarmoff.php", })
		 .done(function( data, textStatus, jqXHR ) {	$("#container_tblproduct").html( data );	})
		 .fail(function( jqXHR, textStatus, errorThrown ) {		});
	});
	$("#btn_inactive").click(function(){
		$.ajax({	data:"",type:"GET", dataType:"text",url:"attached/get/filter_product_inactive.php"	})
		.done(function(data, textStatus, jqXHR){	$("#container_tblproduct").html(data);	})
		.fail(function(data, textStatus, errorThrown){	});
	});

	$("#container_filterbutton").css("display","none");

	$("#div_expand_filterbutton").click(function(){
		$("#container_filterbutton").toggle(500);
		$("#div_expand_filterbutton").toggleClass("fa-angle-double-right");
		$("#div_expand_filterbutton").toggleClass("fa-angle-double-left");
	});

	$("#txt_p_1, #txt_p_2, #txt_p_3, #txt_p_4, #txt_p_5").on("blur",function(){
		this.value = val_intw2dec(this.value);
	});

	$("#txt_codigo").on("blur", function(){
		if(this.value.length == '6'){
			this.value = "0000000"+this.value;
		}
	});

	$("#txt_filterproduct").on("keyup",function(){
		value = url_replace_regular_character($("#txt_filterproduct").val());
		$.ajax({	data: {"a" : value },	type: "GET",	dataType: "text",	url: "attached/get/filter_product.php", })
		.done(function( data, textStatus, jqXHR ) { console.log("GOOD "+textStatus);
			$("#tbl_product tbody").html(data);
		})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
	});

	$( function() {
		$("#txt_codigo").autocomplete({
			source: "attached/get/filter_producto_codigo.php",
			minLength: 2,
			select: function( event, ui ) {
				splited_value = ui.item.value.split(" | ");
				new_value = splited_value[0];
				ui.item.value = new_value;
				fire_recall('container_product_recall', '¡Atencion!, Codigo a duplicar')
			}
		});
	});

	$("#btn_qry_report").on("click",function(){
		open_popup('popup_stock_report.php','popup_stock_report','600','425');
	});



});
	function upd_btn_report(){
		$.ajax({	data:"",type:"GET", dataType:"text",url:"attached/get/get_btn_report.php"	})
		.done(function(data, textStatus, jqXHR){
			$("#btn_qry_report").html(data);
		})
		.fail(function(data, textStatus, errorThrown){	});

		setTimeout("upd_btn_report()",60000)
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
        Bienvenido: <label class="label label_blue_sky"  class="bg-primary">
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
<form name="form_inventory" id="form_inventory" method="post">
<div id="container_create_product" class="col-xs-12 col-sm-12 col-md-8 col-lg-8" >

    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label class="label label_blue_sky"  class="label label-primary" for="txt_nombre">Nombre:</label>
			<input type="text" class="form-control input-sm" id="txt_nombre" name="txt_nombre" onkeyup="setUpperCase(this);" />
    </div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			<label class="label label_blue_sky"  class="label label-primary"  for="txt_codigo">Codigo:</label>
			<input type="text" class="form-control input-sm" id="txt_codigo" name="txt_codigo" onkeyup="setUpperCase(this);">
    </div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			<label class="label label_blue_sky"  class="label label-primary"  for="txt_referencia">Referencia:</label>
			<input type="text" class="form-control input-sm" id="txt_referencia" name="txt_referencia" onkeyup="setUpperCase(this);">
    </div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 alert alert-danger display_none" id="container_product_recall">

			</div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label class="label label_blue_sky"  class="label label-primary"  for="txt_cantidad">Cantidad:</label>
			<input type="text" class="form-control  input-sm" id="txt_cantidad" name="txt_cantidad">
        </div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label class="label label_blue_sky"  for="sel_medida">Medida:</label>
			<select  class="form-control input-sm" id="sel_medida" name="sel_medida">
<?php	do{	?>
<option value="<?php echo $rs_medida['AI_medida_id']; ?>"><?php echo $rs_medida['TX_medida_value']; ?></option>
<?php	}while($rs_medida=$qry_medida->fetch_array(MYSQLI_ASSOC));	?>
			</select>
        </div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			<label class="label label_blue_sky"  for="txt_cantminima">Cantidad M&iacute;nima:</label>
			<input type="text" class="form-control input-sm" id="txt_cantminima" name="txt_cantminima">
        </div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			<label class="label label_blue_sky"  for="txt_cantmaxima">Cantidad M&aacute;xima:</label>
			<input type="text" class="form-control input-sm" id="txt_cantmaxima" name="txt_cantmaxima">
        </div>
		<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
			<label class="label label_blue_sky"  for="txt_impuesto">Impuesto %:</label><br />
			<input type="text" class="form-control input-sm" id="txt_impuesto" name="txt_impuesto" value="<?php echo $itbm; ?>">
        </div>
		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
			<label class="label label_blue_sky"  for="txt_impuesto">Letra:</label><br />
			<select id="sel_letter" class="form-control input-sm">
            <?php while($rs_letter=$qry_letter->fetch_array(MYSQLI_ASSOC)){ ?>
    <option value="<?php echo $rs_letter['AI_letra_id']; ?>"><?php echo $rs_letter['TX_letra_value']; ?></option>
            <?php } ?>
            </select>
        </div>
    </div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        	<span id="span_title" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><h4><strong>Precios</strong></h4></span>
			<label class="label label_blue_sky"  for="txt_cantmaxima">Standard:</label>
			<input type="text" class="form-control input-sm" id="txt_p_4" name="txt_p_4">
			<label class="label label_blue_sky"  for="txt_cantmaxima">Precio M&aacute;ximo:</label>
			<input type="text" class="form-control input-sm" id="txt_p_5" name="txt_p_5">
			<label class="label label_blue_sky"  for="txt_cantmaxima">Descuento #1:</label>
			<input type="text" class="form-control input-sm" id="txt_p_3" name="txt_p_3">
			<label class="label label_blue_sky"  for="txt_cantmaxima">Descuento #2:</label>
			<input type="text" class="form-control input-sm" id="txt_p_2" name="txt_p_2">
			<label class="label label_blue_sky"  for="txt_cantmaxima">Descuento #3:</label>
			<input type="text" class="form-control input-sm" id="txt_p_1" name="txt_p_1">
        </div>

    </div>


	<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <button type="button" name="btn_save_product" id="btn_save_product" class="btn btn-success">Nuevo Producto</button>
    &nbsp;
    <button type="button" name="btn_clean_product" id="btn_clean_product" class="btn btn-warning">Limpiar Campos</button>
  </div>
</div>

<div id="container_btn_purchase" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
	<div id="container_div_newproduct" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
		<div id="div_newproduct" class="fa fa-angle-double-down"> Nvo. Articulo</div>
  </div>

<button type="button" name="btn_new_entry" id="btn_purchase" class="btn btn-default btn-lg" >Compras</button>
&nbsp;
<button type="button" name="btn_reg_entry" id="btn_reg_entry" class="btn btn-info btn-lg" >Pedidos</button>
&nbsp;
<button type="button" name="btn_qry_entry" id="btn_qry_entry" class="btn btn-primary btn-lg" >Buscar Compra</button>
&nbsp;
<button type="button" name="btn_qry_sale" id="btn_qry_sale" class="btn btn-primary btn-lg" >Buscar Venta</button>
&nbsp;
<button type="button" name="btn_qry_report" id="btn_qry_report" class="btn btn-warning btn-lg" ><?php echo $value_button; ?></button>
&nbsp;
<button type="button" name="btn_admincuentaxpagar" id="btn_admincuentaxpagar" class="btn btn-info btn-lg"><strong>Proveedores</strong></button>
</div>
<div id="container_alert" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<?php if($nr_checkbeneath > 0){ ?>
		<div class="alert alert-danger alert-dismissable fade in">
		<a href="#" onclick="filter_beneath();" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<strong>Atenci&oacute;n!</strong> Hay productos con baja existencia.
		</div>
    <?php } ?>
</div>
<div id="container_filterproduct" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
	<label class="label label_blue_sky"  for="txt_filterproduct">Buscar:</label>
  <input type="text" autofocus class="form-control" id="txt_filterproduct" name="txt_filterproduct" autocomplete="off" />
</div>

<div id="container_filterbutton" class="col-xs-10 col-sm-10 col-md-5 col-lg-5">
	<label class="label label_blue_sky"  class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Ver:</label>
	<button type="button" id="btn_alarm_off" name="btn_alarm_off" class="btn btn-warning btn-xs">Alarma Off</button>
    &nbsp;&nbsp;
	<button type="button" id="btn_inactive" name="btn_inactive" class="btn btn-warning btn-xs">Inactivo</button>
</div>
<div id="container_div_expand_filterbutton" class="col-xs-2 col-sm-2 col-md-1 col-lg-1" >
    <div id="div_expand_filterbutton" class="fa fa-angle-double-right"></div>
</div>
<div id="container_tblproduct" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<?php
	if($nr_product=$qry_product->num_rows != '0'){ ?>
		<table id="tbl_product" border="0" class="table table-bordered table-hover table-condensed table-striped">
			<thead class="bg-primary">
				<tr>
					<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Codigo</th>
					<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Referencia</th>
					<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Nombre</th>
					<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
					<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Precio</th>
					<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
					<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
					<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
				</tr>
			</thead>
			<tfoot class="bg-primary">
				<tr><td colspan="8"></td></tr>
			</tfoot>
			<tbody>
<?php			do{		?>
					<tr ondblclick="openpopup_updproduct('<?php echo $rs_product['AI_producto_id'] ?>');">
						<td><?php echo $rs_product['TX_producto_codigo'] ?></td>
						<td><?php echo $rs_product['TX_producto_referencia'] ?></td>
						<td><?php echo $rs_product['TX_producto_value'] ?></td>
						<td>
<?php						if($rs_product['TX_producto_cantidad'] >= $rs_product['TX_producto_maximo']){
								echo '<font style="color:#51AA51">'.$rs_product['TX_producto_cantidad'].'</font>';
							}elseif($rs_product['TX_producto_cantidad'] <= $rs_product['TX_producto_minimo']){
								echo '<font style="color:#C63632">'.$rs_product['TX_producto_cantidad'].'</font>';
							}else{
								echo '<font style="color:#000000">'.$rs_product['TX_producto_cantidad'].'</font>';
							}
?>						</td>
						<td>
<?php 					$prep_precio->bind_param("i",$rs_product['AI_producto_id']); $prep_precio->execute(); $qry_precio=$prep_precio->get_result();
							$rs_precio=$qry_precio->fetch_array(MYSQLI_ASSOC);
							echo $rs_precio['TX_precio_cuatro'];
?>						</td>
						<td><button type="button" class="btn btn-success" onclick="open_popup('popup_relacion.php?a=<?php echo $rs_product['AI_producto_id'] ?>','popup_relacion','500','491')"><i class="fa fa-rotate-right" aria-hidden="true"></i><?php echo $rs_product['TX_producto_rotacion']; ?></button></td>
						<td><button type="button" name="btn_upd_product" id="btn_upd_product" class="btn btn-warning btn-sm" onclick="openpopup_updproduct('<?php echo $rs_product['AI_producto_id'] ?>');">Modificar</button></td>
						<td>
<?php 					$prep_facturaventa->bind_param("i", $rs_product['AI_producto_id']); $prep_facturaventa->execute(); $qry_facturaventa=$prep_facturaventa->get_result();
							if($qry_facturaventa->num_rows < 1){
								$prep_facturacompra->bind_param("i", $rs_product['AI_producto_id']); $prep_facturacompra->execute(); $qry_facturacompra=$prep_facturacompra->get_result();
								if ($qry_facturacompra->num_rows < 1) {  ?>
									<button type="button" name="btn_del_product" id="btn_del_product" class="btn btn-danger btn-sm" onclick="del_product('<?php echo $rs_product['AI_producto_id'] ?>');">Eliminar</button>
<?php							}
							} ?>
						</td>
					</tr>
<?php			}while($rs_product=$qry_product->fetch_array()); ?>
			</tbody>
		</table><?php
	} ?>
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
