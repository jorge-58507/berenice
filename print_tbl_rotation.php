<?php
require 'bh_conexion.php';
$link=conexion();
require 'attached/php/req_login_sale.php';

function cal_month($date_i, $date_f){
	$datetime1=new DateTime($date_i);
	$datetime2=new DateTime($date_f);
	$interval=$datetime2->diff($datetime1);
	$intervalMeses=$interval->format("%m");
	$intervalAnos = $interval->format("%y")*12;
	$meses = $intervalMeses+$intervalAnos;
	$raw_fecha=array();
	$fecha_sumada=$date_i;
	for($i=0;$i<$meses;$i++){
		$fecha_sumada = date('Y-m-d',strtotime('+1 month',strtotime($fecha_sumada)));
		$raw_fecha[$i]=$fecha_sumada;
	}
	return $raw_fecha;
}
$prep_purchase=$link->prepare("SELECT SUM(TX_datocompra_cantidad) AS cantidad FROM (bh_datocompra INNER JOIN bh_facturacompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id) WHERE bh_facturacompra.TX_facturacompra_fecha >= ? AND  bh_facturacompra.TX_facturacompra_fecha < ? AND bh_datocompra.datocompra_AI_producto_id = ?")or die($link->error);
$prep_sold=$link->prepare("SELECT SUM(TX_datoventa_cantidad) AS venta	FROM ((bh_datoventa	INNER JOIN bh_facturaventa ON bh_facturaventa.AI_facturaventa_id = bh_datoventa.datoventa_AI_facturaventa_id)	INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_facturaventa.facturaventa_AI_facturaf_id)	WHERE bh_facturaf.TX_facturaf_fecha >= ? AND bh_facturaf.TX_facturaf_fecha < ? AND bh_datoventa.datoventa_AI_producto_id = ?");


$qry_opcion=$link->query("SELECT TX_opcion_titulo, TX_opcion_value FROM bh_opcion")or die($link->error);
$raw_opcion=array();
while($rs_opcion=$qry_opcion->fetch_array()){
	$raw_opcion[$rs_opcion['TX_opcion_titulo']]=$rs_opcion['TX_opcion_value'];
}

	$date_i=date('Y-m',strtotime($_GET['a']));	$date_i=date('Y-m-d',strtotime($date_i));
	$date_f=date('Y-m',strtotime($_GET['b']));	$date_f=date('Y-m-d',strtotime($date_f));
	$product_id=$_GET['c'];
	echo $date_i." ".$date_f;

	$raw_fecha = cal_month($date_i,$date_f);
// echo json_encode($raw_fecha);
	$qry_product=$link->query("SELECT AI_producto_id, TX_producto_codigo, TX_producto_referencia, TX_producto_value, TX_producto_minimo, TX_producto_maximo FROM bh_producto WHERE AI_producto_id = '$product_id'")or die($link->error);
	$rs_product=$qry_product->fetch_array();

	$qry_json=$link->query("SELECT TX_rotacion_json FROM bh_rotacion WHERE rotacion_AI_producto_id = '$product_id'")or die($link->error);
	$array_merged=array();
	while($rs_json=$qry_json->fetch_array()){
		$year=date('Y',strtotime($rs_json['TX_rotacion_json']));
		$array_merged=array_merge($array_merged,json_decode($rs_json['TX_rotacion_json'],true));
	}
