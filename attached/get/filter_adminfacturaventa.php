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
$txt_facturaventa="SELECT bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.AI_facturaventa_id, bh_cliente.TX_cliente_nombre, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status, bh_user.TX_user_seudonimo FROM ((bh_facturaventa INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id) INNER JOIN bh_user ON bh_facturaventa.facturaventa_AI_user_id = bh_user.AI_user_id) WHERE";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." TX_cliente_nombre LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." TX_cliente_nombre LIKE '%{$arr_value[$it]}%' AND";
	}
}

$txt_facturaventa=$txt_facturaventa." AND TX_facturaventa_status != 'CANCELADA' OR";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." TX_facturaventa_numero LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." TX_facturaventa_numero LIKE '%{$arr_value[$it]}%' AND";
	}
}

$txt_facturaventa=$txt_facturaventa." AND TX_facturaventa_status != 'CANCELADA' ORDER BY AI_facturaventa_id DESC LIMIT 10";
//echo $txt_facturaventa;
$qry_facturaventa = mysql_query($txt_facturaventa);
$rs_facturaventa = mysql_fetch_assoc($qry_facturaventa);
?>
<table id="tbl_facturaventa" class="table table-bordered table-striped">
	<thead>
    	<tr>
        	<th class="bg-primary col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
        	<th class="bg-primary col-xs-3 col-sm-3 col-md-3 col-lg-3">Vendedor</th>
            <th class="bg-primary col-xs-3 col-sm-3 col-md-3 col-lg-3">Cliente</th>
            <th class="bg-primary col-xs-1 col-sm-1 col-md-1 col-lg-2">Nº Factura</th>
            <th class="bg-primary col-xs-1 col-sm-1 col-md-1 col-lg-1">Total</th>
            <th class="bg-primary col-xs-1 col-sm-1 col-md-1 col-lg-1">Status</th>
            <th class="bg-primary col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        </tr>
    </thead>
    <tfoot>
    	<tr>
        	<td class="bg-primary"> </td>
        	<td class="bg-primary"> </td>
        	<td class="bg-primary"> </td>
        	<td class="bg-primary"> </td>
        	<td class="bg-primary"> </td>
        	<td class="bg-primary"> </td>
        	<td class="bg-primary"> </td>
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
        <td><?php echo $rs_facturaventa['TX_user_seudonimo']; ?></td>
        <td><?php echo $rs_facturaventa['TX_cliente_nombre']; ?></td>
        <td><?php echo $rs_facturaventa['TX_facturaventa_numero']; ?></td>
        <td><?php echo $rs_facturaventa['TX_facturaventa_total']; ?></td>
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
        <?php if($rs_facturaventa['TX_facturaventa_status'] != 'CANCELADA'){ ?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" onclick="jacascript:window.location='old_sale.php?a='+this.name">Modificar</button>
        <?php } ?>
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
