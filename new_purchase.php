<?php
require 'bh_conexion.php';
$link=conexion();
date_default_timezone_set('America/Panama');

require 'attached/php/req_login_stock.php';
$proveedor="";
$pedido_numero="";

if(isset($_GET['a'])){
	$qry_datopedido = $link->prepare("SELECT bh_producto.AI_producto_id,	bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_producto.TX_producto_exento, bh_datopedido.TX_datopedido_revisado, bh_datopedido.TX_datopedido_precio, bh_datopedido.datopedido_AI_pedido_id FROM (bh_datopedido INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datopedido.datopedido_AI_producto_id) WHERE bh_datopedido.AI_datopedido_id = ? ")or die($link->error);
	$insert_new_purchase = $link->prepare("INSERT INTO bh_nuevacompra (nuevacompra_AI_user_id, nuevacompra_AI_producto_id, TX_nuevacompra_unidades, TX_nuevacompra_precio, TX_nuevacompra_itbm, TX_nuevacompra_descuento, TX_nuevacompra_medida) VALUES ('{$_COOKIE['coo_iuser']}', ?, ?, ?, ?, '0','1')")or die($link->error);
	$json_producto=$_GET['a'];
	$raw_producto = json_decode($json_producto);
	foreach ($raw_producto as $datopedido_id => $pedido_cantidad) {
		$qry_datopedido->bind_param("i",$datopedido_id);
		$qry_datopedido->execute()or die($link->error);
		$result = $qry_datopedido->get_result();
		$rs_datopedido = $result->fetch_array();
		$insert_new_purchase->bind_param("issd", $rs_datopedido['AI_producto_id'], $rs_datopedido['TX_datopedido_revisado'], $rs_datopedido['TX_datopedido_precio'], $rs_datopedido['TX_producto_exento'])or die($link->error);
		$insert_new_purchase->execute()or die("error ".$link->error);
	}
	$pedido_id=$rs_datopedido['datopedido_AI_pedido_id'];
	$qry_pedido=$link->query("SELECT TX_pedido_numero, pedido_AI_proveedor_id FROM bh_pedido WHERE AI_pedido_id = '$pedido_id'");
	$rs_pedido=$qry_pedido->fetch_array();
	$pedido_numero=$rs_pedido['TX_pedido_numero'];
	$proveedor=$rs_pedido['pedido_AI_proveedor_id'];
}
$qry_warehouse=$link->query("SELECT AI_almacen_id, TX_almacen_value FROM bh_almacen")or die($link->error);
$rs_warehouse=$qry_warehouse->fetch_array();

$qry_product=$link->query("SELECT AI_producto_id, TX_producto_codigo, TX_producto_value, TX_producto_referencia, TX_producto_cantidad, TX_producto_activo FROM bh_producto ORDER BY TX_producto_value ASC LIMIT 15")or die($link->error);
$rs_product=$qry_product->fetch_array();

$qry_newpurchase=$link->query("SELECT bh_nuevacompra.AI_nuevacompra_id, bh_nuevacompra.nuevacompra_AI_producto_id, bh_nuevacompra.TX_nuevacompra_unidades, bh_nuevacompra.TX_nuevacompra_precio, bh_nuevacompra.TX_nuevacompra_itbm, bh_nuevacompra.TX_nuevacompra_descuento, bh_producto.AI_producto_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_producto.TX_producto_cantidad, bh_nuevacompra.TX_nuevacompra_p4, bh_nuevacompra.TX_nuevacompra_medida
	FROM (bh_nuevacompra
	INNER JOIN bh_producto ON bh_nuevacompra.nuevacompra_AI_producto_id = bh_producto.AI_producto_id)
	WHERE bh_nuevacompra.nuevacompra_AI_user_id = '{$_COOKIE['coo_iuser']}'")or die($link->error);
$rs_newpurchase=$qry_newpurchase->fetch_array();

$qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida")or die($link->error);
$raw_medida = array();
while($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)){
	$raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
}
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
<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="attached/css/newpurchase_css.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="attached/css/font-awesome.css" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/newpurchase_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	var raw_facturacompra = new Object();
			raw_facturacompra.numero = '';
			raw_facturacompra.orden='';
			raw_facturacompra.observacion='';
			raw_facturacompra.almacen='1';
			raw_facturacompra.proveedor='';
			raw_facturacompra.proveedor_nombre='';
<?php
			if(isset($_GET['b'])){
				$json_facturacompra=$_GET['b'];?>
				var raw_facturacompra = <?php echo $json_facturacompra; ?>;
<?php	}	 ?>
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

