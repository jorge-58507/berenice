<?php
require '../../bh_conexion.php';
$link=conexion();
$client_id = $_GET['a'];
$monto_credito = $_GET['b'];

$qry_credit = $link->query("SELECT TX_cliente_limitecredito, TX_cliente_plazocredito FROM bh_cliente WHERE AI_cliente_id = '$client_id'");
$row_credit = $qry_credit->fetch_array();


$facturaf_limite=strtotime('-'.$row_credit['TX_cliente_plazocredito'].' weeks');
$limit_facturaf=date('Y-m-d',$facturaf_limite);
$qry_outcredit_term=$link->query("SELECT AI_facturaf_id, TX_facturaf_fecha, TX_facturaf_numero, TX_facturaf_deficit FROM bh_facturaf WHERE facturaf_AI_cliente_id = '$client_id' AND TX_facturaf_fecha < '$limit_facturaf' AND TX_facturaf_deficit > '0'");
$nr_outcredit_term=$qry_outcredit_term->num_rows;

$qry_outcredit_limit=$link->query("SELECT bh_facturaf.TX_facturaf_deficit FROM (bh_cliente INNER JOIN bh_facturaf ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id) WHERE bh_cliente.AI_cliente_id = '$client_id' AND bh_facturaf.TX_facturaf_deficit > '0'");
$deuda=$monto_credito*1;
while($rs_outcredit_limit=$qry_outcredit_limit->fetch_array()){
  $deuda += $rs_outcredit_limit['TX_facturaf_deficit'];
}

$qry_restricted_customer = $link->query("SELECT AI_cliente_id, TX_cliente_restringido FROM bh_cliente WHERE AI_cliente_id = '$client_id'")or die($link->error);
$rs_restricted_customer = $qry_restricted_customer->fetch_array(MYSQLI_ASSOC);

session_start();
if(!empty($_SESSION['admin']) && $_SESSION['admin']  < 3) {
  $admon_logon = 1;
}else {
  $admon_logon = 0;
}
?>


<div id="container_alertlimit" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no_padding">
<?php
  $class_alert = ($rs_restricted_customer['TX_cliente_restringido'] === '0' || $admon_logon === 1) ? 'warning' : 'danger';
  if($nr_outcredit_term > '0'){  $rs_outcredit_term=$qry_outcredit_term->fetch_array(MYSQLI_ASSOC); ?>
    <div id="credit_time_limit" class="alert alert-<?php echo $class_alert; ?>">
      <strong>Atencion!</strong> Sobrepasado el PLAZO de <?php echo $row_credit['TX_cliente_plazocredito'].' Semanas ('.date('d-m-Y', strtotime($limit_facturaf))."),  <strong>(Fact. ".$rs_outcredit_term['TX_facturaf_numero']." del ".date('d-m-Y', strtotime($rs_outcredit_term['TX_facturaf_fecha'])).", Deficit: B/ ".number_format($rs_outcredit_term['TX_facturaf_deficit'],2)." )</strong>."; ?>
    </div>
<?php
  } else {  ?>
    <div id="credit_time_limit" class="alert alert-success">
      <strong>Atencion!</strong> Este cliente no a sobrepasado el PLAZO para cr&eacute;ditos. <?php echo ($rs_restricted_customer['TX_cliente_restringido'] === "1") ? 'Restringido' : 'No Restringido'; ?>
    </div><?php
  }         ?>
<?php if($row_credit['TX_cliente_limitecredito'] < $deuda){ ?>
<div id="credit_amount_limit" class="alert alert-<?php echo $class_alert; ?>">
    <strong>Atencion!</strong> Se ha sobrepasado el monto LIMITE de (B/
      <?php echo number_format($row_credit['TX_cliente_limitecredito'],2).'), <strong>';
            echo ' (B/ '.number_format($deuda,2).')'; ?></strong>
</div>
<?php	} else {  ?>
<div id="credit_amount_limit" class="alert alert-success">
    <strong>Atencion!</strong> No se ha sobrepasado el monto LIMITE para cr&eacute;ditos. (B/  <?php echo number_format($row_credit['TX_cliente_limitecredito'],2); ?>)
</div>
<?php  } 
if ($rs_restricted_customer['TX_cliente_restringido'] === '0') {
?>
<div id="" class="alert alert-info">
    Este cliente no tiene Restricci&oacute;n de Cr&eacute;dito.
</div>
<?php
} ?>

</div>
