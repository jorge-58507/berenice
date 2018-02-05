<?php
require '../../bh_con.php';
$link = conexion();

$value=$_GET['a'];
$date=$_GET['b'];
$client_id=$_GET['c'];
$raw_cb_selected = array();
if (isset($_GET['d'])) {
	$raw_cb_selected = $_GET['d'];
}
if(!empty($date)){
	$pre_date=strtotime($date);
	$date = date('Y-m-d',$pre_date);

	$line_date=" bh_facturaventa.TX_facturaventa_fecha = '$date' AND";
}else{
	$line_date="";
}
	$line_status = "";


$txt_facturaventa="SELECT bh_facturaventa.AI_facturaventa_id, bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.facturaventa_AI_cliente_id, bh_facturaventa.facturaventa_AI_user_id, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status, bh_cliente.TX_cliente_nombre, bh_user.TX_user_seudonimo FROM ((bh_facturaventa
INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
INNER JOIN bh_user ON bh_facturaventa.facturaventa_AI_user_id = bh_user.AI_user_id)
WHERE" ;
$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);
for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaventa=$txt_facturaventa.$line_date." bh_facturaventa.facturaventa_AI_cliente_id = '$client_id' AND bh_facturaventa.TX_facturaventa_status = 'ACTIVA' AND TX_facturaventa_numero LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_facturaventa=$txt_facturaventa.$line_date." bh_facturaventa.facturaventa_AI_cliente_id = '$client_id' AND bh_facturaventa.TX_facturaventa_status = 'ACTIVA' AND TX_facturaventa_numero LIKE '%{$arr_value[$it]}%' AND";
	}
}
$txt_facturaventa=$txt_facturaventa." OR";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaventa=$txt_facturaventa.$line_date." bh_facturaventa.facturaventa_AI_cliente_id = '$client_id' AND bh_facturaventa.TX_facturaventa_status = 'FACTURADA' AND TX_facturaventa_numero LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_facturaventa=$txt_facturaventa.$line_date." bh_facturaventa.facturaventa_AI_cliente_id = '$client_id' AND bh_facturaventa.TX_facturaventa_status = 'FACTURADA' AND TX_facturaventa_numero LIKE '%{$arr_value[$it]}%' AND";
	}
}

$txt_facturaventa=$txt_facturaventa." OR";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaventa=$txt_facturaventa.$line_date." bh_facturaventa.facturaventa_AI_cliente_id = '$client_id' AND bh_facturaventa.TX_facturaventa_status = 'ACTIVA' AND TX_facturaventa_total LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_facturaventa=$txt_facturaventa.$line_date." bh_facturaventa.facturaventa_AI_cliente_id = '$client_id' AND bh_facturaventa.TX_facturaventa_status = 'ACTIVA' AND TX_facturaventa_total LIKE '%{$arr_value[$it]}%' AND";
	}
}

$txt_facturaventa=$txt_facturaventa." OR";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaventa=$txt_facturaventa.$line_date." bh_facturaventa.facturaventa_AI_cliente_id = '$client_id' AND bh_facturaventa.TX_facturaventa_status = 'FACTURADA' AND TX_facturaventa_total LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_facturaventa=$txt_facturaventa.$line_date." bh_facturaventa.facturaventa_AI_cliente_id = '$client_id' AND bh_facturaventa.TX_facturaventa_status = 'FACTURADA' AND TX_facturaventa_total LIKE '%{$arr_value[$it]}%' AND";
	}
}

$txt_facturaventa = $txt_facturaventa." ORDER BY TX_facturaventa_fecha, TX_facturaventa_numero DESC";

$qry_facturaventa = mysql_query($txt_facturaventa);
$rs_facturaventa = mysql_fetch_assoc($qry_facturaventa);
$nr_facturaventa = mysql_num_rows($qry_facturaventa);

?>

<?php
if($nr_facturaventa > 0){
	do{
?>
<?php
		$ans = array_search($rs_facturaventa['AI_facturaventa_id'],$raw_cb_selected);
		if($ans === false){
?>
<tr id="tr_<?php echo $rs_facturaventa['AI_facturaventa_id'];?>" title="<?php echo $rs_facturaventa['TX_user_seudonimo'];?>" ondblclick="pick_one('<?php echo $rs_facturaventa['AI_facturaventa_id'];?>')">
  <td><?php echo $rs_facturaventa['TX_facturaventa_numero']; ?></td>
  <td>
<?php
		echo $date=date('d-m-Y',strtotime($rs_facturaventa['TX_facturaventa_fecha']));
?>
	</td>
  <td>B/ <?php echo $rs_facturaventa['TX_facturaventa_total']; ?></td>
</tr>

<?php
		}else{
?>
<tr id="tr_<?php echo $rs_facturaventa['AI_facturaventa_id'];?>" title="<?php echo $rs_facturaventa['TX_user_seudonimo'];?>" ondblclick="pick_one('<?php echo $rs_facturaventa['AI_facturaventa_id'];?>')" class="tbl_primary_hovered">
  <td><?php echo $rs_facturaventa['TX_facturaventa_numero']; ?></td>
  <td>
<?php
		echo $date=date('d-m-Y',strtotime($rs_facturaventa['TX_facturaventa_fecha']));
?>
	</td>
  <td>B/ <?php echo $rs_facturaventa['TX_facturaventa_total']; ?></td>
</tr>
<?php
		}
?>
<?php
  	}while($rs_facturaventa=mysql_fetch_assoc($qry_facturaventa));
	}else{
?>
<tr>
	<td></td><td></td><td></td>
</tr>
<?php } ?>
