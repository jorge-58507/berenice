<?php
require 'bh_con.php';
$link=conexion();
$cambio = 0;
$numero_correlativo = 0;
if (!empty($_GET['a'])) {
	$last_ff = $_GET['a'];

	$qry_ff = mysql_query("SELECT TX_facturaf_cambio, TX_facturaf_numero FROM bh_facturaf WHERE AI_facturaf_id = '$last_ff'")or die (mysql_error());
	$rs_last_ff = mysql_fetch_assoc($qry_ff);
	if (!empty($rs_last_ff['TX_facturaf_cambio'])) { $cambio=round($rs_last_ff['TX_facturaf_cambio'],2); }else{  $cambio = 0 ;}
	$numero_correlativo = $rs_last_ff['TX_facturaf_numero'];
}

require 'attached/php/req_login_paydesk.php';
session_destroy();

?>
<?php
$qry_product=mysql_query("SELECT * FROM bh_producto ORDER BY TX_producto_value ASC LIMIT 10");
$rs_product=mysql_fetch_assoc($qry_product);

$qry_client=mysql_query("SELECT * FROM bh_cliente ORDER BY TX_cliente_nombre ASC");
$rs_client=mysql_fetch_assoc($qry_client);

$rs = mysql_query("SELECT MAX(AI_facturaventa_id) AS id FROM bh_facturaventa");
if ($row = mysql_fetch_row($rs)) {
	$last_id = trim($row[0]);
	$next_id = $last_id+'1';
}

$txt_facturaventa="SELECT bh_facturaventa.facturaventa_AI_user_id, bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.AI_facturaventa_id, bh_cliente.TX_cliente_nombre, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status, bh_facturaventa.facturaventa_AI_cliente_id, bh_user.TX_user_seudonimo
FROM ((bh_facturaventa INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
INNER JOIN bh_user ON bh_facturaventa.facturaventa_AI_user_id = bh_user.AI_user_id)
WHERE bh_facturaventa.TX_facturaventa_status != 'INACTIVA' AND
bh_facturaventa.TX_facturaventa_status != 'CANCELADA'
 ORDER BY AI_facturaventa_id DESC LIMIT 10";

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
$("#btn_start").click(function(){
	window.location="start.php";
});
$("#btn_exit").click(function(){
	location.href="index.php";
})

$(window).on('beforeunload',function(){
	close_popup();
});

$("#btn_newsale").click(function(){
	window.location="new_sale.php?a="+<?php echo $next_id; ?>;
});

$("#txt_filterpaydesk").focus();
$("#txt_filterpaydesk").keyup(function(){
	filter_paydesk();
});
$("#txt_filterpaydesk").keyup(function(e){
	if(e.which == 13){
		$("#btn_newcollect").click();
	}
	if(e.which == 120){
		$("#btn_newsale").click();
	}
});

$("#btn_cashmovement").click(function(){
	open_popup_w_scroll('popup_cashmovement.php','popup_cashmovement','625','420');
})
$("#btn_cashregister").on("click",function(){
	open_popup("popup_cashregister.php",'_blank','820','620');
});
$("#btn_facturaf").on("click",function(){
	document.location.href="admin_facturaf.php";
//	open_popup_w_scroll('popup_facturaf_paydesk.php','popup_facturaf','1000','420');
});
$("#btn_creditnote").on("click",function(){
	open_popup_w_scroll('popup_paydesk_creditnote.php','_popup','1000','420');
});

$("#btn_client").on("click", function(){
	document.location.href="admin_account_receivable.php";
})


});