$stock=0;
$ite=0;
$raw_stock=array();
$counter=0;
$raw_date_finded=array();
foreach($raw_fecha as $fecha){
	$fecha_mes = date('Y-m',strtotime($fecha));
	foreach ($array_merged as $key => $ciclo) {
		foreach ($ciclo as $fecha_merged => $stock_merged) {
			$fecha_mes_merged=date('Y-m',strtotime($fecha_merged));
			if ($fecha_mes === $fecha_mes_merged) {
				$stock+=$ciclo[$fecha_merged];
				$raw_date_finded[$counter]=$fecha_merged;
				$raw_stock[$ite]=$ciclo[$fecha_merged];
				$counter++;
				$ite++;
			}
		}
	}

	// for($it=0;$it<count($array_merged);$it++){
	// 	if(isset($array_merged[$it][$fecha])){
  //
	// 		$stock+=$array_merged[$it][$fecha];
	// 		$raw_date_finded[$counter]=$fecha;
	// 		$counter++;
	// 		$raw_stock[$ite]=$array_merged[$it][$fecha];
	// 		$ite++;
  //
	// 	}
	// }
}
//print_r($raw_date_finded);
//############### compras y ventas por intervalo
$raw_purchase=array();	$iter=0;
$raw_sold=array();	$itera=0;
foreach($raw_date_finded as $finded){
	$date_initial=$finded;
	if($finded != end($raw_date_finded)){
		$date_final=date('Y-m',strtotime('+1 month',strtotime($finded)));	$date_final=date('Y-m-d',strtotime($date_final));
	}else{
		$date_final=date('Y-m-d',strtotime($_GET['b']));
	}
	$prep_purchase->bind_param("ssi",$date_initial,$date_final,$product_id); $prep_purchase->execute(); $qry_purchase=$prep_purchase->get_result();
	$rs_purchase=$qry_purchase->fetch_array();
	// echo "desde:".$date_initial." hasta: ".$date_final." cantidad: ".$rs_purchase['cantidad']."<br />";
	if(isset($rs_purchase['cantidad'])){
		$raw_purchase[$iter]=$rs_purchase['cantidad'];
	}else{
		$raw_purchase[$iter]=0;
	}
	$raw_date_interval[$iter][0]=$date_initial;
	$raw_date_interval[$iter][1]=$date_final;
	$iter++;

	$prep_sold->bind_param("ssi",$date_initial,$date_final,$product_id); $prep_sold->execute(); $qry_sold=$prep_sold->get_result();
	$rs_sold=$qry_sold->fetch_array();
	if(isset($rs_sold['venta'])){
		$raw_sold[$itera]=$rs_sold['venta'];
	}else{
		$raw_sold[$itera]=0;
	}
		$itera++;
}

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Rotacion de Material</title>
<link href="attached/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="attached/css/print_css.css" rel="stylesheet" type="text/css">
</head>
<script type="text/javascript">

</script>

<body style="font-family:Arial" >
<!-- onLoad="window.print()" -->
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
<tr style="height:85px" align="right">
	<td colspan="2" style="text-align:left">
    </td>

   	<td valign="top" colspan="6" style="text-align:center">
			<img width="200px" height="75px" src="attached/image/logo_factura.png">
			<br />
			<font style="font-size:10px">RUC: <?php echo $raw_opcion['RUC']; ?> DV: <?php echo $raw_opcion['DV']."<br/>"; ?></font>
			<font style="font-size:10px"><?php echo $raw_opcion['DIRECCION']."<br />"; ?></font>
			<font style="font-size:10px"><?php echo $raw_opcion['TELEFONO']." ".$raw_opcion['FAX']."<br />"; ?></font>
			<font style="font-size:10px"><?php echo $raw_opcion['EMAIL']."<br />"; ?></font>
    </td>
    <td valign="top" colspan="2" class="optmayuscula">
			<?php echo $fecha_dia."&nbsp;-&nbsp;".$fecha; ?>
    </td>
</tr>
<tr style="height:21px" align="center">
	<td valign="top" colspan="10"></td>
</tr>
<tr style="height:60px">
	<td valign="top" colspan="10">
    <table id="tbl_client" class="table" style="border:none;">
		<tbody style="background-color:#DDDDDD; border:solid;">
    	<tr>
        	<td valign="top"  class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <strong>Codigo: </strong><?php echo strtoupper($rs_product['TX_producto_codigo']); ?>
            </td>
        	<td valign="top"  class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <strong>Referencia: </strong><?php echo strtoupper($rs_product['TX_producto_referencia']); ?>
            </td>
        	<td valign="top"  class="col-xs-6 col-sm-6 col-md-6 col-lg-6" rowspan="2">
            <strong>Nombre: </strong><?php echo strtoupper($rs_product['TX_producto_value']); ?>
            </td>
    	</tr>
        <tr>
        	<td valign="top">
            <strong>M&iacute;nimo: </strong><?php echo strtoupper($rs_product['TX_producto_minimo']); ?>
            </td>
        	<td valign="top">
            <strong>M&aacute;ximo: </strong><?php echo strtoupper($rs_product['TX_producto_maximo']); ?>
            </td>
        </tr>
    </table>
    </td>
