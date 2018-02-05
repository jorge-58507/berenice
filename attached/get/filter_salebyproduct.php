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
echo $txt_facturaf="SELECT bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento,
bh_facturaf.TX_facturaf_fecha, bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_numero, bh_cliente.TX_cliente_nombre, bh_user.TX_user_seudonimo
FROM ((((bh_datoventa
INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_facturaf.facturaf_AI_cliente_id)
INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaf.facturaf_AI_user_id)
WHERE bh_datoventa.datoventa_AI_producto_id = '$product_id'".$line_date."
ORDER BY TX_facturaf_fecha DESC".$line_limit;

$qry_facturaf=$link->query($txt_facturaf);

?>

        <?php
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
        <td><?php
		$prefecha=strtotime($rs_facturaf['TX_facturaf_fecha']);
		echo $fecha = date('d-m-Y',$prefecha);
		 ?></td>
        <td><?php echo $rs_facturaf['TX_facturaf_numero']; ?></td>
        <td><?php echo $rs_facturaf['TX_cliente_nombre']; ?></td>
        <td><?php echo $rs_facturaf['TX_datoventa_cantidad']; ?></td>
        <td><?php echo number_format($precio_impuesto,2); ?></td>
        </tr>
        <?php } ?>
			<?php }else{ ?>
        <tr>
            <td>&nbsp;</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <?php } ?>
