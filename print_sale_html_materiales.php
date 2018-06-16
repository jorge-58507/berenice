<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_sale.php';

$facturaventa_id=$_GET['a'];

$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion")or die($link->error);
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}

$txt_checkuser_bill="SELECT AI_facturaventa_id FROM bh_facturaventa WHERE AI_facturaventa_id = '$facturaventa_id'";
$qry_checkuser_bill=$link->query($txt_checkuser_bill)or die($link->error);
$nr_checkuser_bill=$qry_checkuser_bill->num_rows;

if($nr_checkuser_bill < 1){
	echo "<meta http-equiv='Refresh' content='1;url=stock.php'>";
}

$qry_facturaventa=$link->query("SELECT bh_facturaventa.AI_facturaventa_id, bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.TX_facturaventa_observacion, bh_facturaventa.facturaventa_AI_cliente_id, bh_facturaventa.facturaventa_AI_user_id, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status, bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_direccion, bh_cliente.TX_cliente_telefono, bh_datoventa.datoventa_AI_producto_id, bh_producto.TX_producto_value, bh_datoventa.TX_datoventa_cantidad, bh_datoventa.TX_datoventa_precio, bh_datoventa.TX_datoventa_impuesto, bh_datoventa.TX_datoventa_descuento, bh_producto.TX_producto_codigo, bh_user.TX_user_seudonimo, bh_datoventa.TX_datoventa_descripcion, bh_datoventa.TX_datoventa_medida
FROM ((((bh_facturaventa
       INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
       INNER JOIN bh_datoventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)
       INNER JOIN bh_producto ON bh_datoventa.datoventa_AI_producto_id = bh_producto.AI_producto_id)
       INNER JOIN bh_user ON bh_facturaventa.facturaventa_AI_user_id = bh_user.AI_user_id)
WHERE AI_facturaventa_id = '$facturaventa_id'")or die($link->error);
$rs_facturaventa=$qry_facturaventa->fetch_array();

$qry_medida=$link->query("SELECT AI_medida_id, TX_medida_value FROM bh_medida")or die($link->error);
$raw_medida = array();
while($rs_medida = $qry_medida->fetch_array(MYSQLI_ASSOC)){
	$raw_medida[$rs_medida['AI_medida_id']] = $rs_medida['TX_medida_value'];
}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Factura: <?php echo $rs_facturaventa['TX_cliente_nombre']." - #".$rs_facturaventa['TX_facturaventa_numero'] ?></title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css">
</head>
<script type="text/javascript">
// setTimeout('self.close()',15000);
</script>

<body style="font-family:Arial" onLoad="window.print()">

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
			<img width="200px" height="75px" src="attached/image/logo_factura_materiales.png" ondblclick="window.location.href='print_sale_html.php?a=<?php echo $facturaventa_id; ?>'">
			<br />
			<font style="font-size:10px">RUC: 36021-11-261981 DV: 90<br/></font>
			<font style="font-size:10px"><?php echo $raw_opcion['DIRECCION']."<br />"; ?></font>
			<font style="font-size:10px"><?php echo "TLF. ".$raw_opcion['TELEFONO']." WHATSAPP: ".$raw_opcion['FAX']."<br />"; ?></font>
			<font style="font-size:10px"><?php echo $raw_opcion['EMAIL']."<br />"; ?></font>
	  </td>
	  <td valign="top" colspan="2" class="optmayuscula"><?php
			$time=strtotime($rs_facturaventa['TX_facturaventa_fecha']);
			$date=date('d-m-Y',$time);
			echo $fecha."&nbsp;-&nbsp;".$date;
	?></td>
	</tr>
	<tr style="height:57px">
		<td colspan="10" style="text-align:center;"><h3>COTIZACI&Oacute;N</h3></td>
	</tr>
	<tr style="height:123px">
		<td valign="top" colspan="10">
	  	<table id="tbl_client" class="table table-print" style="border:solid; background-color:#DDDDDD;">
	    	<tr>
	      	<td valign="top" style="width:50%;"><strong>Vendedor(a): </strong><?php echo strtoupper($rs_facturaventa['TX_user_seudonimo']); ?></td>
	        <td valign="top" style="width:20%"><strong>Presupuesto Nº: </strong><?php echo strtoupper($rs_facturaventa['TX_facturaventa_numero']); ?></td>
	        <td valign="top" style="width:30%"></td>
	    	</tr>
			</table>
	    <table id="tbl_client" class="table table-print" style="border:solid; background-color:#DDDDDD;">
	    	<tr>
	        <td valign="top" class="col-xs-5 col-sm-5 col-md-5 col-lg-5"><strong>Nombre: </strong><?php echo strtoupper($rs_facturaventa['TX_cliente_nombre']); ?></td>
	        <td valign="top" class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><strong>RUC: </strong><?php echo strtoupper($rs_facturaventa['TX_cliente_cif']); ?></td>
	        <td valign="top" class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><strong>Telefono: </strong><?php echo $rs_facturaventa['TX_cliente_telefono']; ?></td>
	    	</tr>
	      <tr>
	      	<td colspan="2"><strong>Direcci&oacute;n: </strong><?php echo strtoupper($rs_facturaventa['TX_cliente_direccion']); ?></td>
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
	<tr style="height:545px;">
		<td valign="top" colspan="10" style="padding-top:2px;">
	    <table table id="tbl_product" class="table table-print table-bordered table-striped" >
		    <thead style="border:solid">
		    	<tr>
		        <th>Codigo</th>
		        <th>Detalle</th>
						<th>Medida</th>
		        <th>Cant.</th>
		        <th>Precio</th>
		        <th>Desc.</th>
		        <th>Imp.</th>
		        <th>Total.</th>
					</tr>
				</thead>
		  	<tbody>
<?php
				$total=0; 	$totalitbm=0; 	$totaldescuento=0;
				$index = 1;	$pager = 0;
	 			do{
					$pager++;
					if($index === 1){
						if($pager === 13){
							$pager = 0;
							$index++;
?>
				</tbody>
			</table>
			<tr style="height:25px; font-size:10px; border:none; padding: 0 5px;">
				<td colspan="10"><strong>Condiciones: </strong><br />Precios sujetos a cambio sin previo aviso.</td>
			</tr>
			<tr style="height:38px; font-size:14px; border:solid; ">
				<td colspan="10"></td>
			</tr>
		</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" style="height:975px; width:720px; font-size:12px; margin:0 auto; page-break-before: always">
	<tr style="height:911px;">
		<td valign="top" colspan="10" style="padding-top:2px;">
			<table table id="tbl_product" class="table table-bordered table-striped table-print" >
		    <thead style="border:solid">
		    	<tr>
		        <th>Codigo</th>
		        <th>Detalle</th>
						<th>Medida</th>
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
					if($pager === 21){
						$pager = 0;
						$index++;
	?>
				</tbody>
			</table>
			<tr style="height:25px; font-size:10px; border:none; padding: 0 5px;">
				<td colspan="10"><strong>Condiciones: </strong><br />Precios sujetos a cambio sin previo aviso.</td>
			</tr>
			<tr style="height:38px; font-size:14px; border:solid; ">
				<td colspan="10"></td>
			</tr>
		</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" style="height:975px; width:720px; font-size:12px; margin:0 auto; page-break-before: always">
	<tr style="height:911px;">
		<td valign="top" colspan="10" style="padding-top:2px;">
			<table table id="tbl_product" class="table table-bordered table-striped table-print" >
		    <thead style="border:solid">
		    	<tr>
		        <th>Codigo</th>
		        <th>Detalle</th>
						<th>Medida</th>
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
		    	<tr  style="height:41px;">
		        <td style="vertical-align: middle;"><?php echo $rs_facturaventa['TX_producto_codigo']; 				?></td>
		        <td style="vertical-align: middle;"><?php echo substr($r_function->replace_special_character($rs_facturaventa['TX_datoventa_descripcion']),0,96); 	?></td>
						<td style="vertical-align: middle;" class="al_center"><?php echo $raw_medida[$rs_facturaventa['TX_datoventa_medida']]; ?></td>
						<td style="vertical-align: middle;" class="al_center"><?php echo $rs_facturaventa['TX_datoventa_cantidad']; 		?></td>
		        <td style="vertical-align: middle;" class="al_center"><?php	echo number_format($rs_facturaventa['TX_datoventa_precio'],2);	?></td>
		        <td style="vertical-align: middle;" class="al_center"><?php echo number_format($descuento,4); 			?></td>
		        <td style="vertical-align: middle;" class="al_center"><?php echo number_format($itbm,4); 						?></td>
		        <td style="vertical-align: middle;" class="al_center"><?php echo number_format($precio_total,4); 		?></td>
					</tr>
	<?php
				$totalitbm += $itbm;
				$totaldescuento += $descuento;
				$total += $precio_total;
?>
	<?php }while($rs_facturaventa=$qry_facturaventa->fetch_array()); ?>
	 			</tbody>
			</table>
		</td>
	</tr>
	<tr style="height:25px; font-size:10px; border:none; padding: 0 5px;">
		<td colspan="10"><strong>Condiciones: </strong><br />Precios sujetos a cambio sin previo aviso.</td>
	</tr>
	<tr style="height:38px; font-size:14px; border:solid; ">
		<td colspan="2"></td>
		<td colspan="2"><strong>Subtotal</strong><br /> B/ <?php echo number_format($total-$totalitbm+$totaldescuento,4) ?></td>
		<td colspan="2"><strong>Impuesto</strong><br />B/ <?php echo number_format($totalitbm,4) ?></td>
		<td colspan="2"><strong>Descuento</strong><br />B/ <?php echo number_format($totaldescuento,4); ?></td>
		<td colspan="2"><strong>Total</strong><br />B/ <?php echo number_format($total,2); ?></td>
	</tr>
</tbody>
</table>
<!-- </td>
</tr>
</table> -->
</body>
</html>
