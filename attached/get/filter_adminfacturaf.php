<?php
require '../../bh_conexion.php';
$link = conexion();

$value=$_GET['a'];
$date_i=$_GET['b'];
$str=$_GET['c'];
$limit=$_GET['d'];
$date_f=$_GET['e'];
$metododepago = $_GET['f'];

$limit = ($_GET['d'] === "") ? "" : " LIMIT ".$limit;
$order = ($str == "deficit") ? " ORDER BY  TX_facturaf_deficit DESC, TX_cliente_nombre ASC" : " ORDER BY  TX_facturaf_numero DESC";
if(!empty($date_i)  && !empty($date_f)){
	$pre_date_i=strtotime($date_i);
	$date_i=date('Y-m-d',$pre_date_i);
	$pre_date_f=strtotime($date_f);
	$date_f=date('Y-m-d',$pre_date_f);
	$line_date = " bh_facturaf.TX_facturaf_fecha >= '$date_i' AND bh_facturaf.TX_facturaf_fecha <= '$date_f' AND";
}else{
	$line_date = "";
}
$line_metododepago = ($_GET['f'] != 'todos') ? " datopago_AI_metododepago_id = '{$_GET['f']}' AND" : "";


$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);

$txt_facturaf="SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.facturaf_AI_cliente_id, bh_facturaf.facturaf_AI_user_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_subtotalni, bh_facturaf.TX_facturaf_subtotalci, bh_facturaf.TX_facturaf_impuesto, bh_facturaf.TX_facturaf_descuento, bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_status,
bh_cliente.TX_cliente_nombre
FROM ((bh_facturaf
	INNER JOIN bh_cliente ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id)
	INNER JOIN bh_datopago ON bh_facturaf.AI_facturaf_id = bh_datopago.datopago_AI_facturaf_id)
WHERE ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaf=$txt_facturaf.$line_date.$line_metododepago." bh_cliente.TX_cliente_nombre LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_facturaf=$txt_facturaf.$line_date.$line_metododepago." bh_cliente.TX_cliente_nombre LIKE '%{$arr_value[$it]}%' AND";
	}
}

$txt_facturaf=$txt_facturaf." OR";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaf=$txt_facturaf.$line_date.$line_metododepago." bh_facturaf.TX_facturaf_numero LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_facturaf=$txt_facturaf.$line_date.$line_metododepago." bh_facturaf.TX_facturaf_numero LIKE '%{$arr_value[$it]}%' AND";
	}
}

$txt_facturaf=$txt_facturaf." GROUP BY AI_facturaf_id ".$order.$limit;
$qry_facturaf = $link->query($txt_facturaf)or die($link->error);
$rs_facturaf = $qry_facturaf->fetch_array();
?>
<table id="tbl_facturaf" class="table table-bordered table-condensed table-striped">
	<thead class="bg-primary">
		<tr>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Nº</th>
			<th class="col-xs-4 col-sm-4 col-md-4 col-lg-3">Nombre</th>
			<th class="col-xs-12 col-sm-12 col-md-12 col-lg-1">Fecha</th>
			<th class="col-xs-12 col-sm-12 col-md-12 col-lg-1">Hora</th>
			<th class="col-xs-12 col-sm-12 col-md-12 col-lg-1">Total</th>
			<th class="col-xs-12 col-sm-12 col-md-12 col-lg-1">Deficit</th>
			<th class="col-xs-12 col-sm-12 col-md-12 col-lg-1">Vendedor</th>
			<th class="col-xs-12 col-sm-12 col-md-12 col-lg-1"></th>
			<th class="col-xs-12 col-sm-12 col-md-12 col-lg-1"><i id="filter_by_deficit" class="fa fa-angle-double-down" onclick="filter_adminfacturaf('deficit');"></i></th>
			<th class="col-xs-12 col-sm-12 col-md-12 col-lg-1"></th>
		</tr>
	</thead>
	<tbody>
