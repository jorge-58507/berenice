<?php
require '../../bh_con.php';
$link=conexion();

$qry_checkreport=mysql_query("SELECT AI_reporte_id FROM bh_reporte WHERE TX_reporte_tipo = 'INVENTARIO' AND TX_reporte_status = 'ACTIVA'");
$nr_checkreport=mysql_num_rows($qry_checkreport);
if($nr_checkreport > 0){ echo $value_button="Reporte (".$nr_checkreport.")"; }else{ echo $value_button="Reporte"; }



?>