<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_paydesk.php';
$raw_generate = json_decode($_SESSION["str_generate"],true);

if ($raw_generate['method'] === 'facturaventa') {

  $qry_lastff = $link->query('SELECT bh_facturaf.TX_facturaf_numero FROM bh_facturaf ORDER BY AI_facturaf_id DESC LIMIT 1');
  $rs_lastff = $qry_lastff->fetch_array();

  $str_factid = $raw_generate['factid'];
  $arr_factid = explode(",",$str_factid);

  $factid = "";
  foreach ($arr_factid as $a => $value) {
    $factid .= $value.",";
  }
  $factid = substr($factid,0,-1)."";

  $txt_fact = "SELECT bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_direccion, bh_cliente.TX_cliente_dv, bh_cliente.TX_cliente_correo, bh_cliente.TX_cliente_contribuyente, bh_cliente.TX_cliente_tipo
  FROM (bh_facturaventa 
  INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
  WHERE bh_facturaventa.AI_facturaventa_id IN ($factid)";

  $qry_fact = $link->query($txt_fact)or die($link->error);
  $rs_fact = $qry_fact->fetch_array();

  $fac_number = '000000'.$rs_lastff['TX_facturaf_numero'];
  $fac_number = substr($fac_number,-5);
  $documento = "FACTI".$fac_number;

  $qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida")or die($link->error);
  $raw_medida = array();
  while($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)){
    $raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
  }

  $txt_datoventa="SELECT bh_producto.AI_producto_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_descripcion,
  bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_descuento, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_medida
  FROM ((bh_facturaventa
  INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
  INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
  WHERE bh_facturaventa.AI_facturaventa_id IN ($factid)";

  $qry_datoventa=$link->query($txt_datoventa)or die($link->error);
  $rs_datoventa=$qry_datoventa->fetch_array();
  // $base_noimpo = 0;
  // $base_impo = 0;
  // $ttl_descuento_ni = 0;
  // $ttl_descuento_ci = 0;
  // $ttl_impuesto = 0;
  $facmv = '';
  $raw_datoventa = [];
  do {
    $raw_datoventa[] = ['cantidad' => $rs_datoventa['TX_datoventa_cantidad'], 'precio' => $rs_datoventa['TX_datoventa_precio'], 'descuento' => $rs_datoventa['TX_datoventa_descuento'], 'alicuota' => $rs_datoventa['TX_datoventa_impuesto']];
    // $descuento = ($rs_datoventa['TX_datoventa_precio']*$rs_datoventa['TX_datoventa_descuento'])/100;
    // $descuento = round($descuento,2);
    // $precio_descuento = ($rs_datoventa['TX_datoventa_precio']-$descuento) * $rs_datoventa['TX_datoventa_cantidad'];

    // if ($rs_datoventa['TX_datoventa_impuesto'] === "0") {
    //   $base_noimpo += $precio_descuento;
    //   $ttl_descuento_ni += $descuento*$rs_datoventa['TX_datoventa_cantidad'];
    // }else{
    //   $base_impo += $precio_descuento;
    //   $ttl_descuento_ci += $descuento*$rs_datoventa['TX_datoventa_cantidad'];

    //   $impuesto = ($precio_descuento*$rs_datoventa['TX_datoventa_impuesto'])/100;
    //   $ttl_impuesto += round($impuesto,2);
    // }
    $descripcion=$r_function->replace_special_character($rs_datoventa['TX_datoventa_descripcion']);
    $facmv .= $documento.chr(9).substr($rs_datoventa['TX_producto_codigo'],-6).chr(9).substr($raw_medida[$rs_datoventa['TX_datoventa_medida']],0,3)." ".trim($descripcion).chr(9).$raw_medida[$rs_datoventa['TX_datoventa_medida']].chr(9).$rs_datoventa['TX_datoventa_cantidad'].chr(9).$rs_datoventa['TX_datoventa_precio'].chr(9).$rs_datoventa['TX_datoventa_impuesto'].chr(9). PHP_EOL;
  } while ($rs_datoventa=$qry_datoventa->fetch_array());
    $raw_total = $r_function->calcular_factura($raw_datoventa);
  // $total = $base_impo + $base_noimpo + $ttl_impuesto;

}else{

  $factid = $raw_generate['factid'];

  $txt_fact = "SELECT bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_direccion, bh_cliente.TX_cliente_dv, bh_cliente.TX_cliente_correo, bh_cliente.TX_cliente_contribuyente, bh_cliente.TX_cliente_tipo,
  bh_facturaf.TX_facturaf_numero
  FROM (bh_facturaventa 
  INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
  INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id
  WHERE bh_facturaf.AI_facturaf_id IN ($factid)";

  $qry_fact = $link->query($txt_fact)or die($link->error);
  $rs_fact = $qry_fact->fetch_array();

  $fac_number = substr($rs_fact['TX_facturaf_numero'],0,5);
  $documento = "FACTI".$fac_number;

  $qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida")or die($link->error);
  $raw_medida = array();
  while($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)){
    $raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
  }

  
  $txt_datoventa="SELECT bh_producto.AI_producto_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_descripcion,
  bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_descuento, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_medida
  FROM (((bh_facturaventa
  INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
  INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
  INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
  WHERE bh_facturaf.AI_facturaf_id IN ($factid)";

  $qry_datoventa=$link->query($txt_datoventa)or die($link->error);
  $rs_datoventa=$qry_datoventa->fetch_array();
  // $base_noimpo = 0;
  // $base_impo = 0;
  // $ttl_descuento_ni = 0;
  // $ttl_descuento_ci = 0;
  // $ttl_impuesto = 0;
  $facmv = '';
  $raw_datoventa = [];
  do {
    $raw_datoventa[] = ['cantidad' => $rs_datoventa['TX_datoventa_cantidad'], 'precio' => $rs_datoventa['TX_datoventa_precio'], 'descuento' => $rs_datoventa['TX_datoventa_descuento'], 'alicuota' => $rs_datoventa['TX_datoventa_impuesto']];
    // $descuento = ($rs_datoventa['TX_datoventa_precio']*$rs_datoventa['TX_datoventa_descuento'])/100;
    // $descuento = round($descuento,2);
    // $precio_descuento = ($rs_datoventa['TX_datoventa_precio']-$descuento) * $rs_datoventa['TX_datoventa_cantidad'];

    // if ($rs_datoventa['TX_datoventa_impuesto'] === "0") {
    //   $base_noimpo += $precio_descuento;
    //   $ttl_descuento_ni += $descuento*$rs_datoventa['TX_datoventa_cantidad'];
    // }else{
    //   $base_impo += $precio_descuento;
    //   $ttl_descuento_ci += $descuento*$rs_datoventa['TX_datoventa_cantidad'];

    //   $impuesto = ($precio_descuento*$rs_datoventa['TX_datoventa_impuesto'])/100;
    //   $ttl_impuesto += round($impuesto,2);
    // }
    $descripcion=$r_function->replace_special_character($rs_datoventa['TX_datoventa_descripcion']);
    $facmv .= $documento.chr(9).substr($rs_datoventa['TX_producto_codigo'],-6).chr(9).substr($raw_medida[$rs_datoventa['TX_datoventa_medida']],0,3)." ".trim($descripcion).chr(9).$raw_medida[$rs_datoventa['TX_datoventa_medida']].chr(9).$rs_datoventa['TX_datoventa_cantidad'].chr(9).$rs_datoventa['TX_datoventa_precio'].chr(9).$rs_datoventa['TX_datoventa_impuesto'].chr(9). PHP_EOL;
  } while ($rs_datoventa=$qry_datoventa->fetch_array());
  $raw_total = $r_function->calcular_factura($raw_datoventa);
  // $total = $base_impo + $base_noimpo + $ttl_impuesto;


}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- <meta http-equiv='Refresh' content='15;url=paydesk.php' /> -->
<title>Trilli, S.A. - Todo en Materiales</title>

<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap-theme.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_layout.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />
<link href="attached/css/sell_css.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>

<script type="text/javascript">

  $(document).ready(function() {
    $("#btn_facmv").click();
    setTimeout(function(){ $("#btn_facti").click(); }, 1500);
  });

</script>

</head>
<body>
<div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div id="logo_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-2" >
      <div id="logo" ></div>
    </div>

  	<div id="navigation_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-10">
  		<div id="navigation" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"></div>
  	</div>
  </div>

<div id="content-sidebar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<form action="" method="post" name="print_f_fiscal"  id="print_f_fiscal">
<?php
$raw_facti=[
  "documento" => "", 
  "c_nombre" => "", 
  "c_ruc" => "", 
  "c_direccion" => "", 
  "total_descuento" => "", 
  "total_pagado" => "", 
  "total_final" => "", 
  "recargo" => "", 
  "porcentaje_recargo" => "",
  "p_efectivo" => "", 
  "p_cheque" => "", 
  "p_tdc" => "", 
  "p_tdd" => "", 
  "p_nc" => "", 
  "p_otro" => 0, 
  "dv" => "",
  "c_email" => "",
  "c_contribuyente" => "",
  "c_tipo" => "",
  "nota" => ""
];
$raw_facti['documento']       = $documento;
$raw_facti['c_nombre']        = $r_function->replace_special_character_no_html($rs_fact['TX_cliente_nombre']);
$raw_facti['c_ruc']           = $rs_fact['TX_cliente_cif'];
$raw_facti['c_direccion']     = (strlen($rs_fact['TX_cliente_direccion']) > 7) ? substr($rs_fact['TX_cliente_direccion'],0,140) : 'NO INDICA';
$raw_facti['total_descuento'] = round($raw_total['ttl_descuento'],2); 
$raw_facti['total_pagado']    = round($raw_total['total'],2);
$raw_facti['total_final']     = round($raw_total['total'],2);
$raw_facti['recargo']         = 0;
$raw_facti['porcentaje_recargo'] = 0;
$raw_facti['p_efectivo']      = '0.00';
$raw_facti['p_cheque']        = '0.00';
$raw_facti['p_tdc']           = '0.00';
$raw_facti['p_tdd']           = '0.00';
$raw_facti['p_nc']            = '0.00';
$raw_facti['p_otro']          = round($raw_total['total'],2);

$raw_facti['dv']              = $rs_fact['TX_cliente_dv'];
$raw_facti['c_email']         = $rs_fact['TX_cliente_correo'];
$raw_facti['c_contribuyente'] = $rs_fact['TX_cliente_contribuyente'];
$raw_facti['c_tipo']          = $rs_fact['TX_cliente_tipo'];
$raw_facti['nota']            = '';


/* ###################### ENCABEZADO  ######################## */
$file = fopen("GENERADO/".$documento.".txt", "w");
$str_factid="";
foreach ($raw_facti as $key => $value) {
  if ($value === reset($raw_facti)) {
    $str_factid .= $value;
  }else{
    $str_factid .= chr(9).$value;
  }
}
fwrite($file, $str_factid  );
fclose($file);
/* ####################### ENCABEZADO  ###################### */
/* ####################### ARTICULOS  ###################### */
// $txt_datoventa="SELECT bh_producto.AI_producto_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_descripcion,
// bh_datoventa.TX_datoventa_precio AS precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_medida
// FROM (((bh_facturaf
// INNER JOIN bh_facturaventa ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
// INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
// INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
// WHERE bh_facturaf.AI_facturaf_id = '$facturaf_id'";
// $qry_datoventa=$link->query($txt_datoventa)or die($link->error);
// $rs_datoventa=$qry_datoventa->fetch_array();

// $qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida")or die($link->error);
// $raw_medida = array();
// while($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)){
//   $raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
// }

$file = fopen("GENERADO/FACMV".$fac_number.".txt", "w");


// ARREGLAR EL ARTICULOS

// if ($qry_datoventa->num_rows > 3) {
//   do{
//     $descripcion=substr($r_function->replace_special_character($rs_datoventa['TX_datoventa_descripcion']),0,31);
//     fwrite($file, $documento.chr(9).substr($rs_datoventa['TX_producto_codigo'],-6).chr(9).substr($raw_medida[$rs_datoventa['TX_datoventa_medida']],0,3)." ".trim($descripcion).chr(9).$raw_medida[$rs_datoventa['TX_datoventa_medida']].chr(9).$rs_datoventa['TX_datoventa_cantidad'].chr(9).$rs_datoventa['precio'].chr(9).$rs_datoventa['TX_datoventa_impuesto'].chr(9). PHP_EOL);
//   }while($rs_datoventa=$qry_datoventa->fetch_array());
// }else{
//   do{
//     $descripcion=substr($r_function->replace_special_character($rs_datoventa['TX_datoventa_descripcion']),0,61);
//     fwrite($file, $documento.chr(9).substr($rs_datoventa['TX_producto_codigo'],-6).chr(9).substr($raw_medida[$rs_datoventa['TX_datoventa_medida']],0,3)." ".trim($descripcion).chr(9).$raw_medida[$rs_datoventa['TX_datoventa_medida']].chr(9).$rs_datoventa['TX_datoventa_cantidad'].chr(9).$rs_datoventa['precio'].chr(9).$rs_datoventa['TX_datoventa_impuesto'].chr(9). PHP_EOL);
//   }while($rs_datoventa=$qry_datoventa->fetch_array());
// }
fwrite($file, $facmv);
fclose($file);

 ?>
 <div id="container_background" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="height:500px;">
 </div>
 <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	 &nbsp;
 </div>
 <div id="container_cambio" class="col-xs-6 col-sm-6 col-md-6 col-lg-6 bg-primary" style="margin-top:80px;">
 </div>
 <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 al_center">
   <a href='<?php echo "GENERADO/".$documento.".txt" ?>' download='<?php echo $documento.".txt" ?>'><button type="button" class="btn btn-default" name="button" id="btn_facti">Titulo</button></a>
   <a href='<?php echo "GENERADO/FACMV".$fac_number.".txt" ?>' download='<?php echo "FACMV".$fac_number.".txt" ?>'><button type="button" class="btn btn-default" name="button" id="btn_facmv">Articulos</button></a>
   <button type="button" class="btn btn-warning btn-lg" onclick="document.location.href='paydesk.php'" name="button">Volver</button>
 </div>
</form>
</div>

<div id="footer">
  <?php require 'attached/php/req_footer.php'; ?>
</div>
</div>
<?php  unset($_SESSION["str_generate"]);  ?>
</body>
</html>