</tr>
<tr style="height:688px;">
	<td valign="top" colspan="10" style="padding-top:2px;">
        <table id="tbl_relation" class="table table-bordered">
    	<thead style="border:solid; background-color:#DDDDDD;">
        <tr>
            <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">EXISTENCIAS</th>
            <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">COMPRAS</th>
            <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">VENTAS</th>
            <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">RELACION C/V</th>
        </tr>
        </thead>
		<tbody>
        <?php
			$total_stock=0;
			$total_purchase=0;
			$total_sold=0;
			for($iterac=0;$iterac<$itera;$iterac++){
      	if($raw_purchase[$iterac] > 0){
					$relation =  ($raw_sold[$iterac]*100)/$raw_purchase[$iterac];
				}else{
					$relation = 0;
				}
		?>
        <tr style="border-top: solid 2px; ">
	        <td colspan="4" style="text-align:left;">
            	<strong>Desde: </strong><?php echo date('d-m-Y',strtotime($raw_date_interval[$iterac][0])); ?>
            	<strong>Hasta: </strong><?php echo date('d-m-Y',strtotime($raw_date_interval[$iterac][1])); ?>
          </td>
        </tr>
        <tr style="border-bottom: solid 2px; ">
        	<td valign="top">
            <?php echo $raw_stock[$iterac]; ?>
          </td>
        	<td valign="top">
            <?php echo $raw_purchase[$iterac]; ?>
          </td>
          <td valign="top">
            <?php echo $raw_sold[$iterac]; ?>
          </td>
          <td valign="top">
            <?php echo round($relation,1)."%"; ?>
          </td>
        </tr>
        <?php
				$total_stock+=$raw_stock[$iterac];
				$total_purchase+=$raw_purchase[$iterac];
				$total_sold+=$raw_sold[$iterac];
				}
		?>
        </tbody>
        <?php
			 if($iterac == 0){
				$promedio=0;
				$rotacion_mes=0;
				$mes_rotacion=0;
				$average_sales=0;
			 }else{
			 	$promedio = $total_stock/$iterac;
				$rotacion_mes=$total_purchase/$promedio;
				if($rotacion_mes == 0){ $mes_rotacion=0; }else{
					$mes_rotacion=30.41/$rotacion_mes;
				}
				$average_sales=$total_sold/$iterac;
			 }
		?>
		<tfoot style="border:solid; background-color:#DDDDDD;">
        <tr>
        	<td>
            <strong>SUM EXISTENCIAS:</strong> <?php echo round($total_stock,1); ?>
             </td>
        	<td>
            <strong>TOTAL COMPRAS:</strong> <?php echo round($total_purchase,1); ?>
             </td>
        	<td>
            <strong>TOTAL VENTAS:</strong> <?php echo round($total_sold,1); ?>
             </td>
        	<td>
            <strong>PROMEDIO VENTAS:</strong> <?php echo round($average_sales,1); ?>
             </td>
        </tr>
        <tr>
        	<td>
            <strong>PROMEDIO:</strong> <?php echo round($promedio,3); ?>
             </td>
        	<td>
            <strong>ROTACION/MES:</strong> <?php echo round($rotacion_mes,3); ?>
             </td>
        	<td>
            <strong>DIAS/ROTACION:</strong> <?php echo round($mes_rotacion,3); ?>
             </td>
             <td></td>
        </tr>
        </tfoot>
	</table>
    </td>
</tr>
<tr style="height:66px">
</tr>
</table>
</body>
</html>
