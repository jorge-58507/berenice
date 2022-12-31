<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login.php';
$str_factid=$_GET['a'];
$txt_datoventa="SELECT bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_datoventa.AI_datoventa_id, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento
FROM (bh_datoventa
INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
WHERE";
$arr_factid = explode(",",$str_factid);
foreach ($arr_factid as $key => $value) {
	if ($value === end($arr_factid)) {
		$txt_datoventa=$txt_datoventa." bh_datoventa.datoventa_AI_facturaventa_id = '$value'";
	}else{
		$txt_datoventa=$txt_datoventa." bh_datoventa.datoventa_AI_facturaventa_id = '$value' OR";
	}
}
$qry_datoventa=$link->query($txt_datoventa." ORDER BY datoventa_AI_facturaventa_id ASC, AI_datoventa_id ASC")or die($link->error);
$rs_datoventa=$qry_datoventa->fetch_array();
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
				$("#btn_salir").click(function(){
					window.location='new_collect.php?a=<?php echo $_GET['a'] ?>&b=<?php echo $_GET['b'] ?>';
				});
				$("#btn_discount").click(function(){
					make_discount();
				});
			});
			function make_discount(){
				var facturaventa_id = get('a');
				var percent = prompt("¿Que porcentaje desea descontar?", "0");
				var patt = new RegExp(/[0-9]/);
				var res = patt.test(percent);
				if(res){
					upd_discount(facturaventa_id,percent);
				}
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
		        Bienvenido: <label class="bg-primary">
		        <?php echo $rs_checklogin['TX_user_seudonimo']; ?>
		        </label>
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
						}
	?>			</div>
				</div>
			</div>
			<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<form method="post" name="form_editdatoventa" action="">
					<div id="container_tbleditdatoventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				    <table id="tbl_editdatoventa" class="table table-bordered table-condensed table-striped tab">
					    <thead class="bg-primary">
						    <tr>
					        <th>C&oacute;digo</th>
					        <th>Producto</th>
					        <th>Cantidad</th>
					        <th>Precio</th>
					        <th>Impuesto</th>
					        <th>Descuento</th>
						    </tr>
					    </thead>
					    <tfoot class="bg-primary"><tr><td colspan="6"></td></tr></tfoot>
					    <tbody>
	<?php  				do{  ?>
							    <tr onclick="open_popup('popup_editdatoventa.php?a=<?php echo $rs_datoventa['AI_datoventa_id']; ?>','popup_editdatoventa','400','427')">
							        <td><?php echo $rs_datoventa['TX_producto_codigo']; ?></td>
							        <td><?php echo $rs_datoventa['TX_producto_value']; ?></td>
							        <td><?php echo $rs_datoventa['TX_datoventa_cantidad']; ?></td>
							        <td><?php echo number_format($rs_datoventa['TX_datoventa_precio'],2); ?></td>
							        <td><?php echo number_format($rs_datoventa['TX_datoventa_impuesto'],2); ?></td>
							        <td><?php echo number_format($rs_datoventa['TX_datoventa_descuento'],2); ?></td>
							    </tr>
	<?php 				}while($rs_datoventa=$qry_datoventa->fetch_array()); ?>
					    </tbody>
				    </table>
					</div>
					<div id="container_btn" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<button type="button" id="btn_salir" class="btn btn-warning">Volver</button>
						<button type="button" id="btn_discount" class="btn btn-info">Descuento general</button>
					</div>
				</form>
			</div>
			<div id="footer">
				<?php require 'attached/php/req_footer.php'; ?>
			</div>
		</div>
	<!-- </div> -->
		<script type="text/javascript">
			<?php include 'attached/php/req_footer_js.php'; ?>
		</script>
	</body>
</html>
