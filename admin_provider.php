<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_stock.php';

$fecha_actual=date('Y-m-d');
$qry_proveedor = $link->query("SELECT AI_proveedor_id, TX_proveedor_nombre, TX_proveedor_cif, TX_proveedor_dv, TX_proveedor_direccion, TX_proveedor_telefono FROM bh_proveedor ORDER BY TX_proveedor_nombre ASC LIMIT 10");
$qry_saldo = $link->prepare("SELECT bh_cpp.AI_cpp_id, bh_cpp.TX_cpp_total, bh_cpp.TX_cpp_saldo FROM (bh_cpp INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = cpp_AI_proveedor_id) WHERE cpp_AI_proveedor_id = ? AND TX_cpp_saldo > 0") or die($link->error);
$qry_cpp_facturacompra = $link->prepare("SELECT AI_facturacompra_id,TX_facturacompra_numero FROM (bh_facturacompra INNER JOIN bh_cpp ON bh_facturacompra.AI_facturacompra_id = bh_cpp.cpp_AI_facturacompra_id) WHERE AI_cpp_id = ?");
$qry_cpp_pedido = $link->prepare("SELECT AI_pedido_id,TX_pedido_numero FROM (bh_pedido INNER JOIN bh_cpp ON bh_pedido.AI_pedido_id = bh_cpp.cpp_AI_pedido_id) WHERE AI_cpp_id = ?");
$qry_expired_cpp = $link->query("SELECT bh_cpp.AI_cpp_id,bh_cpp.TX_cpp_saldo,bh_cpp.TX_cpp_total,bh_cpp.TX_cpp_fecha, bh_proveedor.TX_proveedor_nombre, bh_proveedor.AI_proveedor_id FROM (bh_cpp INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_cpp.cpp_AI_proveedor_id) WHERE TX_cpp_fecha <= '$fecha_actual' AND TX_cpp_status = 'ACTIVA' ORDER BY TX_cpp_fecha DESC")or die($link->error);

