<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_admin.php';

$_SESSION['facturaf_id'] = $_GET['a'];

$facturaf_id = $_GET['a'];

$txt_facturaf="SELECT
bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_ticket, bh_facturaf.TX_facturaf_subtotalni, bh_facturaf.TX_facturaf_subtotalci, bh_facturaf.TX_facturaf_impuesto, bh_facturaf.TX_facturaf_descuento, bh_facturaf.TX_facturaf_status, bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_cambio,
bh_cliente.TX_cliente_nombre,
bh_datopago.TX_datopago_monto, bh_datopago.TX_datopago_numero, bh_datopago.TX_datopago_fecha,
bh_metododepago.TX_metododepago_value,
bh_user.TX_user_seudonimo
FROM ((((bh_facturaf
INNER JOIN bh_cliente ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id)
INNER JOIN bh_datopago ON bh_facturaf.AI_facturaf_id = bh_datopago.datopago_AI_facturaf_id)
INNER JOIN bh_metododepago ON bh_datopago.datopago_AI_metododepago_id = bh_metododepago.AI_metododepago_id)
INNER JOIN bh_user ON bh_datopago.datopago_AI_user_id = bh_user.AI_user_id)
WHERE bh_facturaf.AI_facturaf_id = '$facturaf_id'";
$qry_facturaf=$link->query($txt_facturaf);
$rs_facturaf=$qry_facturaf->fetch_array();

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
	return($ip);
}

$host_ip=ObtenerIP();
$host_name=gethostbyaddr($host_ip);
$qry_impresora = $link->query("SELECT AI_impresora_id, TX_impresora_retorno, TX_impresora_recipiente FROM bh_impresora WHERE TX_impresora_cliente = '$host_name'")or die($link->error);
$nr_impresora = $qry_impresora->num_rows;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Trilli, S.A. - Todo en Materiales</title>

    <link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="attached/css/bootstrap-theme.css" rel="stylesheet" type="text/css" />
    <link href="attached/css/gi_layout.css" rel="stylesheet" type="text/css" />
    <link href="attached/css/gi_general.css" rel="stylesheet" type="text/css" />
    <link href="attached/css/popup_css.css" rel="stylesheet" type="text/css" />
    <link href="attached/css/font-awesome.css" rel="stylesheet" type="text/css" />

    <script type="text/javascript" src="attached/js/jquery.js"></script>
    <script type="text/javascript" src="attached/js/bootstrap.js"></script>
    <script type="text/javascript" src="attached/js/general_funct.js"></script>
    <script type="text/javascript" src="attached/js/ajax_funct.js"></script>
    <script type="text/javascript">

      $(document).ready(function() {
        window.resizeTo(800, 496)
        
        $("#btn_generate").on("click", function(){
          window.opener.location.href = "generate_f_fiscal.php"
        })
        
        $("#btn_process").click(function(){

        });

      });


    </script>

  </head>

  <body>

    <div id="main" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div id="header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div id="logo_container" class="col-xs-12 col-sm-12 col-md-6 col-lg-2" >
          <div id="logo" ></div>
        </div>
      </div>

      <div id="content-sidebar_popup" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <div id="container_spannumeroff" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            <label class="label label_blue_sky"  for="span_numeroff">Nº</label>
            <span id="span_numeroff" class="form-control bg-disabled"><?php echo $rs_facturaf['TX_facturaf_numero']; ?></span>
          </div>
          <div id="container_spanclientname" class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
            <label class="label label_blue_sky"  for="span_clientname">Cliente</label>
            <span id="span_clientname" class="form-control bg-disabled"><?php echo $rs_facturaf['TX_cliente_nombre']; ?></span>
          </div>
          <div id="container_spandate" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            <label class="label label_blue_sky"  for="span_date">Fecha</label>
            <span id="span_date" class="form-control bg-disabled"><?php
              $predate = strtotime($rs_facturaf['TX_facturaf_fecha']);
              echo $date = date('d-m-Y',$predate);
            ?></span>
          </div>
          <div id="container_cuenta" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div id="container_spanstatus" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
              <label class="label label_blue_sky"  for="span_status">Ticket</label>
              <span id="span_status" class="form-control bg-disabled"><?php echo $rs_facturaf['TX_facturaf_ticket']; ?></span>
            </div>
            <div id="container_spantotal" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
              <label class="label label_blue_sky"  for="span_total">Total</label>
              <span id="span_total" class="form-control bg-disabled"><?php
                echo number_format($facturaf_total = $rs_facturaf['TX_facturaf_subtotalni']+$rs_facturaf['TX_facturaf_subtotalci']+$rs_facturaf['TX_facturaf_impuesto'],2);
              ?></span>
            </div>
            <div id="container_spancambio" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
              <label class="label label_blue_sky"  for="span_cambio">Cambio</label>
              <span id="span_cambio" class="form-control bg-disabled"><?php echo number_format($cambio=$rs_facturaf['TX_facturaf_cambio'],2); ?></span>
            </div>
            <div id="container_spandeficit" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
              <label class="label label_blue_sky"  for="span_deficit">Deficit</label>
              <span id="span_deficit" class="form-control bg-disabled"><?php echo number_format($deficit=$rs_facturaf['TX_facturaf_deficit'],2); ?></span>
            </div>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 div_centered al_center pt_7"> 
            <?php if($nr_impresora > 0){ ?>
              <button type="button" id="btn_process" class="btn btn-lg btn-info" >Imprimir Factura Fiscal</button>
            <?php } ?>
            <button type="button" id="btn_generate" class="btn btn-lg btn-info" >Descargar Factura Fiscal</button>
          </div>
        </div> 
      </div>

      <div id="footer">
        <div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >&copy; Derechos Reservados a: Trilli, S.A. 2017</div>
      </div>
    </div>

  </body>
</html>
