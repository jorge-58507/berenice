<?php
require '../../bh_con.php';
$link = conexion();

$user_id=$_GET['a'];
$date_i=$_GET['b'];
$date_f=$_GET['c'];

if(!empty($_GET['d'])){
	$line_order=" ORDER BY ".$_GET['d']." DESC";
//	"datopago_AI_metododepago_id";
}
	$pre_datei=strtotime($date_i);
	$date_i = date('Y-m-d',$pre_datei);
	$pre_datef=strtotime($date_f);
	$date_f = date('Y-m-d',$pre_datef);

$txt_facturaventa="SELECT bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.AI_facturaventa_id, bh_cliente.TX_cliente_nombre,
bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status,
bh_facturaf.TX_facturaf_numero,
bh_datopago.TX_datopago_monto, bh_datopago.datopago_AI_metododepago_id, bh_metododepago.TX_metododepago_value
FROM ((((bh_facturaf
INNER JOIN bh_facturaventa ON bh_facturaventa.facturaventa_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
INNER JOIN bh_datopago ON bh_datopago.datopago_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
INNER JOIN bh_metododepago ON bh_datopago.datopago_AI_metododepago_id = bh_metododepago.AI_metododepago_id)
WHERE
bh_facturaventa.TX_facturaventa_fecha >= '$date_i' AND bh_facturaventa.TX_facturaventa_fecha <= '$date_f' AND
bh_facturaventa.TX_facturaventa_status = 'CANCELADA' AND
bh_facturaventa.facturaventa_AI_user_id = '$user_id'
GROUP BY bh_datopago.AI_datopago_id".$line_order;
$qry_facturaventa_total = mysql_query($txt_facturaventa);
$total_no_credit=0;

while($rs_facturaventa_total = mysql_fetch_assoc($qry_facturaventa_total)){
	if($rs_facturaventa_total['datopago_AI_metododepago_id'] != '4' && $rs_facturaventa_total['datopago_AI_metododepago_id'] != '5'){
		$total_no_credit += $rs_facturaventa_total['TX_datopago_monto'];
	}
}
$qry_facturaventa = mysql_query($txt_facturaventa);
$rs_facturaventa = mysql_fetch_assoc($qry_facturaventa);
?>
<div id="container_span" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_spancantidad" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        <label for="span_cantidad">Cantidad de Pagos</label>
        <span id="span_cantidad" class="form-control bg-disabled"><?php echo $nr_facturaventa=mysql_num_rows($qry_facturaventa); ?></span>
    </div>
	<div id="container_spantotal" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        <label for="span_total">Monto Total</label>
        <span id="span_total" class="form-control bg-disabled"><?php echo number_format($total_no_credit,2); ?></span>
    </div>
</div>
<table id="tbl_facturaventa" class="table table-bordered table-striped">
	<thead class="bg-primary">
    	<tr>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
            <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Cliente</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Nº de Cotización</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Factura F. Asociada</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Metodo de P.</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Monto de P.</th>
        </tr>
    </thead>
    <tbody>
    <?php
		$total_total=0;
		$total_efectivo=0; $total_tarjeta=0; $total_cheque=0; $total_credito=0; $total_notadc=0;
		if($nr_facturaventa=mysql_num_rows($qry_facturaventa)>0){ 
	do{
	?>
    <tr>
        <td><?php
		$time=strtotime($rs_facturaventa['TX_facturaventa_fecha']);
		$date=date('d-m-Y',$time);
		echo $date;
		?></td>
        <td><?php echo $rs_facturaventa['TX_cliente_nombre']; ?></td>
        <td><?php echo $rs_facturaventa['TX_facturaventa_numero']; ?></td>
        <td style="color:<?php echo $color_facturaf[$color_index]; ?>">
		<?php echo $rs_facturaventa['TX_facturaf_numero']; ?>
        </td>
        <?php switch($rs_facturaventa['datopago_AI_metododepago_id']){
			case '1':	$color='#67b847';	$total_efectivo += $rs_facturaventa['TX_datopago_monto'];	break;
			case '2':	$color='#e9ca2f';	$total_tarjeta += $rs_facturaventa['TX_datopago_monto'];	break;
			case '3':	$color='#57afdb';	$total_cheque += $rs_facturaventa['TX_datopago_monto'];	break;
			case '4':	$color='#b54a4a';	$total_credito += $rs_facturaventa['TX_datopago_monto'];	break;
			case '5':	$color='#EFA63F';	$total_notadc += $rs_facturaventa['TX_datopago_monto'];	break;
		}?>
        <td style="color:<?php echo $color;?>; text-shadow: 1px 1px 0 <?php echo $color;?>;"><?php
		 echo $rs_facturaventa['TX_metododepago_value'];
		 ?></td>
        <td><?php echo number_format($rs_facturaventa['TX_datopago_monto'],2); ?></td>
    </tr>
    <?php
	$facturaf_number[]=$rs_facturaventa['TX_facturaf_numero'];
	}while($rs_facturaventa=mysql_fetch_assoc($qry_facturaventa));
  }else{ ?>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <?php
		}
	$total_total = 0 + $total_cheque+$total_credito+$total_efectivo+$total_notadc+$total_tarjeta;
	 ?>
    </tbody>
    <tfoot class="bg-primary">
    	<tr>
        	<td colspan="6">
            <table id="tbl_total" class="table-condensed table-bordered" style="width:100%">
			<tr>
            	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>Efectivo:</strong> <br /><?php
				if(isset($total_efectivo)){
					echo number_format($total_efectivo,2);
				};?>
                </td>
            	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>Tarjeta:</strong> <br /><?php
				if(isset($total_efectivo)){
					echo number_format($total_tarjeta,2);
				}
				?>
                </td>
            	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>Cheque:</strong> <br /><?php
				if(isset($total_efectivo)){
					echo number_format($total_cheque,2);
				}?>
                </td>
            	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>Cr&eacute;dito:</strong> <br /><?php
				if(isset($total_efectivo)){
					echo number_format($total_credito,2);
				}?>
                </td>
            	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>Nota de C.:</strong> <br /><?php
				if(isset($total_efectivo)){
					echo number_format($total_notadc,2);
				}?>
                </td>
            	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>Total:</strong> <br /><?php
				if(isset($total_efectivo)){
					echo number_format($total_total,2);
				}?>
                </td>
            </tr>
			</table>
            </td>
		</tr>
    </tfoot>
</table>
