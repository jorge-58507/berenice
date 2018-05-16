<?php
include_once '../../../bh_conexion.php';

class method_crud {
  public function get_name_tool(){
    $dirname = getcwd();
    $array_dirname = explode("\\",$dirname);
    $name_tool = end($array_dirname);
    return $name_tool;
  }
  public function read_json_tool($name_tool){
    $content = file_get_contents($name_tool.".json");
    return $content;
  }
  public function write_json_tool($name_tool,$json_contenido){
    $file = fopen($name_tool.".json", 'w');
    fwrite($file, $json_contenido);
    fclose($file);
  }
}

class public_access_bd {
  public function get_lista_producto($limit){
    $link = conexion();
    $qry_producto=$link->query("SELECT AI_producto_id, TX_producto_codigo, TX_producto_value, TX_producto_cantidad FROM bh_producto WHERE TX_producto_activo = 0 ORDER BY TX_producto_value ASC LIMIT $limit")or die($link->error);
    $raw_producto = array();
    while ($rs_producto=$qry_producto->fetch_array()) {
      $raw_producto[]=$rs_producto;
    }
    return $raw_producto;
  }
  public function get_tbl_medida(){
    $link = conexion();
    $qry_medida = $link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida");
    $raw_medida = array();
    while($rs_medida=$qry_medida->fetch_array(MYSQLI_ASSOC)){
      $raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
    }
    return $raw_medida;
  }
  public function consultar_bh_producto($raw_columna,$raw_where){
    $link = conexion();
    $txt_consulta = "SELECT ";
    foreach($raw_columna as $key => $columna){
      $txt_consulta .= ($columna != end($raw_columna)) ? $columna."," : $columna;
    }
    $txt_consulta .= " FROM bh_producto WHERE ";
    foreach($raw_where as $field => $value){
      $txt_consulta .= ($value != end($raw_where)) ? $field." = ".$value." AND " : $field." = ".$value;
    }
    $qry_producto = $link->query($txt_consulta)or die($link->error);
    return $rs_producto=$qry_producto->fetch_array();
  }
  public function upd_TX_producto_cantidad($producto_id,$cantidad){
    $link = conexion();
    $qry_producto=$link->query("SELECT TX_producto_cantidad FROM bh_producto WHERE AI_producto_id = '$producto_id'")or die($link->error);
    $rs_producto=$qry_producto->fetch_array(MYSQLI_ASSOC);
    $n_cantidad = $rs_producto['TX_producto_cantidad'] + $cantidad;
    $link->query("UPDATE bh_producto SET TX_producto_cantidad = '$n_cantidad' WHERE AI_producto_id = '$producto_id'")or die($link->error);
    $link->close();
    return $rs_producto=$qry_producto->fetch_array();
  }
  public function consultar_medida_x_producto($producto_id){
    $link = conexion();
    $qry_medida = $link->query("SELECT rel_producto_medida.productomedida_AI_medida_id, rel_producto_medida.TX_rel_productomedida_cantidad, bh_medida.TX_medida_value FROM (rel_producto_medida INNER JOIN bh_medida ON bh_medida.AI_medida_id = rel_producto_medida.productomedida_AI_medida_id) WHERE productomedida_AI_producto_id = '$producto_id'")or die($link->error);
    $raw_medida= array();
    while($rs_medida=$qry_medida->fetch_array(MYSQLI_ASSOC)){
      $raw_medida[] = $rs_medida;
    }
    return $raw_medida;
  }
}
?>
