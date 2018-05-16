<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_admin.php';

$proveedor_nombre=''; $proveedor_id='';
if (!empty($_GET['a'])) {
	$proveedor_id = $_GET['a'];
	$qry_proveedor=$link->query("SELECT AI_proveedor_id, TX_proveedor_nombre FROM bh_proveedor WHERE AI_proveedor_id = '$proveedor_id'")or die($link->error);
	$rs_proveedor=$qry_proveedor->fetch_array(MYSQLI_ASSOC);
	$proveedor_nombre=$rs_proveedor['TX_proveedor_nombre'];
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
<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="attached/css/admin_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/admin_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>

<script type="text/javascript">

$(document).ready(function() {

$("#btn_navsale").click(function(){
	window.location="sale.php";
});
$("#btn_navstock").click(function(){
	window.location="stock.php";
});
$("#btn_navpaydesk").click(function(){
	window.location="paydesk.php";
})
$("#btn_navadmin").click(function(){
	window.location="start_admin.php";
});

$("#btn_start").click(function(){
	window.location="start.php";
});
$("#btn_exit").click(function(){
	location.href="index.php";
})

$("#txt_motivonc").validCampoFranz("0123456789 abcdefghijklmnopqrstuvwxyz.,");
$("#txt_monto").validCampoFranz("0123456789.");

$("#btn_print").click(function(){
	$.ajax({	data: {"a" : "", "b" : $("#txt_number").val(), "c" : $("#txt_monto").val(), "d" : $("#txt_montoletras").val(), "e" : $("#txt_observation").val(), "f" : $("#txt_filterprovider").prop("alt") },	type: "GET",	dataType: "text",	url: "attached/get/plus_check.php", })
	 .done(function( data, textStatus, jqXHR ) {
		 if (data) {
		 	console.log('GOOD '+textStatus);
			setTimeout(function(){ print_html('print_check.php?a='+data); },100);
		 }
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
});
$("#btn_cancel").click(function(){
	window.location.href="admin_provider.php";
});
$("#txt_monto").on("keyup", function(){
  $("#btn_letter_amount").click();
})
$("#btn_add_provider").on("click", function(){
	provider_id = $("#txt_filterprovider").prop("alt");
	if (provider_id === '') {
		var value = $("#txt_filterprovider").val();
		open_popup('popup_addprovider.php?a='+value,'popup_addprovider','460','463');
	} else {
		open_popup('popup_updprovider.php?a='+provider_id,'popup_addprovider','460','463');
	}
})

$("#txt_filterprovider").on("keyup", function(e){
  if (e.which === 13) {
		$("#btn_add_provider").click();
	}else{
		$( "#txt_filterprovider").prop("alt","");
	}

  $( function() {
    $( "#txt_filterprovider").autocomplete({
      source: "attached/get/filter_check_provider.php",
      minLength: 2,
      select: function( event, ui ) {
        $("#txt_filterprovider").prop('alt', ui.item.id);
        content = '<strong>Nombre:</strong> '+ui.item.value+' <strong>Tlf.</strong> '+ui.item.telefono+' <strong>Dir.</strong> '+ui.item.direccion.substr(0,20);
        fire_recall('container_provider_recall', content)
      }
    });
  });
});



});

</script>

</head>

<body>
<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-2" >
  	<div id="logo" ></div>
   	</div>

	<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-10 hidden-md">
    	<div id="container_username" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
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
<form action="" >
	<div id="container_cheque" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  	<div id="container_provider" class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
    	<label for="span_provider" class="label label_blue_sky">Beneficiario: </label>
      <input type="text" class="form-control" id="txt_filterprovider" placeholder="Proveedor" alt="<?php echo $proveedor_id; ?>" value="<?php echo $proveedor_nombre; ?>">
    </div>
    <div class="col-xs-2 col-sm-1 col-md-1 col-lg-1 side-btn-md">
      <button type="button" id="btn_add_provider" class="btn btn-success"><i class="fa fa-plus btn-md" aria-hidden="true"></i></button>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="container_provider_recall">

    </div>
  	<div id="container_number" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
    	<label for="txt_number"  class="label label_blue_sky">Numero: </label>
  		<input type="text" id="txt_number" class="form-control" value="" />
    </div>
  	<div id="container_monto" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
    	<label for="txt_monto"  class="label label_blue_sky">Monto:</label>
  		<input type="text" id="txt_monto" class="form-control" value="" />
    </div>
  	<div id="container_montoletras" class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
    	<label for="txt_montoletras" class="label label_blue_sky">Monto en Letras:</label>
  		<input type="text" id="txt_montoletras" class="form-control" value="" />
    </div>
  	<div id="container_montoletras" class="col-xs-2 col-sm-2 col-md-2 col-lg-2 side-btn-md">
  		<button type="button" class="btn btn-primary" id="btn_letter_amount" onclick="$('#txt_montoletras').val(nn($('#txt_monto').val()))"><i class="fa fa-refresh"></i></button>
    </div>
  	<div id="container_observation" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<label for="txt_observation" class="label label_blue_sky">Observaci&oacute;n: </label>
  		<textarea id="txt_observation" class="form-control"><?php if(!empty($rs_facturacompra[0])){ echo "Factura: ".$rs_facturacompra[0]; }; if(!empty($rs_pedido[0])){ echo "Orden de Compra: ".$rs_pedido[0]; }; ?></textarea>
    </div>
    <div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<button type="button" id="btn_print" class="btn btn-info"><span class="glyphicon glyphicon-print"></span> Imprimir</button>
      <button type="button" id="btn_cancel" class="btn btn-warning">Salir</button>
    </div>
	</div>
</form>
</div>


<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
        <div id="container_btnadminicon" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
        </div>
        <div id="container_txtcopyright" class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
    &copy; Derechos Reservados a: Trilli, S.A. 2017
        </div>
        <div id="container_btnstart" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
        		<i id="btn_start" class="fa fa-home" title="Ir al Inicio"></i>
        </div>
        <div id="container_btnexit" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            <button type="button" class="btn btn-danger" id="btn_exit">Salir</button></div>
        </div>
	</div>
</div>
</div>

</body>
</html>
