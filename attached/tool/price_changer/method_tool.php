<?php
include '../method_crud.php';
date_default_timezone_set('America/Panama');

function save_group(){
  $crud_function = new method_crud(); $public_bd = new public_access_bd();
  $name_tool = $crud_function->get_name_tool();
  $json_contenido = $crud_function->read_json_tool($name_tool);
  $raw_contenido =  json_decode($json_contenido, true);
  $raw_group=$_GET['a'];
  $raw_contenido['GROUP'][$_GET['b']] = $raw_group;
  // echo json_encode($raw_group);

  $json_contenido = json_encode($raw_contenido);
  $crud_function->write_json_tool($name_tool,$json_contenido);
  echo '<tr><td colspan="4"></td></tr>';
}


$method = $_GET['z'];
switch ($method) {
  case 'save_group':
    save_group();
    break;
}

 ?>
