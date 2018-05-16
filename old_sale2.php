<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_sale.php';

$facturaventa_id=$_GET['a'];
$qry_product=$link->query("SELECT AI_producto_id, TX_producto_value, TX_producto_codigo, TX_producto_medida, TX_producto_cantidad, producto_AI_letra_id FROM bh_producto ORDER BY TX_producto_value ASC LIMIT 10")or die($link->error);
$rs_product=$qry_product->fetch_array();

$qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida")or die($link->error);
$raw_medida = array();
while($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)){
	$raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
}

/* #####################  FACTURA VENTA QUERY   #####################*/

$txt_facturaventa="SELECT bh_facturaventa.TX_facturaventa_observacion, bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.facturaventa_AI_user_id, bh_facturaventa.AI_facturaventa_id, bh_cliente.TX_cliente_nombre, bh_facturaventa.facturaventa_AI_cliente_id, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status, bh_facturaventa.TX_facturaventa_status, bh_facturaventa.TX_facturaventa_promocion
FROM (bh_facturaventa INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_facturaventa.facturaventa_AI_cliente_id)
WHERE bh_facturaventa.AI_facturaventa_id = '$facturaventa_id' AND bh_facturaventa.TX_facturaventa_status != 'CANCELADA'";
$qry_facturaventa=$link->query($txt_facturaventa)or die($link->error);
$nr_facturaventa=$qry_facturaventa->num_rows;
if($nr_facturaventa < 1){
	echo "<meta http-equiv='Refresh' content='1;url=index.php'>";
}

$rs_facturaventa=$qry_facturaventa->fetch_array();

$qry_vendor=$link->query("SELECT AI_user_id, TX_user_seudonimo FROM bh_user WHERE AI_user_id = '{$rs_facturaventa['facturaventa_AI_user_id']}'");
$rs_vendor=$qry_vendor->fetch_array();

$qry_datoventa=$link->query("SELECT AI_datoventa_id, datoventa_AI_facturaventa_id, datoventa_AI_producto_id, TX_datoventa_cantidad, TX_datoventa_precio, TX_datoventa_impuesto, TX_datoventa_descuento, TX_datoventa_descripcion, TX_datoventa_medida, TX_datoventa_promocion FROM bh_datoventa WHERE datoventa_AI_facturaventa_id = '$facturaventa_id' ORDER BY AI_datoventa_id ASC")or die($link->error);
$bh_del="DELETE FROM bh_nuevaventa WHERE nuevaventa_AI_user_id = '{$_COOKIE['coo_iuser']}'";
$link->query($bh_del) or die($link->error);

$prep_producto_value=$link->prepare("SELECT TX_producto_value FROM bh_producto WHERE AI_producto_id = ?")or die($link->error);

while($rs_datoventa=$qry_datoventa->fetch_array()){
	$prep_producto_value->bind_param("i",$rs_datoventa['datoventa_AI_producto_id']); $prep_producto_value->execute();$qry_producto_value=$prep_producto_value->get_result();
	$rs_producto_value=$qry_producto_value->fetch_array();
	ins_nuevaventa($rs_datoventa['datoventa_AI_producto_id'],$rs_datoventa['TX_datoventa_cantidad'],$rs_datoventa['TX_datoventa_precio'],$rs_datoventa['TX_datoventa_impuesto'],$rs_datoventa['TX_datoventa_descuento'],(!empty($rs_datoventa['TX_datoventa_descripcion']))?$rs_datoventa['TX_datoventa_descripcion']:$rs_producto_value['TX_producto_value'],$rs_datoventa['TX_datoventa_medida'],$rs_datoventa['TX_datoventa_promocion']);
};

