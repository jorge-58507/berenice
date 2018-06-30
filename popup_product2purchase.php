<?php 
require 'bh_conexion.php';
$link=conexion();

$product_id=$_GET['a'];

$qry_product=$link->query("SELECT bh_producto.AI_producto_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_producto.TX_producto_exento FROM bh_producto WHERE AI_producto_id = '$product_id' AND TX_producto_activo = '0'")or die($link->error);
$rs_product=$qry_product->fetch_array();

$qry_product_letter = $link->query("SELECT bh_letra.TX_letra_value, bh_letra.TX_letra_porcentaje FROM (bh_producto INNER JOIN bh_letra ON bh_letra.AI_letra_id = bh_producto.producto_AI_letra_id) WHERE AI_producto_id = '$product_id'")or die($link->error);
$rs_product_letter = $qry_product_letter->fetch_array();
$porcentaje = (!empty($rs_product_letter['TX_letra_porcentaje'])) ? $rs_product_letter['TX_letra_porcentaje'] : "0" ;

$qry_precio=$link->query("SELECT bh_datocompra.TX_datocompra_precio, bh_facturacompra.TX_facturacompra_fecha
FROM (bh_datocompra
INNER JOIN bh_facturacompra ON bh_datocompra.datocompra_AI_facturacompra_id = bh_facturacompra.AI_facturacompra_id)
WHERE bh_datocompra.datocompra_AI_producto_id = '$product_id'
ORDER BY TX_facturacompra_fecha DESC, AI_facturacompra_id DESC LIMIT 1");
$rs_precio=$qry_precio->fetch_array();
$last_price=$rs_precio['TX_datocompra_precio'];

$qry_letra=$link->query("SELECT AI_letra_id, TX_letra_value, TX_letra_porcentaje FROM bh_letra")or die($link->error);

$qry_precio_listado = $link->query("SELECT bh_precio.AI_precio_id, bh_precio.TX_precio_fecha, bh_precio.precio_AI_medida_id, bh_precio.TX_precio_uno, bh_precio.TX_precio_dos, bh_precio.TX_precio_tres, bh_precio.TX_precio_cuatro, bh_precio.TX_precio_cinco, bh_producto.AI_producto_id FROM (bh_precio INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_precio.precio_AI_producto_id) WHERE bh_producto.AI_producto_id = '$product_id' ORDER BY TX_precio_fecha DESC, AI_precio_id DESC")or die($link->error);

$qry_datocompra_listado = $link->query("SELECT bh_facturacompra.TX_facturacompra_fecha,bh_datocompra.TX_datocompra_precio,bh_datocompra.TX_datocompra_impuesto,bh_datocompra.TX_datocompra_descuento,bh_datocompra.TX_datocompra_medida FROM ((bh_datocompra INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datocompra.datocompra_AI_producto_id) INNER JOIN bh_facturacompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id)
WHERE bh_producto.AI_producto_id = '$product_id' ORDER BY TX_facturacompra_fecha DESC, AI_facturacompra_id DESC")or die($link->error);
$raw_datocompra_listado=array();
if ($qry_datocompra_listado->num_rows > 0) {
	while ($rs_datocompra_listado = $qry_datocompra_listado->fetch_array(MYSQLI_ASSOC)) {
		$raw_datocompra_listado[]=$rs_datocompra_listado;
	}
	$ultimo_precio_compra = $raw_datocompra_listado[0]['TX_datocompra_precio'];
}else{
	$ultimo_precio_compra = '';
}
$qry_producto_medida = $link->query("SELECT bh_medida.AI_medida_id, bh_medida.TX_medida_value, rel_producto_medida.AI_rel_productomedida_id, rel_producto_medida.TX_rel_productomedida_cantidad FROM (bh_medida INNER JOIN rel_producto_medida ON bh_medida.AI_medida_id = rel_producto_medida.productomedida_AI_medida_id) WHERE productomedida_AI_producto_id = '{$_GET['a']}'")or die($link->error);
$raw_producto_medida=array();
while ($rs_producto_medida = $qry_producto_medida->fetch_array(MYSQLI_ASSOC)) {
	$raw_producto_medida[]=$rs_producto_medida;
}

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
<link href="attached/css/popup_css.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/product2purchase_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	$("#form_product2purchase").on("keyup", function(e){
		console.log(e.which);
		if (e.which === 13) {
			$("#btn_acept").focus()
			setTimeout( function(){ $("#btn_acept").click(); }, 300);
		}
	})
