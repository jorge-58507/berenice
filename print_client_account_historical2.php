<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_sale.php';

$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion")or die($link->error);
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}
$client_id = $_GET['a'];
$txt_client="SELECT bh_cliente.AI_cliente_id, bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_telefono, SUM(bh_facturaf.TX_facturaf_deficit) AS deficit, SUM(bh_facturaf.TX_facturaf_subtotalni) AS subtotal_ni, SUM(bh_facturaf.TX_facturaf_subtotalci) AS subtotal_ci, SUM(bh_facturaf.TX_facturaf_total) AS total, SUM(bh_facturaf.TX_facturaf_impuesto) AS impuesto FROM (bh_cliente INNER JOIN bh_facturaf ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id)
WHERE bh_facturaf.facturaf_AI_cliente_id = '$client_id'";
$qry_client=$link->query($txt_client);
$rs_client=$qry_client->fetch_array();
$qry_client=$link->query("SELECT TX_cliente_nombre, TX_cliente_cif, TX_cliente_telefono, TX_cliente_direccion FROM bh_cliente WHERE AI_cliente_id = '$client_id'");
$rs_client=$qry_client->fetch_array();

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Facturas: <?php echo $rs_client['TX_cliente_nombre']; ?></title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css" />
</head>
<script type="text/javascript">
function cap_fl(str){
	  return string.charAt(0).toUpperCase() + string.slice(1);
}
</script>

<body style="font-family:Arial<?php /* echo $RS_medinfo['TX_fuente_medico']; */?>" onLoad="window.print()">
<?php
$fecha_actual=date('Y-m-d');
$dias = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
$d_number=date('w',strtotime($fecha_actual));
$fecha_dia = $dias[$d_number];
$fecha = date('d-m-Y',strtotime($fecha_actual));

