<?php
require '../../bh_conexion.php';
$link = conexion();

$value=$_GET['a'];
$limit=$_GET['b'];
$date_i=date('Y-m-d',strtotime($_GET['c']));
$date_f=date('Y-m-d',strtotime($_GET['d']));

if($limit == ""){	$line_limit="";	}else{	$line_limit= " LIMIT ".$limit;	}
if (!empty($date_i) && !empty($date_f)) {
	$line_date = " AND TX_facturacompra_fecha >=	'$date_i' AND TX_facturacompra_fecha <= '$date_f'";
}

$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);

$txt_facturacompra="SELECT bh_facturacompra.AI_facturacompra_id, bh_facturacompra.TX_facturacompra_fecha, bh_facturacompra.TX_facturacompra_numero, bh_almacen.TX_almacen_value, bh_facturacompra.TX_facturacompra_ordendecompra, bh_proveedor.TX_proveedor_nombre
FROM ((bh_facturacompra
      INNER JOIN bh_proveedor ON bh_facturacompra.facturacompra_AI_proveedor_id = bh_proveedor.AI_proveedor_id)
	  INNER JOIN bh_almacen ON bh_facturacompra.TX_facturacompra_almacen = bh_almacen.AI_almacen_id)
WHERE";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturacompra=$txt_facturacompra." bh_facturacompra.TX_facturacompra_numero LIKE '%{$arr_value[$it]}%'"." AND TX_facturacompra_preguardado != 1 ".$line_date;
	}else{
$txt_facturacompra=$txt_facturacompra." bh_facturacompra.TX_facturacompra_numero LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$txt_facturacompra=$txt_facturacompra." OR ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturacompra=$txt_facturacompra." bh_facturacompra.TX_facturacompra_ordendecompra LIKE '%{$arr_value[$it]}%'"." AND TX_facturacompra_preguardado != 1 ".$line_date;
	}else{
$txt_facturacompra=$txt_facturacompra." bh_facturacompra.TX_facturacompra_ordendecompra LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$txt_facturacompra=$txt_facturacompra." OR ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_facturacompra=$txt_facturacompra." bh_proveedor.TX_proveedor_nombre LIKE '%{$arr_value[$it]}%'"." AND TX_facturacompra_preguardado != 1 ".$line_date;
	}else{
$txt_facturacompra=$txt_facturacompra." bh_proveedor.TX_proveedor_nombre LIKE '%{$arr_value[$it]}%' AND ";
	}
}

$txt_facturacompra .= " ORDER BY TX_facturacompra_fecha DESC".$line_limit;

$qry_facturacompra=$link->query($txt_facturacompra)or die($link->error);
$rs_facturacompra=$qry_facturacompra->fetch_array(MYSQLI_ASSOC);
$nr_facturacompra=$qry_facturacompra->num_rows;

?>

    	<table id="tbl_facturacompra" class="table table-bordered table-condensed table-striped">
        <thead>
	        <tr class="bg-info">
						<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Fecha</th>
						<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Nº de Fact.</th>
						<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Orden</th>
						<th class="col-xs-6 col-sm-6 col-md-6 col-lg-6">Proveedor</th>
						<th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Almacen</th>
						<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2"></th>
	        </tr>
		    </thead>
        <tfoot class="bg-info"><tr><td colspan="4"></td><td id="ttl_purchase"></td><td colspan="1"></td></tr></tfoot>
        <tbody>
        <?php
				if($nr_facturacompra > 0){
					do{ ?>
		        <tr onclick="filter_productbypurchase('<?php echo $rs_facturacompra['AI_facturacompra_id']; ?>');">
		        	<td><?php 	echo $fecha = date('d-m-Y',strtotime($rs_facturacompra['TX_facturacompra_fecha']));		 			?></td>
			        <td><?php echo $rs_facturacompra['TX_facturacompra_numero']; ?></td>
			        <td><?php echo $rs_facturacompra['TX_facturacompra_ordendecompra']; ?></td>
			        <td><?php echo $rs_facturacompra['TX_proveedor_nombre']; ?></td>
			        <td><?php echo $rs_facturacompra['TX_almacen_value']; ?></td>
							<td>
								<button type="button" id="btn_delete" class="btn btn-danger btn-sm" name="" onclick="transform_facturacompra('<?php echo $rs_facturacompra['AI_facturacompra_id']; ?>')"><i class="fa fa-times" aria-hidden="true"></i></button>
								&nbsp;
								<button type="button" id="btn_print" class="btn btn-info btn-sm" name="" onclick="print_html('print_purchase_html.php?a=<?php echo $rs_facturacompra['AI_facturacompra_id']; ?>')"><i class="fa fa-print" aria-hidden="true"></i></button>
								&nbsp;
								<button type="button" id="btn_nc" class="btn btn-warning btn-sm" name="" onclick="document.location.href = 'make_purchase_nc.php?a=<?php echo $rs_facturacompra['AI_facturacompra_id']; ?>'"><i class="fa fa-reply" aria-hidden="true"></i></button>
							</td>
		        </tr>
		<?php }while($rs_facturacompra=$qry_facturacompra->fetch_array(MYSQLI_ASSOC));
				}else{?>
	        <tr>
		        <td colspan="5"></td>
	        </tr>
<?php 	} ?>
      </tbody>
    </table>
