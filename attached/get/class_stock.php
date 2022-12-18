<?php
require '../../bh_conexion.php';
$link = conexion();
require '../php/req_login_stock.php';

$method = $_GET['method'];

class class_stock {
  public function filter_product ($str,$limite) {
    $link = conexion();
    $r_function = new recurrent_function;
    $value=$r_function->url_replace_special_character($str);
    $value=$r_function->replace_regular_character($value);

    $prep_precio=$link->prepare("SELECT AI_precio_id, TX_precio_cuatro FROM bh_precio WHERE precio_AI_producto_id = ? AND TX_precio_inactivo = '0' AND precio_AI_medida_id = ? ORDER BY TX_precio_fecha DESC LIMIT 1")or die($link->error);
    $prep_checkfacturaventa=$link->prepare("SELECT bh_facturaventa.AI_facturaventa_id FROM (bh_datoventa INNER JOIN bh_facturaventa ON bh_datoventa.datoventa_AI_facturaventa_id = bh_facturaventa.AI_facturaventa_id) WHERE bh_datoventa.datoventa_AI_producto_id = ?")or die($link->error);
    $prep_facturacompra=$link->prepare("SELECT bh_facturacompra.AI_facturacompra_id FROM (bh_datocompra INNER JOIN bh_facturacompra ON bh_datocompra.datocompra_AI_facturacompra_id = bh_facturacompra.AI_facturacompra_id) WHERE bh_datocompra.datocompra_AI_producto_id = ?")or die($link->error);
    $prep_facturacompra->bind_param("i", $product_id); 	/* <----BOTON ELIMINAR 	*/
    $prep_facturaventa=$link->prepare("SELECT bh_facturaventa.AI_facturaventa_id FROM (bh_datoventa INNER JOIN bh_facturaventa ON bh_datoventa.datoventa_AI_facturaventa_id = bh_facturaventa.AI_facturaventa_id) WHERE bh_datoventa.datoventa_AI_producto_id = ?")or die($link->error);
    $prep_facturaventa->bind_param("i", $product_id); 	/* <----BOTON ELIMINAR 	*/

    $arr_value = (explode(' ',$value));
    $arr_value = array_values(array_unique($arr_value));
    $txt_product="SELECT AI_producto_id, TX_producto_value, TX_producto_codigo, TX_producto_referencia, TX_producto_activo, TX_producto_minimo, TX_producto_maximo, TX_producto_cantidad, TX_producto_rotacion, TX_producto_medida, TX_producto_inventariado FROM bh_producto WHERE ";
    foreach ($arr_value as $key => $value) {
      $txt_product .= ($value === end($arr_value)) ? "TX_producto_value LIKE '%{$value}%' OR " : "TX_producto_value LIKE '%{$value}%' AND ";
    }
    foreach ($arr_value as $key => $value) {
      $txt_product .= ($value === end($arr_value)) ? "TX_producto_codigo LIKE '%{$value}%' OR " : "TX_producto_codigo LIKE '%{$value}%' AND ";
    }
    foreach ($arr_value as $key => $value) {
      $txt_product .= ($value === end($arr_value)) ? "TX_producto_referencia LIKE '%{$value}%'" : "TX_producto_referencia LIKE '%{$value}%' AND ";
    }
    $qry_product=$link->query($txt_product." ORDER BY TX_producto_value ASC LIMIT ".$limite)or die($link->error);


    $raw_stock_product=array(); $i=0;
    while($rs_product=$qry_product->fetch_array(MYSQLI_ASSOC)){
      $raw_stock_product["'".$rs_product['AI_producto_id']."'"] = $rs_product;
      $product_id = $rs_product['AI_producto_id'];
      $raw_stock_product["'".$rs_product['AI_producto_id']."'"]['btn_del_product'] = 0;
    }
    return $raw_stock_product;
  }

