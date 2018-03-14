<?php
require 'bh_conexion.php';
$link=conexion();

require 'attached/php/req_login_sale.php';

$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion");
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}
$value=$_GET['a'];
$limit=$_GET['b'];
$date_i=date('Y-m-d',strtotime($_GET['c']));
$date_f=date('Y-m-d',strtotime($_GET['d']));

if($limit == ""){	$line_limit="";	}else{	$line_limit= " LIMIT ".$limit;	}
if (!empty($date_i) && !empty($date_f)) {
	$line_date = " AND TX_facturacompra_fecha >=	'$date_i' AND TX_facturacompra_fecha <= '$date_f'";
}

$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);

$txt_facturacompra="SELECT bh_facturacompra.AI_facturacompra_id, bh_facturacompra.TX_facturacompra_fecha, bh_facturacompra.TX_facturacompra_numero, bh_almacen.TX_almacen_value, bh_facturacompra.TX_facturacompra_ordendecompra, bh_proveedor.TX_proveedor_nombre, bh_facturacompra.TX_facturacompra_status
FROM ((bh_facturacompra
    INNER JOIN bh_proveedor ON bh_facturacompra.facturacompra_AI_proveedor_id = bh_proveedor.AI_proveedor_id)
	  INNER JOIN bh_almacen ON bh_facturacompra.TX_facturacompra_almacen = bh_almacen.AI_almacen_id)
WHERE";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturacompra=$txt_facturacompra." bh_facturacompra.TX_facturacompra_numero LIKE '%{$arr_value[$it]}%'".$line_date;
	}else{
$txt_facturacompra=$txt_facturacompra." bh_facturacompra.TX_facturacompra_numero LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$txt_facturacompra=$txt_facturacompra." OR ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturacompra=$txt_facturacompra." bh_facturacompra.TX_facturacompra_ordendecompra LIKE '%{$arr_value[$it]}%'".$line_date;
	}else{
$txt_facturacompra=$txt_facturacompra." bh_facturacompra.TX_facturacompra_ordendecompra LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$txt_facturacompra=$txt_facturacompra." OR ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturacompra=$txt_facturacompra." bh_proveedor.TX_proveedor_nombre LIKE '%{$arr_value[$it]}%'".$line_date;
	}else{
$txt_facturacompra=$txt_facturacompra." bh_proveedor.TX_proveedor_nombre LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$txt_facturacompra .= " ORDER BY TX_facturacompra_fecha DESC, AI_facturacompra_id DESC".$line_limit;

$qry_facturacompra=$link->query($txt_facturacompra)or die($link->error);
$nr_facturacompra=$qry_facturacompra->num_rows;

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reporte de Compras</title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css" />
</head>
<script type="text/javascript">
function cap_fl(str){
	  return string.charAt(0).toUpperCase() + string.slice(1);
}
</script>

<body style="font-family:Arial<?php /* echo $RS_medinfo['TX_fuente_medico']; */?>" >
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
					<p><h4>FACTURAS DE COMPRAS</h4></p>
					<strong>Desde:</strong> <?php echo date('d-m-Y',strtotime($date_i)) ?>&nbsp;
					<strong>Hasta:</strong> <?php echo date('d-m-Y',strtotime($date_f)) ?>
        </td>
      </tr>
    </table>
  </td>
</tr>
<tr style="height:773px;">
	<td valign="top" colspan="10" style="padding-top:2px;">
		<table id="tbl_facturacompra" class="table table-bordered table-condensed table-striped table-print">
		<thead style="border: solid">
		<tr>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">FECHA</th>
			<th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">PROVEEDOR</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">FACT.</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">STATUS</th>
			<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">ALMACEN</th>
			<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">TOTAL</th>
		</tr>
		</thead>
		<tbody>
<?php
	$qry_datocompra = $link->prepare("SELECT bh_datocompra.TX_datocompra_precio,
		bh_datocompra.TX_datocompra_cantidad, bh_datocompra.TX_datocompra_impuesto, bh_datocompra.TX_datocompra_descuento
	FROM (bh_datocompra
	INNER JOIN bh_facturacompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id)
	WHERE bh_facturacompra.AI_facturacompra_id = ?");
	$total=0;
	while($rs_facturacompra=$qry_facturacompra->fetch_array()){
		$qry_datocompra->bind_param("i", $rs_facturacompra['AI_facturacompra_id']);
		$qry_datocompra->execute()or die($link->error);
		$result=$qry_datocompra->get_result();
		$total4facturacompra=0;
		while ($rs_datocompra=$result->fetch_array()) {
			$base4product=$rs_datocompra['TX_datocompra_cantidad']*$rs_datocompra['TX_datocompra_precio'];
			$descuento=($rs_datocompra['TX_datocompra_descuento']*$base4product)/100;
			$base_descuento=$base4product-$descuento;
			$impuesto=($rs_datocompra['TX_datocompra_impuesto']*$base_descuento)/100;
			$precio4product=$base_descuento+$impuesto;
			$total4facturacompra += $precio4product;
		}
		$total+=$total4facturacompra;

	?>
		<tr>
			<td><?php echo date('d-m-Y',strtotime($rs_facturacompra['TX_facturacompra_fecha'])); ?></td>
			<td><?php echo $rs_facturacompra['TX_proveedor_nombre']; ?></td>
			<td><?php echo $rs_facturacompra['TX_facturacompra_numero']; ?></td>
			<td><?php echo $rs_facturacompra['TX_facturacompra_status']; ?></td>
			<td><?php echo $rs_facturacompra['TX_almacen_value']; ?></td>
			<td><?php echo number_format($total4facturacompra,4); ?></td>
		</tr>
<?php } ?>
		</tbody>
		<tfoot style="border:solid;">
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td colspan="2"  style="text-align: right;"><strong>Total: </strong> B/ <?php echo number_format($total,2); ?></td>
		</tr>
		</tfoot>
		</table>


  </td>
</tr>
</table>
</body>
</html>
