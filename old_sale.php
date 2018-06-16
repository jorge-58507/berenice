<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_sale.php';

//############################## FUNCIONES
function read_viejaventa_content(){
	$link=conexion();
	$qry_nuevaventa = $link->query("SELECT TX_rel_nuevaventa_compuesto FROM rel_nuevaventa WHERE AI_rel_nuevaventa_id = 3")or die($link->error);
	$rs_nuevaventa = $qry_nuevaventa->fetch_array();
	$contenido = $rs_nuevaventa['TX_rel_nuevaventa_compuesto'];
	if (empty($contenido)) {
		$contenido =	'{"'.$_COOKIE['coo_iuser'].'":{}}';
	}
	return $contenido;
}
function read_viejaventa_rel(){
	$link=conexion();
	$qry_nuevaventa = $link->query("SELECT TX_rel_nuevaventa_compuesto FROM rel_nuevaventa WHERE AI_rel_nuevaventa_id = 4")or die($link->error);
	$rs_nuevaventa = $qry_nuevaventa->fetch_array();
	$contenido = $rs_nuevaventa['TX_rel_nuevaventa_compuesto'];
	if (empty($contenido)) {
		$contenido =	'{"'.$_COOKIE['coo_iuser'].'":{}}';
	}
	return $contenido;
}
function write_viejaventa_content($contenido){
	$link=conexion(); $r_function = new recurrent_function();
	$qry_nuevaventa = $link->query("UPDATE rel_nuevaventa SET TX_rel_nuevaventa_compuesto = '$contenido' WHERE AI_rel_nuevaventa_id = 3")or die($link->error);
}
function write_viejaventa_rel($contenido){
	$link=conexion(); $r_function = new recurrent_function();
	$contenido = $r_function->replace_regular_character($contenido);
	$qry_nuevaventa = $link->query("UPDATE rel_nuevaventa SET TX_rel_nuevaventa_compuesto = '$contenido' WHERE AI_rel_nuevaventa_id = 4")or die($link->error);
}
// ############################# FIN DE FUNCIONES
$facturaventa_id=$_GET['a'];

$qry_precio = $link->prepare("SELECT TX_precio_cuatro FROM bh_precio WHERE precio_AI_producto_id = ? AND TX_precio_inactivo = '0'")or die($link->error);
$qry_letra = $link->prepare("SELECT bh_letra.TX_letra_value FROM (bh_letra INNER JOIN bh_producto ON bh_letra.AI_letra_id = bh_producto.producto_AI_letra_id) WHERE bh_producto.AI_producto_id = ? ")or die($link->error);
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

$qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida")or die($link->error);
$raw_medida = array();
while($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)){
	$raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
}

$qry_promocion = $link->query("SELECT AI_promocion_id, TX_promocion_descripcion, TX_promocion_componente, TX_promocion_tipo, TX_promocion_titulo FROM bh_promocion")or die($link->error);
$raw_promociones=array();	$i=0;
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
	$raw_promociones[$i]['promo_titulo'] = $rs_promocion['TX_promocion_titulo'];
	$raw_promociones[$i]['promo_contenido'] = $rs_promocion['TX_promocion_descripcion'];
	$raw_promociones[$i]['promo_producto'] = json_encode($raw_producto_id);
	$raw_promociones[$i]['promo_medida'] = json_encode($raw_medida_id);
	$raw_promociones[$i]['promo_cantidad'] = json_encode($raw_cantidad);
	$raw_promociones[$i]['promo_precio'] = json_encode($raw_precio);
	$raw_promociones[$i]['promo_impuesto'] = json_encode($raw_impuesto);
	$raw_promociones[$i]['promo_descuento'] = json_encode($raw_descuento);
	$raw_promociones[$i]['promo_tipo'] = $rs_promocion['TX_promocion_tipo'];
	$i++;
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

