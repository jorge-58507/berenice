<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login.php';
?>
<?php
$datoventa_id=$_GET['a'];
$datoventa_cantidad=$_GET['b'];
$producto_id=$_GET['c'];
?>
<?php
$row_producto=mysql_fetch_row(mysql_query("SELECT TX_producto_value FROM bh_producto WHERE AI_producto_id = '$producto_id'"));

$row_datoventa=mysql_fetch_row(mysql_query("SELECT TX_datoventa_cantidad FROM bh_datoventa WHERE AI_datoventa_id = '$datoventa_id'"));
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
var cantidad_actual = <?php echo $row_datoventa[0];?>

$('#txt_cantidad').validCampoFranz('.0123456789');

$("#btn_save").click(function(){
var cantidad = $("#txt_cantidad").val();
	pat = new RegExp(/[0-9]/);
	res = pat.test(cantidad);
	if(!res){
		alert("El valor ingresado es erroneo");
		return false;
	}
	if(cantidad > cantidad_actual){
		alert("El valor ingresado es erroneo");
		return false;
	}
	if(cantidad < 1){
		alert("El valor ingresado es erroneo");
		return false;
	}
	plus_creditnote(cantidad);
});
$("#btn_cancel").click(function(){
	self.close()
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
	<div id="container_producto" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<label for="span_product">Producto: </label>
    	<span id="span_product" class="form-control bg-disabled"><?php echo $row_producto[0]; ?></span>
    </div>
    <div id="container_cantidad" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<label for="txt_cantidad">Cantidad</label>
    	<input type="text" id="txt_cantidad" class="form-control" />
    </div>
	<div id="container_purpose" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<label for="sel_purpose">Retorno en:</label>
        <select id="sel_purpose" class="form-control">
        <option value="">Seleccione</option>
        <option value="efectivo">Efectivo</option>
        <option value="saldo">Saldo a Favor</option>
        </select>
    </div>
    <div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<button type="button" id="btn_save" class="btn btn-success">Guardar</button>
        <button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
    </div>
</div>

<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
&copy; Derechos Reservados a: Trilli, S.A. 2017
	</div>
</div>
</div>

</body>
</html>