$qry_cheque = $link->query("SELECT bh_cheque.AI_cheque_id, bh_cheque.TX_cheque_fecha, bh_proveedor.TX_proveedor_nombre, bh_cheque.cheque_AI_cpp_id, bh_cheque.TX_cheque_numero, bh_cheque.TX_cheque_monto, bh_cheque.TX_cheque_observacion, bh_cheque.cheque_AI_proveedor_id
	FROM (bh_cheque
		INNER JOIN bh_proveedor ON bh_proveedor.AI_proveedor_id = bh_cheque.cheque_AI_proveedor_id)
		ORDER BY TX_cheque_fecha LIMIT 10;")or die($link->error);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Trilli, S.A. - Todo en Materiales</title>
	<?php include 'attached/php/req_required.php'; ?>
	<link href="attached/css/admin_css.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript">

		$(document).ready(function() {
			$(window).on('beforeunload', function(){
				close_popup();
			});
			$("#btn_add_provider").on("click", function(){
				open_popup(`popup_addprovider.php?a=${$("#txt_filterprovider").val()}`,'_popup','420','473');
			})
			$("#txt_filterprovider").on("keyup", function(){
				$.ajax({	data: {"a" : this.value },	type: "GET",	dataType: "text",	url: "attached/get/filter_provider_adminprovider.php", })
				 .done(function( data, textStatus, jqXHR ) { console.log("GOOD"+textStatus);
				 	$("#tbl_provider tbody").html(data);
					})
				 .fail(function( jqXHR, textStatus, errorThrown ) {		});
			})
			$("#txt_filtercpp").on("keyup", function(){
				$.ajax({	data: {"a" : this.value, "b" : $("#txt_cpp_fechai").val(), "c" : $("#txt_cpp_fechaf").val() },	type: "GET",	dataType: "text",	url: "attached/get/filter_provider_admincpp.php", })
				 .done(function( data, textStatus, jqXHR ) { console.log("GOOD"+textStatus);
				 	$("#tbl_cpp tbody").html(data);
					})
				 .fail(function( jqXHR, textStatus, errorThrown ) {		});
			})
			$("#txt_filtercheque").on("keyup", function(){
				$.ajax({	data: {"a" : this.value, "b" : $("#txt_check_fechai").val(), "c" : $("#txt_check_fechaf").val() },	type: "GET",	dataType: "text",	url: "attached/get/filter_provider_admincheck.php", })
				 .done(function( data, textStatus, jqXHR ) { console.log("GOOD"+textStatus);
				 	$("#tbl_cheque tbody").html(data);
					})
				 .fail(function( jqXHR, textStatus, errorThrown ) {		});
			})
		// #######################  FILTRO DE CUENTAS POR PAGAR
			$( function() {
				var dateFormat = "dd-mm-yy",
			  from = $( "#txt_cpp_fechai" )
				.datepicker({
				  defaultDate: "+1w",
				  changeMonth: true,
				  numberOfMonths: 2
				})
				.on( "change", function() {
				  to.datepicker( "option", "minDate", getDate( this ) );
				}),
			  to = $( "#txt_cpp_fechaf" ).datepicker({
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

		// ################## FILTRO DE CHEQUES
			$( function() {
				var dateFormat = "dd-mm-yy",
			  from = $( "#txt_check_fechai" )
				.datepicker({
				  defaultDate: "+1w",
				  changeMonth: true,
				  numberOfMonths: 2
				})
				.on( "change", function() {
				  to.datepicker( "option", "minDate", getDate( this ) );
				}),
			  to = $( "#txt_check_fechaf" ).datepicker({
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
		});

		function del_provider(provider_id){
			$.ajax({	data: {"a" : provider_id },	type: "GET",	dataType: "text",	url: "attached/get/del_provider.php", })
			.done(function( data, textStatus, jqXHR ) { console.log("GOOD"+textStatus);
			 	$("#tbl_provider tbody").html(data);
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
		<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-10">
			<div id="container_username" class="col-lg-4  visible-lg">
				Bienvenido: <label class="bg-primary"><?php echo $rs_checklogin['TX_user_seudonimo']; ?></label>
		  </div>
			<div id="navigation" class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
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
<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-top:5px;">
<form name="form_provider" onsubmit="return false;">

	<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#cpp">Cts. por Pagar</a></li>
    <li><a data-toggle="tab" href="#proveedor">Proveedores</a></li>
    <li><a data-toggle="tab" href="#cheque">Cheques</a></li>
  </ul>
  <div class="tab-content">
    <div id="cpp" class="tab-pane fade in active">
			<div id="container_cpp" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div id="container_filtercpp" class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
					<label for="txt_filtercpp" class="label label_blue_sky" >Buscar</label>
					<input type="text" id="txt_filtercpp" placeholder="Buscar Cuentas por Pagar" autocomplete="off" class="form-control">
				</div>
				<div id="container_fechai" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
					<label for="txt_cpp_fechai" class="label label_blue_sky" >Fecha Inicial</label>
					<input type="text" id="txt_cpp_fechai" class="form-control" readonly="readonly" value="<?php  $month_year=date('Y-m',strtotime($fecha_actual)); echo date('d-m-Y',strtotime($month_year)); ?>">
				</div>
				<div id="container_fechaf" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
					<label for="txt_cpp_fechaf" class="label label_blue_sky" >Fecha Final</label>
					<input type="text" id="txt_cpp_fechaf" class="form-control" readonly="readonly"value="<?php  echo date('d-m-Y',strtotime($fecha_actual)); ?>">
				</div>

				<div id="container_provider_tblcpp" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<table id="tbl_cpp" class="table table-bordered table-condensed table-striped">
						<caption>Cuentas por Pagar</caption>
						<thead class="bg_red">
							<tr>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center">FECHA</th>
								<th class="col-xs-7 col-sm-7 col-md-7 col-lg-7 al_center">PROVEEDOR</th>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center">TOTAL</th>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center">SALDO</th>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center">NUMERO</th>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center"></th>
							</tr>
						</thead>
						<tfoot class="bg_red">
							<tr>
								<td colspan="6"></td>
							</tr>
						</tfoot>
						<tbody>
			<?php 	while ($rs_expired_cpp = $qry_expired_cpp->fetch_array()) { ?>
							<tr>
								<td><?php echo date('d-m-Y', strtotime($rs_expired_cpp['TX_cpp_fecha'])); ?></td>
								<td><?php echo $r_function->replace_special_character($rs_expired_cpp['TX_proveedor_nombre']); ?></td>
								<td><?php echo "B/ ".number_format($rs_expired_cpp['TX_cpp_total'],2); ?></td>
								<td><?php echo "B/ ".number_format($rs_expired_cpp['TX_cpp_saldo'],2); ?></td>
								<td><?php
									$qry_cpp_facturacompra->bind_param("i",$rs_expired_cpp['AI_cpp_id']); $qry_cpp_facturacompra->execute();
									$result = $qry_cpp_facturacompra->get_result(); $rs_cpp_facturacompra=$result->fetch_array(MYSQLI_ASSOC);
									if (!empty($rs_cpp_facturacompra['TX_facturacompra_numero'])) { ?>
										<a onclick="open_popup('popup_show_contentcpp.php?a=fc&b=<?php echo $rs_cpp_facturacompra['AI_facturacompra_id']; ?>','_popup','920','420'); return false;"><?php echo $rs_cpp_facturacompra['TX_facturacompra_numero']; ?></a>
			<?php					}
									$qry_cpp_pedido->bind_param("i",$rs_expired_cpp['AI_cpp_id']); $qry_cpp_pedido->execute();
									$result = $qry_cpp_pedido->get_result(); $rs_cpp_pedido=$result->fetch_array(MYSQLI_ASSOC);
									if (!empty($rs_cpp_pedido['TX_pedido_numero'])) { ?>
										<a onclick="open_popup('popup_show_contentcpp.php?a=oc&b=<?php echo $rs_cpp_pedido['AI_pedido_id']; ?>','_popup','920','420'); return false;"><?php echo $rs_cpp_pedido['TX_pedido_numero']; ?></a>
			<?php					}
								 ?></td>
								 <td class="al_center">
									 <button type="button" class="btn btn-info btn-sm" onclick="document.location.href='provider_info.php?a=<?php echo $rs_expired_cpp['AI_proveedor_id']; ?>'"><i class="fa fa-search" aria-hidden="true"></i></button>
								 </td>
							</tr>
			<?php 	} ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
    <div id="proveedor" class="tab-pane fade">
			<div id="container_provider" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div id="container_filterprovider" class="col-xs-10 col-sm-11 col-md-11 col-lg-11">
					<label for="txt_filterprovider" class="label label_blue_sky" >Buscar</label>
					<input type="text" id="txt_filterprovider" placeholder="Buscar Proveedor" autocomplete="off" class="form-control">
				</div>
				<div class="col-xs-2 col-sm-1 col-md-1 col-lg-1 side-btn-md">
					<button type="button" id="btn_add_provider" class="btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i></button>
				</div>

				<div id="container_tblprovider" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				    <table id="tbl_provider" class="table table-bordered table-condensed table-striped">
							<caption>Proveedores</caption>
				    <thead class="bg-primary">
				    <tr>
			        <th>Nombre</th>
			        <th>RUC</th>
			        <th>Telefono</th>
							<th>Direcci&oacute;n</th>
							<th>N&deg; Doc.</th>
							<th>Saldo</th>
							<th></th>
							<th></th>
				    </tr>
				    </thead>
				    <tfoot class="bg-primary"><tr><td colspan="8"></td></tr></tfoot>
				    <tbody>
			<?php 	while ($rs_proveedor = $qry_proveedor->fetch_array()){
							$saldo_total=0;
							$doc_counter=0;
							$last_doc =	"";

							$qry_saldo->bind_param('i', $rs_proveedor['AI_proveedor_id']);
							$qry_saldo->execute();
							$result = $qry_saldo->get_result();
							while ($rs_saldo = $result->fetch_array()) {
								if ($rs_saldo['AI_cpp_id'] != $last_doc) {
									$doc_counter++;
									$last_doc=$rs_saldo['AI_cpp_id'];
								}
								$saldo_total += $rs_saldo['TX_cpp_saldo'];
							}
			?>
				    <tr>
			        <td><?php echo $rs_proveedor['TX_proveedor_nombre']; ?></td>
			        <td><?php echo $rs_proveedor['TX_proveedor_cif']; ?></td>
			        <td><?php echo $rs_proveedor['TX_proveedor_telefono']; ?></td>
			        <td><?php echo $rs_proveedor['TX_proveedor_direccion']; ?></td>
							<td><?php echo $doc_counter; ?></td>
							<td><?php echo number_format($saldo_total,2); ?></td>
							<td class="al_center">
								<button type="button" class="btn btn-info btn-sm" onclick="document.location.href='provider_info.php?a=<?php echo $rs_proveedor['AI_proveedor_id']; ?>'"><i class="fa fa-search" aria-hidden="true"></i></button>
							</td>
			<?php 	$qry_facturacompra=$link->query("SELECT AI_facturacompra_id FROM bh_facturacompra WHERE facturacompra_AI_proveedor_id = '{$rs_proveedor['AI_proveedor_id']}'") ?>
							<td class="al_center"><?php if ($qry_facturacompra->num_rows < 1) { ?>
								<button type="button" id="btn_del" class="btn btn-danger btn-sm" onclick="del_provider('<?php echo $rs_proveedor['AI_proveedor_id']; ?>');"><i class="fa fa-times" aria-hidden="true"></i></button>
			<?php 	} ?></td>
				    </tr>
				    <?php }; ?>
				    </tbody>
				    </table>
				</div>
			</div>
    </div>
    <div id="cheque" class="tab-pane fade">
			<div id="container_cheque" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div id="container_filtercheque" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<label for="txt_filtercheque" class="label label_blue_sky" >Buscar</label>
					<input type="text" id="txt_filtercheque" placeholder="Buscar N&deg; de Cheque" autocomplete="off" class="form-control">
				</div>
				<div id="container_nvo_cheque" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
					<button type="button" id="btn_add_check" class="btn btn-info btn-sm" onclick="window.location='make_check.php'"><i class="fa fa-money" aria-hidden="true"></i> Nvo. Cheque</button>
				</div>
				<div id="container_fechai" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
					<label for="txt_check_fechai" class="label label_blue_sky" >Fecha Inicial</label>
					<input type="text" id="txt_check_fechai" class="form-control" readonly="readonly" value="<?php  $month_year=date('Y-m',strtotime($fecha_actual)); echo date('d-m-Y',strtotime($month_year)); ?>">
				</div>
				<div id="container_fechaf" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
					<label for="txt_check_fechaf"  class="label label_blue_sky" >Fecha Final</label>
					<input type="text" id="txt_check_fechaf" class="form-control" readonly="readonly"value="<?php  echo date('d-m-Y',strtotime($fecha_actual)); ?>">
				</div>


				<div id="container_tblcheque" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<table id="tbl_cheque" class="table table-bordered table-condensed table-striped">
						<caption>Cheques Guardados</caption>
						<thead class="bg_green">
							<tr>
								<th>FECHA</th>
								<th>BENEFICIARIO</th>
								<th>CPP</th>
								<th>NUMERO</th>
								<th>MONTO</th>
								<th>OBSERVACION</th>
								<th></th>
							</tr>
						</thead>
						<tfoot class="bg_green"><tr><td colspan="7"></td></tr></tfoot>
						<tbody>
<?php 				if ($qry_cheque->num_rows > 0) {
								while($rs_cheque = $qry_cheque->fetch_array()){ ?>
									<tr>
										<td><?php echo date('d-m-Y',strtotime($rs_cheque['TX_cheque_fecha'])); ?></td>
										<td><button type="button" class="btn btn-link" onclick="document.location='provider_info.php?a=<?php echo $rs_cheque['cheque_AI_proveedor_id']; ?>'"><?php echo $rs_cheque['TX_proveedor_nombre']; ?></button></td>
										<?php $href_cpp = (!empty($rs_cheque['cheque_AI_cpp_id'])) ? "admin_pay_cpp.php?a=".$rs_cheque['cheque_AI_cpp_id'] : ''; ?>
										<td class="al_center"><button type="button" class="btn btn-link" onclick="document.location='<?php echo $href_cpp; ?>'"><?php echo substr("0000000".$rs_cheque['cheque_AI_cpp_id'],-8); ?></button></td>
										<td><?php echo $rs_cheque['TX_cheque_numero']; ?></td>
										<td>B/ <?php echo number_format($rs_cheque['TX_cheque_monto'],2); ?></td>
										<td><?php echo $r_function->replace_special_character($rs_cheque['TX_cheque_observacion']); ?></td>
										<td class="al_center"><button type="button" class="btn btn-info btn-sm" onclick="print_html('print_check.php?a=<?php echo $rs_cheque['AI_cheque_id']; ?>')"><i class="fa fa-search"></i></button></td>
									</tr>
<?php 					}
							} else {	?>
								<tr><td colspan="7"></td></tr>
<?php 				} 	?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
  </div>




<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<button type="button" id="btn_salir" class="btn btn-warning" onclick="history.back(1);">Volver</button>
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
