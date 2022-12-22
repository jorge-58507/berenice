<?php
require '../../bh_conexion.php';
$link = conexion();

$value=$_GET['a'];
$date_i=date('Y-m-d', strtotime($_GET['b']));
$date_f=date('Y-m-d', strtotime($_GET['c']));
$txt_debito="SELECT bh_notadebito.TX_notadebito_numero, bh_notadebito.TX_notadebito_fecha, bh_notadebito.TX_notadebito_hora,
bh_notadebito.TX_notadebito_total, bh_cliente.TX_cliente_nombre, bh_notadebito.AI_notadebito_id, bh_user.TX_user_seudonimo, bh_notadebito.TX_notadebito_status
FROM ((bh_notadebito
INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_notadebito.notadebito_AI_cliente_id)
INNER JOIN bh_user ON bh_user.AI_user_id = bh_notadebito.notadebito_AI_user_id)
WHERE  bh_notadebito.TX_notadebito_numero LIKE '%$value%' AND
bh_notadebito.TX_notadebito_fecha >= '$date_i' AND
bh_notadebito.TX_notadebito_fecha <= '$date_f'
ORDER BY AI_notadebito_id DESC";

$qry_debito=$link->query($txt_debito)or die($link->error);

$prep_facturaf = $link->prepare("SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_numero
  FROM ((bh_notadebito
    INNER JOIN rel_facturaf_notadebito ON rel_facturaf_notadebito.rel_AI_notadebito_id = bh_notadebito.AI_notadebito_id)
    INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = rel_facturaf_notadebito.rel_AI_facturaf_id)
    WHERE bh_notadebito.AI_notadebito_id = ? ")or die($link->error);

?>
<?php
	if($qry_debito->num_rows > 0){
		while($rs_debito=$qry_debito->fetch_array()){
?>
    <tr title="<?php echo $rs_debito['TX_user_seudonimo']; ?>">
      <td><?php echo date('d-m-Y', strtotime($rs_debito['TX_notadebito_fecha']))."<br />".$rs_debito['TX_notadebito_hora']; ?></td>
      <td><?php echo $rs_debito['TX_notadebito_numero']; ?></td>
			<td><?php echo $rs_debito['TX_cliente_nombre']; ?></td>
<?php $prep_facturaf->bind_param("i", $rs_debito['AI_notadebito_id']); $prep_facturaf->execute(); $qry_facturaf=$prep_facturaf->get_result();
      $rs_facturaf=$qry_facturaf->fetch_array(MYSQLI_ASSOC);
      $ff_numero="";
      do{
        $ff_numero .= $rs_facturaf['TX_facturaf_numero']."\n";
      }while ($rs_facturaf=$qry_facturaf->fetch_array(MYSQLI_ASSOC)); ?>
      <td title="<?php echo $ff_numero; ?>"><?php if ($qry_facturaf->num_rows > 1) { echo "<strong>Multiples</strong>"; }else{ echo $ff_numero; } ?></td>
      <td><?php echo number_format($rs_debito['TX_notadebito_total'],2); ?></td>
      <td class="al_center">
        <button type="button" id="btn_print_ff" name="<?php echo "print_client_debito.php?a=".$rs_debito['AI_notadebito_id']; ?>" class="btn btn-info btn-md" onclick="print_html(this.name);"><strong><i class="fa fa-print fa_print" aria-hidden="true"></i></strong></button>
        &nbsp;
<?php   if ($rs_debito['TX_notadebito_status'] != '1') { ?>
          <button type="button" id="btn_redo_nd" name="" class="btn btn-danger btn-md" onclick="redo_nd('<?php echo $rs_debito['AI_notadebito_id']; ?>');"><strong><i class="fa fa-times fa_print" aria-hidden="true"></i></strong></button>
<?php   } ?>
      </td>
    </tr>
<?php
		}
	}else{
?>
    <tr>
    	<td colspan="6"> </td>
    </tr>
<?php } ?>
