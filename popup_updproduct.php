<?php
require 'bh_conexion.php';
$link=conexion();
session_start();

$product_id=$_GET['a'];

$qry_product=$link->query("SELECT * FROM bh_producto WHERE AI_producto_id = '$product_id'")or die($link->error);
$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);

$qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida ORDER BY TX_medida_value")or die($link->error);

$qry_precio=$link->query("SELECT AI_precio_id, TX_precio_uno, TX_precio_dos, TX_precio_tres, TX_precio_cuatro, TX_precio_cinco FROM bh_precio WHERE precio_AI_producto_id = '$product_id' AND precio_AI_medida_id = '{$rs_product['TX_producto_medida']}' AND TX_precio_inactivo = '0' ORDER BY TX_precio_fecha DESC")or die($link->error);
$rs_precio=$qry_precio->fetch_array(MYSQLI_ASSOC);

$qry_producto_medida = $link->query("SELECT bh_medida.AI_medida_id, bh_medida.TX_medida_value, rel_producto_medida.TX_rel_productomedida_cantidad, rel_producto_medida.AI_rel_productomedida_id, bh_letra.TX_letra_value, bh_letra.TX_letra_porcentaje
FROM ((bh_medida
INNER JOIN rel_producto_medida ON bh_medida.AI_medida_id = rel_producto_medida.productomedida_AI_medida_id)
INNER JOIN bh_letra ON bh_letra.AI_letra_id = rel_producto_medida.productomedida_AI_letra_id)
WHERE productomedida_AI_producto_id = '{$_GET['a']}'")or die($link->error);
$raw_producto_medida=array();
while ($rs_producto_medida = $qry_producto_medida->fetch_array(MYSQLI_ASSOC)) {
	$raw_producto_medida[]=$rs_producto_medida;
}

$qry_letra=$link->query("SELECT bh_letra.AI_letra_id, bh_letra.TX_letra_value, bh_letra.TX_letra_porcentaje FROM bh_letra")or die($link->error);

$qry_precio_listado = $link->query("SELECT bh_precio.AI_precio_id, bh_precio.TX_precio_fecha, bh_precio.TX_precio_uno, bh_precio.TX_precio_dos, bh_precio.TX_precio_tres, bh_precio.TX_precio_cuatro, bh_precio.TX_precio_cinco, bh_precio.precio_AI_user_id, bh_producto.AI_producto_id, bh_precio.TX_precio_comentario FROM (bh_precio INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_precio.precio_AI_producto_id) WHERE bh_producto.AI_producto_id = '$product_id' AND bh_precio.precio_AI_medida_id = '{$rs_product['TX_producto_medida']}' ORDER BY TX_precio_fecha DESC, AI_precio_id DESC")or die($link->error);

$qry_datocompra_listado = $link->query("SELECT bh_facturacompra.TX_facturacompra_fecha,bh_datocompra.TX_datocompra_precio,bh_datocompra.TX_datocompra_impuesto,bh_datocompra.TX_datocompra_descuento,bh_datocompra.TX_datocompra_cantidad FROM ((bh_datocompra INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datocompra.datocompra_AI_producto_id) INNER JOIN bh_facturacompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id)
WHERE bh_producto.AI_producto_id = '$product_id' AND TX_datocompra_medida = '{$rs_product['TX_producto_medida']}' ORDER BY TX_facturacompra_fecha DESC")or die($link->error);

$qry_user = $link->query("SELECT AI_user_id, TX_user_seudonimo FROM bh_user")or die($link->error);
$raw_user=array();
while($rs_user = $qry_user->fetch_array(MYSQLI_ASSOC)) {
	$raw_user[$rs_user['AI_user_id']] = $rs_user['TX_user_seudonimo'];
}
$qry_inventario = $link->query("SELECT AI_inventario_id, TX_inventario_json FROM bh_inventario WHERE inventario_AI_producto_id = '$product_id'")or die($link->error);
$str_inventariado = '';
$it = 1;
if ($qry_inventario->num_rows > 0) {
	$rs_inventario = $qry_inventario->fetch_array(MYSQLI_ASSOC);
	$raw_inventario = json_decode($rs_inventario['TX_inventario_json'], true);
	$raw_inventario = array_reverse($raw_inventario);
	foreach ($raw_inventario as $key => $raw_value) {
		if ($it > 10) {		break;	} else {  $it++; }
		$str_line = '';
		foreach ($raw_value as $clave => $value) {
				if ($value === reset($raw_value)) {
					$str_line .= "(".$clave.") =>".$value." ";
				}elseif($clave === 'user'){
					$str_line .= $raw_user[$value]." - ";
				}else{
					$str_line .= 'Ex.'.$value;
				}
		}
		$str_inventariado .= $str_line.PHP_EOL;
	}
}
$product_value = $r_function->replace_special_character($rs_product['TX_producto_value']);


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
<link href="attached/css/popup_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>

<script type="text/javascript">

$(document).ready(function() {
	window.resizeTo("1010", "667");
	$('#btn_cancel_product').click(function(){
		self.close();
	});
	$("#txt_precio1, #txt_precio2, #txt_precio3, #txt_precio4, #txt_precio5").on("blur",function(){
		this.value = val_intw2dec(this.value);
	})
	$('#btn_save_product').click(function(){
		ans = confirm("¿Seguro desea guardar la información?");
		if(!ans){ return false; }
		cls_stock.update_product(<?php echo $product_id; ?>);
		// upd_product(<?php echo $product_id; ?>);
	});
	$("#btn_save_price").on("click", function(){

		if($("#txt_precio4").val() === '') { $("#txt_precio4").val('0.00') }
		var comment = prompt('Indique el comentario','Autorizado por: ');
		$.ajax({	data: {"a" : <?php echo $_GET['a'] ?>, "b" : $("#hd_medida").attr("alt"), "c" : $("#txt_precio1").val(), "d" :  $("#txt_precio2").val(), "e" :  $("#txt_precio3").val(), "f" :  $("#txt_precio4").val(), "g" :  $("#txt_precio5").val(), "h" : comment},	type: "GET",	dataType: "text",	url: "attached/get/plus_product_price.php",	})
		.done(function( data, textStatus, jqXHR ) {	console.log("GOOD " + textStatus);
			data = JSON.parse(data);
			$("#tbl_historical_price tbody").html(data[0]);
			$("#container_sel_medida_descripcion").html(data[1]);
		})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log( "BAD " +  textStatus); })
	})

	$("#txt_nombre").on("blur", function(){
		$("#txt_nombre").val(this.value.toUpperCase());
	});
	$("#txt_reference").on("blur", function(){
		$("#txt_reference").val(this.value.toUpperCase());
	});
	$("#txt_impuesto").on("click", function(){
		if($(this).attr("readonly")){
			var ans=confirm("¿Desea agregar los impuestos manualmente?");
			if(!ans){
				window.location.href="popup_modify_tax.php?a=<?php echo $product_id; ?>";
			}else{
				$(this).attr("readOnly",false);
			}
		}
	});

	$("#btn_discount").on("click",function(){
		var discount = prompt("Indique el Porcentaje a Deducir                  (Este aplicara a todos los precios)");
		ans = val_intwdec(discount);
		if (!ans) {	return false;	}
		$.ajax({	data: {"a" : discount, "b" : <?php echo $_GET['a']; ?>},	type: "GET",	dataType: "text",	url: "attached/get/upd_product_discount.php",	})
		.done(function( data, textStatus, jqXHR ) {	console.log("GOOD " + data);
			$("#btn_discount").text(data)
		})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log( "BAD " +  textStatus); })
	})
	$("#btn_inventory").on("click", function(){
		var quantity = prompt("Indique el resultado del conteo");
		if (quantity === '' || quantity === null ) { return false; }
		quantity = val_dec(quantity,4,0,1);
		$.ajax({	data: {"a" : quantity, "b" : <?php echo $_GET['a']; ?>},	type: "GET",	dataType: "text",	url: "attached/get/upd_product_inventory.php",	})
		.done(function( data, textStatus, jqXHR ) {	console.log("GOOD " + data);
			$("#txt_cantidad").val(data)
			$("#r_inventoried_0").attr('checked', 'checked');
		})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log( "BAD " +  textStatus); })
	})
	$("#btn_addproducto_medida").on("click", function(){
		$.ajax({	data: {"a" : $("#sel_medida_precio").val(), "b" : <?php echo $_GET['a']; ?>, "c" : $("#txt_unidadespormedida").val(), "d" : $("#sel_letter").val() },	type: "GET",	dataType: "text",	url: "attached/get/plus_producto_medida.php",	})
		.done(function( data, textStatus, jqXHR ) { console.log("GOOD " + textStatus); 
			data = JSON.parse(data);
			$("#tbl_producto_medida tbody").html(data[0]);
			$("#container_sel_medida_descripcion").html(data[1]);
			$("#container_alert div").html(`${data[2]}<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>`);
			$("#container_alert").removeClass("hide");
		})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log( "BAD " +  textStatus); })
	})

	$('#txt_cantidad').validCampoFranz('-.0123456789');
	$('#txt_cantminima, #txt_cantmaxima, #txt_impuesto').validCampoFranz('0123456789');
	$('#txt_nombre, #txt_reference').validCampoFranz(".0123456789abcdefghijklmnopqrstuvwxyzº'#/-;()&*,ñ ");
	$('#txt_codigo').validCampoFranz(".0123456789abcdefghijklmnopqrstuvwxyz");
	$('#txt_precio1, #txt_precio2, #txt_precio3, #txt_precio4, #txt_precio5').validCampoFranz('.0123456789');


});
var del_producto_medida = function(rel_id){
	$.ajax({	data: {"a" : rel_id},	type: "GET",	dataType: "text",	url: "attached/get/del_producto_medida.php",	})
	.done(function( data, textStatus, jqXHR ) { console.log("GOOD " + textStatus);
		data = JSON.parse(data);
		$("#tbl_producto_medida tbody").html(data[0]);
		$("#container_sel_medida_descripcion").html(data[1]);
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log( "BAD " +  textStatus); })
}
function get_medida_precio(medida_id){
	$.ajax({	data: {"a" : medida_id, "b" : <?php echo $_GET['a']; ?>},	type: "GET",	dataType: "text",	url: "attached/get/get_medida_precio.php",	})
	.done(function( data, textStatus, jqXHR ) { console.log("GOOD " + textStatus);
		data = JSON.parse(data);
		$("#container_measure_selected").html(data['titulo']);
		$("#hd_medida").attr("alt",data['medida_id']);
		$("#txt_precio1").val(data['precio']['TX_precio_uno']);
		$("#txt_precio2").val(data['precio']['TX_precio_dos']);
		$("#txt_precio3").val(data['precio']['TX_precio_tres']);
		$("#txt_precio4").val(data['precio']['TX_precio_cuatro']);
		$("#txt_precio5").val(data['precio']['TX_precio_cinco']);
		var datocompra_listado = data['datocompra_listado'];
		var content_datocompra = '';
		for (var x in datocompra_listado) {
			var descuento = (datocompra_listado[x]['TX_datocompra_precio']*datocompra_listado[x]['TX_datocompra_descuento'])/100;
			var precio_descuento = datocompra_listado[x]['TX_datocompra_precio']-descuento;
			var impuesto = (precio_descuento*datocompra_listado[x]['TX_datocompra_impuesto'])/100;
			var sub_total = precio_descuento+impuesto;
			content_datocompra += '<tr><td>'+convertir_formato_fecha(datocompra_listado[x]['TX_facturacompra_fecha'])+'</td><td>B/ '+datocompra_listado[x]['TX_datocompra_precio']+'</td><td>'+datocompra_listado[x]['TX_datocompra_impuesto']+'%</td><td>'+datocompra_listado[x]['TX_datocompra_descuento']+'%</td><td>B/ '+sub_total.toFixed(2)+'</td></tr>';
		}
		$("#tbl_purchase_price tbody").html(content_datocompra);

		var precioventa_listado = data['precio_listado'];
		var content_historicalprice = '';
		for (var x in precioventa_listado) {
			content_historicalprice += '<tr><td>'+convertir_formato_fecha(precioventa_listado[x]['TX_precio_fecha'])+'</td><td>B/ '+precioventa_listado[x]['TX_precio_cuatro']+'</td><td>B/ '+precioventa_listado[x]['TX_precio_cinco']+'</td><td>B/ '+precioventa_listado[x]['TX_precio_tres']+'</td><td>B/ '+precioventa_listado[x]['TX_precio_dos']+'</td><td>B/ '+precioventa_listado[x]['TX_precio_uno']+'</td></tr>';
		}
		$("#tbl_historical_price tbody").html(content_historicalprice);
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log( "BAD " +  textStatus); })
}
</script>

