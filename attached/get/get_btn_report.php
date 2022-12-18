<?php
require '../../bh_conexion.php';
$link=conexion();

$qry_checkreport=$link->query("SELECT AI_reporte_id FROM bh_reporte WHERE TX_reporte_tipo = 'INVENTARIO' AND TX_reporte_status = 'ACTIVA'")or die($link->error);
$nr_checkreport=$qry_checkreport->num_rows;
if($nr_checkreport > 0){ echo $value_button="Reporte <span class='badge'>".$nr_checkreport."</span>"; }else{ echo $value_button="Reporte"; }



?>
