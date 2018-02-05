<?php
require 'bh_con.php';
$link=conexion();
?>
<?php
require 'attached/php/req_login_sale.php';
?>
<?php
//$factura_numero=$_GET['a'];
$facturaventa_id=$_GET['a'];

$qry_opcion=mysql_query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion");
$raw_opcion=array();
while($rs_opcion=mysql_fetch_array($qry_opcion)){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}

$txt_checkuser_bill="SELECT * FROM bh_facturaventa WHERE AI_facturaventa_id = '$facturaventa_id'";

$qry_checkuser_bill=mysql_query($txt_checkuser_bill, $link);
$nr_checkuser_bill=mysql_num_rows($qry_checkuser_bill);

if($nr_checkuser_bill < 1){
//	echo "<meta http-equiv='Refresh' content='1;url=index.php'>";
echo $txt_checkuser_bill;
}

$qry_facturaventa=mysql_query("SELECT bh_facturaventa.AI_facturaventa_id, bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.TX_facturaventa_observacion, bh_facturaventa.facturaventa_AI_cliente_id, bh_facturaventa.facturaventa_AI_user_id, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status, bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_direccion, bh_cliente.TX_cliente_telefono, bh_datoventa.datoventa_AI_producto_id, bh_producto.TX_producto_value, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento, bh_producto.TX_producto_codigo, bh_user.TX_user_seudonimo
FROM ((((bh_facturaventa
       INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
       INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
       INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
       INNER JOIN bh_user ON bh_facturaventa.facturaventa_AI_user_id = bh_user.AI_user_id)
WHERE AI_facturaventa_id = '$facturaventa_id'");
$rs_facturaventa=mysql_fetch_assoc($qry_facturaventa);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Factura: <?php echo $rs_facturaventa['TX_cliente_nombre']." - #".$rs_facturaventa['TX_facturaventa_numero'] ?></title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css">
</head>
<script type="text/javascript">
function cap_fl(str){
	  return string.charAt(0).toUpperCase() + string.slice(1);
}
</script>

<body style="font-family:Arial<?php /* echo $RS_medinfo['TX_fuente_medico']; */?>" onLoad="window.print()">

<?php
$dias = array('','Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','Sabado');
$fecha = $dias[date('N', strtotime($rs_facturaventa['TX_facturaventa_fecha']))+1];
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
<tr style="height:135px" align="right">
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
    <?php
		$time=strtotime($rs_facturaventa['TX_facturaventa_fecha']);
		$date=date('d-m-Y',$time);
	?>
<?php echo $fecha."&nbsp;-&nbsp;"; ?><?php echo $date; ?>
    </td>
</tr>
<?php /*?><tr style="height:21px" align="center">
	<td valign="top" colspan="10">
    </td>
</tr>
<?php */?>
<tr style="height:123px">
	<td valign="top" colspan="10">
    <table id="tbl_client" class="table table-print" style="border:solid; background-color:#DDDDDD;">
    	<tr>
        	<td valign="top" style="width:50%;">
            <strong>Vendedor(a): </strong><?php echo strtoupper($rs_facturaventa['TX_user_seudonimo']); ?>
            </td>
            <td valign="top" style="width:20%">
            <strong>Presupuesto Nº: </strong><?php echo strtoupper($rs_facturaventa['TX_facturaventa_numero']); ?>
            </td>
            <td valign="top" style="width:30%">
            </td>
    	</tr>
	</table>
    <table id="tbl_client" class="table table-print" style="border:solid; background-color:#DDDDDD;">
    	<tr>
        	<td valign="top" class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
            <strong>Nombre: </strong><?php echo strtoupper($rs_facturaventa['TX_cliente_nombre']); ?>
            </td>
            <td valign="top" class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <strong>RUC: </strong><?php echo strtoupper($rs_facturaventa['TX_cliente_cif']); ?>
            </td>
            <td valign="top" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <strong>Telefono: </strong><?php echo $rs_facturaventa['TX_cliente_telefono']; ?>
            </td>
    	</tr>
        <tr>
        	<td colspan="2">
            <strong>Direcci&oacute;n: </strong><?php echo strtoupper($rs_facturaventa['TX_cliente_direccion']); ?>
            </td>
            <td></td>
        </tr>
    </table>
    </td>
</tr>
<tr style="height:45px">
	<td valign="top" colspan="10">
        <table id="tbl_observation" class="table table-print" style="border:solid;">
        <tr>
        	<td valign="top" style="width:100%">
            <strong>Observaci&oacute;n: </strong><?php echo strtoupper($rs_facturaventa['TX_facturaventa_observacion']); ?>
            </td>
        </tr>
        </table>
    </td>
</tr>
<tr style="height:592px;">
	<td valign="top" colspan="10" style="padding-top:2px;">
    <table table id="tbl_product" class="table table-print table-bordered table-striped" >
    <thead style="border:solid">
    	<tr>
        <th>Codigo</th>
        <th>Detalle</th>
        <th>Cant.</th>
        <th>Precio</th>
        <th>Desc.</th>
        <th>Imp.</th>
        <th>Total.</th>
		</tr>
		</thead>
    <tbody>
<?php
				$total=0;
				$totalitbm=0;
				$totaldescuento=0;
				$index = 1;
				$pager = 0;
 			do{
				$pager++;
				if($index === 1){
					if($pager === 16){
						$pager = 0;
						$index++;
?>
				</tbody>
				</table>
				</td>
				</tr>
				<tr style="height:592px;">
				<td valign="top" colspan="10" style="padding-top:2px;">
					<table table id="tbl_product" class="table table-bordered table-striped table-print" >
			    <thead style="border:solid">
			    	<tr>
			        <th>Codigo</th>
			        <th>Detalle</th>
			        <th>Cant.</th>
			        <th>Precio</th>
			        <th>Desc.</th>
			        <th>Imp.</th>
			        <th>Total.</th>
					</tr>
					</thead>
			    <tbody>
<?php
				}
			}else{
				if($pager === 23){
					$pager = 0;
					$index++;
?>
					</tbody>
					</table>
					</td>
					</tr>
					<tr style="height:943px;">
					<td valign="top" colspan="10" style="padding-top:2px;">
						<table table id="tbl_product" class="table table-bordered table-striped table-print" >
				    <thead style="border:solid">
				    	<tr>
				        <th>Codigo</th>
				        <th>Detalle</th>
				        <th>Cant.</th>
				        <th>Precio</th>
				        <th>Desc.</th>
				        <th>Imp.</th>
				        <th>Total.</th>
						</tr>
						</thead>
				    <tbody>
<?php
					}
				}

				$precio = $rs_facturaventa['TX_datoventa_cantidad'] * $rs_facturaventa['TX_datoventa_precio'];
				$descuento=($precio*$rs_facturaventa['TX_datoventa_descuento'])/100;
				$precio_descuento=$precio-$descuento;
				$itbm=($precio_descuento*$rs_facturaventa['TX_datoventa_impuesto'])/100;
				$precio_total=$precio_descuento+$itbm;
	 ?>

    	<tr>
            <td><?php echo $rs_facturaventa['TX_producto_codigo']; ?></td>
            <td>
				<?php echo $rs_facturaventa['TX_producto_value']; ?>
            </td>
            <td>
				<?php echo $rs_facturaventa['TX_datoventa_cantidad']; ?>
            </td>
            <td>
				<?php
					echo number_format($rs_facturaventa['TX_datoventa_precio'],2);
				?>
            </td>
            <td>
                <?php echo number_format($descuento,4); ?>
            </td>
            <td>
                <?php echo number_format($itbm,4); ?>
            </td>
            <td>
                <?php echo number_format($precio_total,4); ?>
            </td>
		</tr>
        <?php
			/* ###### impuesto ######## */
			$totalitbm += $itbm;
			/* ###### impuesto ######## */
			$totaldescuento += $descuento;
			/* ###### subtotal ######## */
			$total += $precio_total;
		?>
        <?php }while($rs_facturaventa=mysql_fetch_assoc($qry_facturaventa)); ?>
 	</tbody>
	</table>
    </td>
</tr>
<tr style="border:solid; height:22px; font-size:14px;">
	<td colspan="2"></td>
	<td colspan="2"><strong>Subtotal</strong><br /> B/ <?php echo number_format($total-$totalitbm+$totaldescuento,4) ?></td>
	<td colspan="2"><strong>Impuesto</strong><br />B/ <?php echo number_format($totalitbm,4) ?></td>
	<td colspan="2"><strong>Descuento</strong><br />B/ <?php echo number_format($totaldescuento,4); ?></td>
	<td colspan="2"><strong>Total</strong><br />B/ <?php echo number_format($total,2); ?></td>
</tr>
    </tbody>
    </table>
    </td>
</tr>
</table>
</body>
</html>
