<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_paydesk.php';
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
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/admin_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript">

$(document).ready(function() {

$('#txt_filter_creditnote').validCampoFranz('0123456789');
$("#btn_cancel").click(function(){
	self.close()
});

$("#txt_filter_creditnote").on("keyup", function(){
	$.ajax({	data: {"a" : this.value, "b" : $("#txt_datei").val(), "c" : $("#txt_datef").val() },	type: "GET",	dataType: "text",	url: "attached/get/filter_paydesk_creditnote.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 $("#tbl_paydesk_creditnote tbody").html(data);
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
})
$("#btn_search").on("click", function(){
	$("#txt_filter_creditnote").keyup();
})

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
	<div id="container_txtfiltercreditnote" class="col-xs-12 col-sm-8 col-md-5 col-lg-5">
  	<label for="txt_filter_creditnote" class="label label_blue_sky">Buscar</label>
		<input type="text" id="txt_filter_creditnote" value="" class="form-control" placeholder="Numero de Nota de Credito" autofocus>
  </div>
	<div id="container_txtdatei" class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
  	<label for="txt_datei" class="label label_blue_sky">Fecha Inicial</label>
		<input type="text" id="txt_datei" value="<?php echo date('d-m-Y',strtotime(date('Y-m-d',strtotime('-1 week')))) ?>" class="form-control" readonly>
  </div>
	<div id="container_txtdatef" class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
  	<label for="txt_datef" class="label label_blue_sky">Fecha Final</label>
		<input type="text" id="txt_datef" value="<?php echo date('d-m-Y') ?>" class="form-control" readonly>
  </div>
	<div class="col-xs-12 col-sm-6 col-md-2 col-lg-2 container_btn_md">
		<button type="button" id="btn_search" class="btn btn-success btn-md btn_squared_md" name="button"><i class="fa fa-search"></i></button>
	</div>
	<div id="container_tblcreditnote" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding:5px 15px;">
		<table id="tbl_paydesk_creditnote" class="table table-bordered table-condensed table-striped">
			<thead class="bg-primary">
				<tr>
					<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
					<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Numero</th>
					<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Factura</th>
					<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Cliente</th>
					<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Destino</th>
					<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Total</th>
					<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Saldo</th>
					<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="7"> </td>
				</tr>
			</tbody>
			<tfoot class="bg-primary">
				<tr><td colspan="8"> </td></tr>
			</tfoot>

		</table>
  </div>





  <div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
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
