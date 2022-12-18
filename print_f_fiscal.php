<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_paydesk.php';
$facturaf_id=$_SESSION["facturaf_id"];

function upd_return(){
  $link=conexion();
  $ip   = ObtenerIP();
  $cliente = gethostbyaddr($ip);
  $facturaf_id=$_SESSION["facturaf_id"];
  $facturaf_id -= 1;

  $qry_facturaf = $link->query("SELECT TX_facturaf_numero FROM bh_facturaf WHERE AI_facturaf_id = '$facturaf_id'")or die($link->error);
  $rs_facturaf = $qry_facturaf->fetch_array();
  $ff_numero = $rs_facturaf['TX_facturaf_numero'];

  $qry_impresora=$link->query("SELECT AI_impresora_id, TX_impresora_recipiente, TX_impresora_retorno, TX_impresora_cliente, TX_impresora_serial FROM bh_impresora WHERE TX_impresora_cliente = '$cliente'");
  $row_impresora=$qry_impresora->fetch_array();
  $retorno = $row_impresora['TX_impresora_retorno'];


  if(file_exists($retorno."FACTI".substr("0000000".$ff_numero,-7).".TXT")) {
    $file = fopen($retorno."FACTI".substr("0000000".$ff_numero,-7).".TXT", "r");
    $content = fgets($file);
    fclose($file);
    $raw_content = explode("\t",$content);
    $ticket = $raw_content[7];
    $link->query("UPDATE bh_facturaf SET TX_facturaf_serial = '$raw_content[6]', TX_facturaf_ticket = '$ticket' WHERE AI_facturaf_id = '$facturaf_id'")or die($link->error);
  }else{
    echo "¡No hay archivo de Retorno!";
  }
}
upd_return();

function ObtenerIP(){
  if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"),"unknown"))
  $ip = getenv("HTTP_CLIENT_IP");
  else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
  $ip = getenv("HTTP_X_FORWARDED_FOR");
  else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
  $ip = getenv("REMOTE_ADDR");
  else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
  $ip = $_SERVER['REMOTE_ADDR'];
  else
  $ip = "IP desconocida";

  if (strstr($ip, ', ')) {
    $ips = explode(', ', $ip);
    $ip = $ips[0];
  }
  return($ip);
}
$ip   = ObtenerIP();
$cliente = gethostbyaddr($ip);

$qry_impresora=$link->query("SELECT AI_impresora_id, TX_impresora_recipiente, TX_impresora_retorno, TX_impresora_cliente, TX_impresora_serial, TX_impresora_cajaregistradora FROM bh_impresora WHERE TX_impresora_cliente = '$cliente'")or die($link->error);
$rs_impresora=$qry_impresora->fetch_array();
$impresora_id = $rs_impresora['AI_impresora_id'];
//  #### APERTURA DEL CAJON
// if (!empty($rs_impresora['TX_impresora_cajaregistradora'])){
//   $dir_cajaregistradora = $rs_impresora['TX_impresora_cajaregistradora'];
//   $handle = printer_open($dir_cajaregistradora);
//   if ($handle) {
//     printer_start_doc($handle, "");
//     printer_start_page($handle);
//     printer_set_option($handle, PRINTER_MODE, 'raw');
//     printer_draw_text($handle, "Open Sesame", 400, 400);
//     printer_end_page($handle);
//     printer_end_doc($handle);
//     printer_close($handle);
//   }
// }
//  #### APERTURA DEL CAJON

$txt_facturaf="SELECT bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_direccion, bh_cliente.TX_cliente_dv, bh_cliente.TX_cliente_correo, bh_cliente.TX_cliente_contribuyente, bh_cliente.TX_cliente_tipo,
bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_descuento, bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_cambio, bh_facturaf.TX_facturaf_nota
FROM (bh_facturaf
INNER JOIN bh_cliente ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id)
WHERE bh_facturaf.AI_facturaf_id = '$facturaf_id'";
$qry_facturaf=$link->query($txt_facturaf)or die($link->error);
$rs_facturaf=$qry_facturaf->fetch_array();
$minuendo=$rs_facturaf['TX_facturaf_deficit'];
if (!empty($rs_facturaf['TX_facturaf_cambio'])) { $cambio=round($rs_facturaf['TX_facturaf_cambio'],2); }else{  $cambio = 0 ;}

