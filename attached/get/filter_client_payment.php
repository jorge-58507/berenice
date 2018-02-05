<?php
require '../../bh_con.php';
$link = conexion();

$client_id=$_GET['a'];
if(isset($_GET['b'])){	$date_i = date('Y-m-d', strtotime($_GET['b']));	};
if(isset($_GET['c'])){	$date_f = date('Y-m-d', strtotime($_GET['c']));	};

$txt_payment="SELECT bh_notadebito.TX_notadebito_fecha, bh_notadebito.TX_notadebito_total, bh_notadebito.AI_notadebito_id, bh_user.TX_user_seudonimo 
FROM (bh_notadebito INNER JOIN bh_user ON bh_user.AI_user_id = bh_notadebito.notadebito_AI_user_id)
WHERE bh_notadebito.notadebito_AI_cliente_id = '$client_id' AND bh_notadebito.TX_notadebito_fecha >= '$date_i' AND bh_notadebito.TX_notadebito_fecha <= '$date_f'";
$qry_payment=mysql_query($txt_payment);

/*$txt_facturaf="SELECT AI_facturaf_id, TX_facturaf_numero, TX_facturaf_total, TX_facturaf_deficit 
FROM bh_facturaf 
WHERE facturaf_AI_cliente_id = '$client_id' AND TX_facturaf_fecha >= '$date_i' AND TX_facturaf_fecha <= '$date_f'";
$qry_facturaf=mysql_query($txt_facturaf) or die(mysql_error());*/
?>
<table id="tbl_notadebito" class="table table-bordered table-condensed table-striped">
    <caption class="caption">Debitos y Abonos</caption>
    <thead class="bg_green">
    <tr>
    	<th class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Fecha</th>
        <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Factura</th>
        <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Total</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2"> </th>
    </tr>
    </thead>
    <tbody>
<?php 
$total_payment=0;
if($nr_payment=mysql_num_rows($qry_payment) > 0){
	while($rs_payment=mysql_fetch_array($qry_payment)){ 
	$total_payment+=$rs_payment[1];
	$qry_ff=mysql_query("SELECT TX_facturaf_numero 
	FROM ((bh_notadebito
INNER JOIN rel_facturaf_notadebito ON bh_notadebito.AI_notadebito_id = rel_facturaf_notadebito.rel_AI_notadebito_id)
INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = rel_facturaf_notadebito.rel_AI_facturaf_id) WHERE AI_notadebito_id = '{$rs_payment[2]}' 
")or die(mysql_error());
	$ff="";
	while($rs_ff=mysql_fetch_array($qry_ff)){	$ff .=	$rs_ff[0]."<br/>";	}
?>
    <tr title="<?php echo $rs_payment['TX_user_seudonimo']; ?>">
    	<td><?php echo date('d-m-Y',strtotime($rs_payment[0])); ?></td>
        <td><?php echo $ff; ?></td>
        <td><?php echo number_format($rs_payment[1],2); ?></td>
        <td><button type="button" id="btn_print_payment" name="<?php echo "print_client_debito.php?a=".$rs_payment[2]; ?>" class="btn btn-info btn-xs" onclick="print_html(this.name);">
        <strong><i class="fa fa-print fa_print" aria-hidden="true"></i></strong></button></td>
    </tr>
<?php 
	} 
}else{
?>
    <tr>
    	<td></td>
        <td></td>
        <td>&nbsp;</td>
    </tr>
<?php } ?>
    </tbody>
    <tfoot class="bg_green">
    <tr>
    	<td> </td>
        <td> </td>
        <td><?php echo number_format($total_payment,2); ?> </td>
        <td> </td>
    </tr>
    </tfoot>
    </table>
       