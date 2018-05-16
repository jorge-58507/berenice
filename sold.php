<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_stock.php';

	$qry_product=$link->query("SELECT * FROM bh_producto ORDER BY TX_producto_value ASC LIMIT 5 ")or die($link->error);
	$rs_product=$qry_product->fetch_array();

$fecha_actual = date('d-m-Y');
$month_year = date('m-Y',strtotime($fecha_actual));
$fecha_inicial = date('d-m-Y',strtotime("01-".$month_year));
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
<link href="attached/css/stock_css.css" rel="stylesheet" type="text/css" />
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
$("#txt_filterproduct").on("keyup", function(){
	var limit = ($("input[name=r_limit]:checked").val());
	$.ajax({	data: { "a" : this.value, "b" : limit },	type: "GET",	dataType: "text",	url: "attached/get/filter_product_sale.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 console.log("GOOD" + textStatus);
		 $("#tbl_product tbody").html(data);
	 })
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

$("#txt_filterfacturaf").on("keyup",function(){
	var limit = $("input[name=r_limit]:checked").val();
	var date_i = $("#txt_date_initial").val();
	var date_f = $("#txt_date_final").val();
	$.ajax({	data: { "a" : this.value, "b" : limit, "c" : date_i, "d" : date_f  },	type: "GET",	dataType: "text",	url: "attached/get/filter_facturaf_sold.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 console.log("GOOD" + textStatus);
		 $("#tbl_facturaf tbody").html(data);
	 })
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
})
$("#btn_filterfacturaf").on("click",function(){
	$("#txt_filterfacturaf").keyup();
})
$("#btn_printfacturaf").on("click",function(){
	var value = $("#txt_filterfacturaf").val();
	var limit = $("input[name=r_limit]:checked").val();
	var date_i = $("#txt_date_initial").val();
	var date_f = $("#txt_date_final").val();
	var href = "print_venta_html.php?a="+value+"&b="+limit+"&c="+date_i+"&d="+date_f;
	print_html(href);
})
$("#btn_printfacturaf_total").on("click",function(){
	var date_i = $("#txt_date_initial").val();
	var date_f = $("#txt_date_final").val();
	var href = "print_venta_total_html.php?c="+date_i+"&d="+date_f;
	print_html(href);
})
});
function filter_salebyproduct(product_id){
	var limit = ($("input[name=r_limit]:checked").val());
	var date_i = $("#txt_date_initial").val();
	var date_f = $("#txt_date_final").val();
	$.ajax({	data: { "a" : product_id, "b" : limit , "c" : date_i , "d" : date_f },	type: "GET",	dataType: "text",	url: "attached/get/filter_salebyproduct.php", })
	 .done(function( data, textStatus, jqXHR ) {	console.log("GOOD" + textStatus);	$("#tbl_facturaf tbody").html(data);	})
	 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD" + textStatus);});
}
function filter_productbysale(facturaf_id){
	$.ajax({	data: { "a" : facturaf_id },	type: "GET",	dataType: "text",	url: "attached/get/filter_productbysale.php", })
	 .done(function( data, textStatus, jqXHR ) {	console.log("GOOD" + textStatus);	$("#tbl_product tbody").html(data);	})
	 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD" + textStatus);});
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

<div id="container_product" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pt_7">
  <div id="container_filterproduct" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
    <label class="label label_blue_sky"  for="txt_filterproduct">Buscar por Producto:</label>
    <input type="text" alt="table" class="form-control" id="txt_filterproduct" name="txt_filterproduct" placeholder="Nombre o C&oacute;digo de producto" />
  </div>
  <div id="container_rlimit" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
    <label class="label label_blue_sky"  for="r_limit">Mostrar:</label><br />
<label class="radio-inline pt_7"><input type="radio" name="r_limit" id="r_limit" value="10" checked="checked"/> 10</label>
<label class="radio-inline pt_7"><input type="radio" name="r_limit" id="r_limit" value="50" /> 50</label>
<label class="radio-inline pt_7"><input type="radio" name="r_limit" id="r_limit" value="" /> Todas</label>
	</div>
	<div id="container_date" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
		<div id="container_txtdateinitial" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<label class="label label_blue_sky"  for="txt_date_initial">F. Inicio</label>
				<input type="text" id="txt_date_initial" class="form-control" readonly="readonly" value="<?php echo $fecha_inicial; ?>" />
		</div>
		<div id="container_txtdatefinal" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<label class="label label_blue_sky"  for="txt_date_final">F. Final</label>
				<input type="text" id="txt_date_final" class="form-control" readonly="readonly" value="<?php echo $fecha_actual; ?>" />
		</div>
	</div>
	<div id="container_tblproduct" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

        <?php
		if($nr_product=$qry_product->num_rows != '0'){
			?>
	<table id="tbl_product" border="0" class="table table-bordered table-hover table-condensed">
	<thead class="bg-primary">
	<tr>
		<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Codigo</th>
    <th class="col-xs-6 col-sm-6 col-md-6 col-lg-6">Nombre</th>
		<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Cantidad</th>
		<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Precio</th>
	</tr>
	</thead>
	<tfoot class="bg-primary">
		<td></td><td></td><td></td><td></td>
	</tfoot>
	<tbody>
	<?php
    do{
        ?>
    <tr onclick="filter_salebyproduct('<?php echo $rs_product['AI_producto_id']; ?>');">
        <td><?php echo $rs_product['TX_producto_codigo'] ?></td>
        <td><?php echo $r_function->replace_special_character($rs_product['TX_producto_value']); ?></td>
        <td>
        <?php
        if($rs_product['TX_producto_cantidad'] >= $rs_product['TX_producto_maximo']){
            echo '<font style="color:#51AA51">'.$rs_product['TX_producto_cantidad'].'</font>';
        }elseif($rs_product['TX_producto_cantidad'] <= $rs_product['TX_producto_minimo']){
            echo '<font style="color:#C63632">'.$rs_product['TX_producto_cantidad'].'</font>';
        }else{
            echo '<font style="color:#000000">'.$rs_product['TX_producto_cantidad'].'</font>';
        }
        ?>
        </td>
    </tr>
        <?php
    }while($rs_product=$qry_product->fetch_array());
    ?>
    </tbody>
    </table>

            <?php
		}
		?>
	</div>
</div>
<div id="container_facturaf" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pt_7">
    <div id="container_filterfacturaf" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <label class="label label_blue_sky"  for="txt_filterfacturaf">Buscar por Factura:</label>
        <input type="text" class="form-control" id="txt_filterfacturaf" name="txt_filterfacturaf" placeholder="Numero de Factura o Nombre de Cliente" />
    </div>
		<div id="container_btnfilterfacturaf" class="col-xs-4 col-sm-4 col-md-4 col-lg-4 pt_14">
			<button type="button" id="btn_filterfacturaf" class="btn btn-success btn-search"><i class="fa fa-search" aria-hidden="true"></i></button>
			&nbsp;&nbsp;
			<button type="button" id="btn_printfacturaf" class="btn btn-success"><i class="fa fa-print" aria-hidden="true">&nbsp;</i>Imprimir Consulta</button>
			&nbsp;&nbsp;
			<button type="button" id="btn_printfacturaf_total" class="btn btn-info"><i class="fa fa-print" aria-hidden="true">&nbsp;</i>Imprimir Totales</button>
		</div>
	<div id="container_tblfacturaf" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<table id="tbl_facturaf" class="table table-bordered table-condensed table-striped">
        <thead>
        <tr class="bg-info">
	        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Fecha</th>
	        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Nº de Factura</th>
	        <th class="col-xs-6 col-sm-6 col-md-6 col-lg-6">Cliente</th>
	        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
	        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Precio</th>
        </tr>
	    </thead>
        <tfoot class="bg-info"><tr><td></td><td></td><td></td><td></td><td></td></tr></tfoot>
        <tbody><tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr></tbody>
        </table>
    </div>

</div>
<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<button type="button" id="btn_back" class="btn btn-warning" onclick="history.back(1);">Volver</button>
</div>
</form>
</div>
<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
&copy; Derechos Reservados a: Trilli, S.A. 2017
	<div id="container_btnexit" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
    	<button type="button" class="btn btn-danger" id="btn_exit">Salir</button></div>
    </div>
	</div>
</div>
</div>

</body>
</html>
