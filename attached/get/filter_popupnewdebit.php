<?php
require '../../bh_con.php';
$link = conexion();

$value=$_GET['a'];
$date=$_GET['b'];
$client_id=$_GET['c'];
if(!empty($date)){
	$pre_date=strtotime($date);
	$date = date('Y-m-d',$pre_date);
	
	$line_date=" bh_facturaf.TX_facturaf_fecha = '$date' AND";
}else{
	$line_date="";
}
	$line_status = "";


$txt_facturaf="SELECT bh_facturaf.TX_facturaf_numero, bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_deficit, bh_cliente.TX_cliente_nombre
FROM (bh_facturaf
INNER JOIN bh_cliente ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id)
WHERE" ;
// bh_facturaf.facturaf_AI_cliente_id = '$client_id' AND bh_facturaf.TX_facturaf_deficit > '0' ORDER BY AI_facturaf_id DESC
$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);
for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaf=$txt_facturaf.$line_date." bh_facturaf.facturaf_AI_cliente_id = '$client_id' AND bh_facturaf.TX_facturaf_deficit > '0' AND TX_facturaf_numero LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_facturaf=$txt_facturaf.$line_date." bh_facturaf.facturaf_AI_cliente_id = '$client_id' AND bh_facturaf.TX_facturaf_deficit > '0' AND TX_facturaf_numero LIKE '%{$arr_value[$it]}%' AND";
	}
}
$txt_facturaf=$txt_facturaf." OR";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaf=$txt_facturaf.$line_date." bh_facturaf.facturaf_AI_cliente_id = '$client_id' AND bh_facturaf.TX_facturaf_deficit > '0' AND TX_facturaf_deficit LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_facturaf=$txt_facturaf.$line_date." bh_facturaf.facturaf_AI_cliente_id = '$client_id' AND bh_facturaf.TX_facturaf_deficit > '0' AND TX_facturaf_deficit LIKE '%{$arr_value[$it]}%' AND";
	}
}

$txt_facturaf = $txt_facturaf." ORDER BY AI_facturaf_id DESC";

//ORDER BY TX_facturaf_fecha DESC";
$qry_facturaf = mysql_query($txt_facturaf);
$rs_facturaf = mysql_fetch_assoc($qry_facturaf);
$nr_facturaf = mysql_num_rows($qry_facturaf);
?>
<table class="table table-bordered table-hover table-striped" id="tbl_bill">
<caption>Creditos pendientes de: <?php echo $rs_facturaf['TX_cliente_nombre']; ?>
<thead class="bg-primary">
<tr>
	<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	<button type="button" id="btn_pickall" class="btn btn-primary btn-sm">Todos</button>
	</th>
    <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Nº</th>
    <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Fecha</th>
    <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Total</th>
</tr>
</thead>
<tfoot></tfoot>
<tbody>
	<?php if($nr_facturaf > 0){ ?>
	<?php $it='0';
     do{?>
<tr>
	<td>
      <label><input name="cb_bill<?php echo $it; ?>" type="checkbox" value="<?php echo $rs_facturaf['AI_facturaf_id'] ?>">
      </label>
	</td>
    <td><?php echo $rs_facturaf['TX_facturaf_numero']; ?></td>
    <td><?php 
		$time=strtotime($rs_facturaf['TX_facturaf_fecha']);
		echo $date=date('d-m-Y',$time);
	?></td>
    <td><?php echo number_format($rs_facturaf['TX_facturaf_deficit'],2); ?> $</td>
</tr>
    <?php $it++; 
	}while($rs_facturaf=mysql_fetch_assoc($qry_facturaf)); ?>
	<?php }else{ ?>
<tr>
	<td></td>
    <td></td>
    <td></td>
    <td></td>
</tr>
	<?php } ?>

</tbody>
</table>

    
    
    
    

