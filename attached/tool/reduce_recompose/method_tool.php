<?php
include '../method_crud.php';
date_default_timezone_set('America/Panama');

function add_minus_item($producto_id, $cantidad){
  $crud_function = new method_crud(); $public_bd = new public_access_bd();
  $raw_columna=["TX_producto_value"]; $raw_where = ["AI_producto_id" => $producto_id];
  $rs_producto = $public_bd->consultar_bh_producto($raw_columna,$raw_where);

  $name_tool = $crud_function->get_name_tool();
  $json_contenido = $crud_function->read_json_tool($name_tool);
  $raw_contenido =  json_decode($json_contenido, true);
  $next_index = (!empty($raw_contenido['minus'])) ? count($raw_contenido['minus']) : 0;
  $raw_contenido['minus'][$next_index]['producto_id'] = $producto_id;
  $raw_contenido['minus'][$next_index]['producto_value'] = $rs_producto['TX_producto_value'];
  $raw_contenido['minus'][$next_index]['cantidad'] = $cantidad;
  $json_contenido = json_encode($raw_contenido);
  $crud_function->write_json_tool($name_tool,$json_contenido);
  echo $json_contenido;
}
function add_plus_item($producto_id, $cantidad){
  $crud_function = new method_crud(); $public_bd = new public_access_bd();
  $raw_columna=["TX_producto_value"]; $raw_where = ["AI_producto_id" => $producto_id];
  $rs_producto = $public_bd->consultar_bh_producto($raw_columna,$raw_where);

  $name_tool = $crud_function->get_name_tool();
  $json_contenido = $crud_function->read_json_tool($name_tool);
  $raw_contenido =  json_decode($json_contenido, true);
  $next_index = (!empty($raw_contenido['plus'])) ? count($raw_contenido['plus']) : 0;
  $raw_contenido['plus'][$next_index]['producto_id'] = $producto_id;
  $raw_contenido['plus'][$next_index]['producto_value'] = $rs_producto['TX_producto_value'];
  $raw_contenido['plus'][$next_index]['cantidad'] = $cantidad;
  $json_contenido = json_encode($raw_contenido);
  $crud_function->write_json_tool($name_tool,$json_contenido);
  echo $json_contenido;
}
function del_minus_item($position){
  $crud_function = new method_crud(); $public_bd = new public_access_bd();
  $name_tool = $crud_function->get_name_tool();
  $json_contenido = $crud_function->read_json_tool($name_tool);
  $raw_contenido =  json_decode($json_contenido, true);
  unset($raw_contenido['minus'][$position]);
  $json_contenido = json_encode($raw_contenido);
  $crud_function->write_json_tool($name_tool,$json_contenido);
  echo $json_contenido;
}
function del_plus_item($position){
  $crud_function = new method_crud(); $public_bd = new public_access_bd();
  $name_tool = $crud_function->get_name_tool();
  $json_contenido = $crud_function->read_json_tool($name_tool);
  $raw_contenido =  json_decode($json_contenido, true);
  unset($raw_contenido['plus'][$position]);
  $json_contenido = json_encode($raw_contenido);
  $crud_function->write_json_tool($name_tool,$json_contenido);
  echo $json_contenido;
}
function save_reduce_recompose(){
  $crud_function = new method_crud(); $public_bd = new public_access_bd();
  $name_tool = $crud_function->get_name_tool();
  $json_contenido = $crud_function->read_json_tool($name_tool);
  $raw_contenido =  json_decode($json_contenido, true);

  $raw_minus=array();
  foreach ($raw_contenido['minus'] as $key => $value) {
    $raw_columna=["TX_producto_value"]; $raw_where = ["AI_producto_id" => $value['producto_id']];
    $rs_producto = $public_bd->consultar_bh_producto($raw_columna,$raw_where);
    $raw_minus[$key]['producto_id']=$value['producto_id'];
    $raw_minus[$key]['cantidad']=$value['cantidad'];
    $raw_minus[$key]['descripcion']=$rs_producto['TX_producto_value'];
    // $raw_minus[$value['producto_id']]=$value['cantidad'];
    $public_bd->upd_TX_producto_cantidad($value['producto_id'],($value['cantidad']*-1));
  }
  $raw_plus=array();
  foreach ($raw_contenido['plus'] as $key => $value) {
    $raw_columna=["TX_producto_value"]; $raw_where = ["AI_producto_id" => $value['producto_id']];
    $rs_producto = $public_bd->consultar_bh_producto($raw_columna,$raw_where);
    $raw_plus[$key]['producto_id']=$value['producto_id'];
    $raw_plus[$key]['cantidad']=$value['cantidad'];
    $raw_plus[$key]['descripcion']=str_replace("'"," ",$rs_producto['TX_producto_value']);

    $public_bd->upd_TX_producto_cantidad($value['producto_id'],($value['cantidad']*1));
}
  $fecha_actual = date('Y-m-d');
  $array_2_save = ["fecha"=>$fecha_actual,"user_id"=>$_COOKIE['coo_iuser'],"minus"=>$raw_minus,"plus"=>$raw_plus];
  $raw_contenido['saved'][] = $array_2_save;
  unset($raw_contenido['plus']);
  unset($raw_contenido['minus']);
  $json_contenido = json_encode($raw_contenido);
  $crud_function->write_json_tool($name_tool,$json_contenido);
  echo $json_contenido;
}
function filter_rr(){
  $crud_function = new method_crud(); $public_bd = new public_access_bd(); $r_function = new recurrent_function();
  $name_tool = $crud_function->get_name_tool();
  $json_contenido = $crud_function->read_json_tool($name_tool);
  $raw_contenido =  json_decode($json_contenido, true);
  $str = $r_function->url_replace_special_character($_GET['a']);
  $str = $r_function->replace_regular_character($str);
  $raw_finded=array();
  foreach ($raw_contenido['saved'] as $index => $saved) {
    foreach ($saved['minus'] as $key => $minus) {
      if (ereg($str,$minus['descripcion'])) {
        $raw_finded[$index]=$saved;
      }
    }
    foreach ($saved['plus'] as $key => $plus) {
      if (ereg($str,$plus['descripcion'])) {
        $raw_finded[$index]=$saved;
      }
    }
  }
  echo json_encode($raw_finded);
}


$method = $_GET['z'];
switch ($method) {
  case 'add_minus_item':
    add_minus_item($_GET['a'], $_GET['b']);
    break;
  case 'add_plus_item':
    add_plus_item($_GET['a'], $_GET['b']);
    break;
  case 'del_minus_item':
    del_minus_item($_GET['a']);
    break;
  case 'del_plus_item':
    del_plus_item($_GET['a']);
    break;
  case 'save_reduce_recompose':
    save_reduce_recompose();
    break;
  case 'filter':
    filter_rr();
    break;
}

 ?>
