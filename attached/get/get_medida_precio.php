<?php
require '../../bh_conexion.php';
$link = conexion();

$medida_id=$_GET['a'];
$producto_id=$_GET['b'];

$qry_medida = $link->query("SELECT TX_medida_value FROM bh_medida WHERE AI_medida_id = '$medida_id'")or die($link->error);
$rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC);

$raw_medida_precio = array();
$raw_medida_precio['titulo'] = $rs_medida['TX_medida_value'];
$raw_medida_precio['medida_id'] = $medida_id;

$qry_precio=$link->query("SELECT TX_precio_uno, TX_precio_dos, TX_precio_tres, TX_precio_cuatro, TX_precio_cinco FROM bh_precio WHERE precio_AI_producto_id = '$producto_id' AND precio_AI_medida_id = '$medida_id' AND TX_precio_inactivo = '0'")or die($link->error);
$rs_precio = $qry_precio->fetch_array(MYSQLI_ASSOC);
$raw_precio = ['TX_precio_uno' => $rs_precio['TX_precio_uno'], 'TX_precio_dos' => $rs_precio['TX_precio_dos'], 'TX_precio_tres' => $rs_precio['TX_precio_tres'], 'TX_precio_cuatro' => $rs_precio['TX_precio_cuatro'], 'TX_precio_cinco' => $rs_precio['TX_precio_cinco']];
$raw_medida_precio['precio'] = $raw_precio;

$qry_datocompra_listado = $link->query("SELECT bh_facturacompra.TX_facturacompra_fecha,bh_datocompra.TX_datocompra_precio,bh_datocompra.TX_datocompra_impuesto,bh_datocompra.TX_datocompra_descuento FROM ((bh_datocompra INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datocompra.datocompra_AI_producto_id) INNER JOIN bh_facturacompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id) WHERE bh_producto.AI_producto_id = '$producto_id' AND bh_datocompra.TX_datocompra_medida = '$medida_id' ORDER BY TX_facturacompra_fecha DESC, AI_facturacompra_id DESC")or die($link->error);
$raw_datocompra_listado = array();
while($rs_datocompra_listado = $qry_datocompra_listado->fetch_array(MYSQLI_ASSOC)){
  $raw_datocompra_listado[] = $rs_datocompra_listado;
}
$raw_medida_precio['datocompra_listado'] = $raw_datocompra_listado;

$qry_precio_listado = $link->query("SELECT bh_precio.AI_precio_id, bh_precio.TX_precio_fecha, bh_precio.TX_precio_uno, bh_precio.TX_precio_dos, bh_precio.TX_precio_tres, bh_precio.TX_precio_cuatro, bh_precio.TX_precio_cinco, bh_producto.AI_producto_id FROM (bh_precio INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_precio.precio_AI_producto_id) WHERE bh_producto.AI_producto_id = '$producto_id' AND bh_precio.precio_AI_medida_id = '$medida_id' ORDER BY TX_precio_fecha DESC, AI_precio_id DESC")or die($link->error);
$raw_precio_listado = array();
while ($rs_precio_listado = $qry_precio_listado->fetch_array(MYSQLI_ASSOC)) {
  $raw_precio_listado[] = $rs_precio_listado;
}
$raw_medida_precio['precio_listado'] = $raw_precio_listado;

echo json_encode($raw_medida_precio);
?>