$txt_datopago="SELECT bh_datopago.TX_datopago_monto, bh_datopago.TX_datopago_numero, bh_datopago.datopago_AI_metododepago_id
 FROM bh_datopago
WHERE bh_datopago.datopago_AI_facturaf_id = '$facturaf_id'";
$qry_datopago=$link->query($txt_datopago)or die($link->error);
$raw_datopago=array();
$total_pagado=0;
while($rs_datopago=$qry_datopago->fetch_array()){
	$total_pagado += $rs_datopago['TX_datopago_monto'];
	$raw_datopago[$rs_datopago['datopago_AI_metododepago_id']]['monto'] = $rs_datopago['TX_datopago_monto'];
	$raw_datopago[$rs_datopago['datopago_AI_metododepago_id']]['numero'] = $rs_datopago['TX_datopago_numero'];
};
if(array_key_exists(5,$raw_datopago) || array_key_exists(8,$raw_datopago)) {
  $p_sumando = (!empty($raw_datopago[5])) ? $raw_datopago[5]['monto'] : 0;
  $s_sumando = (!empty($raw_datopago[8])) ? $raw_datopago[8]['monto'] : 0;
  $deficit = $p_sumando+$s_sumando;
	$link->query("UPDATE bh_facturaf SET TX_facturaf_deficit = '$deficit', TX_facturaf_status = 'IMPRESA' WHERE AI_facturaf_id = '$facturaf_id'");
}else{
	$link->query("UPDATE bh_facturaf SET TX_facturaf_deficit = '0', TX_facturaf_status = 'IMPRESA' WHERE AI_facturaf_id = '$facturaf_id'");
}

$qry_vendedor = $link->query("SELECT bh_user.TX_user_seudonimo FROM (bh_facturaventa INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaventa.facturaventa_AI_user_id) WHERE facturaventa_AI_facturaf_id = '$facturaf_id' LIMIT 1")or die($link->error);
$rs_vendedor = $qry_vendedor->fetch_array();
$vendedor = $rs_vendedor['TX_user_seudonimo'];

// ############################                PRODUCIR LOS .TXT
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
  "p_otro" => '0.00',
  "dv" => "",
  "c_email" => "",
  "c_contribuyente" => "",
  "c_tipo" => "",
  "nota" => ""
];

$raw_facti['documento']="FACTI".substr($rs_facturaf['TX_facturaf_numero'],-7);
$raw_facti['c_nombre']=$r_function->replace_special_character_no_html($rs_facturaf['TX_cliente_nombre']);
$raw_facti['c_ruc']=$rs_facturaf['TX_cliente_cif'];
$raw_facti['c_direccion']=substr($vendedor,0,3)."-".$rs_facturaf['TX_cliente_direccion'];
$raw_facti['total_descuento']=(!empty($rs_facturaf['TX_facturaf_descuento'])) ? round($rs_facturaf['TX_facturaf_descuento'],2) : '0.00'; 
$raw_facti['total_pagado']=round($total_pagado,2);
$raw_facti['total_final']=round($rs_facturaf['TX_facturaf_total'],2);

