<?php
require 'bh_con.php';
$link=conexion();

require 'attached/php/req_login_paydesk.php';

$facturaf_id = $_GET['a'];

$txt_facturaf="SELECT
bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_subtotalni, bh_facturaf.TX_facturaf_subtotalci, bh_facturaf.TX_facturaf_impuesto, bh_facturaf.TX_facturaf_descuento, bh_facturaf.TX_facturaf_status, bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_cambio,
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
$qry_facturaf=mysql_query($txt_facturaf);
$rs_facturaf=mysql_fetch_assoc($qry_facturaf);
?>
<?php
$txt_notadebito="SELECT bh_notadebito.TX_notadebito_numero, bh_notadebito.TX_notadebito_total, bh_notadebito.TX_notadebito_impuesto, bh_notadebito.TX_notadebito_fecha, bh_notadebito.TX_notadebito_hora, bh_user.TX_user_seudonimo, SUM(bh_datodebito.TX_datodebito_monto) as TX_datodebito_monto
FROM ((((bh_facturaf
        INNER JOIN rel_facturaf_notadebito ON bh_facturaf.AI_facturaf_id = rel_facturaf_notadebito.rel_AI_facturaf_id)
       INNER JOIN bh_notadebito ON rel_facturaf_notadebito.rel_AI_notadebito_id = bh_notadebito.AI_notadebito_id)
      INNER JOIN bh_datodebito ON bh_datodebito.datodebito_AI_notadebito_id = bh_notadebito.AI_notadebito_id)
      INNER JOIN bh_user ON bh_notadebito.notadebito_AI_user_id = bh_user.AI_user_id)

      WHERE bh_facturaf.AI_facturaf_id = '$facturaf_id'
	  GROUP BY bh_notadebito.TX_notadebito_numero
	  ORDER BY bh_notadebito.TX_notadebito_fecha";
