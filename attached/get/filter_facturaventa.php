<?php
require '../../bh_con.php';
$link = conexion();

$value=$_GET['a'];
$status=$_GET['b'];
$date_i=$_GET['c'];
$date_f=$_GET['d'];
if(!empty($date_i) && !empty($date_f)){
	$pre_datei=strtotime($date_i);
	$date_i = date('Y-m-d',$pre_datei);
	$pre_datef=strtotime($date_f);
	$date_f = date('Y-m-d',$pre_datef);

	$line_date=" bh_facturaf.TX_facturaf_fecha >= '$date_i' AND  bh_facturaf.TX_facturaf_fecha <= '$date_f' AND";
}else{
	$line_date="";
}

if(!empty($status)){
	$line_status = " bh_facturaventa.TX_facturaventa_status = 'CANCELADA' AND";
}else{
	$line_status = " bh_facturaventa.TX_facturaventa_status = 'CANCELADA' AND";
}

$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);

$text=1;

$txt_facturaventa="SELECT bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.AI_facturaventa_id, bh_cliente.TX_cliente_nombre, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status,
bh_facturaf.TX_facturaf_numero, bh_facturaf.AI_facturaf_id
FROM ((bh_facturaventa
INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
INNER JOIN bh_facturaf ON bh_facturaventa.facturaventa_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
WHERE";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." TX_cliente_nombre LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." TX_cliente_nombre LIKE '%{$arr_value[$it]}%' AND";
	}
}

switch ($_COOKIE['coo_tuser']) {
case "1":
$txt_facturaventa=$txt_facturaventa;
break;
case "2":
$txt_facturaventa=$txt_facturaventa;
break;
case "4":
$txt_facturaventa=$txt_facturaventa;
break;
default:
$txt_facturaventa=$txt_facturaventa." AND bh_facturaventa.facturaventa_AI_user_id = '{$_COOKIE['coo_iuser']}'";
break;
}

$txt_facturaventa=$txt_facturaventa." OR ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." TX_facturaventa_numero LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." TX_facturaventa_numero LIKE '%{$arr_value[$it]}%' AND";
	}
}
switch ($_COOKIE['coo_tuser']) {
case "1":
$txt_facturaventa=$txt_facturaventa." ORDER BY TX_facturaf_fecha DESC, TX_facturaventa_numero DESC";
break;
case "2":
$txt_facturaventa=$txt_facturaventa." ORDER BY TX_facturaf_fecha DESC, TX_facturaventa_numero  DESC";
break;
case "4":
$txt_facturaventa=$txt_facturaventa." ORDER BY TX_facturaf_fecha DESC, TX_facturaventa_numero  DESC";
break;
default:
$txt_facturaventa=$txt_facturaventa." AND
bh_facturaventa.facturaventa_AI_user_id = '{$_COOKIE['coo_iuser']}' ORDER BY TX_facturaventa_status, TX_facturaventa_numero DESC";
break;
}

if($status != 'CANCELADA'){

if(!empty($date_i) && !empty($date_f)){
	$pre_datei=strtotime($date_i);
	$date_i = date('Y-m-d',$pre_datei);
	$pre_datef=strtotime($date_f);
	$date_f = date('Y-m-d',$pre_datef);

	$line_date=" bh_facturaventa.TX_facturaventa_fecha >= '$date_i' AND  bh_facturaventa.TX_facturaventa_fecha <= '$date_f' AND";
}else{
	$line_date="";
}

$line_status= " bh_facturaventa.TX_facturaventa_status = '{$status}' AND";

$txt_facturaventa="SELECT bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.AI_facturaventa_id, bh_cliente.TX_cliente_nombre,
 bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status
 FROM (bh_facturaventa
 INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
 WHERE ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." bh_cliente.TX_cliente_nombre LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." bh_cliente.TX_cliente_nombre LIKE '%{$arr_value[$it]}%' AND";
	}
}

switch ($_COOKIE['coo_tuser']) {
case "1":
$txt_facturaventa=$txt_facturaventa;
break;
case "2":
$txt_facturaventa=$txt_facturaventa;
break;
case "4":
$txt_facturaventa=$txt_facturaventa;
break;
default:
$txt_facturaventa=$txt_facturaventa." AND bh_facturaventa.facturaventa_AI_user_id = '{$_COOKIE['coo_iuser']}'";
break;
}

$txt_facturaventa=$txt_facturaventa." OR ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." bh_facturaventa.TX_facturaventa_numero LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." bh_facturaventa.TX_facturaventa_numero LIKE '%{$arr_value[$it]}%' AND";
	}
}
switch ($_COOKIE['coo_tuser']) {
case "1":
$txt_facturaventa=$txt_facturaventa." ORDER BY TX_facturaventa_fecha DESC, TX_facturaventa_numero DESC";
break;
case "2":
$txt_facturaventa=$txt_facturaventa." ORDER BY TX_facturaventa_fecha DESC, TX_facturaventa_numero  DESC";
break;
case "4":
$txt_facturaventa=$txt_facturaventa." ORDER BY TX_facturaventa_fecha DESC, TX_facturaventa_numero  DESC";
break;
default:
$txt_facturaventa=$txt_facturaventa." AND
bh_facturaventa.facturaventa_AI_user_id = '{$_COOKIE['coo_iuser']}' ORDER BY TX_facturaventa_status, TX_facturaventa_numero DESC";
break;
}
//echo $txt_facturaventa;

}