var last_price = '<?php echo $last_price; ?>';
last_price = val_intw4dec(last_price);
$("#alert").css("display","none");
$('#btn_acept').click(function(){
	var id = $("#txt_product").attr("alt");
	var txt_price = document.forms[0]['txt_price'].name;
	var txt_quantity = document.forms[0]['txt_quantity'].name;
	var txt_itbm = document.forms[0]['txt_itbm'].name;
	var txt_discount = document.forms[0]['txt_discount'].name;
	if (id == ""||isEmpty(txt_price)||isEmpty(txt_quantity)||isEmpty(txt_itbm)||isEmpty(txt_discount)){
		return false;
	}
	if($("#txt_p_4").val() === ''){ set_bad_field('txt_p_4'); return false; }else{ set_good_field('txt_p_4'); }
	ans = val_intwdec($("#txt_quantity").val());
	if(!ans){ isInvalid("txt_quantity"); return false; }else{ isValid("txt_quantity"); }
	ans = val_intwdec($("#txt_price").val());
	if(!ans){ isInvalid("txt_price"); return false; }else{ isValid("txt_price"); }
	ans = val_intwdec($("#txt_itbm").val());
	if(!ans){ isInvalid("txt_itbm"); return false; }else{ isValid("txt_quantity"); }
	ans = val_intwdec($("#txt_discount").val());
	if(!ans){ isInvalid("txt_discount"); return false; }else{ isValid("txt_quantity"); }
	plus_product2purchase(id);
})
$('#btn_cancel').click(function(){
	self.close();
})

$("#sel_letra").on("change", function(){
	$.ajax({	data: {"a" : this.value, "b" : <?php echo $product_id; ?> },	type: "GET",	dataType: "text",	url: "attached/get/upd_product_letter.php", })
	 .done(function( data, textStatus, jqXHR ) { console.log("GOOD "+data+" "+textStatus); suggest_price(); })
	 .fail(function( jqXHR, textStatus, errorThrown ) {	 console.log("BAD "+textStatus);	});
})

$('#txt_quantity').on("keyup", function(){
	suggest_price();
})

$('#txt_quantity, #txt_p_4').validCampoFranz('.0123456789');
$('#txt_price').validCampoFranz('.0123456789');
$('#txt_itbm').validCampoFranz('.0123456789');
$('#txt_discount').validCampoFranz('.0123456789');

function suggest_price(){
	var base = ($("#txt_price").val() === '') ? 0.00 : $("#txt_price").val(); base = parseFloat(base);
	var letra = $("#sel_letra option:selected").attr("label");
	var sugerido = ((base*letra)/100)+base;

	sugerido = sugerido.toFixed(2);
	$("#txt_p_4").attr("placeholder",sugerido);
}

function cal_total(){
	var base = ($("#txt_price").val() === '') ? 0.00 : $("#txt_price").val(); base = parseFloat(base);
	var impuesto = ($("#txt_itbm").val() === '') ? 0.00 : $("#txt_itbm").val(); impuesto = parseFloat(impuesto);
	var descuento = ($("#txt_discount").val() === '') ? 0.00 : $("#txt_discount").val(); descuento = parseFloat(descuento);
	var cantidad = ($("#txt_quantity").val() === '') ? 0.00 : $("#txt_quantity").val(); cantidad = parseFloat(cantidad);
	var precio = cantidad*base;
	var descuento = (precio*descuento)/100;
	var precio_descuento = precio-descuento;
	var impuesto = (precio_descuento*impuesto)/100;
	var total = precio_descuento+impuesto;
	total = total.toFixed(2);

	if (!isNaN(total)){
		$("#span_total").html(total);
	}
}

$("#txt_price, #txt_quantity, #txt_itbm, #txt_discount").on("keyup",function(){
	suggest_price();
	cal_total();
});


$("#txt_p_4, #txt_quantity, #txt_itbm, #txt_discount").on("blur", function(){
	($(this).val() === "") ?	$(this).val("0.00") :	this.value = val_intw2dec(this.value);
})
$("#txt_quantity, #txt_itbm, #txt_discount").on("blur",function(){
	this.value = val_intw2dec(this.value);
});