$line_order=" ORDER BY TX_facturaf_fecha ASC, TX_facturaf_numero ASC";


		$prep_nd = $link->prepare("SELECT bh_notadebito.AI_notadebito_id, bh_notadebito.TX_notadebito_fecha, bh_notadebito.TX_notadebito_hora, bh_notadebito.TX_notadebito_motivo, bh_notadebito.TX_notadebito_numero, bh_notadebito.TX_notadebito_total, rel_facturaf_notadebito.TX_rel_facturafnotadebito_importe
			FROM (bh_notadebito
				INNER JOIN rel_facturaf_notadebito ON bh_notadebito.AI_notadebito_id =	rel_facturaf_notadebito.rel_AI_notadebito_id)
				WHERE bh_notadebito.AI_notadebito_id = ? ORDER BY TX_notadebito_fecha ASC, TX_notadebito_hora ASC")or die($link->error);

		$prep_ff=$link->prepare("SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_facturaf.TX_facturaf_numero,
			 bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_subtotalni, bh_facturaf.TX_facturaf_descuentoni,
			  bh_facturaf.TX_facturaf_subtotalci, bh_facturaf.TX_facturaf_impuesto, bh_facturaf.TX_facturaf_descuento, bh_facturaf.TX_facturaf_cambio,
		bh_user.TX_user_seudonimo, bh_datopago.TX_datopago_monto
		FROM (((bh_facturaf
		INNER JOIN bh_facturaventa ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
		INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaventa.facturaventa_AI_user_id)
		INNER JOIN bh_datopago ON bh_datopago.datopago_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
		WHERE bh_facturaf.AI_facturaf_id = ? ORDER BY TX_facturaf_fecha ASC, TX_facturaf_numero ASC")or die($link->error);

		$prep_ff_numero=$link->prepare("SELECT bh_facturaf.TX_facturaf_numero
			FROM (bh_facturaf
			INNER JOIN rel_facturaf_notadebito ON rel_facturaf_notadebito.rel_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
			WHERE rel_facturaf_notadebito.rel_AI_notadebito_id = ? AND rel_facturaf_notadebito.TX_rel_facturafnotadebito_importe = ?")or die($link->error);

		$txt_facturaf="	SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_facturaf.TX_facturaf_numero,
			 bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_subtotalni, bh_facturaf.TX_facturaf_descuentoni,
			  bh_facturaf.TX_facturaf_subtotalci, bh_facturaf.TX_facturaf_impuesto, bh_facturaf.TX_facturaf_descuento, bh_facturaf.TX_facturaf_cambio,
		bh_user.TX_user_seudonimo, bh_datopago.TX_datopago_monto
		FROM (((bh_facturaf
		INNER JOIN bh_facturaventa ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
		INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaventa.facturaventa_AI_user_id)
		INNER JOIN bh_datopago ON bh_datopago.datopago_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
		WHERE facturaf_AI_cliente_id = '$client_id' AND datopago_AI_metododepago_id = '5' AND TX_facturaf_deficit > 0 GROUP BY AI_facturaf_id";
		$qry_facturaf=$link->query($txt_facturaf.$line_order) or die($link->error);
			$raw_ff_included=array();
			$raw_nd_included=array();
			$ciclo=0;
			while($rs_facturaf=$qry_facturaf->fetch_array(MYSQLI_ASSOC)){
					$raw_ff_included[$rs_facturaf['AI_facturaf_id']]=$rs_facturaf;
			}
			find_nd_to_include($raw_ff_included,$raw_nd_included,$ciclo);
        // #########   ENCONTRAR NOTA DE DEBITO PARA INCLUIR AL ARRAY
function find_nd_to_include($raw_ff_included,$raw_nd_included,$ciclo){
	$link=conexion();
	global $raw_ff_included, $raw_nd_included, $ciclo;
	$prep_nd = $link->prepare("SELECT bh_notadebito.AI_notadebito_id, bh_notadebito.TX_notadebito_fecha, bh_notadebito.TX_notadebito_hora, bh_notadebito.TX_notadebito_motivo, bh_notadebito.TX_notadebito_numero, rel_facturaf_notadebito.TX_rel_facturafnotadebito_importe
		FROM ((bh_notadebito
			INNER JOIN rel_facturaf_notadebito ON bh_notadebito.AI_notadebito_id =	rel_facturaf_notadebito.rel_AI_notadebito_id)
			INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id =	rel_facturaf_notadebito.rel_AI_facturaf_id)
			WHERE bh_facturaf.AI_facturaf_id = ? ORDER BY TX_notadebito_fecha ASC, TX_notadebito_hora ASC")or die($link->error);

	foreach ($raw_ff_included as $ff_key => $rs_facturaf) {
		$prep_nd->bind_param("i", $ff_key); $prep_nd->execute(); $qry_nd = $prep_nd->get_result();
		while ($rs_nd=$qry_nd->fetch_array(MYSQLI_ASSOC)) {
			if (!array_key_exists($rs_nd['AI_notadebito_id'],$raw_nd_included)) {
				$raw_nd_included[$rs_nd['AI_notadebito_id']]=$rs_nd;
				$ciclo=1;
			}
		}
	}
	if ($ciclo === 1) {
		$ciclo=0;
		find_ff_to_include($raw_ff_included,$raw_nd_included,$ciclo);
	}
}
// #########   ENCONTRAR FACTURA FISCAL PARA INCLUIR AL ARRAY
function find_ff_to_include($raw_ff_included,$raw_nd_included,$ciclo){
	$link=conexion();
	global $raw_ff_included, $raw_nd_included, $ciclo;

	foreach ($raw_nd_included as $nd_key => $nd_included) {
		$prep_ff=$link->prepare("SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_facturaf.TX_facturaf_numero,
			 bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_facturaf.TX_facturaf_subtotalni, bh_facturaf.TX_facturaf_descuentoni,
				bh_facturaf.TX_facturaf_subtotalci, bh_facturaf.TX_facturaf_impuesto, bh_facturaf.TX_facturaf_descuento, bh_facturaf.TX_facturaf_cambio,
		bh_user.TX_user_seudonimo, bh_datopago.TX_datopago_monto
		FROM (((((bh_facturaf
		INNER JOIN bh_facturaventa ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
		INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaventa.facturaventa_AI_user_id)
		INNER JOIN bh_datopago ON bh_datopago.datopago_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
		INNER JOIN rel_facturaf_notadebito ON bh_facturaf.AI_facturaf_id =	rel_facturaf_notadebito.rel_AI_facturaf_id)
		INNER JOIN bh_notadebito ON bh_notadebito.AI_notadebito_id =	rel_facturaf_notadebito.rel_AI_notadebito_id)
		WHERE bh_notadebito.AI_notadebito_id = ? ORDER BY TX_facturaf_fecha ASC, TX_facturaf_numero ASC")or die($link->error);

		$prep_ff->bind_param("i",$nd_key); $prep_ff->execute(); $qry_ff =	$prep_ff->get_result();
		while ($rs_ff=$qry_ff->fetch_array(MYSQLI_ASSOC)) {
			if (!array_key_exists($rs_ff['AI_facturaf_id'],$raw_ff_included)) {
				$raw_ff_included[$rs_ff['AI_facturaf_id']]=$rs_ff;
				$ciclo=1;
			}
		}
	}
	if ($ciclo === 1) {
		$ciclo=0;
		find_nd_to_include($raw_ff_included,$raw_nd_included,$ciclo);
	}
}


$raw_facturaf_debito=array();
$it=0;
foreach ($raw_ff_included as $key_ff => $value_ff) {
	$prep_ff->bind_param("i",$key_ff); $prep_ff->execute(); $qry_ff = $prep_ff->get_result();
	while ($rs_ff = $qry_ff->fetch_array(MYSQLI_ASSOC)) {
		$raw_facturaf_debito[$rs_ff['TX_facturaf_fecha']."ff".$it]=$rs_ff;
		$it++;
	}
}
$it=0;
foreach ($raw_nd_included as $key_nd => $value_nd) {
	$prep_nd->bind_param("i",$key_nd); $prep_nd->execute(); $qry_nd = $prep_nd->get_result();
	while ($rs_nd = $qry_nd->fetch_array(MYSQLI_ASSOC)) {
		$raw_facturaf_debito[$rs_nd['TX_notadebito_fecha']."nd".$it]=$rs_nd;
		$it++;
	}
}
			ksort($raw_facturaf_debito);
?>
<table cellpadding="0" cellspacing="0" border="0" style="height:975px; width:720px; font-size:12px; margin:0 auto">
<tr style="height:6px">
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
</tr>
<tr style="height:132px" align="right">
	<td colspan="2" style="text-align:left">
    </td>

   	<td valign="top" colspan="6" style="text-align:center">
<img width="200px" height="75px" src="attached/image/logo_factura.png">
<br />
<font style="font-size:10px">RUC: <?php echo $raw_opcion['RUC']; ?> DV: <?php echo $raw_opcion['DV']."<br/>"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['DIRECCION']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['TELEFONO']." "
.$raw_opcion['FAX']."<br />"; ?></font>
<font style="font-size:10px"><?php echo $raw_opcion['EMAIL']."<br />"; ?></font>
    </td>

    <td valign="top" colspan="2" class="optmayuscula">
<?php echo $fecha_dia."&nbsp;-&nbsp;"; ?><?php echo $fecha; ?>
    </td>
</tr>
<tr style="height:21px" align="center">
	<td valign="top" colspan="10">
    </td>
</tr>
<tr style="height:134px">
	<td valign="top" colspan="10">
		<table align="center" border="0" cellpadding="0" cellspacing="0" style="width: 100%; font-size: 12px;">
        <tr>
        	<td valign="top" style="text-align:center;">
						<h4>Estado de Cuenta Hist&oacute;rico</h4><br />
          </td>
        </tr>
    </table>
		<table id="tbl_client" class="table table-print">
		<tbody style="background-color:#dddddd; border:solid;">
    	<tr>
        	<td valign="top" class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
            <strong>Nombre: </strong><?php echo strtoupper($rs_client['TX_cliente_nombre']); ?>
            </td>
            <td valign="top" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <strong>RUC: </strong><?php echo strtoupper($rs_client['TX_cliente_cif']); ?>
            </td>
            <td valign="top" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <strong>Telefono: </strong><?php echo $rs_client['TX_cliente_telefono']; ?>
            </td>
    	</tr>
        <tr>
        	<td valign="top" colspan="3">
            <strong>Direcci&oacute;n: </strong><?php echo strtoupper($rs_client['TX_cliente_direccion']); ?>
            </td>
        </tr>
        </tbody>
    </table>
    </td>
</tr>
<tr style="height:682px;">
	<td valign="top" colspan="10" style="padding-top:2px;">
    <table id="tbl_movement" class="table table-striped table-bordered" style="font-size:12px">
	    <thead style="border:solid; background-color:#DDDDDD">
	    	<tr>
					<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><strong>FECHA</strong></th>
					<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><strong>DESCRIPCION</strong></th>
					<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>NUMERO</strong></th>
					<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>IMPORTE</strong></th>
					<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>SALDO</strong></th>
				</tr>
			</thead>
	    	<tbody>
	<?php
		$sumatoria=0; $index = 1;	$pager = 0;
		foreach ($raw_facturaf_debito as $key => $rs_facturaf_debito) {
			$pager++;
			if($index === 1){
				if($pager === 19){
					$pager = 0;
					$index++;
?>
				</tbody>
			</table>
		</td>
	</tr>
	<tr style="height:720px;">
		<td valign="top" colspan="10" style="padding-top:2px;">
		    <table id="tbl_movement" class="table table-striped table-bordered" style="font-size:12px">
			    <thead style="border:solid; background-color:#DDDDDD">
			    	<tr>
							<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><strong>FECHA</strong></th>
							<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><strong>DESCRIPCION</strong></th>
							<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>NUMERO</strong></th>
							<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>IMPORTE</strong></th>
							<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>SALDO</strong></th>
						</tr>
					</thead>
		    	<tbody>
<?php
				}
			}else{
				if($pager === 19){
					$pager = 0;
					$index++;
?>
					</tbody>
				</table>
			</td>
		</tr>
		<tr style="height:720px;">
			<td valign="top" colspan="10" style="padding-top:2px;">
				<table id="tbl_movement" class="table table-striped table-bordered" style="font-size:12px">
					<thead style="border:solid; background-color:#DDDDDD">
						<tr>
							<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><strong>FECHA</strong></th>
							<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><strong>DESCRIPCION</strong></th>
							<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>NUMERO</strong></th>
							<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>IMPORTE</strong></th>
							<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>SALDO</strong></th>
						</tr>
					</thead>
					<tbody>
<?php
				}
			}

		$str = $key;
			if ($coincidencia = substr_count($str, "ff") > 0): ?>
				<tr>
					<td><?php echo date('d-m-Y', strtotime($rs_facturaf_debito['TX_facturaf_fecha']))."-".$rs_facturaf_debito['TX_facturaf_hora']; ?></td>
					<td><?php echo "FACTURA"; ?></td>
					<td><?php echo $rs_facturaf_debito['TX_facturaf_numero']; ?></td>
					<td><?php echo "+".$rs_facturaf_debito['TX_datopago_monto']; ?></td>
					<td><?php $sumatoria += $rs_facturaf_debito['TX_datopago_monto']; echo "B/ ".number_format($sumatoria,2); ?></td>
				</tr>
	<?php else: ?>
				<tr>
					<td><?php echo date('d-m-Y', strtotime($rs_facturaf_debito['TX_notadebito_fecha']))." - ".$rs_facturaf_debito['TX_notadebito_hora']; ?></td>
<?php $prep_ff_numero->bind_param("is",$rs_facturaf_debito['AI_notadebito_id'],$rs_facturaf_debito['TX_rel_facturafnotadebito_importe']); $prep_ff_numero->execute(); $qry_ff_numero = $prep_ff_numero->get_result(); $rs_ff_numero = $qry_ff_numero->fetch_array(MYSQLI_ASSOC);
					$ff_numero = $rs_ff_numero['TX_facturaf_numero']; ?>
					<td><?php echo $rs_facturaf_debito['TX_notadebito_motivo']." (FACT ".$ff_numero.")"; ?></td>
					<td><?php echo $rs_facturaf_debito['TX_notadebito_numero']; ?></td>
					<td><?php echo "-".number_format($rs_facturaf_debito['TX_rel_facturafnotadebito_importe'],2); ?></td>
					<td><?php $sumatoria -= $rs_facturaf_debito['TX_rel_facturafnotadebito_importe']; echo "B/ ".number_format($sumatoria,2); ?></td>
				</tr>
	<?php endif; ?>


	<?php } ?>
		 	</tbody>
			<tfoot style="border:solid; background-color:#DDDDDD; text-align: left;"><tr><td colspan="4"></td><td style="font-weight: bolder; padding:0;"><?php echo "SALDO: B/".number_format($sumatoria,2); ?></td></tr></tfoot>
		</table>
  </td>
</tr>
</table>
</body>
</html>
