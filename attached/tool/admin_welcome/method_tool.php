<?php
include '../method_crud.php';
date_default_timezone_set('America/Panama');
function save_motd ($new_motd) {
  $crud_function = new method_crud();
  $name_tool = $crud_function->get_name_tool();
  $json_contenido = $crud_function->read_json_tool($name_tool);
  $raw_contenido = json_decode($json_contenido, true);
  $raw_contenido['saved'][0]['motd'][] = ['fecha'=>date('Y-m-d'),'message'=>$new_motd];
  $crud_function->write_json_tool($name_tool,json_encode($raw_contenido));
  echo 'exito';
}
function save_phrase ($new_phrase) {
  $crud_function = new method_crud();
  $name_tool = $crud_function->get_name_tool();
  $json_contenido = $crud_function->read_json_tool($name_tool);
  $raw_contenido = json_decode($json_contenido, true);
  $raw_contenido['saved'][1]['phrase'][] = ['message'=>$new_phrase];
  $crud_function->write_json_tool($name_tool,json_encode($raw_contenido));
  echo 'exito';
}

$method = $_GET['z'];
switch ($method) {
  case 'save_motd':
    save_motd($_GET['a']);
    break;
  case 'save_phrase':
    save_phrase($_GET['a']);
    break;
}

 ?>
