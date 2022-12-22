<?php
require 'bh_conexion.php';
$link=conexion();
$cambio = 0;
$numero_correlativo = 0;
if (!empty($_GET['a'])) {
	$last_ff = $_GET['a'];
	$qry_ff = $link->query("SELECT TX_facturaf_cambio, TX_facturaf_numero FROM bh_facturaf WHERE AI_facturaf_id = '$last_ff'")or die ($link->error);
	$rs_last_ff = $qry_ff->fetch_array();
	if (!empty($rs_last_ff['TX_facturaf_cambio'])) { $cambio=round($rs_last_ff['TX_facturaf_cambio'],2); }else{  $cambio = 0 ;}
	$numero_correlativo = $rs_last_ff['TX_facturaf_numero'];
}
require 'attached/php/req_login_paydesk.php';
$qry_product=$link->query("SELECT * FROM bh_producto ORDER BY TX_producto_value ASC LIMIT 10")or die($link->error);
$rs_product=$qry_product->fetch_array();

$qry_client=$link->query("SELECT * FROM bh_cliente ORDER BY TX_cliente_nombre ASC")or die($link->error);
$rs_client=$qry_client->fetch_array();

$txt_facturaventa="SELECT bh_facturaventa.facturaventa_AI_user_id, bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.AI_facturaventa_id, bh_cliente.TX_cliente_nombre, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status, bh_facturaventa.facturaventa_AI_cliente_id, bh_user.TX_user_seudonimo, bh_cliente.TX_cliente_direccion
FROM ((bh_facturaventa INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
INNER JOIN bh_user ON bh_facturaventa.facturaventa_AI_user_id = bh_user.AI_user_id)
WHERE bh_facturaventa.TX_facturaventa_status != 'INACTIVA' AND
bh_facturaventa.TX_facturaventa_status != 'CANCELADA'
 ORDER BY AI_facturaventa_id DESC LIMIT 10";

$qry_facturaventa=$link->query($txt_facturaventa)or die($link->error);
$rs_facturaventa=$qry_facturaventa->fetch_array();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Trilli, S.A. - Todo en Materiales</title>
	<?php include 'attached/php/req_required.php'; ?>
	<link href="attached/css/sell_css.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript">
		function open_newcollect(id,user){
			open_popup_w_scroll('popup_newcollect.php?a='+id+'&b='+user, 'popup_newcollect','525','420');
		}
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
					Bienvenido:<label class="bg-primary"><?php echo $rs_checklogin['TX_user_seudonimo']; ?></label>
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
			<form action="" method="post" name="form_sell"  id="form_sell" onsubmit="return false;">
				<div id="container_btn_sale" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	        <button type="button" class="btn btn-info btn-lg" autofocus="autofocus" onclick="window.location='new_sale.php?a='" ><strong>Nueva Venta</strong></button>
	        &nbsp;&nbsp;
	        <button type="button" id="btn_cashmovement" class="btn btn-warning"><strong>Caja Menuda</strong></button>
	        &nbsp;&nbsp;
					<button type="button" id="btn_facturaf" class="btn btn-info"><strong>Factura F.</strong></button>
					&nbsp;&nbsp;
					<button type="button" id="btn_refresh" class="btn btn-success btn-md btn_squared_md"><strong><i class="fa fa-refresh fa-spin fa-1x fa-fw"></i><span class="sr-only"></strong></button>
					&nbsp;&nbsp;
					<button type="button" id="btn_creditnote" class="btn btn-info"><strong>Notas de C.</strong></button>
	        &nbsp;&nbsp;
					<button type="button" id="btn_debitnote" class="btn btn-warning"><strong>Debitos</strong></button>
	        &nbsp;&nbsp;
					<button type="button" id="btn_client" class="btn btn-info"><strong>Clientes</strong></button>
	        &nbsp;&nbsp;
	        <button type="button" id="btn_cashregister" class="btn btn-danger"><strong>Arqueo de Caja</strong></button>
				</div>
				<div id="container_facturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			    <div id="container_txtfilterpaydesk" class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
						<label for="txt_filterpaydesk" class="label label_blue_sky">Buscar</label>
		        <input type="text" id="txt_filterpaydesk" class="form-control" />
			    </div>
					<div  class="col-xs-2 col-sm-2 col-md-2 col-lg-2"></div>
					<div id="container_spandiference" class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
						<span id="span_ff_number" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><strong>N&deg; Correlativo: </strong><?php echo $numero_correlativo; ?></span>
						<span id="span_diference"><i id="i_piggy_bank" class="glyphicon glyphicon-piggy-bank" aria-hidden="true"></i>&nbsp;Cambio:&nbsp;B/ <?php echo number_format($cambio,2); ?></span>
					</div>
    			<div id="container_tblfacturaventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<table id="tbl_facturaventa" class="table table-bordered table-striped">
							<thead class="bg-info">
						    <tr>
						      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
						      <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Vendedor</th>
						      <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Cliente</th>
						      <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Nº Factura</th>
						      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Total</th>
						      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
						      <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
						    </tr>
						  </thead>
    					<tfoot class="bg-info"><tr><td colspan="8"> </td></tr></tfoot>
    					<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</form>
		</div>
		<div id="footer">
			<?php require 'attached/php/req_footer.php'; ?>
		</div>
	</div>
	<script type="text/javascript">
		<?php require 'attached/php/req_footer_js.php'; ?>
		var slideLeft = {
			distance: '200%',
			origin: 'left',
			duration: 1000,
			opacity: 0.1
		};
		var leftNode = [
			document.querySelector('#btn_newsale'),
			document.querySelector('#btn_cashmovement'),
			document.querySelector('#btn_facturaf')
		];
		ScrollReveal().reveal(leftNode, slideLeft);

		var slideRight = {
			distance: '200%',
			origin: 'right',
			duration: 1000,
			opacity: 0.1
		};
		var rightNode = [
			document.querySelector('#btn_creditnote'),
			document.querySelector('#btn_debitnote'),
			document.querySelector('#btn_client'),
			document.querySelector('#btn_cashregister')
		];
		ScrollReveal().reveal(rightNode, slideRight);

		$(document).ready(function() {
			filter_paydesk();
			$(window).on('beforeunload',function(){	close_popup();	});
			$("#txt_filterpaydesk").focus();
			$("#txt_filterpaydesk").keyup(function(){
				filter_paydesk();
			});
			$("#txt_filterpaydesk").keyup(function(e){
				if(e.which == 13){
					$("#btn_newcollect").click();
				}
				if(e.which == 120){
					$("#btn_newsale").click();
				}
			});
			$("#btn_cashmovement").click(function(){
				open_popup_w_scroll('popup_cashmovement.php','popup_cashmovement','625','420');
			})
			$("#btn_cashregister").on("click",function(){
				open_popup("popup_cashregister.php",'_blank','820','620');
			});
			$("#btn_facturaf").on("click",function(){
				document.location.href="admin_facturaf.php";
			});
			$("#btn_creditnote").on("click",function(){
				open_popup_w_scroll('popup_paydesk_creditnote.php','_popup','1000','420');
			});
			$("#btn_debitnote").on("click",function(){
				open_popup_w_scroll('popup_paydesk_debitnote.php','_popup','1000','420');
			});
			$("#btn_client").on("click", function(){
				document.location.href="admin_account_receivable.php";
			})
			$("#btn_refresh").on("click",function(){
				filter_paydesk();

				// $.ajax({	data: "",	type: "GET",	dataType: "text",	url: "attached/get/get_paydesk_facturaventa.php", })
				// .done(function( data, textStatus, jqXHR ) { 
				// 	$("#tbl_facturaventa tbody").html(data);
				// })
				// .fail(function( jqXHR, textStatus, errorThrown ) {	     console.log("BAD " + textStatus);	});
			})
			// $("#i_piggy_bank").on("click",function(){
			// 	$.ajax({	data: "",type: "GET",dataType: "json",url: "attached/get/get_session_admin.php",	})
			// 	 .done(function( data, textStatus, jqXHR ) { console.log( "GOOD " + textStatus);
			// 		if(data[0][0] != ""){
			// 			$.ajax({	data: {},	type: "GET",	dataType: "text",	url: "attached/php/open_cashdrawer.php", })
			// 				.done(function( data, textStatus, jqXHR ) { console.log("GOOD "+textStatus);				})
			// 				.fail(function( jqXHR, textStatus, errorThrown ) {		});
			// 			}else{
			// 				popup = window.open("popup_loginadmin.php?z=start_admin.php", "popup_loginadmin", 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=no,width=425,height=420');
			// 			}
			// 		})
			// 	.fail(function( jqXHR, textStatus, errorThrown ) {
			// 		if ( console && console.log ) {	 console.log( "La solicitud a fallado: " +  textStatus); }
			// 	})
			// })

		});

	</script>
</body>
</html>
