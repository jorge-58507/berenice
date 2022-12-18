<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_admin.php';

$fecha_actual = date('Y-m-d');
$proveedor_id = $_GET['a'];

$qry_proveedor = $link->query("SELECT AI_proveedor_id, TX_proveedor_nombre, TX_proveedor_cif, TX_proveedor_dv, TX_proveedor_direccion, TX_proveedor_telefono FROM bh_proveedor WHERE AI_proveedor_id = '$proveedor_id'")or die($link->error);
$rs_proveedor = $qry_proveedor->fetch_array();

$qry_facturacompra = $link->query("SELECT bh_facturacompra.AI_facturacompra_id, bh_facturacompra.TX_facturacompra_fecha, bh_facturacompra.TX_facturacompra_numero, bh_facturacompra.TX_facturacompra_ordendecompra, bh_facturacompra.TX_facturacompra_status FROM bh_facturacompra WHERE bh_facturacompra.facturacompra_AI_proveedor_id = '$proveedor_id' order by TX_facturacompra_fecha DESC") or die($link->error);

$qry_saldo = $link->prepare("SELECT AI_facturacompra_id, TX_datocompra_cantidad, TX_datocompra_precio, TX_datocompra_impuesto, TX_datocompra_descuento FROM (bh_facturacompra INNER JOIN bh_datocompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id) WHERE AI_facturacompra_id = ?") or die($link->error);

$qry_bank_account = $link->query("SELECT bh_banco.TX_banco_value, bh_user.TX_user_seudonimo, bh_banconumero.AI_banconumero_id, bh_banconumero.TX_banconumero_value FROM (((bh_banconumero INNER JOIN bh_banco ON bh_banco.AI_banco_id = bh_banconumero.banconumero_AI_banco_id) INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_banconumero.banconumero_AI_proveedor_id) INNER JOIN bh_user ON bh_user.AI_user_id = bh_banconumero.banconumero_AI_user_id) WHERE bh_proveedor.AI_proveedor_id = '$proveedor_id'") or die($link->error);

$qry_cpp = $link->query("SELECT bh_cpp.AI_cpp_id, bh_cpp.TX_cpp_total, bh_cpp.TX_cpp_saldo, bh_cpp.TX_cpp_fecha FROM (bh_cpp INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = cpp_AI_proveedor_id) WHERE cpp_AI_proveedor_id = '$proveedor_id' AND TX_cpp_status = 'ACTIVA' ORDER BY TX_cpp_fecha DESC") or die($link->error);
$qry_datocpp = $link->prepare("SELECT AI_datocpp_id FROM bh_datocpp WHERE datocpp_AI_cpp_id = ?")or die($link->error);
$qry_cheque = $link->prepare("SELECT AI_cheque_id FROM bh_cheque WHERE cheque_AI_cpp_id = ?")or die($link->error);

$qry_cpp_expired = $link->query("SELECT bh_cpp.AI_cpp_id, bh_cpp.TX_cpp_total, bh_cpp.TX_cpp_saldo, bh_cpp.TX_cpp_fecha FROM (bh_cpp INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = cpp_AI_proveedor_id) WHERE cpp_AI_proveedor_id = '$proveedor_id' AND TX_cpp_status = 'ACTIVA' AND bh_cpp.TX_cpp_fecha <= '$fecha_actual' ORDER BY TX_cpp_fecha DESC") or die($link->error);
$nr_cpp_expired = $qry_cpp_expired->num_rows;

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
<link href="attached/css/admin_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>


<script type="text/javascript">

