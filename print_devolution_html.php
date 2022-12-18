<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_sale.php';
$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion");
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array(MYSQLI_ASSOC)){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}
$compradevolucion_id=$_GET['a'];

$qry_compradevolucion = $link->query("SELECT * FROM bh_compradevolucion WHERE AI_compradevolucion_id = '$compradevolucion_id'")or die($link->error);
$rs_compradevolucion = $qry_compradevolucion->fetch_array(MYSQLI_ASSOC);

$qry_proveedor = $link->query("SELECT AI_proveedor_id, TX_proveedor_nombre, TX_proveedor_cif, TX_proveedor_dv, TX_proveedor_direccion FROM bh_proveedor WHERE AI_proveedor_id = '{$rs_compradevolucion['compradevolucion_AI_proveedor_id']}' ")or die($link->error);
$rs_proveedor = $qry_proveedor->fetch_array(MYSQLI_ASSOC);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Devolucion: <?php echo $rs_proveedor['TX_proveedor_nombre']; ?></title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css" />
</head>
<script type="text/javascript">
function cap_fl(str){
	  return string.charAt(0).toUpperCase() + string.slice(1);
}
</script>

<body style="font-family:Arial<?php /* echo $RS_medinfo['TX_fuente_medico']; */?>" onLoad="">
<?php
$fecha_actual=date('Y-m-d');
$dias = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
$d_number=date('w',strtotime($fecha_actual));
$fecha_dia = $dias[$d_number];
$fecha = date('d-m-Y',strtotime($fecha_actual));
?>

