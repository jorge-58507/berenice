<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_stock.php';

$fecha_limite=date('Y-m-d', strtotime("-1 week",strtotime(date('Y-m-d'))));
$qry_order=$link->query("SELECT bh_user.TX_user_seudonimo, bh_proveedor.TX_proveedor_nombre, bh_pedido.AI_pedido_id, bh_pedido.TX_pedido_numero, bh_pedido.TX_pedido_fecha, bh_pedido.TX_pedido_status FROM ((bh_pedido INNER JOIN bh_user ON bh_user.AI_user_id = bh_pedido.pedido_AI_user_id) INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_pedido.pedido_AI_proveedor_id) WHERE TX_pedido_fecha >= '$fecha_limite' ORDER BY bh_pedido.TX_pedido_numero DESC")or die($link->error);
$qry_provider=$link->query("SELECT AI_proveedor_id, TX_proveedor_nombre FROM bh_proveedor");
$qry_product=$link->query("SELECT bh_producto.AI_producto_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_cantidad, bh_producto.TX_producto_exento FROM bh_producto ORDER BY TX_producto_value LIMIT 10");
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
	$("#txt_price").on("blur",function(){	this.value = val_intw2dec(this.value);	})
	$("#txt_quantity").on("blur",function(){	this.value = val_intw2dec(this.value);	})
	$("#btn_add_provider").on("click",function(){
		open_popup('popup_addprovider.php','popup_addprovider','425','420');
	})
	$("#container_neworder").css("display","none");
	$("#container_orderinfo").css("display","none");

	$("#txt_product").on("keyup",function(){
		$.ajax({	data: {	"a" : this.value	},	type: "GET",	dataType: "text",	url: "attached/get/filter_order_product.php", })
		 .done(function( data, textStatus, jqXHR ) {	$("#tbl_product tbody").html( data );	})
		 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD " + textStatus);	});
	})

	$('#btn_save_neworder').click(function(){
		if ($("#txt_filterprovider").attr("alt") == "") {
			$("#txt_filterprovider").css("box-shadow","inset 0px 0px 2px red");
			$("#txt_filterprovider").focus();
			return false;
		}$("#txt_filterprovider").css("box-shadow","none");

		if(raw_product.length ===	0){
			return false;
		}
		var ans = confirm("¿Desea Imprimir el Documento?");
		if (ans) {
			$.ajax({	data: {	"a" : this.value	},	type: "GET",	dataType: "text",	url: "attached/get/get_next_order.php", })
			 .done(function( data, textStatus, jqXHR ) {
				 var href = "print_order_html.php?a=" + data;
					 save_neworder();
					 setTimeout(function(){ print_html(href); },500);
			 	})
			 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD " + textStatus);	});
		}else{
			$.ajax({	data: {	"a" : this.value	},	type: "GET",	dataType: "text",	url: "attached/get/get_next_order.php", })
			 .done(function( data, textStatus, jqXHR ) {
					 save_neworder();
			 	})
			 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD " + textStatus);	});
		}
	})
	$("#btn_cancel").on("click",function(){
		history.back(1);
	})
	$("#txt_filterorder").on("keyup",function(){
		var limit = ($("input[name=r_limit]:checked").val());
		$.ajax({	data: {	"a" : this.value, "b" : limit	},	type: "GET",	dataType: "text",	url: "attached/get/filter_order.php", })
		 .done(function( data, textStatus, jqXHR ) {
			 $("#tbl_order tbody").html(data);
			})
		 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD " + textStatus);	});
	})

	$('#txt_product').validCampoFranz('.0123456789 abcdefghijklmnopqrstuvwxyz-/');
	$('#txt_filterprovider').validCampoFranz('.0123456789 abcdefghijklmnopqrstuvwxyz');
	$('#txt_quantity').validCampoFranz('.0123456789');
	$('#txt_price').validCampoFranz('.0123456789');
	$('#div_neworder').on("click",function(){
		$('#container_neworder').toggle("slow");
		$('#i_neworder').toggleClass("fa-angle-double-down");
		$('#container_div_neworder').toggleClass("border-top");
	})
	$("#btn_cancel_neworder").on("click", function(){
		location.reload();
	})
	$("#txt_price").on("keyup",function(e){
		if(e.which === 13){
			$("#txt_price").blur();
			$("#btn_add").click();
		}
	})

	$("#txt_filterprovider").on("keyup", function(e){
	  (e.which === 13) ?	$("#btn_add_provider").click() :	$( "#txt_filterprovider").prop("alt","");
	  $( function() {
	    $( "#txt_filterprovider").autocomplete({
	      source: "attached/get/filter_check_provider.php",
	      minLength: 2,
	      select: function( event, ui ) {
	        $("#txt_filterprovider").prop('alt', ui.item.id);
	        content = '<strong>Nombre:</strong> '+ui.item.value+' <strong>Tlf.</strong> '+ui.item.telefono+' <strong>Dir.</strong> '+ui.item.direccion.substr(0,20);
	        fire_recall('container_provider_recall', content)
	      }
	    });
	  });
	})




});
function set_order_info(id,codigo,detalle,impuesto){
	$("#container_orderinfo").show(500);
	$("#hd_product_id").val(id);
	$("#hd_product_codigo").val(codigo);
	$("#hd_product_nombre").val(detalle);
	$("#hd_product_impuesto").val(impuesto);
	$("#txt_quantity").focus();
}

