<?php
require '../../bh_conexion.php';
$link = conexion();

$str=$_GET['a'];
$raw_str = explode(" ",$str);

$txt_nc="SELECT bh_facturaf.TX_facturaf_numero, bh_notadecredito.TX_notadecredito_destino, (bh_notadecredito.TX_notadecredito_monto+bh_notadecredito.TX_notadecredito_impuesto) AS total,
bh_notadecredito.TX_notadecredito_exedente, bh_notadecredito.AI_notadecredito_id, bh_notadecredito.TX_notadecredito_numero,
 bh_notadecredito.TX_notadecredito_hora, bh_notadecredito.TX_notadecredito_fecha, bh_user.TX_user_seudonimo, bh_cliente.TX_cliente_nombre
FROM (((bh_notadecredito
INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_notadecredito.notadecredito_AI_facturaf_id)
INNER JOIN bh_user ON bh_user.AI_user_id = bh_notadecredito.notadecredito_AI_user_id)
INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_notadecredito.notadecredito_AI_cliente_id)  WHERE";

foreach ($raw_str as $key => $value) {
	if ($value === end($raw_str)) {
		$txt_nc .= " bh_notadecredito.TX_notadecredito_numero LIKE '%$value%'";
	}else{
		$txt_nc .= " bh_notadecredito.TX_notadecredito_numero LIKE '%$value%' OR";
	}
}
$qry_nc=$link->query($txt_nc)or die($link->error);
?>
<?php
	if($qry_nc->num_rows > 0){
		while($rs_nc=$qry_nc->fetch_array()){
?>
    <tr title="<?php echo $rs_nc['TX_user_seudonimo']; ?>">
			<td><?php echo $rs_nc['TX_notadecredito_numero']; ?></td>
      <td><?php echo $rs_nc['TX_facturaf_numero']; ?></td>
			<td><?php echo $rs_nc['TX_cliente_nombre']; ?></td>
      <td><?php echo $rs_nc['TX_notadecredito_destino']; ?></td>
      <td><?php echo number_format($rs_nc['total'],2); ?></td>
      <td><?php echo number_format($rs_nc['TX_notadecredito_exedente'],2); ?></td>
      <td>
				<button type="button" id="btn_print_ff" name="<?php echo "print_client_nc.php?a=".$rs_nc['AI_notadecredito_id']; ?>" class="btn btn-info btn-xs" onclick="print_html(this.name);"><strong><i class="fa fa-print fa_print" aria-hidden="true"></i></strong></button>
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