$("#btn_add_provider").click(function(){
	open_addprovider();
})
$("#btn_insert").click(function(){
	var	provider = $("#txt_filterprovider").val();
	var	billnumber = $("#txt_billnumber").val();
	var chk_product = $("#tbl_newentry tbody tr td").html();

	if(provider ===	""){ set_bad_field("txt_filterprovider"); $("#txt_filterprovider").focus(); return false; }else{ set_good_field("txt_filterprovider"); }
	if(billnumber ===	""){ set_bad_field("txt_billnumber"); $("#txt_billnumber").focus(); return false; }else{ set_good_field("txt_billnumber"); }
	if(chk_product ===	""){ set_bad_field("txt_filterproduct"); $("#txt_filterproduct").focus(); return false; }else{ set_good_field("txt_filterproduct"); }

	$.ajax({	data: { "a" : provider, "b" : billnumber	},	type: "GET",	dataType: "text",	url: "attached/get/get_invoice.php", })
	.done(function( data, textStatus, jqXHR ) {  console.log("GOOD" + textStatus);
	 	if(data === '0'){ save_invoice(0);	}else{	alert("La Factura "+billnumber+" de "+$("#txt_filterprovider option:selected").text()+" ya existe.");	}	 })
	.fail(function( jqXHR, textStatus, errorThrown ) {		});
})
$("#btn_save").click(function(){
	var	provider = $("#txt_filterprovider").val();
	var	billnumber = $("#txt_billnumber").val();
	var chk_product = $("#tbl_newentry tbody tr td").html();

	if(provider ===	""){ $("#txt_filterprovider").css("border","inset 2px #cc3300"); $("#txt_filterprovider").focus(); return false; }
	if(billnumber ===	""){ $("#txt_billnumber").css("border","inset 2px #cc3300"); $("#txt_billnumber").focus(); return false; }
	if(chk_product ===	""){ $("#txt_filterproduct").css("border","inset 2px #cc3300"); $("#txt_filterproduct").focus(); return false; }

	$.ajax({	data: { "a" : provider, "b" : billnumber	},	type: "GET",	dataType: "text",	url: "attached/get/get_invoice.php", })
	 .done(function( data, textStatus, jqXHR ) {  console.log("GOOD" + textStatus);
	 	if(data === '0'){ save_invoice(1);	}else{	alert("La Factura "+billnumber+" de "+$("#txt_filterprovider option:selected").text()+" ya existe.");	}	 })
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
})
$("#btn_cancelar").click(function(){	clean_product2purchase();	})
$( function() {
	$("#txt_date").datepicker({
		changeMonth: true,
		changeYear: true
	});
});
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
$("#ta_observation").on("keyup",function(){
	this.value = this.value.toUpperCase();
	chk_txtobservation(this.value);
	var count = this.value.length;
	rest = 200-count;
	$("#span_taobservation").html(rest);
});

$("#btn_addproduct").on("click", function(){
	open_popup('popup_addproduct.php','_popup',520,509);
})


$("#txt_billnumber").validCampoFranz('.0123456789 abcdefghijklmnopqrstuvwxyz');
$("#txt_purchaseorder").validCampoFranz('.0123456789 abcdefghijklmnopqrstuvwxyz');
$("#ta_observation").validCampoFranz('.0123456789 abcdefghijklmnopqrstuvwxyz-*#');

$("#txt_billnumber").val(raw_facturacompra['numero']);
$("#txt_purchaseorder").val(raw_facturacompra['orden']);
$("#ta_observation").val(raw_facturacompra['observacion']);
$("#sel_warehouse").val(raw_facturacompra['almacen']);
$("#txt_filterprovider").prop("alt",raw_facturacompra['proveedor']);
$("#txt_filterprovider").val(raw_facturacompra['proveedor_nombre']);

});

