<?php
require '../../bh_conexion.php';
$link = conexion();

$product_id=$_GET['a'];
$limit=$_GET['b'];
$date_i=date('Y-m-d',strtotime($_GET['c']));
$date_f=date('Y-m-d',strtotime($_GET['d']));

if($limit == ""){	$line_limit="";	}else{	$line_limit= " LIMIT ".$limit;	}
if (!empty($date_i) && !empty($date_f)) {
	$line_date = " AND TX_facturaf_fecha >=	'$date_i' AND TX_facturaf_fecha <= '$date_f'";
}
$txt_facturaf="SELECT bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento,
bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_numero, bh_cliente.TX_cliente_nombre, bh_user.TX_user_seudonimo
FROM ((((bh_datoventa
INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_facturaf.facturaf_AI_cliente_id)
INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaf.facturaf_AI_user_id)
WHERE bh_datoventa.datoventa_AI_producto_id = '$product_id'".$line_date."
ORDER BY TX_facturaf_fecha DESC, TX_facturaf_numero DESC".$line_limit;

$qry_facturaf=$link->query($txt_facturaf)or die($link->error);


$txt_nc="SELECT bh_datodevolucion.TX_datodevolucion_cantidad, bh_notadecredito.TX_notadecredito_fecha, bh_notadecredito.TX_notadecredito_numero, bh_cliente.TX_cliente_nombre, bh_notadecredito.TX_notadecredito_anulado
FROM ((bh_datodevolucion
INNER JOIN bh_notadecredito ON bh_notadecredito.AI_notadecredito_id = bh_datodevolucion.datodevolucion_AI_notadecredito_id)
INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_notadecredito.notadecredito_AI_cliente_id)
WHERE bh_datodevolucion.datodevolucion_AI_producto_id = '$product_id' AND TX_notadecredito_fecha >=	'$date_i' AND TX_notadecredito_fecha <= '$date_f'
ORDER BY TX_notadecredito_fecha DESC, TX_notadecredito_numero ASC".$line_limit;

$qry_nc=$link->query($txt_nc)or die($link->error);

				if($qry_facturaf->num_rows > 0){
					$total_cantidad =	0;
					while($rs_facturaf=$qry_facturaf->fetch_array()){
						$total_cantidad += $rs_facturaf['TX_datoventa_cantidad'];
						$descuento4product = ($rs_facturaf['TX_datoventa_descuento']*$rs_facturaf['TX_datoventa_precio'])/100;
						$precio_descuento = $rs_facturaf['TX_datoventa_precio']-$descuento4product;
						$impuesto4product = ($rs_facturaf['TX_datoventa_impuesto']*$precio_descuento)/100;
						$precio_impuesto = $precio_descuento+$impuesto4product;
?>
		        <tr onclick="filter_productbysale('<?php echo $rs_facturaf['AI_facturaf_id']; ?>');">
			        <td><?php echo $fecha = date('d-m-Y',strtotime($rs_facturaf['TX_facturaf_fecha']))." - ".$rs_facturaf['TX_facturaf_hora']; ?></td>
			        <td><?php echo $rs_facturaf['TX_facturaf_numero']; 		?></td>
			        <td><?php echo $rs_facturaf['TX_cliente_nombre']; 		?></td>
			        <td><?php echo $rs_facturaf['TX_datoventa_cantidad']; ?></td>
			        <td><?php echo number_format($precio_impuesto,2); 		?></td>
		      	</tr>
<?php 		} ?>
					<tr class="bg-info">
						<td></td>
						<td></td>
						<td></td>
						<td><strong>TOTAL: </strong><br /><?php echo $total_cantidad; ?></td>
						<td></td>
					</tr>
<?php 	}else{ ?>
	        <tr>
            <td>&nbsp;</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
	        </tr>
<?php 	} ?>
					<tr class="bg-danger">
						<td><strong>Fecha</strong></td>
						<td><strong>N&deg; NC</strong></td>
						<td><strong>Cliente</strong></td>
						<td><strong>Cantidad</strong></td>
						<td><strong>Anulacion</strong></td>
					</tr>
<?php 		$total_devuelto=0;
					if ($qry_nc->num_rows > 0) {
						while ($rs_nc = $qry_nc->fetch_array(MYSQLI_ASSOC)) {?>
							<tr>
								<td><?php echo $rs_nc['TX_notadecredito_fecha']; 			?></td>
								<td><?php echo $rs_nc['TX_notadecredito_numero']; 		?></td>
								<td><?php echo $rs_nc['TX_cliente_nombre']; 					?></td>
								<td><?php echo $rs_nc['TX_datodevolucion_cantidad']; $total_devuelto+=$rs_nc['TX_datodevolucion_cantidad']; 	?></td>
								<td><?php if($rs_nc['TX_notadecredito_anulado'] > 0){ echo "ANULADA"; }else{	echo "NO ANULADA";	} 					?></td>
							</tr>
	<?php			} ?>
						<tr class="bg-info">
							<td></td>
							<td></td>
							<td></td>
							<td><strong>TOTAL: </strong><br /><?php echo $total_devuelto; ?></td>
							<td></td>
						</tr>
	<?php		}else{ ?>
						<tr>
							<td>&nbsp;</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
	<?php 	} ?>
