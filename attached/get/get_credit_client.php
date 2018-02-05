<?php
require '../../bh_con.php';
$link=conexion();
$client_id = $_GET['a'];

$qry_credit = mysql_query("SELECT TX_cliente_limitecredito, TX_cliente_plazocredito FROM bh_cliente WHERE AI_cliente_id = '$client_id'");
$row_credit = mysql_fetch_array($qry_credit);

$facturaf_limite=strtotime('-'.$row_credit['TX_cliente_plazocredito'].' weeks');
$limit_facturaf=date('Y-m-d',$facturaf_limite);
$qry_outcredit_term=mysql_query("SELECT AI_facturaf_id FROM bh_facturaf WHERE facturaf_AI_cliente_id = '$client_id' AND TX_facturaf_fecha < '$limit_facturaf' AND TX_facturaf_deficit > '0'");
$nr_outcredit_term=mysql_num_rows($qry_outcredit_term);

$qry_outcredit_limit=mysql_query("SELECT bh_facturaf.TX_facturaf_deficit FROM (bh_cliente INNER JOIN bh_facturaf ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id) WHERE bh_cliente.AI_cliente_id = '$client_id' AND bh_facturaf.TX_facturaf_deficit > '0'");
$deuda=0;
while($rs_outcredit_limit=mysql_fetch_array($qry_outcredit_limit)){
  $deuda += $rs_outcredit_limit['TX_facturaf_deficit'];
}
?>


<div id="container_alertlimit" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<?php	if($nr_outcredit_term > '0'){ ?>
<div class="alert alert-danger">
    <strong>Atencion!</strong> Este cliente a sobrepasado el PLAZO para creditos.
</div>
<?php	} else {
?>
<div class="alert alert-success">
    <strong>Atencion!</strong> Este cliente no a sobrepasado el PLAZO para creditos.
</div>
<?php
}?>
<?php if($row_credit['TX_cliente_limitecredito'] < $deuda){ ?>
<div class="alert alert-danger">
    <strong>Atencion!</strong> Este cliente a sobrepasado el monto LIMITE para creditos. <strong>(B/  <?php echo number_format($row_credit['TX_cliente_limitecredito'],2); ?>)</strong>
</div>
<?php	} else {
?>
<div class="alert alert-success">
    <strong>Atencion!</strong> Este cliente no a sobrepasado el monto LIMITE para creditos. <strong>(B/  <?php echo number_format($row_credit['TX_cliente_limitecredito'],2); ?>)</strong>
</div>
<?php
}?>
</div>