$raw_facti['p_efectivo']  = (!empty($raw_datopago[1]['monto'])) ? round($raw_datopago[1]['monto']+$cambio,2) : '0.00';
$raw_facti['p_cheque']    = (!empty($raw_datopago[2]['monto'])) ? round($raw_datopago[2]['monto'],2) : '0.00';
$raw_facti['p_tdc']       = (!empty($raw_datopago[3]['monto'])) ? round($raw_datopago[3]['monto'],2) : '0.00';
$raw_facti['p_tdd']       = (!empty($raw_datopago[4]['monto'])) ? round($raw_datopago[4]['monto'],2) : '0.00';
$raw_facti['p_nc']        = (!empty($raw_datopago[7]['monto'])) ? round($raw_datopago[7]['monto'],2) : '0.00';
$raw_facti['p_otro']     += (!empty($raw_datopago[5]['monto'])) ? round($raw_datopago[5]['monto'],2) : $raw_facti['p_otro'];
$raw_facti['p_otro']     += (!empty($raw_datopago[8]['monto'])) ? round($raw_datopago[8]['monto'],2) : $raw_facti['p_otro'];
$raw_facti['dv']=$rs_facturaf['TX_cliente_dv'];
$raw_facti['c_email']=$rs_facturaf['TX_cliente_correo'];
$raw_facti['c_contribuyente']=$rs_facturaf['TX_cliente_contribuyente'];
$raw_facti['c_tipo']=$rs_facturaf['TX_cliente_tipo'];
$raw_facti['nota']=$rs_facturaf['TX_facturaf_nota'];

$recipiente = $rs_impresora['TX_impresora_recipiente'];
$retorno = $rs_impresora['TX_impresora_retorno'];
if (!file_exists($recipiente)) {
  echo "No es posible accesar al recipiente.";
  return false;
}

