<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_sale.php';
session_destroy();

$fecha_actual = date('d-m-Y');
$month_year = date('Y-m',strtotime($fecha_actual));
$fecha_inicial = date('d-m-Y',strtotime($month_year));

$qry_facturacompra=$link->query("SELECT bh_facturacompra.AI_facturacompra_id, bh_facturacompra.TX_facturacompra_fecha, bh_facturacompra.TX_facturacompra_elaboracion, bh_facturacompra.TX_facturacompra_numero, bh_proveedor.TX_proveedor_nombre
FROM (bh_facturacompra INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_facturacompra.facturacompra_AI_proveedor_id) WHERE bh_facturacompra.TX_facturacompra_preguardado = 1 ORDER BY TX_facturacompra_fecha DESC, AI_facturacompra_id DESC")or die($link->error);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>

<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap-theme.css" rel="stylesheet" type="text/css" />
<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_layout.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_blocks.css" rel="stylesheet" type="text/css" />
<link href="attached/css/newpurchase_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />


<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/newpurchase_funct.js"></script>
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
$(window).on('beforeunload', function(){
	cerrarPopup();
});

$("#btn_qry_entry").click(function(){
	window.location='filter_byproduct.php';
});


$("#btn_filtercompra").on("click",function(){
	$("#txt_filterfacturacompra").keyup();
});

$("#txt_filterfacturacompra").on("keyup",function(){
	$.ajax({	data: { "a" : this.value, "b" : $("input[name=r_limit]:checked").val(), "c" : $("#txt_date_initial").val(), "d" : $("#txt_date_final").val() },	type: "GET",	dataType: "text",	url: "attached/get/filter_prefacturacompra.php", })
	 .done(function( data, textStatus, jqXHR ) {  console.log("GOOD" + textStatus);
		if(data){
			$("#tbl_purchase tbody").html(data);
		}	 })
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
})

$( function() {
	var dateFormat = "dd-mm-yy",
		from = $( "#txt_date_initial" )
			.datepicker({
				defaultDate: "+1w",
				changeMonth: true,
				numberOfMonths: 2
			})
			.on( "change", function() {
				to.datepicker( "option", "minDate", getDate( this ) );
			}),
		to = $( "#txt_date_final" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 2
		})
		.on( "change", function() {
			from.datepicker( "option", "maxDate", getDate( this ) );
		});

	function getDate( element ) {
		var date;
		try {
			date = $.datepicker.parseDate( dateFormat, element.value );
		} catch( error ) {
			date = null;
		}

		return date;
	}
});


});

var mod_facturacompra = function (facturacompra_id){
	$.ajax({	data: { "a" : facturacompra_id },	type: "GET",	dataType: "JSON",	url: "attached/get/get_datocompra.php", })
	 .done(function( data, textStatus, jqXHR ) {  console.log("GOOD" + textStatus);
		if(data['resultado'] === 'acepted'){
			window.location.href="new_purchase.php?b="+JSON.stringify(data, true);
		}else{	console.log("Denegada "+data);	}	 })
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}

