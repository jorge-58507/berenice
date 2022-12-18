<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_paydesk.php';
?>
<?php
$client_id=$_GET['a'];
$vendor_id=$_GET['b'];
$qry_facturaventa=$link->query("SELECT bh_facturaventa.AI_facturaventa_id, bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.facturaventa_AI_cliente_id, bh_facturaventa.facturaventa_AI_user_id, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status, bh_cliente.TX_cliente_nombre, bh_user.TX_user_seudonimo FROM ((bh_facturaventa
INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
INNER JOIN bh_user ON bh_facturaventa.facturaventa_AI_user_id = bh_user.AI_user_id)
WHERE bh_facturaventa.facturaventa_AI_user_id = '$vendor_id' AND bh_facturaventa.facturaventa_AI_cliente_id = '$client_id' AND bh_facturaventa.TX_facturaventa_status = 'ACTIVA' OR bh_facturaventa.facturaventa_AI_user_id = '$vendor_id' AND bh_facturaventa.facturaventa_AI_cliente_id = '$client_id' AND bh_facturaventa.TX_facturaventa_status = 'FACTURADA' ORDER BY TX_facturaventa_fecha DESC, AI_facturaventa_id DESC");
$nr_facturaventa = $qry_facturaventa->num_rows;
if($nr_facturaventa < 1){
	$jscript = "<script type='text/javascript'>self.close();</script>";
	echo $jscript;
}else{
	$rs_facturaventa=$qry_facturaventa->fetch_array();
}

$qry_numeroff=$link->query("SELECT TX_facturaf_numero, TX_facturaf_status FROM bh_facturaf ORDER BY TX_facturaf_numero DESC");
$row_numeroff=$qry_numeroff->fetch_array();

if($row_numeroff[1] == 'INACTIVA'){
	$numero_ff = $row_numeroff[0];
}else{
	$pre_numero_ff="00000000".($row_numeroff[0]+1);
	$numero_ff=substr($pre_numero_ff,-8);
}
unset($_SESSION['numero_ff']);
$_SESSION['numero_ff'] = $numero_ff;

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
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript" src="attached/js/addprovider_funct.js"></script>
<script type="text/javascript">

var raw_cb_selected = [];
var raw_print = [];

$(document).ready(function() {

$("#btn_collect").click(function(){
	send_collect();
});
$("#btn_cancel").click(function(){
	clean_session('numero_ff');
	self.close();
});

$("#txt_filternewcollect").focus();
$("#txt_filternewcollect").keyup(function(e){
	if(e.which == 13){
		$("#tbl_bill tbody tr:first").dblclick();
		$("#btn_collect").click();
	}
});
$("#txt_filternewcollect").keyup(function(){
	$.ajax({	data: {"a" : this.value, "b" : $("#txt_date").val(), "c" : <?php echo $client_id ?>, "d" : raw_cb_selected, "e" : <?php echo $vendor_id ?>},	type: "GET",	dataType: "text",	url: "attached/get/filter_popupnewcollect.php",	})
	.done(function( data, textStatus, jqXHR ) {
			$("#tbl_bill tbody").html(data);
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log( "BAD" +  textStatus);
	});
});
$( function() {
	$("#txt_date").datepicker({
		changeMonth: true,
		changeYear: true
	});
});


});

	function pick_one(fact_id,quote_number,quote_amount){
		var ans = raw_cb_selected.includes( fact_id );
		if(!ans){
			add_raw_selected(fact_id,quote_number,quote_amount);
		}else{
			remove_raw_selected(fact_id);
		}
	}
	function add_raw_selected(fact_id,quote_number,quote_amount){
		raw_cb_selected.push(fact_id);
		$("#tr_"+fact_id).addClass("tbl_primary_hovered");
    // DISPLAY
		raw_print.push([fact_id, {data : {"number" : quote_number, "amount" : quote_amount}}] );
		var new_quote = ''; var quote_total = 0;
		for (var i in raw_print) {
			if (raw_print.hasOwnProperty(i)) {
				new_quote += `
				<div id="${raw_print[i][0]}" class="col-xs-3 col-sm-2 col-md-1 col-lg-1 no_padding bt_1">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding al_center bb_1 bg-info font_bolder">
						#${raw_print[i][1]['data']['number']}
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding al_center">
						B/ ${parseFloat(raw_print[i][1]['data']['amount']).toFixed(2)}
					</div>
				</div>
				`;
				quote_total += parseFloat(raw_print[i][1]['data']['amount']);
			}
		}
			$("#container_quote_selected").html(new_quote);
			$("#span_total").html(quote_total.toFixed(2));
	}
	function remove_raw_selected(fact_id){
		var index = raw_cb_selected.indexOf(fact_id.toString());
		raw_cb_selected.splice(index,1);
		raw_print.splice(index,1);
		var new_quote = ''; var quote_total = 0;
		for (var i in raw_print) {
			if (raw_print.hasOwnProperty(i)) {
				new_quote += `
				<div id="${raw_print[i][0]}" class="col-xs-3 col-sm-2 col-md-1 col-lg-1 no_padding bt_1">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding al_center bb_1 bg-info font_bolder">
						#${raw_print[i][1]['data']['number']}
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding al_center">
						B/ ${parseFloat(raw_print[i][1]['data']['amount']).toFixed(2)}
					</div>
				</div>
				`;
				quote_total += parseFloat(raw_print[i][1]['data']['amount']);
			}
		}

		$("#tr_"+fact_id).removeClass("tbl_primary_hovered");
		$("#container_quote_selected").html(new_quote);
		$("#span_total").html(quote_total.toFixed(2));
	}
	function send_collect(){
		if(raw_cb_selected.length === 0){
			$("#tbl_bill tbody tr:first").dblclick();
		};
		window.opener.location="new_collect.php?a="+raw_cb_selected+"&b=<?php echo $client_id; ?>";
		self.close();
	}