var raw_product = [];
var i = -1;
function add_rawproduct(product_id, product_codigo, product_nombre, cantidad, precio, impuesto){
	i = i + 1;
	$("#container_orderinfo").hide(500);
	var product_id = $("#hd_product_id").val();
	var product_codigo = $("#hd_product_codigo").val();
	var product_nombre = $("#hd_product_nombre").val();
	var cantidad = $("#txt_quantity").val();
	var precio = ($("#txt_price").val() != '') ? $("#txt_price").val() : '0.00';
	var impuesto = $("#hd_product_impuesto").val();
	if(!val_intw2dec(cantidad)){ return false;	}
	if(!val_intw2dec(precio)){ return false;	}
	var tr_product = new Object();
	tr_product['id'] = product_id;
	tr_product['codigo'] = product_codigo;
	tr_product['nombre'] = product_nombre;
	tr_product['cantidad'] = cantidad;
	tr_product['precio'] = precio;
	tr_product['impuesto'] = impuesto;
	if(raw_product[i]){
		add_rawproduct(product_id,product_codigo,product_nombre,cantidad,precio,impuesto);
	}else{
		raw_product[i] = tr_product;
		$("#hd_product_id, #hd_product_codigo, #hd_product_nombre, #hd_product_impuesto, #txt_quantity, #txt_price").val("");
	}
	// console.log(raw_product);
	print_rawproduct(raw_product);
}
function print_rawproduct(obj){
	console.log(obj);
	var content_tbody=""
	var largo = obj.length+1;
	var total_neworder=0;
	for(var it in obj){
		if(obj[it]){
			content_tbody = content_tbody+"<tr><td>"+obj[it]['codigo']+"</td><td>"+obj[it]['nombre']+"</td><td>"+obj[it]['cantidad']+"</td><td>"+obj[it]['precio']+"</td><td><button type='button' name='"+it+"' class='btn btn-danger btn-xs' onclick='javascript: remove_rawproduct(this.name)'>X</button></td></tr>";
			imp = (obj[it]['impuesto']*obj[it]['precio'])/100;
			pre_imp = parseFloat(obj[it]['precio'])+imp;
			console.log(pre_imp);
			subtotal = obj[it]['cantidad']*pre_imp;
			total_neworder = total_neworder+subtotal;
		}
	}
	if(content_tbody === ""){
		content_tbody = "<tr><td></td><td></td><td></td><td></td><td></td></tr>";
	}
	$("#tbl_product2buy tbody").html(content_tbody);
	$("#span_neworder_total").html(total_neworder.toFixed(2));
}
function remove_rawproduct(index){
	raw_product.splice(index,1)
	print_rawproduct(raw_product);
}
function save_neworder(){
	$.ajax({	data: {	"a" : raw_product, "b" : $("#txt_filterprovider").attr("alt")	},	type: "GET",	dataType: "text",	url: "attached/get/plus_neworder.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 console.log("GOOD " + textStatus);
		 $("#tbl_order tbody").html(data);
	 })
	 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD " + textStatus);	});
	 raw_product.length = 0;
	 $("#txt_filterprovider").attr("alt",""); $("#txt_filterprovider").val("");
	 print_rawproduct(raw_product);
}
function show_datopedido(tr){
	$("#"+tr+"").toggle("fast");
}
function open_process_order(order_id){
	open_popup("popup_process_order.php?a="+order_id,"","1000","420");
}
function upd_order_sended(order_id){
	$.ajax({	data: {"a" : order_id},	type: "GET",	dataType: "text",	url: "attached/get/upd_order_sended.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 console.log("GOOD " + textStatus);
		 $("#tbl_order tbody").html(data);
	 })
	 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD " + textStatus);	});
}
function del_order(order_id){
	$.ajax({	data: {"a" : order_id},	type: "GET",	dataType: "text",	url: "attached/get/del_order.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 console.log("GOOD " + textStatus);
		 $("#tbl_order tbody").html(data);
	 })
	 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD " + textStatus);	});
}

