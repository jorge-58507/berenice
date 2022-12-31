<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_stock.php';

$qry_product=$link->query("SELECT AI_producto_id, TX_producto_value, TX_producto_minimo, TX_producto_codigo, TX_producto_medida, TX_producto_alarma, TX_producto_maximo, TX_producto_cantidad, TX_producto_rotacion, TX_producto_referencia, TX_producto_activo, TX_producto_inventariado FROM bh_producto ORDER BY TX_producto_value ASC LIMIT 10 ");
$array_product = [];
while($rs_product = $qry_product->fetch_array(MYSQLI_ASSOC)){
	$array_product[] = $rs_product;
}

$qry_productgroup=$link->query("SELECT AI_productogrupo_id, TX_productogrupo_titulo, TX_productogrupo_json FROM bh_productogrupo ORDER BY TX_productogrupo_titulo ASC")OR DIE($link->error);
$array_productogrupo = [];
while ($rs_productgroup = $qry_productgroup->fetch_array(MYSQLI_ASSOC)) {
	$array_productogrupo[] = $rs_productgroup;
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
	<script type="text/javascript">
		$(document).ready(function() {
			$('[data-toggle="tooltip"]').tooltip({trigger:'hover', html: true}); 
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
						case '6':
							include 'attached/php/nav_assistant.php';
						break;
						case '7':
							include 'attached/php/nav_warehouse.php';
						break;
					}
?>
				</div>
			</div>
		</div>
		<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<form action="" method="post" name="form_sell"  id="form_sell" onsubmit="return false;">


				<div id="mng_top" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 pt_14">
						<label for="txt_product_filter" class="label label_blue_sky">Buscar Producto</label>
						<input type="text" id="txt_product_filter" autocomplete="off" class="form-control"  value="">
					</div>
					<div id="container_rlimit" class="col-xs-12 col-sm-6 col-md-2 col-lg-2 pt_14">
						<label class="label label_blue_sky" for="txt_rlimit">Mostrar:</label><br />
						<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="10"  checked="checked" /> 10</label>
						<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="50" /> 50</label>
						<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="200" /> 200</label>
						<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="" /> TODAS</label>
					</div>
					<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 pt_28">
						<button type="button" name="button" class="btn btn-primary" onclick="cls_management.set_modal()">Guardar Grupo</button>
					</div>
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 pt_28">
						<label class="label label_blue_sky prueba">Conteo</label>
						<label id="lbl_cb_count" class="switch" data-toggle="tooltip" title="<p style='font-size: 20px'>Marcar Conteo</p>" data-placement="bottom">
							<input id="cb_count" type="checkbox" onclick="cls_management.count_group()">
							<span class="slider_switch round"></span>
						</label>
					</div>
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 pt_28">
						<label class="label label_blue_sky">Activo</label>
						<label id="lbl_cb_disable" class="switch" data-toggle="tooltip" title="<p style='font-size: 20px'>Habilitar Productos</p>" data-placement="bottom">
							<input id="cb_disable" type="checkbox" onclick="cls_management.block_group()" >
							<span class="slider_switch round"></span>
						</label>
					</div>
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 pt_28">
						<label class="label label_blue_sky">Descontable</label>
						<label id="lbl_cb_discount" class="switch" data-toggle="tooltip" title="<p style='font-size: 20px'>Habilitar Descontar</p>" data-placement="bottom">
							<input id="cb_discount" type="checkbox" onclick="cls_management.discount_group()" >
							<span class="slider_switch round"></span>
						</label>
					</div>
					<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 pt_28">
						<button type="button" id="btn_set0" name="button" class="btn btn-info btn-lg" onclick="cls_management.set_to_cero()" data-toggle="tooltip" title="<p style='font-size: 20px'>Pasar a Cero</p>" data-placement="bottom">Set 0</button>
					</div>
				</div>
				<!-- The actual snackbar -->
				<div id="snackbar">Some text some message..</div>
				<!-- ##################### vv MODAL  ########################## -->
				<div id="mod_set_savegroup" class="col-xs-5 col-sm-5 col-md-5 col-lg-5 display_none">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 border_1 py_7">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center">
							<h4 style="color: #d55044"><strong>Nuevo Grupo de Productos</strong></h4>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no_padding">
							<label for="txt_group_title" class="label label_blue_sky">T&iacute;tulo</label>
							<input type="text" id="txt_group_title" class="form-control" name="" value="" placeholder="T&iacute;tulo">
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 pt_14">
							<button type="button" name="" class="btn btn-success" onclick="cls_management.save_group()">Aceptar</button>
							&nbsp;
							<button type="button" name="button" class="btn btn-warning" onclick="modal_out('mod_set_savegroup');">Cancelar</button>
						</div>
					</div>
				</div>
				<!-- ##################### ^^ MODAL  ########################## -->					
				<div id="mng_middle" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div id="container_tabs" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pt_7">
					<ul class="nav nav-tabs">
						<li class="active"><a data-toggle="tab" href="#product_list">Productos</a></li>
						<li><a data-toggle="tab" href="#group_list">Grupos</a></li>
					</ul>
				</div>
				<div class="tab-content">
					<div id="product_list" class="tab-pane fade in active no_padding">

						<div id="container_tblproduct" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<table id="tbl_product" class="table table-bordered table-condensed table-striped">
								<thead>
									<tr>
										<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4 bg-info">CODIGO</th>
										<th class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-info">DESCRIPCION</th>
									</tr>
								</thead>
								<tfoot><tr><td colspan="2" class=" bg-info"></td></tr></tfoot>
								<tbody>
	<?php						foreach($array_product as $key => $rs_product){
										$product_description = $rs_product['TX_producto_value'];
										$product_code = $rs_product['TX_producto_codigo'];
										$product_id = $rs_product['AI_producto_id']; ?>
										<tr onclick='cls_management.pick_line("<?php echo $product_id; ?>","<?php echo $product_code; ?>","<?php echo $r_function->replace_regular_character($product_description) ?>")'>
											<td><?php echo $rs_product['TX_producto_codigo']; ?></td>
											<td><?php echo $r_function->replace_special_character($product_description); ?></td>
										</tr>
	<?php							if ($key === 9) {	break;	}
									} ?>
								</tbody>
							</table>
						</div>
					</div>
					<div id="group_list" class="tab-pane fade in no_padding">
						<div id="container_tblproduct" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<table id="tbl_productgroup" class="table table-bordered table-condensed table-striped">
								<thead>
									<tr>
										<th class="col-xs-11 col-sm-11 col-md-11 col-lg-11 bg-primary">TITULO</th>
										<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 bg-primary"></th>
									</tr>
								</thead>
								<tfoot><tr><td class=" bg-primary" colspan="2"></td></tr></tfoot>
								<tbody>
	<?php						foreach($array_productogrupo as $key => $rs_group){ ?>
										<tr>
											<td class="al_center"  onclick='cls_management.pick_group("<?php echo $rs_group['AI_productogrupo_id']; ?>")'>
												<?php echo $r_function->replace_special_character($rs_group['TX_productogrupo_titulo']); ?>
											</td>
											<td class="al_center">
												<button type="button" class="btn btn-danger btn-sm" onclick="cls_management.del_group(<?php echo $rs_group['AI_productogrupo_id']; ?>)"><i class="fa fa-times"></i></button>
											</td>
										</tr>
	<?php						} ?>
								</tbody>
							</table>
						</div>							
					</div>
				</div>


					
					<!-- TABLE GROUP -->
					<div id="container_tblgroup" class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<table id="tbl_group" class="table table-bordered table-condensed table-striped">
							<thead>
								<tr>
									<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4 bg-primary">CODIGO</th>
									<th class="col-xs-7 col-sm-7 col-md-7 col-lg-7 bg-primary">DESCRIPCION</th>
									<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 bg-primary"></th>
								</tr>
							</thead>
							<tfoot><tr><td colspan="3" class=" bg-primary"></td></tr></tfoot>
							<tbody>
								<tr><td colspan="3"></td></tr>
							</tbody>
						</table>
					</div>

				</div>
				<?php // include 'attached/php/inc_payroll_management.php'; ?>
			</form>
		</div>
		<div id="footer">
			<?php require 'attached/php/req_footer.php'; ?>
		</div>
	</div>
	<script type="text/javascript" src="attached/js/stock_funct.js"></script>	
	<script type="text/javascript">
		const cls_management = new product_management;
		<?php require 'attached/php/req_footer_js.php'; ?>

		document.getElementById('txt_product_filter').addEventListener('keyup', function() {
			cls_management.filter_product(this.value);
		});
	</script>
</body>
</html>