function open_newcollect(id,user){
	open_popup_w_scroll('popup_newcollect.php?a='+id+'&b='+user, 'popup_newcollect','525','420');
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
<form action="" method="post" name="form_sell"  id="form_sell" onsubmit="return false;">

<div id="container_btn_sale" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <button type="button" id="btn_newsale" class="btn btn-info btn-lg" autofocus="autofocus"><strong>Nueva Venta</strong></button>
        &nbsp;&nbsp;
        <button type="button" id="btn_cashmovement" class="btn btn-warning"><strong>Caja Menuda</strong></button>
        &nbsp;&nbsp;
        <button type="button" id="btn_refresh" class="btn btn-info btn-md" onclick="document.location.reload();"><strong><i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>
<span class="sr-only"></strong></button>
				&nbsp;&nbsp;
				<button type="button" id="btn_facturaf" class="btn btn-warning"><strong>Factura F.</strong></button>
				&nbsp;&nbsp;
				<button type="button" id="btn_creditnote" class="btn btn-info"><strong>Notas de C.</strong></button>
        &nbsp;&nbsp;
				<button type="button" id="btn_client" class="btn btn-warning"><strong>Clientes</strong></button>
        &nbsp;&nbsp;
        <button type="button" id="btn_cashregister" class="btn btn-danger"><strong>Arqueo de Caja</strong></button>
</div>
<div id="container_facturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div id="container_txtfilterpaydesk" class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
		<label for="txt_filterpaydesk">Buscar</label>
        <input type="text" id="txt_filterpaydesk" class="form-control" />
    </div>
		<div  class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
		</div>
		<div id="container_spandiference" class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
			<span id="span_ff_number" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><strong>N&deg; Correlativo: </strong><?php echo $numero_correlativo; ?></span>
			<span id="span_diference"><i id="i_piggy_bank" class="glyphicon glyphicon-piggy-bank" aria-hidden="true"></i>&nbsp;Cambio:&nbsp;B/ <?php echo number_format($cambio,2); ?></span>
		</div>
    <div id="container_tblfacturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

<table id="tbl_facturaventa" class="table table-bordered table-striped">
	<thead class="bg-info">
    	<tr>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
        	<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Vendedor</th>
            <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Cliente</th>
            <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Nº Factura</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Total</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Status</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        </tr>
    </thead>
    <tfoot class="bg-info">
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
<?php
			if($nr_facturaventa=mysql_num_rows($qry_facturaventa)>0){
			do{
?>
    <tr ondblclick="open_newcollect('<?php echo $rs_facturaventa['facturaventa_AI_cliente_id']?>','<?php echo $rs_facturaventa['facturaventa_AI_user_id']?>');">
        <td><?php
		$pre_fecha = strtotime($rs_facturaventa['TX_facturaventa_fecha']);
		echo $fecha = date('d-m-Y',$pre_fecha);
		; ?></td>
    	<td><?php echo $rs_facturaventa['TX_user_seudonimo']; ?></td>
        <td><?php echo $rs_facturaventa['TX_cliente_nombre']; ?></td>
        <td><?php echo $rs_facturaventa['TX_facturaventa_numero']; ?></td>
        <td><?php echo number_format($rs_facturaventa['TX_facturaventa_total'],4); ?></td>
        <td>
<?php
		switch($rs_facturaventa['TX_facturaventa_status']){
			case "ACTIVA":	$font='#00CC00';	break;
			case "FACTURADA":	$font='#0033FF';	break;
			default:	$font='#990000';
		}
?>
        <font color="<?php echo $font ?>" style="font-weight:bold">
<?php 	echo $rs_facturaventa['TX_facturaventa_status']; 		?>
        </font>
        </td>
        <td style="text-align:center;">
        <?php if($rs_facturaventa['TX_facturaventa_status'] == "ACTIVA"){ ?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" onclick="javascript:window.location='old_sale.php?a='+this.name">Abrir</button>
        <?php }else{ ?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" onclick="javascript:window.location='new_paydesk.php?a='+this.name">Abrir</button>
        <?php } ?>
        </td>
        <td style="text-align:center;">
        <?php if($rs_facturaventa['TX_facturaventa_status'] == "ACTIVA" || $rs_facturaventa['TX_facturaventa_status'] == "FACTURADA"){ ?>
        	<button type="button" id="btn_newcollect" name="<?php echo $rs_facturaventa['facturaventa_AI_cliente_id'] ?>" class="btn btn-success" onclick="open_newcollect(this.name,'<?php echo $rs_facturaventa['facturaventa_AI_user_id']?>');">Cobrar</button>
        <?php }else{ ?>
<!--NADA PARA MOSTRAR -->
        <?php } ?>
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