$("#txt_price").on("blur",function(){
	($(this).val() === "") ?	$(this).val("") :	this.value = val_intw4dec(this.value);
		if(this.value != last_price){
			// console.log("value: "+this.value+" last: "+last_price);
			$("#alert").show(500);
		}else{
			$("#alert").hide(500);
		}
});



});


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
<form method="post" id="form_product2purchase" name="form_product2purchase">
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no_padding">
		<div id="container_product" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label class="label label_blue_sky" for="txt_product">Producto:</label>
		  <input type="text" name="txt_product" id="txt_product" alt="<?php echo $rs_product['AI_producto_id'] ?>" class="form-control" readonly="readonly" value="<?php echo $r_function->replace_special_character($rs_product['TX_producto_value']); ?>" />
		</div>
		<div id="container_quantity" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			<label class="label label_blue_sky" for="sel_letra">Letra:</label>
			<select class="form-control" id="sel_letra">
<?php 	while($rs_letra = $qry_letra->fetch_array()){
					if ($rs_letra['TX_letra_value'] === $rs_product_letter['TX_letra_value']) {
?>					<option value="<?php echo $rs_letra['AI_letra_id']; ?>" label="<?php echo $rs_letra['TX_letra_porcentaje']; ?>" selected><?php echo $rs_letra['TX_letra_value']." (".$rs_letra['TX_letra_porcentaje']."%)"; ?></option>
<?php 		}else{	?>
						<option value="<?php echo $rs_letra['AI_letra_id']; ?>" label="<?php echo $rs_letra['TX_letra_porcentaje']; ?>" ><?php echo $rs_letra['TX_letra_value']." (".$rs_letra['TX_letra_porcentaje']."%)"; ?></option>
<?php 		}
				} ?>
			</select>
		</div>
		<div id="container_code" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			<label class="label label_blue_sky" for="txt_code">C&oacute;digo:</label>
	    <input type="text" name="txt_code" id="txt_code" class="form-control" readonly="readonly" value="<?php echo $rs_product['TX_producto_codigo'] ?>" />
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label class="label label_blue_sky" class="label label_blue_sky"  for="sel_medida">Medida:</label>
			<select class="form-control" id="sel_measure" name="sel_measure" autofocus><?php
				foreach ($raw_producto_medida as $key => $rs_medida) {
					if($rs_medida['AI_medida_id']===$rs_product['TX_producto_medida']){
						$producto_medida = $rs_product['TX_producto_medida'];
		?>			<option value="<?php echo $rs_medida['AI_medida_id']; ?>" selected="selected"><?php echo $rs_medida['TX_medida_value']." (".$rs_medida['TX_rel_productomedida_cantidad'].")"; ?></option>
		<?php	}else{ 	?>
						<option value="<?php echo $rs_medida['AI_medida_id']; ?>"><?php echo $rs_medida['TX_medida_value']." (".$rs_medida['TX_rel_productomedida_cantidad'].")"; ?></option>
		<?php }
				}					?>
			</select>
		</div>
		<div id="container_quantity" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
			<label class="label label_blue_sky" for="txt_quantity">Cantidad:</label>
	    <input type="text" name="txt_quantity" id="txt_quantity" class="form-control" value="1" onkeyup="chk_quantity(this)" />
		</div>
		<div id="container_price" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	    <label class="label label_blue_sky" for="txt_price">Costo Base:</label>
	    <input type="text" name="txt_price" id="txt_price" class="form-control" onkeyup="chk_price(this)" value="<?php echo $ultimo_precio_compra; ?>" />
		</div>
		<div id="container_regular" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	    <label class="label label_blue_sky" for="txt_p_4">P. Venta/<?php echo $raw_medida[$producto_medida]; ?>:</label>
	    <input type="text" name="txt_p_4" id="txt_p_4" class="form-control" />
		</div>
		<div id="container_itbm" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	    <label class="label label_blue_sky" for="txt_itbm">Impuesto %:</label>
	    <input type="text" name="txt_itbm" id="txt_itbm" class="form-control" value="<?php echo $rs_product['TX_producto_exento'] ?>" onkeyup="chk_itbm(this)"/>
		</div>
		<div id="container_discount" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	    <label class="label label_blue_sky" for="txt_discount">Descuento %:</label>
	    <input type="text" name="txt_discount" id="txt_discount" class="form-control" value="0" onkeyup="chk_descuento(this)" />
		</div>
		<div id="container_total" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	    <label class="label label_blue_sky" for="txt_discount">Total:</label>
			<span id="span_total" class="form-control bg-disabled">0.00</span>
    </div>
		<div id="container_button" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<button type="button" id="btn_acept" class="btn btn-success">Aceptar</button>
			&nbsp;
			<button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
		</div>
		<div id="alert" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div id="div_alert" class="alert alert-danger">
				<strong>Atenci&oacute;n!</strong> El precio cambi&oacute;.
			</div>
		</div>
	</div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		<div id="container_tbl_purchase_price" style="max-height: 167px; padding: 0;" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<table id="tbl_purchase_price" class="table table-bordered table-condensed table-striped">
				<caption class="caption">Historial de Precios de Compras</caption>
				<thead class="bg-primary">
					<tr>
						<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Fecha</th>
						<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Medida</th>
						<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Precio</th>
						<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Imp.</th>
						<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Desc.</th>
						<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Total</th>
					</tr>
				</thead>
				<tfoot class="bg-primary">
					<tr>
						<td colspan="6"></td>
					</tr>
				</tfoot>
				<tbody>
		<?php
					foreach ($raw_datocompra_listado as $key => $rs_datocompra_listado){
						$descuento = ($rs_datocompra_listado['TX_datocompra_descuento']*$rs_datocompra_listado['TX_datocompra_precio'])/100;
						$precio_descuento = $rs_datocompra_listado['TX_datocompra_precio']-$descuento;
						$impuesto = ($rs_datocompra_listado['TX_datocompra_impuesto']*$precio_descuento)/100;
						$total_precio = $precio_descuento + $impuesto;
			?>
						<tr>
							<td><?php echo date('d-m-Y', strtotime($rs_datocompra_listado['TX_facturacompra_fecha'])); ?></td>
							<td class="font_bolder al_center"><?php echo $raw_medida[$rs_datocompra_listado['TX_datocompra_medida']]; ?></td>
							<td>B/ <?php echo $rs_datocompra_listado['TX_datocompra_precio']; ?></td>
							<td><?php echo $rs_datocompra_listado['TX_datocompra_impuesto']; ?>%</td>
							<td><?php echo $rs_datocompra_listado['TX_datocompra_descuento']; ?>%</td>
							<td>B/ <?php echo number_format($total_precio,2); ?></td>
						</tr>
		<?php }	?>
				</tbody>
			</table>
		</div>
		<div id="container_tbl_historical_price" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"  style="max-height: 167px; padding: 0;" >
			<table id="tbl_historical_price" class="table table-bordered table-condensed table-striped" >
				<caption>Historial de Precios de Venta</caption>
				<thead class="bg_green">
					<tr>
						<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Fecha</th>
						<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Medida</th>
						<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">P.Reg.</th>
						<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">P.Max.</th>
						<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">D. #3</th>
						<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">D. #2</th>
						<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">D. #1</th>
					</tr>
				</thead>
				<tfoot class="bg_green">
					<tr>
						<td colspan="7"></td>
					</tr>
				</tfoot>
				<tbody>
