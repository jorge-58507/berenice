<?php

function reduce_merchandise(){
	include '../../bh_conexion.php';
	$link=conexion();

	$qry_producto=$link->query("SELECT AI_producto_id, TX_producto_codigo, TX_producto_value, TX_producto_cantidad FROM bh_producto WHERE TX_producto_activo = 0 ORDER BY TX_producto_value ASC LIMIT 5")or die($link->error);
	$raw_producto = array();
	while ($rs_producto=$qry_producto->fetch_array()) {
		$raw_producto[]=$rs_producto;
	}
	$contenido='
	<script type="text/javascript">
		function filter_reduce_product(str){
			$.ajax({data: {"a" : replace_regular_character(str) }, type: "GET", dataType: "text", url: "attached/get/filter_reduce_product.php",})
			.done(function( data, textStatus, jqXHR ) {
				$("#tbl_product_plus tbody").html(data);
				$("#tbl_product_minus tbody").html(data);
			})
			.fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD "+textStatus);	});
		}
	</script>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<table id="tbl_minus" class="table table-bordered table-hover ">
				<caption>Productos a Restar</caption>
				<thead class="bg_red">
					<tr>
						<th class="col-xs-9 col-sm-9 col-md-9 col-lg-9">Descripcion</th>
						<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Cantidad</th>
					</tr>
				</thead>
				<tbody>
					<tr><td colspan="2"></td></tr>
				</tbody>
				<tfoot class="bg_red"><tr><td colspan="2"></td></tr></tfoot>
			</table>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<table id="tbl_plus" class="table table-bordered table-hover ">
				<caption>Productos a Sumar</caption>
				<thead class="bg_green">
					<tr>
						<th class="col-xs-9 col-sm-9 col-md-9 col-lg-9">Descripcion</th>
						<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Cantidad</th>
					</tr>
				</thead>
				<tbody>
					<tr><td colspan="2"></td></tr>
				</tbody>
				<tfoot class="bg_green"><tr><td colspan="2"></td></tr></tfoot>
			</table>
		</div>
	</div>
	<div id="container_product" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label for="txt_filterproduct" class="label label_blue_sky">Buscar</label>
			<input type="text" id="txt_filterproduct" name="" placeholder="Codigo o Descripcion" onkeyup="filter_reduce_product(this.value);" class="form-control" value="">
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no_padding">
			<div id="container_tbl_product" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<table id="tbl_product_minus" class="table table-bordered table-hover ">
					<caption>Lista de Productos</caption>
					<thead class="bg-danger">
						<tr>
							<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Codigo</th>
							<th class="col-xs-7 col-sm-7 col-md-7 col-lg-7">Descripcion</th>
							<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Cantidad</th>
						</tr>
					</thead>
					<tbody>';
			foreach ($raw_producto as $key => $rs_producto) {
				$contenido .= '<tr onclick="plus_minus_item('.$rs_producto['AI_producto_id'].');"><td class="no_padding">'.$rs_producto['TX_producto_codigo'].'</td><td class="no_padding">'.$r_function->replace_special_character($rs_producto['TX_producto_value']).'</td><td class="no_padding">'.$rs_producto['TX_producto_cantidad'].'</td></tr>';
			}
			$contenido .= '
					</tbody>
					<tfoot class="bg-danger"><tr><td colspan="3"></tr></tfoot>
				</table>
			</div>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no_padding">
			<div id="container_tbl_product" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<table id="tbl_product_plus" class="table table-bordered table-hover ">
					<caption>Lista de Productos</caption>
					<thead class="bg-success">
						<tr>
							<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Codigo</th>
							<th class="col-xs-7 col-sm-7 col-md-7 col-lg-7">Descripcion</th>
							<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Cantidad</th>
						</tr>
					</thead>
					<tbody>';
			foreach ($raw_producto as $key => $rs_producto) {
				$contenido .= '<tr onclick="plus_minus_item('.$rs_producto['AI_producto_id'].');"><td class="no_padding">'.$rs_producto['TX_producto_codigo'].'</td><td class="no_padding">'.$r_function->replace_special_character($rs_producto['TX_producto_value']).'</td><td class="no_padding">'.$rs_producto['TX_producto_cantidad'].'</td></tr>';
			}
			$contenido .= '
					</tbody>
					<tfoot class="bg-success"><tr><td colspan="3"></tr></tfoot>
				</table>
			</div>
		</div>

		<div id="container_tbl_sold" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<table id="tbl_sold" class="table table-bordered table-hover ">
				<caption>Ultimas Ventas</caption>
				<thead class="bg-info">
					<tr>
						<th class="col-xs-9 col-sm-9 col-md-9 col-lg-9">Cliente</th>
						<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Cantidad</th>
					</tr>
				</thead>
				<tbody>
					<tr><td colspan="3"></td></tr>
				</tbody>
				<tfoot class="bg-info"><tr><td colspan="2"></td></tr></tfoot>
			</table>
		</div>
	</div>';

		echo $contenido;
}


$tool = $_GET['z'];
switch ($tool) {
	case 'reduce_merchandise':
		reduce_merchandise();
		break;
}
