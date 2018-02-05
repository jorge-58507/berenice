<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
$product_id=$_GET['a'];

$qry_warehouse=mysql_query("SELECT * FROM bh_almacen");
$rs_warehouse=mysql_fetch_assoc($qry_warehouse);

$qry_product=mysql_query("SELECT bh_producto.AI_producto_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_producto.TX_producto_exento FROM bh_producto WHERE AI_producto_id = '$product_id' AND TX_producto_activo = '0'")or die(mysql_error());
$rs_product=mysql_fetch_assoc($qry_product);

$qry_product_letter = mysql_query("SELECT bh_letra.TX_letra_value, bh_letra.TX_letra_porcentaje FROM (bh_producto INNER JOIN bh_letra ON bh_letra.AI_letra_id = bh_producto.producto_AI_letra_id) WHERE AI_producto_id = '$product_id'")or die(mysql_error());
$rs_product_letter = mysql_fetch_array($qry_product_letter);
$porcentaje = (!empty($rs_product_letter['TX_letra_porcentaje'])) ? $rs_product_letter['TX_letra_porcentaje'] : "0" ;
//$porcentaje = $rs_product_letter['TX_letra_porcentaje'];



$qry_precio=mysql_query("SELECT bh_datocompra.TX_datocompra_precio, bh_facturacompra.TX_facturacompra_fecha
FROM (bh_datocompra
INNER JOIN bh_facturacompra ON bh_datocompra.datocompra_AI_facturacompra_id = bh_facturacompra.AI_facturacompra_id)
WHERE bh_datocompra.datocompra_AI_producto_id = '$product_id'
ORDER BY TX_facturacompra_fecha DESC LIMIT 1");
$rs_precio=mysql_fetch_assoc($qry_precio);
$last_price=$rs_precio['TX_datocompra_precio'];

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
	window.resizeTo("555", "490");

var last_price = '<?php echo $last_price; ?>';
last_price = val_intw2dec(last_price);
console.log(last_price);
$("#alert").css("display","none");
$('#btn_acept').click(function(){
	var id = $("#txt_product").attr("alt");
	var txt_price = document.forms[0]['txt_price'].name;
	var txt_quantity = document.forms[0]['txt_quantity'].name;
	var txt_itbm = document.forms[0]['txt_itbm'].name;
	var txt_discount = document.forms[0]['txt_discount'].name;
	if (id == ""||isEmpty(txt_price)||isEmpty(txt_quantity)||isEmpty(txt_itbm)||isEmpty(txt_discount)){
		alert("Faltan datos para continuar.");
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


$('#txt_quantity, #txt_p_4').validCampoFranz('.0123456789');
$('#txt_price').validCampoFranz('.0123456789');
$('#txt_itbm').validCampoFranz('.0123456789');
$('#txt_discount').validCampoFranz('.0123456789');

// $("#txt_price").blur(function(){
// 	if(this.value != last_price){
// 		console.log("value: "+this.value+" last: "+last_price);
// 		$("#alert").show(500);
// 	}else{
// 		$("#alert").hide(500);
// 	}
// });
$("#txt_price").on("keyup",function(){
var base = parseFloat($("#txt_price").val()); var impuesto = $("#txt_itbm").val();
var descuento = $("#txt_discount").val(); var cantidad = $("#txt_quantity").val();
var letra = '<?php echo $porcentaje; ?>';

	var sugerido = ((base*letra)/100)+base;
	sugerido = sugerido.toFixed(2);

	var precio = cantidad*base;
	var descuento = (precio*descuento)/100;
	var precio_descuento = precio+descuento;
	var impuesto = (precio_descuento*impuesto)/100;
	var total = precio_descuento+impuesto;
	total = total.toFixed(2);

	if (!isNaN(total)){
		$("#span_total").html(total);
	}
	if (!isNaN(total)){
		$("#txt_p_4").val(sugerido);
	}
	$("#txt_p_4").on("blur", function(){
		this.value = val_intw2dec(this.value);
	})

});

$("#txt_quantity, #txt_price, #txt_itbm, #txt_discount").on("blur",function(){
	this.value = val_intw2dec(this.value);
	if ($(this).prop("id") === 'txt_price') {
		if(this.value != last_price){
			console.log("value: "+this.value+" last: "+last_price);
			$("#alert").show(500);
		}else{
			$("#alert").hide(500);
		}
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
<form method="post" name="form_product2purchase">
<div id="container_product" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
	<label for="txt_product">Producto:</label>
    <input type="text" name="txt_product" id="txt_product" alt="<?php echo $rs_product['AI_producto_id'] ?>" class="form-control" readonly="readonly" value="<?php echo $rs_product['TX_producto_value'] ?>" />
</div>
<div id="container_measure" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	<label for="txt_measure">Medida:</label>
    <input type="text" name="txt_measure" id="txt_measure" class="form-control" readonly="readonly" value="<?php echo $rs_product['TX_producto_medida'] ?>" />
</div>
<div id="container_quantity" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	<label for="span_letra">Letra:</label>
		<span id="span_letra" class="form-control"><?php echo $rs_product_letter['TX_letra_value'] ?></span>
</div>
<div id="container_code" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	<label for="txt_code">C&oacute;digo:</label>
    <input type="text" name="txt_code" id="txt_code" class="form-control" readonly="readonly" value="<?php echo $rs_product['TX_producto_codigo'] ?>" />
</div>
<div id="container_quantity" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	<label for="txt_quantity">Cantidad:</label>
    <input type="text" name="txt_quantity" id="txt_quantity" class="form-control" value="1" onkeyup="chk_quantity(this)" />
</div>
<div id="container_price" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
    <label for="txt_price">Costo Base:</label>
    <input type="text" name="txt_price" id="txt_price" class="form-control" onkeyup="chk_price(this)" autofocus="autofocus" />
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
