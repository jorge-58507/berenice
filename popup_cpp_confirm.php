<?php
require 'bh_conexion.php';
$link=conexion();

$cpp_id = $_GET['a'];

$qry_proveedor = $link->query("SELECT AI_proveedor_id FROM (bh_proveedor INNER JOIN bh_cpp ON bh_cpp.cpp_AI_proveedor_id = bh_proveedor.AI_proveedor_id) WHERE AI_cpp_id = '$cpp_id'")or die($link->error);
$rs_proveedor=$qry_proveedor->fetch_array();

$qry_cheque = $link->query("SELECT AI_cheque_id, TX_cheque_numero, TX_cheque_monto, TX_cheque_status
	FROM (bh_cheque
		INNER JOIN bh_cpp ON bh_cpp.AI_cpp_id = bh_cheque.cheque_AI_cpp_id)
		WHERE bh_cpp.AI_cpp_id = '$cpp_id'")or die($link->error);
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
	 		data = JSON.parse(data);
			var content_cheque = '';
			for (var x in data[0]) {
				var btn_entregado = (data[0][x]['TX_cheque_status'] === 'ALMACENADO' || data[0][x]['TX_cheque_status'] === 'DEPOSITADO') ?	'<button type="button" class="btn btn-primary btn-sm" onclick="upd_cheque_status('+data[0][x]['AI_cheque_id']+',\'ENTREGADO\')">Entregado</button>' : '';
				var btn_depositado = (data[0][x]['TX_cheque_status'] === 'ALMACENADO' || data[0][x]['TX_cheque_status'] === 'ENTREGADO') ?	'<button type="button" class="btn btn-info btn-sm" onclick="upd_cheque_status('+data[0][x]['AI_cheque_id']+',\'DEPOSITADO\')">Depositado</button>' : '';
				content_cheque = content_cheque+'<tr><td>'+data[0][x]['TX_cheque_numero']+'</td><td>'+data[0][x]['TX_cheque_monto']+'</td><td>'+data[0][x]['TX_cheque_status']+'</td><td>'+btn_entregado+'</td><td>'+btn_depositado+'</td><td><button type="button" class="btn btn-danger btn-sm" onclick="upd_cheque_unassign('+data[0][x]['AI_cheque_id']+')"><i class="fa fa-times"></i></button></td></tr>';
				$("#tbl_cheque tbody").html(content_cheque);
			}
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}
function upd_cheque_unassigned_status(cheque_id, status, cpp_id){
	$.ajax({	data: {"a" : cheque_id, "b" : status, "c" : cpp_id },	type: "GET",	dataType: "text",	url: "attached/get/upd_cheque_unassigned_status.php", })
	 .done(function( data, textStatus, jqXHR ) { console.log("GOOD"+textStatus);
		 data = JSON.parse(data);
		 var content_cheque = '';
		 for (var x in data[0]) {
			 var btn_entregado = (data[0][x]['TX_cheque_status'] === 'ALMACENADO' || data[0][x]['TX_cheque_status'] === 'DEPOSITADO') ?	'<button type="button" class="btn btn-primary btn-sm" onclick="upd_cheque_status('+data[0][x]['AI_cheque_id']+',\'ENTREGADO\')">Entregado</button>' : '';
			 var btn_depositado = (data[0][x]['TX_cheque_status'] === 'ALMACENADO' || data[0][x]['TX_cheque_status'] === 'ENTREGADO') ?	'<button type="button" class="btn btn-info btn-sm" onclick="upd_cheque_status('+data[0][x]['AI_cheque_id']+',\'DEPOSITADO\')">Depositado</button>' : '';
			 content_cheque = content_cheque+'<tr><td>'+data[0][x]['TX_cheque_numero']+'</td><td>'+data[0][x]['TX_cheque_monto']+'</td><td>'+data[0][x]['TX_cheque_status']+'</td><td>'+btn_entregado+'</td><td>'+btn_depositado+'</td><td><button type="button" class="btn btn-danger btn-sm" onclick="upd_cheque_unassign('+data[0][x]['AI_cheque_id']+')"><i class="fa fa-times"></i></button></td></tr>';
		 }
		 $("#tbl_cheque tbody").html(content_cheque);

		 var content_cheque_proveedor = '';
		 for (var x in data[1]) {
			 var btn_entregado = (data[1][x]['TX_cheque_status'] === 'ALMACENADO') ?	'<button type="button" class="btn btn-primary btn-sm" onclick="upd_cheque_unassigned_status('+data[1][x]['AI_cheque_id']+',\'ENTREGADO\','+cpp_id+')">Entregado</button>' : '';
			 var btn_depositado = (data[1][x]['TX_cheque_status'] === 'ALMACENADO') ?	'<button type="button" class="btn btn-info btn-sm" onclick="upd_cheque_unassigned_status('+data[1][x]['AI_cheque_id']+',\'DEPOSITADO\','+cpp_id+')">Depositado</button>' : '';
			 content_cheque_proveedor = content_cheque_proveedor+'<tr><td>'+data[1][x]['TX_cheque_numero']+'</td><td>'+data[1][x]['TX_cheque_monto']+'</td><td>'+data[1][x]['TX_cheque_status']+'</td><td>'+btn_entregado+'</td><td>'+btn_depositado+'</td></tr>';
		 }
		 $("#tbl_cheque_proveedor tbody").html(content_cheque_proveedor);
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}

function upd_cheque_unassign(cheque_id){
	$.ajax({	data: {"a" : cheque_id },	type: "GET",	dataType: "text",	url: "attached/get/upd_cheque_unassign.php", })
	.done(function( data, textStatus, jqXHR ) { console.log("GOOD"+textStatus);
		data = JSON.parse(data);
		console.log("data_obj: "+data);
		var content_cheque = '';
		for (var x in data[0]) {
			var btn_entregado = (data[0][x]['TX_cheque_status'] === 'ALMACENADO' || data[0][x]['TX_cheque_status'] === 'DEPOSITADO') ?	'<button type="button" class="btn btn-primary btn-sm" onclick="upd_cheque_status('+data[0][x]['AI_cheque_id']+',\'ENTREGADO\')">Entregado</button>' : '';
			var btn_depositado = (data[0][x]['TX_cheque_status'] === 'ALMACENADO' || data[0][x]['TX_cheque_status'] === 'ENTREGADO') ?	'<button type="button" class="btn btn-info btn-sm" onclick="upd_cheque_status('+data[0][x]['AI_cheque_id']+',\'DEPOSITADO\')">Depositado</button>' : '';
			content_cheque = content_cheque+'<tr><td>'+data[0][x]['TX_cheque_numero']+'</td><td>'+data[0][x]['TX_cheque_monto']+'</td><td>'+data[0][x]['TX_cheque_status']+'</td><td>'+btn_entregado+'</td><td>'+btn_depositado+'</td><td><button type="button" class="btn btn-danger btn-sm" onclick="upd_cheque_unassign('+data[0][x]['AI_cheque_id']+')"><i class="fa fa-times"></i></button></td></tr>';
		}
		$("#tbl_cheque tbody").html(content_cheque);

		var content_cheque_proveedor = '';
		for (var x in data[1]) {
			var btn_entregado = (data[1][x]['TX_cheque_status'] === 'ALMACENADO') ?	'<button type="button" class="btn btn-primary btn-sm" onclick="upd_cheque_unassigned_status('+data[1][x]['AI_cheque_id']+',\'ENTREGADO\','+data[3]+')">Entregado</button>' : '';
			var btn_depositado = (data[1][x]['TX_cheque_status'] === 'ALMACENADO') ?	'<button type="button" class="btn btn-info btn-sm" onclick="upd_cheque_unassigned_status('+data[1][x]['AI_cheque_id']+',\'DEPOSITADO\','+data[3]+')">Depositado</button>' : '';
			content_cheque_proveedor = content_cheque_proveedor+'<tr><td>'+data[1][x]['TX_cheque_numero']+'</td><td>'+data[1][x]['TX_cheque_monto']+'</td><td>'+data[1][x]['TX_cheque_status']+'</td><td>'+btn_entregado+'</td><td>'+btn_depositado+'</td></tr>';
		}
		$("#tbl_cheque_proveedor tbody").html(content_cheque_proveedor);
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
		<caption>Cheques Relacionados a CPP</caption>
		<thead class="bg_green">
			<tr>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center">Numero</th>
				<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3 al_center">Monto</th>
				<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3 al_center">Status</th>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"></th>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"></th>
				<th></th>
			</tr>
		</thead>
		<tfoot class="bg_green">
			<tr><td colspan="6"></td></tr>
		</tfoot>
		<tbody>
<?php 	while($rs_cheque = $qry_cheque->fetch_array()){ ?>
			<tr>
				<td><?php echo $rs_cheque['TX_cheque_numero']; ?></td>
				<td><?php echo number_format($rs_cheque['TX_cheque_monto'],2); ?></td>
				<td><?php echo $rs_cheque['TX_cheque_status']; ?></td>
				<td class="al_center"><?php if($rs_cheque['TX_cheque_status'] != 'ALMACENADO' && $rs_cheque['TX_cheque_status'] != 'DEPOSITADO'){ echo ''; }else{?><button type="button" class="btn btn-primary btn-sm" onclick="upd_cheque_status(<?php echo $rs_cheque['AI_cheque_id']; ?>,'ENTREGADO')">Entregado</button><?php } ?></td>
				<td class="al_center"><?php if($rs_cheque['TX_cheque_status'] != 'ALMACENADO' && $rs_cheque['TX_cheque_status'] != 'ENTREGADO'){ echo ''; }else{?><button type="button" class="btn btn-info btn-sm" onclick="upd_cheque_status(<?php echo $rs_cheque['AI_cheque_id']; ?>,'DEPOSITADO')">Depositado</button><?php } ?></td>
				<td class="al_center"><button class="btn btn-danger btn-sm" onclick="upd_cheque_unassign(<?php echo $rs_cheque['AI_cheque_id']; ?>)"><i class="fa fa-times"></i></td>
			</tr>
<?php } ?>
		</tbody>
	</table>
</div>
<?php
$qry_cheque_proveedor = $link->query("SELECT AI_cheque_id, TX_cheque_numero, TX_cheque_monto, TX_cheque_status
	FROM (bh_cheque
		INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_cheque.cheque_AI_proveedor_id)
		WHERE bh_proveedor.AI_proveedor_id = '{$rs_proveedor['AI_proveedor_id']}' AND TX_cheque_status = 'ALMACENADO' AND cheque_AI_cpp_id = '0'")or die($link->error);
?>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<table id="tbl_cheque_proveedor" class="table table-bordered table-condensed table-striped">
		<caption>Cheques sin asignar</caption>
		<thead class="bg_red">
			<tr>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center">Numero</th>
				<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3 al_center">Monto</th>
				<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3 al_center">Status</th>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"></th>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"></th>
			</tr>
		</thead>
		<tfoot class="bg_red">
			<tr><td colspan="5"></td></tr>
		</tfoot>
		<tbody>
<?php 	while($rs_cheque_proveedor = $qry_cheque_proveedor->fetch_array()){ ?>
			<tr>
				<td><?php echo $rs_cheque_proveedor['TX_cheque_numero']; ?></td>
				<td><?php echo number_format($rs_cheque_proveedor['TX_cheque_monto'],2); ?></td>
				<td><?php echo $rs_cheque_proveedor['TX_cheque_status']; ?></td>
				<td class="al_center"><button type="button" class="btn btn-primary btn-sm" onclick="upd_cheque_unassigned_status(<?php echo $rs_cheque_proveedor['AI_cheque_id']; ?>,'ENTREGADO',<?php echo $cpp_id; ?>)">Entregado</button></td>
				<td class="al_center"><button type="button" class="btn btn-info btn-sm" onclick="upd_cheque_unassigned_status(<?php echo $rs_cheque_proveedor['AI_cheque_id']; ?>,'DEPOSITADO',<?php echo $cpp_id; ?>)">Depositado</button></td>
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