$date_i = date('Y-m-d',strtotime('-12 week'));
$date_f = date('Y-m-d');
$qry_cliente_favorito=$link->query("SELECT TX_datoventa_descripcion, count(TX_datoventa_descripcion) as conteo_descripcion, datoventa_AI_producto_id, TX_datoventa_precio FROM bh_datoventa INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id WHERE facturaventa_AI_cliente_id = '{$rs_facturaventa['facturaventa_AI_cliente_id']}' AND bh_facturaventa.TX_facturaventa_fecha >= '$date_i' AND bh_facturaventa.TX_facturaventa_fecha <= '$date_f' GROUP BY TX_datoventa_descripcion ORDER BY conteo_descripcion DESC LIMIT 8");

$qry_vendor=$link->query("SELECT AI_user_id, TX_user_seudonimo FROM bh_user WHERE AI_user_id = '{$rs_facturaventa['facturaventa_AI_user_id']}'");
$rs_vendor=$qry_vendor->fetch_array();

$prep_producto_value=$link->prepare("SELECT TX_producto_value FROM bh_producto WHERE AI_producto_id = ?")or die($link->error);
$qry_datoventa=$link->query("SELECT AI_datoventa_id, datoventa_AI_facturaventa_id, datoventa_AI_producto_id, TX_datoventa_cantidad, TX_datoventa_precio, TX_datoventa_impuesto, TX_datoventa_descuento, TX_datoventa_descripcion, TX_datoventa_medida, TX_datoventa_promocion FROM bh_datoventa WHERE datoventa_AI_facturaventa_id = '$facturaventa_id' ORDER BY AI_datoventa_id ASC")or die($link->error);
$raw_datoventa=array();	$i=0;
$raw_producto_id = array();	$raw_medida_id = array();	$raw_cantidad = array();
$raw_precio = array();	$raw_impuesto = array();	$raw_descuento = array();
$raw_datoventa_id = array(); $raw_descripcion = array();
while($rs_datoventa=$qry_datoventa->fetch_array()){
	$raw_producto_id[] = $rs_datoventa['datoventa_AI_producto_id'];
	$raw_medida_id[] = $rs_datoventa['TX_datoventa_medida'];
	$raw_cantidad[] = $rs_datoventa['TX_datoventa_cantidad'];
	$raw_precio[] = $rs_datoventa['TX_datoventa_precio'];
	$raw_impuesto[] = $rs_datoventa['TX_datoventa_impuesto'];
	$raw_descuento[] = $rs_datoventa['TX_datoventa_descuento'];
	$raw_promocion[] = $rs_datoventa['TX_datoventa_promocion'];
	$raw_descripcion[] = str_replace('\\\'','&squote;',$rs_datoventa['TX_datoventa_descripcion']);
	$raw_datoventa_id[$rs_datoventa['AI_datoventa_id']] = $i;
	$i++;
}

$contenido_viejaventa=read_viejaventa_content();
$raw_viejaventa=json_decode($contenido_viejaventa, true);
if (!array_key_exists($_COOKIE['coo_iuser'], $raw_viejaventa)) {	$raw_viejaventa[$_COOKIE['coo_iuser']]= array();	}
unset($raw_viejaventa[$_COOKIE['coo_iuser']]);
$raw_viejaventa[$_COOKIE['coo_iuser']]= array();
foreach ($raw_datoventa_id as $datoventa_id => $indice) {
	$qry_producto = $link->query("SELECT TX_producto_value, TX_producto_codigo, TX_producto_medida, TX_producto_cantidad FROM bh_producto WHERE AI_producto_id = '$raw_producto_id[$indice]'")or die($link->error);
	$rs_producto=$qry_producto->fetch_array(MYSQLI_ASSOC);
	$descripcion = $rs_producto['TX_producto_value'];

	$raw_viejaventa[$_COOKIE['coo_iuser']][$indice]['producto_id'] = $raw_producto_id[$indice];
	$raw_viejaventa[$_COOKIE['coo_iuser']][$indice]['cantidad'] = $raw_cantidad[$indice];
	$raw_viejaventa[$_COOKIE['coo_iuser']][$indice]['precio'] = $raw_precio[$indice];
	$raw_viejaventa[$_COOKIE['coo_iuser']][$indice]['impuesto'] = $raw_impuesto[$indice];
	$raw_viejaventa[$_COOKIE['coo_iuser']][$indice]['descuento'] = $raw_descuento[$indice];
	$raw_viejaventa[$_COOKIE['coo_iuser']][$indice]['descripcion'] = $raw_descripcion[$indice];
	$raw_viejaventa[$_COOKIE['coo_iuser']][$indice]['codigo'] = $rs_producto['TX_producto_codigo'];
	$raw_viejaventa[$_COOKIE['coo_iuser']][$indice]['medida'] = $raw_medida_id[$indice];
	$raw_viejaventa[$_COOKIE['coo_iuser']][$indice]['stock'] = $rs_producto['TX_producto_cantidad'];
	$raw_viejaventa[$_COOKIE['coo_iuser']][$indice]['promocion'] = $raw_promocion[$indice];
}
$contenido_viejaventa = json_encode($raw_viejaventa);
write_viejaventa_content($contenido_viejaventa);

