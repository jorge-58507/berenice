<?php
require 'bh_conexion.php';
$link=conexion();
date_default_timezone_set('America/Panama');

require 'attached/php/req_login_admin.php';

$cpp_id=$_GET['a'];
$qry_cpp = $link->query("SELECT bh_cpp.AI_cpp_id, bh_cpp.TX_cpp_total, bh_cpp.TX_cpp_saldo, bh_cpp.TX_cpp_fecha,
	bh_proveedor.TX_proveedor_nombre,bh_proveedor.TX_proveedor_cif,bh_proveedor.TX_proveedor_dv,bh_proveedor.TX_proveedor_telefono,bh_proveedor.TX_proveedor_direccion
	 FROM (bh_cpp INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = cpp_AI_proveedor_id) WHERE AI_cpp_id = '$cpp_id'") or die($link->error);
$rs_cpp = $qry_cpp->fetch_array();

$qry_datocpp = $link->query("SELECT bh_user.TX_user_seudonimo, bh_datocpp.AI_datocpp_id,bh_datocpp.TX_datocpp_monto,bh_datocpp.TX_datocpp_numero,bh_datocpp.TX_datocpp_fecha,bh_datocpp.datocpp_AI_metododepago_id FROM (bh_datocpp INNER JOIN bh_user ON bh_user.AI_user_id = bh_datocpp.datocpp_AI_user_id) WHERE datocpp_AI_cpp_id =	'$cpp_id'")or die($link->error);

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
<link href="attached/css/admin_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />
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


$("#btn_acept").on("click",function(){
	plus_cpp_payment('<?php echo $cpp_id; ?>');
})
$("#btn_back").click(function(){
	history.back(1);
});


$("#container_paymentmethod button").on("click", function(){
	if ($("#txt_amount").val() === "" || this.id === '2' && $("#txt_number").val() === "") {
		return false;
	}
	add_payment_cpp(this.id, $("#txt_amount").val(), $("#txt_number").val(), '<?php echo $cpp_id; ?>', $("#txt_date").val());
	$("#txt_amount").val("");
	$("#txt_number").val("");
})

$( function() {
	$("#txt_date").datepicker({
		changeMonth: true,
		changeYear: true
	});
});


$("#txt_amount").on("blur",function(){
	this.value = val_intw2dec(this.value);
});
$("#txt_amount, #txt_number").validCampoFranz('.1234567890');



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
<form action="" method="post" name="form_sell"  id="form_sell">
	<div class="container-fluid">
		<div id="container_proveedor" class="col-xs-12 col-sm-7 col-md-5 col-lg-5">
			<label for="span_proveedor">Nombre: </label>
			<span id="span_proveedor" class="form-control bg-disabled"><?php echo $rs_cpp['TX_proveedor_nombre'] ?></span>
		</div>
		<div id="container_cif" class="col-xs-6 col-sm-3 col-md-5 col-lg-5">
			<label for="span_cif">RUC:</label>
			<span id="span_cif" class="form-control bg-disabled"><?php echo $rs_cpp['TX_proveedor_cif']; ?></span>
		</div>
		<div id="container_dv" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
			<label for="span_dv">DV:</label>
			<span id="span_dv" class="form-control bg-disabled"><?php echo $rs_cpp['TX_proveedor_dv'] ?></span>
		</div>
	</div>
	<div class="container-fluid" style="padding-top: 5px;">
		<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
		&nbsp;</div>
		<div id="container_cppfecha" class="col-xs-12 col-sm-4 col-md-2 col-lg-2">
			<label for="span_cppfecha">Fecha: </label>
			<span id="span_cppfecha"><?php echo $rs_cpp['TX_cpp_fecha'] ?></span>
		</div>
		<div id="container_cpptotal" class="col-xs-12 col-sm-4 col-md-2 col-lg-2">
			<label for="span_total">Total:</label>
			<span id="span_total">B/ <?php echo number_format($rs_cpp['TX_cpp_total'],2); ?></span>
		</div>
		<div id="container_cppsaldo" class="col-xs-12 col-sm-4 col-md-2 col-lg-2">
			<label for="span_saldo">Saldo:</label>
			<span id="span_saldo">B/ <?php echo number_format($rs_cpp['TX_cpp_saldo'],2); ?></span>
		</div>
	</div>
	<div id="container_payment" class="container-fluid">
		<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3"></div>
		<div id="container_paymentdescription" class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<div id="container_txtfecha" class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
					<label for="txt_date">Fecha</label>
					<input type="text" id="txt_date" name="txt_date" class="form-control" readonly="readonly" value="<?php echo date('d-m-Y'); ?>" />
			</div>
			<div id="container_txtnumber" class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
					<label for="txt_number">Numero de Control</label>
					<input type="text" id="txt_number" name="txt_number" class="form-control" />
			</div>
			<div id="container_txtamount" class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
					<label for="txt_amount">Monto</label>
					<input type="text" id="txt_amount" name="txt_amount" class="form-control"  />
			</div>
		</div>
		<div id="container_paymentmethod" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<button type="button" id="1" name="button" class="btn btn-success btn-lg"><i class="fa fa-money" aria-hidden="true"></i> Efectivo</button>&nbsp;
			&nbsp;
			<button type="button" id="2" name="button" class="btn btn-primary btn-lg"><i class="fa fa-newspaper-o fa-rotate-180" aria-hidden="true"></i> Cheque</button>&nbsp;
			&nbsp;
			<button type="button" id="3" name="button" class="btn btn-default btn-lg"><i class="fa fa-cc-visa" aria-hidden="true"></i> Tarjeta Cr&eacute;dito</button>&nbsp;
			&nbsp;
			<button type="button" id="4" name="button" class="btn btn-info btn-lg"><i class="fa fa-credit-card" aria-hidden="true"></i> Tarjeta Clave</button>&nbsp;
			&nbsp;
			<button type="button" id="7" name="button" class="btn btn-warning btn-lg">N.C.</button>
			&nbsp;
			<button type="button" id="8" name="button" class="btn btn-danger btn-lg">Otro</button>
		</div>
	</div>
	<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	</div>
	<div id="container_tblpayment" class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
		<table id="tbl_payment" class="table table-bordered table-condensed">
			<thead class="bg-primary">
				<tr>
					<th class="al_center col-xs-2 col-sm-2 col-md-2 col-lg-2">FECHA</th>
					<th class="al_center col-xs-4 col-sm-4 col-md-4 col-lg-4">METODO</th>
					<th class="al_center col-xs-2 col-sm-2 col-md-2 col-lg-2">NUMERO</th>
					<th class="al_center col-xs-2 col-sm-2 col-md-2 col-lg-2">MONTO</th>
					<th class="al center col-xs-2 col-sm-2 col-md-2 col-lg-2"></th>
				</tr>
			</thead>
			<tfoot class="bg-primary">
				<tr>
					<td colspan="5"></td>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<td>&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<button type="button" id="btn_acept" class="btn btn-success">Guardar</button>
			&nbsp;
		<button type="button" id="btn_cancel" class="btn btn-warning" onclick="history.back(1);">Volver</button>
	</div>
	<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"></div>
	<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
		<table id="tbl_datocpp" class="table table-bordered table-condensed">
			<caption>Abonos Realizados</caption>
			<thead class="bg-info">
				<tr>
					<th class="al_center col-xs-2 col-sm-2 col-md-2 col-lg-2">FECHA</th>
					<th class="al_center col-xs-3 col-sm-3 col-md-3 col-lg-3">METODO</th>
					<th class="al_center col-xs-3 col-sm-3 col-md-3 col-lg-3">NUMERO</th>
					<th class="al_center col-xs-2 col-sm-2 col-md-2 col-lg-2">MONTO</th>
					<th class="al_center col-xs-2 col-sm-2 col-md-2 col-lg-2">USUARIO</th>
					<th></th>
				</tr>
			</thead>
			<tfoot class="bg-info">
				<tr>
					<td colspan="6"></td>
				</tr>
			</tfoot>
			<tbody>
<?php 	$metododepago = ['','Efectivo','Cheque','T. de Cr&eacute;dito','T. Clave','','','Nota de Cr&eacute;dito','Otro'];
				while ($rs_datocpp = $qry_datocpp->fetch_array()) {	?>
					<tr>
						<td><?php echo $rs_datocpp['TX_datocpp_fecha']; ?></td>
						<td><?php echo $metododepago[$rs_datocpp['datocpp_AI_metododepago_id']]; ?></td>
						<td><?php echo $rs_datocpp['TX_datocpp_numero']; ?></td>
						<td><?php echo $rs_datocpp['TX_datocpp_monto']; ?></td>
						<td><?php echo $rs_datocpp['TX_user_seudonimo']; ?></td>
						<td><button type="button" class="btn btn-danger btn-sm" onclick="del_cpp_payment('<?php echo $rs_datocpp['AI_datocpp_id']; ?>')"><i class="fa fa-times"></i></button></td>
					</tr>
<?php 	} ?>
			</tbody>
		</table>
	</div>
</form>
</div>


<div id="footer">
	<?php require 'attached/php/req_footer.php'; ?>
</div>
</div>
</div>
<script type="text/javascript">
	<?php include 'attached/php/req_footer_js.php'; ?>
</script>
</body>
</html>