$(document).ready(function() {
	$(window).on('beforeunload', function(){
		close_popup();
	});
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


	$('#btn_cancel').click(function(){
		history.back(1);
	})
	$('#btn_acept').click(function(){
		$.ajax({	data: {"a" : $("#txt_proveedor").val(), "b" : $("#txt_cif").val(), "c" : $("#txt_dv").val(), "d" : $("#txt_telephone").val(), "e" : $("#txt_direction").val(), "f" : '<?php echo $_GET['a']; ?>' },	type: "GET",	dataType: "text",	url: "attached/get/upd_providerinfo.php", })
		 .done(function( data, textStatus, jqXHR ) { console.log("GOOD"+textStatus);
			})
		 .fail(function( jqXHR, textStatus, errorThrown ) {		});
	})
	$("#btn_add_bankaccount").on("click",function(){
		open_popup('popup_add_bankaccount.php?a=<?php echo $proveedor_id; ?>', '_popup','450','506');
	})
	$("#btn_expiredcpp").on("click",function(){
		$.ajax({	data: {"a" : <?php echo $proveedor_id; ?>},	type: "GET",	dataType: "text",	url: "attached/get/get_cpp_expiredcpp.php", })
		 .done(function( data, textStatus, jqXHR ) { console.log("GOOD"+textStatus);
		 $("#tbl_cpp tbody").html(data);
	 	})
		 .fail(function( jqXHR, textStatus, errorThrown ) {		});
	})
	$("#txt_fecha_i").on("change",function(){
		$("#txt_filtercpp").keyup();
	})
	$("#txt_filtercpp").on("keyup", function(){
		$.ajax({	data: {"a" : this.value, "b" : <?php echo $proveedor_id; ?>, "c" : $("#txt_fecha_i").val() },	type: "GET",	dataType: "text",	url: "attached/get/filter_providerinfo_cpp.php", })
		 .done(function( data, textStatus, jqXHR ) { console.log("GOOD"+textStatus);
		 $("#tbl_cpp tbody").html(data);
	 })
		.fail(function( jqXHR, textStatus, errorThrown ) {		});
	})
	$( function() {
		$("#txt_fecha_i").datepicker({
			changeMonth: true,
			changeYear: true
		});
	});
	$("#txt_fc_fecha_i, #txt_fc_fecha_f").on("change",function(){
		$("#txt_filterbill").keyup();
	})
	$("#txt_filterbill").on("keyup", function(){
		$.ajax({	data: {"a" : this.value, "b" : <?php echo $proveedor_id; ?>, "c" : $("#txt_fc_fecha_i").val() , "d" : $("#txt_fc_fecha_f").val() },	type: "GET",	dataType: "text",	url: "attached/get/filter_providerinfo_fc.php", })
		 .done(function( data, textStatus, jqXHR ) { console.log("GOOD"+textStatus);
		 $("#tbl_bill tbody").html(data);
	 })
		.fail(function( jqXHR, textStatus, errorThrown ) {		});
	})
	$( function() {
		var dateFormat = "dd-mm-yy",
			from = $( "#txt_fc_fecha_i" )
				.datepicker({
					defaultDate: "+1w",
					changeMonth: true,
					numberOfMonths: 2
				})
				.on( "change", function() {
					to.datepicker( "option", "minDate", getDate( this ) );
				}),
			to = $( "#txt_fc_fecha_f" ).datepicker({
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

$("#btn_add_check").on("click", function(){
	window.location.href="make_check.php?a=<?php echo $_GET['a']; ?>";
})

$('#txt_cif, #txt_telephone, #txt_dv').validCampoFranz('-0123456789');
$('#txt_proveedor, #txt_direction').validCampoFranz('.0123456789 abcdefghijklmnopqrstuvwxyz-,');
$('#txt_filtercpp').validCampoFranz('.0123456789');

var	content_proveedor = "";
$('#txt_proveedor').on("keyup",function(){
	if (this.value.length > 60) {
		this.value = content_proveedor;
	}else{
		content_proveedor = this.value;
	}
})

var	content_direction = "";
$('#txt_direction').on("keyup",function(){
	if (this.value.length > 160) {
		this.value = content_direction;
	}else{
		content_direction = this.value;
	}
})

var	content_cif = "";
$('#txt_cif').on("keyup",function(){
	if (this.value.length > 30) {
		this.value = content_cif;
	}else{
		content_cif = this.value;
	}
})

var	content_dv = "";
$('#txt_dv').on("keyup",function(){
	if (this.value.length > 2) {
		this.value = content_dv;
	}else{
		content_dv = this.value;
	}
})

var	content_telephone = "";
$('#txt_telephone').on("keyup",function(){
	if (this.value.length > 20) {
		this.value = content_telephone;
	}else{
		content_telephone = this.value;
	}
})


});
function del_account_number(account_number_id){
	$.ajax({	data: {"a" : account_number_id, "b" : '<?php echo $_GET['a']; ?>' },	type: "GET",	dataType: "text",	url: "attached/get/del_provider_accountnumber.php", })
	 .done(function( data, textStatus, jqXHR ) { console.log("GOOD"+textStatus);
	 	$("#tbl_bankaccount tbody").html(data);
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}
function del_provider_cpp(cpp_id){
	$.ajax({	data: {"a" : cpp_id },	type: "GET",	dataType: "text",	url: "attached/get/del_provider_cpp.php", })
	 .done(function( data, textStatus, jqXHR ) { console.log("GOOD"+textStatus);
	 		$("#tbl_cpp tbody").html(data);
		})
	 .fail(function( jqXHR, textStatus, errorThrown ) {		});
}

</script>

</head>

<body>
<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-2" >
  	<div id="logo" ></div>
 	</div>
	<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-10">
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
<form method="post" name="form_editdatoventa" action="">
	<div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 pt_7">
		<div id="container_proveedor" class="col-xs-12 col-sm-7 col-md-7 col-lg-7">
			<label class="label label_blue_sky"  for="txt_proveedor">Nombre: </label>
		  <input type="text" name="txt_proveedor" id="txt_proveedor" alt="<?php echo $rs_proveedor['AI_proveedor_id'] ?>" class="form-control" value="<?php echo $rs_proveedor['TX_proveedor_nombre'] ?>" />
		</div>
		<div id="container_cif" class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
			<label class="label label_blue_sky"  for="txt_cif">RUC:</label>
		  <input name="txt_cif" id="txt_cif" class="form-control" value="<?php echo $rs_proveedor['TX_proveedor_cif']; ?>" />
		</div>
		<div id="container_dv" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
			<label class="label label_blue_sky"  for="txt_dv">DV:</label>
		  <input type="text" name="txt_dv" id="txt_dv" class="form-control" value="<?php echo $rs_proveedor['TX_proveedor_dv'] ?>" />
		</div>
		<div id="container_telephone" class="col-xs-3 col-sm-3 col-md-3 col-lg-3 pt_7">
			<label class="label label_blue_sky"  for="txt_telephone">Tel&eacute;fono:</label>
		  <input type="text" name="txt_telephone" id="txt_telephone" class="form-control" value="<?php echo $rs_proveedor['TX_proveedor_telefono'] ?>" />
		</div>
		<div id="container_direction" class="col-xs-9 col-sm-9 col-md-9 col-lg-9 pt_7">
		  <label class="label label_blue_sky"  for="txt_direction">Direcci&oacute;n:</label>
		  <textarea name="txt_direction" id="txt_direction" class="form-control"><?php echo $rs_proveedor['TX_proveedor_direccion']; ?></textarea>
		</div>
		<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<button type="button" id="btn_acept" class="btn btn-success">Guardar</button>
				&nbsp;
			<button type="button" id="btn_cancel" class="btn btn-warning">Volver</button>
		</div>
	</div>
	<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 pt_7">
		<table id="tbl_bankaccount" class="table table-bordered table-condensed table-striped">
			<caption>Cuentas Bancarias</caption>
		<thead class="bg_green">
			<tr>
				<th class="al_center">BANCO</th>
				<th class="al_center">NUMERO</th>
				<th></th>
			</tr>
		</thead>
		<tfoot class="bg_green">
			<tr>
				<td colspan="3"></td>
			</tr>
		</tfoot>
		<tbody>
	<?php while($rs_bank_account = $qry_bank_account->fetch_array()){ ?>
			<tr title="<?php echo $rs_bank_account['TX_user_seudonimo'] ?>">
				<td><?php echo $rs_bank_account['TX_banco_value'] ?></td>
				<td><?php echo $rs_bank_account['TX_banconumero_value'] ?></td>
				<td class="al_center"><button type="button" class="btn btn-danger btn-sm" onclick="del_account_number('<?php echo $rs_bank_account['AI_banconumero_id']; ?>')"><i class="fa fa-times" aria-hidden="true"></i></button></td>
			</tr>
	<?php } ?>
		</tbody>
		</table>
		<div id="container_btn" class="container-fluid">
			<button type="button" id="btn_add_bankaccount" class="btn btn-default btn-sm"><i class="fa fa-bank" aria-hidden="true"></i> Agregar</button>
			&nbsp;
			<button type="button" id="btn_add_check" class="btn btn-info btn-sm"><i class="fa fa-money" aria-hidden="true"></i> Nvo. Cheque</button>
		</div>
	</div>


	<div id="container_txtfilterbill" class="col-xs-12 col-sm-6 col-md-6 col-lg-6 pt_7">
		<div id="container_txtfilterbill" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			<label class="label label_blue_sky"  for="txt_filterbill">Buscar</label>
			<input type="text" class="form-control" id="txt_filterbill">
		</div>
		<div id="container_txtfcfechai" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
			<label class="label label_blue_sky"  for="txt_fc_fecha_i">Inicio</label>
			<input type="text" class="form-control" id="txt_fc_fecha_i" readonly="readonly" value="<?php echo date('d-m-Y',strtotime(date('Y-m-d', strtotime('-1 week')))); ?>">
		</div>
		<div id="container_txtfcfechaf" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
			<label class="label label_blue_sky"  for="txt_fc_fecha_f">Final</label>
			<input type="text" class="form-control" id="txt_fc_fecha_f" readonly="readonly" value="<?php echo date('d-m-Y'); ?>">
		</div>
		<div id="container_tblbill" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<table id="tbl_bill" class="table table-bordered table-hover table-condensed">
			<caption>Facturas de Compra</caption>
			<thead class="bg_green">
				<tr>
					<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center">FECHA</th>
					<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center">NUMERO</th>
					<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center">O.C.</th>
					<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center">TOTAL</th>
				</tr>
			</thead>
			<tfoot class="bg_green">
				<tr>
					<td colspan="4"></td>
				</tr>
			</tfoot>
			<tbody>
		<?php while ($rs_facturacompra=$qry_facturacompra->fetch_array()) {
			$saldo_total=0;
			$qry_saldo->bind_param('i', $rs_facturacompra['AI_facturacompra_id']);
			$qry_saldo->execute();
			$result = $qry_saldo->get_result();
			while ($rs_saldo = $result->fetch_array()) {
				$descuento = ($rs_saldo['TX_datocompra_descuento']*$rs_saldo['TX_datocompra_precio'])/100;
				$precio_descuento = $rs_saldo['TX_datocompra_precio']-$descuento;
				$impuesto = ($rs_saldo['TX_datocompra_impuesto']*$precio_descuento)/100;
				$precio_impuesto = $precio_descuento+$impuesto;
				$precio_producto = $precio_impuesto *$rs_saldo['TX_datocompra_cantidad'];
				$saldo_total += $precio_producto;
			}
		?>
			<tr>
				<td><?php echo date('d-m-Y',strtotime($rs_facturacompra['TX_facturacompra_fecha'])); ?></td>
				<td><?php echo $rs_facturacompra['TX_facturacompra_numero'] ?></td>
				<td><?php echo $rs_facturacompra['TX_facturacompra_ordendecompra'] ?></td>
				<td><?php echo number_format($saldo_total,2) ?></td>
			</tr>
		<?php } ?>
			</tbody>
			</table>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 pt_7">
		<div id="container_txtfiltercpp" class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
			<label class="label label_blue_sky"  for="txt_filtercpp">Buscar</label>
			<input type="text" class="form-control" id="txt_filtercpp">
		</div>
		<div id="container_txtfechai" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
			<label class="label label_blue_sky"  for="txt_fecha_i">Fecha</label>
			<input type="text" class="form-control" id="txt_fecha_i" readonly="readonly" value="<?php echo date('d-m-Y'); ?>">
		</div>
		<div id="container_btnaddcpp" class="col-xs-3 col-sm-3 col-md-3 col-lg-3 side-btn-md">
			<button type="button" class="btn btn-success" id="btn_addcpp" onclick="open_popup('popup_addcpp.php?a=<?php echo $proveedor_id; ?>','_popup','420','420')"><i class="fa fa-plus"></i></button>
		&nbsp;
			<button type="button" class="btn btn-warning btn-sm" id="btn_expiredcpp" onclick=""><span class="badge"><?php echo $nr_cpp_expired; ?></span></button>
		</div>
		<div id="container_tblcpp" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<table id="tbl_cpp" class="table table-bordered table-hover table-condensed">
			<caption>Cuentas por Pagar</caption>
			<thead class="bg-primary">
				<tr>
					<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center">FECHA</th>
					<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center">TOTAL</th>
					<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center">SALDO</th>
					<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center">NUMERO</th>
					<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center"></th>
					<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center"></th>
					<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center"></th>
				</tr>
			</thead>
			<tfoot class="bg-primary">
				<tr>
					<td colspan="7"></td>
				</tr>
			</tfoot>
			<tbody>
		<?php while ($rs_cpp=$qry_cpp->fetch_array()) {
		?>
			<tr>
				<td><?php echo date('d-m-Y',strtotime($rs_cpp['TX_cpp_fecha'])); ?></td>
				<td class="al_center"><?php echo "B/ ".number_format($rs_cpp['TX_cpp_total'],2); ?></td>
				<td class="al_center"><?php echo "B/ ".number_format($rs_cpp['TX_cpp_saldo'],2); ?></td>
<?php
$qry_facturanumero=$link->query("SELECT TX_facturacompra_numero FROM (bh_facturacompra INNER JOIN bh_cpp ON bh_facturacompra.AI_facturacompra_id = bh_cpp.cpp_AI_facturacompra_id) WHERE AI_cpp_id = '{$rs_cpp['AI_cpp_id']}' ")or die($link->error);
$rs_facturanumero = $qry_facturanumero->fetch_array();
$qry_pedido=$link->query("SELECT TX_pedido_numero FROM (bh_pedido INNER JOIN bh_cpp ON bh_pedido.AI_pedido_id = bh_cpp.cpp_AI_pedido_id) WHERE AI_cpp_id = '{$rs_cpp['AI_cpp_id']}' ")or die($link->error);
$rs_pedido = $qry_pedido->fetch_array();
 ?>
				<td><?php
				if (!empty($rs_facturanumero[0])) {	echo "<em>".$rs_facturanumero[0]."</em>"; }
				if (!empty($rs_pedido[0])) {	echo "<strong>".$rs_pedido[0]."</strong>"; }
				?></td>
				<td class=" al_center"><button type="button" class="btn btn-success btn-sm" title="Elaborar Pago" onclick="document.location.href='admin_pay_cpp.php?a=<?php echo $rs_cpp['AI_cpp_id']; ?>'"><i class="fa fa-money" aria-hidden="true"></i></button></td>
				<td class=" al_center"><button type="button" class="btn btn-info btn-sm" title="Cerrar CPP" onclick="open_popup('popup_cpp_confirm.php?a=<?php echo $rs_cpp['AI_cpp_id']; ?>','_popup','580','420')"><i class="fa fa-check" aria-hidden="true"></i></button></td>
				<td class=" al_center"><?php
				$qry_datocpp->bind_param("i", $rs_cpp['AI_cpp_id']); $qry_datocpp->execute(); $result_datocpp = $qry_datocpp->get_result();
				$qry_cheque->bind_param("i", $rs_cpp['AI_cpp_id']); $qry_cheque->execute(); $result_cheque = $qry_cheque->get_result();
				if ($result_datocpp->num_rows < 1 && $result_cheque->num_rows < 1) { ?>
					<button type="button" class="btn btn-danger btn-sm" title="Eliminar CPP" onclick="del_provider_cpp('<?php echo $rs_cpp['AI_cpp_id']; ?>')"><i class="fa fa-times" aria-hidden="true"></i></button>
<?php		}
				?></td>
			</tr>
		<?php } ?>
			</tbody>
			</table>
		</div>
	</div>
</form>
</div>


<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
        <div id="container_btnadminicon" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
        </div>
        <div id="container_txtcopyright" class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
    &copy; Derechos Reservados a: Jorge Salda&nacute;a <?php echo date('Y'); ?>
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