<?php 		$iterator=0;
					while ($rs_precio_listado = $qry_precio_listado->fetch_array(MYSQLI_ASSOC)) {
						$style='';
						if($rs_precio_listado['precio_AI_medida_id'] === $producto_medida && $iterator === 0) { $style="background-color: #e48f8f4d;"; $iterator++; };	?>
						<tr style="<?php echo $style; ?>">
							<td><?php echo date('d-m-Y', strtotime($rs_precio_listado['TX_precio_fecha'])); ?></td>
							<td class="font_bolder al_center"><?php echo $raw_medida[$rs_precio_listado['precio_AI_medida_id']]; ?></td>
							<td><?php if (!empty($rs_precio_listado['TX_precio_cuatro'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_cuatro'],2); } ?></td>
							<td><?php if (!empty($rs_precio_listado['TX_precio_cinco'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_cinco'],2); } ?></td>
							<td><?php if (!empty($rs_precio_listado['TX_precio_tres'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_tres'],2); } ?></td>
							<td><?php if (!empty($rs_precio_listado['TX_precio_dos'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_dos'],2); } ?></td>
							<td><?php if (!empty($rs_precio_listado['TX_precio_uno'])) { echo "B/ ".number_format($rs_precio_listado['TX_precio_uno'],2); } ?></td>
						</tr>
<?php			}		?>
				</tbody>
			</table>
		</div>
	</div>
</form>

</div>

<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
		&copy; Derechos Reservados a: Trilli, S.A. 2017
	</div>
</div>

</div>

</body>
</html>