$qry_notadebito=mysql_query($txt_notadebito);
$rs_notadebito=mysql_fetch_assoc($qry_notadebito);
$nr_notadebito=mysql_num_rows($qry_notadebito);
?>
<?php
$txt_datoventa="SELECT bh_producto.TX_producto_value, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento,
bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio as precio,
((bh_datoventa.TX_datoventa_descuento*bh_datoventa.TX_datoventa_precio)/100) as descuento
FROM (((bh_datoventa
INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
WHERE bh_facturaf.AI_facturaf_id = '$facturaf_id'";

$qry_datoventa = mysql_query($txt_datoventa);
$rs_datoventa = mysql_fetch_assoc($qry_datoventa);
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
<link href="attached/css/gi_blocks.css" rel="stylesheet" type="text/css" />
<link href="attached/css/popup_css.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="attached/js/jquery.js"></script>
<script type="text/javascript" src="attached/js/bootstrap.js"></script>
<script type="text/javascript" src="attached/js/general_funct.js"></script>
<script type="text/javascript" src="attached/js/ajax_funct.js"></script>
<script type="text/javascript">

$(document).ready(function() {

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

	<div id="container_spannumeroff" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <label for="span_numeroff">Nº</label>
        <span id="span_numeroff" class="form-control bg-disabled"><?php echo $rs_facturaf['TX_facturaf_numero']; ?></span>
    </div>
	<div id="container_spanclientname" class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
        <label for="span_clientname">Cliente</label>
        <span id="span_clientname" class="form-control bg-disabled"><?php echo $rs_facturaf['TX_cliente_nombre']; ?></span>
    </div>
	<div id="container_spandate" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <label for="span_date">Fecha</label>
        <span id="span_date" class="form-control bg-disabled"><?php
			$predate = strtotime($rs_facturaf['TX_facturaf_fecha']);
			echo $date = date('d-m-Y',$predate);

		 ?></span>
    </div>
<div id="container_cuenta" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_spanstatus" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <label for="span_status">Status</label>
       <span id="span_status" class="form-control bg-disabled"><?php echo $rs_facturaf['TX_facturaf_status']; ?></span>
    </div>
	<div id="container_spantotal" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        <label for="span_total">Total</label>
        <span id="span_total" class="form-control bg-disabled"><?php
		echo number_format($facturaf_total = $rs_facturaf['TX_facturaf_subtotalni']+$rs_facturaf['TX_facturaf_subtotalci']+$rs_facturaf['TX_facturaf_impuesto'],2);
		?></span>
    </div>
    <div id="container_spancambio" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
          <label for="span_cambio">Cambio</label>
          <span id="span_cambio" class="form-control bg-disabled"><?php echo number_format($cambio=$rs_facturaf['TX_facturaf_cambio'],2); ?></span>
    </div>
    <div id="container_spandeficit" class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
          <label for="span_deficit">Deficit</label>
          <span id="span_deficit" class="form-control bg-disabled"><?php echo number_format($deficit=$rs_facturaf['TX_facturaf_deficit'],2); ?></span>
    </div>
</div>
<div id="container_payment" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	<div id="container_tblpayment" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <table id="tbl_payment" class="table table-bordered table-condensed table-striped">
        <caption>Pagos Asociados</caption>
        <thead class="bg-primary">
        <tr>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Fecha</th>
        <th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Metodo de Pago</th>
        <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Nº de Control</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Monto</th>
        </tr>
        </thead>
        <tbody>
        <?php $total_pago=0 ?>
        <?php do{ ?>
        <?php
		$tr_color = '000000';
		if($rs_facturaf['TX_metododepago_value'] ==  'Cr&eacute;dito'){
		$tr_color = 'F00';
		}
		?>
        <tr title="<?php echo $rs_facturaf['TX_user_seudonimo']; ?>" style='color:#<?php echo $tr_color; ?>;'>
        <td><?php echo $rs_facturaf['TX_datopago_fecha']; ?></td>
        <td><?php echo $rs_facturaf['TX_metododepago_value']; ?></td>
        <td><?php echo $rs_facturaf['TX_datopago_numero']; ?></td>
        <td><?php echo number_format($rs_facturaf['TX_datopago_monto'],2); ?></td>
        </tr>
        <?php
		if($rs_facturaf['TX_metododepago_value'] !=  'Cr&eacute;dito'){
		$total_pago += $rs_facturaf['TX_datopago_monto'];
		}
		?>

		<?php }while($rs_facturaf=mysql_fetch_assoc($qry_facturaf)); ?>
        </tbody>
        <tfoot class="bg-primary"><tr><td></td><td></td><td></td>
        <td>$ <?php echo number_format($total_pago,2); ?></td>
        </tr></tfoot>
        </table>


    </div>
	<div id="container_tbldebit" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">


        <table id="tbl_debit" class="table table-bordered table-condensed table-striped">
        <caption>Abonos Asociados</caption>
        <thead class="bg-info">
        <tr>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Fecha</th>
        <th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Hora</th>
        <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Nº de Control</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Total</th>
        </tr>
        </thead>
        <tbody>
        <?php $total_abono=0; ?>
        <?php
		if($nr_notadebito=mysql_num_rows($qry_notadebito) > 0){
		do{ ?>
        <tr title="<?php echo $rs_notadebito['TX_user_seudonimo']; ?>">
        <td><?php echo $rs_notadebito['TX_notadebito_fecha']; ?></td>
        <td><?php echo $rs_notadebito['TX_notadebito_hora']; ?></td>
        <td><?php echo $rs_notadebito['TX_notadebito_numero']; ?></td>
        <td><?php echo number_format($rs_notadebito['TX_datodebito_monto'],2);?></td>
        </tr>
        <?php $total_abono += $rs_notadebito['TX_datodebito_monto']; ?>
        <?php }while($rs_notadebito=mysql_fetch_assoc($qry_notadebito));
		}else{
		?>
        <tr>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        </tr>
        <?php } ?>
        </tbody>
        <tfoot class="bg-info"><tr><td></td><td></td><td></td>
        <td>
        $ <?php echo number_format($total = $total_pago+$total_abono,2); ?>
        </td>
        </tr></tfoot>
        </table>
	</div>
</div>
<div id="container_datoventa" class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	<div id="container_tbldatoventa" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<table id="tbl_datoventa" class="table table-bordered table-condensed table-striped">
        <caption>Productos Relacionados</caption>
        <thead class="bg_green">
        <tr>
        	<th class="col-xs-8 col-sm-8 col-md-8 col-lg-8">Producto</th>
        	<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Cantidad</th>
        	<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Total</th>
        </tr>
        </thead>
        <tbody>
        <?php $total=0; ?>
        <?php do{ ?>
        <?php	$precio_descuento = $rs_datoventa['TX_datoventa_precio']-$rs_datoventa['descuento'];
				$precio_impuesto = ($rs_datoventa['TX_datoventa_impuesto']*$precio_descuento)/100;
				$precio_total = $rs_datoventa['TX_datoventa_cantidad']*($precio_descuento+$precio_impuesto);
		 ?>
        <tr>
        	<td><?php echo $rs_datoventa['TX_producto_value']; ?></td>
        	<td><?php echo $rs_datoventa['TX_datoventa_cantidad']; ?></td>
        	<td><?php echo number_format($precio_total,2); ?></td>
        </tr>
        <?php $total += $precio_total; ?>
        <?php }while($rs_datoventa = mysql_fetch_assoc($qry_datoventa)); ?>
        </tbody>
        <tfoot class="bg_green">
		<tr>
        	<th></th><th></th><th><strong>$ <?php echo number_format($total,2); ?></strong></th>
        </tr>
        </tfoot>
        </table>
    </div>
</div>

</div>

<div id="footer">
	<div id="copyright" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
&copy; Derechos Reservados a: Trilli, S.A. 2017
	</div>
</div>
</div>

</body>
</html>
