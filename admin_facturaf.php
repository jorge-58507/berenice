<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_paydesk.php';

$fecha_actual=date('Y-m-d');
$fecha_i=date('d-m-Y',strtotime(date('Y-m-d',strtotime('-1 week'))));
$fecha_f = date('d-m-Y', strtotime($fecha_actual));

$qry_facturaf=$link->query("SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.facturaf_AI_cliente_id, bh_facturaf.facturaf_AI_user_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_subtotalni, bh_facturaf.TX_facturaf_subtotalci, bh_facturaf.TX_facturaf_impuesto, bh_facturaf.TX_facturaf_descuento, bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_status,
bh_cliente.TX_cliente_nombre,
bh_user.TX_user_seudonimo
FROM (((bh_facturaf
INNER JOIN bh_cliente ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id)
INNER JOIN bh_facturaventa ON bh_facturaventa.facturaventa_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaventa.facturaventa_AI_user_id)
ORDER BY  TX_facturaf_numero DESC LIMIT 10");
$rs_facturaf=$qry_facturaf->fetch_array();
$raw_facturaf = [];
do{
	array_push($raw_facturaf,$rs_facturaf);
}while($rs_facturaf=$qry_facturaf->fetch_array()); ?>
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
	<!-- <link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" /> -->

	<script type="text/javascript" src="attached/js/jquery.js"></script>
	<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
	<script type="text/javascript" src="attached/js/bootstrap.js"></script>
	<script type="text/javascript" src="attached/js/general_funct.js"></script>
	<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
	<script type="text/javascript" src="attached/js/admin_funct.js"></script>

	<script type="text/javascript">

		$(document).ready(function() {
			$(window).on('beforeunload',function(){
				close_popup();
			});

			$("#btn_back").click(function(){
				window.history.back(1);
			});
			$("#txt_filterfacturaf").keyup(function(){
				filter_adminfacturaf(this.value);
			})
			$("input[name=r_limit]").on("change", function(){
				$("#txt_filterfacturaf").keyup();
			})
			$("#sel_paymentmethod").on("change", function(){
				filter_adminfacturaf($("#txt_filterfacturaf").val());
			})

			$( function() {
				var dateFormat = "dd-mm-yy",
				from = $( "#txt_date_initial" )
				.datepicker({
					defaultDate: "+1w",
					changeMonth: true,
					numberOfMonths: 2
				})
				.on( "change", function() {
					to.datepicker( "option", "minDate", getDate( this ) );
				}),
				to = $( "#txt_date_final" ).datepicker({
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

			$("#filter_by_deficit").on("click",function(){
				filter_adminfacturaf("deficit");
			});

			$("#txt_date_final").change(function(){
				filter_adminfacturaf($("#txt_filterfacturaf").val());
			});

		});
		function toggle_tr(tr_id){
			$("#"+tr_id+"").toggle("slow","swing");
		}
		function open_newdebit(client_id){
			$.ajax({	data: "",	type: "GET",	dataType: "JSON",	url: "attached/get/get_session_admin.php", })
			.done(function( data, textStatus, jqXHR ) {
				if(data[0][0] != ""){
					open_popup('popup_newdebit.php?a='+client_id,'newdebit','425','420');
				}else{
					open_popup('popup_loginadmin.php?z=start_admin.php','_popup','425','420');
				}
			})
			.fail(function( jqXHR, textStatus, errorThrown ) { });
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
				<div id="container_username" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
    			Bienvenido: <label class="bg-primary"><?php echo $rs_checklogin['TX_user_seudonimo']; ?></label>
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
					} ?>
				</div>
			</div>
		</div>

		<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<form action="login.php" method="post" name="form_login"  id="form_login">
				<div id="container_filterfacturaf" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div id="container_txtfilterfacturaf" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
						<label for="txt_filterfacturaf" class="label label_blue_sky">Buscar</label>
						<input type="text" id="txt_filterfacturaf" class="form-control" placeholder="Numero Factura o Nombre de Cliente" autofocus />
					</div>
					<div id="container_txtdatei"  class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
						<label for="txt_date_initial" class="label label_blue_sky">Fecha Inicial</label>
						<input type="text" id="txt_date_initial" name="txt_date_initial" class="form-control" readonly="readonly" value="<?php echo $fecha_i; ?>" />
					</div>
					<div id="container_txtdatef"  class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
						<label for="txt_date_final" class="label label_blue_sky">Fecha Final</label>
						<input type="text" id="txt_date_final" name="txt_date_final" class="form-control" readonly="readonly" value="<?php echo $fecha_f; ?>" />
					</div>
					<div id="container_selpaymentmethod" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
						<?php 	
						$qry_paymentmethod = $link->query("SELECT AI_metododepago_id, TX_metododepago_value FROM bh_metododepago")or die($link->error);
						$raw_metododepago = array();
						while ($rs_paymentmethod = $qry_paymentmethod->fetch_array()) {
							$raw_metododepago[$rs_paymentmethod['AI_metododepago_id']] = $rs_paymentmethod['TX_metododepago_value'];
						} ?>
						<label class="label label_blue_sky" for="sel_paymentmethod">M&eacute;todo de P.</label>
						<select id="sel_paymentmethod" class="form-control">
							<option value="todos">Todos</option>
							<?php  		
							foreach ($raw_metododepago as $key => $value) {
								echo "<option value=\"$key\">$value</option>";
							}	?>
						</select>
					</div>
					<div id="container_rlimit"  class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
						<label for="r_limit" class="label label_blue_sky">Mostrar</label><br />
						<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit_10" value="10" checked="checked">10</label>
						<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit_50" value="50">50</label>
						<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit_100" value="100">100</label>
						<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit_0" value="">Todas</label>
					</div>
				</div>
				<div id="container_tblfacturaf" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<table id="tbl_facturaf" class="table table-bordered table-condensed table-striped">
						<thead class="bg-primary">
							<tr>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Nº</th>
								<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Nombre</th>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Total</th>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Deficit</th>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Vendedor</th>
								<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><i id="filter_by_deficit" class="glyphicon glyphicon-chevron-down" onclick="filter_adminfacturaf('deficit');"></i></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="7"></td>
							</tr>
  					</tbody>
						<tfoot class="bg-primary">
							<tr>
								<td colspan="7"></td>
							</tr>
						</tfoot>
					</table>
				</div>
				<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<button type="button" id="btn_back" class="btn btn-warning">Volver</button>
				</div>
			</form>
		</div>
		<div id="footer">
			<?php require 'attached/php/req_footer.php'; ?>
		</div>
	</div>
	<script type="text/javascript">
		var facturaf = JSON.parse('<?php echo json_encode($raw_facturaf); ?>');
		render_tableff(facturaf);
	</script>
</body>
</html>
