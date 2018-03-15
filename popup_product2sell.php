<?php
require 'bh_conexion.php';
$link=conexion();

if(!empty($_GET['a'])){
	$product_id=$_GET['a'];
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

$qry_precio=$link->query("SELECT AI_precio_id, TX_precio_uno,TX_precio_dos, TX_precio_tres, TX_precio_cuatro, TX_precio_cinco FROM bh_precio WHERE precio_AI_producto_id = '$product_id' AND TX_precio_inactivo = '0' ORDER BY TX_precio_fecha DESC");
$nr_precio =	$qry_precio->num_rows;

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
	var input_price = document.forms[0]['input_price'].name;
	var txt_quantity = document.forms[0]['txt_quantity'].name;
	var txt_itbm = document.forms[0]['txt_itbm'].name;
	var txt_discount = document.forms[0]['txt_discount'].name;
	if (id == ""||isEmpty(input_price)||isEmpty(txt_itbm)||isEmpty(txt_discount)){
		return false;
	}
	var ans = val_intwdec($("#input_price").val());
	if(!ans){
		$("#input_price").css("border", "2px outset #F00")
		return false;
	}	$("#input_price").css("border", "2px inset #797b7e80")
	var url = window.opener.location;
	var patt = new RegExp("old_sale.php");
	ans = patt.test(url)
	var activo = window.opener.$(".tab-pane.active").attr("id");
	if (ans) {
		plus_product2sell(id);
	}else{
		var	cantidad = $("#txt_quantity").val();
		if(cantidad === ""){cantidad='1.00'}
		precio = $("#input_price").val();	descuento = $("#txt_discount").val();	itbm = $("#txt_itbm").val();
		window.opener.plus_product2nuevaventa(id,precio,descuento,itbm,activo,cantidad);
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
<div id="container_product" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
	<label for="txt_product">Producto: </label>
    <input type="text" name="txt_product" id="txt_product" alt="<?php echo $rs_product['AI_producto_id'] ?>" class="form-control" readonly="readonly" value="<?php echo $rs_product['TX_producto_value'] ?>" />
</div>
<div id="container_measure" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	<label for="txt_measure">Codigo:</label>
    <span class=" form-control bg-disabled"><?php echo $rs_product['TX_producto_codigo'] ?></span>
    <input type="hidden" name="txt_measure" id="txt_measure" class="form-control" readonly="readonly" value="<?php echo $rs_product['TX_producto_medida'] ?>" />
</div>
<div id="container_stock" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	<label for="txt_stock">Existencia:</label>
    <input type="text" name="txt_stock" id="txt_stock" class="form-control" readonly="readonly" value="<?php echo $rs_product['TX_producto_cantidad'] ?>" />
</div>
<div id="container_quantity" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
	<label for="txt_quantity">Cantidad:</label>
    <input type="text" name="txt_quantity" id="txt_quantity" class="form-control" placeholder="1" autofocus/>
</div>
<div id="container_price" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
    <label for="input_price">Precio:</label>
<?php
	$rs_precio=$qry_precio->fetch_array(MYSQLI_ASSOC);
	if($nr_precio > 0){
		if($rs_precio['TX_precio_cuatro'] === '0' || $rs_precio['TX_precio_cuatro'] === '' || $rs_precio['TX_precio_cuatro'] === '0.00'){
?>
		<input type="text" name="input_price" id="input_price" class="form-control" />
	<?php }else{ ?>
	<select id="input_price" name="input_price" class="form-control">
	<?php if($rs_precio['TX_precio_uno'] > 0){ ?>
    <option value="<?php echo $rs_precio['TX_precio_uno'] ?>"><?php echo $rs_precio['TX_precio_uno'] ?></option>
    <?php } ?>
	<?php if($rs_precio['TX_precio_dos'] > 0){ ?>
    <option value="<?php echo $rs_precio['TX_precio_dos'] ?>"><?php echo $rs_precio['TX_precio_dos'] ?></option>
    <?php } ?>
	<?php if($rs_precio['TX_precio_tres'] > 0){ ?>
    <option value="<?php echo $rs_precio['TX_precio_tres'] ?>"><?php echo $rs_precio['TX_precio_tres'] ?></option>
    <?php } ?>
	<?php if($rs_precio['TX_precio_cuatro'] > 0){ ?>
    <option value="<?php echo $rs_precio['TX_precio_cuatro'] ?>" selected="selected">Regular: <?php echo $rs_precio['TX_precio_cuatro'] ?></option>
    <?php } ?>
	<?php if($rs_precio['TX_precio_cinco'] > 0){ ?>
    <option value="<?php echo $rs_precio['TX_precio_cinco'] ?>"><?php echo $rs_precio['TX_precio_cinco'] ?></option>
    <?php } ?>
    </select>
    	<?php } ?>
    <?php
	}else{
	?>
  <input type="text" name="input_price" id="input_price" class="form-control" />
<?php } ?>
</div>
<div id="container_itbm" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
  <label for="txt_itbm">Imp.%:</label>
  <input type="text" name="txt_itbm" id="txt_itbm" class="form-control" value="<?php echo $rs_product['TX_producto_exento'] ?>" readonly="readonly"/>
</div>
<div id="container_discount" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
  <label for="txt_discount">Descuento%:</label>
<?php
	if ($_COOKIE['coo_iuser'] < 3) { ?>
		<input type="text" name="txt_discount" id="txt_discount" class="form-control" value="<?php echo $rs_product['TX_producto_descuento'] ?>"/>
<?php
	}else {
	?>
		<input type="text" name="txt_discount" id="txt_discount" class="form-control" value="<?php echo $rs_product['TX_producto_descuento'] ?>" readonly="readonly"/>
<?php
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
