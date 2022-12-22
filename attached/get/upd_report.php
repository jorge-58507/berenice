<?php
require '../../bh_con.php';
$link = conexion();

$report_id=$_GET['a'];

mysql_query("UPDATE bh_reporte SET TX_reporte_status = 'INACTIVA' WHERE AI_reporte_id = '$report_id'");

//########################       ANSWER       ##################

$qry_reporte=mysql_query("SELECT bh_reporte.AI_reporte_id, bh_reporte.TX_reporte_value, bh_reporte.TX_reporte_fecha, bh_reporte.TX_reporte_status, bh_user.TX_user_seudonimo FROM (bh_reporte INNER JOIN bh_user ON bh_user.AI_user_id = bh_reporte.reporte_AI_user_id) WHERE bh_reporte.TX_reporte_tipo = 'INVENTARIO' AND bh_reporte.TX_reporte_status = 'ACTIVA'");
?>

	<table id="tbl_report" class="table table-bordered table-condensed table-hover">
    <thead>
    <tr class="bg-primary">
    	<th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Contenido</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Fecha</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Usuario</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Status</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"> </th>
    </tr>
    </thead>
    <tbody>
    <?php
	if($nr_reporte=mysql_num_rows($qry_reporte) > 0){
	 while($rs_reporte=mysql_fetch_assoc($qry_reporte)){ ?>
    <tr>
    	<td><?php echo $rs_reporte['TX_reporte_value']; ?></td>
        <td><?php echo $rs_reporte['TX_reporte_fecha']; ?></td>
        <td><?php echo $rs_reporte['TX_user_seudonimo']; ?></td>
        <td><?php echo $rs_reporte['TX_reporte_status']; ?></td>
        <td><button type="button" id="btn_process" name="<?php echo $rs_reporte['AI_reporte_id']; ?>" class="btn btn-success btn-sm" onclick="upd_report(this.name)"><i class="fa fa-check" aria-hidden="true"></i></button></td>
    </tr>
    <?php } 
	}else{?>
    <tr>
    	<td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <?php } ?>
    </tbody>
    <tfoot class="bg-primary"><tr><td></td><td></td><td></td><td></td><td></td></tr></tfoot>
	</table>