</head>

<body>
<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
		<div id="logo" ></div>
	</div>

</div>

<div id="content-sidebar_popup" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<form name="form_inventory" id="form_inventory" method="post">
	<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#descripcion" onclick="inFocus('txt_nombre',500)">Descripcion</a></li>
    <li><a data-toggle="tab" href="#precio" onclick="inFocus('txt_precio4',500)">Agrupados y Precios</a></li>
  </ul>
	<div class="tab-content">
    <div id="descripcion" class="tab-pane fade in active">
	    <div id="container_updproduct_description" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 px_7">
			      <label class="label label_blue_sky"  for="txt_nombre">Nombre:</label>
			      <input type="text" class="form-control input-sm" id="txt_nombre" name="txt_nombre" title='<?php echo $product_value; ?>' value="<?php echo $product_value; ?>" tabindex="1" autofocus>
		      </div>
					<div class="col-xs-3 col-sm-3 col-md-6 col-lg-6 px_7">
						<label class="label label_blue_sky"  for="txt_reference">Referencia:</label>
						<input type="text" id="txt_reference" name="txt_reference" class="form-control input-sm" value="<?php echo $r_function->replace_special_character($rs_product['TX_producto_referencia']); ?>" tabindex="5"/>
					</div>
				</div>
				<!-- DIV IZQUIERDO -->
				<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 no_padding">
					<div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 px_7">
			      <label class="label label_blue_sky"  for="txt_codigo">Codigo:</label>
			      <input type="text" class="form-control input-sm" id="txt_codigo" name="txt_codigo" value="<?php echo $rs_product['TX_producto_codigo']; ?>" tabindex="2">
		    	</div>
					<div id="container_sel_medida_descripcion" class="col-xs-6 col-sm-2 col-md-2 col-lg-2 px_7">
						<label class="label label_blue_sky"  for="sel_medida_descripcion">Medida:</label>
						<select  class="form-control input-sm" id="sel_medida_descripcion" name="sel_medida_descripcion" tabindex="3">
	<?php				foreach ($raw_producto_medida as $key => $rs_medida) {
								if($rs_medida['AI_medida_id']===$rs_product['TX_producto_medida']){
									$measure_selected=$rs_medida['TX_medida_value'];
	?>							<option value="<?php echo $rs_medida['AI_medida_id']; ?>" selected="selected"><?php echo $rs_medida['TX_medida_value']; ?></option>
	<?php					}else{?>
									<option value="<?php echo $rs_medida['AI_medida_id']; ?>"><?php echo $rs_medida['TX_medida_value']; ?></option>
	<?php 				}
							}?>
						</select>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 no_padding">
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 px_7">
							<label class="label label_blue_sky"  for="txt_cantidad">Cantidad:</label>
							<input type="text" class="form-control input-sm" id="txt_cantidad" name="txt_cantidad" value="<?php echo $rs_product['TX_producto_cantidad']; ?>" tabindex="4" readonly/>
						</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 pt_14 ">
							<button type="button" id="btn_inventory" name="button" class="btn btn-info btn-sm" title="<?php echo $str_inventariado; ?>"><i class="fa fa-edit"> </i> Inventariar</button>
						</div>
		    	</div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 px_7">
						<label class="label label_blue_sky"  for="txt_cantmaxima">Ubicaci&oacute;n</label>
						<?php 		$qry_area = $link->query("SELECT AI_area_id, TX_area_value FROM bh_area ORDER BY TX_area_value ASC")or die($link->error); ?>
						<select  class="form-control input-sm" id="sel_ubication_product" name="sel_ubication_product" tabindex="7">
							<?php 			while($rs_area=$qry_area->fetch_array(MYSQLI_ASSOC)){
								if ($rs_area['AI_area_id'] === $rs_product['producto_AI_area_id']) { ?>
									<option value="<?php echo $rs_area['AI_area_id']; ?>" selected="selected"><?php echo $rs_area['TX_area_value']; ?></option>
								<?php 				}else{?>
									<option value="<?php echo $rs_area['AI_area_id']; ?>"><?php echo $rs_area['TX_area_value']; ?></option>
								<?php 				}
							} ?>
						</select>
					</div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 px_7">
						<label class="label label_blue_sky"  for="txt_impuesto">Impuesto:</label>
						<input type="text" class="form-control input-sm" id="txt_impuesto" name="txt_impuesto" value="<?php echo $rs_product['TX_producto_exento']; ?>" readonly="readonly">
					</div>
					<div id="container_btnpercent" class="col-xs-2 col-sm-2 col-md-2 col-lg-2 px_7 py_7">
						<label class="label label_blue_sky"  for="btn_percent">Descuento %:</label>
	<?php 		if($_COOKIE['coo_tuser'] == 1 || $_COOKIE['coo_tuser'] == 2 || isset($_SESSION['admin'])){ ?>
							<button type="button" id="btn_discount" class="btn btn-default form-control input-sm"><?php echo $rs_product['TX_producto_descuento']; ?></button>
	<?php 		}else{	?>
							<button type="button" id="" class="btn btn-default form-control input-sm" disabled="disabled"><?php echo $rs_product['TX_producto_descuento']; ?></button>
	<?php			}	?>
					</div>
					<div class="col-xs-3 col-sm-2 col-md-2 col-lg-2 px_7 py_7">
						<label class="label label_blue_sky"  for="txt_cantminima">Cant. M&iacute;nima:</label>
						<input type="text" class="form-control input-sm" id="txt_cantminima" name="txt_cantminima" value="<?php echo $rs_product['TX_producto_minimo']; ?>" tabindex="7">
					</div>
					<div class="col-xs-3 col-sm-2 col-md-2 col-lg-2 px_7 py_7">
						<label class="label label_blue_sky"  for="txt_cantmaxima">Cant. M&aacute;xima:</label>
						<input type="text" class="form-control input-sm" id="txt_cantmaxima" name="txt_cantmaxima" value="<?php echo $rs_product['TX_producto_maximo']; ?>" tabindex="8">
					</div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 px_7 py_7">
						<label class="label label_blue_sky"  for="sel_subfamilia">Subfamilia</label>
