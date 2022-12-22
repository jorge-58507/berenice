<?php
require 'bh_conexion.php';
$link=conexion();
$qry_product = $link->query("SELECT TX_producto_value, TX_producto_codigo, TX_producto_referencia FROM bh_producto WHERE AI_producto_id = '{$_GET['a']}'")or die($link->error);
$rs_product=$qry_product->fetch_array(MYSQLI_ASSOC);
$qry_inventory = $link->query("SELECT TX_inventario_json FROM bh_inventario WHERE inventario_AI_producto_id = '{$_GET['a']}'")or die($link->error);
$rs_inventory=$qry_inventory->fetch_array(MYSQLI_ASSOC);
$array_inventory = json_decode($rs_inventory['TX_inventario_json'], true);
$last_count = end($array_inventory);
$last_count_date = key($last_count);
$last_count_quantity = $last_count[$last_count_date];
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
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>

<script type="text/javascript">

$(document).ready(function() {

	$('#btn_cancel').click(function(){
		self.close();
	});

$( function() {
	var dateFormat = "dd-mm-yy",
		from = $( "#txt_datei" )
		.datepicker({
		defaultDate: "+1w",
		changeMonth: true,
		changeYear: true,
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

$("#btn_calculate").on("click",function(){
	 // ############   RELACION COMPRA/VENTA
	 if(isNaN($("#txt_count").val())) { console.log("no es un numero"); return false; }
	$.ajax({	data: {'a': $("#txt_datei").val(), 'b': $("#txt_datef").val(), 'c': '<?php echo $_GET['a']; ?>', 'd' : $("#txt_count").val() },	type: "GET",	dataType: "text",	url: "attached/get/get_relation.php", })
	 .done(function( data, textStatus, jqXHR ) {
	 console.log(data);
	 var raw_data = JSON.parse(data);
//	 console.log(raw_data);
	 $("#span_purchase").html(`${raw_data[0]}+${raw_data[3]}=${raw_data[0]+raw_data[3]}`);
	 $("#span_sold").html( `${raw_data[1]}+${raw_data[4]}=${raw_data[1]+raw_data[4]}`	);
	 $("#span_relation").html( raw_data[2]+"%"	);
	})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
	 // ############      ROTACIONES
	$.ajax({	data: {'a': $("#txt_datei").val(), 'b': $("#txt_datef").val(), 'c': <?php echo $_GET['a']; ?>},	type: "GET",	dataType: "text",	url: "attached/get/get_rotation.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 console.log(data);
		 var raw_data = JSON.parse(data.toString());
		 $("#span_average").html( raw_data[3]	);
		 $("#span_rotation4month").html( raw_data[4]	);
		 $("#span_day4rotation").html( raw_data[5]	);

		 var txt_alert="<strong>Desde: </strong>"+raw_data[1]+" <strong>Hasta: </strong>"+raw_data[2];
		 $("#container_alertdate").hide(500,"linear")
		 $("#container_alertdate").show(500,"linear",function(){
			 $('#alert_date').html(txt_alert);
	 	});
	})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
});
$("#btn_print").on("click",function(){
	print_html("print_tbl_rotation.php?a="+$("#txt_datei").val()+"&b="+$("#txt_datef").val()+"&c=<?php echo $_GET['a']; ?>");
});

});


</script>

</head>

<body>
<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
		<div id="logo" ></div>
	</div>
</div>

<div id="content-sidebar_popup" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <?php $date_i=date('d-m-Y',strtotime($last_count_date)); $date_f=date('d-m-Y',strtotime('+1 day')); ?>
    <div id="container_product" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
        <div id="container_spanproduct" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
	    	<label for="span_product" class="label label-info">Nombre</label>
            <input type="text" id="span_product" class="form-control bg-disabled" value="<?php echo $r_function->replace_special_character($rs_product['TX_producto_value']); ?>" />
		</div>
		<div id="container_spancode" class="col-xs-6 col-sm-6 col-md-6 col-lg-6" >
        	<label for="span_code" class="label label-info">Codigo</label>
			<span id="span_code" class="form-control bg-disabled"><?php echo $rs_product['TX_producto_codigo']; ?></span>
        </div>
		<div id="container_spanreference" class="col-xs-6 col-sm-6 col-md-6 col-lg-6" >
    	<label for="span_reference" class="label label-info">Referencia</label>
			<span id="span_reference" class="form-control bg-disabled"><?php echo $r_function->replace_special_character($rs_product['TX_producto_referencia']); ?></span>
    </div>
    </div>
    <div id="container_date" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
        <div id="container_datei" class="col-xs-4 col-sm-4 col-md-4 col-lg-4" >
        	<label for="txt_datei" class="label label-info">Fecha Inicio</label>
           <input type="text" id="txt_datei" class="form-control" readonly="readonly" value="<?php echo $date_i; ?>" />
        </div>
        <div id="container_datef" class="col-xs-4 col-sm-4 col-md-4 col-lg-4" >
        	<label for="txt_datef" class="label label-info">Fecha Final</label>
           <input type="text" id="txt_datef" class="form-control" readonly="readonly" value="<?php echo $date_f; ?>" />
        </div>
				<div id="container_txtcount" class="col-xs-4 col-sm-4 col-md-4 col-lg-4"  >
					<label for="txt_count" class="label label-info">Conteo</label>
					<input type="text" id="txt_count" name="" class="form-control" value="<?php echo $last_count_quantity ?>">
				</div>
    </div>
    <div id="container_relation" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
				<div id="container_spanpurchase" class="col-xs-4 col-sm-4 col-md-4 col-lg-4"  >
        	<label for="span_purchase" class="label label-info">Ingreso</label>
          <span id="span_purchase" class="form-control bg-disabled"></span>
        </div>
        <div id="container_spansold" class="col-xs-4 col-sm-4 col-md-4 col-lg-4"  >
        	<label for="span_sold" class="label label-info">Egreso</label>
            <span id="span_sold" class="form-control bg-disabled"></span>
        </div>
        <div id="container_spanrelation" class="col-xs-4 col-sm-4 col-md-4 col-lg-4"  >
        	<label for="span_relation" class="label label-info">Relaci&oacute;n (%)</label>
            <span id="span_relation" class="form-control bg-disabled"></span>
        </div>
    </div>
	<div id="container_alertdate" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
        <div id="alert_date"class="alert alert-info alert-dismissable fade in">
        &nbsp;
        </div>
    </div>
    <div id="container_rotation" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
        <div id="container_average" class="col-xs-4 col-sm-4 col-md-4 col-lg-4" >
        	<label for="span_average" class="label label-info">Promedio Existencia</label>
            <span id="span_average" class="form-control bg-disabled"></span>
        </div>
        <div id="container_rotation4month" class="col-xs-4 col-sm-4 col-md-4 col-lg-4" >
        	<label for="span_rotation4month" class="label label-info">N&deg; Rotaciones/Mes</label>
            <span id="span_rotation4month" class="form-control bg-disabled"></span>
        </div>
        <div id="container_day4rotation" class="col-xs-4 col-sm-4 col-md-4 col-lg-4" >
        	<label for="span_day4rotation" class="label label-info">N&deg; Dias/Rotaci&oacute;n</label>
            <span id="span_day4rotation" class="form-control bg-disabled"></span>
        </div>
    </div>
    <div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
    <button type="button" id="btn_calculate" class="btn btn-success">Calcular</button>
    &nbsp;&nbsp;
    <button type="button" id="btn_print" class="btn btn-info">Imprimir</button>
    &nbsp;&nbsp;
    <button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
    </div>


</div>


<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
&copy; Derechos Reservados a: Jorge Salda&nacute;a <?php echo date('Y'); ?>
	</div>
</div>
</div>

</body>
</html>