<?php
$total_total=0; $total_deficit=0;
$total_efectivo=0; $total_tarjeta_dc=0; $total_tarjeta_dd=0; $total_cheque=0; $total_credito=0; $total_notadc=0; $total_porcobrar=0;
$prep_vendor = $link->prepare("SELECT bh_user.TX_user_seudonimo FROM ((bh_facturaf
	INNER JOIN bh_facturaventa ON bh_facturaventa.facturaventa_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
	INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaventa.facturaventa_AI_user_id)
	WHERE bh_facturaf.AI_facturaf_id = ?")or die($link->error);
$prep_payment=$link->prepare("SELECT bh_datopago.TX_datopago_monto, bh_datopago.datopago_AI_metododepago_id, bh_metododepago.TX_metododepago_value FROM ((bh_datopago INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_datopago.datopago_AI_facturaf_id) INNER JOIN bh_metododepago ON bh_datopago.datopago_AI_metododepago_id = bh_metododepago.AI_metododepago_id) WHERE bh_facturaf.AI_facturaf_id = ?")or die($link->error);
		if($nr_facturaf=$qry_facturaf->num_rows > 0){
			do{
				$total_total += $rs_facturaf['TX_facturaf_total'];
				$total_deficit += $rs_facturaf['TX_facturaf_deficit'];
				$prep_payment->bind_param('i',$rs_facturaf['AI_facturaf_id']); $prep_payment->execute(); $qry_payment=$prep_payment->get_result();
				$raw_payment=array();	$i=0;
				while($rs_payment=$qry_payment->fetch_array()){
					$raw_payment[$rs_payment['datopago_AI_metododepago_id']]=$rs_payment['TX_datopago_monto'];
					$i++;
				}
				$style='';
				if (array_key_exists(1,$raw_payment)) {	$style= ($style === '') ? 'style="color: #74c374"' : 'style="color: #700fb4"';	}
				if (array_key_exists(2,$raw_payment)) {	$style= ($style === '') ? 'style="color: #518ec2"' : 'style="color: #700fb4"';	}
				if (array_key_exists(3,$raw_payment)) {	$style= ($style === '') ? 'style="color: #000000"' : 'style="color: #700fb4"';	}
				if (array_key_exists(4,$raw_payment)) {	$style= ($style === '') ? 'style="color: #73c9e3"' : 'style="color: #700fb4"';	}
				if (array_key_exists(5,$raw_payment)) {	$style= ($style === '') ? 'style="color: #df6d69"' : 'style="color: #700fb4"';	}
				if (array_key_exists(7,$raw_payment)) {	$style= ($style === '') ? 'style="color: #f2b968"' : 'style="color: #700fb4"';	}
				if (array_key_exists(8,$raw_payment)) {	$style= ($style === '') ? 'style="color: #bdbd07"' : 'style="color: #700fb4"';	}

				?>
				<tr <?php echo $style; ?> onclick="toggle_tr('tr_<?php echo $rs_facturaf['AI_facturaf_id'];?>')" ondblclick="print_html('print_client_facturaf.php?a=<?php echo $rs_facturaf['AI_facturaf_id'];?>')" >
					<td><?php echo $rs_facturaf['TX_facturaf_numero']; ?></td>
					<td><?php echo $rs_facturaf['TX_cliente_nombre']; ?></td>
					<td><?php echo $fecha=date('d-m-Y',strtotime($rs_facturaf['TX_facturaf_fecha']));	?></td>
					<td><?php echo $rs_facturaf['TX_facturaf_hora']; ?></td>
					<td><?php echo number_format($rs_facturaf['TX_facturaf_total'],2); ?></td>
					<td><?php echo number_format($rs_facturaf['TX_facturaf_deficit'],2); ?></td>
					<td><?php
						$prep_vendor->bind_param('i', $rs_facturaf['AI_facturaf_id']); $prep_vendor->execute(); $qry_vendor=$prep_vendor->get_result();
						$rs_vendor=$qry_vendor->fetch_array(MYSQLI_ASSOC);
						echo $rs_vendor['TX_user_seudonimo'];
					?></td>
					<td><button type="button" id="btn_openff" name="<?php echo $rs_facturaf['AI_facturaf_id']; ?>" class="btn btn-info btn-sm" onclick="open_popup_w_scroll('popup_watchfacturaf.php?a='+this.name,'watch_facturaf','950','547');">VER</button></td>
					<td>
<?php 				if($rs_facturaf['TX_facturaf_deficit'] != '0'){ ?>
							<button type="button" id="btn_opennewdebit" name="<?php echo $rs_facturaf['facturaf_AI_cliente_id']; ?>" class="btn btn-success btn-sm" onclick="open_newdebit(this.name);">DEBITAR</button>
<?php					}			?>
					</td>
					<td><button type="button" id="btn_makenc" name="<?php echo $rs_facturaf['AI_facturaf_id']; ?>" class="btn btn-warning btn-sm" onclick="make_nc(this.name);">N.C.</button></td>
				</tr>
				<tr id="tr_<?php echo $rs_facturaf['AI_facturaf_id'];?>" style="display:none;">
					<td colspan="10" style="padding:0;">
						<table id="tbl_payment" class="table table-condensed table_no_margin table-bordered" style="margin:0;">
							<tr>
								<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<?php								if(isset($raw_payment[1])){ echo "<strong>Efectivo:</strong> <br />".number_format($raw_payment[1],2); $total_efectivo += $raw_payment[1];}	?>
								</td>
								<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<?php								if(isset($raw_payment[2])){ echo "<strong>Cheque:</strong> <br />".number_format($raw_payment[2],2); $total_cheque += $raw_payment[2];}	?>
								</td>
								<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
<?php								if(isset($raw_payment[3])){ echo "<strong>TDC:</strong> <br />".number_format($raw_payment[3],2); $total_tarjeta_dc += $raw_payment[3];}	?>
								</td>
								<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
<?php								if(isset($raw_payment[4])){ echo "<strong>TDD:</strong> <br />".number_format($raw_payment[4],2); $total_tarjeta_dd += $raw_payment[4];}	?>
								</td>
								<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<?php								if(isset($raw_payment[5])){ echo "<strong>Cr&eacute;dito:</strong> <br />".number_format($raw_payment[5],2); $total_credito += $raw_payment[5];}	?>
								</td>
								<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<?php								if(isset($raw_payment[7])){ echo "<strong>Nota de C.:</strong> <br />".number_format($raw_payment[7],2); $total_notadc += $raw_payment[7];}	?>
								</td>
								<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
<?php								if(isset($raw_payment[8])){ echo "<strong>P.Cobrar:</strong> <br />".number_format($raw_payment[8],2); $total_porcobrar += $raw_payment[8];}	?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
<?php 	}while($rs_facturaf=$qry_facturaf->fetch_array()); ?>
<?php }else{ 		?>
		<tr>
			<td colspan="10"></td>
		</tr><?php
	} ?>
</tbody>
<tfoot class="bg-primary">
	<tr>
		<td colspan="10">
			<table id="tbl_total" class="table-condensed table-bordered" style="width:100%">
				<tr>
					<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
						<strong>Efectivo:</strong> <br />B/ <?php echo number_format($total_efectivo,2); ?></td>
					<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
						<strong>Cheque:</strong> <br />B/ <?php echo number_format($total_cheque,2); ?></td>
					<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
						<strong>TDC:</strong> <br />B/ <?php echo number_format($total_tarjeta_dc,2); ?></td>
					<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
						<strong>TDD:</strong> <br />B/ <?php echo number_format($total_tarjeta_dd,2); ?></td>
					<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
						<strong>Cr&eacute;dito:</strong> <br />B/ <?php echo number_format($total_credito,2); ?></td>
					<td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
						<strong>Nota de C.:</strong> <br />B/ <?php echo number_format($total_notadc,2); ?></td>
					<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
						<strong>Total:</strong> <br />B/ <?php echo number_format($total_total,2); ?></td>
					<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
						<strong>Deuda:</strong> <br />B/ <?php echo number_format($total_deficit,2); ?></td>
				</tr>
			</table>
		</td>
	</tr>
</tfoot>
</table>
