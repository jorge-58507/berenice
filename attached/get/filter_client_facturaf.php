<?php
require '../../bh_con.php';
$link = conexion();

$value=$_GET['a'];
if(isset($_GET['b'])){	$date_i = date('Y-m-d', strtotime($_GET['b']));	};
if(isset($_GET['c'])){	$date_f = date('Y-m-d', strtotime($_GET['c']));	};
$txt_facturaf="SELECT 
bh_facturaf.AI_facturaf_id, 
bh_facturaf.TX_facturaf_numero, 
bh_facturaf.TX_facturaf_fecha, 
bh_facturaf.TX_facturaf_subtotalni, 
bh_facturaf.TX_facturaf_subtotalci, 
bh_facturaf.TX_facturaf_impuesto, 
bh_facturaf.TX_facturaf_descuento, 
bh_facturaf.TX_facturaf_deficit
FROM bh_facturaf
WHERE bh_facturaf.facturaf_AI_cliente_id = '$value' AND bh_facturaf.TX_facturaf_fecha >= '$date_i' AND bh_facturaf.TX_facturaf_fecha <= '$date_f'
ORDER BY TX_facturaf_fecha DESC";
//echo $txt_facturaf;
$qry_facturaf=mysql_query($txt_facturaf) or die(mysql_error());
$rs_facturaf=mysql_fetch_assoc($qry_facturaf);
$nr_facturaf=mysql_num_rows($qry_facturaf);
?>
<table id="tbl_facturaf" class="table table-bordered table-condensed table-striped">
        <thead class="bg-primary">
        <tr>
        	<th>Numero</th>
            <th>Fecha</th>
            <th>Total</th>
            <th>Deficit</th>
        </tr>
        </thead>
        <tfoot class="bg-primary">
        <tr><td></td><td></td><td></td><td></td></tr>
        </tfoot>
        <tbody>
        <?php if($nr_facturaf > '0'){?>
        <?php do{
		?>
        
                
        <tr onclick="javascript: get_datoventabyfacturaf('<?php echo $rs_facturaf['AI_facturaf_id'] ?>');">
        	<td><?php echo $rs_facturaf['TX_facturaf_numero'] ?></td>
            <td><?php echo date('d-m-Y',strtotime($rs_facturaf['TX_facturaf_fecha'])); ?></td>
            <td><?php
			echo number_format($total = $rs_facturaf['TX_facturaf_subtotalni'] + $rs_facturaf['TX_facturaf_subtotalci'] + $rs_facturaf['TX_facturaf_impuesto'],2);
			?></td>
            <td><?php echo number_format($rs_facturaf['TX_facturaf_deficit'],2); ?></td>
        </tr>
        <?php }while($rs_facturaf=mysql_fetch_assoc($qry_facturaf));?>
        <?php }else{?>        
        <tr>
        	<td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <?php }?>
        </tbody>
        </table>
       