$raw_facturaventa_promocion = json_decode($rs_facturaventa['TX_facturaventa_promocion']);
$raw_datoventa_relacionado = array();
foreach ($raw_facturaventa_promocion as $key => $string_related) {
	$chain_related='';
	if (!empty($string_related)) {
		$raw_related = explode(",", $string_related);
		foreach ($raw_related as $key => $value) {
			$chain_related .= ($value === end($raw_related))  ? $raw_datoventa_id[$value] : $raw_datoventa_id[$value].",";
		}
		$raw_datoventa_relacionado[] = $chain_related;
	}
}

$contenido_viejaventarel = read_viejaventa_rel();
$raw_contenido_viejaventarel = json_decode($contenido_viejaventarel, true);
unset($raw_contenido_viejaventarel[$_COOKIE['coo_iuser']]);
$raw_contenido_viejaventarel[$_COOKIE['coo_iuser']]=$raw_datoventa_relacionado;
$contenido_viejaventarel = json_encode($raw_contenido_viejaventarel);
write_viejaventa_rel($contenido_viejaventarel);

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
<script type="text/javascript" src="attached/js/old_sale_funct.js"></script>


<script type="text/javascript">

$(document).ready(function() {

$(window).on('beforeunload', function(){
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

$("#btn_promotion").on("click", function(){
	$("#container_tbl_product_favorite").toggleClass('in');
})

$("#btn_favorite").on("click", function(){
	$("#container_tbl_product_promotion").toggleClass('in');
})

$("#btn_guardar").click(function(){
	if($("#txt_filterclient").attr("alt") === ""){
		set_bad_field('txt_filterclient');
		$("#txt_filterclient").focus();
		return false;
	} set_good_field('txt_filterclient');
	$("#btn_guardar").attr("disabled", true);
	save_old_sale();
	return false;
})

$("#btn_salir").click(function(){
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

});


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
				$("#txt_filterclient").prop('title', 'Completa las ventas '+ui.item.asiduo+" de las veces");
				content = '<strong>Nombre:</strong> '+ui.item.value+' <strong>RUC:</strong> '+ui.item.ruc+' <strong>Tlf.</strong> '+ui.item.telefono+' <strong>Dir.</strong> '+ui.item.direccion.substr(0,20)+' <strong>Asiduo.</strong> '+ui.item.asiduo;
				fire_recall('container_client_recall', content)
				generate_tbl_favorito(ui.item.json_favorito);
			}
		});
	});
}
function open_product2sell(id){
	open_popup('popup_product2sell.php?a='+id+'', '_popup','425','430');
}
function generate_tbl_viejaventa(data){
	var json_medida = '<?php echo json_encode($raw_medida); ?>';
	var array_medida =	JSON.parse(json_medida);
	var raw_data = data;
	var viejaventa = raw_data[<?php echo $_COOKIE['coo_iuser']; ?>];
	var total_itbm=0; var total_descuento=0; var total=0;
	if(Object.keys(viejaventa).length > 0){
		var content = '';
		for (var x in viejaventa) {
			var precio = parseFloat(viejaventa[x]['precio']);
			var descuento = (viejaventa[x]['precio']*viejaventa[x]['descuento'])/100;
			var precio_descuento = viejaventa[x]['precio']-descuento;
			var impuesto = (precio_descuento*viejaventa[x]['impuesto'])/100;
			var precio_unitario = precio_descuento+impuesto;
					precio_unitario = Math.round10(precio_unitario, -4);
			var subtotal = viejaventa[x]['cantidad']*precio_unitario;

			total_itbm += impuesto*viejaventa[x]['cantidad'];
			total_descuento += descuento*viejaventa[x]['cantidad'];
			total += subtotal;
			style_promotion = (viejaventa[x]['promocion'] > 0 ) ? 'style="color: #f86e6e; background-color: #f2ffef; text-shadow: 0.5px 0.5px #f37e7e80;"' : '';
			fire_promotion = (viejaventa[x]['promocion'] > 0 ) ? '<i class="fa fa-free-code-camp"> </i> ' : '';

			content += `<tr ${style_promotion}><td onclick="set_position_viejaventa(${x})"><span class="badge">${parseInt(x)+1}</span></td><td>${viejaventa[x]['codigo']}</td><td onclick="upd_descripcion_viejaventa(${x},\'${viejaventa[x]['descripcion']}\')">${fire_promotion+replace_special_character(viejaventa[x]['descripcion'])}</td><td>${array_medida[viejaventa[x]['medida']]}</td><td onclick="upd_unidades_viejaventa(${x});">${viejaventa[x]['cantidad']}</td><td onclick="upd_precio_viejaventa(${x})">${precio.toFixed(2)}</td><td onclick="upd_descuento_viejaventa(${x})">(${viejaventa[x]['descuento']}%) ${descuento.toFixed(2)}</td><td>(${viejaventa[x]['impuesto']}%) ${impuesto.toFixed(2)}</td><td>${precio_unitario.toFixed(2)}</td><td>${subtotal.toFixed(2)}</td><td><button type="button" id="btn_delproduct" class="btn btn-danger btn-sm" onclick="del_viejaventa(${x});"><strong>X</strong></button></td></tr>`;
		}

		$("#tbl_product2sell tbody").html(content);
		$("#span_discount").html(total_descuento.toFixed(2));
		$("#span_itbm").html(total_itbm.toFixed(2));
		$("#span_total").html(total.toFixed(2));
	}else{
		content=content+'<tr><td colspan="10">&nbsp;</td></tr>';
		$("#tbl_product2sell tbody").html(content);
		$("#span_discount").html(total_descuento.toFixed(2));
		$("#span_itbm").html(total_itbm.toFixed(2));
		$("#span_total").html(total.toFixed(2));
	}
}
function generate_tbl_favorito(data){
	var array_data = JSON.parse(data);
	var content = '';
	if(Object.keys(array_data).length > 0){
		for (var x in array_data) {
			content +=	`<tr onclick="open_product2sell(${array_data[x]['datoventa_AI_producto_id']});"><td>${replace_special_character(array_data[x]['TX_datoventa_descripcion'])}</td><td>${array_data[x]['TX_datoventa_precio']}</td></tr>`;
		}
	}else{
  	content = `<tr><td colspan='2'> </td></tr>`;
	}
	$("#tbl_product_favorite tbody").html(content);
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
	    <input type="text" class="form-control" id="txt_date" name="txt_date" readonly="readonly" value="<?php echo $date=date('d-m-Y',strtotime($rs_facturaventa['TX_facturaventa_fecha'])); ?>" /></div>
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
	<div id="container_client_recall" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 display_none">
	</div>
</div>
<div id="container_observation" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_txtobservation" class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
		<label class="label label_blue_sky" for="txt_observation">Observaciones:</label>
		<input type="text" class="form-control" id="txt_observation" name="txt_observation" value="<?php echo $rs_facturaventa['TX_facturaventa_observacion']; ?>" />
	</div>
	<div id="container_btnrefreshtblproduct2sale" class="col-xs-1 col-sm-1 col-md-1 col-lg-1 side-btn-md-label">
			<button type="button" id="btn_refresh_tblproduct2sale" class="btn btn-info btn-md" title="Refrescar Tabla" onclick="refresh_tbl_viejaventa()">
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
				<th></th>
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
			<?php
			if(count($raw_viejaventa[$_COOKIE['coo_iuser']]) > 0){
				$raw_viejaventa = $raw_viejaventa[$_COOKIE['coo_iuser']];
				$total_itbm = 0;
				$total_descuento = 0;
				$sub_total = 0;
				foreach ($raw_viejaventa as $key => $rs_nuevaventa) {
					$descuento = (($rs_nuevaventa['descuento']*$rs_nuevaventa['precio'])/100);
					$precio_descuento = ($rs_nuevaventa['precio']-$descuento);
					$impuesto = (($rs_nuevaventa['impuesto']*$precio_descuento)/100);
					$precio_unitario = round($precio_descuento+$impuesto,4);
					$precio_total = ($rs_nuevaventa['cantidad']*($precio_unitario));

					$total_itbm += $rs_nuevaventa['cantidad']*$impuesto;
					$total_descuento += $rs_nuevaventa['cantidad']*$descuento;
					$sub_total += $rs_nuevaventa['cantidad']*$rs_nuevaventa['precio'];

					$style_promotion = ($rs_nuevaventa['promocion'] > 0 ) ? 'style="color: #f86e6e; background-color: #f2ffef; text-shadow: 0.5px 0.5px #f37e7e80;"' : '';
					$fire_promotion = ($rs_nuevaventa['promocion'] > 0 ) ? '<i class="fa fa-free-code-camp"> </i> ' : '';
	?> 			<tr <?php echo $style_promotion; ?>>
						<td><span class="badge" onclick="set_position_viejaventa(<?php echo $key; ?>)"><?php echo $key+1 ?></span></td>
						<td><?php echo $rs_nuevaventa['codigo']; ?></td>
						<td onclick="upd_descripcion_viejaventa(<?php echo $key.",'".$r_function->replace_regular_character($rs_nuevaventa['descripcion'])?>')"><?php echo $fire_promotion.$r_function->replace_special_character($rs_nuevaventa['descripcion']); ?></td>
						<td><?php echo $raw_medida[$rs_nuevaventa['medida']]; ?></td>
						<td onclick="upd_unidades_viejaventa(<?php echo $key; ?>);">
							<?php echo $rs_nuevaventa['cantidad']; ?>
							<span id="stock_quantity"><?php echo $rs_nuevaventa['stock']; ?></span>
						</td>
						<td onclick="upd_precio_viejaventa(<?php echo $key; ?>);"><?php echo number_format($rs_nuevaventa['precio'],2); ?></td>
						<td onclick="upd_descuento_viejaventa(<?php echo $key; ?>);"><?php echo "(".$rs_nuevaventa['descuento']."%) ".number_format($descuento,2); ?></td>
						<td><?php echo "(".$rs_nuevaventa['impuesto']."%) ".number_format($impuesto,2); ?></td>
						<td><?php echo number_format($precio_unitario,4); ?></td>
						<td><?php echo number_format($precio_total,4); ?></td>
						<td class="al_center"><button type="button" id="btn_delproduct" class="btn btn-danger btn-sm" onclick="javascript: del_viejaventa(<?php echo $key ?>);"><strong>X</strong></button></td>
					</tr>
	<?php
				}
	 		}else{
				$total_itbm = 0;
				$total_descuento = 0;
				$sub_total = 0;
?>			<tr>
					<td colspan="10"></td>
				</tr>
<?php }
			$total=($sub_total-$total_descuento)+$total_itbm;