var del_facturacompra = function (facturacompra_id){
	$.ajax({	data: { "a" : facturacompra_id },	type: "GET",	dataType: "text",	url: "attached/get/del_prefacturacompra.php", })
	 .done(function( data, textStatus, jqXHR ) {  console.log("GOOD" + textStatus);
		if(data != 'denied'){
			$("#tbl_purchase tbody").html(data);
		}	 })
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
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
<form method="post" name="form_purchase"  id="form_purchase" onsubmit="return false;">
	<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<button type="button" id="btn_back" class="btn btn-default btn-lg" onclick="window.location.href='new_purchase.php';">Nueva compra</button>
		&nbsp;
		<button type="button" name="btn_qry_entry" id="btn_qry_entry" class="btn btn-primary btn-lg" >Buscar Compra</button>
	</div>

	<div id="container_product" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	  <div id="container_filterpurchase" class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
		  <label class="label label_blue_sky" for="txt_filterpurchase">Buscar:</label>
		  <input type="text" class="form-control" id="txt_filterfacturacompra" name="txt_filterfacturacompra" placeholder="Buscar Proveedor, Orden de Compra o Numero Factura"/>
	  </div>
	  <div id="container_show_filterpurchase" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	    <label class="label label_blue_sky" for="rd_filterpurchase">Mostrar:</label><br />
			<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="10" checked="checked"/> 10</label>
			<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="50" /> 50</label>
			<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="" /> Todas</label>
		</div>
		<div id="container_date" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
			<div id="container_txtdateinitial" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<label class="label label_blue_sky" for="txt_date_initial">F. Inicio</label>
				<input type="text" id="txt_date_initial" class="form-control" readonly="readonly" value="<?php echo $fecha_inicial; ?>" />
			</div>
			<div id="container_txtdatefinal" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<label class="label label_blue_sky" for="txt_date_final">F. Final</label>
				<input type="text" id="txt_date_final" class="form-control" readonly="readonly" value="<?php echo $fecha_actual; ?>" />
			</div>
		</div>
		<div id="container_btnfiltercompra" class="col-xs-1 col-sm-1 col-md-1 col-lg-1 side-btn-md-label">
			<button type="button" id="btn_filtercompra" class="btn btn-success btn-search"><i class="fa fa-search" aria-hidden="true"></i></button>
		</div>
		<div id="container_tblpurchase" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<table id="tbl_purchase" class="table table-bordered table-condensed table-hover">
				<caption class="caption">Facturas no procesadas</caption>
				<thead class="bg-primary">
					<tr>
						<th class="col-xs-1 col-md-1 col-lg-1 col-xl-1 al_center">Elaboracion</th>
						<th class="col-xs-1 col-md-1 col-lg-1 col-xl-1 al_center">Fecha</th>
						<th class="col-xs-2 col-md-2 col-lg-2 col-xl-2 al_center">N&deg; de Factura</th>
						<th class="col-xs-6 col-md-6 col-lg-6 col-xl-6 al_center">Proveedor</th>
						<th class="col-xs-1 col-md-1 col-lg-1 col-xl-1 al_center"></th>
					</tr>
				</thead>
				<tfoot class="bg-primary"><tr><td colspan="5"></td></tr></tfoot>
				<tbody>
<?php  		if ($qry_facturacompra->num_rows > 0) {
						while ($rs_facturacompra=$qry_facturacompra->fetch_array()) {	?>
							<tr>
								<td><?php echo $rs_facturacompra['TX_facturacompra_elaboracion']; ?></td>
								<td><?php echo $rs_facturacompra['TX_facturacompra_fecha']; ?></td>
								<td><?php echo $rs_facturacompra['TX_facturacompra_numero']; ?></td>
								<td><?php echo $rs_facturacompra['TX_proveedor_nombre']; ?></td>
								<td class="al_center">
									<button class="btn btn-warning btn-sm" id="btn_modificar" onclick="mod_facturacompra(<?php echo $rs_facturacompra['AI_facturacompra_id']; ?>)"><i class="fa fa-wrench"></i></button>
									&nbsp;
									<button class="btn btn-danger btn-sm" id="btn_modificar" onclick="del_facturacompra(<?php echo $rs_facturacompra['AI_facturacompra_id']; ?>)"><i class="fa fa-times"></i></button>
								</td>
							</tr>
<?php				}
					}else{ ?>
						<tr>
							<td colspan="4">&nbsp;</td>
						</tr>
<?php			}	?>
				</tbody>
			</table>
		</div>
	</div>
	<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<button type="button" id="btn_back" class="btn btn-warning" onclick="window.location.href='stock.php';">Volver</button>
	</div>
</form>
</div>
	<div id="footer">
		<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
			&copy; Derechos Reservados a: Trilli, S.A. 2017
			<label id="container_btnexit">
	    	<button type="button" class="btn btn-danger" id="btn_exit">Salir</button>
			</label>
		</div>
	</div>
</div>

</body>
</html>
