<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_paydesk.php';

$client_id=$_GET['a'];
$qry_credit=$link->query("SELECT TX_cliente_limitecredito, TX_cliente_plazocredito, TX_cliente_saldo FROM bh_cliente WHERE AI_cliente_id = '$client_id'")or die($link->error);
$rs_credit=$qry_credit->fetch_array(MYSQLI_ASSOC);
$qry_client=$link->query("SELECT TX_cliente_nombre, TX_cliente_cif, TX_cliente_telefono, TX_cliente_direccion, TX_cliente_porcobrar FROM bh_cliente WHERE AI_cliente_id = '$client_id'")or die($link->error);
$rs_client=$qry_client->fetch_array(MYSQLI_ASSOC);
$client_name=$rs_client['TX_cliente_nombre'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>

<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap-theme.css" rel="stylesheet" type="text/css" />
<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_layout.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />
<link href="attached/css/popup_css.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/admin_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript">

$(document).ready(function() {

$("#txt_limitcredit").validCampoFranz('0123456789');
$("#txt_credit_term").validCampoFranz('0123456789');

$("#btn_cancel").click(function(){
	self.close();
});
$("#btn_save").click(function(){

	if($("#txt_limitcredit").val() == ""){
		$("#txt_limitcredit").addClass("input_invalid");
		return false;
	}	$("#txt_limitcredit").removeClass("input_invalid");
	if($("#txt_credit_term").val() == ""){
		$("#txt_credit_term").addClass("input_invalid");
		return false;
	}	$("#txt_credit_term").removeClass("input_invalid");

	if($("#txt_clientname").val() === ""){
		$("#txt_clientname").addClass("input_invalid");
		return false;
	}	$("#txt_clientname").removeClass("input_invalid");

	$.ajax({
		data: {"a" : "<?php echo $client_id ?>", "b": $("#txt_limitcredit").val(), "c": $("#txt_credit_term").val(), "d" : $("#sel_porcobrar").val(), "e" : $("#txt_clientname").val()},type: "GET",	dataType: "text",	url: "attached/get/upd_client_credit.php",
	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD "+textStatus); })
	.fail(function( jqXHR, textStatus, errorThrown ) { console.log("BAD "+textStatus);	});
	setTimeout("self.close()",250);
});

$("#btn_print_allpayment").click(function(){
	var href = "print_debito_client.php?a=<?php echo $client_id; ?>&b="+$("#txt_datepay_i").val()+"&c="+$("#txt_datepay_f").val()+"";
	print_html(href);
});

$("#btn_print_allnc").click(function(){
	var href = "print_nc_client.php?a=<?php echo $client_id; ?>&b="+$("#txt_datenc_i").val()+"&c="+$("#txt_datenc_f").val()+"";
	print_html(href);
});
$("#btn_print_allff").click(function(){
	var href = "print_facturaf_client.php?a=<?php echo $client_id; ?>&b="+$("#txt_datei").val()+"&c="+$("#txt_datef").val()+"&d="+$("#sel_deficit").val()+"";
	print_html(href);
});

/*   #########################	FACTURAS	###############################	*/

$("#btn_filter").on("click",function(){
	if($("#txt_datei").val() == "" || $("#txt_datef").val() == ""){
		return false;
	}
	$.ajax({
		data: {"a" : <?php echo $client_id; ?>,"b" : $("#txt_datei").val(),"c" : $("#txt_datef").val(),"d" : $("#sel_deficit").val(),"e" : $("#sel_paymentmethod").val()}, type: "GET", dataType: "text", url: "attached/get/filter_client_deficit.php",
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

/*   #########################	NOTAS DE CREDITO	###############################	*/

$("#btn_filter_nc").on("click",function(){
	if($("#txt_datenc_i").val() == "" || $("#txt_datenc_f").val() == ""){
		return false;
	}
	$.ajax({
		data: {"a" : <?php echo $client_id; ?>,"b" : $("#txt_datenc_i").val(),"c" : $("#txt_datenc_f").val()}, type: "GET", dataType: "text", url: "attached/get/filter_client_nc.php",
	})
	.done(function( data, textStatus, jqXHR ) {
		$("#container_tblcreditnote").html(data);	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
});

$( function() {
	var dateFormat = "dd-mm-yy",
	from = $( "#txt_datenc_i" )
	.datepicker({
		defaultDate: "+1w",
		changeMonth: true,
		numberOfMonths: 2
	})
	.on( "change", function() {
		to.datepicker( "option", "minDate", getDate( this ) );
	}),
	to = $( "#txt_datenc_f" ).datepicker({
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

/*  #########################          PAYMENTS          ###################*/

$("#btn_filter_pay").on("click",function(){
	if($("#txt_datepay_i").val() == "" || $("#txt_datepay_f").val() == ""){
		return false;
	}
	$.ajax({
		data: {"a" : <?php echo $client_id; ?>,"b" : $("#txt_datepay_i").val(),"c" : $("#txt_datepay_f").val()}, type: "GET", dataType: "text", url: "attached/get/filter_client_payment.php",
	})
	 .done(function( data, textStatus, jqXHR ) {
		 $("#container_tblpayment").html(data);	})
	 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
});

$( function() {
	var dateFormat = "dd-mm-yy",
	from = $( "#txt_datepay_i" )
	.datepicker({
		defaultDate: "+1w",
		changeMonth: true,
		numberOfMonths: 2
	})
	.on( "change", function() {
		to.datepicker( "option", "minDate", getDate( this ) );
	}),
	to = $( "#txt_datepay_f" ).datepicker({
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

$("#btn_addsaldo").on("click",function(){
	$.ajax({ data: "", type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD " + textStatus);
		if (data[0][0] != "") {
			var value = prompt("Ingrese el monto");
			value = val_intw2dec(value);
			$.ajax({ data: {"a" : value, "b" : '<?php echo $client_id; ?>'}, type: "GET", dataType: "text", url: "attached/get/upd_cash2saldo.php",	})
			.done(function( data, textStatus, jqXHR ) {	console.log("GOOD " + textStatus);
			$("#span_saldo").html(data);
	 	})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
})

$("#txt_credit_term").on("focus", function(){
	$.ajax({ data: "", type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD " + textStatus);
		if (data[0][0] != "") {
			$("#txt_credit_term").removeAttr("readonly");
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
})
$("#txt_limitcredit").on("focus", function(){
	$.ajax({ data: "", type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD " + textStatus);
		if (data[0][0] != "") {
			$("#txt_limitcredit").removeAttr("readonly");
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
})

$('#sel_porcobrar option:not(:selected)').attr('disabled',true);
$("#sel_porcobrar").on("click", function(){
	$.ajax({ data: "", type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD " + textStatus);
		if (data[0][0] != "") {
			$('#sel_porcobrar option:not(:selected)').attr('disabled',false);
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
})

$("#txt_clientname").on("focus", function(){
	$.ajax({ data: "", type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD " + textStatus);
		if (data[0][0] != "") {
			$("#txt_clientname").removeAttr("readonly");
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
})
$("#txt_clientname").on("blur", function(){
	$("#txt_clientname").val($("#txt_clientname").val().toUpperCase());
})


});


function convert_saldo2cash(client_id){

	$.ajax({ data: "", type: "GET", dataType: "JSON", url: "attached/get/get_session_admin.php",	})
	.done(function( data, textStatus, jqXHR ) {	console.log("GOOD " + textStatus);
		if (data[0][0] != "") {
			var value = prompt("Ingrese el monto");
			value = val_intw2dec(value);
			$.ajax({ data: {"a" : value, "b" : '<?php echo $client_id; ?>'}, type: "GET", dataType: "text", url: "attached/get/upd_saldo2cash.php",	})
			.done(function( data, textStatus, jqXHR ) {	console.log("GOOD " + textStatus);
			$("#span_saldo").html(data);
	 	})
		.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});

}
function open_newdebit(client_id){
	$.ajax({	data: "",	type: "GET",	dataType: "JSON",	url: "attached/get/get_session_admin.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 if(data[0][0] != ""){
			 document.location.href = 'popup_newdebit.php?a='+client_id;
		 }else{
			 open_popup('popup_loginadmin.php?z=start_admin.php','_popup','425','420');
		 }
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}

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
<form method="post" name="form_loginadmin" action="">
<div id="container_client" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_spanname" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
    	<label class="label label_blue_sky" for="span_name">Nombre:</label>
			<input type="text" id="txt_clientname" name="" class="form-control" value="<?php echo $rs_client['TX_cliente_nombre']; ?>" readonly="readonly">
    </div>
	<div id="container_spanruc" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
    	<label class="label label_blue_sky" for="span_ruc">RUC:</label>
    	<span id="span_ruc" class="form-control bg-disabled"><?php echo $rs_client['TX_cliente_cif']; ?></span>
    </div>
	<div id="container_spantelephone" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
    	<label class="label label_blue_sky" for="span_telephone">Tel&eacute;fono:</label>
    	<span id="span_telephone" class="form-control bg-disabled"><?php echo $rs_client['TX_cliente_telefono']; ?></span>
    </div>
		<div id="container_spandirection" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label class="label label_blue_sky" for="span_direction">Direcci&oacute;n:</label>
			<span id="span_direction" class="form-control bg-disabled"><?php echo $rs_client['TX_cliente_direccion']; ?></span>
		</div>
</div>
<div id="container_credit" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_limit" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
    <label class="label label_blue_sky" for="txt_limitcredit">Limite de credito</label>
    <input type="text" id="txt_limitcredit" name="txt_limitcredit" class="form-control"	value="<?php echo $rs_credit['TX_cliente_limitecredito']; ?>" readonly="readonly" />
  </div>
	<div id="container_plazo" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
    <label class="label label_blue_sky" for="txt_credit_term">Plazo en semanas</label>
    <input type="text" id="txt_credit_term" name="txt_credit_term" class="form-control"	value="<?php echo $rs_credit['TX_cliente_plazocredito']; ?>" readonly="readonly" />
  </div>
	<div id="container_saldo" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
		<div id="container_txtsaldo" class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
			<label class="label label_blue_sky" for="span_saldo">Saldo Disp.</label>
			<span id="span_saldo" class="form-control bg-disabled"><?php echo number_format($rs_credit['TX_cliente_saldo'],2); ?></span>
		</div>
		<div id="container_btnaddsaldo" class="col-xs-5 col-sm-5 col-md-5 col-lg-5 side-btn-md px_0">
			<button type="button" id="btn_addsaldo" class="btn btn-info"><i class="fa fa-plus" aria-hidden="true"></i></button>&nbsp;
			<button type="button" id="btn_convertsaldo" class="btn btn-primary" onclick="convert_saldo2cash();"><strong><i class="fa fa-money" aria-hidden="true"></i></strong></button>
		</div>
  </div>
	<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
		<label for="sel_porcobrar" class="label label_blue_sky">Por Cobrar</label>
		<select class="form-control" name="sel_porcobrar" id="sel_porcobrar">
			<?php echo ($rs_client['TX_cliente_porcobrar'] === '0') ? '<option value="0" selected="selected">Inhabilitado</option>' : '<option value="0">Inhabilitado</option>'; ?>
			<?php echo ($rs_client['TX_cliente_porcobrar'] === '1') ? '<option value="1" selected="selected">Habilitado</option>' : '<option value="1">Habilitado</option>'; ?>
		</select>
	</div>
  <div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  	<button type="button" id="btn_save" class="btn btn-success">Aceptar</button>
		&nbsp;&nbsp;
		<button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>

  </div>
</div>
<div id="container_facturaf" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pt_7">
	<div id="container_filterfacturaf" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<?php
	$date_i=date('d-m-Y',strtotime('-1 week'));
	$date_f=date('d-m-Y');
	?>
	    <div id="container_txtdatei" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	        <label class="label label_blue_sky" for="txt_datei">Fecha Inicio:</label>
	        <input type="text" class="form-control" id="txt_datei" name="txt_datei" readonly="readonly" value="<?php echo $date_i; ?>" />
	    </div>
	    <div id="container_txtdatef" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	        <label class="label label_blue_sky" for="txt_datef">Fecha Final:</label>
	        <input type="text" class="form-control" id="txt_datef" name="txt_datef" readonly="readonly" value="<?php echo $date_f; ?>" />
	    </div>
	    <div id="container_seldeficit" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <label class="label label_blue_sky" for="sel_deficit">Saldo</label>
				<select id="sel_deficit" class="form-control">
					<option value="todas">Todas</option>
          <option value="deficit">Con Saldo</option>
				</select>
	    </div>
			<div id="container_selpaymentmethod" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<?php 	$qry_paymentmethod = $link->query("SELECT AI_metododepago_id, TX_metododepago_value FROM bh_metododepago")or die($link->error);
				$raw_metododepago = array();
				while ($rs_paymentmethod = $qry_paymentmethod->fetch_array()) {
					$raw_metododepago[$rs_paymentmethod['AI_metododepago_id']] = $rs_paymentmethod['TX_metododepago_value'];
				} ?>
        <label class="label label_blue_sky" for="sel_paymentmethod">M&eacute;todo de P.</label>
				<select id="sel_paymentmethod" class="form-control">
					<option value="todos">Todos</option>
<?php  		foreach ($raw_metododepago as $key => $value) {
						echo "<option value=\"$key\">$value</option>";
					}	?>
				</select>
	    </div>
			<div id="container_btnfilter" class="col-xs-2 col-sm-2 col-md-2 col-lg-2 side-btn-md-label">
	        <button type="button" id="btn_filter" class="btn btn-success"><i class="fa fa-search" aria-hidden="true"></i></button>
	    </div>
	</div>
	<div id="container_tblfacturaf" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<?php
	$date_i=date('Y-m-d',strtotime($date_i));
	$date_f=date('Y-m-d',strtotime($date_f));

		$txt_facturaf="SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_ticket, bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_user.TX_user_seudonimo, bh_facturaf.facturaf_AI_cliente_id
		FROM bh_facturaf INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaf.facturaf_AI_user_id
		WHERE facturaf_AI_cliente_id = '$client_id' AND TX_facturaf_fecha >= '$date_i' AND TX_facturaf_fecha <= '$date_f' ORDER BY AI_facturaf_id DESC";
		$qry_facturaf=$link->query($txt_facturaf) or die($link->error);
	?>
		<table id="tbl_facturaf" class="table table-bordered table-condensed table-striped">
	    <caption class="caption">Facturas</caption>
	    <thead class="bg-primary">
	    <tr>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">FECHA</th>
	      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">HORA</th>
	    	<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">FACTURA N&deg;</th>
	    	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">TICKET</th>
	      <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">TOTAL</th>
	      <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">DEUDA</th>
	      <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2"> </th>
	    </tr>
	    </thead>
	    <tbody>
	    <?php
		$total=0; $deficit=0;
		if($nr_facturaf=$qry_facturaf->num_rows > 0){
	    	while($rs_facturaf=$qry_facturaf->fetch_array()){ ?>
	    <tr title="<?php echo $rs_facturaf['TX_user_seudonimo']; ?>">
	    	<td><?php echo $rs_facturaf[1]; ?></td>
	    	<td><?php echo $rs_facturaf[2]; ?></td>
	    	<td><?php echo $rs_facturaf[3]; ?></td>
	      <td><?php echo number_format($rs_facturaf[5],2); ?></td>
	      <td><?php echo number_format($rs_facturaf[6],2); ?></td>
	      <td>
					<button type="button" id="btn_print_ff" name="<?php echo "print_client_facturaf.php?a=".$rs_facturaf[0]; ?>" class="btn btn-info btn-xs" onclick="print_html(this.name);"><strong><i class="fa fa-print fa_print" aria-hidden="true"></i></strong></button>
	<?php 	if($rs_facturaf['TX_facturaf_deficit'] != '0'){ ?>
					&nbsp;&nbsp;
							<button type="button" id="btn_opennewdebit" name="<?php echo $rs_facturaf['facturaf_AI_cliente_id']; ?>" class="btn btn-success btn-xs" onclick="open_newdebit(this.name);"><strong><i class="fa fa-money fa_print" aria-hidden="true"></i></strong></button>
	<?php		}	?>
					&nbsp;&nbsp;
					<button type="button" id="btn_makenc" name="<?php echo $rs_facturaf['AI_facturaf_id']; ?>" class="btn btn-warning btn-xs" onclick="popup_make_nc(this.name);">N.C.</button>
				</td>
	    </tr>
	    <?php $total+=$rs_facturaf[5]; $deficit+=$rs_facturaf[6];
			}
		}else{?>
	    <tr>
	    	<td colspan="5"> </td>
	    </tr> <?php
		} ?>
	    </tbody>
	    <tfoot class="bg-primary">
	    <tr><td colspan="4"></td>
	    	<td><?php echo number_format($total,2); ?></td>
	        <td><?php echo number_format($deficit,2); ?></td>
	        <td></td>
	    </tr></tfoot>
	    </table>
	</div>
	<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<button type="button" id="btn_print_allff" class="btn btn-info">Imprimir</button>
	</div>
</div>

<div id="container_nc" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
<?php
$date_i=date('d-m-Y',strtotime('-1 week'));
$date_f=date('d-m-Y');
?>
<div id="container_filternc" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div id="container_txtdatenci" class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
        <label class="label label_blue_sky" for="txt_datenc_i">Fecha Inicio:</label>
        <input type="text" class="form-control" id="txt_datenc_i" name="txt_datenc_i" readonly="readonly" value="<?php echo $date_i; ?>" />
    </div>
    <div id="container_txtdatencf" class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
        <label class="label label_blue_sky" for="txt_datenc_f">Fecha Final:</label>
        <input type="text" class="form-control" id="txt_datenc_f" name="txt_datenc_f" readonly="readonly" value="<?php echo $date_f; ?>" />
    </div>
    <div id="container_btnfilter" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <button type="button" id="btn_filter_nc" class="btn btn-success"><i class="fa fa-search" aria-hidden="true"></i>
</button>
    </div>
</div>
<div id="container_tblcreditnote" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<?php
$date_i=date('Y-m-d',strtotime($date_i));
$date_f=date('Y-m-d',strtotime($date_f));
$txt_nc="SELECT bh_facturaf.TX_facturaf_numero, bh_notadecredito.TX_notadecredito_destino, (bh_notadecredito.TX_notadecredito_monto+bh_notadecredito.TX_notadecredito_impuesto), bh_notadecredito.TX_notadecredito_exedente, bh_notadecredito.AI_notadecredito_id, bh_user.TX_user_seudonimo
FROM ((bh_notadecredito
INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_notadecredito.notadecredito_AI_facturaf_id)
INNER JOIN bh_user ON bh_user.AI_user_id = bh_notadecredito.notadecredito_AI_user_id)
WHERE bh_notadecredito.notadecredito_AI_cliente_id = '$client_id' AND bh_notadecredito.TX_notadecredito_fecha >= '$date_i' AND bh_notadecredito.TX_notadecredito_fecha <= '$date_f'";
$qry_nc=$link->query($txt_nc);
?>
	<table id="tbl_creditnote" class="table table-bordered table-condensed table-striped">
    <caption class="caption">Notas de Cr&eacute;dito</caption>
    <thead class="bg-info">
    <tr>
    	<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Factura</th>
      <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Destino</th>
      <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Total</th>
      <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Saldo</th>
      <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">&nbsp;</th>
    </tr>
    </thead>
    <tbody>
<?php
	$total_nc=0;$total_saldo=0;
	if($nr_nc=$qry_nc->num_rows > 0){
		while($rs_nc=$qry_nc->fetch_array()){
			if($rs_nc[1] == 'SALDO'){
				$total_nc+=$rs_nc[2];
			}
			$total_saldo+=$rs_nc[3];
?>
    <tr title="<?php echo $rs_nc['TX_user_seudonimo']; ?>">
    	<td><?php echo $rs_nc[0]; ?></td>
      <td><?php echo $rs_nc[1]; ?></td>
      <td><?php echo number_format($rs_nc[2],2); ?></td>
      <td><?php echo number_format($rs_nc[3],2); ?></td>
      <td>
				<button type="button" name="<?php echo "print_client_nc.php?a=".$rs_nc[4]; ?>" class="btn btn-info btn-xs" onclick="print_html(this.name);"><strong><i class="fa fa-print fa_print" aria-hidden="true"></i></strong></button>&nbsp;
		</td>
    </tr>
<?php
		}
	}else{
?>
    <tr>
    	<td> </td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td>&nbsp;</td>
    </tr>
<?php } ?>
    </tbody>
    <tfoot class="bg-info">
    <tr>
    	<td></td>
        <td></td>
        <td><?php echo number_format($total_nc,2); ?></td>
        <td><?php echo number_format($total_saldo,2); ?></td>
        <td></td>
    </tr>
    </tfoot>
    </table>
</div>
<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<button type="button" id="btn_print_allnc" class="btn btn-info">Imprimir</button>
</div>
</div>

<div id="container_payment" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
<?php
$date_i=date('d-m-Y',strtotime('-1 week'));
$date_f=date('d-m-Y');
?>
<div id="container_filternc" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div id="container_txtdatenci" class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
        <label class="label label_blue_sky" for="txt_datenc_i">Fecha Inicio:</label>
        <input type="text" class="form-control" id="txt_datepay_i" name="txt_datepay_i" readonly="readonly" value="<?php echo $date_i; ?>" />
    </div>
    <div id="container_txtdatencf" class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
        <label class="label label_blue_sky" for="txt_datenc_f">Fecha Final:</label>
        <input type="text" class="form-control" id="txt_datepay_f" name="txt_datepay_f" readonly="readonly" value="<?php echo $date_f; ?>" />
    </div>
    <div id="container_btnfilter" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <button type="button" id="btn_filter_pay" class="btn btn-success"><i class="fa fa-search" aria-hidden="true"></i>
</button>
    </div>
</div>
<div id="container_tblpayment" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<?php
$date_i=date('Y-m-d',strtotime($date_i));
$date_f=date('Y-m-d',strtotime($date_f));
$txt_payment="SELECT bh_notadebito.TX_notadebito_fecha, bh_notadebito.TX_notadebito_total, bh_notadebito.AI_notadebito_id, bh_user.TX_user_seudonimo
FROM (bh_notadebito INNER JOIN bh_user ON bh_user.AI_user_id = bh_notadebito.notadebito_AI_user_id)
WHERE bh_notadebito.notadebito_AI_cliente_id = '$client_id' AND bh_notadebito.TX_notadebito_fecha >= '$date_i' AND bh_notadebito.TX_notadebito_fecha <= '$date_f'";
$qry_payment=$link->query($txt_payment)or die($link->error);
?>

<table id="tbl_notadebito" class="table table-bordered table-condensed table-striped">
    <caption class="caption">Debitos y Abonos</caption>
    <thead class="bg_green">
    <tr>
    	<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Fecha</th>
        <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Factura</th>
        <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Total</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2"> </th>
    </tr>
    </thead>
    <tbody>
<?php
$total_payment=0;
if($nr_payment=$qry_payment->num_rows > 0){
	while($rs_payment=$qry_payment->fetch_array()){
	$total_payment+=$rs_payment[1];
	$qry_ff=$link->query("SELECT TX_facturaf_numero
	FROM ((bh_notadebito
INNER JOIN rel_facturaf_notadebito ON bh_notadebito.AI_notadebito_id = rel_facturaf_notadebito.rel_AI_notadebito_id)
INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = rel_facturaf_notadebito.rel_AI_facturaf_id) WHERE AI_notadebito_id = '{$rs_payment[2]}'
")or die($link->error);
	$ff="";
	while($rs_ff=$qry_ff->fetch_array()){	$ff .=	$rs_ff[0]."<br/>";	}
?>
    <tr title="<?php echo $rs_payment['TX_user_seudonimo']; ?>">
    	<td><?php echo date('d-m-Y',strtotime($rs_payment[0])); ?></td>
      <td><?php echo $ff; ?></td>
      <td><?php echo number_format($rs_payment[1],2); ?></td>
      <td><button type="button" id="btn_print_payment" name="<?php echo "print_client_debito.php?a=".$rs_payment[2]; ?>" class="btn btn-info btn-xs" onclick="print_html(this.name);"><strong><i class="fa fa-print fa_print" aria-hidden="true"></i></strong></button></td>
    </tr>
<?php
	}
}else{
?>
    <tr>
    	<td></td>
        <td></td>
        <td>&nbsp;</td>
    </tr>
<?php } ?>
    </tbody>
    <tfoot class="bg_green">
    <tr>
    	<td> </td>
        <td> </td>
        <td><?php echo number_format($total_payment,2); ?> </td>
        <td> </td>
    </tr>
    </tfoot>
    </table>
</div>
<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<button type="button" id="btn_print_allpayment" class="btn btn-info">Imprimir</button>
</div>
</div>

</form>
</div>


<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
&copy; Derechos Reservados a: Trilli, S.A. 2017
	</div>
</div>
</div>

</body>
</html>