function upd_order(order_id){
	$.ajax({	data: {"a" : order_id},	type: "GET",	dataType: "text",	url: "attached/get/get_order.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 console.log("Good " + textStatus);
		 // console.log("data "+data);
		 data = JSON.parse(data);
		 raw_product = data;
		 print_rawproduct(data);
  })
	 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD " + textStatus);	});
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
<form name="form_inventory" id="form_inventory" method="post">
<div id="container_neworder" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
	<div id="container_provider" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_txtprovider"class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
			<label for="txt_filterprovider">Proveedor</label>
			<input type="text" id="txt_filterprovider" alt="" class="form-control" />
		</div>
		<div id="container_btnaddprovider" class="col-xs-1 col-sm-1 col-md-1 col-lg-1 side_btn-md">
			<button type="button" id="btn_add_provider" class="btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i></button>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="container_provider_recall">

		</div>
	</div>
	<div id="container_orderinfo"class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
		<input type="hidden" id="hd_product_id" />
		<input type="hidden" id="hd_product_codigo" />
		<input type="hidden" id="hd_product_nombre" />
		<input type="hidden" id="hd_product_impuesto" />
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<h3>Introduzca los Datos</h3>
		</div>
		<div id="container_txtquantity"class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			<label for="txt_quantity">Cantidad</label>
			<input type="text" id="txt_quantity" class="form-control" />
		</div>
		<div id="container_txtprice"class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			<label for="txt_price">Precio Neto</label>
			<input type="text" id="txt_price" class="form-control" placeholder="0.00" />
		</div>
		<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<button type="button" id="btn_add" class="btn btn-success btn-sm"  onclick="add_rawproduct();"><i class="fa fa-plus fa-2x"></i></button>
			&nbsp;&nbsp;
			<button type="button" id="btn_close" class="btn btn-warning btn-sm" onclick="$('#container_orderinfo').hide(500)" name="btn_close"><i class="fa fa-ban fa-2x"></i></button>
		</div>
	</div>
	<div id="container_product" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		<div id="container_txtproduct" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label for="txt_product">Producto:</label>
			<input type="text" id="txt_product" class="form-control"/>
		</div>
		<div id="container_tblproduct" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<table id="tbl_product" class="table table-bordered table-condensed table-striped table-hover">
				<caption>Listado de Productos</caption>
				<thead class="bg_green">
					<tr>
						<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">C&oacute;digo</th>
						<th class="col-xs-6 col-sm-6 col-md-6 col-lg-6">Detalle</th>
						<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Existencia</th>
					</tr>
				</thead>
        <tbody>
					<?php if($qry_product->num_rows === 0){ ?>
	        <tr>
	        	<td></td>
	        	<td></td>
	          <td></td>
	        </tr>
<?php 		}else{
						while ($rs_product=$qry_product->fetch_array()) { 	?>
				<tr onclick="set_order_info('<?php echo $rs_product[0]; ?>','<?php echo $rs_product[1]; ?>','<?php echo str_replace("'","\'",$rs_product[2]); ?>', '<?php echo $rs_product['TX_producto_exento']; ?>')">
					<td><?php echo $rs_product[1]; ?></td>
					<td><?php echo $rs_product[2]; ?></td>
					<td><?php echo $rs_product[3]; ?></td>
				</tr>
<?php 		}
 					} ?>
        </tbody>
				<tfoot>
					<tr class="bg_green">
						<td></td><td></td><td></td>
					</tr>
				</tfoot>
      </table>
		</div>
	</div>
	<div id="container_product2buy" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		<div id="container_tblproduct2buy" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<table id="tbl_product2buy" class="table table-bordered table-condensed table-striped table-hover">
				<caption>Productos a Comprar</caption>
				<thead class="bg-success">
					<tr>
						<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">C&oacute;digo</th>
						<th class="col-xs-6 col-sm-6 col-md-6 col-lg-6">Detalle</th>
						<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
						<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Precio</th>
						<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2"></th>
					</tr>
				</thead>
        <tbody>
	        <tr>
	        	<td></td>
	        	<td></td>
	          <td></td>
						<td></td>
						<td></td>
	        </tr>
        </tbody>
				<tfoot>
					<tr class="bg-success">
						<td></td><td></td><td colspan="2"><strong>Total:</strong> B/ <span id="span_neworder_total"></span></td>
						<td></td>
					</tr>
				</tfoot>
      </table>
		</div>
		<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<button type="button" id="btn_save_neworder" class="btn btn-success btn-md">Guardar</button>
			&nbsp;&nbsp;
			<button type="button" id="btn_cancel_neworder" class="btn btn-warning btn-md">Cancelar</button>
		</div>
	</div>
