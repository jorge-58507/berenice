<?php
require '../../bh_conexion.php';
$link = conexion();  $r_function = new recurrent_function();

// NUEVAVENTA

$qry_nuevaventa = $link->query("SELECT TX_rel_nuevaventa_compuesto FROM rel_nuevaventa WHERE AI_rel_nuevaventa_id = 1")or die($link->error);
$rs_nuevaventa = $qry_nuevaventa->fetch_array();
$contenido = $rs_nuevaventa['TX_rel_nuevaventa_compuesto'];
$raw_nuevaventa = json_decode($contenido, true);

unset($raw_nuevaventa[$_COOKIE['coo_iuser']]['first_sale']);
unset($raw_nuevaventa[$_COOKIE['coo_iuser']]['second_sale']);
$contenido = json_encode($raw_nuevaventa, true);
$qry_nuevaventa = $link->query("UPDATE rel_nuevaventa SET TX_rel_nuevaventa_compuesto = '$contenido' WHERE AI_rel_nuevaventa_id = 1")or die($link->error);

// NUEVAVENTA RELACIONES

$qry_nuevaventa = $link->query("SELECT TX_rel_nuevaventa_compuesto FROM rel_nuevaventa WHERE AI_rel_nuevaventa_id = 2")or die($link->error);
$rs_nuevaventa = $qry_nuevaventa->fetch_array();
$contenido = $rs_nuevaventa['TX_rel_nuevaventa_compuesto'];
$raw_nuevaventa_rel = json_decode($contenido, true);

unset($raw_nuevaventa_rel[$_COOKIE['coo_iuser']]['first_sale']);
unset($raw_nuevaventa_rel[$_COOKIE['coo_iuser']]['second_sale']);
$contenido = json_encode($raw_nuevaventa_rel, true);
$qry_nuevaventa = $link->query("UPDATE rel_nuevaventa SET TX_rel_nuevaventa_compuesto = '$contenido' WHERE AI_rel_nuevaventa_id = 2")or die($link->error);

// VIEJAVENTA
$qry_viejaventa = $link->query("SELECT TX_rel_nuevaventa_compuesto FROM rel_nuevaventa WHERE AI_rel_nuevaventa_id = 3")or die($link->error);
$rs_viejaventa = $qry_viejaventa->fetch_array();
$contenido = $rs_viejaventa['TX_rel_nuevaventa_compuesto'];
$raw_viejaventa = json_decode($contenido, true);

unset($raw_viejaventa[$_COOKIE['coo_iuser']]);
$contenido = json_encode($raw_nuevaventa, true);
$qry_viejaventa = $link->query("UPDATE rel_nuevaventa SET TX_rel_nuevaventa_compuesto = '$contenido' WHERE AI_rel_nuevaventa_id = 3")or die($link->error);

// VIEJAVENTA RELACIONES

$qry_viejaventa_rel = $link->query("SELECT TX_rel_nuevaventa_compuesto FROM rel_nuevaventa WHERE AI_rel_nuevaventa_id = 4")or die($link->error);
$rs_viejaventa_rel = $qry_viejaventa_rel->fetch_array();
$contenido = $rs_viejaventa_rel['TX_rel_nuevaventa_compuesto'];
$raw_viejaventa_rel = json_decode($contenido, true);

unset($raw_viejaventa_rel[$_COOKIE['coo_iuser']]);
$contenido = json_encode($raw_viejaventa_rel, true);
$qry_viejaventa_rel = $link->query("UPDATE rel_nuevaventa SET TX_rel_nuevaventa_compuesto = '$contenido' WHERE AI_rel_nuevaventa_id = 4")or die($link->error);
