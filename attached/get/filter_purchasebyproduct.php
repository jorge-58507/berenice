<?php
require '../../bh_conexion.php';
$link = conexion();

$product_id=$_GET['a'];
$limit=$_GET['b'];
$date_i=date('Y-m-d',strtotime($_GET['c']));
$date_f=date('Y-m-d',strtotime($_GET['d']));

if($limit == ""){	$line_limit="";	}else{	$line_limit= " LIMIT ".$limit;	}
if (!empty($date_i) && !empty($date_f)) {
	$line_date = " AND TX_facturacompra_fecha >=	'$date_i' AND TX_facturacompra_fecha <= '$date_f'";
}

$txt_facturacompra="SELECT bh_facturacompra.AI_facturacompra_id, bh_facturacompra.TX_facturacompra_fecha, bh_facturacompra.TX_facturacompra_numero, bh_almacen.TX_almacen_value, bh_facturacompra.TX_facturacompra_ordendecompra, bh_proveedor.TX_proveedor_nombre
FROM (((bh_facturacompra
INNER JOIN bh_datocompra ON bh_facturacompra.AI_facturacompra_id = bh_datocompra.datocompra_AI_facturacompra_id)
      INNER JOIN bh_proveedor ON bh_facturacompra.facturacompra_AI_proveedor_id = bh_proveedor.AI_proveedor_id)
	  INNER JOIN bh_almacen ON bh_facturacompra.TX_facturacompra_almacen = bh_almacen.AI_almacen_id)
WHERE bh_datocompra.datocompra_AI_producto_id = '$product_id'".$line_date." ORDER BY TX_facturacompra_fecha DESC, AI_facturacompra_id DESC".$line_limit;
$qry_facturacompra=$link->query($txt_facturacompra)or die($link->error);

$rs_facturacompra=$qry_facturacompra->fetch_array();
?>

    	<table id="tbl_facturacompra" class="table table-bordered table-condensed table-striped">
        <thead>
					<tr class="bg-info">
		        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
		        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Nº de Fact.</th>
		        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Nº de Orden</th>
		        <th class="col-xs-6 col-sm-6 col-md-6 col-lg-6">Proveedor</th>
		        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Almacen</th>
						<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
	        </tr>
	    	</thead>
        <tfoot class="bg-info"><tr><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot>
        <tbody>
        <?php if($nr_facturacompra=$qry_facturacompra->num_rows > 0){ ?>
		<?php do{ ?>
	        <tr onclick="filter_productbypurchase('<?php echo $rs_facturacompra['AI_facturacompra_id']; ?>');">
		        <td><?php	echo $fecha = date('d-m-Y',strtotime($rs_facturacompra['TX_facturacompra_fecha'])); ?></td>
		        <td><?php echo $rs_facturacompra['TX_facturacompra_numero']; ?></td>
		        <td><?php echo $rs_facturacompra['TX_facturacompra_ordendecompra']; ?></td>
		        <td><?php echo $rs_facturacompra['TX_proveedor_nombre']; ?></td>
		        <td><?php echo $rs_facturacompra['TX_almacen_value']; ?></td>
						<td>
							<button type="button" id="btn_delete" class="btn btn-danger btn-sm" name="" onclick="transform_facturacompra('<?php echo $rs_facturacompra['AI_facturacompra_id']; ?>')"><i class="fa fa-times" aria-hidden="true"></i></button>
							&nbsp;
							<button type="button" id="btn_print" class="btn btn-info btn-sm" name="" onclick="print_html('print_purchase_html.php?a=<?php echo $rs_facturacompra['AI_facturacompra_id']; ?>')"><i class="fa fa-print" aria-hidden="true"></i></button>
						</td>
	        </tr>
<?php 	}while($rs_facturacompra=$qry_facturacompra->fetch_array()); ?>
<?php }else{ ?>
        <tr>
        	<td colspan="6"></td>
        </tr>
<?php } ?>
      </tbody>
    </table>
