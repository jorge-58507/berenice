<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_stock.php';

$qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida WHERE TX_medida_value = 'UNIDADES' ORDER BY TX_medida_value ASC");
$rs_medida=$qry_medida->fetch_array(MYSQLI_ASSOC);
$qry_itbm=$link->query("SELECT TX_opcion_value FROM bh_opcion WHERE TX_opcion_titulo = 'IMPUESTO'")or die($link->error);
$row_itbm=$qry_itbm->fetch_array();
$itbm = $row_itbm[0];
$qry_letter=$link->query("SELECT AI_letra_id, TX_letra_value, TX_letra_porcentaje FROM bh_letra")or die ($link->error);

// $qry_checkbeneath=$link->query("SELECT AI_producto_id FROM bh_producto WHERE TX_producto_cantidad < TX_producto_minimo AND TX_producto_alarma = '0'");
// $nr_checkbeneath=$qry_checkbeneath->num_rows;

// $qry_checkreport=$link->query("SELECT AI_reporte_id FROM bh_reporte WHERE TX_reporte_tipo = 'INVENTARIO' AND TX_reporte_status = 'ACTIVA'");
// $nr_checkreport=$qry_checkreport->num_rows;
// if($nr_checkreport > 0){ $value_button="Reporte <span class='badge'>".$nr_checkreport."</span>"; }else{ $value_button="Reporte"; }

// $prep_facturaventa=$link->prepare("SELECT bh_facturaventa.AI_facturaventa_id FROM (bh_datoventa INNER JOIN bh_facturaventa ON bh_datoventa.datoventa_AI_facturaventa_id = bh_facturaventa.AI_facturaventa_id) WHERE bh_datoventa.datoventa_AI_producto_id = ?")or die($link->error);
// $prep_facturacompra=$link->prepare("SELECT bh_facturacompra.AI_facturacompra_id FROM (bh_datocompra INNER JOIN bh_facturacompra ON bh_datocompra.datocompra_AI_facturacompra_id = bh_facturacompra.AI_facturacompra_id) WHERE bh_datocompra.datocompra_AI_producto_id = ?")or die($link->error);

// $prep_facturaventa->bind_param("i", $product_id); 	/* <----BOTON ELIMINAR 	*/
// $prep_facturacompra->bind_param("i", $product_id); 	/* <----BOTON ELIMINAR 	*/
	
// $qry_product=$link->query("SELECT AI_producto_id, TX_producto_value, TX_producto_minimo, TX_producto_codigo, TX_producto_medida, TX_producto_alarma, TX_producto_maximo, TX_producto_cantidad, TX_producto_rotacion, TX_producto_referencia, TX_producto_activo, TX_producto_inventariado FROM bh_producto ORDER BY TX_producto_value ASC LIMIT 0 ")or die($link->error);
// $raw_stock_product = array();
// while($rs_product=$qry_product->fetch_array(MYSQLI_ASSOC)){
// 	$raw_stock_product[$rs_product['AI_producto_id']] = $rs_product;
// 	$product_id = $rs_product['AI_producto_id'];
// 	$raw_stock_product[$rs_product['AI_producto_id']]['btn_del_product'] = 1;
// 	$prep_facturacompra->execute(); $qry_facturacompra = $prep_facturacompra->get_result(); 
// 	if ($qry_facturacompra->num_rows > 0) {	$raw_stock_product[$rs_product['AI_producto_id']]['btn_del_product'] = 0;	}
// 	$prep_facturaventa->execute(); $qry_facturaventa = $prep_facturaventa->get_result();
// 	if ($qry_facturaventa->num_rows > 0) {	$raw_stock_product[$rs_product['AI_producto_id']]['btn_del_product'] = 0;	}
// }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="sr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Trilli, S.A. - Todo en Materiales</title>
	<?php include 'attached/php/req_required.php'; ?>
	<link href="attached/css/stock_css.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div id="logo_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-2" >
				<div id="logo" ></div>
			</div>
			<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-12 col-lg-10">
				<div id="container_username" class="col-lg-4  visible-lg">
					Bienvenido: <label class="bg-primary"><?php echo $rs_checklogin['TX_user_seudonimo']; ?></label>
				</div>
				<div id="navigation" class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
