<?php
require '../../bh_conexion.php';
$method = $_GET['z'];


switch ($method) {
    case 'compraByProduct':
        $product_id = $_GET['a'];
        get_compra_by_product ($product_id);
        break;
    case 'productAlarmOn':
        get_product_lowered();
        break;
    default:
        $product_id = $_GET['a'];
        get_product($product_id);
        break;
}

function get_product($product_id) {
    $link = conexion();
    $qry_product = $link->query("SELECT * FROM bh_product WHERE AI_product_id = $product_id")or die($link->error);
    $rs_product = $qry_product->fetch_array(MYSQLI_ASSOC);
    return $rs_product;
}

function get_compra_by_product($product_id) {
    $link = conexion();
    $qry = $link->query("SELECT bh_facturacompra.TX_facturacompra_fecha, bh_datocompra.TX_datocompra_precio, bh_datocompra.TX_datocompra_medida FROM bh_datocompra INNER JOIN bh_facturacompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id WHERE datocompra_AI_producto_id = $product_id ORDER BY AI_datocompra_id DESC LIMIT 5")or die($link->error);
    $recordset = $qry->fetch_array(MYSQLI_ASSOC);
    $qry_medida = $link->query("SELECT * FROM bh_medida")or die($link->error);
    $rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC);
    $array_medida = [];
    do {
        $array_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
    } while ($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC));
    $array_compra = [];
    if ($qry->num_rows > 0) {
        do {
            $array_compra[] = ["fecha"=>$recordset['TX_facturacompra_fecha'], "precio"=>$recordset['TX_datocompra_precio'], "medida"=>$array_medida[$recordset['TX_datocompra_medida']]];
        } while ($recordset = $qry->fetch_array(MYSQLI_ASSOC));
    }
    echo json_encode($array_compra);
}
function get_product_lowered(){
    $link = conexion();
    $limit = $_GET['a'];
    $qry = $link->query("SELECT * FROM bh_producto WHERE TX_producto_minimo >= TX_producto_cantidad AND TX_producto_alarma = 0 LIMIT $limit")or die($link->error);
    // $recordset = $qry->fetch_array(MYSQLI_ASSOC);
    $array_lowered = [];
    while ($recordset = $qry->fetch_array(MYSQLI_ASSOC)) {
        $array_lowered[] = $recordset;
    }
    echo json_encode($array_lowered);
}



?>