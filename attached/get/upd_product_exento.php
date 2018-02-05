<?php
require '../../bh_conexion.php';
$link=conexion();
$total_tax=$_GET['a'];
$product_id=$_GET['b'];
$raw_taxes=$_GET['c'];

$link->query("UPDATE bh_producto SET TX_producto_exento = '$total_tax' WHERE AI_producto_id = '$product_id'")OR die($link->error);

$link->query("DELETE FROM rel_producto_impuesto WHERE rel_AI_producto_id = '$product_id'");
// insertar relacion producto-impuesto
foreach ($raw_taxes as $key => $taxes) {
  $qry_chkspecial=$link->query("SELECT AI_impuesto_id FROM bh_impuesto WHERE AI_impuesto_id = '{$taxes['id']}' AND TX_impuesto_categoria = 'ESPECIAL'");
  if ($qry_chkspecial->num_rows > 0) {

    $qry_chkrel=$link->query("SELECT * FROM rel_producto_impuesto WHERE rel_AI_producto_id = '$product_id' AND rel_AI_impuesto_id = '{$taxes['id']}'") or die($link->error);
    if($qry_chkrel->num_rows === 0){
      $link->query("INSERT INTO rel_producto_impuesto (rel_AI_producto_id, rel_AI_impuesto_id) VALUES ('$product_id','{$taxes['id']}')");
    }

  }
}
echo $total_tax;
$link->close();
?>
