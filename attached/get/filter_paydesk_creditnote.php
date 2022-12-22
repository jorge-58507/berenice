<?php
require '../../bh_conexion.php';
$link = conexion();

$value=$_GET['a'];
$date_i=date('Y-m-d', strtotime($_GET['b']));
$date_f=date('Y-m-d', strtotime($_GET['c']));

$txt_nc="SELECT bh_facturaf.TX_facturaf_numero, bh_notadecredito.TX_notadecredito_destino, (bh_notadecredito.TX_notadecredito_monto+bh_notadecredito.TX_notadecredito_impuesto) AS total,
bh_notadecredito.TX_notadecredito_exedente, bh_notadecredito.AI_notadecredito_id, bh_notadecredito.TX_notadecredito_numero,
 bh_notadecredito.TX_notadecredito_hora, bh_notadecredito.TX_notadecredito_fecha, bh_user.TX_user_seudonimo, bh_cliente.TX_cliente_nombre
FROM (((bh_notadecredito
INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_notadecredito.notadecredito_AI_facturaf_id)
INNER JOIN bh_user ON bh_user.AI_user_id = bh_notadecredito.notadecredito_AI_user_id)
INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_notadecredito.notadecredito_AI_cliente_id)
WHERE  bh_notadecredito.TX_notadecredito_numero LIKE '%$value%' AND
bh_notadecredito.TX_notadecredito_fecha >= '$date_i' AND
bh_notadecredito.TX_notadecredito_fecha <= '$date_f'
ORDER BY AI_notadecredito_id DESC";

$qry_nc=$link->query($txt_nc)or die($link->error);
?>
<?php
	if($qry_nc->num_rows > 0){
		while($rs_nc=$qry_nc->fetch_array()){
?>
    <tr title="<?php echo $rs_nc['TX_user_seudonimo']; ?>">
      <td><?php echo date('d-m-Y', strtotime($rs_nc['TX_notadecredito_fecha']))."<br />".$rs_nc['TX_notadecredito_hora']; ?></td>
      <td><?php echo $rs_nc['TX_notadecredito_numero']; ?></td>
      <td><?php echo $rs_nc['TX_facturaf_numero']; ?></td>
			<td><?php echo $rs_nc['TX_cliente_nombre']; ?></td>
      <td><?php echo $rs_nc['TX_notadecredito_destino']; ?></td>
      <td><?php echo number_format($rs_nc['total'],2); ?></td>
      <td><?php echo number_format($rs_nc['TX_notadecredito_exedente'],2); ?></td>
      <td class="al_center">
				<button type="button" id="btn_print_ff" name="<?php echo "print_client_nc.php?a=".$rs_nc['AI_notadecredito_id']; ?>" class="btn btn-info btn-md" onclick="print_html(this.name);"><strong><i class="fa fa-print fa_print" aria-hidden="true"></i></strong></button>
			</td>
    </tr>
<?php
		}
	}else{
?>
    <tr>
    	<td colspan="7"> </td>
    </tr>
<?php } ?>
