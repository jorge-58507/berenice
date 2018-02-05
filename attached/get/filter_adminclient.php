<?php
require '../../bh_con.php';
$link = conexion();

$value=$_GET['a'];
$limit=$_GET['b'];


if(!empty($limit)){
	$line_limit=" LIMIT $limit";
}else{	
	$line_limit="";
}
//bh_facturaf.TX_facturaf_deficit > '0' GROUP BY AI_cliente_id ORDER BY TX_cliente_nombre DESC LIMIT 10
$arr_value = (explode(' ',$value));
$size_value=sizeof($arr_value);
$txt_cliente="SELECT bh_cliente.AI_cliente_id, bh_cliente.TX_cliente_nombre, bh_cliente.TX_cliente_cif, bh_cliente.TX_cliente_telefono, SUM(bh_facturaf.TX_facturaf_deficit) AS suma, bh_facturaf.TX_facturaf_deficit FROM (bh_cliente INNER JOIN bh_facturaf ON bh_facturaf.facturaf_AI_cliente_id = bh_cliente.AI_cliente_id) WHERE ";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_cliente=$txt_cliente." TX_cliente_nombre LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_cliente=$txt_cliente." TX_cliente_nombre LIKE '%{$arr_value[$it]}%' AND";
	}
}

$txt_cliente=$txt_cliente." OR";

for($it=0;$it<$size_value;$it++){
	if($it == $size_value-1){
$txt_cliente=$txt_cliente." TX_cliente_cif LIKE '%{$arr_value[$it]}%'";
	}else{
$txt_cliente=$txt_cliente." TX_cliente_cif LIKE '%{$arr_value[$it]}%' AND";
	}
}

$txt_cliente=$txt_cliente." GROUP BY AI_cliente_id ORDER BY TX_cliente_nombre ASC".$line_limit;
//echo $txt_cliente;
$qry_cliente = mysql_query($txt_cliente);
$rs_cliente = mysql_fetch_assoc($qry_cliente);
?>
    <table id="tbl_client" class="table table-bordered table-condensed table-striped">
    <thead class="bg-primary">
        <tr>
            <th class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Nombre</th>
            <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">RUC</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Deuda</th>
            <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Telefono</th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
            <th class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></th>
        </tr>
    </thead>
    <tfoot class="bg-primary">
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
    
    <tbody>
    
    <?php
	if($nr_cliente=mysql_num_rows($qry_cliente) > 0){
	do{ ?>
    	<tr>
            <td><?php echo $rs_cliente['TX_cliente_nombre']; ?></td>
            <td><?php echo $rs_cliente['TX_cliente_cif']; ?></td>
            <td><?php echo number_format($rs_cliente['suma'],2); ?></td>
            <td><?php echo $rs_cliente['TX_cliente_telefono']; ?></td>
            <td>
            <?php
			if($rs_cliente['suma'] > 0){
			?>
            <button type="button" id="btn_openaccount" name="<?php echo $rs_cliente['AI_cliente_id']; ?>" class="btn btn-info btn-sm" onclick="open_popup_w_scroll('popup_client_account.php?a='+this.name,'client_account','1000','420');">CTS. P/COBRAR</button>
			<?php
			}
			?>
            </td>
            <td>
            <button type="button" id="btn_enablecredit" name="<?php echo $rs_cliente['AI_cliente_id']; ?>" class="btn btn-success btn-sm" onclick="open_popup_w_scroll('popup_client_credit.php?a='+this.name,'client_credit','1000','420');">MOVIMIENTOS</button>
            </td>
    	</tr>
    <?php 
	}while($rs_cliente=mysql_fetch_assoc($qry_cliente));
	}else{?>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    <?php } ?>
    </tbody>
    </table>