  public function save_product ($codigo,$value,$medida,$cantidad,$maximo,$minimo,$exento,$p_5,$p_4,$p_3,$p_2,$p_1,$referencia,$letra,$familia) {
    $link = conexion();
    $r_function = new recurrent_function;
    $value=$r_function->url_replace_special_character($value);
    $value=$r_function->replace_regular_character($value);    
    $fecha_actual=date('Y-m-d');
    $returnValue=(empty($value) || $value === ' ') ? 0 : preg_match('/\D/', $value, $matches);
    if($returnValue === 0){ return false; }

    $qry_checkproduct=$link->query("SELECT AI_producto_id FROM bh_producto WHERE TX_producto_codigo = '$codigo'")or die($link->error);
    $nr_checkproduct=$qry_checkproduct->num_rows;
    if($nr_checkproduct < 1){
      $bh_insert="INSERT INTO bh_producto (TX_producto_codigo, TX_producto_value, TX_producto_medida, TX_producto_cantidad, TX_producto_minimo, TX_producto_maximo, TX_producto_exento, TX_producto_referencia, producto_AI_letra_id, producto_AI_subfamilia_id, TX_producto_alarma) VALUES ('$codigo','$value','$medida','$cantidad','$minimo','$maximo','$exento','$referencia','$letra','$familia', 1)";
      $link->query($bh_insert) or die($link->error);
      $rs = $link->query("SELECT MAX(AI_producto_id) AS id FROM bh_producto");
      if ($row = $rs->fetch_array()) {  $lastid = trim($row[0]);  }
      $bh_insprecio="INSERT INTO bh_precio (precio_AI_producto_id, precio_AI_medida_id, TX_precio_uno, TX_precio_dos, TX_precio_tres, TX_precio_cuatro, TX_precio_cinco, TX_precio_fecha) VALUES ('$lastid','$medida','$p_1','$p_2','$p_3','$p_4','$p_5','$fecha_actual')";
      $link->query($bh_insprecio) or die($link->error);
      $link->query("INSERT INTO rel_producto_medida (productomedida_AI_medida_id, productomedida_AI_producto_id, TX_rel_productomedida_cantidad, productomedida_AI_user_id, productomedida_AI_letra_id) VALUES ('1','$lastid','1',{$_COOKIE['coo_iuser']},$letra)")or die($link->error);
    }
  }
  public function delete_product ($product_id)  {
    $link = conexion();
    $prep_facturacompra=$link->prepare("SELECT bh_facturacompra.AI_facturacompra_id FROM (bh_datocompra INNER JOIN bh_facturacompra ON bh_datocompra.datocompra_AI_facturacompra_id = bh_facturacompra.AI_facturacompra_id) WHERE bh_datocompra.datocompra_AI_producto_id = ?")or die($link->error);
    $prep_facturacompra->bind_param("i", $product_id); 	/* <----BOTON ELIMINAR 	*/
    $prep_facturacompra->execute(); $qry_facturacompra = $prep_facturacompra->get_result();
    $prep_facturaventa=$link->prepare("SELECT bh_facturaventa.AI_facturaventa_id FROM (bh_datoventa INNER JOIN bh_facturaventa ON bh_datoventa.datoventa_AI_facturaventa_id = bh_facturaventa.AI_facturaventa_id) WHERE bh_datoventa.datoventa_AI_producto_id = ?")or die($link->error);
    $prep_facturaventa->bind_param("i", $product_id); 	/* <----BOTON ELIMINAR 	*/
    $prep_facturaventa->execute(); $qry_facturaventa = $prep_facturaventa->get_result();

    $qry_datopedido=$link->query("SELECT bh_datopedido.AI_datopedido_id FROM bh_datopedido WHERE bh_datopedido.datopedido_AI_producto_id = $product_id")or die($link->error);

    $deletable = 0;
    if ($qry_facturacompra->num_rows < 1 && $qry_facturaventa->num_rows < 1 && $qry_datopedido->num_rows < 1) {
      $link->query("DELETE FROM bh_producto WHERE AI_producto_id = '$product_id' LIMIT 1")or die($link->error);
      $message = 'Eliminado';
    }else{
      $link->query("UPDATE bh_producto SET TX_producto_activo = 1 WHERE AI_producto_id = '$product_id'")or die($link->error);
      $message = 'Desactivado';
    }
    return $message;
  }
  public function update_product ($codigo,$value,$medida,$impuesto,$cantidad,$minimo,$maximo,$alarm,$activo,$pre_referencia,$letra,$descontable,$inventariado,$ubicacion,$subfamilia,$product_id) {
    $link = conexion(); $r_function = new recurrent_function();
    $reference=$r_function->url_replace_special_character($pre_referencia);
    $reference=$r_function->replace_regular_character($reference);

    $fecha_actual=date('Y-m-d');

    $qry_checkproduct=$link->query("SELECT * FROM bh_producto WHERE AI_producto_id = '$product_id'")or die($link->error);
    $nr_checkproduct=$qry_checkproduct->num_rows;
    if($nr_checkproduct > 0){
      $rs_checkproduct=$qry_checkproduct->fetch_array();
      $id=$rs_checkproduct['AI_producto_id'];
      $product_info = date('d-m-Y H:i:s')." ".$_COOKIE['coo_suser'].": /*".$rs_checkproduct['TX_producto_value']." (".$id.")"." Codigo: ".$rs_checkproduct['TX_producto_codigo']." Medida: ".$rs_checkproduct['TX_producto_medida']." Cantidad ".$rs_checkproduct['TX_producto_cantidad']." Min: ".$rs_checkproduct['TX_producto_minimo']." Max: ".$rs_checkproduct['TX_producto_maximo']." Imp: ".$rs_checkproduct['TX_producto_exento']." Alarma: ".$rs_checkproduct['TX_producto_alarma']." Activo: ".$rs_checkproduct['TX_producto_alarma']." Letra: ".$rs_checkproduct['producto_AI_letra_id']." Descontable: ".$rs_checkproduct['TX_producto_descontable']." Inventario: ".$rs_checkproduct['TX_producto_inventariado']." Subfamilia: ".$rs_checkproduct['TX_producto_inventariado']."*/";
      $bh_update="UPDATE bh_producto SET TX_producto_value='$value', TX_producto_medida='$medida', TX_producto_cantidad='$cantidad', TX_producto_minimo='$minimo', TX_producto_maximo='$maximo', TX_producto_exento='$impuesto', TX_producto_alarma='$alarm', TX_producto_activo = '$activo', TX_producto_referencia = '$reference', producto_AI_letra_id= '$letra', TX_producto_codigo = '$codigo', TX_producto_descontable = '$descontable', TX_producto_inventariado = '$inventariado', producto_AI_area_id = '$ubicacion', producto_AI_subfamilia_id = '$subfamilia' WHERE AI_producto_id = '$id'";
  
      $link->query($bh_update) or die ($link->error);
      $file = fopen("../../inventario_log.txt", "a");
      fwrite($file, $product_info." ---> ".$value." (".$id.")"." Medida: ".$medida." Cantidad ".$cantidad ." Min: ".$minimo." Max: ".$maximo." Imp: ".$impuesto." Alarma: ".$alarm." Activo: ".$activo." Letra: ".$letra." Codigo: ".$codigo." Descontable: ".$descontable." Inventario: ".$inventariado." Ubicacion: ".$ubicacion." Subfamilia: ".$subfamilia.PHP_EOL );
      fclose($file);  
    }
    return 'ok';
  }

}
$cls_stock = new class_stock;
switch ($method) {
  case 'filter':
    $str = $_GET['a'];
    $limit = $_GET['b'];
    $data_filtered = $cls_stock->filter_product($str,$limit);
    echo json_encode($data_filtered);
  break;
  case 'create':
    $cls_stock->save_product($_GET['a'],$_GET['b'],$_GET['c'],$_GET['d'],$_GET['e'],$_GET['f'],$_GET['g'],$_GET['h'],$_GET['i'],$_GET['j'],$_GET['k'],$_GET['l'],$_GET['m'],$_GET['n'],$_GET['o']);
    $data_filtered = $cls_stock->filter_product($_GET['a'],'10');
    echo json_encode($data_filtered);
  break;
  case 'delete':
    $product_id = $_GET['a'];
    $message = $cls_stock->delete_product($product_id);
    $data_filtered = $cls_stock->filter_product($_GET['b'],$_GET['c']);
    echo json_encode(["message" => $message, "filtered" => $data_filtered]);
  break;
  case 'update':
    $value=$r_function->url_replace_special_character($_GET['b']);
    $value=$r_function->replace_regular_character($value);
    $returned = $cls_stock->update_product($_GET['a'],$value,$_GET['c'],$_GET['l'],$_GET['d'],$_GET['f'],$_GET['e'],$_GET['m'],$_GET['n'],$_GET['o'],$_GET['p'],$_GET['s'],$_GET['t'],$_GET['u'],$_GET['v'],$_GET['q']);
    if($returned === 'ok')  {
      // ##################   CREAR NOTIFICACION
      $raw_user = $r_function->read_user();
      $qry_user = $link->query("SELECT AI_user_id, TX_user_seudonimo FROM bh_user WHERE TX_user_type = '2' AND TX_user_activo = '1' or TX_user_type = '5'  AND TX_user_activo = '1'")or die($link->error);
      while($rs_user=$qry_user->fetch_array(MYSQLI_ASSOC)) {
        $content =		$raw_user[$_COOKIE['coo_iuser']].' actualiz&oacute; la informacion de '.$value.' ('.$_GET['a'].')';
        $r_function->method_message('create', $_COOKIE['coo_iuser'], $rs_user['AI_user_id'], 'Producto Actualizado', $content, 'notification', date('H:i:s'), date('d-m-Y'));
      }		
    }
    $data_filtered = $cls_stock->filter_product($_GET['r'],'10');
    echo json_encode($data_filtered);
  break;
}

?>