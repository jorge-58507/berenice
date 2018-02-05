<?php
require 'bh_con.php';
$link=conexion();
?>
<?php

$qry_proveedor=mysql_query("SELECT * FROM bh_proveedor");
$rs_proveedor=mysql_fetch_assoc($qry_proveedor);

$qry_warehouse=mysql_query("SELECT * FROM bh_almacen");
$rs_warehouse=mysql_fetch_assoc($qry_warehouse);

$qry_product=mysql_query("SELECT * FROM bh_producto");
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
<script type="text/javascript" src="attached/js/newentry_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>

<script type="text/javascript">

$(document).ready(function() {

	$('.dropdown-toggle').dropdown();
	
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
<div id="container_date" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<label for="txt_date">Fecha:</label>
    <input type="text" name="txt_date" id="txt_date" value="<?php echo date('d/m/Y'); ?>" class="form-control" />
</div>
<div id="container_provider" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<label for="sel_provider_purchase">Proveedor:</label>
	<select id="sel_provider_purchase" name="sel_provider_purchase" class="form-control">
    <?php
	do{
	?>
    <option value="<?php echo $rs_proveedor['AI_proveedor_id'] ?>"><?php echo $rs_proveedor['TX_proveedor_nombre'] ?></option>
    <?php
	}while($rs_proveedor=mysql_fetch_assoc($qry_proveedor));
	?>
    </select>
</div>
<div id="container_billnumber" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
    <label for="txt_bill">Factura N°:</label>
    <input type="text" name="txt_billnumber" id="txt_billnumber" class="form-control" />
</div>
<div id="container_paymethod" class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
    <label for="sel_paymethod">Forma de Pago:</label>
    <select name="sel_paymethod" id="sel_paymethod" class="form-control">
    	<option value="Cheque">Cheque</option>
    	<option value="Efectivo">Efectivo</option>
    	<option value="Tarjeta de Cr&eacute;dito">Tarjeta de Cr&eacute;dito</option>
    	<option value="Tarjeta de D&eacute;bito">Tarjeta de D&eacute;bito</option>
    </select>
</div>
<div id="container_warehouse" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
    <label for="sel_warehouse">Almacen:</label>
    <select name="sel_warehouse" id="sel_warehouse" class="form-control">
    <?php
	do{
	?>
    <option value="<?php echo $rs_warehouse['AI_almacen_id'] ?>"><?php echo $rs_warehouse['TX_almacen_value'] ?></option>
    <?php
	}while($rs_warehouse=mysql_fetch_assoc($qry_warehouse));
	?>
    </select>
</div>
<div id="container_product" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
    <label for="sel_product">Almacen:</label>
    <select name="sel_product" id="sel_product" class="form-control" onchange="add_newentry(this);">
    <?php
	do{
	?>
    <option value="<?php echo $rs_product['AI_producto_id'] ?>"><?php echo $rs_product['TX_producto_value'] ?></option>
    <?php
	}while($rs_product=mysql_fetch_assoc($qry_product));
	?>
    </select>
</div>
<div id="container_tblnewentry" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<table id="tbl_newentry" cellpadding="0" cellspacing="0" border="2" width="100%">
	<thead>
    <tr>
    	<th>Codigo</th><th>Producto</th><th>Medida</th><th>Uds</th><th>precio</th><th>ITBM%</th><th>Desc%</th><th>SubTotal</th>
    </tr>
    </thead>
    <tfoot></tfoot>
</table>
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
