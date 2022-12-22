<?php
require 'bh_conexion.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login_admin.php';
?>
<?php
$client_id=$_GET['a'];
$qry_facturaf=$link->query("SELECT bh_facturaf.TX_facturaf_numero, bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_deficit, bh_cliente.TX_cliente_nombre
FROM (bh_facturaf
INNER JOIN bh_cliente ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id)
WHERE bh_facturaf.facturaf_AI_cliente_id = '$client_id' AND bh_facturaf.TX_facturaf_deficit > '0' ORDER BY AI_facturaf_id DESC");

$nr_facturaf = $qry_facturaf->num_rows;
if($nr_facturaf < 1){
	$jscript = "<script type='text/javascript'>self.close();</script>";
	echo $jscript;
}else{
	$rs_facturaf=$qry_facturaf->fetch_array();
}

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
<script type="text/javascript" src="attached/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript" src="attached/js/addprovider_funct.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	var length_facturaf = <?php echo $nr_facturaf; ?>;

$("#btn_pickall").click(function(){
	for(it=0;it<length_facturaf;it++){
		$('input:checkbox[name=cb_bill'+it+']').prop("checked", true);
	}
});
$("#btn_collect").click(function(){
	send_collect();
});
$("#btn_cancell").click(function(){
	clean_session('numero_ff');
	self.close();
});

$("#txt_filternewdebit").focus();
$("#txt_filternewdebit").keyup(function(e){
	if(e.which == 13){
		$("#tbl_bill tbody tr:first").dblclick();
		$("#btn_collect").click();
	}else{
		filter_popupnewdebit(this.value,'<?php echo $client_id ?>');
	}
});
$( function() {
	$("#txt_date").datepicker({
		changeMonth: true,
		changeYear: true
	});
});

});

var raw_cb_selected = [];

function pick_one(fact_id){
	var ans = raw_cb_selected.includes( fact_id );
	if(!ans){
		add_raw_selected(fact_id);
	}else{
		remove_raw_selected(fact_id);
	}
}
function add_raw_selected(fact_id){
	raw_cb_selected.push(fact_id);
	$("#tr_"+fact_id).addClass("tbl_primary_hovered");
	console.log(raw_cb_selected);
}
function remove_raw_selected(fact_id){
	var index = raw_cb_selected.indexOf(fact_id.toString());
	raw_cb_selected.splice(index,1);
	$("#tr_"+fact_id).removeClass("tbl_primary_hovered");
	console.log(raw_cb_selected);
}
function send_collect(){
	if(raw_cb_selected.length === 0){
		$("#tbl_bill tbody tr:first").dblclick();
	};
	window.opener.location="new_debit.php?a="+raw_cb_selected+"&b=<?php echo $client_id; ?>";
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
<form action="" method="post" name="form_login"  id="form_login">
	<div id="container_filternewcollect" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<div id="container_txtfilternewdebit"  class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <label for="txt_filternewdebit">Buscar</label>
            <input type="text" id="txt_filternewdebit" class="form-control"  />
        </div>
    	<div id="container_txtdate"  class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <label for="txt_date">Fecha
            <button type="button" id="clear_date_initial" class="btn btn-danger btn-xs" onclick="setEmpty('txt_date')"><strong>!</strong></button>
            </label>
            <input type="text" id="txt_date" name="txt_date" class="form-control" readonly="readonly" />
        </div>
    </div>
	<div id="container_tblbill" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<table class="table table-bordered" id="tbl_bill">
<caption>Creditos pendientes de: <?php echo $rs_facturaf['TX_cliente_nombre']; ?></caption>
<thead class="bg-primary">
<tr>
    <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Nº</th>
    <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Fecha</th>
    <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Total</th>
</tr>
</thead>
<tfoot></tfoot>
<tbody>
<?php do{	?>
<tr id="tr_<?php echo $rs_facturaf['AI_facturaf_id'];?>" ondblclick="pick_one('<?php echo $rs_facturaf['AI_facturaf_id'];?>')">
    <td><?php echo $rs_facturaf['TX_facturaf_numero']; ?></td>
    <td><?php
		$time=strtotime($rs_facturaf['TX_facturaf_fecha']);
		echo $date=date('d-m-Y',$time);
		?></td>
    <td>B/ <?php echo number_format($rs_facturaf['TX_facturaf_deficit'],2); ?></td>
</tr>
<?php }while($rs_facturaf=$qry_facturaf->fetch_array()); ?>
</tbody>
</table>
	</div>
    <div id="container_btncollect" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<button type="button" id="btn_collect" class="btn btn-success" >Continuar</button>
&nbsp;&nbsp;
<button type="button" id="btn_cancell" class="btn btn-danger" >Cancelar</button>
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
