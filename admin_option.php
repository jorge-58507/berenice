<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_admin.php';

$qry_opcion=$link->query("SELECT AI_opcion_id, TX_opcion_titulo, TX_opcion_value FROM bh_opcion")or die($link->error);
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]['ID']=$rs_opcion['AI_opcion_id'];
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]['VALUE']=$rs_opcion['TX_opcion_value'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Trilli, S.A. - Todo en Materiales</title>
	<?php include 'attached/php/req_required.php'; ?>
	<link href="attached/css/admin_css.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="attached/js/admin_funct.js"></script>
	<script type="text/javascript">

		$(document).ready(function() {
			$("#btn_back").click(function(){	history.back(1);	});
			$("#btn_save").click(function(){
				var raw_form = $("form input[type=text]").toArray();
				var form_length = raw_form.length;
				var raw_value=new Object();
				for(i=0;i<form_length;i++){
				var id=$(raw_form[i]).attr("name");
					raw_value[id]=$(raw_form[i]).val();
				}

				$.ajax({	data: {"a" : raw_value},	type: "GET",	dataType: "json",	url: "attached/get/upd_option.php",	})
				.done(function( data, textStatus, jqXHR ) {
				})
				.fail(function( jqXHR, textStatus, errorThrown ) {	console.log( "BAD" +  textStatus);
				});
				 setTimeout(function(){ history.back(1); }, 300);
			});
			$("#txt_title").on("click",function(){
				if($(this).attr("readOnly")){
				var ans = confirm("¿Confirma modificar el campo?");
				if(ans){
					$(this).attr("readOnly",false);
				}
				}
			});
			$("#txt_ruc").on("click",function(){
				if($(this).attr("readOnly")){
					var ans = confirm("¿Confirma modificar el campo?");
					if(ans){
						$(this).attr("readOnly",false);
					}
				}
			});
			$("#txt_dv").on("click",function(){
				if($(this).attr("readOnly")){
				var ans = confirm("¿Confirma modificar el campo?");
					if(ans){
						$(this).attr("readOnly",false);
					}
				}
			});
			$("#txt_direction").on("click",function(){
				if($(this).attr("readOnly")){
				var ans = confirm("¿Confirma modificar el campo?");
					if(ans){
						$(this).attr("readOnly",false);
					}
				}
			});
			$("#txt_telephone").on("click",function(){
				if($(this).attr("readOnly")){
				var ans = confirm("¿Confirma modificar el campo?");
					if(ans){
						$(this).attr("readOnly",false);
					}
				}
			});
			$("#txt_fax").on("click",function(){
				if($(this).attr("readOnly")){
					var ans = confirm("¿Confirma modificar el campo?");
					if(ans){
						$(this).attr("readOnly",false);
					}
				}
			});
			$("#txt_email").on("click",function(){
				if($(this).attr("readOnly")){
					var ans = confirm("¿Confirma modificar el campo?");
					if(ans){
						$(this).attr("readOnly",false);
					}
				}
			});
			$("#span_impuesto").on("click",function(){
				open_popup("popup_alicuota.php","popup","500","425");
			});
		});


	</script>
</head>
<body>
<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div id="logo_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-2" >
  	<div id="logo" ></div>
  </div>

	<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-10">
    	<div id="container_username" class="col-lg-4 visible-lg">
        Bienvenido: <label class="bg-primary">
         <?php echo $rs_checklogin['TX_user_seudonimo']; ?>
        </label>
        </div>
		<div id="navigation" class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
<?php
switch ($_COOKIE['coo_tuser']){
	case '1':
		include 'attached/php/nav_master.php';
	break;
	case '2':
		include 'attached/php/nav_admin.php';
	break;
	case '3':
		include 'attached/php/nav_sale.php';
	break;
	case '4':
		include 'attached/php/nav_paydesk.php';
	break;
	case '5':
		include 'attached/php/nav_stock.php';
	break;
}
?>
		</div>
	</div>

</div>

<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<form action="" method="post" name="form_option"  id="form_option">
<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
</div>
<div id="container_tbloption" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
<table id="tbl_option" class="table table-bordered table-condensed table-hover table-striped">
<caption class="caption">Opciones</caption>
<thead class="bg-primary">
<tr>
	<th>Opci&oacute;n</th>
    <th>Contenido</th>
</tr>
</thead>
<tbody>
<tr>
	<td>Titulo</td>
<td><input type="text" id="txt_title" name="<?php echo $raw_opcion['TITULO']['ID']; ?>" class="form-control" value="<?php echo $raw_opcion['TITULO']['VALUE']; ?>" readonly="readonly"/></td>
</tr>
<tr>
	<td>RUC</td>
<td><input type="text" id="txt_ruc" name="<?php echo $raw_opcion['RUC']['ID']; ?>"class="form-control" value="<?php echo $raw_opcion['RUC']['VALUE']; ?>" readonly="readonly"/></td>
</tr>
<tr>
	<td>DV</td>
<td><input type="text" id="txt_dv" name="<?php echo $raw_opcion['DV']['ID']; ?>"class="form-control" value="<?php echo $raw_opcion['DV']['VALUE']; ?>" readonly="readonly"/></td>
</tr>
<tr>
	<td>Direcci&oacute;n</td>
<td><input type="text" id="txt_direction" name="<?php echo $raw_opcion['DIRECCION']['ID']; ?>"class="form-control" value="<?php echo $raw_opcion['DIRECCION']['VALUE']; ?>" readonly="readonly"/></td>
</tr>
<tr>
	<td>Tel&eacute;fono</td>
<td><input type="text" id="txt_telephone" name="<?php echo $raw_opcion['TELEFONO']['ID']; ?>" class="form-control" value="<?php echo $raw_opcion['TELEFONO']['VALUE']; ?>" readonly="readonly"/></td>
</tr>
<tr>
	<td>Fax</td>
<td><input type="text" id="txt_fax" name="<?php echo $raw_opcion['FAX']['ID']; ?>" class="form-control" value="<?php echo $raw_opcion['FAX']['VALUE']; ?>" readonly="readonly"/></td>
</tr>
<tr>
	<td>E-Mail</td>
<td><input type="text" id="txt_email" name="<?php echo $raw_opcion['EMAIL']['ID']; ?>" class="form-control" value="<?php echo $raw_opcion['EMAIL']['VALUE']; ?>" readonly="readonly"/></td>
</tr>
<tr>
	<td>Alicuota</td>
	<td>
		<span id="span_impuesto" name="<?php echo $raw_opcion['IMPUESTO']['ID']; ?>" class="form-control bg-disabled"><?php echo $raw_opcion['IMPUESTO']['VALUE']; ?></span>
	</td>
</tr>

</tbody>
<tfoot class="bg-primary"><tr><td></td><td></td></tr></tfoot>
</table>
	</div>
    <div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <button type="button" id="btn_save" class="btn btn-success">Guardar</button>
    &nbsp;&nbsp;
    <button type="button" id="btn_back" class="btn btn-warning">Volver</button>
    </div>
</form>
</div>


<div id="footer">
	<?php require 'attached/php/req_footer.php'; ?>
</div>
</div>
<script type="text/javascript">
	ScrollReveal().reveal('#tbl_product tbody tr', {interval: 100});
	<?php include 'attached/php/req_footer_js.php'; ?>
</script>

</body>
</html>
