<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login.php';
?>
<?php
$datoventa_id=$_GET['a'];
?>
<?php
$txt_datoventa="SELECT bh_datoventa.AI_datoventa_id, bh_producto.TX_producto_value, bh_producto.AI_producto_id, bh_producto.TX_producto_medida, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento, bh_medida.TX_medida_value
FROM ((bh_datoventa
INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
INNER JOIN bh_medida ON bh_producto.TX_producto_medida = bh_medida.TX_medida_value)
WHERE AI_datoventa_id = '$datoventa_id'";
$qry_datoventa=mysql_query($txt_datoventa,$link);
$rs_datoventa=mysql_fetch_assoc($qry_datoventa);

$qry_product=mysql_query("SELECT * FROM bh_producto ORDER BY TX_producto_value ASC");
$rs_product=mysql_fetch_assoc($qry_product);
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
$("#container_selproductlist").css("display","none");
$("#txt_producto").focus(function(){
	$("#container_selproductlist").show(250);
});
$("#txt_producto").blur(function(){
	$("#container_selproductlist").hide(250);
});

$("#btn_save").click(function(){
	if($('#txt_cantidad').val() == "" || $('#txt_descuento').val() == "" || $('#txt_impuesto').val() == "" || $('#txt_precio').val() == ""){
		return false;
	}
	save_datoventa(<?php echo $rs_datoventa['AI_datoventa_id'];?>);
});
$("#btn_cancel").click(function(){
	self.close();
});

$('#txt_cantidad,#txt_precio').validCampoFranz('.0123456789');
$('#txt_descuento,#txt_impuesto').validCampoFranz('.1234567890');
});
function set_txtproducto(field){
	$("#txt_producto").val(field.text);
	$("#txt_producto").prop("alt",field.value);
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
<form method="post" name="form_editdatoventa" action="">

<div id="container_input" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div id="container_txtproducto" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<label for="txt_producto">Producto</label>
    	<input type="text" id="txt_producto" class="form-control" alt="<?php echo $rs_datoventa['AI_producto_id']; ?>" value="<?php echo $rs_datoventa['TX_producto_value']; ?>" onkeyup="filter_product_editdatoventa(this); setUpperCase(this);" />
    </div>
    <div id="container_selproductlist" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <select id="sel_productlist" name="sel_productlist" class="form-control" size="3">
	<?php	do{ ?>
			<option value="<?php echo $rs_product['AI_producto_id'] ?>" onclick="set_txtproducto(this);"><?php echo $rs_product['TX_producto_value'] ?></option>
        <?php }while($rs_product=mysql_fetch_assoc($qry_product)); ?>
        </select>
    </div>
    <div id="container_txtcantidad" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<label for="txt_cantidad">Medida</label>
    	<input type="text" id="txt_unidad" readonly="readonly" class="form-control" value="<?php echo $rs_datoventa['TX_medida_value']; ?>" />
    </div>
    <div id="container_txtcantidad" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
    	<label for="txt_cantidad">Cantidad</label>
    	<input type="text" id="txt_cantidad" class="form-control" value="<?php echo $rs_datoventa['TX_datoventa_cantidad']; ?>" />
    </div>
    <div id="container_txtprecio" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
    	<label for="txt_precio">Precio</label>
    	<input type="text" id="txt_precio" class="form-control" value="<?php echo $rs_datoventa['TX_datoventa_precio']; ?>" />
    </div>
    <div id="container_txtimpuesto" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
    	<label for="txt_impuesto">Impuesto</label>
    	<input type="text" id="txt_impuesto" class="form-control" value="<?php echo $rs_datoventa['TX_datoventa_impuesto']; ?>" />
    </div>
    <div id="container_txtdescuento" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
    	<label for="txt_descuento">Descuento</label>
    	<input type="text" id="txt_descuento" class="form-control" value="<?php echo $rs_datoventa['TX_datoventa_descuento']; ?>" />
    </div>
</div>
<div id="container_btn" class="col-xs-12 col-sm-12 col-md-2col-lg-12">
	<button type="button" id="btn_save" class="btn btn-success">Guardar</button>
  <button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
</div>
</form>
</div>


<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
&copy; Derechos Reservados a: Jorge Salda&nacute;a <?php echo date('Y'); ?>
	</div>
</div>
</div>

</body>
</html>
