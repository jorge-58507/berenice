<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login_admin.php';
?>
<?php
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
<link href="attached/css/gi_blocks.css" rel="stylesheet" type="text/css" />
<link href="attached/css/popup_css.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
<script type="text/javascript">

$(document).ready(function() {

$("#txt_nombre").validCampoFranz('abcdefghijklmnopqrstuvwxyz 0123456789');
$("#txt_alicuota").validCampoFranz('0123456789.');
var txt_nombre="";
var nombre_length=80;
$("#txt_nombre").on("keyup",function(){
	this.value =	this.value.toUpperCase();
	if(this.value.length >= nombre_length){
		this.value = txt_nombre;
	}else{
		return txt_nombre = this.value;
	}
})
$("#txt_alicuota").on("blur",function(){
	this.value=val_intw2dec(this.value);
})
$("#btn_back").click(function(){
	window.location.href = "popup_alicuota.php";
//	history.back(1);
});
$("#btn_save").click(function(){
	if($("#txt_nombre").val() == ""){
		$("#txt_nombre").focus();
		return false;
	}
	var ans = val_intwdec($("#txt_alicuota").val())
	if(!ans){ $("#txt_alicuota").focus(); return false; }
	$.ajax({	data: {"a" : $("#txt_nombre").val(),"b" : $("#txt_alicuota").val(),"c" : $("#sel_categoria").val()},	type: "GET",	dataType: "text",	url: "attached/get/plus_tax.php",	})
	.done(function( data, textStatus, jqXHR ) {
		console.log("GOOD" + textStatus);
		$("#container_tbltax").html(data);
		$("#txt_nombre,#txt_alicuota").val("");
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {	console.log( "BAD " +  textStatus);	});
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
<form method="post" name="form_createtax" action="">
<div id="container_title" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<h4>Crear Impuesto</h4>
</div>
<div id="container_taxe" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_txtnombre" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label for="txt_nombre">Titulo</label>
			<input type="text" id="txt_nombre" name="txt_nombre" class="form-control" />
	</div>
	<div id="container_txtalicuota" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			<label for="txt_alicuota">Alicuota</label>
			<input type="text" id="txt_alicuota" name="txt_alicuota" class="form-control" />
	</div>
	<div id="container_selcategoria" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			<label for="sel_categoria">Aplica</label>
			<select id="sel_categoria" class="form-control">
				<option value="GENERAL">General</option>
				<option value="ESPECIAL">Especial</option>
			</select>
	</div>
</div>
<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <button type="button" id="btn_save" class="btn btn-success">Crear</button>
    &nbsp;&nbsp;
    <button type="button" id="btn_back" class="btn btn-warning">Volver</button>
</div>
<div id="container_tbltax" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<?php $qry_taxes=mysql_query("SELECT AI_impuesto_id, TX_impuesto_nombre, TX_impuesto_value, TX_impuesto_categoria FROM bh_impuesto"); ?>
	<table id="tbl_tax" class="table table-bordered table-condensed table-striped">
	<thead class="bg-primary">
	<tr>
		<th></th><th></th><th></th><th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	while ($rs_taxes=mysql_fetch_array($qry_taxes)) {
	?>
		<tr>
			<td><?php echo $rs_taxes['1']; ?></td>
			<td><?php echo $rs_taxes['2']; ?></td>
			<td><?php echo $rs_taxes['3']; ?></td>
            <td>
	<button type="button" id="btn_del"  name="<?php echo $rs_taxes['0']; ?>" class="btn btn-danger btn-sm" onclick="del_tax(this.name);"><i class="fa fa-times" aria-hidden="true"></i></button>
            </td>
		</tr>
	<?php
	}
	?>
	</tbody>
	<tfoot class="bg-primary">
		<tr>
			<td></td><td></td><td></td><td></td>
		</tr>
	</tfoot>
	</table
></div>


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
