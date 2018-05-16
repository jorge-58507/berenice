<?php
require '../../bh_conexion.php';
$link = conexion();

	$link->query("DELETE FROM bh_pago WHERE pago_AI_user_id = '{$_COOKIE['coo_iuser']}'") or die($link->error);

	/* ####################               ANSWER                   ################## */
	$client_id=$_GET['b'];
	$str_factid=$_GET['a'];
	$arr_factid = explode(",",$str_factid);

	$txt_facturaventa="SELECT
	bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento
	FROM ((((bh_facturaventa
	       INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
	       INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
	       INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
	       INNER JOIN bh_user ON bh_facturaventa.facturaventa_AI_user_id = bh_user.AI_user_id)
	WHERE";
	foreach ($arr_factid as $key => $value) {
		if ($value === end($arr_factid)) {
			$txt_facturaventa=$txt_facturaventa." bh_facturaventa.facturaventa_AI_cliente_id = '$client_id' AND AI_facturaventa_id = '$value' ORDER BY AI_facturaventa_id ASC, AI_datoventa_id ASC ";
		}else {
			$txt_facturaventa=$txt_facturaventa." bh_facturaventa.facturaventa_AI_cliente_id = '$client_id' AND AI_facturaventa_id = '$value' OR";
		}
	}
	$qry_facturaventa=$link->query($txt_facturaventa)or die($link->error);
	$total_ff = 0;
	while ($rs_facturaventa=$qry_facturaventa->fetch_array()) {
		$descuento = (($rs_facturaventa['TX_datoventa_descuento']*$rs_facturaventa['TX_datoventa_precio'])/100);
		$precio_descuento = ($rs_facturaventa['TX_datoventa_precio']-$descuento);
		$impuesto = (($rs_facturaventa['TX_datoventa_impuesto']*$precio_descuento)/100);
		$precio_total = ($rs_facturaventa['TX_datoventa_cantidad']*($precio_descuento+$impuesto));
		$total_ff += $precio_total;
	}


	$txt_pago="SELECT bh_pago.AI_pago_id, bh_pago.TX_pago_fecha, bh_pago.TX_pago_monto, bh_pago.TX_pago_numero, bh_metododepago.TX_metododepago_value
	FROM (bh_pago INNER JOIN bh_metododepago ON bh_pago.pago_AI_metododepago_id = bh_metododepago.AI_metododepago_id) WHERE pago_AI_user_id = '{$_COOKIE['coo_iuser']}'";
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
				<td><?php	echo $date=date('d-m-Y',strtotime($rs_pago['TX_pago_fecha']));?></td>
				<td><?php echo $rs_pago['TX_metododepago_value']; ?></td>
				<td><?php echo $rs_pago['TX_pago_numero']; ?></td>
				<td><?php echo number_format($rs_pago['TX_pago_monto'],2); ?></td>
				<td>
	<?php				if($_COOKIE['coo_tuser'] < 3 || $_COOKIE['coo_tuser'] == '4' ){	?>
						<button type="button" name="<?php echo $rs_pago['AI_pago_id']; ?>" id="btn_delpago" class="btn btn-danger btn-xs btn-fa" onclick="del_payment(this.name,'<?php echo $str_factid; ?>')"><i class="fa fa-times" aria-hidden="true"></i></button>
	<?php    		}        ?>
				</td>
			</tr>
	<?php
			$monto_pagado += $rs_pago['TX_pago_monto'];
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
					<strong>Diferencia: </strong><br />
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
