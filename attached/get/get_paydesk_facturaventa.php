<?php
require '../../bh_conexion.php';
$link = conexion();

$txt_facturaventa="SELECT bh_facturaventa.facturaventa_AI_user_id, bh_facturaventa.TX_facturaventa_fecha, bh_facturaventa.AI_facturaventa_id, bh_cliente.TX_cliente_nombre, bh_facturaventa.TX_facturaventa_numero, bh_facturaventa.TX_facturaventa_total, bh_facturaventa.TX_facturaventa_status, bh_facturaventa.facturaventa_AI_cliente_id, bh_user.TX_user_seudonimo, bh_cliente.TX_cliente_direccion
FROM ((bh_facturaventa INNER JOIN bh_cliente ON bh_facturaventa.facturaventa_AI_cliente_id = bh_cliente.AI_cliente_id)
INNER JOIN bh_user ON bh_facturaventa.facturaventa_AI_user_id = bh_user.AI_user_id)
WHERE bh_facturaventa.TX_facturaventa_status != 'INACTIVA' AND
bh_facturaventa.TX_facturaventa_status != 'CANCELADA'
 ORDER BY AI_facturaventa_id DESC LIMIT 10";

$qry_facturaventa=$link->query($txt_facturaventa)or die($link->error);
$rs_facturaventa=$qry_facturaventa->fetch_array();

if($qry_facturaventa->num_rows > 0){
	do{		?>
  	<tr ondblclick="open_newcollect('<?php echo $rs_facturaventa['facturaventa_AI_cliente_id']?>','<?php echo $rs_facturaventa['facturaventa_AI_user_id']?>');">
    	<td><?php	echo date('d-m-Y',strtotime($rs_facturaventa['TX_facturaventa_fecha'])); ?></td>
    	<td><?php echo $rs_facturaventa['TX_user_seudonimo']; ?></td>
      <td><?php echo $rs_facturaventa['TX_cliente_nombre']; ?><br /><font style="font-size:10px; font-weight:bolder;"><?php echo $rs_facturaventa['TX_cliente_direccion']; ?></font></td>
      <td><?php echo $rs_facturaventa['TX_facturaventa_numero']; ?></td>
      <td>B/ <?php echo number_format($rs_facturaventa['TX_facturaventa_total'],2); ?></td>
      <td><?php
				switch($rs_facturaventa['TX_facturaventa_status']){
					case "ACTIVA":	$font='#00CC00';	break;
					case "FACTURADA":	$font='#0033FF';	break;
					default:	$font='#990000';
				}
?>      <font color="<?php echo $font ?>" style="font-weight:bold"><?php 	echo $rs_facturaventa['TX_facturaventa_status']; ?></font>
      </td>
      <td style="text-align:center;">
<?php 	if($rs_facturaventa['TX_facturaventa_status'] == "ACTIVA"){ ?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" onclick="javascript:window.location='old_sale.php?a='+this.name">Abrir</button>
<?php 	}else{ 	?>
        	<button type="button" id="btn_editfacturaventa" name="<?php echo $rs_facturaventa['AI_facturaventa_id'] ?>" class="btn btn-warning" onclick="javascript:window.location='new_paydesk.php?a='+this.name">Abrir</button>
<?php 	} 			?>
			</td>
      <td style="text-align:center;">
<?php 	if($rs_facturaventa['TX_facturaventa_status'] == "ACTIVA" || $rs_facturaventa['TX_facturaventa_status'] == "FACTURADA"){ ?>
        	<button type="button" id="btn_newcollect" name="<?php echo $rs_facturaventa['facturaventa_AI_cliente_id'] ?>" class="btn btn-success" onclick="open_newcollect(this.name,'<?php echo $rs_facturaventa['facturaventa_AI_user_id']?>');">Cobrar</button>
<?php 	}?>
      </td>
    </tr>
<?php
	}while($rs_facturaventa=$qry_facturaventa->fetch_array());
}else{ ?>
	<tr>
		<td colspan="5"> </td>
  </tr><?php
} ?>
