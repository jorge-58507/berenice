<?php
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');

$debito_id=$_GET['a'];

//    COLOCAR COMO ANULADA LA notadebito_AI_user_id
$link->query("UPDATE bh_notadebito SET TX_notadebito_status = 1 WHERE AI_notadebito_id = '$debito_id'")or die($link->error);
//     COLOCAR ANULADO LAS SALIDAS
$qry_debito=$link->query("SELECT TX_notadebito_numero FROM bh_notadebito WHERE AI_notadebito_id = '$debito_id'")or die($link->error);
$rs_debito = $qry_debito->fetch_array(MYSQLI_ASSOC);
$motivo = $debito_id.' CAMBIO A CHEQUE ('.$rs_debito['TX_notadebito_numero'].')';
$qry_salida = $link->query("SELECT AI_efectivo_id FROM bh_efectivo WHERE TX_efectivo_motivo = '$motivo'")or die($link->error);
$rs_salida = $qry_salida->fetch_array(MYSQLI_ASSOC);
$link->query("UPDATE bh_efectivo SET efectivo_AI_arqueo_id = '-1' WHERE AI_efectivo_id = '{$rs_salida['AI_efectivo_id']}'")or die($link->error);
//    ACTUALIZAR EL SALDO DEUDOR DE LAS FACTURAS
$qry_datodebito = $link->query("SELECT TX_rel_facturafnotadebito_importe, rel_AI_facturaf_id FROM rel_facturaf_notadebito WHERE rel_AI_notadebito_id = '$debito_id'")or die($link->error);
$raw_anulado = [];
while ($rs_datodebito=$qry_datodebito->fetch_array(MYSQLI_ASSOC)) {
	$raw_anulado[$rs_datodebito['rel_AI_facturaf_id']] = $rs_datodebito['TX_rel_facturafnotadebito_importe'];
}
$prep_facturaf = $link->prepare("SELECT TX_facturaf_deficit FROM bh_facturaf WHERE AI_facturaf_id = ?")or die($link->error);
$prep_facturaf->bind_param("i", $ff_id);
$prep_redo_saldo = $link->prepare("UPDATE bh_facturaf SET TX_facturaf_deficit = ? WHERE AI_facturaf_id = ?") or die($link->error);
$prep_redo_saldo->bind_param("si",$saldo_pendiente,$ff_id);
foreach ($raw_anulado as $key => $value) {
	$ff_id=$key;
	$prep_facturaf->execute(); $qry_facturaf = $prep_facturaf->get_result();
	$rs_facturaf = $qry_facturaf->fetch_array(MYSQLI_ASSOC);
	$saldo_pendiente = $rs_facturaf['TX_facturaf_deficit']+$value;
	$prep_redo_saldo->execute();
}
echo json_encode($raw_anulado);

?>