<div style="height:975px; width:720px; font-size:12px; margin:0 auto">
	<div id="print_header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="height: 140px; padding-top: 10px;">
		<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">&nbsp;</div>
		<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 al_center">
			<img width="200px" height="75px" src="attached/image/logo_factura.png">
			<br />
			<font style="font-size:10px">RUC: <?php echo $raw_opcion['RUC']; ?> DV: <?php echo $raw_opcion['DV']."<br/>"; ?></font>
			<font style="font-size:10px"><?php echo $raw_opcion['DIRECCION']."<br />"; ?></font>
			<font style="font-size:10px"><?php echo "TLF. ".$raw_opcion['TELEFONO']." WHATSAPP: ".$raw_opcion['FAX']."<br />"; ?></font>
			<font style="font-size:10px"><?php echo $raw_opcion['EMAIL']."<br />"; ?></font>
		</div>
		<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><?php
			$dias = array('','Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
			$fecha = $dias[date('N', strtotime(date('d-m-Y')))+1];
			echo $fecha."&nbsp;-&nbsp;".$date=date('d-m-Y');
?>	</div>
	</div>
	<div id="print_title" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 110px;">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center" style="height: 50px;">
			<h3>DEVOLUCI&Oacute;N</h3>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 30px;">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no_padding"><strong>Proveedor: </strong><?php echo substr(strtoupper($rs_proveedor['TX_proveedor_nombre']),0,40); ?></div>
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding"><strong>RUC: </strong><?php echo strtoupper($rs_proveedor['TX_proveedor_cif']); ?><strong>DV: </strong><?php echo $rs_proveedor['TX_proveedor_dv']; ?></div>
			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no_padding"><strong>Fecha: </strong><?php echo date('d-m-Y',strtotime($rs_compradevolucion['TX_compradevolucion_fecha'])); ?></div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 30px;">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
				<strong>Motivo: </strong><?php echo substr(strtoupper($rs_proveedor['TX_proveedor_direccion']),0,90); ?>
			</div>
		</div>
	</div>
<!-- #####################         BODY          #################   -->
	<div id="print_body" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_caption">
			Facturas Procesadas
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_header">
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>CODIGO</strong></div>
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><strong>DESCRIPCION</strong></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>MEDIDA</strong></div>
			<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><strong>CANT.</strong></div>
			<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"><strong>PRECIO</strong></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>TOTAL</strong></div>
		</div><?php
		$total=0;
		$qry_datocompradevolucion = $link->query("SELECT bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_medida.TX_medida_value, bh_datocompradevolucion.TX_datocompradevolucion_cantidad, bh_datocompradevolucion.datocompradevolucion_AI_datocompra_id, bh_datocompradevolucion.datocompradevolucion_AI_producto_id, bh_datocompradevolucion.datocompradevolucion_AI_medida_id FROM bh_datocompradevolucion
		INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datocompradevolucion.datocompradevolucion_AI_producto_id
		INNER JOIN bh_medida ON bh_medida.AI_medida_id = bh_datocompradevolucion.datocompradevolucion_AI_medida_id
		WHERE datocompradevolucion_AI_compradevolucion_id = '$compradevolucion_id'")or die($link->error);

		$prep_datocompra = $link->prepare("SELECT * FROM bh_datocompra WHERE AI_datocompra_id = ?")or die($link->error);
		$prep_producto_medida = $link->prepare("SELECT AI_rel_productomedida_id, TX_rel_productomedida_cantidad FROM rel_producto_medida WHERE productomedida_AI_producto_id = ? AND productomedida_AI_medida_id = ?")or die($link->error);

		$prep_datocompra->bind_param('i',$datocompra_id);
		while($rs_datocompradevolucion=$qry_datocompradevolucion->fetch_array(MYSQLI_ASSOC)){
			$datocompra_id = $rs_datocompradevolucion['datocompradevolucion_AI_datocompra_id'];
			$prep_datocompra->execute(); $qry_datocompra = $prep_datocompra->get_result();
			$rs_datocompra = $qry_datocompra->fetch_array(MYSQLI_ASSOC);

			$precio_descuento = $rs_datocompra['TX_datocompra_precio']-(($rs_datocompra['TX_datocompra_descuento']*$rs_datocompra['TX_datocompra_precio'])/100);
      $impuesto = ($precio_descuento*$rs_datocompra['TX_datocompra_impuesto'])/100;
      $precio_total = $precio_descuento+$impuesto;

			$prep_producto_medida->bind_param('ii',$producto_id,$medida);
			$producto_id = $rs_datocompradevolucion['datocompradevolucion_AI_producto_id'];

			$medida = $rs_datocompradevolucion['datocompradevolucion_AI_medida_id'];
			$prep_producto_medida->execute(); $qry_producto_medida = $prep_producto_medida->get_result();
			$rs_producto_medida = $qry_producto_medida->fetch_array(MYSQLI_ASSOC);
			$devolucion_qtymedida = $rs_producto_medida['TX_rel_productomedida_cantidad'];

			$medida = $rs_datocompra['TX_datocompra_medida'];
			$prep_producto_medida->execute(); $qry_producto_medida = $prep_producto_medida->get_result();
			$rs_producto_medida = $qry_producto_medida->fetch_array(MYSQLI_ASSOC);
			$compra_qtymedida = $rs_producto_medida['TX_rel_productomedida_cantidad'];

			$factor = $devolucion_qtymedida/$compra_qtymedida;
			?>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_body">
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><?php echo $rs_datocompradevolucion['TX_producto_codigo'] ?></div>
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 al_center"><?php echo $r_function->replace_special_character($rs_datocompradevolucion['TX_producto_value']); ?></div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><?php echo $rs_datocompradevolucion['TX_medida_value']; ?></div>
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center"><?php echo $qty = round($rs_datocompradevolucion['TX_datocompradevolucion_cantidad'],2); ?></div>
				<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center"><?php echo number_format($precio_total*$factor,2); ?></div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><?php echo number_format(($precio_total*$factor)*$qty,2); ?></div>
			</div><?php $total += ($precio_total*$factor)*$qty;
		} ?>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_body">
			<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8"> </div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"></div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><strong>TOTAL:</strong><br /><?php echo "B/ ".number_format($total,2); ?></div>
		</div>
	</div>
<!-- #####################         BODY          #################   -->
</div>
</body>
</html>
