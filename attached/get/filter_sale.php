<?php
require '../../bh_con.php';
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



$txt_facturaventa=$txt_facturaventa." ORDER BY AI_facturaventa_id DESC LIMIT 10";
//echo $txt_facturaventa;
$qry_facturaventa = mysql_query($txt_facturaventa);
$rs_facturaventa = mysql_fetch_assoc($qry_facturaventa);
?>
<table id="tbl_facturaventa" class="table table-bordered table-striped">
	<thead class="bg-primary">
    	<tr>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
            <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Cliente</th>
            <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Nº Factura</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Total</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Status</th>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Vendedor</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        </tr>
    </thead>
    <tfoot class="bg-primary">
    	<tr>
        	<td> </td>
        	<td> </td>
        	<td> </td>
        	<td> </td>
        	<td> </td>
        	<td> </td>
        	<td> </td>
        	<td> </td>
		</tr>
    </tfoot>
    <tbody>
    <?php if($nr_facturaventa=mysql_num_rows($qry_facturaventa)>0){ ?>
    <?php
	do{
	?>
    <tr>
        <td><?php 
		$time=strtotime($rs_facturaventa['TX_facturaventa_fecha']);
		$date=date('d-m-Y',$time);
		echo $date; ?></td>
        <td><?php echo $rs_facturaventa['TX_cliente_nombre']; ?></td>
        <td><?php echo $rs_facturaventa['TX_facturaventa_numero']; ?></td>
        <td><?php echo number_format($rs_facturaventa['TX_facturaventa_total'],2); ?></td>
        <td>
        <?php 
		switch($rs_facturaventa['TX_facturaventa_status']){
			case "ACTIVA":
				$font='#00CC00';
				break;
			case "FACTURADA":
				$font='#0033FF';
				break;
			default:
				$font='#990000';
		}
		?>
        <font color="<?php echo $font ?>" style="font-weight:bold">
		<?php echo $rs_facturaventa['TX_facturaventa_status']; ?>
        </font>
        </td>
		<td>
        <?php echo $rs_facturaventa['TX_user_seudonimo']; ?>
        </td>
        <td>
        <?php if($rs_facturaventa['TX_facturaventa_status'] == "ACTIVA"){ ?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" onclick="jacascript:window.location='old_sale.php?a='+this.name">Modificar</button>
        <?php }else if($rs_facturaventa['TX_facturaventa_status'] == "FACTURADA" && $_COOKIE['coo_iuser'] > '2'){ ?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" disabled="disabled">Modificar</button>
        <?php }else if($rs_facturaventa['TX_facturaventa_status'] == "INACTIVA" && $_COOKIE['coo_iuser'] > '2'){ ?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" disabled="disabled">Modificar</button>
        <?php }else{ ?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" onclick="javascript:window.location='old_sale.php?a='+this.name">Modificar</button>
        <?php } ?>
        </td>
        <td>
        <button type="button" id="btn_print" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-info" onclick="print_html('print_sale_html.php?a='+this.name+'')">Imprimir</button>
        </td>
    </tr>
    <?php
	}while($rs_facturaventa=mysql_fetch_assoc($qry_facturaventa));
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
</table>
