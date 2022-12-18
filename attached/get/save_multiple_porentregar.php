<?php
require '../../bh_conexion.php';
$link = conexion();
date_default_timezone_set('America/Panama');
$fecha_actual = date('Y-m-d');
$hora_actual=date('h:i a');

$raw_marked_datoventa = $_GET['a'];
$raw_marked_ff = array();

$prep_facturaf = $link->prepare("SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.facturaf_AI_cliente_id, bh_datoventa.datoventa_AI_producto_id
	FROM ((bh_datoventa
	INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
	INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
	WHERE bh_datoventa.AI_datoventa_id = ?")or die($link->error);
$prep_facturaf->bind_param("i", $datoventa_id);

$prep_ins_entrega = $link->prepare("INSERT INTO bh_entrega (entrega_AI_cliente_id, entrega_AI_facturaf_id, TX_entrega_fecha, TX_entrega_hora) VALUES (?,?,'$fecha_actual','$hora_actual')")or die($link->error);
$prep_ins_entrega->bind_param("ii",$cliente_id,$facturaf_id);
$prep_entregado = $link->prepare("SELECT SUM(bh_datoentrega.TX_datoentrega_cantidad) AS cantidad, bh_datoventa.TX_datoventa_cantidad FROM (bh_datoentrega INNER JOIN bh_datoventa ON bh_datoventa.AI_datoventa_id = bh_datoentrega.datoentrega_AI_datoventa_id) WHERE datoentrega_AI_datoventa_id = ?")or die($link->error);
$prep_entregado->bind_param("i",$datoventa_id);
$prep_datoentrega = $link->prepare("INSERT INTO bh_datoentrega (datoentrega_AI_entrega_id,TX_datoentrega_cantidad,datoentrega_AI_datoventa_id) VALUES (?,?,?)")or die($link->error);
$prep_datoentrega->bind_param("isi", $entrega_id, $datoentrega_cantidad, $datoventa_id);
$prep_upddatoventa = $link->prepare("UPDATE bh_datoventa SET TX_datoventa_entrega = 1 WHERE AI_datoventa_id = ?")or die($link->error);
$prep_upddatoventa->bind_param("i", $datoventa_id);

foreach ($raw_marked_datoventa as $datoventa_id => $cantidad_entregado) {
	$datoventa_id=$datoventa_id;
	$prep_facturaf->execute(); $qry_facturaf=$prep_facturaf->get_result(); $rs_facturaf=$qry_facturaf->fetch_array();

	$raw_marked_ff[$rs_facturaf['AI_facturaf_id']][$datoventa_id]=$cantidad_entregado;
	$cliente_id=$rs_facturaf['facturaf_AI_cliente_id'];
}

foreach ($raw_marked_ff as $ff_id => $raw_datoventa) {
	$facturaf_id = $ff_id;
	$prep_ins_entrega->execute();

	$qry_lastid=$link->query("SELECT LAST_INSERT_ID();")or die($link->error);
	$rs_lastid = $qry_lastid->fetch_array();
	$last_id = trim($rs_lastid[0]);

	foreach ($raw_datoventa as $datoventa_id => $cantidad_entregado) {
		$entrega_id = $last_id;
		$datoentrega_cantidad = $cantidad_entregado;
		$prep_datoentrega->execute();

		$prep_entregado->execute(); $qry_entregado = $prep_entregado->get_result();
		$rs_entregado = $qry_entregado->fetch_array(MYSQLI_ASSOC);
		if ($rs_entregado['cantidad'] >= $rs_entregado['TX_datoventa_cantidad']) {
			$prep_upddatoventa->execute();
		}
	}
}

if ($_GET['z'] === 'client') {
	echo $cliente_id;
} else {
	echo $rs_facturaf['datoventa_AI_producto_id'];
}



?>
