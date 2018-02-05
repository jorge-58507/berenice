<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login_paydesk.php';
?>
<?php
$txt_client="SELECT bh_cliente.AI_cliente_id, bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_telefono, SUM(bh_facturaf.TX_facturaf_deficit) AS deficit, SUM(bh_facturaf.TX_facturaf_subtotalni) AS subtotal_ni, SUM(bh_facturaf.TX_facturaf_subtotalci) AS subtotal_ci, SUM(bh_facturaf.TX_facturaf_total) AS total, SUM(bh_facturaf.TX_facturaf_impuesto) AS impuesto , SUM(bh_facturaf.TX_facturaf_descuento) AS descuento FROM (bh_cliente INNER JOIN bh_facturaf ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id) GROUP BY bh_facturaf.facturaf_AI_cliente_id ORDER BY TX_cliente_nombre ASC";
//echo $txt_client;
$qry_client=mysql_query($txt_client);
$rs_client=mysql_fetch_array($qry_client);
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
<link href="attached/css/popup_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	$("#sel_client").css('display','none');
	$("#txt_filterclient").focus(function(){
		$("#sel_client").show(500);
	});
	$("#txt_filterclient").blur(function(){
		$("#sel_client").hide(500);
	});
	
	$("#txt_filterclient").on("keyup",function(){
		$.ajax({
			data: {"a" : this.value}, type: "GET", dataType: "text", url: "attached/get/filter_client_paydesk.php",
		})
		.done(function( data, textStatus, jqXHR ) {	$("#container_selclient").html(data);	})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("bad "+textStatus);	});
	});
	
	$("#btn_filter").on("click",function(){
		if($("#txt_filterclient").prop("alt") == "" || $("#txt_datei").val() == "" || $("#txt_datef").val() == ""){
			return false;
		}
		$.ajax({
			data: {"a" : $("#txt_filterclient").prop("alt"),"b" : $("#txt_datei").val(),"c" : $("#txt_datef").val()}, type: "GET", dataType: "text", url: "attached/get/filter_client_facturaf.php",
		})
		 .done(function( data, textStatus, jqXHR ) {
			 $("#container_tblfacturaf").html(data);	})
		 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
	});
	$( function() {
    var dateFormat = "dd-mm-yy",
      from = $( "#txt_datei" )
        .datepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 2
        })
        .on( "change", function() {
          to.datepicker( "option", "minDate", getDate( this ) );
        }),
      to = $( "#txt_datef" ).datepicker({
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

function set_txtfilterclient(field,cif,telephone,deficit,subtotal_ci,subtotal_ni,total,impuesto,descuento){
	$("#txt_filterclient").prop("alt",field.value);
	$("#txt_filterclient").prop("value",field.text);
	$("#span_clientname").html(field.text);
	$("#span_ruc").html(cif);
	$("#span_telephon").html(telephone);
	$("#span_deficit").html(deficit);
	$("#span_subtotalni").html(subtotal_ni);
	$("#span_subtotalci").html(subtotal_ci);
	$("#span_total").html(total);
	$("#span_impuesto").html(impuesto);
	$("#span_descuento").html(descuento);

//	document.getElementById("txt_filterclient").value = field.text;
//	document.getElementById("txt_filterclient").alt = field.value;
}

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
	<div id="container_txtfilterclient" class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
    	<label for="txt_filterclient">Cliente:</label>
	    <input type="text" class="form-control" id="txt_filterclient" name="txt_filterclient" />
    </div>
	<div id="container_txtdatei" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
    	<label for="txt_datei">Fecha Inicio:</label>
	    <input type="text" class="form-control" id="txt_datei" name="txt_datei" readonly="readonly" value="<?php echo date('d-m-Y',strtotime('-1 week')); ?>" />
    </div>
	<div id="container_txtdatef" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
    	<label for="txt_datef">Fecha Final:</label>
	    <input type="text" class="form-control" id="txt_datef" name="txt_datef" readonly="readonly" value="<?php echo date('d-m-Y'); ?>" />
    </div>
    <div id="container_btnfilter" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
    	<button type="button" id="btn_filter" class="btn btn-success"><i class="fa fa-search" aria-hidden="true"></i>
</button>
    </div>
	<div id="container_selclient" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	    <select id="sel_client" name="sel_client" class="form-control" size="4">
        	<?php do{ ?>
            <option value="<?php echo $rs_client['AI_cliente_id']; ?>" onclick="set_txtfilterclient(this,'<?php echo $rs_client['TX_cliente_cif']; ?>','<?php echo $rs_client['TX_cliente_telefono']; ?>','<?php echo round($rs_client['deficit'],2); ?>','<?php echo round($rs_client['subtotal_ci'],2); ?>','<?php echo round($rs_client['subtotal_ni'],2); ?>','<?php echo round($rs_client['total'],2); ?>','<?php echo round($rs_client['impuesto'],2); ?>','<?php echo round($rs_client['descuento'],2); ?>')"><?php echo $rs_client['TX_cliente_nombre']; ?></option>
            <?php }while($rs_client=mysql_fetch_assoc($qry_client)); ?>
        </select>
    </div>
	<div id="container_spanclientname" class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
        <label for="span_clientname">Nombre</label>
<span id="span_clientname" class="form-control bg-disabled"><?php echo $rs_client['TX_cliente_nombre']; ?></span>
    </div>
	<div id="container_spandate" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <label for="span_ruc">RUC</label>
        <span id="span_ruc" class="form-control bg-disabled"><?php echo $rs_client['TX_cliente_cif']; ?></span>
    </div>
	<div id="container_spanstatus" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <label for="span_telephon">Telefono</label>
        <span id="span_telephon" class="form-control bg-disabled"><?php echo $rs_client['TX_cliente_telefono']; ?></span>
    </div>
    
	<div id="container_spantotal" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <label for="span_total">Total Comprado</label>
        <span id="span_total" class="form-control bg-disabled"><?php echo number_format(0,2); ?></span>
    </div>
	<div id="container_spandeficit" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <label for="span_deficit">Saldo Deudor</label>
        <span id="span_deficit" class="form-control bg-disabled"><?php echo number_format(0,2); ?></span>
    </div>
	<div id="container_spansubtotalni" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <label for="span_subtotalni">Prod. s/Impuesto</label>
        <span id="span_subtotalni" class="form-control bg-disabled"><?php echo number_format(0,2); ?></span>
    </div>
	<div id="container_spansubtotalci" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <label for="span_subtotalci">Prod. c/Impuesto</label>
        <span id="span_subtotalci" class="form-control bg-disabled"><?php echo number_format(0,2); ?></span>
    </div>
	<div id="container_spandeficit" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <label for="span_impuesto">Total en Impuesto</label>
        <span id="span_impuesto" class="form-control bg-disabled"><?php echo number_format(0,2); ?></span>
    </div>
	<div id="container_spandescuento" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <label for="span_descuento">Total Descuento</label>
        <span id="span_descuento" class="form-control bg-disabled"><?php echo number_format(0,2); ?></span>
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
        <tr>
        	<td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
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
