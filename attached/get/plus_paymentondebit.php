<?php
require '../../bh_conexion.php';
$link = conexion();
require '../php/req_login_admin.php';
$uid = $user_id;
date_default_timezone_set('America/Panama');


if(intval($_GET['a']) > 0){	$method=intval($_GET['a']);	}else{ return false; }
if(floatval($_GET['b']) > 0 ){ $amount=$_GET['b']; }else{ return false; }
$number=$_GET['c'];

$str_factid=$_GET['d'];
$arr_factid = explode(",",$str_factid);

$fecha_actual = date('Y-m-d');

function insert_payment($method, $amount, $number, $approved, $uid){
	$link=conexion();
	 // echo "insertar";
	if($approved === 1){
		 // echo "aprobadop";
		$fecha_actual = date('Y-m-d');
		$link->query("INSERT INTO bh_nuevodebito (nuevodebito_AI_user_id, nuevodebito_AI_metododepago_id, TX_nuevodebito_monto, TX_nuevodebito_numero, TX_nuevodebito_fecha)
		VALUES ('$uid', '$method', '$amount', '$number', '$fecha_actual')")or die($link->error);
	}else{
		// echo "no aprobado";
	}
}


$txt_clientid="SELECT facturaf_AI_cliente_id FROM bh_facturaf WHERE";
foreach ($arr_factid as $key => $value) {
	if ($value === end($arr_factid)) {
		$txt_clientid=$txt_clientid." AI_facturaf_id = '$value'";
	}else{
		$txt_clientid=$txt_clientid." AI_facturaf_id = '$value' OR";
	}
}
$qry_clientid=$link->query($txt_clientid)or die($link->error);
$row_clientid=$qry_clientid->fetch_array();
$client_id=$row_clientid['facturaf_AI_cliente_id'];

$txt_facturaf="SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_subtotalci, bh_facturaf.TX_facturaf_subtotalni, bh_facturaf.TX_facturaf_impuesto, bh_facturaf.TX_facturaf_descuento,
bh_cliente.TX_cliente_nombre
FROM (bh_facturaf
INNER JOIN bh_cliente ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id)
WHERE";
foreach ($arr_factid as $key => $value) {
	if($value === end($arr_factid)){
		$txt_facturaf = $txt_facturaf." bh_facturaf.facturaf_AI_cliente_id = '$client_id' AND bh_facturaf.AI_facturaf_id = '$value' ORDER BY bh_facturaf.TX_facturaf_deficit ASC";
	}else{
		$txt_facturaf = $txt_facturaf." bh_facturaf.facturaf_AI_cliente_id = '$client_id' AND bh_facturaf.AI_facturaf_id = '$value' OR";
	}
}
// echo $txt_facturaf;
$qry_facturaf=$link->query($txt_facturaf)or die($link->error);
$raw_facturaf=array();
while ($rs_facturaf=$qry_facturaf->fetch_array()) {
	$raw_facturaf[]=$rs_facturaf;
}
$total_ff = 0;
foreach ($raw_facturaf as $key => $value) {
	$total_ff += $value['TX_facturaf_deficit'];
}
$total_ff = round($total_ff,2);
$total_ff = floatval($total_ff);
// echo $total_ff."<br />";

$raw_payment=array();
$total_pagado=0;
$qry_payment = $link->query("SELECT nuevodebito_AI_user_id,nuevodebito_AI_metododepago_id,TX_nuevodebito_monto,TX_nuevodebito_numero FROM bh_nuevodebito WHERE nuevodebito_AI_user_id = '$uid'")or die($link->error);
while ($rs_payment = $qry_payment->fetch_array()) {
	$raw_payment[$rs_payment['nuevodebito_AI_metododepago_id']]['monto'] = $rs_payment['TX_nuevodebito_monto'];
	$raw_payment[$rs_payment['nuevodebito_AI_metododepago_id']]['numero'] = $rs_payment['TX_nuevodebito_numero'];
	$total_pagado+=$rs_payment['TX_nuevodebito_monto'];
}
$total_pagado=round($total_pagado,2);
$total_pagado = floatval($total_pagado);
$approved = 1;
// foreach ($raw_payment as $key => $value) {
	if(array_key_exists($method, $raw_payment)){
		// echo "<br />si existe";
		$approved = 0;
	}else{
		// echo "<br />no existe";
	}
// }

if ($total_pagado < $total_ff) {
	$pagando = $total_pagado+$amount;
	$pagando=floatval($pagando);
	$resta = $pagando-$total_ff;
	$resta = round($resta,2);
	  // echo "Pagando: ".$pagando." A pagar: ".$total_ff."<br />";

	//if($pagando <= $total_ff){
	// echo "resta: ".$resta."<br />";
	if($resta <= 0){
	 // echo "insertar";
		if($method === 7){
			$qry_cliente = $link->query("SELECT TX_cliente_saldo FROM bh_cliente WHERE AI_cliente_id = '$client_id'")or die($link->error);
			$rs_cliente = $qry_cliente->fetch_array();
			if($rs_cliente['TX_cliente_saldo'] >= $amount){
				insert_payment($method, $amount, $number, $approved, $uid);
			}
		}else{
			insert_payment($method, $amount, $number, $approved, $uid);
		}
	}else{
		// echo "verificar si es efectivo o cheque";
		if($method === 1){
			// echo "es 1 o 2";
			insert_payment($method, $amount, $number, $approved, $uid);
		}elseif($method === 2){

			if(array_key_exists(1, $raw_payment) && $resta <= round($raw_payment[1]['monto'],2)){
				insert_payment($method, $amount, $number, $approved, $uid);
			}elseif(!array_key_exists(1, $raw_payment)){
				insert_payment($method, $amount, $number, $approved, $uid);
			}

		}else{
			if (array_key_exists(1, $raw_payment) && $resta <= round($raw_payment[1]['monto'],2)) {
				insert_payment($method, $amount, $number, $approved, $uid);
			}elseif (array_key_exists(2, $raw_payment) && $resta <= round($raw_payment[2]['monto'],2)) {
				insert_payment($method, $amount, $number, $approved, $uid);
			}
		}
	}
}else{ echo "no pasara";}

/* ####################ANSWER################## */
?>

<?php
$txt_pago="SELECT bh_nuevodebito.AI_nuevodebito_id, bh_nuevodebito.TX_nuevodebito_fecha, bh_nuevodebito.TX_nuevodebito_monto, bh_nuevodebito.TX_nuevodebito_numero, bh_metododepago.TX_metododepago_value
FROM (bh_nuevodebito INNER JOIN bh_metododepago ON bh_nuevodebito.nuevodebito_AI_metododepago_id = bh_metododepago.AI_metododepago_id) WHERE nuevodebito_AI_user_id = '$uid'";
$qry_pago=$link->query($txt_pago)or die($link->error);
$rs_pago=$qry_pago->fetch_array();
$ite=0;
?>


<table id="tbl_paymentlist" class="table table-bordered table-condensed table-striped">
	<thead class="bg-primary">
		<tr>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
			<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Fecha</th>
			<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Metodo de Pago</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Nº de Control</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Monto</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
		</tr>
		</thead>
		<tbody id="tbody_paymentlist">
<?php
			$monto_pagado=0;	$var_pmethod="0";
			if($nr_pago=$qry_pago->num_rows > 0){
			do{ ?>
		<tr>
			<td><?php echo $ite=$ite+'1'.".-" ?></td>
			<td><?php	echo $date=date('d-m-Y',strtotime($rs_pago['TX_nuevodebito_fecha']));?></td>
			<td><?php echo $rs_pago['TX_metododepago_value']; ?></td>
			<td><?php echo $rs_pago['TX_nuevodebito_numero']; ?></td>
			<td><?php echo number_format($rs_pago['TX_nuevodebito_monto'],2); ?></td>
			<td>
<?php				if($_COOKIE['coo_tuser'] < 3 || $_COOKIE['coo_tuser'] == '4' ){	?>
					<button type="button" name="<?php echo $rs_pago['AI_nuevodebito_id']; ?>" id="btn_delpago" class="btn btn-danger btn-xs btn-fa" onclick="del_paymentondebit(this.name,'<?php echo $str_factid; ?>')"><i class="fa fa-times" aria-hidden="true"></i></button>
<?php    		}        ?>
			</td>
		</tr>
<?php
		$monto_pagado += $rs_pago['TX_nuevodebito_monto'];
	}while($rs_pago=$qry_pago->fetch_array()); ?>
<?php }else{ ?>
		<tr>
			<td>&nbsp;</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
<?php 	}
		if ($total_ff > $monto_pagado) {
			$cambio = 0;
			$diferencia = $total_ff-$monto_pagado;
		}else{
			$cambio = $monto_pagado-$total_ff;
			$diferencia = 0;
		}
?>
		</tbody>
<tfoot class="bg-primary">
<tr>
	<td colspan="6">
		<div id="container_payment_data" class="container-fluid">
			<div id="payment_total" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
				<strong>Total: </strong><br />
				B/ <span id="span_payment_total"><?php echo number_format($total_ff,2); ?></span>
			</div>
			<div id="payment_paid_out" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
				<strong>Entrega: </strong><br />
				B/ <span id="span_payment_paid_out"><?php echo number_format($monto_pagado,2); ?></span>
			</div>
			<div id="payment_to_pay" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
				<strong>Diferencia</strong><br />
				B/ <span id="span_payment_to_pay"><?php	echo number_format($diferencia,2);	?> </span>
			</div>
			<div id="payment_change" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
				<strong>Cambio: </strong><br />
				B/ <span id="span_payment_change"><?php	echo number_format($cambio,2);	?> </span>
			</div>
		</div>
	</td>
</tr>
</tfoot>
</table>
