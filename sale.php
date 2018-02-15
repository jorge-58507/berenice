<?php
require 'bh_con.php';
$link=conexion();
date_default_timezone_set('America/Panama');

require 'attached/php/req_login_sale.php';


$txt_facturaventa="SELECT bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.AI_facturaventa_id, bh_cliente.TX_cliente_nombre, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status, bh_user.TX_user_seudonimo
FROM ((bh_facturaventa
INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaventa.facturaventa_AI_user_id)
WHERE ";
switch ($_COOKIE['coo_tuser']) {
case "1":
$txt_facturaventa=$txt_facturaventa." 1 ORDER BY AI_facturaventa_id DESC LIMIT 10";
break;
case "2":
$txt_facturaventa=$txt_facturaventa." TX_facturaventa_status != 'CANCELADA' ORDER BY AI_facturaventa_id DESC LIMIT 10";
break;
case "4":
$txt_facturaventa=$txt_facturaventa." TX_facturaventa_status != 'CANCELADA' ORDER BY AI_facturaventa_id DESC LIMIT 10";
break;
default:
$txt_facturaventa=$txt_facturaventa." TX_facturaventa_status != 'CANCELADA' AND TX_facturaventa_status != 'INACTIVA' ORDER BY AI_facturaventa_id DESC LIMIT 10";
break;
}
$qry_facturaventa=mysql_query($txt_facturaventa)or die(mysql_error());
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
<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
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
$("#btn_start").click(function(){
	window.location="start.php";
});
$("#btn_exit").click(function(){
	location.href="index.php";
})


$("#btn_newsale").click(function(){
	window.location="new_sale.php";
})

$("#btn_inspectsale").click(function(){
	open_popup_w_scroll('popup_inspect_sale.php','inspectsale','840','420');
});

$("#txt_filterfacturaventa").on("keyup",function(){
	filter_sale(this.value);
});
$("#sel_status").on("change",function(){
	$("#txt_filterfacturaventa").keyup();
});
$("#txt_date").on("change",function(){
	$("#txt_filterfacturaventa").keyup();
});
$( function() {
	$("#txt_date").datepicker({
		changeMonth: true,
		changeYear: true
	});
});
$(window).on('beforeunload',function(){
	close_popup();
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
	<button type="button" id="btn_newsale" class="btn btn-info btn-lg" autofocus="autofocus"><strong>Nueva Venta</strong></button>
    &nbsp;
	<button type="button" id="btn_inspectsale" class="btn btn-default btn-lg"><strong>Listado de Venta</strong></button>
</div>
<div id="container_facturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_filterfacturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div id="container_txtfilterfacturaventa" class="col-xs-12 col-sm-8 col-md-5 col-lg-5">
            <label for="txt_filterfacturaventa">Buscar</label>
            <input type="text" id="txt_filterfacturaventa" class="form-control" />
        </div>
		<div id="container_selfilterfacturaventa" class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
            <label for="sel_status">Status</label>
            <select id="sel_status" class="form-control">
            	<option value="">TODAS</option>
            	<option value="ACTIVA">ACTIVA</option>
            	<option value="INACTIVA">INACTIVA</option>
            	<option value="FACTURADA">FACTURADA</option>
            </select>
        </div>
        <div id="container_txtfilterfacturaventa" class="col-xs-6 col-sm-4 col-md-3 col-lg-3">
            <label for="txt_date">Fecha
            <button type="button" id="clear_date_initial" class="btn btn-danger btn-xs" onclick="setEmpty('txt_date')"><strong>!</strong></button>
            </label>
            <input type="text" id="txt_date" class="form-control" readonly="readonly" />
        </div>

    </div>
    <div id="container_tblfacturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

<table id="tbl_facturaventa" class="table table-bordered table-striped">
	<thead class="bg-primary">
    	<tr>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
            <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Cliente</th>
            <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Nº Factura</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Total</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Status</th>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Vendedor</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        </tr>
    </thead>
    <tfoot class="bg-primary">
    	<tr>
        	<td> </td>
        	<td> </td>
        	<td> </td>
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
		<td>
        <?php echo $rs_facturaventa['TX_user_seudonimo']; ?>
        </td>
        <td>
        <?php if($rs_facturaventa['TX_facturaventa_status'] == "ACTIVA"){ ?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" onclick="jacascript:window.location='old_sale.php?a='+this.name">Modificar</button>
        <?php }else if($rs_facturaventa['TX_facturaventa_status'] == "FACTURADA" && $_COOKIE['coo_iuser'] > '2'){ ?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" disabled="disabled">Modificar</button>
        <?php }else if($rs_facturaventa['TX_facturaventa_status'] == "INACTIVA" && $_COOKIE['coo_iuser'] > '2'){ ?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" disabled="disabled">Modificar</button>
        <?php }else{ ?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" onclick="javascript:window.location='old_sale.php?a='+this.name">Modificar</button>
        <?php } ?>
        </td>
        <td>
        <button type="button" id="btn_print" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-info" onclick="print_html('print_sale_html.php?a='+this.name+'')">Imprimir</button>
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
