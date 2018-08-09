<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_paydesk.php';

$qry_oldest = $link->query("SELECT bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_deficit FROM bh_facturaf WHERE bh_facturaf.facturaf_AI_cliente_id = '{$_GET['a']}' AND bh_facturaf.TX_facturaf_deficit > 0 ORDER BY TX_facturaf_fecha ASC LIMIT 1");
$rs_oldest = $qry_oldest->fetch_array();
$ff_oldest = $rs_oldest['TX_facturaf_fecha'];
$fecha_actual=date('Y-m-d');
$fecha_i=date('d-m-Y',strtotime($ff_oldest));
$fecha_f = date('d-m-Y', strtotime($fecha_actual));

$client_id = $_GET['a'];
$txt_client="SELECT bh_cliente.AI_cliente_id, bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_telefono, SUM(bh_facturaf.TX_facturaf_deficit) AS deficit, SUM(bh_facturaf.TX_facturaf_subtotalni) AS subtotal_ni, SUM(bh_facturaf.TX_facturaf_subtotalci) AS subtotal_ci, SUM(bh_facturaf.TX_facturaf_total) AS total, SUM(bh_facturaf.TX_facturaf_impuesto) AS impuesto FROM (bh_cliente INNER JOIN bh_facturaf ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id)
WHERE bh_facturaf.facturaf_AI_cliente_id = '$client_id'";
$qry_client=$link->query($txt_client);
$rs_client=$qry_client->fetch_array();

$txt_facturaf="SELECT
bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_subtotalni, bh_facturaf.TX_facturaf_subtotalci, bh_facturaf.TX_facturaf_impuesto, bh_facturaf.TX_facturaf_descuento, bh_facturaf.TX_facturaf_deficit
FROM bh_facturaf
WHERE bh_facturaf.facturaf_AI_cliente_id = '$client_id' ORDER BY TX_facturaf_fecha DESC";
$qry_facturaf=$link->query($txt_facturaf);
$rs_facturaf=$qry_facturaf->fetch_array();
$nr_facturaf=$qry_facturaf->num_rows;
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
<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript">

$(document).ready(function() {

	$("#btn_print_clientaccount").on("click", function(){
		var date_i = $("#txt_date_initial").val();
		var date_f = $("#txt_date_final").val();
		window.open("print_client_account.php?a="+<?php echo $client_id; ?>+"&b="+date_i+"&c="+date_f+"");
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

</script>

</head>

<body>

<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-2" >
		<div id="logo" ></div>
	</div>
</div>

<div id="content-sidebar_popup" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 px_0 py_7">
		<div id="container_spanclientname" class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
      <label class="label label_blue_sky" for="span_clientname">Nombre</label>
			<span id="span_clientname" class="form-control bg-disabled"><?php echo $rs_client['TX_cliente_nombre']; ?></span>
	  </div>
		<div id="container_spandate" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
      <label class="label label_blue_sky" for="span_ruc">RUC</label>
      <span id="span_ruc" class="form-control bg-disabled"><?php echo $rs_client['TX_cliente_cif']; ?></span>
	  </div>
		<div id="container_spanstatus" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
      <label class="label label_blue_sky" for="span_telephon">Telefono</label>
      <span id="span_telephon" class="form-control bg-disabled"><?php echo $rs_client['TX_cliente_telefono']; ?></span>
    </div>
		<div id="container_spantotal" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
      <label class="label label_blue_sky" for="span_total">Total Comprado</label>
      <span id="span_total" class="form-control bg-disabled"><?php echo number_format($rs_client['total'],2); ?></span>
    </div>
		<div id="container_spandeficit" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
      <label class="label label_blue_sky" for="span_deficit">Saldo Deudor</label>
      <span id="span_deficit" class="form-control bg-disabled"><?php echo number_format($rs_client['deficit'],2); ?></span>
    </div>
		<div id="container_spansubtotalni" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
      <label class="label label_blue_sky" for="span_subtotalni">Prod. s/Impuesto</label>
      <span id="span_subtotalni" class="form-control bg-disabled"><?php echo number_format($rs_client['subtotal_ni'],2); ?></span>
    </div>
		<div id="container_spansubtotalci" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
      <label class="label label_blue_sky" for="span_subtotalci">Prod. c/Impuesto</label>
      <span id="span_subtotalci" class="form-control bg-disabled"><?php echo number_format($rs_client['subtotal_ci'],2); ?></span>
    </div>
		<div id="container_spandeficit" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
      <label class="label label_blue_sky" for="span_impuesto">Total en Impuesto</label>
      <span id="span_impuesto" class="form-control bg-disabled"><?php echo number_format($rs_client['impuesto'],2); ?></span>
    </div>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 px_0 py_7 bt_1 bb_1">
		<div id="container_txtdatei"  class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
			<label for="txt_date_initial" class="label label_blue_sky">Fecha</label>
			<input type="text" id="txt_date_initial" name="txt_date_initial" class="form-control" readonly="readonly" value="<?php echo $fecha_i; ?>" />
		</div>
		<div id="container_txtdatef"  class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
			<label for="txt_date_final" class="label label_blue_sky">Fecha</label>
			<input type="text" id="txt_date_final" name="txt_date_final" class="form-control" readonly="readonly" value="<?php echo $fecha_f; ?>" />
		</div>
		<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 no_padding pt_14">
			<button class="btn btn-info" id="btn_print_clientaccount">Imprimir Edo. Cuenta</button>
		</div>
	</div>

    <div id="container_tblfacturaf" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			<table id="tbl_facturaf" class="table table-bordered table-condensed table-striped">
        <thead class="bg-primary">
        <tr>
        	<th>Numero</th><th>Fecha</th><th>Total</th><th>Deficit</th>
        </tr>
        </thead>
        <tfoot class="bg-primary">
        <tr><td></td><td></td><td></td><td></td></tr>
        </tfoot>
        <tbody>
        <?php if($nr_facturaf > '0'){?>
        <?php do{?>
        <tr onclick="javascript: get_datoventabyfacturaf('<?php echo $rs_facturaf['AI_facturaf_id'] ?>');">
        	<td><?php echo $rs_facturaf['TX_facturaf_numero'] ?></td>
          <td><?php echo $rs_facturaf['TX_facturaf_fecha'] ?></td>
          <td><?php	echo number_format($total = $rs_facturaf['TX_facturaf_subtotalni'] + $rs_facturaf['TX_facturaf_subtotalci'] + $rs_facturaf['TX_facturaf_impuesto'],2);?></td>
          <td><?php echo number_format($rs_facturaf['TX_facturaf_deficit'],2); ?></td>
        </tr>
			<?php }while($rs_facturaf=$qry_facturaf->fetch_array());?>
        <?php }else{?>
        <tr>
        	<td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <?php }?>
        </tbody>
        </table>
    </div>
    <div id="container_tbldatoventa" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
    	<table id="tbl_datoventa" class="table table-bordered table-condensed table-striped">
        <thead class="bg-info">
        <tr>
        	<th>Producto</th>
        	<th>Cantidad</th>
        	<th>Total</th>
        </tr>
        </thead>
        <tfoot class="bg-info"><tr><th></th><th></th><th></th></tr></tfoot>
        <tbody>
        <tr>
        	<td></td>
        	<td></td>
        	<td></td>
        </tr>
        </tbody>
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