<?php 			$qry_subfamilia = $link->query("SELECT bh_subfamilia.AI_subfamilia_id, bh_subfamilia.TX_subfamilia_value, bh_familia.TX_familia_value
						FROM (bh_subfamilia
						INNER JOIN bh_familia ON bh_familia.AI_familia_id = bh_subfamilia.subfamilia_AI_familia_id)
						ORDER BY subfamilia_AI_familia_id ASC , TX_subfamilia_value ASC")or die($link->error); 		?>
						<select  class="form-control input-sm" id="sel_subfamilia" name="sel_subfamilia" tabindex="9">
<?php 				$group = '';
							while($rs_subfamilia=$qry_subfamilia->fetch_array(MYSQLI_ASSOC)){
								if ($rs_subfamilia['TX_familia_value'] != $group) {
									echo "</optgroup><optgroup label=".$rs_subfamilia['TX_familia_value'].">";
									$group=$rs_subfamilia['TX_familia_value'];

									if ($rs_subfamilia['AI_subfamilia_id'] === $rs_product['producto_AI_subfamilia_id']) { 				?>
										<option value="<?php echo $rs_subfamilia['AI_subfamilia_id']; ?>" selected="selected"><?php echo $rs_subfamilia['TX_subfamilia_value']; ?></option>
<?php 						}else{			?>
										<option value="<?php echo $rs_subfamilia['AI_subfamilia_id']; ?>"><?php echo $rs_subfamilia['TX_subfamilia_value']; ?></option>
<?php 						}

								}else{

									if ($rs_subfamilia['AI_subfamilia_id'] === $rs_product['producto_AI_subfamilia_id']) { 				?>
										<option value="<?php echo $rs_subfamilia['AI_subfamilia_id']; ?>" selected="selected"><?php echo $rs_subfamilia['TX_subfamilia_value']; ?></option>
<?php 						}else{			?>
										<option value="<?php echo $rs_subfamilia['AI_subfamilia_id']; ?>"><?php echo $rs_subfamilia['TX_subfamilia_value']; ?></option>
<?php 						}

								}

							} 	?>
						</select>
					</div>
				</div>
				<!-- DIV DERECHO -->
				<div class="col-xs-2 col-sm-2 col-md-1 col-lg-1 no_padding">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 px_7">
						<label for="cb_alarm" class="label label_blue_sky">Alarma</label>
						<label id="lbl_cb_alarm" class="switch">
							<?php $alarm = ($rs_product['TX_producto_alarma'] == '0') ? 'checked' : '' ?>
							<input id="cb_alarm" type="checkbox" <?php echo $alarm; ?> >
							<span class="slider_switch round"></span>
						</label>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 px_7 pt_7">
						<label for="cb_active" class="label label_blue_sky">Activo</label>
						<label id="lbl_cb_active" class="switch">
							<?php $active = ($rs_product['TX_producto_activo'] == '0') ? 'checked' : '' ?>
							<input id="cb_active" type="checkbox" <?php echo $active; ?> >
							<span class="slider_switch round"></span>
						</label>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 px_7 pt_7">
						<label for="cb_discountable" class="label label_blue_sky">Descontable</label>
						<label id="lbl_cb_discountable" class="switch">
							<?php $discountable = ($rs_product['TX_producto_descontable'] == '1') ? 'checked' : '' ?>
							<input id="cb_discountable" type="checkbox" <?php echo $discountable; ?> >
							<span class="slider_switch round"></span>
						</label>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 px_7 pt_7">
						<label for="cb_inventoried" class="label label_blue_sky">Inventariado</label>
						<label id="lbl_cb_inventoried" class="switch">
							<?php $inventoried = ($rs_product['TX_producto_inventariado'] == '1') ? 'checked' : '' ?>
							<input id="cb_inventoried" type="checkbox" <?php echo $inventoried; ?> >
							<span class="slider_switch round"></span>
						</label>
					</div>
				</div>
				<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<button type="button" name="btn_save_product" id="btn_save_product" class="btn btn-success">Guardar</button>
					&nbsp;
					<button type="button" name="btn_cancel_product" id="btn_cancel_product" class="btn btn-warning">Cancelar</button>
				</div>
			</div>
		</div><!-- FIN DE DESCRIPCION -->
		<div id="precio" class="tab-pane fade">
			<div id="container_updproduct_price" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 pl_0 pr_7">
						<label class="label label_blue_sky"  for="sel_medida_precio">Medida:</label>
						<select  class="form-control" id="sel_medida_precio" name="sel_medida_precio">
	<?php				while($rs_medida=$qry_medida->fetch_array(MYSQLI_ASSOC)) {
								if($rs_medida['AI_medida_id']===$rs_product['TX_producto_medida']){
	?>							<option value="<?php echo $rs_medida['AI_medida_id']; ?>" selected="selected"><?php echo $rs_medida['TX_medida_value']; ?></option>
	<?php					}else{?>
									<option value="<?php echo $rs_medida['AI_medida_id']; ?>"><?php echo $rs_medida['TX_medida_value']; ?></option>
	<?php 				}
							}
	?>   			</select>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 pl_0 pr_7">
						<label class="label label_blue_sky" for="txt_unidadespormedida">Cant/Medida</label>
						<input type="text" class="form-control" name="txt_unidadespormedida" id="txt_unidadespormedida" value="1">
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 pl_0 pr_7">
						<label class="label label_blue_sky"  for="sel_letter">Letra:</label>
						<select  class="form-control" id="sel_letter" name="sel_letter" tabindex="6">
		<?php  		$percent = 0;
							while($rs_letra=$qry_letra->fetch_array(MYSQLI_ASSOC)){
								if($rs_letra['AI_letra_id']===$rs_product['producto_AI_letra_id']){
									$percent = $rs_letra['TX_letra_porcentaje'];?>
									<option value="<?php echo $rs_letra['AI_letra_id']; ?>" selected="selected"><?php echo $rs_letra['TX_letra_value']." (".$rs_letra['TX_letra_porcentaje']."%)"; ?></option>
		<?php				}else{ ?>
									<option value="<?php echo $rs_letra['AI_letra_id']; ?>"><?php echo $rs_letra['TX_letra_value']." (".$rs_letra['TX_letra_porcentaje']."%)"; ?></option>
		<?php 			}
							}
		?>      </select>
					</div>
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 side-btn-md-label">
						<button type="button" id="btn_addproducto_medida" class="btn btn-success" ><i class="fa fa-plus"></i></button>
					</div>
				</div>
				<div id="container_tbl_producto_medida" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<table id="tbl_producto_medida" class="table table-bordered table-condensed tbl-padding-0">
						<thead class="bg-primary">
							<tr>
								<th class="al_center" style="width:30%;">Medida</th>
								<th class="al_center" style="width:30%;">Cant/Medida</th>
								<th class="al_center" style="width:30%;">Letra</th>
								<th class="al_center" style="width:20%;"></th>
							</tr>
						</thead>
						<tbody>
<?php 				foreach($raw_producto_medida as $key => $rs_producto_medida){ ?>
								<tr>
									<td onclick="get_medida_precio(<?php echo $rs_producto_medida['AI_medida_id']; ?>)" class="al_center"><?php echo $rs_producto_medida['TX_medida_value']; ?></td>
									<td class="al_center"><?php echo $rs_producto_medida['TX_rel_productomedida_cantidad']; ?></td>
									<td class="al_center"><?php echo $rs_producto_medida['TX_letra_value']." (".$rs_producto_medida['TX_letra_porcentaje']."%)"; ?></td>
									<td class="al_center"><button type="button" class="btn btn-danger btn-sm" id="btn_delmedida" name="btn_delmedida" onclick="del_producto_medida(<?php echo $rs_producto_medida['AI_rel_productomedida_id']; ?>)"><i class="fa fa-times"></i></button> </td>
								</tr>
<?php 				} ?>
						</tbody>
						<tfoot class="bg-primary">
							<tr>
								<td colspan="4">&nbsp;</td>
							</tr>
						</tfoot>
					</table>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-v-10">
					<input type="hidden" name="hd_medida" alt="<?php echo $rs_product['TX_producto_medida']; ?>" value="" id="hd_medida" />
					<div id="container_measure_selected" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center">
						<?php echo $measure_selected; ?>
					</div>
				</div>
				<div id="container_precio4" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
					<label class="label label_blue_sky"  for="txt_precio4">Standard:</label>
					<input type="text" class="form-control input-sm" id="txt_precio4" name="txt_precio4" value="<?php echo $rs_precio['TX_precio_cuatro']; ?>">
				</div>
				<div id="container_precio5" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
					<label class="label label_blue_sky"  for="txt_precio5">P. M&aacute;ximo:</label>
					<input type="text" class="form-control input-sm" id="txt_precio5" name="txt_precio5" value="<?php echo $rs_precio['TX_precio_cinco']; ?>">
				</div>
				<div id="container_precio3" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
					<label class="label label_blue_sky"  for="txt_precio3">Descuento #3:</label>
					<input type="text" class="form-control input-sm" id="txt_precio3" name="txt_precio3" value="<?php echo $rs_precio['TX_precio_tres']; ?>">
				</div>
				<div id="container_precio2" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
					<label class="label label_blue_sky"  for="txt_precio2">Descuento #2:</label>
					<input type="text" class="form-control input-sm" id="txt_precio2" name="txt_precio2" value="<?php echo $rs_precio['TX_precio_dos']; ?>">
				</div>
				<div id="container_precio1" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
					<label class="label label_blue_sky"  for="txt_precio1">Descuento #1:</label>
					<input type="text" class="form-control input-sm" id="txt_precio1" name="txt_precio1" value="<?php echo $rs_precio['TX_precio_uno']; ?>">
				</div>
				<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<button type="button" name="btn_save_price" id="btn_save_price" class="btn btn-success">Agregar Precios</button>
				</div>
				<div id="container_alert" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 hide">
					<div class="alert alert-warning alert-dismissible " role="alert">
					</div>
				</div>	
			</div>


<!-- ####################  COMPRAS #############-->
<div id="container_tbl_purchase_price" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
	<table id="tbl_purchase_price" class="table table-bordered table-condensed table-striped">
		<caption class="caption">Historial de Precios de Compras</caption>
		<thead class="bg-primary">
		<tr>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Fecha</th>
			<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Precio</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Imp.</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Desc.</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Total</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Cantidad</th>
		</tr>
		</thead>
		<tfoot class="bg-primary">
		<tr>
			<td colspan="6"></td>
		</tr>
		</tfoot>
		<tbody>
<?php
		while ($rs_datocompra_listado = $qry_datocompra_listado->fetch_array(MYSQLI_ASSOC)) {
			$descuento = ($rs_datocompra_listado['TX_datocompra_descuento']*$rs_datocompra_listado['TX_datocompra_precio'])/100;
			$precio_descuento = $rs_datocompra_listado['TX_datocompra_precio']-$descuento;
			$impuesto = ($rs_datocompra_listado['TX_datocompra_impuesto']*$precio_descuento)/100;
			$total_precio = $precio_descuento + $impuesto;
?>
			<tr>
				<td><?php echo date('d-m-Y', strtotime($rs_datocompra_listado['TX_facturacompra_fecha'])); ?></td>
				<td>B/ <?php echo $rs_datocompra_listado['TX_datocompra_precio']; ?></td>
				<td><?php echo $rs_datocompra_listado['TX_datocompra_impuesto']; ?>%</td>
				<td><?php echo $rs_datocompra_listado['TX_datocompra_descuento']; ?>%</td>
				<td>B/ <?php echo number_format($total_precio,2); ?></td>
				<td><?php echo $rs_datocompra_listado['TX_datocompra_cantidad']; ?></td>
			</tr>
<?php
		}
?>
		</tbody>
	</table>
</div>

<div id="container_tbl_historical_price" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
	<table id="tbl_historical_price" class="table table-bordered table-condensed table-striped">
		<caption>Historial de Precios de Venta</caption>
		<thead class="bg_green">
		<tr>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Fecha</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">P.Reg.</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">P.Max.</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">D. #3</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">D. #2</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">D. #1</th>
		</tr>
		</thead>
		<tfoot class="bg_green">
			<tr>
				<td colspan="6"></td>
			</tr>
		</tfoot>
		<tbody>
<?php
		while ($rs_precio_listado = $qry_precio_listado->fetch_array(MYSQLI_ASSOC)) {
?>
	<tr title="<?php echo $raw_user[$rs_precio_listado['precio_AI_user_id']].", ".$rs_precio_listado['TX_precio_comentario']; ?>">
		<td><?php echo date('d-m-Y', strtotime($rs_precio_listado['TX_precio_fecha'])); ?></td>
		<td><?php if (!empty($rs_precio_listado['TX_precio_cuatro'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_cuatro'],2); } ?></td>
		<td><?php if (!empty($rs_precio_listado['TX_precio_cinco'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_cinco'],2); } ?></td>
		<td><?php if (!empty($rs_precio_listado['TX_precio_tres'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_tres'],2); } ?></td>
		<td><?php if (!empty($rs_precio_listado['TX_precio_dos'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_dos'],2); } ?></td>
		<td><?php if (!empty($rs_precio_listado['TX_precio_uno'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_uno'],2); } ?></td>
	</tr>
<?php
		}
?>
		</tbody>
	</table>
</div>
</div>
<!-- ###################  FIN DE PRECIO TAB PANE -->



</div>


	<div id="footer">
		<?php require 'attached/php/req_footer_popup.php'; ?>
	</div>
	</form>
</div>
<script type="text/javascript">
		const cls_stock = new class_stock;
</script>
</body>
</html>
 