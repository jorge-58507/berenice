<?php
require 'bh_conexion.php';
$link=conexion();

$cpp_id = $_GET['a'];

$qry_cheque = $link->query("SELECT AI_cheque_id, TX_cheque_numero, TX_cheque_monto, TX_cheque_status FROM (bh_cheque INNER JOIN bh_cpp ON bh_cpp.AI_cpp_id = bh_cheque.cheque_AI_cpp_id) WHERE bh_cpp.AI_cpp_id = '$cpp_id'")or die($link->error);
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

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript" src="attached/js/addprovider_funct.js"></script>
<script type="text/javascript">

$(document).ready(function() {

$('#btn_acept').click(function(){
	var ans = confirm("Se cerrara esta cuenta por pagar, ¿Desea Continuar?");
	if (!ans) {	return false;	}
	$.ajax({	data: {"a" : <?php echo $cpp_id; ?>},	type: "GET",	dataType: "text",	url: "attached/get/upd_cpp_status.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 window.opener.$("#tbl_cpp tbody").html(data);
		 setTimeout('self.close()',300);
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
})
$('#btn_cancel').click(function(){
	self.close();
})

});
function upd_cheque_status(cheque_id, status){
	$.ajax({	data: {"a" : cheque_id, "b" : status },	type: "GET",	dataType: "text",	url: "attached/get/upd_cheque_status.php", })
	 .done(function( data, textStatus, jqXHR ) { console.log("GOOD"+textStatus);
		 	$("#tbl_cheque tbody").html(data);
		})
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

</div>

<div id="content-sidebar_popup" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<form method="post" name="form_addprovider">

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<table id="tbl_cheque" class="table table-bordered table-condensed table-striped">
		<caption>Cheques Relacionados</caption>
		<thead class="bg_green">
			<tr>
				<th class="al_center">NUMERO</th>
				<th class="al_center">MONTO</th>
				<th class="al_center">STATUS</th>
				<th class="al_center"></th>
				<th class="al_center"></th>
			</tr>
		</thead>
		<tfoot class="bg_green">
			<tr><td colspan="5"></td></tr>
		</tfoot>
		<tbody>
<?php 	while($rs_cheque = $qry_cheque->fetch_array()){ ?>
			<tr>
				<td><?php echo $rs_cheque['TX_cheque_numero']; ?></td>
				<td><?php echo number_format($rs_cheque['TX_cheque_monto'],2); ?></td>
				<td><?php echo $rs_cheque['TX_cheque_status']; ?></td>
				<td class="al_center"><button type="button" class="btn btn-primary" onclick="upd_cheque_status(<?php echo $rs_cheque['AI_cheque_id']; ?>,'ENTREGADO')">Entregado</button></td>
				<td class="al_center"><button type="button" class="btn btn-info" onclick="upd_cheque_status(<?php echo $rs_cheque['AI_cheque_id']; ?>,'DEPOSITADO')">Depositado</button></td>
			</tr>
<?php } ?>
		</tbody>
	</table>
</div>
<div id="container_button" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<button type="button" id="btn_acept" class="btn btn-success">Aceptar</button>
&nbsp;
<button type="button" id="btn_cancel" class="btn btn-warning">Cancelar</button>
</div>

</form>
</div>


<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
&copy; Derechos Reservados a: Trilli, S.A. 2017
	</div>
</div>
</div>

</body>
</html>