function ins_nuevaventa($product,$cantidad,$precio,$itbm,$descuento,$descripcion,$medida,$promocion){
	$link = conexion();	$r_function = new recurrent_function();
 	$descripcion = $r_function->replace_regular_character($descripcion);
	$link->query("INSERT INTO bh_nuevaventa (nuevaventa_AI_user_id, nuevaventa_AI_producto_id, TX_nuevaventa_unidades, TX_nuevaventa_precio, TX_nuevaventa_itbm, TX_nuevaventa_descuento, TX_nuevaventa_descripcion, TX_nuevaventa_medida, TX_nuevaventa_promocion)
	VALUES ('{$_COOKIE['coo_iuser']}','$product','$cantidad','$precio','$itbm','$descuento','$descripcion','$medida','$promocion')");
}

$qry_nuevaventa=$link->query("SELECT bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_producto.TX_producto_cantidad, bh_nuevaventa.TX_nuevaventa_unidades, bh_nuevaventa.TX_nuevaventa_precio, bh_nuevaventa.TX_nuevaventa_itbm, bh_nuevaventa.TX_nuevaventa_descuento, bh_nuevaventa.nuevaventa_AI_producto_id, bh_nuevaventa.TX_nuevaventa_descripcion, bh_nuevaventa.AI_nuevaventa_id, bh_nuevaventa.TX_nuevaventa_medida, bh_nuevaventa.TX_nuevaventa_promocion
	FROM (bh_producto
	INNER JOIN bh_nuevaventa ON bh_producto.AI_producto_id = bh_nuevaventa.nuevaventa_AI_producto_id)
	WHERE bh_nuevaventa.nuevaventa_AI_user_id = '{$_COOKIE['coo_iuser']}' ORDER BY AI_nuevaventa_id ASC")or die($link->error);
$nr_nuevaventa=$qry_nuevaventa->num_rows;

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
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>


<script type="text/javascript">

$(document).ready(function() {

$(window).on('beforeunload', function(){
	clean_nuevaventa();
	close_popup();
});

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

$("#sel_client").css('display','none');
$("#txt_filterclient").focus(function(){
	$("#sel_client").show(500);
});
$("#txt_filterclient").blur(function(){
	$("#sel_client").hide(500);
});

$("#btn_sale").click(function(){
	window.location="sale.php";
});
$("#btn_stock").click(function(){
	window.location="stock.php";
});
$("#btn_addclient").click(function(){
	var name = $("#txt_filterclient").val();
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

$("#btn_refresh_tblproduct2sale").on("click",function(){
	$.ajax({	data: "", type: "GET", dataType: "text", url: "attached/get/get_tblproduct2sale.php",	})
	.done(function( data, textStatus, jqXHR ) {
		$("#container_tblproduct2sale").html(data);
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
});



$("#btn_guardar").click(function(){
	if($("#txt_filterclient").attr("alt") === ""){
		$("#txt_filterclient").css("border","inset 2px #F84C4C");
		$("#txt_filterclient").focus();
		return false;
	}
	$("#btn_guardar").attr("disabled", true);
	save_old_sale();
	return false;
})

$("#btn_salir").click(function(){
	clean_nuevaventa();
	setTimeout("history.back(1)",250);
});





$("#btn_statusbill").on("click",function(){
	upd_statusbill('<?php echo $rs_facturaventa['AI_facturaventa_id']; ?>');
});

$( function(){
	$("#txt_date").datepicker({
		changeMonth: true,
		changeYear: true
	});
});

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
$("#container_client_recall").css("display","none");


});

var upd_nuevaventa_descripcion = function(nuevaventa_id, old_descripcion){
	$.ajax({	data: "", type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
	.done(function( data, textStatus, jqXHR ) {
		if (data[0][0] === '') {
			return false
		}else{
			var n_description = prompt("Introduzca la nueva descripcion",old_descripcion);
			if (n_description.length > 100) {
				alert("La descripcion en muy larga");
				upd_nuevaventa_descripcion(nuevaventa_id,old_descripcion);
			}else{
				n_description = url_replace_regular_character(n_description);
				n_description = n_description.toUpperCase();
				$.ajax({	data: {"a" : nuevaventa_id, "b" : n_description }, type: "GET", dataType: "text", url: "attached/get/upd_nuevaventa_descripcion.php",	})
				.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
					$("#container_tblproduct2sale").html(data);
			})
				.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
			}
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}
function  unset_filterclient_oldsale(e){
	if (e.which === 13) {
		$("#btn_addclient").click();
	}else{
		$( "#txt_filterclient").prop("alt","");
	}

	$( function() {
		$( "#txt_filterclient").autocomplete({
			source: "attached/get/filter_client_sell.php",
			minLength: 2,
			select: function( event, ui ) {
							var n_val = ui.item.value;
								raw_n_val = n_val.split(" | Dir:");
								ui.item.value = raw_n_val[0];
				$("#txt_filterclient").prop('alt', ui.item.id);
				content = '<strong>Nombre:</strong> '+ui.item.value+' <strong>RUC:</strong> '+ui.item.ruc+' <strong>Tlf.</strong> '+ui.item.telefono+' <strong>Dir.</strong> '+ui.item.direccion.substr(0,20);
				fire_recall('container_client_recall', content)
			}
		});
	});
}
function open_product2sell(id){
	open_popup('popup_product2sell.php?a='+id+'', 'popup_product2sell','425','420');
}
function upd_precionuevaventa(product_id){
	$.ajax({ data: {"a" : "1"}, type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
	.done(function( data, textStatus, jqXHR ) {	if(data[0][0] != ""){
			var new_price = prompt("Ingrese el Nvo. Precio:");
			if (new_price === '' || isNaN(new_price)) {
				return false;
			}
			new_price = val_intw2dec(new_price);
			new_price = parseFloat(new_price);
			upd_priceproduct2sell(product_id,new_price);
		}	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
}
function upd_unidadesnuevaventa(product_id){
	var new_quantity = prompt("Ingrese la cantidad:");
	if (new_quantity === '' || isNaN(new_quantity)) {
		return false;
	}
	new_quantity = val_intw2dec(new_quantity);
	upd_quantityproduct2sell(product_id,new_quantity);
}
function upd_descripcion_nuevaventa(product_id){
	$.ajax({	data: "", type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
	.done(function( data, textStatus, jqXHR ) {
		if (data[0][0] === '') {
			return false
		}else{
			var n_description = prompt("Introduzca la nueva descripcion");
			if (n_description.length > 100) {
				alert("La descripcion en muy larga");
				upd_descripcion_nuevaventa(product_id);
			}else{
				var activo = $(".tab-pane.active").attr("id");
				$.ajax({	data: {"a" : product_id, "b" : n_description, "c" : activo, "d" : 'descripcion', "z" : 'upd' }, type: "GET", dataType: "text", url: "attached/php/method_nuevaventa.php",	})
				.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
					if(data){
					data = JSON.parse(data);
					generate_tbl_nuevaventa(data,activo);
				}
				})
				.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
			}
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});

}
</script>

</head>

<body>

<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-2">
  	<div id="logo"></div>
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
<div class="container-fluid" > <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 bg_red" id="div_title"><h2>Modificar Cotizaci&oacute;n</h2></div></div>
<div id="container_complementary" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_txtdate" class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
    	<label class="label label_blue_sky" for="txt_date">Fecha:</label>
	    <input type="text" class="form-control" id="txt_date" name="txt_date" readonly="readonly"
        value="<?php
		$pre_date=strtotime($rs_facturaventa['TX_facturaventa_fecha']);
		echo $date=date('m/d/Y',$pre_date); ?>" />
    </div>
	<div id="container_txtnumero" class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
    	<label class="label label_blue_sky" for="txt_numero">Numero:</label>
	    <input type="text" class="form-control" id="txt_numero" name="txt_numero" readonly="readonly"
        value="<?php echo $rs_facturaventa['TX_facturaventa_numero']; ?>" />
    </div>
	<div id="container_txtvendedor" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
    	<label class="label label_blue_sky" for="txt_vendedor">Vendedor:</label>
        <?php if($_COOKIE['coo_tuser'] > 2){ ?>
	    <input type="text" class="form-control" alt="<?php echo $rs_vendor['AI_user_id']; ?>" id="txt_vendedor" name="txt_vendedor" readonly="readonly"
        value="<?php echo $rs_vendor['TX_user_seudonimo']; ?>" />
        <?php }else{ ?>
        <select id="txt_vendedor" class="form-control" onchange="upd_vendor(this.value,'<?php echo $rs_facturaventa['AI_facturaventa_id']; ?>');">
        <?php
		$qry_user=$link->query("SELECT AI_user_id, TX_user_seudonimo FROM bh_user")or die($link->error);
		$rs_user=$qry_user->fetch_array();
			do{
		 ?>
         <?php
		 if($rs_user['AI_user_id'] === $rs_vendor['AI_user_id']){
			 echo "<option value='{$rs_user['AI_user_id']}' selected='selected'>{$rs_user['TX_user_seudonimo']}</option>";
		 }else{
			 echo "<option value='{$rs_user['AI_user_id']}'>{$rs_user['TX_user_seudonimo']}</option>";
		 }
		 ?>
        <?php
			}while($rs_user=$qry_user->fetch_array());
		?>
        </select>
		<?php
        }
		?>
    </div>
	<div id="container_txtvendedor" class="col-xs-3 col-sm-3 col-md-2 col-lg-2">
    	<label class="label label_blue_sky" for="txt_vendedor">Status:</label>
	    <input type="text" class="form-control" id="txt_status" name="txt_status" readonly="readonly"
        value="<?php echo $rs_facturaventa['TX_facturaventa_status']; ?>" />
    </div>
</div>
<div id="container_client" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_txtfilterclient" class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
    	<label class="label label_blue_sky" for="txt_filterclient">Cliente:</label>
	    <input type="text" class="form-control" alt="<?php echo $rs_facturaventa['facturaventa_AI_cliente_id']; ?>" id="txt_filterclient" name="txt_filterclient" onkeyup="unset_filterclient_oldsale(event)" value="<?php echo $rs_facturaventa['TX_cliente_nombre']; ?>" />
    </div>
	<div id="container_btnaddclient" class="col-xs-1 col-sm-1 col-md-1 col-lg-1 side-btn-md-label">
		<button type="button" id="btn_addclient" class="btn btn-success"><strong><i class="fa fa-wrench" aria-hidden="true"></i></strong></button>
	</div>
	<div id="container_client_recall" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	</div>
</div>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_txtobservation" class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
		<label class="label label_blue_sky" for="txt_observation">Observaciones:</label>
		<input type="text" class="form-control" id="txt_observation" name="txt_observation" value="<?php echo $rs_facturaventa['TX_facturaventa_observacion']; ?>" />
	</div>
	<div id="container_btnrefreshtblproduct2sale" class="col-xs-1 col-sm-1 col-md-1 col-lg-1 side-btn-md-label">
			<button type="button" id="btn_refresh_tblproduct2sale" class="btn btn-info btn-md" title="Refrescar Tabla">
	    <strong><i class="fa fa-refresh fa-spin fa-1x fa-fw"></i><span class="sr-only"></span></strong>
	    </button>
	</div>
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
			$rs_nuevaventa=$qry_nuevaventa->fetch_array(MYSQLI_ASSOC);

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

				$style_promotion = ($rs_nuevaventa['TX_nuevaventa_promocion'] > 0 ) ? 'style="color: #f86e6e; background-color: #f2ffef; text-shadow: 0.5px 0.5px #f37e7e80;"' : '';
				$fire_promotion = ($rs_nuevaventa['TX_nuevaventa_promocion'] > 0 ) ? '<i class="fa fa-free-code-camp"> </i> ' : '';

			?>

					<tr <?php echo $style_promotion; ?>>
						<td><?php echo $rs_nuevaventa['TX_producto_codigo']; ?></td>
						<td onclick="upd_nuevaventa_descripcion(<?php echo $rs_nuevaventa['AI_nuevaventa_id'];?>,'<?php echo $r_function->replace_special_character($rs_nuevaventa['TX_nuevaventa_descripcion']);?>')"><?php echo $fire_promotion.$r_function->replace_special_character($rs_nuevaventa['TX_nuevaventa_descripcion']); ?></td>
						<td><?php echo $raw_medida[$rs_nuevaventa['TX_nuevaventa_medida']]; ?></td>
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
			<?php }while($rs_nuevaventa=$qry_nuevaventa->fetch_array(MYSQLI_ASSOC)); ?>
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
		<label class="label label_blue_sky" for="txt_filterproduct">Buscar:</label>
    <input type="text" class="form-control" id="txt_filterproduct" name="txt_filterproduct" autocomplete="off" onkeyup="filter_product_sell(this);" />
	</div>
	<div id="container_limit" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
		<label class="label label_blue_sky" for="txt_rlimit">Mostrar:</label><br />
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
    <thead>
  	<tr>
    	<th class="bg-primary col-xs-2 col-sm-2 col-md-1 col-lg-1">
      	Codigo
      </th>
      <th class="bg-primary col-xs-8 col-sm-8 col-md-8 col-lg-8">
      	Nombre
      </th>
    	<th class="bg-primary col-xs-2 col-sm-2 col-md-1 col-lg-1">
      	Cantidad
      </th>
    	<th class="bg-primary col-xs-2 col-sm-2 col-md-1 col-lg-1">
      	Precio
      </th>
      <th class="bg-primary col-xs-2 col-sm-2 col-md-1 col-lg-1">
      	Letra
      </th>
    </tr>
    </thead>
    <tfoot>
	    <tr>
    		<td class="bg-primary">  </td>
    		<td class="bg-primary">  </td>
    		<td class="bg-primary">  </td>
        <td class="bg-primary">  </td>
        <td class="bg-primary">  </td>
    	</tr>
    </tfoot>
    <tbody>
    <?php do{ ?>
    	<tr onclick="javascript:open_product2sell(<?php echo $rs_product['AI_producto_id']; ?>);">
        	<td title="<?php echo $rs_product['AI_producto_id']; ?>">
            <?php echo $rs_product['TX_producto_codigo']; ?>
          </td>
        	<td><?php echo $rs_product['TX_producto_value']; ?></td>
        	<td><?php echo $rs_product['TX_producto_cantidad']; ?></td>
        	<td>
<?php 			$qry_precio = $link->query("SELECT TX_precio_cuatro FROM bh_precio WHERE precio_AI_producto_id = '{$rs_product['AI_producto_id']}' AND TX_precio_inactivo = '0'")or die($link->error);
						$rs_precio=$qry_precio->fetch_array();
						echo $rs_precio['TX_precio_cuatro']; ?>
          </td>
          <td>
<?php 			$qry_letra=$link->query("SELECT TX_letra_value FROM bh_letra WHERE AI_letra_id = '{$rs_product['producto_AI_letra_id']}'")or die($link->error);
      			$rs_letra = $qry_letra->fetch_array();
						echo $rs_letra['TX_letra_value']; ?>
          </td>
        </tr>
    <?php }while($rs_product=$qry_product->fetch_array()); ?>
    </tbody>
    </table>
	</div>
</div>
<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <button type="button" id="btn_facturar" class="btn btn-success" disabled="disabled">Facturar</button>
    &nbsp;&nbsp;&nbsp;
    <button type="button" id="btn_guardar" class="btn btn-primary">Guardar</button>
    &nbsp;&nbsp;&nbsp;    <button type="button" id="btn_salir" class="btn btn-warning">Volver</button>
    <?php if($_COOKIE['coo_tuser'] == '1' || $_COOKIE['coo_tuser'] == '2' || !empty($_SESSION['admin'])){ ?>
    <?php 	if($rs_facturaventa['TX_facturaventa_status'] == 'INACTIVA'){ ?>
                &nbsp;&nbsp;&nbsp;
                <button type="button" id="btn_statusbill" class="btn btn-danger">Activar F.</button>
    <?php 	}else if($rs_facturaventa['TX_facturaventa_status'] == 'FACTURADA'){ ?>
                &nbsp;&nbsp;&nbsp;
                <button type="button" id="btn_statusbill" class="btn btn-danger">Activar F.</button>
    <?php 	}else if($rs_facturaventa['TX_facturaventa_status'] == 'ACTIVA'){ ?>
                &nbsp;&nbsp;&nbsp;
                <button type="button" id="btn_statusbill" class="btn btn-danger">Desactivar F.</button>
    <?php 	} ?>
    <?php } ?>
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
