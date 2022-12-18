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

$qry_notadecredito = $link->prepare("SELECT bh_notadecredito.AI_notadecredito_id, bh_notadecredito.TX_notadecredito_fecha, bh_notadecredito.TX_notadecredito_numero, bh_notadecredito.TX_notadecredito_monto, bh_notadecredito.TX_notadecredito_impuesto  FROM bh_notadecredito WHERE notadecredito_AI_facturaf_id =	?")or die($link->error);


		$total_total=0; $sumatoria_nc_credito=0; $sumatoria_nc=0;
		$total_efectivo=0; $total_tarjeta_credito=0; $total_tarjeta_debito=0; $total_cheque=0; $total_credito=0; $total_notadc=0; $total_pcobrar=0;
		if($qry_facturaf->num_rows > 0){
			$raw_facturaf_credito=array(); $ite=0;
			$raw_facturaf_readed=array();
			while($rs_facturaf=$qry_facturaf->fetch_array(MYSQLI_ASSOC)){
				$ite++;
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
							case '8':	$color='#d12498';	$total_pcobrar += $rs_datopago['TX_datopago_monto'];	break;
						}
						$raw_monto[$i]=$rs_datopago['TX_datopago_monto'];
						$i++;
					}
				}
				$raw_facturaf_readed[]=$rs_facturaf['AI_facturaf_id'];
		 		$qry_notadecredito->bind_param("i", $rs_facturaf['AI_facturaf_id']); $qry_notadecredito->execute(); $result=$qry_notadecredito->get_result();
				while ($rs_notadecredito=$result->fetch_array()) {
					if (in_array($rs_facturaf['AI_facturaf_id'],$raw_facturaf_credito)) {
						$sumatoria_nc_credito+=$rs_notadecredito['TX_notadecredito_monto']+$rs_notadecredito['TX_notadecredito_impuesto'];
					} else {
						$sumatoria_nc+=$rs_notadecredito['TX_notadecredito_monto']+$rs_notadecredito['TX_notadecredito_impuesto'];
					}
				}
			}
		}  /*  ###############   AQUI IBA EL ELSE    ################### */
	$total_total = 0 + $total_cheque+$total_credito+$total_efectivo+$total_notadc+$total_tarjeta_credito+$total_tarjeta_debito+$total_pcobrar;
	 ?>

	 <table id="tbl_total" class="table-condensed table-bordered" style="width:100%">
		 <thead>
		 	<tr>
				<th class="bg-primary al_center">EFECTIVO</th>
				<th class="bg-primary al_center">CHEQUE</th>
				<th class="bg-primary al_center">TDC</th>
				<th class="bg-primary al_center">TDD</th>
				<th class="bg-primary al_center">CREDITO</th>
				<th class="bg-primary al_center">NOTA DE C.</th>
				<th class="bg-primary al_center">POR COBRAR</th>
				<th class="bg-primary al_center">TOTAL</th>
				<th class="bg_red al_center">TOTAL NC</th>
				<th class="bg_red al_center">TOTAL NC A CREDITO</th>
		 	</tr>
		 </thead>
		 <tfoot>
		 	<tr>
				<td colspan="8" class="bg-primary"></td>
				<td colspan="2" class="bg_red"></td>
		 	</tr>
		 </tfoot>
		 <tbody>
		 	<tr>
				<td>B/ <?php if(isset($total_efectivo)){ echo number_format($total_efectivo,2); } ?></td>
				<td>B/ <?php if(isset($total_cheque)){ echo number_format($total_cheque,2); } ?></td>
				<td>B/ <?php if(isset($total_tarjeta_credito)){ echo number_format($total_tarjeta_credito,2); } ?></td>
				<td>B/ <?php if(isset($total_tarjeta_debito)){ echo number_format($total_tarjeta_debito,2); } ?></td>
				<td>B/ <?php if(isset($total_credito)){ echo number_format($total_credito,2); } ?></td>
				<td>B/ <?php if(isset($total_notadc)){ echo number_format($total_notadc,2); } ?></td>
				<td>B/ <?php if(isset($total_pcobrar)){ echo number_format($total_pcobrar,2); } ?></td>

				<td>B/ <?php if(isset($total_total)){ echo number_format($total_total,2); } ?></td>
				<td>B/ <?php if(isset($sumatoria_nc)){ echo number_format($sumatoria_nc,2); } ?></td>
				<td>B/ <?php if(isset($sumatoria_nc_credito)){ echo number_format($sumatoria_nc_credito,2); } ?></td>
		 	</tr>
			<tr>
<?php 	$suma_comision = $total_total-$total_credito;
				//$resta_comision = $sumatoria_nc-$sumatoria_nc_credito; ?>
				<td colspan="7"></td>
				<td colspan="3"><div id="total_comision"><?php echo number_format($suma_comision,2).' - '.number_format($sumatoria_nc,2).' = <strong>B/ '.(number_format($suma_comision-$sumatoria_nc,2)).'</strong>'; ?></div></td>
			</tr>
		</tbody>
	</table>
