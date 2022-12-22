<?php
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');
$fecha_actual = date('Y-m-d');
$hora_actual=date('h:i a');

$ff_id = $_GET['a'];
$raw_marked_ff = $_GET['b'];
$qry_facturaf = $link->query("SELECT facturaf_AI_cliente_id FROM bh_facturaf WHERE AI_facturaf_id = '$ff_id' ")or die($link->error);
$rs_facturaf = $qry_facturaf->fetch_array(MYSQLI_ASSOC);

$link->query("INSERT INTO bh_entrega (entrega_AI_cliente_id, entrega_AI_facturaf_id, TX_entrega_fecha, TX_entrega_hora) VALUES ('{$rs_facturaf['facturaf_AI_cliente_id']}', '$ff_id','$fecha_actual','$hora_actual')")or die($link->error);
$qry_lastid=$link->query("SELECT LAST_INSERT_ID();")or die($link->error);
$rs_lastid = $qry_lastid->fetch_array();
$last_id = trim($rs_lastid[0]);

$prep_entregado = $link->prepare("SELECT SUM(bh_datoentrega.TX_datoentrega_cantidad) AS cantidad, bh_datoventa.TX_datoventa_cantidad FROM (bh_datoentrega INNER JOIN bh_datoventa ON bh_datoventa.AI_datoventa_id = bh_datoentrega.datoentrega_AI_datoventa_id) WHERE datoentrega_AI_datoventa_id = ?")or die($link->error);
$prep_entregado->bind_param("i",$datoventa_id);
$prep_datoentrega = $link->prepare("INSERT INTO bh_datoentrega (datoentrega_AI_entrega_id,TX_datoentrega_cantidad,datoentrega_AI_datoventa_id) VALUES (?,?,?)")or die($link->error);
$prep_datoentrega->bind_param("isi", $entrega_id, $datoentrega_cantidad, $datoventa_id);
$prep_upddatoventa = $link->prepare("UPDATE bh_datoventa SET TX_datoventa_entrega = 1 WHERE AI_datoventa_id = ?")or die($link->error);
$prep_upddatoventa->bind_param("i", $datoventa_id);
foreach ($raw_marked_ff as $key => $value) {
	$entrega_id = $last_id;
	$datoentrega_cantidad = $value;
	$datoventa_id = $key;
	$prep_datoentrega->execute();
	$prep_entregado->execute(); $qry_entregado = $prep_entregado->get_result();
	$rs_entregado = $qry_entregado->fetch_array(MYSQLI_ASSOC);
	if ($rs_entregado['cantidad'] >= $rs_entregado['TX_datoventa_cantidad']) {
		$prep_upddatoventa->execute();
	}
}
$prep_datoentrega->close();


echo "All Right";

?>