?>
		</tbody>
		<tfoot class="bg_green">
			<tr>
				<td colspan="6"></td>
				<td>
					<strong>T. Desc: </strong> <br /><span id="span_discount"><?php echo number_format($total_descuento,2); ?></span>
				</td>
				<td>
					<strong>T. Imp: </strong> <br /><span id="span_itbm"><?php echo number_format($total_itbm,2); ?></span>
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
		<label class="radio-inline pt_7"><input type="radio" name="r_limit" id="r_limit" value="10" checked="checked"/> 10</label>
		<label class="radio-inline pt_7"><input type="radio" name="r_limit" id="r_limit" value="50" /> 50</label>
		<label class="radio-inline pt_7"><input type="radio" name="r_limit" id="r_limit" value="100" /> 100</label>
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
	      	<th class="col-xs-8 col-sm-8 col-md-8 col-lg-8">Nombre</th>
		    	<th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Cantidad</th>
		    	<th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Precio</th>
		      <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Letra</th>
		    </tr>
	    </thead>
	    <tfoot>
		    <tr class="bg-primary">
	    		<td class="bg-primary" colspan="5">  </td>
	    	</tr>
	    </tfoot>
			<tbody>

<?php
			if($nr_product=$qry_product->num_rows > 0){
				foreach ($raw_producto as $key => $rs_product) {
?>
			    <tr onclick="javascript:open_product2sell(<?php echo $rs_product['AI_producto_id']; ?>);">
	        	<td title="<?php echo $rs_product['AI_producto_id']; ?>"><?php echo $rs_product['TX_producto_codigo']; ?></td>
	        	<td><?php echo $r_function->replace_special_character($rs_product['TX_producto_value']); ?></td>
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
	<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 no_padding">

		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
			<button type="button" id="btn_promotion" class="btn btn-success form-control" name="button" data-toggle="collapse" data-target="#container_tbl_product_promotion">Promociones</button>
		</div>
		<div id="container_tbl_product_promotion" class="col-xs-12 col-sm-12 col-md-12 col-lg-12  no_padding collapse in">
			<table id='tbl_product_promotion' class="table table-condensed table-hover table-bordered">
				<thead class="bg-success">
					<tr>
						<th>Promociones</th>
					</tr>
				</thead>
				<tbody>
<?php 		if(count($raw_promociones) > 0){
						foreach ($raw_promociones as $key => $value) {?>
							<tr onclick='insert_multiple_product2sell(<?php echo $value['promo_producto'].",".$value['promo_medida'].",".$value['promo_cantidad'].",".$value['promo_precio'].",".$value['promo_impuesto'].",".$value['promo_descuento'].",".$value['promo_tipo']; ?>)'><td style="font-weight:bolder; cursor:pointer;"><?php echo $value['promo_titulo'];?></td></tr>
							<tr><td>-<?php echo $value['promo_contenido'];?></td></tr>
<?php 			}
					}else{ ?>
						<tr><td></td></tr>
<?php			} ?>
				</tbody>
				<tfoot class="bg-success">
					<tr><td colspan="2"></td></tr>
				</tfoot>
		 </table>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
			<button type="button" class="btn btn-warning form-control" id="btn_favorite" name="button" data-toggle="collapse" data-target="#container_tbl_product_favorite">Favoritos</button>
		</div>
		<div id="container_tbl_product_favorite" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding collapse">
		 <table id='tbl_product_favorite' class="table table-condensed table-hover table-bordered">
				<thead class="bg-warning">
					<tr>
						<th colspan="2">Favoritos</th>
					</tr>
				</thead>
				<tbody>
<?php 		while($rs_cliente_favorito = $qry_cliente_favorito->fetch_array()){ ?>
						<tr onclick="open_product2sell(<?php echo $rs_cliente_favorito['datoventa_AI_producto_id']; ?>);">
							<td><?php echo $r_function->replace_special_character($rs_cliente_favorito['TX_datoventa_descripcion']); ?></td>
							<td><?php echo $rs_cliente_favorito['TX_datoventa_precio']; ?></td>
						</tr>
<?php  		}	?>
				</tbody>
				<tfoot class="bg-warning">
					<tr><td colspan="2"></td></tr>
				</tfoot>
		 </table>
		</div>
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