<?php				switch ($_COOKIE['coo_tuser']){
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
		<div id="snackbar"></div>
<!-- ##################### vv MODAL  ########################## -->
			<!-- <div id="mod_set_savegroup" class="col-xs-5 col-sm-5 col-md-5 col-lg-5 display_none">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 border_1 py_7">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center">
						<h4 style="color: #d55044"><strong>Nuevo Productos</strong></h4>
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
			</div> -->
<!-- ##################### ^^ MODAL  ########################## -->					
			<form name="form_inventory" id="form_inventory" method="post">
				<div id="container_create_product" class="col-xs-12 col-sm-12 col-md-8 col-lg-8 display_none" >
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<label class="label label_blue_sky"  class="label label-primary" for="txt_nombre">Descripci&oacute;n:</label>
							<input type="text" class="form-control input-sm" id="txt_nombre" name="txt_nombre" onblur="setUpperCase(this);" />
					    </div>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pt_7">
							<label class="label label_blue_sky"  class="label label-primary"  for="txt_referencia">Referencia:</label>
							<input type="text" class="form-control input-sm" id="txt_referencia" name="txt_referencia" onblur="setUpperCase(this);">
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 pt_7">
							<input type="hidden" class="form-control  input-sm" id="txt_cantidad" name="txt_cantidad" value="0">
							<label class="label label_blue_sky"  for="sel_subfamilia">Subfamilia</label>
<?php 						$qry_subfamilia = $link->query("SELECT bh_subfamilia.AI_subfamilia_id, bh_subfamilia.TX_subfamilia_value, bh_familia.TX_familia_value
															FROM (bh_subfamilia
															INNER JOIN bh_familia ON bh_familia.AI_familia_id = bh_subfamilia.subfamilia_AI_familia_id)
															ORDER BY subfamilia_AI_familia_id ASC")or die($link->error); 		?>
							<select  class="form-control input-sm" id="sel_subfamilia" name="sel_subfamilia" tabindex="9">
<?php 					$group = '';
								while($rs_subfamilia=$qry_subfamilia->fetch_array(MYSQLI_ASSOC)){
									if ($rs_subfamilia['TX_familia_value'] != $group) {
										echo "</optgroup><optgroup label=".$rs_subfamilia['TX_familia_value'].">";
										$group=$rs_subfamilia['TX_familia_value'];
										if ($rs_subfamilia['AI_subfamilia_id'] === $rs_product['producto_AI_subfamilia_id']) { 				?>
											<option value="<?php echo $rs_subfamilia['AI_subfamilia_id']; ?>" selected="selected"><?php echo $rs_subfamilia['TX_subfamilia_value']; ?></option>
<?php 							}else{			?>
											<option value="<?php echo $rs_subfamilia['AI_subfamilia_id']; ?>"><?php echo $rs_subfamilia['TX_subfamilia_value']; ?></option>
<?php 							}
									}else{
										if ($rs_subfamilia['AI_subfamilia_id'] === $rs_product['producto_AI_subfamilia_id']) { 				?>
											<option value="<?php echo $rs_subfamilia['AI_subfamilia_id']; ?>" selected="selected"><?php echo $rs_subfamilia['TX_subfamilia_value']; ?></option>
<?php 							}else{			?>
											<option value="<?php echo $rs_subfamilia['AI_subfamilia_id']; ?>"><?php echo $rs_subfamilia['TX_subfamilia_value']; ?></option>
<?php 							}
									}
								} 	?>
							</select>
						</div>
						<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 pt_7">
							<label class="label label_blue_sky"  class="label label-primary"  for="txt_codigo">Codigo:</label>
							<input type="text" class="form-control input-sm" id="txt_codigo" name="txt_codigo" onblur="setUpperCase(this);">
						</div>
						<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 side-btn-sm-label ">
							<button type="button" class="btn btn-sm btn-success pt_14" onclick="generate_code();"><i class="fa fa-file-text"></i></button>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 alert alert-danger display_none" id="container_product_recall"></div>
						</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 pt_7">
							<label class="label label_blue_sky"  for="sel_medida">Medida:</label>
							<select  class="form-control input-sm" id="sel_medida" name="sel_medida">
<?php						do{							?>
									<option value="<?php echo $rs_medida['AI_medida_id']; ?>"><?php echo $rs_medida['TX_medida_value']; ?></option>
<?php						}while($rs_medida=$qry_medida->fetch_array(MYSQLI_ASSOC));	?>
							</select>
    				</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 pt_7">
							<label class="label label_blue_sky"  for="txt_impuesto">Impuesto %:</label><br />
							<input type="text" class="form-control input-sm" id="txt_impuesto" name="txt_impuesto" value="<?php echo $itbm; ?>">
						</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 pt_7">
							<label class="label label_blue_sky"  for="sel_letter">Letra:</label><br />
							<select id="sel_letter" class="form-control input-sm">
<?php 							while($rs_letter=$qry_letter->fetch_array(MYSQLI_ASSOC)){ ?>
									<option value="<?php echo $rs_letter['AI_letra_id']; ?>"><?php echo $rs_letter['TX_letra_value']." (".$rs_letter['TX_letra_porcentaje']."%)"; ?></option>
<?php 							} 	?>
							</select>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
							<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 pt_7">
								<label class="label label_blue_sky"  for="txt_cantminima">Cant. M&iacute;nima:</label>
								<input type="text" class="form-control input-sm" id="txt_cantminima" name="txt_cantminima">
							</div>
							<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 pt_7">
								<label class="label label_blue_sky"  for="txt_cantmaxima">Cant. M&aacute;xima:</label>
								<input type="text" class="form-control input-sm" id="txt_cantmaxima" name="txt_cantmaxima">
							</div>
						</div>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<span id="span_title" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><h4><strong>Precios</strong></h4></span>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 py_7 pl_0">
								<label class="label label_blue_sky"  for="txt_p_4">Standard:</label>
								<input type="text" class="form-control input-sm" id="txt_p_4" name="txt_p_4">
							</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 py_7 px_0">
								<label class="label label_blue_sky"  for="txt_p_5">Precio M&aacute;ximo:</label>
								<input type="text" class="form-control input-sm" id="txt_p_5" name="txt_p_5">
							</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 py_7 pl_0">
								<label class="label label_blue_sky"  for="txt_p_3">Descuento #1:</label>
								<input type="text" class="form-control input-sm" id="txt_p_3" name="txt_p_3">
							</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 py_7 px_0">
								<label class="label label_blue_sky"  for="txt_p_2">Descuento #2:</label>
								<input type="text" class="form-control input-sm" id="txt_p_2" name="txt_p_2">
							</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 py_7 pl_0">
								<label class="label label_blue_sky"  for="txt_p_1">Descuento #3:</label>
								<input type="text" class="form-control input-sm" id="txt_p_1" name="txt_p_1">
							</div>
						</div>
					</div>
					<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
						<button type="button" name="btn_save_product" id="btn_save_product" class="btn btn-success">Nuevo Producto</button>
						&nbsp;
						<button type="button" name="btn_clean_product" id="btn_clean_product" class="btn btn-warning">Limpiar Campos</button>
					</div>
				</div>
				<div id="container_div_newproduct" class="bt_1" >
					<div id="div_newproduct" class="fa fa-angle-double-down container_create_product"> Nvo. Articulo</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
					<div id="container_btn_purchase" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<button type="button" name="btn_new_entry" id="btn_purchase" class="btn btn-default btn-lg" >Compras</button>
						&nbsp;
						<button type="button" name="btn_reg_entry" id="btn_reg_entry" class="btn btn-info btn-lg" >Pedidos</button>
						&nbsp;
						<button type="button" name="btn_qry_entry" id="btn_qry_entry" class="btn btn-primary btn-lg" >Reportes</button>
						<!-- &nbsp; -->
						<!-- <button type="button" name="btn_qry_report" id="btn_qry_report" class="btn btn-warning btn-lg" ><?php echo $value_button; ?></button> -->
						&nbsp;
						<button type="button" name="btn_admincuentaxpagar" id="btn_admincuentaxpagar" class="btn btn-info btn-lg"><strong>Proveedores</strong></button>
						&nbsp;
						<button type="button" name="btn_productgestion" id="btn_productgestion" class="btn btn-default btn-lg">Gestion Grupal</button>
					</div>
				</div>
				<div id="container_alert" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<?php /*				if($nr_checkbeneath > 0){ ?>
						<div class="alert alert-danger alert-dismissable fade in">
							<a href="#" onclick="filter_beneath();" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<strong>Atenci&oacute;n!</strong> Hay productos con baja existencia.
						</div>
<?php 				}	*/?>
				</div>
				<div id="container_filterproduct" class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
					<label class="label label_blue_sky"  for="txt_filterproduct">Buscar Descripci&oacute;n:</label>
					<input type="text" autofocus class="form-control" id="txt_filterproduct" name="txt_filterproduct" autocomplete="off" placeholder="Escriba Descripcion o Referencia..." />
				</div>
				<div id="container_filterprovider" class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
					<label class="label label_blue_sky"  for="txt_filterprovider">Buscar Proveedor:</label>
					<input type="text" class="form-control" id="txt_filterprovider" name="txt_filterprovider" autocomplete="off" placeholder="Escriba Nombre del Proveedor..." />
				</div>
				<div id="container_filterbutton" class="col-xs-9 col-sm-9 col-md-3 col-lg-3 display_none">
					<label class="label label_blue_sky"  class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Ver:</label>
					<button type="button" id="btn_alarm_on" name="btn_alarm_off" class="btn btn-warning btn-xs">Alarma ON</button>
					&nbsp;&nbsp;
					<button type="button" id="btn_inactive" name="btn_inactive" class="btn btn-warning btn-xs">Inactivo</button>
				</div>
				<div id="container_div_expand_filterbutton" class="col-xs-2 col-sm-2 col-md-1 col-lg-1" >
					<div id="div_expand_filterbutton" class="fa fa-angle-double-right"></div>
				</div>
				<div id="container_rlimit" class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
					<label class="label label_blue_sky" for="txt_rlimit">Mostrar:</label><br />
					<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="10"  checked="checked" /> 10</label>
					<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="50" /> 50</label>
					<label class="radio-inline"><input type="radio" name="r_limit" id="r_limit" value="200" /> 200</label>
				</div>
				<div id="container_tblproduct" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 load-hidden">
					<table id="tbl_product" border="0" class="table table-bordered table-hover table-condensed table-striped">
						<thead class="bg-primary">
							<tr>
								<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Codigo</th>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Referencia</th>
								<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Nombre</th>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Cantidad</th>
								<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3"></th>
								<!-- <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
								<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th> -->
							</tr>
						</thead>
						<tfoot class="bg-primary">
							<tr><td colspan="5"></td></tr>
						</tfoot>
						<tbody class="">
							<tr><td colspan="5"></td></tr>
						</tbody>
					</table>	
				</div>
			</form>
		</div>
		<div id="footer">
			<?php require 'attached/php/req_footer.php'; ?>
		</div>
	</div>
	<script type="text/javascript">
		<?php include 'attached/php/req_footer_js.php'; ?>
	</script>
<!--    ############## SCRIPTS ##############     -->
	<script type="text/javascript" src="attached/js/stock_funct.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			// const cls_stock = new class_stock;
			$(window).on('beforeunload',function(){	close_popup();	});
			$("#txt_p_1, #txt_p_2, #txt_p_3, #txt_p_4, #txt_p_5").on("blur",function(){
				this.value = val_intw2dec(this.value);
			})
			setTimeout("upd_btn_report()",60000)
			$('#btn_save_product').click(function(){
				if($("#txt_nombre").val() === "" || $("#txt_codigo").val() === "" || $("#txt_medida").val() === "" || $("#txt_cantminima").val() === "" || $("#txt_cantmaxima").val() === ""){
					alert("faltan datos");
					return false;
				}
				if($('#txt_p_4').val() === ""){
					$('#txt_p_4').val('0.00');
				}
				if($('#txt_cantidad').val() === ""){
					$('#txt_cantidad').val('0.00');
				}
				ans = val_intwdec($('#txt_p_4').val());
				if(!ans){	return false;	}
				ans = val_intwdec($('#txt_cantidad').val());
				if(!ans){	return false;	}
				setTimeout(()	=>	{
					cls_stock.add_product()
				},250);
			})

			$('#btn_clean_product').click(function(){
				$('#txt_codigo, #txt_nombre, #txt_cantminima, #txt_cantmaxima, #txt_referencia, #txt_p_1, #txt_p_2, #txt_p_3, #txt_p_4, #txt_p_5').val("");
			})

			$('#txt_cantidad').validCampoFranz('.0123456789');
			$('#txt_cantminima, #txt_cantmaxima, #txt_impuesto').validCampoFranz('-.0123456789');
			$('#txt_p_1, #txt_p_2, #txt_p_3, #txt_p_4, #txt_p_5').validCampoFranz('.0123456789');
			$("#btn_qry_entry").click(function(){
				window.location='filter_byproduct.php';
			});
			$("#btn_reg_entry").click(function(){
				window.location='order.php';
			});
			$("#btn_purchase").on("click",function(){
				window.location='purchase.php';
			})
			$("#btn_admincuentaxpagar").on("click",function(){
				window.location='admin_provider.php';
			});
			$("#btn_productgestion").on("click",function(){
				window.location='product_management.php';
			});
			$("#div_newproduct").click(function(){
				$("#container_create_product").toggle(200);
				$("#div_newproduct").toggleClass("fa-angle-double-down");
				$("#div_newproduct").toggleClass("fa-angle-double-up");
			});

			const FranzNum = '0123456789'; const FranzLetter = 'abcdefghijklmnñopqrstuvwxyz'; const FranzPairs = '()[]{}!¡¿?/'; const FranzSimbol = '-º\'#/"'; const FranzPunctuation = ',;.:';
			$('#txt_codigo').validCampoFranz("-"+FranzNum+FranzLetter);
			$('#txt_nombre, #txt_referencia').validCampoFranz(" "+FranzLetter+FranzNum+FranzPairs+FranzPunctuation+FranzSimbol);

			$("#btn_alarm_on").click(function(){
				var limit = ($("input[name=r_limit]:checked").val());
				$.ajax({	data: { "a": limit,	"z": "productAlarmOn"},	type: "GET",	dataType: "text",	url: "attached/get/get_product.php", })
				.done(function( data, textStatus, jqXHR ) {	
					
					var raw_data = JSON.parse(data);
					$("#tbl_product tbody").html(cls_stock.render_table_product(raw_data));
				
				})
				.fail(function( jqXHR, textStatus, errorThrown ) {		});


		$.ajax({	data: {	"a" : id, "z": "compraByProduct"},	type: "GET",	dataType: "text",	url: "attached/get/get_product.php", })
	 	.done(function( data, textStatus, jqXHR ) {
		 console.log("GOOD " + textStatus);
		 $("#hd_product_id").val(id);
		 $("#hd_product_codigo").val(codigo);
		 $("#hd_product_nombre").val(detalle);
		 $("#hd_product_impuesto").val(impuesto);
		 $("#container_product_name").html(replace_special_character(detalle));
		 $("#container_orderinfo").show(500);

		 var table = '<table class=" table table-bordered table-sm col-xs-12 col-sm-12 col-md-12 col-lg-12"><caption>Historial de Precio</caption><thead><tr><th>Fecha</th><th>Medida</th><th>Costo</th></tr></thead><tfoot><tr><td colspan="3"></td></tr></tfoot><tbody>';
		 obj_data = JSON.parse(data);
		if (obj_data.length > 0) {
			for (const a in obj_data) {
				 var precio = Math.round10(obj_data[a]['precio'],-1);
				 table += `<tr><td>${convertir_formato_fecha(obj_data[a]['fecha'])}</td><td>${obj_data[a]['medida']}</td><td>${precio.toFixed(2)}</td></tr>`;
			}
			table += '</tbody></table>';
		}
		 document.getElementById('container_historical').innerHTML = table;
		 $("#txt_quantity").focus();
	 })
	 .fail(function( jqXHR, textStatus, errorThrown ) {	console.log("BAD " + textStatus);	});




			});
			$("#btn_inactive").click(function(){
				$.ajax({	data:"",type:"GET", dataType:"text",url:"attached/get/filter_product_inactive.php"	})
				.done(function(data, textStatus, jqXHR){	$("#container_tblproduct").html(data);	})
				.fail(function(data, textStatus, errorThrown){	});
			});
			$("#div_expand_filterbutton").click(function(){
				$("#container_filterbutton").toggle(500);
				$("#div_expand_filterbutton").toggleClass("fa-angle-double-right");
				$("#div_expand_filterbutton").toggleClass("fa-angle-double-left");
			});
			$("#txt_codigo").on("blur", function(){
				if(this.value.length == '6'){
					this.value = "0000000"+this.value;
				}
			});
			$("input[name=r_limit]").on("change",function(){
				$("#txt_filterproduct").keyup();
			})
			var intervalo;
			$("#txt_filterproduct").on("keyup",function(){
				clearInterval(intervalo);
				intervalo = setInterval(
					() => {
						cls_stock.filter_product();
						clearInterval(intervalo);
					}, 700);
			});
		// filter 4 codes new product
			$( function() {
				$("#txt_codigo").autocomplete({
					source: "attached/get/filter_producto_codigo.php",
					minLength: 2,
					select: function( event, ui ) {
						splited_value = ui.item.value.split(" | ");
						new_value = splited_value[0];
						ui.item.value = new_value;
						fire_recall('container_product_recall', '¡Atencion!, Codigo a duplicar')
					}
				});
			});
		// filter provider
			$( function() {
				$( "#txt_filterprovider").autocomplete({
					source: "attached/get/filter_stock_provider.php",
					minLength: 2,
					select: function( event, ui ) {
						var n_val = ui.item.value;
						filter_stock_product_by_provider(ui.item.id);
					}
				});
			});
		// filter n_description
			$( function() {
				$( "#txt_nombre").autocomplete({
					source: "attached/get/filter_stock_description.php",
					minLength: 2,
					select: function( event, ui ) {
					}
				});
			});
			$("#btn_qry_report").on("click",function(){
				open_popup('popup_stock_report.php','popup_stock_report','600','425');
			});
		});
		function upd_btn_report(){
			$.ajax({	data:"",type:"GET", dataType:"text",url:"attached/get/get_btn_report.php"	})
			.done(function(data, textStatus, jqXHR){
				$("#btn_qry_report").html(data);
			})
			.fail(function(data, textStatus, errorThrown){	});

			setTimeout("upd_btn_report()",60000)
		}
		function filter_stock_product_by_provider(provider_id){
			data = {"a":provider_id}
			url_data = data_fetch(data);
			var myRequest = new Request(`attached/get/filter_stock_productbyprovider.php${url_data}`);
			fetch(myRequest)
			.then(function(response) {
				return response.text()
				.then(function(text) {
					document.getElementById('tbl_product').tBodies[0].innerHTML = text;
				});
			});
		}
		function generate_code () {
			var subfamily = document.getElementById('sel_subfamilia').value;
			data = {"a":subfamily}
			url_data = data_fetch(data);
			var myRequest = new Request(`attached/get/code_generator.php${url_data}`);
			fetch(myRequest)
			.then(function(response) {
				return response.text()
				.then(function(text) {
					document.getElementById('txt_codigo').value = text;
				});
			});
		}
		const cls_stock = new class_stock;

	</script>
</body>
</html>
 