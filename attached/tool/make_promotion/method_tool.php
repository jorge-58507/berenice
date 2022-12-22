<?php
include '../method_crud.php';
date_default_timezone_set('America/Panama');

function get_producto_medida($producto_id){
  $crud_function = new method_crud(); $public_bd = new public_access_bd(); $r_function = new recurrent_function();
  $raw_columna=["TX_producto_value", "TX_producto_exento"]; $raw_where = ["AI_producto_id" => $producto_id];
  $rs_producto = $public_bd->consultar_bh_producto($raw_columna,$raw_where);
  $rs_medida = $public_bd->consultar_medida_x_producto($producto_id);
  $contenido_select = '';
  foreach ($rs_medida as $key => $value) {
    $contenido_select .= '<option value="'.$value['productomedida_AI_medida_id'].'">'.$value['TX_medida_value'].'</option>';
  }

  $contenido = '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <label for="" class="label label_blue_sky">Producto</label>
    <span id="span_descripcion" class="form-control">'.$r_function->replace_special_character($rs_producto['TX_producto_value']).'</span>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <label for="" class="label label_blue_sky">Medida</label>
    <select class="form-control" id="sel_promotion_medida" name="">
      '.$contenido_select.'
    </select>
  </div>
  <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
    <label for="" class="label label_blue_sky">Cantidad</label>
    <input type="text" id="txt_promotion_cantidad" class="form-control" value="" placeholder="0">
  </div>
  <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
    <label for="" class="label label_blue_sky">Precio</label>
    <input type="text" id="txt_promotion_precio" class="form-control" value="" placeholder="0">
  </div>
  <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
    <label for="" class="label label_blue_sky">Desc.</label>
    <input type="text" id="txt_promotion_descuento" class="form-control" value="0">
  </div>
  <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
    <label for="" class="label label_blue_sky">Imp.</label>
    <input type="text" id="txt_promotion_impuesto" class="form-control" value="'.$rs_producto['TX_producto_exento'].'">
  </div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center">
    <button type="button" class="btn btn-success btn-sm" onclick="add_promotion_product('.$producto_id.')">Guardar</button>
    &nbsp;&nbsp;
    <button type="button" class="btn btn-warning btn-sm" onclick="close_modal()">Cancelar</button>
  </div>';
  echo $contenido;
}
function save_promotion($json_promotion,$titulo,$descripcion,$tipo,$fecha){
  $link = conexion();
  $link->query("INSERT INTO bh_promocion (TX_promocion_componente, TX_promocion_tipo, TX_promocion_descripcion, TX_promocion_titulo, TX_promocion_fecha) VALUES ('$json_promotion','$tipo','$descripcion','$titulo','$fecha')")or die($link->error);
// ###### answer    ######
  $qry_promocion = $link->query("SELECT AI_promocion_id, TX_promocion_componente, TX_promocion_tipo, TX_promocion_titulo, TX_promocion_descripcion, TX_promocion_fecha FROM bh_promocion")or die($link->error);
  $raw_promocion = array();
  while ($rs_promocion = $qry_promocion->fetch_array(MYSQLI_ASSOC)) {
    if ($rs_promocion['TX_promocion_tipo'] < 4) {
      $raw_promocion[] = $rs_promocion;
    }
  }
  echo json_encode($raw_promocion);
}
function get_promotion($promocion_id){
  $public_bd = new public_access_bd();
  $link = conexion();
  $qry_promocion=$link->query("SELECT AI_promocion_id, TX_promocion_componente FROM bh_promocion WHERE AI_promocion_id = '$promocion_id'")or die($link->error);
  $rs_promocion = $qry_promocion->fetch_array();
  $componente = $rs_promocion['TX_promocion_componente'];
  $raw_componente = json_decode($componente, true);
  foreach ($raw_componente as $key => $value) {
    $raw_columna=["TX_producto_value", "TX_producto_exento"]; $raw_where = ["AI_producto_id" => $key];
    $rs_producto = $public_bd->consultar_bh_producto($raw_columna,$raw_where);
    $raw_componente[$key]['producto_value'] = $rs_producto['TX_producto_value'] ;
    $raw_medida = $public_bd->get_tbl_medida();
    $raw_componente[$key]['medida_value'] = $raw_medida[$value['medida']] ;
  }
  echo $componente = json_encode($raw_componente);
}
function del_promotion($promocion_id){
  $link = conexion();
  $link->query("DELETE FROM bh_promocion WHERE AI_promocion_id = '$promocion_id'")or die($link->error);
  $qry_promocion = $link->query("SELECT AI_promocion_id, TX_promocion_componente, TX_promocion_tipo, TX_promocion_titulo, TX_promocion_descripcion, TX_promocion_fecha FROM bh_promocion")or die($link->error);
  $raw_promocion = array();
  while ($rs_promocion = $qry_promocion->fetch_array(MYSQLI_ASSOC)) {
    if ($rs_promocion['TX_promocion_tipo'] < 4) {
      $raw_promocion[] = $rs_promocion;
    }
  }
  echo json_encode($raw_promocion);
}


$method = $_GET['z'];
switch ($method) {
  case 'get_producto_descripcion':
    get_producto_descripcion($_GET['a']);
    break;
  case 'get_producto_medida':
    get_producto_medida($_GET['a']);
    break;
  case 'save_promotion':
    save_promotion($_GET['a'],$_GET['b'],$_GET['c'],$_GET['d'],date('Y-m-d'));
    break;
  case 'get_promotion':
    get_promotion($_GET['a']);
    break;
  case 'del_promotion':
    del_promotion($_GET['a']);
    break;
}

 ?>