function save_invoice(preguardado){
	if(preguardado === 1){ans=false;}else{
		var ans = confirm("¿Esta cuenta esta POR PAGAR?");
	}
	if(ans){	ans="POR PAGAR";	}else{	ans="PAGADO";	}
	var	date = $("#txt_date").val();
	var	provider = $("#txt_filterprovider").prop("alt");
	var	billnumber = $("#txt_billnumber").val();
	var	warehouse = $("#sel_warehouse").val();
	var	purchaseorder = $("#txt_purchaseorder").val();

	$.ajax({	data: { "a" : date, "b" : provider, "c" : billnumber, "e" : warehouse, "f" : purchaseorder, "g" : ans, "h" : preguardado  },	type: "GET",	dataType: "text",	url: "attached/get/save_invoice.php", })
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD" + textStatus);
		if(data){
			if (preguardado === 0) {	print_html("print_purchase_html.php?a="+data);	}
			setTimeout(function(){window.location="purchase.php"},450);
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {		});

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
        Bienvenido: <label class="label label_blue_sky" class="bg-primary">
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
<form action="" method="post" name="form_newpurchase"  id="form_newpurchase">


<div id="container_provider" class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
	<label class="label label_blue_sky" for="txt_filterprovider">Proveedor:</label>
	<input type="text" class="form-control" id="txt_filterprovider" placeholder="Proveedor">
</div>
<div id="container_btnaddprovider" class="col-xs-1 col-sm-1 col-md-1 col-lg-1 side-btn-md-label">
	<button type="button" id="btn_add_provider" class="btn btn-success"><i class="fa fa-plus"></i></button>
</div>
<div id="container_provider_recall" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
</div>
<div id="container_date" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	<label class="label label_blue_sky" for="txt_date">Fecha:</label>
    <input type="text" name="txt_date" id="txt_date" value="<?php echo date('d-m-Y'); ?>" class="form-control" readonly="readonly" />
</div>

<div id="container_billnumber" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
    <label class="label label_blue_sky" for="txt_bill">Factura N°:</label>
    <input type="text" name="txt_billnumber" id="txt_billnumber" class="form-control" />
</div>
<div id="container_purchaseorder" class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
    <label class="label label_blue_sky" for="txt_purchaseorder">Orden de Compra:</label>
    <input type="text" name="txt_purchaseorder" id="txt_purchaseorder" class="form-control" value="<?php echo $pedido_numero ?>" />
</div>
	<div id="container_warehouse" class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
    <label class="label label_blue_sky" for="sel_warehouse">Almacen:</label>
    <select name="sel_warehouse" id="sel_warehouse" class="form-control"><?php
			do{ ?>
	    	<option value="<?php echo $rs_warehouse['AI_almacen_id'] ?>"><?php echo $rs_warehouse['TX_almacen_value'] ?></option><?php
			}while($rs_warehouse=$qry_warehouse->fetch_array());
?>  </select>
	</div>
	<div id="container_product" class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
		<div id="container_filterproduct" class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
		  <label class="label label_blue_sky" for="sel_product">Producto:</label>
	    <input type="text" alt="select" class="form-control" id="txt_filterproduct" name="txt_filterproduct" autocomplete="off" onkeyup="filter_product2purchase(this);" placeholder="Codigo, Descripcion o Referencia" />
		</div>
		<div id="container_btnaddproduct" class="col-xs-2 col-sm-2 col-md-2 col-lg-2 side-btn-md-label">
			<button type="button" name="btn_addproduct" id="btn_addproduct" class="btn btn-success btn-md"><i class="fa fa-plus"></i></button>
		</div>
		<div id="container_tblproduct" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<table id="tbl_product" class="table table-bordered table-condensed table-striped table-hover">
        <tbody><?php
        do{
					$color = ($rs_product['TX_producto_activo'] === '1') ? '#f84c4c; font-weight: bolder;' : '#000';
					$title = ($rs_product['TX_producto_activo'] === '1') ? 'INACTIVO' : '';
?>        <tr style="color:<?php echo $color; ?>" title="<?php echo $title; ?>">
            <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2" onclick="open_product2purchase(<?php echo $rs_product['AI_producto_id'] ?>)"><?php echo $rs_product['TX_producto_codigo'] ?></td>
	        	<td class="col-xs-7 col-sm-7 col-md-7 col-lg-7" onclick="open_product2purchase(<?php echo $rs_product['AI_producto_id'] ?>)"><?php echo $r_function->replace_special_character($rs_product['TX_producto_value']) ?></td>
            <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2" onclick="open_product2purchase(<?php echo $rs_product['AI_producto_id'] ?>)"><?php echo $rs_product['TX_producto_cantidad'] ?></td>
						<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><button type="button" class="btn btn-warning btn-xs" onclick="open_popup('popup_updproduct.php?a=<?php echo $rs_product['AI_producto_id'] ?>', '_popup','1010','654')"><i class="fa fa-wrench"></i></button></td>
	        </tr>
<?php 	}while($rs_product=$qry_product->fetch_array());	?>
      </tbody>
    </table>
  </div>
</div>
<div id="container_observation" class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
	<label class="label label_blue_sky" for="ta_observation">Observaciones</label><span id="span_taobservation" class="span_counter">200</span>
  <textarea id="ta_observation" class="form-control"></textarea>
</div>
<div id="container_tblnewentry" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<table id="tbl_newentry" class="table table-bordered table-condensed">
		<caption class="caption">Productos Incluidos en la Factura</caption>
		<thead class="bg_green">
	    <tr>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Codigo</th>
	      <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Producto</th>
	      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Medida</th>
	      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
	      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Precio</th>
	      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Desc%</th>
	      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">ITBM%</th>
	      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">SubTotal</th>
	      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">P. Regular</th>
	    </tr>
    </thead>
    <tbody>
	<?php
    	$total_itbm = 0;	$total_descuento = 0;	$total = 0;
  		if($nr_newpurchase=$qry_newpurchase->num_rows > 0){
				do{
					$precio4product=$rs_newpurchase['TX_nuevacompra_unidades']*$rs_newpurchase['TX_nuevacompra_precio'];
					$descuento4product=($rs_newpurchase['TX_nuevacompra_descuento']*$precio4product)/100;
					$total_descuento+=$descuento4product;
					$precio_descuento=$precio4product-$descuento4product;
					$impuesto4product=($rs_newpurchase['TX_nuevacompra_itbm']*$precio_descuento)/100;
					$total_itbm+=$impuesto4product;
					$total_desc_imp=$precio_descuento+$impuesto4product;
					$total+=$total_desc_imp; ?>
			    <tr>
			    	<td><?php echo $rs_newpurchase['TX_producto_codigo']; ?></td>
			      <td><?php echo $rs_newpurchase['TX_producto_value']; ?></td>
			      <td><?php echo $raw_medida[$rs_newpurchase['TX_nuevacompra_medida']]; ?></td>
			      <td onclick="upd_quantitynewpurchase(<?php echo $rs_newpurchase['AI_nuevacompra_id']; ?>)"><?php echo $rs_newpurchase['TX_nuevacompra_unidades']; ?></td>
			      <td onclick="upd_pricenewpurchase(<?php echo $rs_newpurchase['AI_nuevacompra_id']; ?>)"><?php echo $rs_newpurchase['TX_nuevacompra_precio']; ?></td>
			      <td><?php echo $rs_newpurchase['TX_nuevacompra_descuento']."% = ".number_format($descuento4product,4);?></td>
			      <td><?php echo $rs_newpurchase['TX_nuevacompra_itbm']."% = ".number_format($impuesto4product,4); ?></td>
			      <td><?php echo number_format($total_desc_imp,4);	?></td>
			      <td class="al_center"><button type="button" name="<?php echo $rs_newpurchase['nuevacompra_AI_producto_id']; ?>" id="btn_delproduct" class="btn btn-danger btn-sm" onclick="javascript: del_product2purchase(this);"><strong>X</strong></button></td>
						<td><span id="<?php echo $rs_newpurchase['AI_nuevacompra_id']; ?>" class="form-control" onclick="upd_newpurchase_price(this)"><?php echo number_format($rs_newpurchase['TX_nuevacompra_p4'],2);	?></span></td>
			    </tr>
<?php 	}while($rs_newpurchase=$qry_newpurchase->fetch_array()); ?>
				<tr class="bg_green">
					<td colspan="5"></td>
						<td>B/ <?php echo number_format($total_descuento,4); ?></td>
						<td>B/ <?php echo number_format($total_itbm,4); ?></td>
						<td>B/ <?php echo number_format($total,2); ?></td>
						<td colspan="2"></td>
				</tr>
<?php }else{ ?>
		    <tr>
		    	<td colspan="9">&nbsp;</td>
		    </tr>
				<tr class="bg_green">
		    	<td colspan="5"></td>
		        <td>B/ <?php echo number_format($total_descuento,4); ?></td>
		        <td>B/ <?php echo number_format($total_itbm,4); ?></td>
		        <td>B/ <?php echo number_format($total,2); ?></td>
						<td colspan="2"></td>
		    </tr>
<?php } ?>
    </tbody>
	</table>
</div>
<div id="alert" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
</div>
<div id="container_btnnewpurchase" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<button type="button" id="btn_save" class="btn btn-info">Guardar</button>
	&nbsp;
	<button type="button" id="btn_insert" class="btn btn-success btn-lg">Procesar</button>
	&nbsp;
	<button type="button" id="btn_cancelar" class="btn btn-warning">Cancelar</button>
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
