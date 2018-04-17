<?php
require '../../bh_conexion.php';
$link = conexion();

$user_id=$_GET['a'];
$date_i=$_GET['b'];
$date_f=$_GET['c'];

	$pre_datei=strtotime($date_i);
	$date_i = date('Y-m-d',$pre_datei);
	$pre_datef=strtotime($date_f);
	$date_f = date('Y-m-d',$pre_datef);

$txt_facturaf="SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_fecha, bh_cliente.TX_cliente_nombre, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_deficit
FROM ((bh_facturaf
	INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_facturaf.facturaf_AI_cliente_id)
	INNER JOIN bh_facturaventa ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
WHERE
bh_facturaf.TX_facturaf_fecha >= '$date_i' AND
bh_facturaf.TX_facturaf_fecha <= '$date_f' AND
bh_facturaventa.facturaventa_AI_user_id = '$user_id' ORDER BY TX_facturaventa_numero DESC";

$prep_facturaf = $link->prepare($txt_facturaf)or die($link->error);
$prep_facturaf->execute(); $qry_facturaf= $prep_facturaf->get_result();

$qry_facturaventa=$link->prepare("SELECT bh_facturaventa.AI_facturaventa_id, bh_facturaventa.TX_facturaventa_numero,
	bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.TX_facturaventa_total
	FROM bh_facturaventa WHERE facturaventa_AI_facturaf_id = ?")or die($link->error);

$qry_datopago=$link->prepare("SELECT TX_datopago_monto, datopago_AI_metododepago_id, bh_metododepago.TX_metododepago_value
FROM ((bh_datopago
INNER JOIN bh_facturaf ON bh_datopago.datopago_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
INNER JOIN bh_metododepago ON bh_datopago.datopago_AI_metododepago_id = bh_metododepago.AI_metododepago_id)
WHERE bh_facturaf.AI_facturaf_id = ?");

$qry_facturaf_credit=$link->prepare("SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_fecha, bh_cliente.TX_cliente_nombre,
	bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_deficit
FROM (bh_facturaf
	INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_facturaf.facturaf_AI_cliente_id)
WHERE bh_facturaf.AI_facturaf_id = ? AND
bh_facturaf.TX_facturaf_deficit = 0");

$qry_notadebito = $link->prepare("SELECT bh_notadebito.AI_notadebito_id, rel_facturaf_notadebito.TX_rel_facturafnotadebito_importe, bh_notadebito.TX_notadebito_fecha
	FROM ((bh_notadebito
	INNER JOIN rel_facturaf_notadebito ON bh_notadebito.AI_notadebito_id = rel_facturaf_notadebito.rel_AI_notadebito_id)
	INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = rel_facturaf_notadebito.rel_AI_facturaf_id)
	WHERE bh_facturaf.AI_facturaf_id = ? ORDER BY TX_notadebito_fecha DESC, AI_notadebito_id DESC")or die($link->error);

$qry_notadecredito = $link->prepare("SELECT bh_notadecredito.AI_notadecredito_id, bh_notadecredito.TX_notadecredito_fecha, bh_notadecredito.TX_notadecredito_numero, bh_notadecredito.TX_notadecredito_monto FROM bh_notadecredito WHERE notadecredito_AI_facturaf_id =	?")or die($link->error);


?>
<div id="container_span" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div id="container_spancantidad" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        <label for="span_cantidad">Cantidad de Pagos</label>
        <span id="span_cantidad" class="form-control bg-disabled"><?php echo $nr_facturaventa=$qry_facturaventa->num_rows; ?></span>
    </div>
	<div id="container_spantotal" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        <label for="span_total">Monto Total</label>
        <span id="span_total" class="form-control bg-disabled"><?php // echo number_format($total_no_credit,2); ?></span>
    </div>
</div>
<table id="tbl_facturaventa" class="table table-bordered table-striped">
	<thead class="bg-primary">
    	<tr>
      	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
        <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Cliente</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Nº de Cotización</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Factura F. Asociada</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-2">Metodo de P.</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Monto de P.</th>
				<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3 bg_red">Nota de Credito</th>
      </tr>
    </thead>
    <tbody>
    <?php
		$total_total=0; $sumatoria_nc_credito=0; $sumatoria_nc=0;
		$total_efectivo=0; $total_tarjeta_credito=0; $total_tarjeta_debito=0; $total_cheque=0; $total_credito=0; $total_notadc=0;
		if($qry_facturaf->num_rows > 0){
			$raw_facturaf_credito=array(); $ite=0;
			$raw_facturaf_readed=array();
	while($rs_facturaf=$qry_facturaf->fetch_array(MYSQLI_ASSOC)){
		$ite++;
	?>
    <tr>
        <td><?php echo $date=date('d-m-Y',strtotime($rs_facturaf['TX_facturaf_fecha'])); ?></td>
        <td><?php echo $rs_facturaf['TX_cliente_nombre']; ?></td>
				<?php $qry_facturaventa->bind_param("i", $rs_facturaf['AI_facturaf_id']); $qry_facturaventa->execute(); $result = $qry_facturaventa->get_result(); ?>
        <td><?php while ($rs_facturaventa=$result->fetch_array(MYSQLI_ASSOC)) {
        	echo $rs_facturaventa['TX_facturaventa_numero']."<br />";
        } ?></td>
        <td><?php echo $rs_facturaf['TX_facturaf_numero']; ?></td>
				<td><?php
$qry_datopago->bind_param("i", $rs_facturaf['AI_facturaf_id']); $qry_datopago->execute(); $result=$qry_datopago->get_result();
$raw_monto=array(); $i=0;
if (!in_array($rs_facturaf['AI_facturaf_id'],$raw_facturaf_readed)) {
	while($rs_datopago=$result->fetch_array(MYSQLI_ASSOC)){
		switch($rs_datopago['datopago_AI_metododepago_id']){
			case '1':	$color='#67b847';	$total_efectivo += $rs_datopago['TX_datopago_monto'];	break;
			case '2':	$color='#57afdb';	$total_cheque += $rs_datopago['TX_datopago_monto'];	break;
			case '3':	$color='#e9ca2f';	$total_tarjeta_credito += $rs_datopago['TX_datopago_monto']; break;
			case '4':	$color='#f04006';	$total_tarjeta_debito += $rs_datopago['TX_datopago_monto'];	break;
			case '5':	$color='#b54a4a';	$total_credito += $rs_datopago['TX_datopago_monto'];	$raw_facturaf_credito[$ite]=$rs_facturaf['AI_facturaf_id']; break;
			case '7':	$color='#EFA63F';	$total_notadc += $rs_datopago['TX_datopago_monto'];	break;
		}
		echo "<font color='{$color}'>".$rs_datopago['TX_metododepago_value']."</font><br />";
		$raw_monto[$i]=$rs_datopago['TX_datopago_monto'];
		$i++;
	}
}
$raw_facturaf_readed[]=$rs_facturaf['AI_facturaf_id'];
?>
				</td>
				<td>
					<?php foreach ($raw_monto as $key => $value): ?>
					<?php echo number_format($value,2)."<br />"; ?>
					<?php endforeach; ?>
				</td>
				<td class="no_padding bg-warning">
					<table id="tbl_total_nc" class="table-condensed table-bordered tbl-padding-0" style="width:100%">
<?php
				 		$qry_notadecredito->bind_param("i", $rs_facturaf['AI_facturaf_id']); $qry_notadecredito->execute(); $result=$qry_notadecredito->get_result();
						while ($rs_notadecredito=$result->fetch_array()) {
?>
						<tr>
							<td style="width:30%"><?php echo $rs_notadecredito['TX_notadecredito_fecha']; ?></td>
							<td style="width:30%"><?php echo $rs_notadecredito['TX_notadecredito_numero']; ?></td>
							<td style="width:30%"><?php echo $rs_notadecredito['TX_notadecredito_monto'];
							if (in_array($rs_facturaf['AI_facturaf_id'],$raw_facturaf_credito)) {
								$sumatoria_nc_credito+=$rs_notadecredito['TX_notadecredito_monto'];
							} else {
								$sumatoria_nc+=$rs_notadecredito['TX_notadecredito_monto'];
							}
							?></td>
						</tr>

<?php
						}
?>
					</table>
				</td>
    	</tr>
    <?php
		};

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
	$total_total = 0 + $total_cheque+$total_credito+$total_efectivo+$total_notadc+$total_tarjeta_credito+$total_tarjeta_debito;
	 ?>
    </tbody>
    <tfoot class="bg-primary">
    	<tr>
        <td colspan="6">
          <table id="tbl_total" class="table-condensed table-bordered" style="width:100%">
						<tr>
            	<td style="width:15%">
								<strong>Efectivo:</strong> <br /><?php
								if(isset($total_efectivo)){
									echo number_format($total_efectivo,2);
								};?>
              </td>
							<td style="width:15%">
								<strong>Cheque:</strong> <br /><?php
								if(isset($total_cheque)){
									echo number_format($total_cheque,2);
								}?>
              </td>
							<td style="width:14%">
								<strong>TDC:</strong> <br /><?php
								if(isset($total_tarjeta_credito)){
									echo number_format($total_tarjeta_credito,2);
								}
								?>
              </td>
							<td style="width:14%">
								<strong>TDD:</strong> <br /><?php
								if(isset($total_tarjeta_debito)){
									echo number_format($total_tarjeta_debito,2);
								}
								?>
              </td>
            	<td style="width:15%">
								<strong>Cr&eacute;dito:</strong> <br /><?php
								if(isset($total_credito)){
									echo number_format($total_credito,2);
								}?>
              </td>
            	<td style="width:15%">
								<strong>Nota de C.:</strong> <br /><?php
								if(isset($total_notadc)){
									echo number_format($total_notadc,2);
								}?>
              </td>
            	<td style="width:15%">
								<strong>Total:</strong> <br /><?php
								if(isset($total_total)){
									echo number_format($total_total,2);
								}?>
              </td>
            </tr>
					</table>
        </td>
				<td class="bg_red">
					<table id="tbl_total_nc" class="table-condensed table-bordered" style="width:100%">
						<tr>
							<td style="width:50%">
								<strong>Total NC:</strong> <br /><?php
								if(isset($sumatoria_nc)){
									echo number_format($sumatoria_nc,2);
								}?>
              </td>
							<td style="width:50%">
								<strong>Total NC a Credito:</strong> <br /><?php
								if(isset($sumatoria_nc)){
									echo number_format($sumatoria_nc_credito,2);
								}?>
              </td>
            </tr>
					</table>
				</td>
			</tr>
    </tfoot>
</table>
<table id="tbl_ff_nodeficit" class="table table-condensed table-bordered">
	<caption class="caption">Facturas a Credito Canceladas</caption>
	<thead class="bg-danger">
		<tr>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center">Fecha</th>
			<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3 al_center">Cliente</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center">Nº de Cotización</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center">Factura F. Asociada</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center">Fecha de Pago</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1 al_center">Monto de Pago.</th>
		</tr>
	</thead>
	<tbody>
<?php
		foreach ($raw_facturaf_credito as $key => $facturaf_id) {
			$qry_facturaf_credit->bind_param("i", $facturaf_id); $qry_facturaf_credit->execute(); $result = $qry_facturaf_credit->get_result();
			if ($result->num_rows > 0) {
				$rs_facturaf_credit=$result->fetch_array();
	?>
				<tr>
					<td><?php echo date('d-m-Y', strtotime($rs_facturaf_credit['TX_facturaf_fecha'])); ?></td>
					<td><?php echo $rs_facturaf_credit['TX_cliente_nombre']; ?></td>
					<?php $qry_facturaventa->bind_param("i", $rs_facturaf_credit['AI_facturaf_id']); $qry_facturaventa->execute(); $result = $qry_facturaventa->get_result(); ?>
	        <td><?php while ($rs_facturaventa=$result->fetch_array(MYSQLI_ASSOC)) {
	        	echo $rs_facturaventa['TX_facturaventa_numero']."<br />";
	        } ?></td>
					<td><?php echo $rs_facturaf_credit['TX_facturaf_numero']; ?></td>
					<td>
<?php 		$qry_notadebito->bind_param("i", $rs_facturaf_credit['AI_facturaf_id']); $qry_notadebito->execute(); $result=$qry_notadebito->get_result();
					$raw_datodebito=Array();
					while($rs_notadebito=$result->fetch_array(MYSQLI_ASSOC)){
?>
					<?php echo date('d-m-Y', strtotime($rs_notadebito['TX_notadebito_fecha']))."<br />"; ?>
<?php
						$raw_datodebito[]=$rs_notadebito['TX_rel_facturafnotadebito_importe'];
					};
 ?>				</td>
 					<td><?php foreach ($raw_datodebito as $key => $value) {
 						echo number_format($value,2)."<br />";
 					} ?></td>
				</tr>
	<?php
			}
		}
?>
	</tbody>
	<tfoot class="bg-danger">
		<tr>
			<td colspan="6"></td>
		</tr>
	</tfoot>
</table>
