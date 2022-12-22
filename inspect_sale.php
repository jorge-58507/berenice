<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login_sale.php';
?>
<?php
session_start();
session_destroy();
?>
<?php

$txt_facturaventa="SELECT bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.AI_facturaventa_id, bh_cliente.TX_cliente_nombre, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status
FROM bh_facturaventa, bh_cliente
WHERE bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id";
switch ($_COOKIE['coo_tuser']) {
case "1":
$txt_facturaventa=$txt_facturaventa." ORDER BY TX_facturaventa_fecha, TX_facturaventa_numero DESC LIMIT 10";
break;
case "2":
$txt_facturaventa=$txt_facturaventa." ORDER BY TX_facturaventa_fecha, TX_facturaventa_numero  DESC LIMIT 10";
break;
case "4":
$txt_facturaventa=$txt_facturaventa." ORDER BY TX_facturaventa_fecha, TX_facturaventa_numero  DESC LIMIT 10";
break;
default:
$txt_facturaventa=$txt_facturaventa." AND
bh_facturaventa.facturaventa_AI_user_id = '{$_COOKIE['coo_iuser']}' ORDER BY TX_facturaventa_status, TX_facturaventa_numero DESC LIMIT 10";
break;
}

$qry_facturaventa=mysql_query($txt_facturaventa);
$rs_facturaventa=mysql_fetch_assoc($qry_facturaventa);
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
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>


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


$("#btn_newsale").click(function(){
	window.location="new_sale.php?a="+<?php echo $next_id; ?>;
})





$("#btn_start").click(function(){
	window.location="start.php";
});
$("#btn_exit").click(function(){
	location.href="index.php";
})
});



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
        Bienvenido: <label class="bg-primary">
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

<div id="container_btn_sale" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<button type="button" id="btn_newsale" class="btn btn-info btn-lg"><strong>Nueva Venta</strong></button>&nbsp;
	<button type="button" id="btn_inspectsale" class="btn btn-default btn-lg"><strong>Listado de Venta</strong></button>
</div>
<div id="container_facturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div id="container_tblfacturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

<table id="tbl_facturaventa" class="table table-bordered table-striped">
	<thead class="bg-primary">
    	<tr>
      	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-5">Cliente</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-2">Nº Factura</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Total</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Status</th>
      </tr>
    </thead>
    <tfoot class="bg-primary">
    	<tr>
      	<td> </td>
      	<td> </td>
      	<td> </td>
      	<td> </td>
      	<td> </td>
			</tr>
    </tfoot>
    <tbody>
    <?php if($nr_facturaventa=mysql_num_rows($qry_facturaventa)>0){ ?>
    <?php
	do{
	?>
    <tr>
        <td><?php
		$time=strtotime($rs_facturaventa['TX_facturaventa_fecha']);
		$date=date('d-m-Y',$time);
		echo $date; ?></td>
        <td><?php echo $rs_facturaventa['TX_cliente_nombre']; ?></td>
        <td><?php echo $rs_facturaventa['TX_facturaventa_numero']; ?></td>
        <td><?php echo number_format($rs_facturaventa['TX_facturaventa_total'],2); ?></td>
        <td>
        <?php
		switch($rs_facturaventa['TX_facturaventa_status']){
			case "ACTIVA":
				$font='#00CC00';
				break;
			case "FACTURADA":
				$font='#0033FF';
				break;
			default:
				$font='#990000';
		}
		?>
        <font color="<?php echo $font ?>" style="font-weight:bold">
		<?php echo $rs_facturaventa['TX_facturaventa_status']; ?>
        </font>
        </td>
    </tr>
    <?php
	}while($rs_facturaventa=mysql_fetch_assoc($qry_facturaventa));
    ?>
    <?php }else{ ?>
    <tr>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <?php } ?>
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
    &copy; Derechos Reservados a: Jorge Salda&nacute;a <?php echo date('Y'); ?>
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
