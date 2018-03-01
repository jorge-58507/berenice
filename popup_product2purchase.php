<?php
require 'bh_conexion.php';
$link=conexion();

$product_id=$_GET['a'];

$qry_warehouse=$link->query("SELECT * FROM bh_almacen");
$rs_warehouse=$qry_warehouse->fetch_array();

$qry_product=$link->query("SELECT bh_producto.AI_producto_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_producto.TX_producto_exento FROM bh_producto WHERE AI_producto_id = '$product_id' AND TX_producto_activo = '0'")or die($link->error);
$rs_product=$qry_product->fetch_array();

$qry_product_letter = $link->query("SELECT bh_letra.TX_letra_value, bh_letra.TX_letra_porcentaje FROM (bh_producto INNER JOIN bh_letra ON bh_letra.AI_letra_id = bh_producto.producto_AI_letra_id) WHERE AI_producto_id = '$product_id'")or die($link->error);
$rs_product_letter = $qry_product_letter->fetch_array();
$porcentaje = (!empty($rs_product_letter['TX_letra_porcentaje'])) ? $rs_product_letter['TX_letra_porcentaje'] : "0" ;

$qry_datocompra_listado = $link->query("SELECT bh_facturacompra.TX_facturacompra_fecha,bh_facturacompra.TX_facturacompra_numero,bh_datocompra.TX_datocompra_precio,bh_datocompra.TX_datocompra_impuesto,bh_datocompra.TX_datocompra_descuento FROM ((bh_datocompra INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datocompra.datocompra_AI_producto_id) INNER JOIN bh_facturacompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id)
WHERE bh_producto.AI_producto_id = '$product_id' ORDER BY TX_facturacompra_fecha DESC LIMIT 3")or die($link->error);


$qry_precio=$link->query("SELECT bh_datocompra.TX_datocompra_precio, bh_facturacompra.TX_facturacompra_fecha
FROM (bh_datocompra
INNER JOIN bh_facturacompra ON bh_datocompra.datocompra_AI_facturacompra_id = bh_facturacompra.AI_facturacompra_id)
WHERE bh_datocompra.datocompra_AI_producto_id = '$product_id'
ORDER BY TX_facturacompra_fecha DESC, AI_facturacompra_id DESC LIMIT 1");
$rs_precio=$qry_precio->fetch_array();
$last_price=$rs_precio['TX_datocompra_precio'];

$qry_letra=$link->query("SELECT AI_letra_id, TX_letra_value, TX_letra_porcentaje FROM bh_letra")or die($link->error);

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
	window.resizeTo("555", "670");

	$("#form_product2purchase").on("keyup", function(e){
		console.log(e.which);
		if (e.which === 13) {
			$("#btn_acept").click();
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
	$("#txt_p_4").val(sugerido);
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
	($(this).val() === "") ?	$(this).val("0.00") :	this.value = val_intw4dec(this.value);
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
	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-2" >
		<div id="logo" ></div>
	</div>

</div>

<div id="content-sidebar_popup" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<form method="post" id="form_product2purchase" name="form_product2purchase">
<div id="container_product" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
	<label for="txt_product">Producto:</label>
    <input type="text" name="txt_product" id="txt_product" alt="<?php echo $rs_product['AI_producto_id'] ?>" class="form-control" readonly="readonly" value="<?php echo $rs_product['TX_producto_value'] ?>" />
</div>
<div id="container_measure" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	<label for="txt_measure">Medida:</label>
    <input type="text" name="txt_measure" id="txt_measure" class="form-control" readonly="readonly" value="<?php echo $rs_product['TX_producto_medida'] ?>" />
</div>
<div id="container_quantity" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	<label for="sel_letra">Letra:</label>
	<select class="form-control" id="sel_letra">
<?php while($rs_letra = $qry_letra->fetch_array()){
	if ($rs_letra['TX_letra_value'] === $rs_product_letter['TX_letra_value']) {
?>
		<option value="<?php echo $rs_letra['AI_letra_id']; ?>" label="<?php echo $rs_letra['TX_letra_porcentaje']; ?>" selected><?php echo $rs_letra['TX_letra_value']; ?></option>
<?php
}else{
?>
		<option value="<?php echo $rs_letra['AI_letra_id']; ?>" label="<?php echo $rs_letra['TX_letra_porcentaje']; ?>" ><?php echo $rs_letra['TX_letra_value']; ?></option>
<?php
			}
		} ?>
	</select>
</div>
<div id="container_code" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	<label for="txt_code">C&oacute;digo:</label>
    <input type="text" name="txt_code" id="txt_code" class="form-control" readonly="readonly" value="<?php echo $rs_product['TX_producto_codigo'] ?>" />
</div>
<div id="container_quantity" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	<label for="txt_quantity">Cantidad:</label>
    <input type="text" name="txt_quantity" id="txt_quantity" class="form-control" value="1" onkeyup="chk_quantity(this)" autofocus="autofocus" />
</div>
<div id="container_price" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
    <label for="txt_price">Costo Base:</label>
    <input type="text" name="txt_price" id="txt_price" class="form-control" onkeyup="chk_price(this)"  />
</div>
<div id="container_regular" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
    <label for="txt_p_4">Precio Regular:</label>
    <input type="text" name="txt_p_4" id="txt_p_4" class="form-control" />
</div>
<div id="container_itbm" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
    <label for="txt_itbm">Impuesto %:</label>
    <input type="text" name="txt_itbm" id="txt_itbm" class="form-control" value="<?php echo $rs_product['TX_producto_exento'] ?>" onkeyup="chk_itbm(this)"/>
</div>
<div id="container_discount" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
    <label for="txt_discount">Descuento %:</label>
    <input type="text" name="txt_discount" id="txt_discount" class="form-control" value="0" onkeyup="chk_descuento(this)" />
</div>
<div id="container_total" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
    <label for="txt_discount">Total:</label>
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
		<table id="tbl_purchase_price" class="table table-bordered table-condensed">
			<caption class="caption">Historial de Precios de Compras</caption>
			<thead class="bg-primary">
			<tr>
				<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Fecha</th>
				<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">N&deg; Fact</th>
				<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Precio</th>
			</tr>
			</thead>
			<tfoot class="bg-primary">
			<tr>
				<td colspan="5"></td>
			</tr>
			</tfoot>
			<tbody>
		<?php
			while ($rs_datocompra_listado = $qry_datocompra_listado->fetch_array()) {
				$descuento = ($rs_datocompra_listado['TX_datocompra_descuento']*$rs_datocompra_listado['TX_datocompra_precio'])/100;
				$precio_descuento = $rs_datocompra_listado['TX_datocompra_precio']-$descuento;
				$impuesto = ($rs_datocompra_listado['TX_datocompra_impuesto']*$precio_descuento)/100;
				$total_precio = $precio_descuento + $impuesto;
		?>
				<tr>
					<td><?php echo date('d-m-Y', strtotime($rs_datocompra_listado['TX_facturacompra_fecha'])); ?></td>
					<td><?php echo $rs_datocompra_listado['TX_facturacompra_numero']; ?></td>
					<td>B/ <?php echo $rs_datocompra_listado['TX_datocompra_precio']; ?></td>
				</tr>
		<?php
			}
		?>
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
