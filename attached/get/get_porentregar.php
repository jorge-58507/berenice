<?php
require '../../bh_conexion.php';

function get_facturaf(){
  $link = conexion();
  $ff_id = $_GET['a'];
  $qry_porentregar = $link->query("SELECT bh_facturaventa.AI_facturaventa_id, bh_datoventa.AI_datoventa_id, bh_datoventa.TX_datoventa_cantidad AS cantidad, bh_datoventa.TX_datoventa_descripcion, bh_datoventa.TX_datoventa_entrega, SUM(bh_datoentrega.TX_datoentrega_cantidad) AS entregado, bh_producto.TX_producto_codigo
 	FROM (((bh_facturaventa
	INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
	INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datoventa.datoventa_AI_producto_id)
  LEFT JOIN bh_datoentrega ON bh_datoentrega.datoentrega_AI_datoventa_id = bh_datoventa.AI_datoventa_id)
	WHERE bh_facturaventa.facturaventa_AI_facturaf_id = '$ff_id' GROUP BY AI_datoventa_id")or die($link->error);
  $raw_porentregar = array();
  while ($rs_porentregar = $qry_porentregar->fetch_array(MYSQLI_ASSOC)) {
    $raw_porentregar[$ff_id][] = $rs_porentregar;
  }
  foreach ($raw_porentregar[$ff_id] as $key => $array) {
    if (empty($array['entregado'])) {
      $raw_porentregar[$ff_id][$key]['entregado'] = "0";
    }
  }

  $qry_entregado = $link->query("SELECT bh_entrega.AI_entrega_id, bh_entrega.TX_entrega_fecha, bh_entrega.TX_entrega_hora, bh_datoentrega.TX_datoentrega_cantidad, bh_datoventa.TX_datoventa_descripcion, bh_producto.TX_producto_codigo
	FROM (((bh_entrega
	INNER JOIN bh_datoentrega ON bh_entrega.AI_entrega_id = bh_datoentrega.datoentrega_AI_entrega_id)
	INNER JOIN bh_datoventa ON bh_datoventa.AI_datoventa_id = bh_datoentrega.datoentrega_AI_datoventa_id)
	INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datoventa.datoventa_AI_producto_id)
	WHERE bh_entrega.entrega_AI_facturaf_id = '$ff_id'")or die($link->error);
  $raw_entregado = array();
  while ($rs_entregado = $qry_entregado->fetch_array(MYSQLI_ASSOC)) {
    $raw_entregado[$ff_id][] = $rs_entregado;
  }

  $qry_facturaf = $link->query("SELECT bh_facturaf.TX_facturaf_numero, bh_cliente.TX_cliente_nombre FROM bh_facturaf INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_facturaf.facturaf_AI_cliente_id WHERE AI_facturaf_id = '$ff_id'")or die($link->error);
  $rs_facturaf = $qry_facturaf->fetch_array(MYSQLI_ASSOC);
  $raw_return[] = $raw_porentregar;
  $raw_return[] = $raw_entregado;
  $raw_return[] = $rs_facturaf['TX_cliente_nombre'].' - &#35;'.$rs_facturaf['TX_facturaf_numero'];
  echo json_encode($raw_return);
}
function get_cliente(){
  $link = conexion();
  $cliente_id = $_GET['a'];

  $qry_porentregar = $link->query("SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_hora, bh_facturaventa.AI_facturaventa_id, bh_facturaventa.facturaventa_AI_cliente_id, bh_datoventa.TX_datoventa_entrega, bh_datoventa.TX_datoventa_descripcion,
bh_producto.TX_producto_codigo, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.AI_datoventa_id, SUM(bh_datoentrega.TX_datoentrega_cantidad) AS entregado
FROM ((((bh_datoventa
LEFT JOIN 	bh_datoentrega ON bh_datoventa.AI_datoventa_id = bh_datoentrega.datoentrega_AI_datoventa_id)
INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datoventa.datoventa_AI_producto_id)
WHERE TX_datoventa_entrega = 0 AND bh_facturaventa.facturaventa_AI_cliente_id = '$cliente_id'
GROUP BY AI_datoventa_id
ORDER BY bh_facturaf.AI_facturaf_id DESC ")or die($link->error);
  $raw_porentregar = array();
  while ($rs_porentregar = $qry_porentregar->fetch_array(MYSQLI_ASSOC)) {
    $raw_porentregar[] = $rs_porentregar;
  }
  foreach ($raw_porentregar as $key => $array) {
    if (empty($array['entregado'])) {
      $raw_porentregar[$key]['entregado'] = "0";
    }
  }
$raw_entregado=array();
  $qry_entregado = $link->query("SELECT bh_datoventa.TX_datoventa_descripcion, bh_datoentrega.TX_datoentrega_cantidad, bh_producto.TX_producto_codigo, bh_entrega.TX_entrega_fecha, bh_entrega.TX_entrega_hora, bh_entrega.AI_entrega_id, bh_entrega.AI_entrega_id
    FROM (((bh_entrega
      INNER JOIN bh_datoentrega ON bh_entrega.AI_entrega_id = bh_datoentrega.datoentrega_AI_entrega_id)
      INNER JOIN bh_datoventa ON bh_datoventa.AI_datoventa_id = bh_datoentrega.datoentrega_AI_datoventa_id)
      INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datoventa.datoventa_AI_producto_id)
      WHERE bh_entrega.entrega_AI_cliente_id = '$cliente_id' LIMIT 50")or die($link->error);
  while($rs_entregado=$qry_entregado->fetch_array(MYSQLI_ASSOC)){
    $raw_entregado[]=$rs_entregado;
  }

  $raw_return[] = $raw_porentregar;
  $raw_return[] = $raw_entregado;
  echo json_encode($raw_return);
}

function get_producto(){
  $link = conexion();
  $producto_id = $_GET['a'];

  $qry_porentregar = $link->query("SELECT  bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_descripcion, bh_datoventa.AI_datoventa_id, bh_datoventa.TX_datoventa_descripcion, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora,
	bh_cliente.TX_cliente_nombre, SUM(bh_datoentrega.TX_datoentrega_cantidad) AS entregado,
  bh_producto.TX_producto_codigo
	FROM bh_datoventa
	INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id
	INNER JOIN bh_facturaf ON bh_facturaventa.facturaventa_AI_facturaf_id = bh_facturaf.AI_facturaf_id
	INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_facturaf.facturaf_AI_cliente_id
	LEFT JOIN bh_datoentrega ON bh_datoentrega.datoentrega_AI_datoventa_id = bh_datoventa.AI_datoventa_id
  INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datoventa.datoventa_AI_producto_id
  WHERE bh_datoventa.datoventa_AI_producto_id = '$producto_id' AND bh_datoventa.TX_datoventa_entrega = '0' GROUP BY AI_datoventa_id
  ")or die($link->error);

  $raw_porentregar = array();
  while ($rs_porentregar = $qry_porentregar->fetch_array(MYSQLI_ASSOC)) {
    $raw_porentregar[] = $rs_porentregar;
  }
  foreach ($raw_porentregar as $key => $array) {
    if (empty($array['entregado'])) {
      $raw_porentregar[$key]['entregado'] = "0";
    }
    $raw_porentregar[$key]['entrega'] = "0";
  }
  $raw_entregado=array();
  $qry_entregado = $link->query("SELECT bh_datoventa.TX_datoventa_descripcion, bh_datoentrega.TX_datoentrega_cantidad, bh_entrega.TX_entrega_fecha, bh_entrega.TX_entrega_hora, bh_entrega.AI_entrega_id, bh_cliente.TX_cliente_nombre, bh_facturaf.TX_facturaf_numero
   FROM ((((((bh_entrega
  INNER JOIN bh_datoentrega ON bh_entrega.AI_entrega_id = bh_datoentrega.datoentrega_AI_entrega_id)
  INNER JOIN bh_datoventa ON bh_datoventa.AI_datoventa_id = bh_datoentrega.datoentrega_AI_datoventa_id)
  INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datoventa.datoventa_AI_producto_id)
  INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_entrega.entrega_AI_cliente_id)
  INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
  INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
  WHERE bh_datoventa.datoventa_AI_producto_id = '$producto_id' ORDER BY AI_entrega_id DESC LIMIT 30")or die($link->error);
  while($rs_entregado=$qry_entregado->fetch_array(MYSQLI_ASSOC)){
    $raw_entregado[]=$rs_entregado;
  }



  $raw_return[] = $raw_porentregar;
  $raw_return[] = $raw_entregado;
  echo json_encode($raw_return);
}

switch ($_GET['z']) {
  case 'facturaf':
    get_facturaf();
    break;
  case 'client':
    get_cliente();
    break;
  case 'product':
    get_producto();
    break;
}



?>
