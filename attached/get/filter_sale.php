<?php
require '../../bh_conexion.php';
$link = conexion();

$value=$_GET['a'];
$status=$_GET['b'];
$date=$_GET['c'];
if(!empty($date)){
	$pre_date=strtotime($date);
	$date = date('Y-m-d',$pre_date);
	$line_date=" bh_facturaventa.TX_facturaventa_fecha = '$date' AND";
}else{
	$line_date="";
}
if(!empty($status)){
	$line_status = " bh_facturaventa.TX_facturaventa_status = '$status' AND";
}else{
	$line_status = "";
}

$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);

$txt_facturaventa="SELECT bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.AI_facturaventa_id, bh_cliente.TX_cliente_nombre, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status, bh_user.TX_user_seudonimo
FROM ((bh_facturaventa
INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaventa.facturaventa_AI_user_id)
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
$txt_facturaventa=$txt_facturaventa." AND TX_facturaventa_status != 'CANCELADA'";
break;
case "4":
$txt_facturaventa=$txt_facturaventa." AND TX_facturaventa_status != 'CANCELADA'";
break;
default:
$txt_facturaventa=$txt_facturaventa." AND TX_facturaventa_status != 'CANCELADA' AND
bh_facturaventa.TX_facturaventa_status != 'INACTIVA'";
break;
}

$txt_facturaventa=$txt_facturaventa." OR";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." TX_facturaventa_numero LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." TX_facturaventa_numero LIKE '%{$arr_value[$it]}%' AND";
	}
}

switch ($_COOKIE['coo_tuser']) {
case "1":
$txt_facturaventa=$txt_facturaventa;
break;
case "2":
$txt_facturaventa=$txt_facturaventa." AND TX_facturaventa_status != 'CANCELADA'";
break;
case "4":
$txt_facturaventa=$txt_facturaventa." AND TX_facturaventa_status != 'CANCELADA'";
break;
default:
$txt_facturaventa=$txt_facturaventa." AND TX_facturaventa_status != 'CANCELADA' AND
bh_facturaventa.TX_facturaventa_status != 'INACTIVA'";
break;
}
$txt_facturaventa=$txt_facturaventa." ORDER BY AI_facturaventa_id DESC LIMIT 150";
$qry_facturaventa = $link->query($txt_facturaventa);
$rs_facturaventa = $qry_facturaventa->fetch_array(MYSQLI_ASSOC);
if($qry_facturaventa->num_rows > 0){
	do{	?>
    <tr>
      <td><?php echo $date=date('d-m-Y',strtotime($rs_facturaventa['TX_facturaventa_fecha'])); ?></td>
      <td><?php echo $rs_facturaventa['TX_cliente_nombre']; ?></td>
      <td><?php echo $rs_facturaventa['TX_facturaventa_numero']; ?></td>
      <td><?php echo number_format($rs_facturaventa['TX_facturaventa_total'],2); ?></td>
<?php	switch($rs_facturaventa['TX_facturaventa_status']){
			case "ACTIVA":
				$font='#00CC00';	break;
			case "FACTURADA":
				$font='#0033FF';	break;
			default:
				$font='#990000';
			} ?>
			<td><font color="<?php echo $font ?>" style="font-weight:bold"><?php echo $rs_facturaventa['TX_facturaventa_status']; ?></font></td>
			<td><?php echo $rs_facturaventa['TX_user_seudonimo']; ?></td>
      <td>
<?php 	if($rs_facturaventa['TX_facturaventa_status'] == "ACTIVA"){ ?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" onclick="jacascript:window.location='old_sale.php?a='+this.name">Modificar</button>
<?php 	}else if($rs_facturaventa['TX_facturaventa_status'] == "FACTURADA" && $_COOKIE['coo_iuser'] > '2'){ ?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" disabled="disabled">Modificar</button>
<?php 	}else if($rs_facturaventa['TX_facturaventa_status'] == "INACTIVA" && $_COOKIE['coo_iuser'] > '2'){ ?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" disabled="disabled">Modificar</button>
<?php 	}else{ ?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" onclick="javascript:window.location='old_sale.php?a='+this.name">Modificar</button>
<?php 	} ?>
      </td>
      <td><button type="button" id="btn_print" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-info" onclick="print_html('print_sale_html.php?a='+this.name+'')"><i class="fa fa-print"><i></button></td>
    </tr>
<?php
	}while($rs_facturaventa=$qry_facturaventa->fetch_array(MYSQLI_ASSOC));	?>
<?php }else{ ?>
    <tr>
        <td colspan="5"> </td>
    </tr>
<?php } ?>
