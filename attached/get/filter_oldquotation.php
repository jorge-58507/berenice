<?php
require '../../bh_con.php';
$link = conexion();

$date_limit=$_GET['a'];

$txt_facturaventa="SELECT bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.AI_facturaventa_id, bh_cliente.TX_cliente_nombre, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status
FROM (bh_facturaventa
INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
WHERE TX_facturaventa_status != 'CANCELADA' AND TX_facturaventa_status != 'INACTIVA' AND TX_facturaventa_fecha < '$date_limit' ";

$qry_facturaventa = mysql_query($txt_facturaventa);
$rs_facturaventa = mysql_fetch_assoc($qry_facturaventa);
?>
<table id="tbl_facturaventa" class="table table-bordered table-striped">
	<thead class="bg-primary">
    	<tr>
        	<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-5">Cliente</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-2">Nº Factura</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Total</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Status</th>
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
