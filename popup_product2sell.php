<?php
require 'bh_conexion.php';
$link=conexion();

if(!empty($_GET['a'])){
	$txt_product = "SELECT * FROM bh_producto WHERE AI_producto_id = '{$_GET['a']}'";
}

$qry_product=$link->query($txt_product)or die($link->error);
$nr_product=$qry_product->num_rows;
if($nr_product < 1){
	$cod_jscript="<script type='text/javascript'>self.close();</script>";
	echo $cod_jscript;
}
$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);
$product_id=$rs_product['AI_producto_id'];

$qry_precio=$link->query("SELECT AI_precio_id, TX_precio_uno,TX_precio_dos, TX_precio_tres, TX_precio_cuatro, TX_precio_cinco FROM bh_precio WHERE precio_AI_producto_id = '$product_id' AND precio_AI_medida_id = '{$rs_product['TX_producto_medida']}' AND TX_precio_inactivo = '0' ORDER BY TX_precio_fecha DESC");
$nr_precio =	$qry_precio->num_rows;

$qry_producto_medida = $link->query("SELECT bh_medida.AI_medida_id, bh_medida.TX_medida_value, rel_producto_medida.AI_rel_productomedida_id, rel_producto_medida.TX_rel_productomedida_cantidad FROM (bh_medida INNER JOIN rel_producto_medida ON bh_medida.AI_medida_id = rel_producto_medida.productomedida_AI_medida_id) WHERE productomedida_AI_producto_id = '{$_GET['a']}'")or die($link->error);
$raw_producto_medida=array();
while ($rs_producto_medida = $qry_producto_medida->fetch_array(MYSQLI_ASSOC)) {
	$raw_producto_medida[]=$rs_producto_medida;
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
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript">

$(document).ready(function() {

$("#txt_quantity").blur(function(){
	var quantity = $("#txt_quantity").val();
	var stock = $("#txt_stock").val();

		quantity = parseFloat(quantity);
		stock = parseFloat(stock);

	if(quantity > stock){
		alert("ALERTA: Las existencias son inferiores");
	}
	this.value = val_intw2dec(this.value);
});

$("#input_price").on("blur",function(){
	this.value = val_intw2dec(this.value);
});

$('#btn_acept').click(function(){
	var id = $("#txt_product").attr("alt");
	if(id === '' || $("#input_price").val() === '' || $("#txt_itbm").val() === '' || $("#txt_discount").val() === '' ){
		return false;
	}

	var ans = val_intwdec($("#input_price").val());
	if(!ans){	set_bad_field("input_price");	return false;	}	set_good_field("input_price");
	var url = window.opener.location;
	var patt = new RegExp("old_sale.php");
	ans = patt.test(url)
	var activo = window.opener.$(".tab-pane.active").attr("id");
	if (ans) {
		var	cantidad = $("#txt_quantity").val();
		if(cantidad === ""){cantidad='1.00'}
		precio = $("#input_price").val();	descuento = $("#txt_discount").val();	itbm = $("#txt_itbm").val(); medida = $("#sel_medida").val();
		window.opener.plus_product2viejaventa(id,precio,descuento,itbm,cantidad,medida,'0');
	}else{
		var	cantidad = $("#txt_quantity").val();
		if(cantidad === ""){cantidad='1.00'}
		precio = $("#input_price").val();	descuento = $("#txt_discount").val();	itbm = $("#txt_itbm").val(); medida = $("#sel_medida").val();
		window.opener.plus_product2nuevaventa(id,precio,descuento,itbm,activo,cantidad,medida,'0');
	}
})

$('#btn_cancel').click(function(){
	self.close();
})


$('#txt_quantity').validCampoFranz('.0123456789');
$('#input_price').validCampoFranz('.0123456789');
$('#txt_itbm').validCampoFranz('.0123456789');
$('#txt_discount').validCampoFranz('.0123456789');

$("#form_product2sell").keyup(function(e){
	if(e.which == 13){
		$('#btn_acept').focus();
		$('#btn_acept').click();
	}
});

});

function get_product2sell_price(medida_id){
	$.ajax({	data: {"a" : <?php echo $_GET['a']; ?>, "b" : medida_id },	type: "GET",	dataType: "text",	url: "attached/get/get_product2sell_price.php",	})
	.done(function( data, textStatus, jqXHR ) { console.log("GOOD " + textStatus);
		$("#container_price").html(data);
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log( "BAD " +  textStatus); })
}

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
<form action="pop_form.php" method="post" name="form_product2sell" id="form_product2sell">
<div id="container_product" class="col-xs-12 col-sm-12 col-md-6 col-lg-6 pt_7">
	<label class="label label_blue_sky"  for="txt_product">Producto: </label>
    <input type="text" name="txt_product" id="txt_product" alt="<?php echo $rs_product['AI_producto_id'] ?>" class="form-control" readonly="readonly" value="<?php echo $r_function->replace_special_character($rs_product['TX_producto_value']); ?>" />
</div>
<div id="container_measure" class="col-xs-6 col-sm-6 col-md-6 col-lg-6 pt_7">
	<label class="label label_blue_sky"  for="txt_measure">Codigo:</label>
    <span class=" form-control bg-disabled"><?php echo $rs_product['TX_producto_codigo'] ?></span>
    <input type="hidden" name="txt_measure" id="txt_measure" class="form-control" readonly="readonly" value="<?php echo $rs_product['TX_producto_medida'] ?>" />
</div>
<div id="container_stock" class="col-xs-6 col-sm-6 col-md-6 col-lg-6 pt_7">
	<label class="label label_blue_sky"  for="txt_stock">Existencia:</label>
  <input type="text" name="txt_stock" id="txt_stock" class="form-control" readonly="readonly" value="<?php echo $rs_product['TX_producto_cantidad'] ?>" />
</div>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pt_7">
	<label class="label label_blue_sky"  for="sel_medida">Medida:</label>
	<select class="form-control" id="sel_medida" name="sel_medida" onchange="get_product2sell_price(this.value)"><?php
		foreach ($raw_producto_medida as $key => $rs_medida) {
			if($rs_medida['AI_medida_id']===$rs_product['TX_producto_medida']){
?>			<option value="<?php echo $rs_medida['AI_medida_id']; ?>" selected="selected"><?php echo $rs_medida['TX_medida_value']." (".$rs_medida['TX_rel_productomedida_cantidad'].")"; ?></option>
<?php	}else{ 	?>
				<option value="<?php echo $rs_medida['AI_medida_id']; ?>"><?php echo $rs_medida['TX_medida_value']." (".$rs_medida['TX_rel_productomedida_cantidad'].")"; ?></option>
<?php }
		}					?>
	</select>
</div>
<div id="container_quantity" class="col-xs-6 col-sm-6 col-md-6 col-lg-3 pt_7">
	<label class="label label_blue_sky"  for="txt_quantity">Cantidad:</label>
  <input type="text" name="txt_quantity" id="txt_quantity" class="form-control" placeholder="1" autofocus/>
</div>
<div id="container_price" class="col-xs-6 col-sm-6 col-md-6 col-lg-3 pt_7">
    <label class="label label_blue_sky"  for="input_price">Precio:</label>
<?php
	$rs_precio=$qry_precio->fetch_array(MYSQLI_ASSOC);
	if($nr_precio > 0){
		if($rs_precio['TX_precio_cuatro'] === '0' || $rs_precio['TX_precio_cuatro'] === '' || $rs_precio['TX_precio_cuatro'] === '0.00'){
?>		<input type="text" name="input_price" id="input_price" class="form-control" /><?php
		}else{ ?>
			<select id="input_price" name="input_price" class="form-control">
<?php if($rs_precio['TX_precio_uno'] > 0){ ?>
    		<option value="<?php echo $rs_precio['TX_precio_uno'] ?>"><?php echo $rs_precio['TX_precio_uno'] ?></option>
<?php }
			if($rs_precio['TX_precio_dos'] > 0){ ?>
    		<option value="<?php echo $rs_precio['TX_precio_dos'] ?>"><?php echo $rs_precio['TX_precio_dos'] ?></option>
<?php }
 			if($rs_precio['TX_precio_tres'] > 0){ ?>
    		<option value="<?php echo $rs_precio['TX_precio_tres'] ?>"><?php echo $rs_precio['TX_precio_tres'] ?></option>
<?php }
			if($rs_precio['TX_precio_cuatro'] > 0){ ?>
    <option value="<?php echo $rs_precio['TX_precio_cuatro'] ?>" selected="selected">Regular: <?php echo $rs_precio['TX_precio_cuatro'] ?></option>
<?php }
			if($rs_precio['TX_precio_cinco'] > 0){ ?>
    <option value="<?php echo $rs_precio['TX_precio_cinco'] ?>"><?php echo $rs_precio['TX_precio_cinco'] ?></option>
<?php } ?>
    </select>
<?php }
		}else{
?> 		<input type="text" name="input_price" id="input_price" class="form-control" /><?php
	} ?>
</div>
<div id="container_itbm" class="col-xs-6 col-sm-6 col-md-6 col-lg-3 pt_7">
  <label class="label label_blue_sky"  for="txt_itbm">Imp.%:</label>
  <input type="text" name="txt_itbm" id="txt_itbm" class="form-control" value="<?php echo $rs_product['TX_producto_exento'] ?>" readonly="readonly"/>
</div>
<div id="container_discount" class="col-xs-6 col-sm-6 col-md-6 col-lg-3 pt_7">
  <label class="label label_blue_sky"  for="txt_discount">Descuento%:</label>
<?php
	if ($_COOKIE['coo_iuser'] < 3) { ?>
		<input type="text" name="txt_discount" id="txt_discount" class="form-control" value="<?php echo $rs_product['TX_producto_descuento'] ?>"/><?php
	}else{ ?>
		<input type="text" name="txt_discount" id="txt_discount" class="form-control" value="<?php echo $rs_product['TX_producto_descuento'] ?>" readonly="readonly"/><?php
	}
?>
</div>
<div id="container_button" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<button type="button" id="btn_acept" class="btn btn-success">Aceptar</button>
	&nbsp;
	<button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
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
