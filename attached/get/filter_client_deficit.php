<?php
require '../../bh_con.php';
$link = conexion();

$client_id=$_GET['a'];
if(isset($_GET['b'])){	$date_i = date('Y-m-d', strtotime($_GET['b']));	};
if(isset($_GET['c'])){	$date_f = date('Y-m-d', strtotime($_GET['c']));	};
$line_deficit = "";
if($_GET['d'] == 'deficit'){	$line_deficit = " AND TX_facturaf_deficit > 0";	};

	$txt_facturaf="SELECT bh_facturaf.AI_facturaf_id, bh_facturaf.TX_facturaf_fecha, bh_facturaf.TX_facturaf_hora, bh_facturaf.TX_facturaf_numero, bh_facturaf.TX_facturaf_ticket, bh_facturaf.TX_facturaf_total, bh_facturaf.TX_facturaf_deficit, bh_user.TX_user_seudonimo 
	FROM bh_facturaf INNER JOIN bh_user ON bh_user.AI_user_id = bh_facturaf.facturaf_AI_user_id
	WHERE facturaf_AI_cliente_id = '$client_id' AND TX_facturaf_fecha >= '$date_i' AND TX_facturaf_fecha <= '$date_f'";
	
	$line_order=" ORDER BY TX_facturaf_fecha DESC";
	
$qry_facturaf=mysql_query($txt_facturaf.$line_deficit.$line_order) or die(mysql_error());
?>
<table id="tbl_facturaf" class="table table-bordered table-condensed table-striped">
    <caption class="caption">Facturas</caption>
    <thead class="bg-primary">
    <tr>
    	<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">FECHA</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">HORA</th>
    	<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">FACTURA N&deg;</th>
    	<th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">TICKET</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">TOTAL</th>
        <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">D&Eacute;FICIT</th>
        <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"> </th>
    </tr>
    </thead>
    <tbody>
    <?php
	$total=0; $deficit=0;  
	if($nr_facturaf=mysql_num_rows($qry_facturaf) > 0){
    	while($rs_facturaf=mysql_fetch_array($qry_facturaf)){ ?>
    <tr title="<?php echo $rs_facturaf['TX_user_seudonimo']; ?>">
    	<td><?php echo $rs_facturaf[1]; ?></td>
    	<td><?php echo $rs_facturaf[2]; ?></td>
    	<td><?php echo $rs_facturaf[3]; ?></td>
    	<td><?php echo $rs_facturaf[4]; ?></td>
        <td><?php echo number_format($rs_facturaf[5],2); ?></td>
        <td><?php echo number_format($rs_facturaf[6],2); ?></td>
        <td><button type="button" id="btn_print_ff" name="<?php echo "print_client_facturaf.php?a=".$rs_facturaf[0]; ?>" class="btn btn-info btn-xs" onclick="print_html(this.name);">
        <strong><i class="fa fa-print fa_print" aria-hidden="true"></i></strong></button></td>
    </tr>
    <?php $total+=$rs_facturaf[5]; $deficit+=$rs_facturaf[6];
		} 
	}else{?>
    <tr>
    	<td> </td>
        <td> </td>
        <td> </td>
        <td>&nbsp;</td>
    </tr>
    <?php } ?>
    </tbody>
    <tfoot class="bg-primary">
    <tr><td></td><td></td><td></td><td></td>
    	<td><?php echo number_format($total,2); ?></td>
        <td><?php echo number_format($deficit,2); ?></td>
        <td></td>
    </tr></tfoot>
    </table>
       