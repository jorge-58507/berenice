<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_sale.php';

function ObtenerIP(){
	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"),"unknown"))
	$ip = getenv("HTTP_CLIENT_IP");
	else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
	$ip = getenv("HTTP_X_FORWARDED_FOR");
	else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
	$ip = getenv("REMOTE_ADDR");
	else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
	$ip = $_SERVER['REMOTE_ADDR'];
	else
	$ip = "IP desconocida";
	return($ip);
}
$ip   = ObtenerIP();
$cliente = gethostbyaddr($ip);

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
$qry_facturaventa=$link->query($txt_facturaventa)or die($link->error);
$rs_facturaventa=$qry_facturaventa->fetch_array();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>
<link href="attached/image/f_icono.ico" rel="shortcut icon" type="icon" />
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
<script type="text/javascript" src="attached/js/sell_funct.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#btn_navsale").click(function(){	window.location="sale.php";	});
	$("#btn_navstock").click(function(){	window.location="stock.php";	});
	$("#btn_navpaydesk").click(function(){	window.location="paydesk.php";	});
	$("#btn_navadmin").click(function(){	window.location="start_admin.php";	});
	$("#btn_start").click(function(){	window.location="start.php";	});
	$("#btn_exit").click(function(){	location.href="index.php";	});

	$("#btn_newsale").click(function(){	window.location.href="new_sale.php";	});
	$("#btn_inspectsale").click(function(){
		open_popup_w_scroll('popup_inspect_sale.php','inspectsale','1040','420');
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
	$(window).on('beforeunload',function(){	close_popup();	});
});

function upd_loginclient (){
	var ans = confirm("¿Desea ocultar este aviso?");
	if (!ans) { return false; }
	$.ajax({	data: {"a" : '<?php echo $_COOKIE['coo_iuser']; ?>', "b" : '<?php echo $cliente; ?>'}, type: "GET", dataType: "text", url: "attached/get/upd_loginclient.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus);
		setTimeout(function(){ window.location.href="index.php";},500);
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
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
	 	<div id="container_username" class="col-xs-4 col-sm-4 col-md-4 col-lg-4 hidden-xs">
			Bienvenido: <label class="bg-primary"><?php echo $rs_checklogin['TX_user_seudonimo']; ?></label>
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
<?php
	if (!empty($_COOKIE['coo_usercliente'])) {
		if ($cliente != $_COOKIE['coo_usercliente']) {?>
			<div id="span_useronline">
				<div ondblclick="upd_loginclient()" class="col-xs-2 col-sm-2 col-md-2 col-lg-2 no_padding"><i class="glyphicon glyphicon-exclamation-sign"></i></div>
					Estas conectado en:<br />
				<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 no_padding"><span><?php echo strtoupper($_COOKIE['coo_usercliente']); ?></span></div>
			</div>
<?php
		}
	} ?>
	<div id="container_btn_sale" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<button type="button" id="btn_newsale" class="btn btn-info btn-lg" autofocus="autofocus"><strong>Nueva Venta</strong></button>
	   &nbsp;
		<button type="button" id="btn_inspectsale" class="btn btn-default btn-lg"><strong>Listado de Venta</strong></button>
	</div>
	<div id="container_facturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="container_filterfacturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div id="container_txtfilterfacturaventa" class="col-xs-12 col-sm-8 col-md-5 col-lg-5">
        <label for="txt_filterfacturaventa" class="label label_blue_sky">Buscar</label>
      	<input type="text" id="txt_filterfacturaventa" class="form-control" />
      </div>
			<div id="container_selfilterfacturaventa" class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
      	<label for="sel_status" class="label label_blue_sky">Estado</label>
        <select id="sel_status" class="form-control">
        	<option value="">TODAS</option>
        	<option value="ACTIVA">ACTIVA</option>
        	<option value="INACTIVA">INACTIVA</option>
        	<option value="FACTURADA">FACTURADA</option>
        </select>
      </div>
      <div id="container_txtfilterfacturaventa" class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
        <label for="txt_date" class="label label_blue_sky">Fecha</label>
        <input type="text" id="txt_date" class="form-control" readonly="readonly" />
      </div>
			<div id="container_txtfilterfacturaventa" class="col-xs-2 col-sm-1 col-md-1 col-lg-1 side-btn-md-label">
				<button type="button" id="" class="btn btn-danger btn-xs clear_date" onclick="setEmpty('txt_date')"><span class="glyphicon glyphicon-exclamation-sign"></span></button>
			</div>
    </div>
    <div id="container_tblfacturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

		<table id="tbl_facturaventa" class="table table-bordered table-striped">
			<caption class="caption">Cotizaciones Guardadas</caption>
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
	        <td colspan="8"></td>
				</tr>
    	</tfoot>
    	<tbody>
<?php 	if($qry_facturaventa->num_rows > 0){
					do{ ?>
    				<tr>
        			<td><?php	echo $date=date('d-m-Y',strtotime($rs_facturaventa['TX_facturaventa_fecha']));	?></td>
			        <td><?php echo $rs_facturaventa['TX_cliente_nombre']; ?></td>
			        <td><?php echo $rs_facturaventa['TX_facturaventa_numero']; ?></td>
			        <td><?php echo number_format($rs_facturaventa['TX_facturaventa_total'],2); ?></td>
<?php						switch($rs_facturaventa['TX_facturaventa_status']){
									case "ACTIVA":	$font='#00CC00';	break;
									case "FACTURADA":	$font='#0033FF';	break;
									case "INACTIVA":	$font='#337ab7';	break;
									default:	$font='#990000';
								}	?>
							<td style="font-weight:bold; color:<?php echo $font ?>;"><?php	echo $rs_facturaventa['TX_facturaventa_status']; ?></td>
							<td><?php echo $rs_facturaventa['TX_user_seudonimo']; ?></td>
        			<td><?php
								if($rs_facturaventa['TX_facturaventa_status'] == "ACTIVA"){ ?>
				        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" onclick="jacascript:window.location='old_sale.php?a='+this.name">Modificar</button>
<?php 					}else if($rs_facturaventa['TX_facturaventa_status'] == "FACTURADA" && $_COOKIE['coo_iuser'] > '2'){ ?>
				        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" disabled="disabled">Modificar</button>
<?php 					}else if($rs_facturaventa['TX_facturaventa_status'] == "INACTIVA" && $_COOKIE['coo_iuser'] > '2'){ ?>
				        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" disabled="disabled">Modificar</button>
<?php 					}else{ ?>
				        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" onclick="javascript:window.location='old_sale.php?a='+this.name">Modificar</button>
<?php 					} ?>
			        </td>
			        <td><button type="button" id="btn_print" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-info btn-md-fa" onclick="print_html('print_sale_html.php?a='+this.name+'')"><i class="fa fa-print"></i></button></td>
    				</tr>
<?php
						}while($rs_facturaventa=$qry_facturaventa->fetch_array());
 					}else{ ?>
				    <tr>
			        <td colspan="8"> </td>
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
