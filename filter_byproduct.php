<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_sale.php';
session_destroy();

$fecha_actual = date('d-m-Y');
$month_year = date('Y-m',strtotime($fecha_actual));
$fecha_inicial = date('d-m-Y',strtotime($month_year));

	$qry_product=$link->query("SELECT AI_producto_id, TX_producto_codigo, TX_producto_value, TX_producto_cantidad, TX_producto_maximo, TX_producto_minimo FROM bh_producto ORDER BY TX_producto_value ASC LIMIT 5 ");
	$raw_producto=array();
	while($rs_product=$qry_product->fetch_array(MYSQLI_ASSOC)){
		$raw_producto[]=$rs_product;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trilli, S.A. - Todo en Materiales</title>

<?php include 'attached/php/req_required.php'; ?>
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />
<link href="attached/css/stock_css.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="attached/js/newpurchase_funct.js"></script>

<script type="text/javascript">

$(document).ready(function() {

	$(window).on('beforeunload', function(){
		cerrarPopup();
	});

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

	$("#btn_filtercompra").on("click",function(){
		$("#txt_filterfacturacompra").keyup();
	});
	$("#btn_printcompra").on("click",function(){
		var value = $("#txt_filterfacturacompra").val();
		var	limit = $("input[name=r_limit]:checked").val();
		var date_i = $("#txt_date_initial").val();
		var date_f = $("#txt_date_final").val();
		var href =	"print_compra_html.php?a="+value+"&b="+limit+"&c="+date_i+"&d="+date_f;
		print_html(href);
	})
	$("#txt_filterproduct").on("keyup", function(){
		var limit = ($("input[name=r_limit]:checked").val());
		$.ajax({	data: { "a" : url_replace_regular_character(this.value), "b" : limit },	type: "GET",	dataType: "text",	url: "attached/get/filter_ps_product.php", })
		.done(function( data, textStatus, jqXHR ) {
			$("#tbl_product tbody").html(data);
		})
		.fail(function( jqXHR, textStatus, errorThrown ) {		});
	})
//   ######################      SOLD FUNCTION
	$("#txt_filterfacturaf").on("keyup",function(){
		var limit = $("input[name=r_limit]:checked").val();
		var date_i = $("#txt_date_initial").val();
		var date_f = $("#txt_date_final").val();
		$.ajax({	data: { "a" : this.value, "b" : limit, "c" : date_i, "d" : date_f  },	type: "GET",	dataType: "text",	url: "attached/get/filter_facturaf_sold.php", })
		 .done(function( data, textStatus, jqXHR ) {
			 $("#tbl_facturaf tbody").html(data);
		 })
		 .fail(function( jqXHR, textStatus, errorThrown ) {		});
	})
	$("#btn_filterfacturaf").on("click",function(){
		$("#txt_filterfacturaf").keyup();
	})
	$("#btn_printfacturaf").on("click",function(){
		var value = $("#txt_filterfacturaf").val();
		var limit = $("input[name=r_limit]:checked").val();
		var date_i = $("#txt_date_initial").val();
		var date_f = $("#txt_date_final").val();
		var href = "print_venta_html.php?a="+value+"&b="+limit+"&c="+date_i+"&d="+date_f;
		print_html(href);
	})
	$("#btn_printfacturaf_total").on("click",function(){
		var date_i = $("#txt_date_initial").val();
		var date_f = $("#txt_date_final").val();
		var href = "print_venta_total_html.php?c="+date_i+"&d="+date_f;
		print_html(href);
	})
});
function transform_facturacompra(facturacompra_id){
  $.ajax({	data: "",type: "GET",dataType: "json",url: "attached/get/get_session_admin.php",	})
	 .done(function( data, textStatus, jqXHR ) { console.log( "GOOD " + textStatus);
		  if(data[0][0] != ""){
	      $.ajax({	data: {"a" : facturacompra_id, "b" : $("#txt_filterfacturacompra").val()	},	type: "GET",	dataType: "text",	url: "attached/get/get_facturacompra.php", })
	      .done(function( data, textStatus, jqXHR ) { console.log("GOOD "+textStatus);
					if (data) {	window.location.href="purchase.php";	}
	      })
	      .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
	    }else{
	      popup = window.open("popup_loginadmin.php?z=start_admin.php", "popup_loginadmin", 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=no,width=425,height=420');
	    }
    })
  .fail(function( jqXHR, textStatus, errorThrown ) {
    if ( console && console.log ) {	 console.log( "La solicitud a fallado: " +  textStatus); }
  })
}


</script>
</head>

<body>
	<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    	<div id="logo_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-2">
  			<div id="logo" ></div>
   		</div>
			<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-10">
	    	<div id="container_username" class="col-xs-4 col-sm-4 col-md-4 col-lg-4 hidden-xs hidden-sm">
	        Bienvenido:<label class="bg-primary"><?php echo $rs_checklogin['TX_user_seudonimo']; ?></label>
	      </div>
				<div id="navigation" class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
<?php			switch ($_COOKIE['coo_tuser']){
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
					} 	?>
				</div>
			</div>
		</div>
		<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<form method="post" name="form_purchase"  id="form_purchase" onsubmit="return false;">
<!--      #########################   TABLA PRODUCTOS   #######################   -->
				<div id="container_product" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pt_7">
				  <div id="container_filterproduct" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
					  <label class="label label_blue_sky"for="txt_filterproduct">Buscar por Producto:</label>
					  <input type="text" alt="table" class="form-control" id="txt_filterproduct" name="txt_filterproduct"  />
				  </div>
				  <div id="container_filter_limitproduct" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				    <label class="label label_blue_sky"for="txt_filterproduct">Mostrar:</label><br />
						<label class="radio-inline pt_7"><input type="radio" name="r_limit" id="r_limit" value="10" checked="checked"/> 10</label>
						<label class="radio-inline pt_7"><input type="radio" name="r_limit" id="r_limit" value="50" /> 50</label>
						<label class="radio-inline pt_7"><input type="radio" name="r_limit" id="r_limit" value="" /> Todas</label>
					</div>
					<div id="container_date" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div id="container_txtdateinitial" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							<label class="label label_blue_sky" for="txt_date_initial">F. Inicio</label>
							<input type="text" id="txt_date_initial" class="form-control" readonly="readonly" value="<?php echo $fecha_inicial; ?>" />
						</div>
						<div id="container_txtdatefinal" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							<label class="label label_blue_sky" for="txt_date_final">F. Final</label>
							<input type="text" id="txt_date_final" class="form-control" readonly="readonly" value="<?php echo $fecha_actual; ?>" />
						</div>
					</div>
					<div id="container_tblproduct" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<?php 			if($nr_product=$qry_product->num_rows != '0'){					?>
							<table id="tbl_product" border="0" class="table table-bordered table-hover table-condensed">
								<thead class="bg-primary">
									<tr>
										<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Codigo</th>
								    <th class="col-xs-6 col-sm-6 col-md-6 col-lg-6">Nombre</th>
								    <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Cantidad</th>
									</tr>
								</thead>
								<tfoot class="bg-primary"><tr><td colspan="3"></td></tr></tfoot>
								<tbody>
<?php					    foreach ($raw_producto as $key => $rs_product) {			?>
		    						<tr onclick="filter_psbyproduct('<?php echo $rs_product['AI_producto_id']; ?>');">
							        <td><?php echo $rs_product['TX_producto_codigo'] ?></td>
							        <td><?php echo $r_function->replace_special_character($rs_product['TX_producto_value']); ?></td>
		        					<td>
<?php								    if($rs_product['TX_producto_cantidad'] >= $rs_product['TX_producto_maximo']){
							            echo '<font style="color:#51AA51">'.$rs_product['TX_producto_cantidad'].'</font>';
								        }elseif($rs_product['TX_producto_cantidad'] <= $rs_product['TX_producto_minimo']){
							            echo '<font style="color:#C63632">'.$rs_product['TX_producto_cantidad'].'</font>';
								        }else{
							            echo '<font style="color:#000000">'.$rs_product['TX_producto_cantidad'].'</font>';
								        }
?>										</td>
		    						</tr>
<?php 						}; 			    ?>
						    </tbody>
					    </table>
<?php				} 				?>
					</div>
				</div>
<!--    ########################      TABS      ######################### -->
				<div id="container_tabs" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pt_7 bt_1">
					<ul class="nav nav-tabs">
					  <li class="active"><a data-toggle="tab" href="#purchased">Compras</a></li>
					  <li><a data-toggle="tab" href="#sold">Ventas</a></li>
					</ul>
				</div>
<!--    ########################      PURCHASED      ######################### -->

				<div class="tab-content">
					<div id="purchased" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding tab-pane fade in active" >
						<div id="container_facturacompra" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pt_7">
		    			<div id="container_filterfacturacompra" class="col-xs-9 col-sm-9 col-md-6 col-lg-6">
				        <label class="label label_blue_sky" for="txt_filterfacturacompra">Buscar por Factura de Compra:</label>
				        <input type="text" class="form-control" id="txt_filterfacturacompra" name="txt_filterfacturacompra" onkeyup="filter_facturacompra(this);" placeholder="Numero de Factura o Nombre de Proveedor"/>
					    </div>
							<div id="container_btnfiltercompra" class="col-xs-2 col-sm-2 col-md-2 col-lg-2 pt_14">
								<button type="button" id="btn_filtercompra" class="btn btn-success btn-search"><i class="fa fa-search" aria-hidden="true"></i></button>
								&nbsp;&nbsp;
								<button type="button" id="btn_printcompra" class="btn btn-info"><i class="fa fa-print" aria-hidden="true">&nbsp;</i>Imprimir</button>
							</div>
							<div id="container_tblfacturacompra" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					    	<table id="tbl_facturacompra" class="table table-bordered table-condensed table-striped">
					        <thead>
							      <tr class="bg-info">
							        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
							        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Nº de Fact.</th>
							        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Orden</th>
							        <th class="col-xs-6 col-sm-6 col-md-6 col-lg-6">Proveedor</th>
							        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Cantidad</th>
											<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
						        </tr>
						    	</thead>
					        <tfoot class="bg-info"><tr><td colspan="4"></td><td id="ttl_purchase"></td><td colspan="1"></td></tr></tfoot>
					        <tbody><tr><td colspan="6"></td></tr></tbody>
		        		</table>
		    			</div>
						</div>
						<div id="container_datofacturacompra" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 double_padding"></div>
						<div id="container_compradevolucion" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 double_padding"></div>
					</div>
	<!-- ###################             SOLD              ############## -->
					<div id="sold" class="container-fluid no_padding tab-pane fade">
						<div id="container_facturaf" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pt_7">
					    <div id="container_filterfacturaf" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
		        		<label class="label label_blue_sky"  for="txt_filterfacturaf">Buscar por Factura de Venta:</label>
		        		<input type="text" class="form-control" id="txt_filterfacturaf" name="txt_filterfacturaf" placeholder="Numero de Factura o Nombre de Cliente" />
		    			</div>
							<div id="container_btnfilterfacturaf" class="col-xs-4 col-sm-4 col-md-4 col-lg-4 pt_14">
								<button type="button" id="btn_filterfacturaf" class="btn btn-success btn-search"><i class="fa fa-search" aria-hidden="true"></i></button>
								&nbsp;&nbsp;
								<button type="button" id="btn_printfacturaf" class="btn btn-success"><i class="fa fa-print" aria-hidden="true">&nbsp;</i>Imprimir Consulta</button>
								&nbsp;&nbsp;
								<button type="button" id="btn_printfacturaf_total" class="btn btn-info"><i class="fa fa-print" aria-hidden="true">&nbsp;</i>Imprimir Totales</button>
							</div>
							<div id="container_tblfacturaf" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pt_7">
		    				<table id="tbl_facturaf" class="table table-bordered table-condensed table-striped">
		        			<thead>
						        <tr class="bg-info">
							        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Fecha</th>
							        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Nº de Factura</th>
							        <th class="col-xs-6 col-sm-6 col-md-6 col-lg-6">Cliente</th>
							        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
							        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Precio</th>
						        </tr>
							    </thead>
					        <tfoot class="bg-info"><tr><td colspan="3"></td><td colspan="2"><span id="span_ttl_sold"></span></td></tr></tfoot>
					        <tbody><tr><td colspan="5"></td></tr></tbody>
		        		</table>
								<table id="tbl_nc" class="table table-bordered table-condensed table-striped">
									<thead>
										<tr class="bg_red">
											<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Fecha</th>
											<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Nº de NC</th>
											<th class="col-xs-6 col-sm-6 col-md-6 col-lg-6">Cliente</th>
											<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
											<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Anulacion</th>
										</tr>
									</thead>
									<tfoot class="bg_red"><tr><td colspan="3"></td><td colspan="2"><span id="span_ttl_nc"></span></td></tr></tfoot>
									<tbody><tr><td colspan="5"></td></tr></tbody>
								</table>

					  	</div>
						</div>
						<div id="container_datofacturaf" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 double_padding"></div>
					</div>
					<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<button type="button" id="btn_back" class="btn btn-warning" onclick="history.back(1);">Volver</button>
					</div>
				</div>
			</form>
		</div>
		<div id="footer">
			<?php include 'attached/php/req_footer.php'; ?>
		</div>
	</div>
	<script type="text/javascript">
		<?php include 'attached/php/req_footer_js.php'; ?>
	</script>

</body>
</html>
