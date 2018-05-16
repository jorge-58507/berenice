<?php
require '../../bh_con.php';
$link = conexion();

$value=$_GET['a'];
$status=$_GET['b'];
$date_i=$_GET['c'];
$date_f=$_GET['d'];
	$line_date="";
	$line_status = "";

$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);

$txt_facturaventa="SELECT bh_facturaventa.facturaventa_AI_user_id, bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.AI_facturaventa_id, bh_cliente.TX_cliente_nombre, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status, bh_facturaventa.facturaventa_AI_cliente_id, bh_user.TX_user_seudonimo, bh_cliente.TX_cliente_direccion
FROM ((bh_facturaventa INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
INNER JOIN bh_user ON bh_facturaventa.facturaventa_AI_user_id = bh_user.AI_user_id)
WHERE";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." bh_cliente.TX_cliente_nombre LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." bh_cliente.TX_cliente_nombre LIKE '%{$arr_value[$it]}%' AND";
	}
}

$txt_facturaventa=$txt_facturaventa." AND
bh_facturaventa.TX_facturaventa_status != 'INACTIVA' AND
bh_facturaventa.TX_facturaventa_status != 'CANCELADA' OR ";


for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." bh_user.TX_user_seudonimo LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." bh_user.TX_user_seudonimo LIKE '%{$arr_value[$it]}%' AND";
	}
}

$txt_facturaventa=$txt_facturaventa." AND
bh_facturaventa.TX_facturaventa_status != 'INACTIVA' AND
bh_facturaventa.TX_facturaventa_status != 'CANCELADA' OR ";


for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." bh_facturaventa.TX_facturaventa_numero LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_facturaventa=$txt_facturaventa.$line_status.$line_date." bh_facturaventa.TX_facturaventa_numero LIKE '%{$arr_value[$it]}%' AND";
	}
}
$txt_facturaventa=$txt_facturaventa." AND
bh_facturaventa.TX_facturaventa_status != 'INACTIVA' AND
bh_facturaventa.TX_facturaventa_status != 'CANCELADA'
 ORDER BY AI_facturaventa_id DESC LIMIT 10";

$qry_facturaventa = mysql_query($txt_facturaventa);
$rs_facturaventa = mysql_fetch_assoc($qry_facturaventa);
?>
<table id="tbl_facturaventa" class="table table-bordered table-striped">
	<thead class="bg-info">
    	<tr>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
        	<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Vendedor</th>
            <th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Cliente</th>
            <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Nº Factura</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Total</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Status</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        </tr>
    </thead>
    <tfoot class="bg-info">
    	<tr>
      	<td colspan="8"> </td>
			</tr>
    </tfoot>
    <tbody>
    <?php if($nr_facturaventa=mysql_num_rows($qry_facturaventa)>0){ ?>
    <?php
	do{
	?>
    <tr ondblclick="open_newcollect('<?php echo $rs_facturaventa['facturaventa_AI_cliente_id']?>','<?php echo $rs_facturaventa['facturaventa_AI_user_id']?>');">
        <td><?php
		$pre_fecha = strtotime($rs_facturaventa['TX_facturaventa_fecha']);
		echo $fecha = date('d-m-Y',$pre_fecha);
		; ?></td>
    	<td><?php echo $rs_facturaventa['TX_user_seudonimo']; ?></td>
        <td><?php echo $rs_facturaventa['TX_cliente_nombre']; ?><br /><font style="font-size:10px; font-weight:bolder;"><?php echo $rs_facturaventa['TX_cliente_direccion']; ?></font></td>
        <td><?php echo $rs_facturaventa['TX_facturaventa_numero']; ?></td>
        <td>B/ <?php echo number_format($rs_facturaventa['TX_facturaventa_total'],2); ?></td>
        <td>
        <?php
		switch($rs_facturaventa['TX_facturaventa_status']){
			case "ACTIVA":	$font='#00CC00';	break;
			case "FACTURADA":	$font='#0033FF';	break;
			default:	$font='#990000';
		}
		?>
        <font color="<?php echo $font ?>" style="font-weight:bold">
		<?php echo $rs_facturaventa['TX_facturaventa_status']; ?>
        </font>
        </td>
        <td style="text-align:center;">
        <?php if($rs_facturaventa['TX_facturaventa_status'] == "ACTIVA"){ ?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" onclick="javascript:window.location='old_sale.php?a='+this.name">Abrir</button>
        <?php }else{ ?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" onclick="javascript:window.location='new_paydesk.php?a='+this.name">Abrir</button>
        <?php } ?>
        </td>
        <td style="text-align:center;">
        <?php if($rs_facturaventa['TX_facturaventa_status'] == "ACTIVA" || $rs_facturaventa['TX_facturaventa_status'] == "FACTURADA"){ ?>
        	<button type="button" id="btn_newcollect" name="<?php echo $rs_facturaventa['facturaventa_AI_cliente_id'] ?>" class="btn btn-success" onclick="open_newcollect(this.name,'<?php echo $rs_facturaventa['facturaventa_AI_user_id']?>');">Cobrar</button>
        <?php }else{ ?>
<!--NADA PARA MOSTRAR -->
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
