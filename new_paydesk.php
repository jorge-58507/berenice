<?php
require 'bh_con.php';
$link=conexion();
date_default_timezone_set('America/Panama');
?>
<?php
require 'attached/php/req_login_paydesk.php';
?>
<?php
$facturaventa_id=$_GET['a'];

$qry_product=mysql_query("SELECT * FROM bh_producto ORDER BY TX_producto_value ASC LIMIT 10");
$rs_product=mysql_fetch_assoc($qry_product);

$qry_client=mysql_query("SELECT * FROM bh_cliente ORDER BY TX_cliente_nombre ASC");
$rs_client=mysql_fetch_assoc($qry_client);

$qry_venta=mysql_query("SELECT bh_facturaventa.AI_facturaventa_id, bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.facturaventa_AI_cliente_id, bh_facturaventa.facturaventa_AI_user_id, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status, bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_direccion, bh_cliente.TX_cliente_telefono, bh_datoventa.datoventa_AI_producto_id, bh_producto.TX_producto_value, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento, bh_datoventa.datoventa_AI_user_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_medida, bh_user.TX_user_seudonimo
FROM ((((bh_facturaventa
       INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
       INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
       INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
       INNER JOIN bh_user ON bh_facturaventa.facturaventa_AI_user_id = bh_user.AI_user_id)
WHERE AI_facturaventa_id = '$facturaventa_id'");
$nr_venta=mysql_num_rows($qry_venta);
$rs_venta=mysql_fetch_assoc($qry_venta);


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
<link href="attached/css/sell_css.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/sell_funct.js"></script>


<script type="text/javascript">

$(document).ready(function() {

$("#btn_navsale").click(function(){
	window.location="sale.php";
});
$("#btn_navstock").click(function(){
	window.location="stock.php";
});
$("#btn_navpaydesk").click(function(){
	window.location="paydesk.php";
})
$("#btn_navadmin").click(function(){
	window.location="start_admin.php";
});

$(window).on('beforeunload', function(){

});

$("#btn_sale").click(function(){
	window.location="sale.php";
});
$("#btn_stock").click(function(){
	window.location="stock.php";
});


$("#btn_imprimir").click(function(){
	window.open("print_sale_html.php?a=<?php echo $_GET['a']; ?>", '_blank');
});
$("#btn_salir").click(function(){
	window.location='paydesk.php';
});




$("#btn_start").click(function(){
	window.location="start.php";
});
$("#btn_exit").click(function(){
	location.href="index.php";
})
});
function new_creditnote(str1,str2){
	alert(str1+" = "+str2);
}

</script>

</head>

<body>

<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-2" >
  	<div id="logo" ></div>
   	</div>

	<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-10">
    	<div id="container_username" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        Bienvenido:<label class="bg-primary">
         <?php echo $rs_checklogin['TX_user_seudonimo']; ?>
        </label>
        </div>
		<div id="navigation" class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
<?php
switch ($_COOKIE['coo_tuser']){
	case '1':
		include 'attached/php/nav_master.php';
	break;
	case '2':
		include 'attached/php/nav_admin.php';
	break;
	case '3':
		include 'attached/php/nav_sale.php';
	break;
	case '4':
		include 'attached/php/nav_paydesk.php';
	break;
	case '5':
		include 'attached/php/nav_stock.php';
	break;
}
?>
		</div>
	</div>

</div>

<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<form action="sale.php" method="post" name="form_sell"  id="form_sell">

<div id="container_complementary" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_txtdate" class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
    	<label for="txt_date">Fecha:</label>
	    <input type="text" class="form-control" alt="" id="txt_date" name="txt_date" readonly="readonly"
        value="<?php echo $rs_venta['TX_facturaventa_fecha']; ?>" />
    </div>
	<div id="container_txtnumero" class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
    	<label for="txt_numero">Numero:</label>
	    <input type="text" class="form-control" alt="" id="txt_numero" name="txt_numero" readonly="readonly"
        value="<?php echo $rs_venta['TX_facturaventa_numero']; ?>" />
    </div>
	<div id="container_txtvendedor" class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
    	<label for="txt_vendedor">Vendedor:</label>
	    <input type="text" class="form-control" alt="<?php echo $rs_vendor['AI_user_id']; ?>" id="txt_vendedor" name="txt_vendedor" readonly="readonly"
        value="<?php echo $rs_venta['TX_user_seudonimo']; ?>" />
    </div>
</div>
<div id="container_client" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_txtfilterclient" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<label for="txt_filterclient">Cliente:</label>
	    <input type="text" class="form-control" alt="<?php echo $rs_venta['facturaventa_AI_cliente_id']; ?>" id="txt_filterclient" name="txt_filterclient" value="<?php echo $rs_venta['TX_cliente_nombre']; ?>" readonly="readonly" />
    </div>
</div>

<div id="container_product2sell" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_tblproduct2sale" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <table id="tbl_product2sell" class="table table-bordered table-hover ">
    <caption>Lista de Productos para la Venta</caption>
	<thead class="bg-danger">
        <tr>
            <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Codigo</th>
            <th class="col-xs-2 col-sm-2 col-md-1 col-lg-5">Producto</th>
            <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Medida</th>
            <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Cantidad</th>
            <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Precio</th>
            <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">ITBM%</th>
            <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">Desc%</th>
            <th class="col-xs-2 col-sm-2 col-md-1 col-lg-1">SubTotal</th>
        </tr>
    </thead>
    <tbody>
<?php
$total_itbm = 0;
$total_descuento = 0;
$total = 0;
?>
<?php
if($nr_venta > 0){
?>
<?php do{ ?>

		<tr>
            <td><?php echo $rs_venta['TX_producto_codigo']; ?></td>
            <td><?php echo $rs_venta['TX_producto_value']; ?></td>
            <td><?php echo $rs_venta['TX_producto_medida']; ?></td>
            <td>
			<?php echo $rs_venta['TX_datoventa_cantidad']; ?>
            <span id="stock_quantity"><?php echo $rs_venta['TX_producto_cantidad']; ?></span>
            </td>
            <td><?php echo $rs_venta['TX_datoventa_precio']; ?></td>

            <td><?php echo $rs_venta['TX_datoventa_impuesto']."% = ".
	number_format($subtotal_impuesto = $rs_venta['TX_datoventa_cantidad']*($rs_venta['TX_datoventa_precio']*($rs_venta['TX_datoventa_impuesto']/100)),2); ?></td>

            <td><?php echo $rs_venta['TX_datoventa_descuento']."% = ".
	number_format($subtotal_descuento = $rs_venta['TX_datoventa_cantidad']*($rs_venta['TX_datoventa_precio']*($rs_venta['TX_datoventa_descuento']/100)),2); ?></td>

            <td>
            <?php
			echo
	number_format($subtotal=($rs_venta['TX_datoventa_precio']*$rs_venta['TX_datoventa_cantidad'])-
			$subtotal_descuento+
			$subtotal_impuesto,2);
			?>
            </td>
		</tr>
<?php
		$total_itbm += $subtotal_impuesto;
		$total_descuento += $subtotal_descuento;
		$total += $subtotal;
?>
<?php }while($rs_venta=mysql_fetch_assoc($qry_venta)); ?>
<?php }else{ ?>
		<tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
		</tr>
<?php } ?>
    </tbody>
    <tfoot class="bg-danger">
		<tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>
            <strong>ITBM: </strong> <br /><span id="span_itbm"><?php echo number_format($total_itbm,2); ?></span>
            </td>
            <td>
            <strong>Desc: </strong> <br /><span id="span_discount"><?php echo number_format($total_descuento,2); ?></span>
            </td>
            <td>
            <strong>Total: </strong> <br /><span id="span_total"><?php echo number_format($total,2); ?></span>
            </td>
		</tr>
    </tfoot>
    </table>
    </div>
</div>
<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <button type="button" id="btn_imprimir" class="btn btn-info">Imprimir</button>
    &nbsp;&nbsp;&nbsp;
    <button type="button" id="btn_salir" class="btn btn-warning">Volver</button>
</div>

<div id="container_product_list" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="display:none">
	<div id="container_filterproduct" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<label for="txt_filterproduct">Buscar:</label>
    <input type="text" class="form-control" id="txt_filterproduct" name="txt_filterproduct" onkeyup="filter_product_sell(this);" />
	</div>
	<div id="container_selproduct" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <table id="tbl_product" class="table table-bordered table-hover table-striped">
    <caption>Lista de Productos:</caption>
    <thead>
    	<tr>
        	<th class="bg-info col-xs-2 col-sm-2 col-md-1 col-lg-1">
            	Codigo
            </th>
            <th class="bg-info col-xs-8 col-sm-8 col-md-10 col-lg-10">
            	Nombre
            </th>
        	<th class="bg-info col-xs-2 col-sm-2 col-md-1 col-lg-1">
            	Cantidad
            </th>
        </tr>
    </thead>
    <tfoot>
	    <tr>
    		<td class="bg-info">  </td>
    		<td class="bg-info">  </td>
    		<td class="bg-info">  </td>
    	</tr>
    </tfoot>
    <tbody>
    <?php do{ ?>
    	<tr ondblclick="javascript:open_product2addpaydesk(<?php echo $rs_product['AI_producto_id']; ?>, <?php echo $facturaventa_id ?>);">
        	<td>
            <?php echo $rs_product['TX_producto_codigo']; ?>
            </td>
        	<td>
            <?php echo $rs_product['TX_producto_value']; ?>
            </td>
        	<td>
            <?php echo $rs_product['TX_producto_cantidad']; ?>
            </td>
        </tr>
    <?php }while($rs_product=mysql_fetch_assoc($qry_product)); ?>
    </tbody>

    </table>
	</div>
</div>

</form>
</div>


<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
        <div id="container_btnadminicon" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
        </div>
        <div id="container_txtcopyright" class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
    &copy; Derechos Reservados a: Trilli, S.A. 2017
        </div>
        <div id="container_btnstart" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                    		<i id="btn_start" class="fa fa-home" title="Ir al Inicio"></i>
        </div>
        <div id="container_btnexit" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <button type="button" class="btn btn-danger" id="btn_exit">Salir</button></div>
        </div>
	</div>
</div>
</div>

</body>
</html>
