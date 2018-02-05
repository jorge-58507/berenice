<?php
require '../../bh_con.php';
$link = conexion();

$client_id=$_GET['a'];
if(isset($_GET['b'])){	$date_i = date('Y-m-d', strtotime($_GET['b']));	};
if(isset($_GET['c'])){	$date_f = date('Y-m-d', strtotime($_GET['c']));	};


$txt_nc="SELECT bh_facturaf.TX_facturaf_numero, bh_notadecredito.TX_notadecredito_destino, (bh_notadecredito.TX_notadecredito_monto+bh_notadecredito.TX_notadecredito_impuesto), bh_notadecredito.TX_notadecredito_exedente, bh_notadecredito.AI_notadecredito_id, bh_user.TX_user_seudonimo
FROM ((bh_notadecredito
INNER JOIN bh_facturaf ON bh_facturaf.AI_facturaf_id = bh_notadecredito.notadecredito_AI_facturaf_id)
INNER JOIN bh_user ON bh_user.AI_user_id = bh_notadecredito.notadecredito_AI_user_id)
WHERE bh_notadecredito.notadecredito_AI_cliente_id = '$client_id' AND bh_notadecredito.TX_notadecredito_fecha >= '$date_i' AND bh_notadecredito.TX_notadecredito_fecha <= '$date_f'";
//echo $txt_nc;
$qry_nc=mysql_query($txt_nc)or die(mysql_error());
?>
	<table id="tbl_creditnote" class="table table-bordered table-condensed table-striped">
    <caption class="caption">Notas de Cr&eacute;dito</caption>
    <thead class="bg-info">
    <tr>
    	<th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Factura</th>
      <th class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Destino</th>
      <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Total</th>
      <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Saldo</th>
      <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">&nbsp;</th>
    </tr>
    </thead>
    <tbody>
<?php
	$total_nc=0;$total_saldo=0;
	if($nr_nc=mysql_num_rows($qry_nc) > 0){
		while($rs_nc=mysql_fetch_array($qry_nc)){
			if($rs_nc[1] == 'SALDO'){
				$total_saldo+=$rs_nc[3];
			}
			$total_nc+=$rs_nc[2];
?>
    <tr title="<?php echo $rs_nc['TX_user_seudonimo']; ?>">
    	<td><?php echo $rs_nc[0]; ?></td>
        <td><?php echo $rs_nc[1]; ?></td>
        <td><?php echo number_format($rs_nc[2],2); ?></td>
        <td><?php echo number_format($rs_nc[3],2); ?></td>
        <td>
					<button type="button" id="btn_print_ff" name="<?php echo "print_client_nc.php?a=".$rs_nc[4]; ?>" class="btn btn-info btn-xs" onclick="print_html(this.name);"><strong><i class="fa fa-print fa_print" aria-hidden="true"></i></strong></button>
				</td>
    </tr>
<?php
		}
	}else{
?>
    <tr>
    	<td> </td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td>&nbsp;</td>
    </tr>
<?php } ?>
    </tbody>
    <tfoot class="bg-info">
    <tr>
    	<td></td>
        <td></td>
        <td><?php echo number_format($total_nc,2); ?></td>
        <td><?php echo number_format($total_saldo,2); ?></td>
        <td></td>
    </tr>
    </tfoot>
    </table>
