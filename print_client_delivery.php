<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_sale.php';

$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion")or die($link->error);
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}
$facturaf_id=$_GET['a'];

$qry_client=$link->query("SELECT bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_telefono, bh_cliente.TX_cliente_direccion, bh_facturaf.TX_facturaf_numero FROM (bh_cliente INNER JOIN bh_facturaf ON bh_cliente.AI_cliente_id = bh_facturaf.facturaf_AI_cliente_id) WHERE AI_facturaf_id = '$facturaf_id'");
$rs_client=$qry_client->fetch_array();

$txt_facturaf="SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_ticket, bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_subtotalni, bh_facturaf.TX_facturaf_subtotalci, bh_facturaf.TX_facturaf_impuesto, bh_facturaf.TX_facturaf_descuento, bh_facturaf.TX_facturaf_cambio,
bh_user.TX_user_seudonimo
FROM ((bh_facturaf
INNER JOIN bh_facturaventa ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaventa.facturaventa_AI_user_id)
WHERE AI_facturaf_id = '$facturaf_id'";
$qry_facturaf=$link->query($txt_facturaf) or die($link->error);
$rs_facturaf=$qry_facturaf->fetch_array();

$qry_facturaventa=$link->query("SELECT bh_facturaventa.TX_facturaventa_observacion FROM (bh_facturaventa INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id) WHERE AI_facturaf_id = '$facturaf_id' GROUP BY AI_facturaf_id");
$rs_facturaventa=$qry_facturaventa->fetch_array();
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Factura: <?php echo $rs_client['TX_cliente_nombre']." - ".$rs_client['TX_facturaf_numero']; ?></title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css">
</head>
<script type="text/javascript">
function cap_fl(str){
	  return string.charAt(0).toUpperCase() + string.slice(1);
}
setTimeout("self.close()", 20000);
</script>

<body style="font-family:Arial" onLoad="window.print()" >
	<div style="height:975px; width:720px; font-size:12px; margin:0 auto">
		<div id="print_header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="height: 90px; padding-top: 10px;">
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">&nbsp;</div>
			<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 al_center">
				<img width="200px" height="75px" src="attached/image/logo_factura_materiales.png" ondblclick="window.location.href='print_sale_html_materiales.php?a=<?php echo $facturaventa_id; ?>'">
			</div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><?php
				$dias = array('','Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
				$fecha = $dias[date('N', strtotime(date('d-m-Y')))+1];
				echo $fecha."&nbsp;-&nbsp;".$date=date('d-m-Y');	?>	
			</div>
		</div>
		<div id="print_title" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 180px;">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center" style="height: 50px;">
				<h3>HOJA DE ENTREGA</h3>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 90px; border: solid 2px #000; font-size: 16px;">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 30px;">
					<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5"><strong>Nombre: </strong><?php echo  substr($r_function->replace_special_character($rs_client['TX_cliente_nombre']),0,23); ?></div>
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><strong>RUC: </strong><?php echo strtoupper($rs_client['TX_cliente_cif']); ?></div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><strong>Tel&eacute;fono: </strong><?php echo $rs_client['TX_cliente_telefono']; ?></div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 30px;">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><strong>Direcci&oacute;n: </strong><?php echo substr($r_function->replace_special_character($rs_client['TX_cliente_direccion']),0,60); ?></div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding" style="height: 30px;">
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 no_padding_right"><strong>Factura: </strong><?php echo $rs_facturaf['TX_facturaf_numero']; ?></div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 no_padding"><strong>Fecha: </strong><?php echo date('d-m-Y',strtotime($rs_facturaf['TX_facturaf_fecha'])); ?></div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 no_padding_right"><strong>Vendedor: </strong><?php echo strtoupper($rs_facturaf['TX_user_seudonimo']); ?></div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding mt-5" style="height: 30px;border: solid 2px #000; font-size: 16px;">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><strong>Observaci&oacute;n: </strong><?php echo strtoupper($rs_facturaventa[0]); ?></div>
			</div>
		</div>
		<!-- #####################         BODY          #################   -->
		<?php
			$txt_datoventa="SELECT bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento, bh_datoventa.TX_datoventa_descripcion,
			bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_datoventa.AI_datoventa_id, bh_producto.AI_producto_id
			FROM (((bh_datoventa
			INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datoventa.datoventa_AI_producto_id)
			INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
			INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
			WHERE bh_facturaventa.facturaventa_AI_facturaf_id = '$facturaf_id'";
			$qry_datoventa=$link->query($txt_datoventa);
		?>
		<div id="print_body" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_caption">
				Producto(s) Relacionados
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_header" style="font-size: 16px;">
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><strong>CODIGO</strong></div>
				<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7"><strong>DETALLE</strong></div>
				<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>CANTIDAD</strong></div>
			</div>
			<?php
			while($rs_datoventa=$qry_datoventa->fetch_array(MYSQLI_ASSOC)){	
				?>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding print_line_body"  style="font-size: 16px;">
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><?php echo $rs_datoventa['TX_producto_codigo']; ?></div>
					<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7"><?php
						if ($rs_datoventa['AI_producto_id'] === "14415") {
							$descripcion = $r_function->replace_special_character($rs_datoventa['TX_datoventa_descripcion']).", Entregar en: ".$rs_client['TX_cliente_direccion'];
						}else{
							$descripcion = $r_function->replace_special_character($rs_datoventa['TX_datoventa_descripcion']);
						}
						echo $descripcion;?>
					</div>
					<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 al_center"><?php echo $rs_datoventa['TX_datoventa_cantidad']; ?></div>
				</div>
				<?php	
			} ?>
			<!-- #####################         BODY          #################   -->
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center pt_28">
			<h3>Por favor sellar</h3>
		</div>
	</div>

	<!-- ###### FIN POR DIV ######-->
</body>
</html>
