<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_paydesk.php';

$fecha_actual = date('Y-m-d');
$qry_cashmovement = $link->query("SELECT bh_efectivo.TX_efectivo_fecha, bh_efectivo.TX_efectivo_tipo, bh_efectivo.TX_efectivo_motivo, bh_efectivo.TX_efectivo_monto, bh_user.TX_user_seudonimo, bh_efectivo.AI_efectivo_id, bh_efectivo.efectivo_AI_arqueo_id
FROM (bh_efectivo
INNER JOIN bh_user ON bh_efectivo.efectivo_AI_user_id = bh_user.AI_user_id)
WHERE TX_efectivo_fecha = '$fecha_actual'");
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
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />
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

$("#txt_motivo").keyup(function(){
	this.value = this.value.toUpperCase();
});
$('#btn_acept').click(function(){
	var ans = val_intwdec($("#txt_monto").val());
	if(!ans){
		$("#txt_monto").focus();
		return false;
	}
	if($("#txt_motivo").val() == ""){
		$("#txt_motivo").focus();
		return false;
	}
	var res = confirm("¿Desea Continuar?");
	if(!res){
		return false;
	}
	plus_cashmovement();
})
$('#btn_cancel').click(function(){
	self.close();
})

$('#txt_monto').validCampoFranz('0123456789.');
$('#txt_motivo').validCampoFranz('0123456789 abcdefghijklmnopqrstuvwxyz-/*+,.()');

$('#txt_monto').on("blur",function(){
	this.value = val_intw2dec(this.value);
});

$("#btn_clear_datei").on("click",function(){
	$("#txt_fecha").val("");
});

$( function() {
	$("#txt_fecha").datepicker({
		changeMonth: true,
		changeYear: true
	});
});

$("#txt_fecha").change(function(){
	filter_cashmovement(this.value);
});

$("#btn_print_all").on("click",function(){
	var fecha = $("#txt_fecha").val();
	print_html('print_all_cashmovement.php?a='+fecha);
});

});

var del_cashmovement = function(efectivo_id){
	var ans = confirm("¿Confirma anular esta salida?");
		if(!ans){ return false; }
	$.ajax({ data: {"a" : efectivo_id}, type: "GET", dataType: "text", url: "attached/get/del_cashmovement.php",	})
	.done(function( data, textStatus, jqXHR ) {
		self.close();
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});

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

<div id="container_seltipo" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
	<label for="sel_tipo">Tipo:</label>
	<select id="sel_tipo" name="sel_tipo" class="form-control">
  	<option value="SALIDA" selected="selected">Salida</option>
	  <option value="ENTRADA">Entrada</option>
  </select>
</div>
<div id="container_txtmotivo" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
	<label for="txt_motivo">Motivo:</label>
    <input type="text" name="txt_motivo" id="txt_motivo" class="form-control" onkeyup="chk_motivo(this)" />
</div>
<div id="container_txtmonto" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
	<label for="txt_monto">Monto:</label>
    <input type="text" name="txt_monto" id="txt_monto" class="form-control" onkeyup="chk_monto(this)" />
</div>
<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<button type="button" id="btn_acept" name="btn_acept" class="btn btn-success">Aceptar</button>
	&nbsp;&nbsp;
    <button type="button" id="btn_cancel" name="btn_cancel" class="btn btn-warning">Cancelar</button>
</div>
<div id="container_filter_cashmovement" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="container_txtfecha" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	<label for="txt_fecha">Fecha:</label>
    <button type="button" id="btn_clear_datei" class="btn btn-danger btn-xs"><strong>!</strong></button>
    <input type="text" id="txt_fecha" name="txt_fecha" class="form-control" value="<?php echo date('d-m-Y'); ?>" readonly="readonly" />
</div>
</div>
<div id="container_cashmovement" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<table id="tbl_cashmovement" class="table table-bordered table-condensed table-striped">
    <caption class="caption">Movimientos del: <?php echo date('d-m-Y'); ?></caption>
    <thead class="bg-primary">
    <tr>
      <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Tipo</th>
      <th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Motivo</th>
      <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Monto</th>
      <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3"></th>
    </tr>
    </thead>
    <tfoot class="bg-primary"><tr><td></td><td></td><td></td><td></td></tr></tfoot>
    <tbody>
<?php 	while($rs_cashmovement = $qry_cashmovement->fetch_array(MYSQLI_ASSOC)){ ?>
			<tr title="<?php echo $rs_cashmovement['TX_user_seudonimo']; ?>">
				<td><?php echo $rs_cashmovement['TX_efectivo_tipo']; ?></td>
				<td><?php echo $rs_cashmovement['TX_efectivo_motivo']; ?></td>
				<td><?php echo substr($rs_cashmovement['TX_efectivo_monto'],0,20); ?></td>
				<td>
					<button type="button" class="btn btn-info btn-xs" name="<?php echo $rs_cashmovement['AI_efectivo_id'] ?>" onclick="print_html('print_cashmovement.php?a='+this.name)" ><i class="fa fa-print fa-2x" aria-hidden="true"></i></button>
					&nbsp;
<?php if ($rs_cashmovement['efectivo_AI_arqueo_id'] === '0') { ?>
					<button type="button" class="btn btn-warning btn-xs" name="<?php echo $rs_cashmovement['AI_efectivo_id'] ?>" onclick="open_popup('popup_updcashmovement.php?a=<?php echo $rs_cashmovement['AI_efectivo_id'] ?>','_popup',420, 420)" ><i class="fa fa-wrench fa-2x" aria-hidden="true"></i></button>
<?php } else {?>
					<button type="button" class="btn btn-warning btn-xs" disabled><i class="fa fa-wrench fa-2x" aria-hidden="true"></i></button>
<?php } ?>
					&nbsp;
<?php 		if ($rs_cashmovement['efectivo_AI_arqueo_id'] === '0' && $rs_cashmovement['TX_efectivo_tipo'] === 'SALIDA') { ?>
					<button type="button" class="btn btn-danger btn-xs" name="<?php echo $rs_cashmovement['AI_efectivo_id'] ?>" onclick="del_cashmovement(<?php echo $rs_cashmovement['AI_efectivo_id'] ?>)" ><i class="fa fa-times fa-2x" aria-hidden="true"></i></button>
<?php 		} else {?>
					<button type="button" class="btn btn-danger btn-xs" disabled><i class="fa fa-times fa-2x" aria-hidden="true"></i></button>
<?php 		} ?>
				</td>
			</tr>
<?php } ?>
    </tbody>
    </table>
</div>
<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	        <button type="button" id="btn_print_all" class="btn btn-info" >Imprimir</button>
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
