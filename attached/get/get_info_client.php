<?php
require '../../bh_con.php';
$link=conexion();
$client_id = $_GET['a'];

$qry_credit_term = mysql_query("SELECT TX_cliente_limitecredito, TX_cliente_plazocredito FROM bh_cliente WHERE AI_cliente_id = '$client_id'");
$row_credit_term = mysql_fetch_row($qry_credit_term);

$facturaf_limite=strtotime('-'.$row_credit_term[1].' weeks');
$limit_facturaf=date('Y-m-d',$facturaf_limite);
$qry_outcredit_term=mysql_query("SELECT AI_facturaf_id FROM bh_facturaf WHERE TX_facturaf_fecha < '$limit_facturaf'");
$nr_outcredit_term=mysql_num_rows($qry_outcredit_term);

$qry_outcredit_limit=mysql_query("SELECT SUM(bh_facturaf.TX_facturaf_deficit) AS suma FROM (bh_cliente INNER JOIN bh_facturaf ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id) WHERE bh_cliente.AI_cliente_id = '$client_id' AND bh_facturaf.TX_facturaf_deficit > '0' GROUP BY AI_cliente_id ORDER BY TX_cliente_nombre DESC LIMIT 10");
$row_outcredit_limit=mysql_fetch_row($qry_outcredit_limit);
?>


<div id="container_alertlimit" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<?php	if($nr_outcredit_term > '0'){ ?>
<div class="alert alert-danger">
    <strong>Atencion!</strong> Este cliente a sobrepasado el PLAZO para creditos.
</div>
<?php	} ?>
<?php if($row_credit_term[0] < $row_outcredit_limit[0]){ ?>
<div class="alert alert-danger">
    <strong>Atencion!</strong> Este cliente a sobrepasado el monto LIMITE para creditos. <strong>($ <?php echo $row_credit_term[0]; ?>)</strong>
</div>
<?php	} ?>
</div>
<div id="container_selpaymentmethod" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <?php 
    $qry_paymentmethod=mysql_query("SELECT AI_metododepago_id, TX_metododepago_value FROM bh_metododepago ORDER BY AI_metododepago_id ASC");
    $rs_paymentmethod=mysql_fetch_assoc($qry_paymentmethod);
    
    $qry_creditnote=mysql_query("SELECT AI_notadecredito_id, TX_notadecredito_numero, TX_notadecredito_exedente FROM bh_notadecredito WHERE notadecredito_AI_cliente_id = '$client_id' AND TX_notadecredito_exedente > '0' AND TX_notadecredito_destino = 'SALDO'");
    $rs_creditnote=mysql_fetch_assoc($qry_creditnote);
    ?>    
        <select id="sel_paymentmethod" name="sel_paymentmethod" class="form-control" size="4" onclick="change_paymentmethod(this.value);">
            <?php do{ 
            if($rs_paymentmethod['AI_metododepago_id'] != '5' && $rs_paymentmethod['AI_metododepago_id'] != '6'){ ?>
            <option value="<?php echo $rs_paymentmethod['AI_metododepago_id']; ?>"><?php
            echo $rs_paymentmethod['TX_metododepago_value']; 
            ?></option>
            <?php } ?>
            <?php }while($rs_paymentmethod=mysql_fetch_assoc($qry_paymentmethod)); ?>
            <?php if($nr_creditnote = mysql_num_rows($qry_creditnote) > 0){ ?>
            <?php do{ ?>
	<option value="5" label="<?php echo $rs_creditnote['TX_notadecredito_numero']; ?>">Nota de Cr&eacute;dito: <?php
            echo number_format($rs_creditnote['TX_notadecredito_exedente'],2); 
            ?></option>
            <?php }while($rs_creditnote=mysql_fetch_assoc($qry_creditnote)); ?>
            <?php } ?>
        </select>
</div>