</script>
</head>

<body>

<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-2" >
		<div id="logo" ></div>
	</div>
</div>
<div id="content-sidebar_popup" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<form id="form_popnewcollect" method="post">
	<div id="container_filternewcollect" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  	<div id="container_txtfilternewcollect"  class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
      <label class="label label_blue_sky" for="txt_filternewcollect">Buscar</label>
      <input type="text" id="txt_filternewcollect" class="form-control" placeholder="Numero o Total de la Factura..." />
    </div>
  	<div id="container_txtdate"  class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
      <label class="label label_blue_sky" for="txt_date">Fecha</label>
      <input type="text" id="txt_date" name="txt_date" class="form-control" readonly="readonly" />
    </div>
		<div id="container_txtdate"  class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
			<label class="label label-danger" for="">Total</label>
			<span id="span_total" class="form-control"></span>
		</div>
  </div>
	<div id="container_quote_selected" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 py_7">
	</div>
	<div id="container_tblbill" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<table class="table table-bordered" id="tbl_bill">
<caption>Facturas pendientes de: <?php echo $rs_facturaventa['TX_cliente_nombre']; ?></caption>
<thead class="bg-primary">
<tr>
  <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3 al_center">Nº</th>
  <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4 al_center">FECHA</th>
  <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3 al_center">TOTAL</th>
</tr>
</thead>
<tfoot class="bg-primary"><tr>	<td colspan="3"></td> </tr></tfoot>
<tbody>
<?php
	do{
?>
		<tr id="tr_<?php echo $rs_facturaventa['AI_facturaventa_id'];?>" title="<?php echo $rs_facturaventa['TX_user_seudonimo'];?>" ondblclick="pick_one('<?php echo $rs_facturaventa['AI_facturaventa_id'];?>','<?php echo $rs_facturaventa['TX_facturaventa_numero'];?>','<?php echo $rs_facturaventa['TX_facturaventa_total'];?>')">
		  <td class="al_center"><?php echo $rs_facturaventa['TX_facturaventa_numero']; ?></td>
		  <td class="al_center"><?php	echo $date=date('d-m-Y',strtotime($rs_facturaventa['TX_facturaventa_fecha'])); ?></td>
		  <td class="al_center">B/ <?php echo number_format($rs_facturaventa['TX_facturaventa_total'],2); ?></td>
		</tr>
<?php
	}while($rs_facturaventa=$qry_facturaventa->fetch_array());
?>
</tbody>
</table>
	</div>
    <div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<button type="button" id="btn_collect" class="btn btn-success" >Continuar</button>
&nbsp;&nbsp;
<button type="button" id="btn_cancel" class="btn btn-danger" >Cancelar</button>
    </div>
</form>
</div>


<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
&copy; Derechos Reservados a: Jorge Salda&nacute;a <?php echo date('Y'); ?>
	</div>
</div>
</div>

</body>
</html>
