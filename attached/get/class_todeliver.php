<?php
require '../../bh_conexion.php';
$link = conexion();
require '../php/req_login_stock.php';

$json_request = trim(file_get_contents("php://input"));
$request = json_decode($json_request, true);

class class_todeliver {
  public function get_todeliver () {

  }
}

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    // echo json_encode(["message" => $_SERVER['REQUEST_URI']]);
    $qry_facturaf = $link->query("SELECT bh_producto.AI_producto_id, bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_cliente.TX_cliente_nombre, bh_cliente.AI_cliente_id, bh_datoventa.TX_datoventa_descripcion, bh_datoventa.TX_datoventa_cantidad
    FROM ((((bh_facturaf
    INNER JOIN bh_cliente ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id)
    INNER JOIN bh_facturaventa ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
    INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
    INNER JOIN bh_producto ON bh_producto.AI_producto_id = bh_datoventa.datoventa_AI_producto_id)
    WHERE bh_datoventa.TX_datoventa_entrega = 0 ORDER BY AI_facturaf_id DESC");
    $raw_porentregar = array();
    while ($rs_facturaf = $qry_facturaf->fetch_array(MYSQLI_ASSOC)) {
      $raw_porentregar[] = $rs_facturaf;
    }
    echo json_encode(["array_obj" => $raw_porentregar, "message" => 'llamado']);

    break;
  
  default:
    # code...
    break;
}

?>