/* ###################### ENCABEZADO  ######################## */
$total_pagado=$total_pagado+$cambio;
$file = fopen($recipiente."FACTI".substr($rs_facturaf['TX_facturaf_numero'],-7).".txt", "w");
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
$txt_datoventa="SELECT bh_producto.AI_producto_id, bh_producto.TX_producto_codigo, bh_producto.TX_producto_value, bh_producto.TX_producto_medida, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_descripcion,
bh_datoventa.TX_datoventa_precio AS precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_medida
FROM (((bh_facturaf
INNER JOIN bh_facturaventa ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
WHERE bh_facturaf.AI_facturaf_id = '$facturaf_id'";
$qry_datoventa=$link->query($txt_datoventa)or die($link->error);
$rs_datoventa=$qry_datoventa->fetch_array();
$qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida")or die($link->error);
$raw_medida = array();
while($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)){
  $raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
}

$flete = "0";
$file = fopen($recipiente."FACMV".substr($rs_facturaf['TX_facturaf_numero'],-7).".txt", "w");
if ($qry_datoventa->num_rows > 3) {
  do{ 
    if ($rs_datoventa['AI_producto_id'] === "14415") {
      $flete = "1";
    }
    $codigo =       (preg_match("/-/", $rs_datoventa['TX_producto_codigo'])) ? substr(str_replace("-","",$rs_datoventa['TX_producto_codigo']),-8) : substr($rs_datoventa['TX_producto_codigo'],-7);
    $descripcion =  (preg_match("/-/", $rs_datoventa['TX_producto_codigo'])) ? substr($r_function->replace_special_character_no_html($rs_datoventa['TX_datoventa_descripcion']),0,25) : substr($r_function->replace_special_character_no_html($rs_datoventa['TX_datoventa_descripcion']),0,25);
    fwrite($file, "FACTI".substr($rs_facturaf['TX_facturaf_numero'],-8).chr(9).$codigo.chr(9).substr($raw_medida[$rs_datoventa['TX_datoventa_medida']],0,3)." ".trim($descripcion).chr(9).$raw_medida[$rs_datoventa['TX_datoventa_medida']].chr(9).$rs_datoventa['TX_datoventa_cantidad'].chr(9).$rs_datoventa['precio'].chr(9).$rs_datoventa['TX_datoventa_impuesto'].chr(9). PHP_EOL);
  }while($rs_datoventa=$qry_datoventa->fetch_array());
}else{
  do{
    if ($rs_datoventa['AI_producto_id'] === "14415") {
      $flete = "1";
    }
    $codigo =       (preg_match("/-/", $rs_datoventa['TX_producto_codigo'])) ? substr(str_replace("-","",$rs_datoventa['TX_producto_codigo']),-8) : substr($rs_datoventa['TX_producto_codigo'],-7);
    $descripcion =  (preg_match("/-/", $rs_datoventa['TX_producto_codigo'])) ? substr($r_function->replace_special_character_no_html($rs_datoventa['TX_datoventa_descripcion']),0,59) : substr($r_function->replace_special_character_no_html($rs_datoventa['TX_datoventa_descripcion']),0,61);
    fwrite($file, "FACTI".substr($rs_facturaf['TX_facturaf_numero'],-8).chr(9).$codigo.chr(9).substr($raw_medida[$rs_datoventa['TX_datoventa_medida']],0,3)." ".trim($descripcion).chr(9).$raw_medida[$rs_datoventa['TX_datoventa_medida']].chr(9).$rs_datoventa['TX_datoventa_cantidad'].chr(9).$rs_datoventa['precio'].chr(9).$rs_datoventa['TX_datoventa_impuesto'].chr(9). PHP_EOL);
  }while($rs_datoventa=$qry_datoventa->fetch_array());
}
if ($flete === "1") {
  $codigo =       "0000001";
  $descripcion =  $rs_facturaf['TX_cliente_direccion'];
  fwrite($file, "FACTI".substr($rs_facturaf['TX_facturaf_numero'],-8).chr(9).$codigo.chr(9).trim("Entregar en: ".$descripcion).chr(9)."UNIDADES".chr(9)."1".chr(9)."0.00".chr(9)."0".chr(9). PHP_EOL);
}
fclose($file);

$facti_exist = (!file_exists($recipiente."\FACTI".substr($rs_facturaf['TX_facturaf_numero'],-7).'.txt')) ? 0 : 1;
$facmv_exist = (!file_exists($recipiente."\FACMV".substr($rs_facturaf['TX_facturaf_numero'],-7).'.txt')) ? 0 : 1;

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
if ($facti_exist === 1 && $facmv_exist === 1) {
  echo "<meta http-equiv='Refresh' content='3;url=paydesk.php?a=$facturaf_id' />";
}
?>
<title>Trilli, S.A. - Todo en Materiales</title>

<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/bootstrap-theme.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_layout.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />
<link href="attached/css/gi_blocks.css" rel="stylesheet" type="text/css" />
<link href="attached/css/sell_css.css" rel="stylesheet" type="text/css" />
<link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>

<script type="text/javascript">

$(document).ready(function() {
  $("#btn_start").click(function(){
    window.location="start.php";
  });
  $("#btn_exit").click(function(){
    location.href="index.php";
  })
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
      <div id="container_background" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="height:500px;"></div>
      <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
        &nbsp;
      </div>
      <div id="container_cambio" class="col-xs-6 col-sm-6 col-md-6 col-lg-6 bg-primary">
        <div id="div_cambio" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <p>Su Cambio: </p>
          <span id="span_cambio">B/ <?php echo number_format($cambio,2); ?></span>
        </div>
        <?php
          if ($facti_exist === 1 && $facmv_exist === 1) {
            echo "Emisi&oacute;n correcta.";
          }else{
            echo "Llame a servicio t&eacute;cnico.";
          }
        ?>
      </div>
    </form>
  </div>
  <div id="footer">
    <div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
      <div id="container_btnadminicon" class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></div>
      <div id="container_txtcopyright" class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
        &copy; Derechos Reservados a: Jorge Salda&nacute;a <?php echo date('Y'); ?>
      </div>
      <div id="container_btnstart" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
        <i id="btn_start" class="fa fa-home" title="Ir al Inicio"></i>
      </div>
      <div id="container_btnexit" class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
        <button type="button" class="btn btn-danger" id="btn_exit">Salir</button></div>
      </div>
    </div>
  </div>
</div>
<?php  
  unset($_SESSION["facturaf_id"]); 
?>
</body>
</html>
