<?php
require '../../bh_conexion.php';
$link=conexion();
$order_id = $_GET['a'];

$qry_datopedido=$link->query("SELECT  bh_datopedido.AI_datopedido_id, bh_datopedido.TX_datopedido_precio, bh_datopedido.TX_datopedido_cantidad, bh_datopedido.datopedido_AI_producto_id, bh_producto.TX_producto_value, bh_producto.TX_producto_codigo
  FROM (bh_datopedido
  INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datopedido.datopedido_AI_producto_id)
  WHERE datopedido_AI_pedido_id = '$order_id'")or die($link->error);
$raw_datopedido = array();  $i=0;


while ($rs_datopedido = $qry_datopedido->fetch_array()) {
  $qry_producto=$link->query("SELECT TX_producto_exento FROM bh_producto WHERE AI_producto_id = '{$rs_datopedido['datopedido_AI_producto_id']}'")or die($link->error);
  $rs_producto=$qry_producto->fetch_array();

  $raw_datopedido[$i]['id'] = $rs_datopedido['datopedido_AI_producto_id'];
  $raw_datopedido[$i]['codigo'] = $rs_datopedido['TX_producto_codigo'];
  $raw_datopedido[$i]['nombre'] = $rs_datopedido['TX_producto_value'];
  $raw_datopedido[$i]['cantidad'] = $rs_datopedido['TX_datopedido_cantidad'];
  $raw_datopedido[$i]['precio'] = $rs_datopedido['TX_datopedido_precio'];
  $raw_datopedido[$i]['impuesto'] = $rs_producto['TX_producto_exento'];
  $i++;
}

echo json_encode($raw_datopedido);
