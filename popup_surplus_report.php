<?php
require 'bh_conexion.php';
$link=conexion();
set_time_limit(240);

$date_abstract = date('Y-m-d', strtotime($_GET['date']));

$prep_datoventa = $link->prepare("SELECT bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_descuento, bh_datoventa.datoventa_AI_producto_id, 
bh_datoventa.TX_datoventa_medida, bh_datoventa.TX_datoventa_cantidad, bh_producto.TX_producto_value, bh_producto.TX_producto_codigo
FROM bh_facturaventa 
INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id 
INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datoventa.datoventa_AI_producto_id
WHERE bh_facturaventa.facturaventa_AI_facturaf_id = ?")or die($link->error);
$prep_datoventa->bind_param('i',$facturaf_id);

$prep_datocompra = $link->prepare("SELECT bh_datocompra.TX_datocompra_precio, bh_datocompra.TX_datocompra_descuento, bh_datocompra.TX_datocompra_descuento, bh_datocompra.TX_datocompra_medida
FROM bh_datocompra 
WHERE datocompra_AI_producto_id = ? ORDER BY AI_datocompra_id DESC LIMIT 1")or die($link->error);
$prep_datocompra->bind_param('i',$product_id);

$prep_productomedida = $link->prepare("SELECT rel_producto_medida.TX_rel_productomedida_cantidad 
FROM rel_producto_medida 
WHERE rel_producto_medida.productomedida_AI_producto_id = ? AND rel_producto_medida.productomedida_AI_medida_id = ?")or die($link->error);
$prep_productomedida->bind_param('ii',$product_id,$medida);

$txt_facturaf="SELECT AI_facturaf_id, TX_facturaf_subtotalni, TX_facturaf_descuentoni, TX_facturaf_subtotalci, TX_facturaf_descuento, TX_facturaf_impuesto
FROM bh_facturaf
INNER JOIN bh_facturaventa ON bh_facturaventa.facturaventa_AI_facturaf_id = bh_facturaf.AI_facturaf_id
WHERE  TX_facturaf_fecha = '$date_abstract'";
$qry_facturaf=$link->query($txt_facturaf)or die($link->error);
$surplus_per_facturaventa = 0; $raw_noinfo = array(); $raw_surplus = array();
while ($rs_facturaf = $qry_facturaf->fetch_array(MYSQLI_ASSOC)) {
	
	$facturaf_id = $rs_facturaf['AI_facturaf_id'];
	$prep_datoventa->execute(); $qry_datoventa = $prep_datoventa->get_result();

	while($rs_datoventa = $qry_datoventa->fetch_array(MYSQLI_ASSOC)){
		$product_id = $rs_datoventa['datoventa_AI_producto_id'];
		// VENTA
		$medida = $rs_datoventa['TX_datoventa_medida'];
		$prep_productomedida->execute(); $qry_productomedida = $prep_productomedida->get_result(); $rs_productomedida = $qry_productomedida->fetch_array(MYSQLI_ASSOC);
		$sale_productomedida = $rs_productomedida['TX_rel_productomedida_cantidad'];
		$sold_price = $rs_datoventa['TX_datoventa_precio']-(($rs_datoventa['TX_datoventa_descuento']*$rs_datoventa['TX_datoventa_precio'])/100);
		// COMPRA
		$prep_datocompra->execute(); $qry_datocompra = $prep_datocompra->get_result(); $rs_datocompra = $qry_datocompra->fetch_array(MYSQLI_ASSOC);
		$last_cost = $rs_datocompra['TX_datocompra_precio']-(($rs_datocompra['TX_datocompra_descuento']*$rs_datocompra['TX_datocompra_precio'])/100);
		$medida = $rs_datocompra['TX_datocompra_medida'];
		$prep_productomedida->execute(); $qry_productomedida = $prep_productomedida->get_result(); $rs_productomedida = $qry_productomedida->fetch_array(MYSQLI_ASSOC);
		if ($qry_productomedida->num_rows > 0) {
			$cost_productomedida = $rs_productomedida['TX_rel_productomedida_cantidad'];			
			$rel_cost_sale = $sale_productomedida/$cost_productomedida; 
			$cost_price = $last_cost*$rel_cost_sale;
			$surplus_per_facturaventa += ($sold_price-$cost_price)*$rs_datoventa['TX_datoventa_cantidad'];

			$raw_surplus[]=["last_cost"=>$last_cost,
			"rel_measure"=>$rel_cost_sale,
			"cost_measure"=>$cost_price,
			"sold_price"=>$sold_price,
			"quantity"=>$rs_datoventa['TX_datoventa_cantidad'],
			"surplus"=>$surplus_per_facturaventa,
			"code"=>$rs_datoventa['TX_producto_codigo'],
			"description"=>$rs_datoventa['TX_producto_value']			
		];
		}else{
			$rel_cost_sale = 0;
			$cost_price = 0;
			$raw_noinfo[$rs_datoventa['datoventa_AI_producto_id']] = ["code" => $rs_datoventa['TX_producto_codigo'],"description" => $rs_datoventa['TX_producto_value']];

		}		
	}
	
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
	<link href="attached/css/popup_css.css" rel="stylesheet" type="text/css" />
	<link href="attached/css/jquery-ui.css" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="attached/js/jquery.js"></script>
	<script type="text/javascript" src="attached/js/jquery-ui.min_edit.js"></script>
	<script type="text/javascript" src="attached/js/bootstrap.js"></script>
	<script type="text/javascript" src="attached/js/general_funct.js"></script>
	<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
	<script type="text/javascript" src="attached/js/validCampoFranz.js"></script>
	<script type="text/javascript">
	$(document).ready(function() {
		$( function() {
			$("#txt_date").datepicker({
				changeMonth: true,
				changeYear: true
			});
		});
		$("#txt_date").on("change",function(){
			document.location = `popup_surplus_report.php?date=${this.value}`;
		})

	});
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
			<form action="pop_form.php" method="post" name="form" id="form">
				<div id="" class="col-xs-3 col-sm-3 col-md-3 col-lg-3"></div>
				<div id="" class="col-xs-3 col-sm-3 col-md-3 col-lg-3 al_right">
					<h4>Resumen del </h4>
				</div>
				<div id="" class="col-xs-3 col-sm-3 col-md-3 col-lg-3 al_left no_padding_left">
					<input type="text" id="txt_date" class="form-control" readonly="readonly" value="<?php echo date('d-m-Y', strtotime($date_abstract)); ?>" />
				</div>
				<div id="" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center">
					<div class="border_1 bg-info font_bolder col-xs-12">
						<h3>Ganancia Bruta</h3>
					</div>
					<div class="border_1">
						<h4>B/ <?php echo number_format($surplus_per_facturaventa,2); ?></h4>
					</div>

					<div class="border_1 bg-info font_bolder col-xs-12">
						<h3>Reporte</h3>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-info no_padding font_bolder">
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 border_1">
							C&oacute;digo
						</div>
						<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 border_1">
							Compra
						</div>
						<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 border_1">
							Rel.
						</div>
						<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 border_1">
							Costo/Md
						</div>
						<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 border_1">
							Venta
						</div>
						<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 border_1">
							Cant
						</div>
						<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 border_1">
							Ganancia
						</div>
					</div>
<?php			foreach ($raw_surplus as $key => $rs_product) { ?>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
							<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 border_1" title="<?php echo $r_function->replace_special_character($rs_product['description']); ?>">
								<?php echo $rs_product['code']; ?>
							</div>
							<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2  border_1">
								<?php echo number_format($rs_product['last_cost'],2); ?>
							</div>
							<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 border_1">
								<?php echo round($rs_product['rel_measure'],2); ?>
							</div>
							<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 border_1">
								<?php echo number_format($rs_product['cost_measure'],2); ?>
							</div>
							<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 border_1">
								<?php echo number_format($rs_product['sold_price'],2); ?>
							</div>
							<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 border_1">
								<?php echo round($rs_product['quantity'],2); ?>
							</div>
							<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2  border_1">
								<?php echo number_format(($rs_product['sold_price']-$rs_product['cost_measure'])*$rs_product['quantity'],2); ?>
							</div>
						</div>
<?php			}	?>

					<div class="border_1 bg-info font_bolder col-xs-12">
						<h3>Productos Sin Informaci&oacute;n</h3>
					</div>
<?php			foreach ($raw_noinfo as $key => $rs_product) { ?>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 border_1">
							<?php echo $rs_product['code']; ?>
						</div>
						<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 border_1">
							<?php echo $r_function->replace_special_character($rs_product['description']); ?>
						</div>
<?php			}	?>

				</div>


			</form>
		</div>


		<div id="footer">
			<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
		&copy; Derechos Reservados a: Jorge Salda&nacute;a <?php echo date('Y'); ?>
			</div>
		</div>
	</div>

</body>
</html>
