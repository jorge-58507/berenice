<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
$product_id=$_GET['a'];
$facturaventa_id=$_GET['b'];

$qry_product=mysql_query("SELECT * FROM bh_producto WHERE AI_producto_id = '$product_id'");
$rs_product=mysql_fetch_assoc($qry_product);

$qry_precio=mysql_query("SELECT * FROM bh_precio WHERE precio_AI_producto_id = '$product_id' AND TX_precio_inactivo = '0' ORDER BY TX_precio_fecha DESC");
$nr_precio=mysql_num_rows($qry_precio);

$qry_itbm=mysql_query("SELECT TX_opcion_value FROM bh_opcion WHERE TX_opcion_titulo = 'itbm'");
$row_itbm=mysql_fetch_row($qry_itbm);
$itbm = $row_itbm[0];
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

setFocus("txt_quantity");

$("#txt_quantity").blur(function(){
	var quantity = $("#txt_quantity").val();
	var stock = $("#txt_stock").val();
		
		quantity = parseFloat(quantity);
		stock = parseFloat(stock);
		
	if(quantity > stock){		
		alert("ALERTA: Las existencias son inferiores");
	}
});


$('#btn_acept').click(function(){
	var id = $("#txt_product").attr("alt");
	var input_price = document.forms[0]['input_price'].name;
	var txt_quantity = document.forms[0]['txt_quantity'].name;
	var txt_itbm = document.forms[0]['txt_itbm'].name;
	var txt_discount = document.forms[0]['txt_discount'].name;
	if (id == ""||isEmpty(input_price)||isEmpty(txt_quantity)||isEmpty(txt_itbm)||isEmpty(txt_discount)){
		alert("Faltan datos para continuar.");
		return false;
	}
//	alert("si acept");
	plus_product2addpaydesk(id,<?php echo $facturaventa_id;?>);
})

$('#btn_cancel').click(function(){
	self.close();
})


$('#txt_quantity').validCampoFranz('.0123456789');
$('#input_price').validCampoFranz('.0123456789');
$('#txt_itbm').validCampoFranz('.0123456789');
$('#txt_discount').validCampoFranz('.0123456789');

$("#txt_quantity, #input_price, #txt_itbm, #txt_discount").on("blur",function(){
	this.value = val_intw2dec(this.value);
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
<div id="container_measure" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	<label for="txt_measure">Medida:</label>
    <input type="text" name="txt_measure" id="txt_measure" class="form-control" readonly="readonly" value="<?php echo $rs_product['TX_producto_medida'] ?>" />
</div>
<div id="container_stock" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	<label for="txt_stock">Existencia:</label>
    <input type="text" name="txt_stock" id="txt_stock" class="form-control" readonly="readonly" value="<?php echo $rs_product['TX_producto_cantidad'] ?>" />
</div>
<div id="container_quantity" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
	<label for="txt_quantity">Cantidad:</label>
    <input type="text" name="txt_quantity" id="txt_quantity" class="form-control" onkeyup="chk_quantity(this)" value="1" />
</div>
<div id="container_price" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
    <label for="input_price">Precio:</label>
    <?php
	if($nr_precio > 0){
		$rs_precio=mysql_fetch_assoc($qry_precio);
		if($rs_precio['TX_precio_cuatro'] == '0'){
		?>
		<input type="text" name="input_price" id="input_price" class="form-control" />
		<?php }else{ ?>
    <select id="input_price" name="input_price" class="form-control">
    <option value="<?php echo $rs_precio['TX_precio_uno'] ?>"><?php echo $rs_precio['TX_precio_uno'] ?></option>
    <option value="<?php echo $rs_precio['TX_precio_dos'] ?>"><?php echo $rs_precio['TX_precio_dos'] ?></option>
    <option value="<?php echo $rs_precio['TX_precio_tres'] ?>"><?php echo $rs_precio['TX_precio_tres'] ?></option>
    <option value="<?php echo $rs_precio['TX_precio_cuatro'] ?>" selected="selected">Regular: <?php echo $rs_precio['TX_precio_cuatro'] ?></option>
    <option value="<?php echo $rs_precio['TX_precio_cinco'] ?>"><?php echo $rs_precio['TX_precio_cinco'] ?></option>
    </select>
    <?php
		}
	}else{
	?>
    <input type="text" name="input_price" id="input_price" class="form-control" />
    <?php } ?>
</div>
<div id="container_itbm" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
    <label for="txt_itbm">ITBM%:</label>
    <input type="text" name="txt_itbm" id="txt_itbm" class="form-control" value="<?php echo $rs_product['TX_producto_exento'] ?>" onkeyup="chk_itbm(this)" readonly="readonly"/>
</div>
<div id="container_discount" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
    <label for="txt_discount">Descuento%:</label>
    <input type="text" name="txt_discount" id="txt_discount" class="form-control" value="0" readonly="readonly" />
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