// echo $txt_facturaventa;
$qry_facturaventa = mysql_query($txt_facturaventa)or die(mysql_error());
$rs_facturaventa = mysql_fetch_assoc($qry_facturaventa);
?>
<table id="tbl_facturaventa" class="table table-bordered table-striped">
	<thead class="bg-primary">
    	<tr>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
            <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Cliente</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Nº Cotizacion</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Total</th>
            <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Factura Asociada</th>
            <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Metodo de P.</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Monto</th>
        </tr>
    </thead>
    <tbody>
    <?php if($nr_facturaventa=mysql_num_rows($qry_facturaventa)>0){ ?>
    <?php
	$raw_facturaf=array();
	$total_total=0;
	$total_efectivo=0; $total_tarjeta=0; $total_cheque=0; $total_credito=0; $total_notadc=0;
	do{
	?>
    <tr>
        <td><?php
		$time=strtotime($rs_facturaventa['TX_facturaventa_fecha']);
		$date=date('d-m-Y',$time);
		echo $date; ?></td>
        <td><?php echo $rs_facturaventa['TX_cliente_nombre']; ?></td>
        <td><?php echo $rs_facturaventa['TX_facturaventa_numero']; ?></td>
        <td><?php echo $rs_facturaventa['TX_facturaventa_total']; ?></td>
        <td><?php if(isset($rs_facturaventa['TX_facturaf_numero'])){ echo $rs_facturaventa['TX_facturaf_numero']; } ?></td>
        <td>
        <?php
		if(isset($rs_facturaventa['AI_facturaf_id'])){
			$answer = array_search($rs_facturaventa['AI_facturaf_id'], $raw_facturaf);
			if($answer >= -1){
				$print=0;
			}else{
				$print=1;
				$raw_facturaf[]="{$rs_facturaventa['AI_facturaf_id']}";
			}

			if($print==1){
				$qry_datopago=mysql_query("SELECT TX_datopago_monto, datopago_AI_metododepago_id, bh_metododepago.TX_metododepago_value
				FROM ((bh_datopago
				INNER JOIN bh_facturaf ON bh_datopago.datopago_AI_facturaf_id = bh_facturaf.AI_facturaf_id)
				INNER JOIN bh_metododepago ON bh_datopago.datopago_AI_metododepago_id = bh_metododepago.AI_metododepago_id)
				WHERE bh_facturaf.AI_facturaf_id = '{$rs_facturaventa['AI_facturaf_id']}'");
				$raw_monto=array();
				$i=0;
				while($rs_datopago=mysql_fetch_array($qry_datopago)){
				switch($rs_datopago['datopago_AI_metododepago_id']){
					case '1':	$color='#67b847';	$total_efectivo += $rs_datopago['TX_datopago_monto'];	break;
					case '2':	$color='#e9ca2f';	$total_tarjeta += $rs_datopago['TX_datopago_monto'];	break;
					case '3':	$color='#57afdb';	$total_cheque += $rs_datopago['TX_datopago_monto'];	break;
					case '4':	$color='#b54a4a';	$total_credito += $rs_datopago['TX_datopago_monto'];	break;
					case '5':	$color='#EFA63F';	$total_notadc += $rs_datopago['TX_datopago_monto'];	break;
				}
				echo "<font color='{$color}'>".$rs_datopago['TX_metododepago_value']."</font><br />";
				$raw_monto[$i]=$rs_datopago['TX_datopago_monto'];
				$i++;
				}
			}
		}
		?>
        </td>
        <td>
        <?php
		if(isset($raw_monto)){
			if($print==1){
				foreach($raw_monto as $monto){
				echo $monto."<br />";
				}
			}
		}
		?>
        </td>
    </tr>
    <?php
	}while($rs_facturaventa=mysql_fetch_assoc($qry_facturaventa));
	$total_total = $total_cheque+$total_credito+$total_efectivo+$total_notadc+$total_tarjeta;
    ?>
    <?php }else{ ?>
    <tr>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <?php } ?>
    </tbody>
    <tfoot class="bg-primary">
    	<tr>
        	<td colspan="7">
            <table id="tbl_total" class="table-condensed table-bordered" style="width:100%">
			<tr>
            	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>Efectivo:</strong> <br /><?php
				if(isset($total_efectivo)){
					echo number_format($total_efectivo,2);
				};?>
                </td>
            	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>Tarjeta:</strong> <br /><?php
				if(isset($total_efectivo)){
					echo number_format($total_tarjeta,2);
				}
				?>
                </td>
            	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>Cheque:</strong> <br /><?php
				if(isset($total_efectivo)){
					echo number_format($total_cheque,2);
				}?>
                </td>
            	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>Cr&eacute;dito:</strong> <br /><?php
				if(isset($total_efectivo)){
					echo number_format($total_credito,2);
				}?>
                </td>
            	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>Nota de C.:</strong> <br /><?php
				if(isset($total_efectivo)){
					echo number_format($total_notadc,2);
				}?>
                </td>
            	<td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				<strong>Total:</strong> <br /><?php
				if(isset($total_efectivo)){
					echo number_format($total_total,2);
				}?>
                </td>
            </tr>
			</table>
            </td>
		</tr>
    </tfoot>
</table>