</div>
<div id="container_order" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
		<div id="container_div_neworder" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
			<div id="div_neworder">
				<i id="i_neworder"class="fa fa-angle-double-down fa-angle-double-up" aria-hidden="true"></i>&nbsp;Nva. Orden</div>
		</div>
	</div>
	<div id="container_filterorder" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_txtfilterorder" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			<label for="txt_filterorder">Buscar:</label>
			<input type="text" id="txt_filterorder" name="" class="form-control" placeholder="Usuario, Proveedor o Numero de Orden " />
		</div>
		<div id="container_rlimit" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
			<label for="r_limit">Mostrar:</label><br />
			<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="10" checked="checked"/> 10</label>
			<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="50" /> 50</label>
			<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="" /> Todas</label>
		</div>
	</div>
	<div id="container_tblorder" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
		<table id="tbl_order" class="table table-bordered table-striped table-condensed table-hover">
		<thead class="bg-primary">
		<tr>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Usuario</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Orden Nº</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
			<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Proveedor</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Status</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2"></th>
		</tr>
		</thead>
		<tbody>
		<?php
		if($qry_order->num_rows > 0){
			$qry_datopedido = $link->prepare("SELECT bh_producto.TX_producto_value, bh_datopedido.TX_datopedido_cantidad, bh_datopedido.TX_datopedido_precio, bh_datopedido.datopedido_AI_pedido_id, bh_producto.TX_producto_exento
				FROM (bh_datopedido
					INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datopedido.datopedido_AI_producto_id)
				WHERE bh_datopedido.datopedido_AI_pedido_id = ? ");
			while ($rs_order=$qry_order->fetch_array()) {
		?>

		<tr onclick="show_datopedido('order_<?php echo $rs_order[2] ?>')">
			<td><?php echo $rs_order[0]; ?></td>
			<td><?php echo $rs_order[3]; ?></td>
			<td><?php echo date('d-m-Y',strtotime($rs_order[4])); ?></td>
			<td><?php echo $rs_order[1]; ?></td>
			<td><?php
			switch ($rs_order[5]) {
				case 'ACTIVO':
					$font_color="#e9ca2f";
					break;
				case 'ENVIADO':
					$font_color="#57afdb";
					break;
				case 'RECIBIDO':
					$font_color="#67b847";
					break;
				case 'CANCELADO':
					$font_color="#b54a4a";
					break;
			}
			 echo "<span style='color:".$font_color.";font-weight: bolder;'>".$rs_order[5]."</span>"; ?>
		 	</td>
			<td>
				<?php switch ($rs_order[5]) {
		      case 'ACTIVO':
		        $button = '<button type="button" id="btn_sended" name="'.$rs_order[2].'" onclick="upd_order_sended(this.name);" class="btn btn-info btn-xs">
		        <i class="fa fa-upload" aria-hidden="true"></i>
		        Enviado</button>';
		        break;
		      case 'ENVIADO':
		        $button =	'<button type="button" id="btn_process" name="'.$rs_order[2].'" onclick="open_process_order(this.name);" class="btn btn-success btn-xs">
		        <i class="fa fa-upload fa-flip-vertical" aria-hidden="true"></i>
		        Recibido</button>';
		        break;
		      default:
		        $button = '<button type="button" id="" class="btn btn-default btn-xs" disabled="disabled"><i  class="fa fa-ban" aria-hidden="true"></i>	Recibido</button>';
		    }
		    echo $button;
		    ?>
			</td>
			<td>
				<button type="button" id="btn_upd" name="" title="Duplicar" onclick="upd_order('<?php echo $rs_order[2]; ?>');" class="btn btn-warning btn-sm"><i class="fa fa-copy" aria-hidden="true"></i></button>
				<button type="button" id="btn_process" name="" title="Imprimir"  onclick="print_html('print_order_html.php?a=<?php echo $rs_order[2]; ?>');" class="btn btn-info btn-sm"><i class="fa fa-print" aria-hidden="true"></i></button>
<?php 	if($rs_order[5] === 'ACTIVO'){	?>
					<button type="button" id="btn_delete" name="" title="Eliminar" onclick="del_order('<?php echo $rs_order[2]; ?>');" class="btn btn-danger btn-sm"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
<?php 	} ?>
			</td>
		</tr>

		<tr id="order_<?php echo $rs_order[2] ?>" style="	display: none;">
			<td></td>
			<td colspan="4">

				<table id="tbl_datopedido" class="table table-bordered tbl-padding-0" style="border:solid 2px #ccc;">
					<thead class="bg-info">
					<tr>
						<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Cantidad</th>
						<th class="col-xs-7 col-sm-7 col-md-7 col-lg-7">Detalle</th>
						<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Precio</th>
					</tr>
					</thead>
					<tbody>
<?php
			$total=0;
			$qry_datopedido->bind_param("i",$rs_order[2]);
			$qry_datopedido->execute();
			$result = $qry_datopedido->get_result();
			while ($rs_datopedido=$result->fetch_array()) {
				$impuesto4product = ($rs_datopedido['TX_producto_exento']*$rs_datopedido[2])/100;
				$precio_impuesto=$rs_datopedido[2]+$impuesto4product;
				$precio4product = $precio_impuesto*$rs_datopedido[1];
				$total+=$precio4product;
	?>
			<tr>
				<td><?php echo $rs_datopedido[1]; ?></td>
				<td><?php echo $rs_datopedido[0]; ?></td>
				<td><?php echo number_format($precio_impuesto,4); ?></td>
			</tr>
			<?php
			}
			?>
					</tbody>
					<tfoot class="bg-info">
					<tr>
						<td></td>
						<td></td>
						<td><strong>B/ <?php echo number_format($total,2); ?></strong></td>
					</tr>
					</tfoot>
				</table>

			</td>
		</tr>
			<?php
			}
		}else{	?>
		<tr>
			<td>&nbsp;</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<?php
		}
		?>
		</tbody>
		<tfoot class="bg-primary">
			<tr>
				<td>&nbsp;</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</tfoot>
		</table>
	</div>
	<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<button type="button" id="btn_cancel" class="btn btn-warning btn-md">Volver</button>
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
