<?php
set_time_limit(120);
require 'bh_conexion.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login_sale.php';
?>
<?php
$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion");
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}
$value=$_GET['a'];
$limit=$_GET['b'];
$date_i=date('Y-m-d',strtotime($_GET['c']));
$date_f=date('Y-m-d',strtotime($_GET['d']));

if($limit === ""){	$line_limit="";	}else{	$line_limit= " LIMIT ".$limit;	}
if (!empty($date_i) && !empty($date_f)) {
	$line_date = " AND TX_facturaf_fecha >=	'$date_i' AND TX_facturaf_fecha <= '$date_f'";
	$line_date_nc = " TX_notadecredito_fecha >= '$date_i' AND TX_notadecredito_fecha <= '$date_f'";
}

$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);

$txt_facturaf="SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_numero,  bh_facturaf.TX_facturaf_total, bh_cliente.TX_cliente_nombre
FROM (bh_facturaf
	INNER JOIN bh_cliente ON bh_cliente.AI_cliente_id = bh_facturaf.facturaf_AI_cliente_id)
WHERE";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaf=$txt_facturaf." bh_facturaf.TX_facturaf_numero LIKE '%{$arr_value[$it]}%'".$line_date;
	}else{
$txt_facturaf=$txt_facturaf." bh_facturaf.TX_facturaf_numero LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$txt_facturaf=$txt_facturaf." OR ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaf=$txt_facturaf." bh_cliente.TX_cliente_nombre LIKE '%{$arr_value[$it]}%'".$line_date;
	}else{
$txt_facturaf=$txt_facturaf." bh_cliente.TX_cliente_nombre LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$txt_facturaf .= " ORDER BY TX_facturaf_fecha DESC, AI_facturaf_id DESC ".$line_limit;

$qry_facturaf=$link->query($txt_facturaf)or die(mysql_error());
$nr_facturaf=$qry_facturaf->num_rows;

$txt_nc = "SELECT TX_notadecredito_monto, TX_notadecredito_impuesto FROM bh_notadecredito WHERE ".$line_date_nc;
$qry_nc = $link->query($txt_nc);
$ttl_nc_impuesto = 0; $ttl_nc = 0;
while ($rs_nc = $qry_nc->fetch_array()) {
	$ttl_nc_impuesto += $rs_nc['TX_notadecredito_impuesto'];
	$ttl_nc += $rs_nc['TX_notadecredito_monto']+$rs_nc['TX_notadecredito_impuesto'];
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de Ventas</title>

<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css" />
</head>
<script type="text/javascript">
function cap_fl(str){
	  return string.charAt(0).toUpperCase() + string.slice(1);
}

</script>

<body style="font-family:Arial<?php /* echo $RS_medinfo['TX_fuente_medico']; */?>" onload="window.print();" >
<?php
$fecha_actual=date('Y-m-d');
$dias = array('Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
$d_number=date('w',strtotime($fecha_actual));
$fecha_dia = $dias[$d_number];
$fecha = date('d-m-Y',strtotime($fecha_actual));
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
<tr style="height:64px">
	<td valign="top" colspan="10">
    <table align="center" border="0" cellpadding="0" cellspacing="0" style="width: 100%; font-size: 12px;">
      <tr>
				<td valign="top" style="text-align:center;">
					<p><h4>FACTURAS DE VENTAS</h4></p>
					<strong>Desde:</strong> <?php echo date('d-m-Y',strtotime($date_i)) ?>&nbsp;
					<strong>Hasta:</strong> <?php echo date('d-m-Y',strtotime($date_f)) ?>
        </td>
      </tr>
    </table>
  </td>
</tr>
<tr id="content_print" style="height:773px;">
	<td valign="top" colspan="10" style="padding-top:2px;">
		<table id="tbl_facturaf" class="table table-bordered table-condensed table-striped table-print">
		<thead style="border: solid">
		<tr>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">FECHA</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">NUMERO</th>
			<th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">CLIENTE</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">BASE NI</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">BASE I</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">IMP.</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">TOTAL</th>
		</tr>
		</thead>
		<tbody>
<?php
	$qry_datoventa = $link->prepare("SELECT bh_datoventa.TX_datoventa_precio,
		bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento
	FROM ((bh_datoventa
	INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
	INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)
	WHERE bh_facturaf.AI_facturaf_id = ?")or die($link->error);
	$total=0;
	$total_impuesto=0;
	$total_base_i=0;
	$total_base_ni=0;
	$index = 1;
	$pager = 1;
	while($rs_facturaf=$qry_facturaf->fetch_array()){
	$pager++;
	if($index === 1){
	if($pager === 28){
		$pager = 0;
		$index++;
	?>
		</tbody>
		</table>
	</td>
</tr>
<tr style="height:773px;">
<td valign="top" colspan="10" style="padding-top:2px;">
		<table class="table table-bordered table-condensed table-striped table-print">
			<table id="tbl_facturaf" class="table table-bordered table-condensed table-striped table-print">
			<thead style="border: solid">
			<tr>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">FECHA</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">NUMERO</th>
				<th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">CLIENTE</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">BASE NI</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">BASE I</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">IMP.</th>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">TOTAL</th>
			</tr>
			</thead>
			<tbody>
	<?php
	}
}else{
	if($pager === 33){
		$pager = 0;
		$index++;
	?>
		</tbody>
		</table>
	</td>
</tr>
<tr style="height:773px;">
<td valign="top" colspan="10" style="padding-top:2px;">
		<table class="table table-bordered table-condensed table-striped table-print">
			<table id="tbl_facturaf" class="table table-bordered table-condensed table-striped table-print">
			<thead style="border: solid">
			<tr>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">FECHA</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">NUMERO</th>
				<th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">CLIENTE</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">BASE NI</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">BASE I</th>
				<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">IMP.</th>
				<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">TOTAL</th>
			</tr>
			</thead>
			<tbody>
	<?php
	}
}
		$qry_datoventa->bind_param("i", $rs_facturaf['AI_facturaf_id']);
		$qry_datoventa->execute()or die($link->error);
		$result=$qry_datoventa->get_result();
		$total4facturaf=0;
		$base_ni4facturaf=0;
		$base_i4facturaf=0;
		$impuesto4facturaf=0;
		while ($rs_datoventa=$result->fetch_array()) {
			$base4product=$rs_datoventa['TX_datoventa_cantidad']*$rs_datoventa['TX_datoventa_precio'];
			$descuento=($rs_datoventa['TX_datoventa_descuento']*$base4product)/100;
			$base_descuento=$base4product-$descuento;
			$impuesto=($rs_datoventa['TX_datoventa_impuesto']*$base_descuento)/100;
			$impuesto4facturaf += $impuesto;
			if ($impuesto == 0) {	$base_ni4facturaf += $base_descuento;	}else{	$base_i4facturaf += $base_descuento;	}
			$precio4product=$base_descuento+$impuesto;
			$total4facturaf += $precio4product;
		}
		$total_impuesto+=$impuesto4facturaf;
		$total_base_i+=$base_i4facturaf;
		$total_base_ni+=$base_ni4facturaf;
		$total+=$total4facturaf;

	?>
		<tr>
			<td><?php echo $rs_facturaf['TX_facturaf_fecha']; ?></td>
			<td><?php echo $rs_facturaf['TX_facturaf_numero']; ?></td>
			<td><?php echo substr($rs_facturaf['TX_cliente_nombre'], 0, 30); ?></td>
			<td><?php echo number_format($base_ni4facturaf,4); ?></td>
			<td><?php echo number_format($base_i4facturaf,4); ?></td>
			<td><?php echo number_format($impuesto4facturaf,4); ?></td>
			<td><?php echo number_format($total4facturaf,4); ?></td>
		</tr>
<?php } ?>
		<tr style="border:solid;">
			<td></td>
			<td></td>
			<td></td>
			<td><?php echo number_format($total_base_ni,2) ?></td>
			<td><?php echo number_format($total_base_i,2) ?></td>
			<td><?php echo number_format($total_impuesto,2); ?></td>
			<td><strong>B/</strong> <?php echo number_format($total,2); ?></td>
		</tr>
	</tbody>
		</table>


  </td>
</tr>
</table>
	<table class="table-bordered table-striped"  style="margin:0 auto; width:720px; page-break-before:always; text-align:center; font-size:30px;">
		<tbody>
			<tr>
				<td><strong>TOTALES DE VENTAS</strong></td>
			</tr>
			<tr>
				<td><strong>Base No Imponible</strong><br /><strong>B/</strong> <?php echo number_format($total_base_ni,2) ?></td>
			</tr>
			<tr>
				<td><strong>Base Imponible</strong><br /><strong>B/</strong> <?php echo number_format($total_base_i,2) ?></td>
			</tr>
			<tr>
				<td><strong>Impuesto</strong><br /><strong>B/</strong> <?php echo number_format($total_impuesto,2); ?></td>
			</tr>
			<tr>
				<td><strong>Total</strong><br /><strong>B/</strong> <?php echo number_format($total,2); ?></td>
			</tr>
			<tr>
				<td><strong>TOTALES DE NOTAS DE CREDITO</strong></td>
			</tr>
			<tr>
				<td><strong>Monto</strong><br /><strong>B/</strong> <?php echo number_format($ttl_nc,2); ?></td>
			</tr>
			<tr>
				<td><strong>Devolucion de Impuestos</strong><br /><strong>B/</strong> <?php echo number_format($ttl_nc_impuesto,2); ?></td>
			</tr>
			<tr>
				<td valign="center" style="text-align:center;">
					<p><h4>FACTURAS DE VENTAS</h4></p>
					<strong>Desde:</strong> <?php echo date('d-m-Y',strtotime($date_i)) ?>&nbsp;
					<strong>Hasta:</strong> <?php echo date('d-m-Y',strtotime($date_f)) ?>
        </td>
			</tr>
		</tbody>
	</table>
</body>
</html>
